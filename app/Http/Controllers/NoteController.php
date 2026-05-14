<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Note;
use App\NoteAttachment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage; 

class NoteController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }

    public function store(Request $request) {
        $this->validate($request, [
            'title' => 'nullable|max:191',
            'content' => 'nullable', 
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $note = new Note();
        $note->user_id = Auth::id();
        $note->title = $request->title;
        $note->content = $request->input('content');
        $note->save();

        if ($request->has('labels')) { $note->labels()->attach($request->labels); }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('public/notes');
                $attachment = new NoteAttachment();
                $attachment->note_id = $note->id;
                $attachment->file_path = $path;
                $attachment->save();
            }
        }

        if ($request->ajax()) { return response()->json(['success' => true]); }
        return redirect()->back()->with('success', 'Đã lưu ghi chú thành công!');
    }

    public function update(Request $request, $id) {
        $this->validate($request, [
            'title' => 'nullable|max:191',
            'content' => 'nullable', 
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $note = Note::findOrFail($id);
        
        // Cấp quyền Edit qua Real-time
        $isShared = $note->sharedUsers->contains(Auth::id());
        if ($isShared) {
            $pivot = $note->sharedUsers->where('id', Auth::id())->first()->pivot;
            if ($pivot->permission !== 'edit') {
                return response()->json(['success' => false, 'message' => 'Bạn chỉ có quyền xem!'], 403);
            }
        }

        $note->title = $request->input('title');
        $note->content = $request->input('content');
        $note->save();
        event(new \App\Events\NoteUpdated($note));

        if ($request->has('labels')) {
            $note->labels()->sync($request->labels);
        } else {
            $note->labels()->detach(); 
        }

        if ($request->has('deleted_images')) {
            $attachmentsToDelete = NoteAttachment::whereIn('id', $request->deleted_images)->where('note_id', $note->id)->get();
            foreach($attachmentsToDelete as $att) {
                Storage::delete($att->file_path);
                $att->delete();
            }
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('public/notes');
                $attachment = new NoteAttachment();
                $attachment->note_id = $note->id;
                $attachment->file_path = $path;
                $attachment->save();
            }
        }

        if ($request->ajax()) { return response()->json(['success' => true]); }
        return redirect()->back();
    }

    public function destroy($id) {
        $note = Note::findOrFail($id);
        foreach($note->attachments as $att) {
            Storage::delete($att->file_path);
            $att->delete();
        }
        $note->labels()->detach(); 
        $note->delete();
        return redirect()->back();
    }

    public function togglePin($id) {
        $note = Note::findOrFail($id);
        $note->is_pinned = !$note->is_pinned;
        if ($note->is_pinned) { $note->pinned_at = \Carbon\Carbon::now(); } 
        else { $note->pinned_at = null; }
        $note->save();
        return response()->json(['success' => true]);
    }

    // ==========================================
    // BẢO MẬT & QUẢN LÝ CHIA SẺ NÂNG CAO (TIÊU CHÍ 2.3)
    // ==========================================
    public function setPassword(Request $request, $id) {
        $note = Note::where('user_id', Auth::id())->findOrFail($id);
        $isChanging = $note->password ? true : false;

        if ($request->password) {
            $note->password = Hash::make($request->password);
            $message = $isChanging ? 'Đã đổi mật khẩu thành công!' : 'Đã cài đặt mật khẩu thành công!';
        } else {
            $note->password = null;
            $message = 'Đã gỡ bỏ mật khẩu bảo vệ!';
        }
        $note->save();
        Session::forget('unlocked_' . $note->id);
        return response()->json(['success' => true, 'message' => $message]);
    }

    public function lock($id) {
        Session::forget('unlocked_' . $id);
        return response()->json(['success' => true]);
    }

    public function unlock(Request $request, $id) {
        $note = Note::findOrFail($id);
        if (Hash::check($request->password, $note->password)) {
            Session::put('unlocked_' . $note->id, true);
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, 'message' => 'Mật khẩu không chính xác!']);
    }

    // Tính năng Share Nâng cao: Đa người dùng + Quyền
    public function share(Request $request, $id) {
        $note = Note::where('user_id', Auth::id())->findOrFail($id);
        $emails = explode(',', $request->emails);
        $permission = $request->input('permission', 'read'); // read hoặc edit
        $successCount = 0;
        $errors = [];

        foreach($emails as $email) {
            $email = trim($email);
            if(empty($email)) continue;
            $receiver = \App\User::where('email', $email)->first();

            if (!$receiver) { $errors[] = "Không có email: $email"; continue; }
            if ($receiver->id == $note->user_id) { continue; }
            
            // Dùng syncWithoutDetaching để tránh xóa người cũ
            $note->sharedUsers()->syncWithoutDetaching([$receiver->id => ['permission' => $permission]]);
            $successCount++;
        }
        
        $errorMsg = count($errors) > 0 ? " | Lỗi: " . implode(', ', $errors) : "";
        return response()->json(['success' => true, 'message' => "Đã chia sẻ cho $successCount người. $errorMsg"]);
    }

    // Lấy danh sách người đang được chia sẻ
    public function getSharedUsers($id) {
        $note = Note::where('user_id', Auth::id())->findOrFail($id);
        $users = $note->sharedUsers()->select('users.id', 'users.email', 'note_user.permission', 'note_user.created_at')->get();
        
        // Format lại thời gian sang giờ Việt Nam (UTC+7) trước khi trả về
        $users->transform(function ($user) {
            $user->formatted_time = \Carbon\Carbon::parse($user->created_at)->timezone('Asia/Ho_Chi_Minh')->format('H:i d/m/Y');
            return $user;
        });

        return response()->json($users);
    }

    // Thay đổi quyền (Read -> Edit hoặc ngược lại)
    public function updateSharePermission(Request $request, $id) {
        $note = Note::where('user_id', Auth::id())->findOrFail($id);
        $note->sharedUsers()->updateExistingPivot($request->user_id, ['permission' => $request->permission]);
        return response()->json(['success' => true]);
    }

    // Thu hồi quyền chia sẻ
    public function revokeShare(Request $request, $id) {
        $note = Note::where('user_id', Auth::id())->findOrFail($id);
        $note->sharedUsers()->detach($request->user_id);
        return response()->json(['success' => true]);
    }

    public function getNoteData($id) {
        $note = Note::findOrFail($id);
        return response()->json(['title' => $note->title, 'content' => $note->content]);
    }

    public function removeShared($id) {
        $user = \App\User::find(Auth::id());
        $user->sharedNotes()->detach($id);
        return redirect()->back();
    }
}