<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use App\User;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    // Đổi trang chuyển hướng mặc định thành trang đăng nhập
    protected $redirectTo = '/login';

    public function __construct()
    {
        $this->middleware('guest');
    }

    // =======================================================
    // 1. TỰ ĐỘNG ĐIỀN EMAIL TỪ BỘ NHỚ (SESSION)
    // =======================================================
    public function showResetForm(Request $request, $token = null)
    {
        // Ưu tiên lấy email từ Session, nếu không có mới để trống
        $email = session('reset_email') ? session('reset_email') : $request->email;
        
        return view('auth.passwords.reset')->with(
            ['token' => $token, 'email' => $email]
        );
    }

    // =======================================================
    // 2. KIỂM TRA BẢO MẬT: CHẶN DÙNG LẠI MẬT KHẨU CŨ
    // =======================================================
    public function reset(Request $request)
    {
        $this->validate($request, $this->rules(), $this->validationErrorMessages());

        // Lấy thông tin người dùng từ Database
        $user = User::where('email', $request->email)->first();

        // Kiểm tra xem mật khẩu mới gõ vào CÓ TRÙNG với mật khẩu đang dùng hay không
        if ($user && Hash::check($request->password, $user->password)) {
            return redirect()->back()
                ->withInput($request->only('email'))
                ->withErrors(['password' => 'Mật khẩu mới không được trùng với mật khẩu hiện tại! Vui lòng chọn mật khẩu khác.']);
        }

        // Nếu qua ải kiểm tra, cho phép đổi mật khẩu
        $response = $this->broker()->reset(
            $this->credentials($request), function ($user, $password) {
                $this->resetPassword($user, $password);
            }
        );

        return $response == Password::PASSWORD_RESET
            ? $this->sendResetResponse($response)
            : $this->sendResetFailedResponse($request, $response);
    }

    // =======================================================
    // 3. NGĂN CHẶN TỰ ĐỘNG ĐĂNG NHẬP SAU KHI ĐỔI MẬT KHẨU
    // =======================================================
    protected function resetPassword($user, $password)
    {
        // THÊM CHÚ THÍCH NÀY ĐỂ ÉP VS CODE NHẬN DIỆN ĐÚNG MODEL USER, HẾT GẠCH ĐỎ
        /** @var \App\User $user */
        
        $user->password = Hash::make($password);
        $user->setRememberToken(Str::random(60));
        $user->save();

        // LƯU Ý QUAN TRỌNG: 
        // Hàm gốc của Laravel có dòng `$this->guard()->login($user);` ở đây.
        // Em đã gỡ bỏ nó để bắt buộc người dùng tự đăng nhập tay theo đúng Tiêu chí 2.1 của đề bài.
    }

    // =======================================================
    // 4. CHUYỂN HƯỚNG VỀ TRANG ĐĂNG NHẬP VỚI THÔNG BÁO
    // =======================================================
    protected function sendResetResponse($response)
    {
        // Trả về trang /login kèm Session chứa thông báo thành công
        return redirect('/login')->with('status', 'Khôi phục mật khẩu thành công! Vui lòng đăng nhập lại.');
    }
}