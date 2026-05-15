@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<style>
    /* Ép thanh điều hướng (Navbar) luôn luôn nổi lên trên cùng */
    nav.navbar, .navbar-default, .navbar-static-top {
        position: relative !important;
        z-index: 999999 !important;
    }
    .navbar-collapse {
        position: relative !important;
        z-index: 999999 !important;
        background-color: #ffffff;
    }
    .sidebar-container {
        position: relative !important;
        z-index: 1 !important;
        margin-bottom: 20px;
    }
    @media (max-width: 767px) {
        .navbar-nav .open .dropdown-menu {
            background-color: #ffffff !important;
            border: 1px solid #ddd !important;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .navbar-nav .open .dropdown-menu > li > a {
            color: #333 !important;
            padding: 10px 20px !important;
        }
        .panel-heading, .panel-body { padding: 10px; }
    }

    /* FIX LỖI LƯỚI RESPONSIVE CHO MÀN HÌNH NGANG ĐIỆN THOẠI TO */
    .grid-active .note-item { width: 50% !important; float: left; }
    @media (max-width: 480px) {
        .grid-active .note-item { width: 100% !important; float: none; }
    }
</style>

<div class="container">
    <div class="row">
        <div class="col-md-3 col-sm-12 col-xs-12 sidebar-container">
            <div class="list-group" style="box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding-right: 10px;">
                <a href="{{ url('/home') }}" class="list-group-item" style="border: none; border-radius: 0 25px 25px 0; margin-bottom: 5px; color: {{ (!$isSharedPage && !request()->has('label_id')) ? '#202124' : '#5f6368' }}; background-color: {{ (!$isSharedPage && !request()->has('label_id')) ? '#feefc3' : 'transparent' }}; font-weight: {{ (!$isSharedPage && !request()->has('label_id')) ? 'bold' : 'normal' }};">
                    <i class="fas fa-lightbulb" style="width: 25px;"></i> Ghi chú
                </a>
                <a href="{{ url('/home?shared=1') }}" class="list-group-item" style="border: none; border-radius: 0 25px 25px 0; margin-bottom: 5px; color: {{ $isSharedPage ? '#202124' : '#5f6368' }}; background: {{ $isSharedPage ? '#feefc3' : 'transparent' }}; font-weight: {{ $isSharedPage ? 'bold' : 'normal' }};">
                    <i class="fas fa-user-friends" style="width: 25px;"></i> Được chia sẻ
                </a>
                <div class="list-group-item" style="background: transparent; font-weight: bold; font-size: 11px; letter-spacing: 0.8px; color: #5f6368; border: none; padding-top: 15px; padding-bottom: 5px;">NHÃN CỦA TÔI</div>
                
                @if(isset($labels))
                    @foreach($labels as $label)
                        <a href="{{ url('/home?label_id=' . $label->id) }}" class="list-group-item" style="border: none; border-radius: 0 25px 25px 0; margin-bottom: 2px; color: {{ request('label_id') == $label->id ? '#202124' : '#5f6368' }}; background: {{ request('label_id') == $label->id ? '#feefc3' : 'transparent' }}; font-weight: {{ request('label_id') == $label->id ? 'bold' : 'normal' }};">
                            <i class="fas fa-tag" style="width: 25px;"></i> {{ $label->name }}
                        </a>
                    @endforeach
                @endif
                <a href="#" class="list-group-item" style="border: none; border-radius: 0 25px 25px 0; margin-top: 5px; color: #5f6368; background: transparent; font-style: italic;" data-toggle="modal" data-target="#manageLabelsModal">
                    <i class="fas fa-pencil-alt" style="width: 25px;"></i> Chỉnh sửa nhãn...
                </a>
            </div>
        </div>

        <div class="col-md-9 col-sm-12 col-xs-12">
            <div class="row" style="margin-bottom: 25px;">
                <div class="col-md-12">
                    <form action="{{ url('/home') }}" method="GET">
                        <div class="input-group" style="box-shadow: 0 2px 5px rgba(0,0,0,0.1); border-radius: 8px; overflow: hidden;">
                            <input type="text" name="search" class="form-control input-lg" placeholder="🔍 Tìm kiếm..." value="{{ request('search') }}" style="border: none; box-shadow: none;">
                            <span class="input-group-btn">
                                @if(request()->has('search') && request('search') != '')
                                    <a href="{{ url('/home') }}" class="btn btn-default input-lg" style="border: none; background: #fff; color: #e74c3c;"><i class="fas fa-times"></i></a>
                                @endif
                                <button class="btn btn-default input-lg" type="submit" style="border: none; background: #fff; color: #5f6368;"><i class="fas fa-search"></i></button>
                            </span>
                        </div>
                    </form>
                </div>
            </div>
            
            @if(!$isSharedPage)
            <div class="panel panel-default" style="box-shadow: 0 3px 6px rgba(0,0,0,0.1); border-radius: 8px; border: none; cursor: pointer; background-color: {{ Auth::user()->note_color ? Auth::user()->note_color : '#ffffff' }} !important;" onclick="openAddModal()">
                <div class="panel-body" style="padding: 20px; display: flex; align-items: center; color: #5f6368; font-size: 16px; font-weight: bold;">
                    <i class="fas fa-plus-circle" style="font-size: 20px; color: #1a73e8; margin-right: 10px;"></i> Tạo ghi chú mới...
                </div>
            </div>
            @else
                <h3 style="font-weight: bold; color: #202124; margin-top: 0;">Ghi chú được chia sẻ với bạn</h3>
            @endif

            <div class="text-right" style="margin-bottom: 15px;">
                <div class="btn-group" style="box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                    <button type="button" id="btn-grid" class="btn btn-default active" style="font-weight: bold; color: #333;" onclick="setGridView()"><i class="fas fa-th-large"></i> Lưới</button>
                    <button type="button" id="btn-list" class="btn btn-default" style="color: #777;" onclick="setListView()"><i class="fas fa-list"></i> Danh sách</button>
                </div>
            </div>

            <div class="row grid-active" id="notes-container">
                @foreach($notes as $note)
                @php
                    $isLocked = $note->password && !\Illuminate\Support\Facades\Session::get('unlocked_' . $note->id);
                    $imgData = []; foreach($note->attachments as $att) { $imgData[] = $att->id . '|' . Storage::url($att->file_path); }
                    $imgDataString = implode(',', $imgData);
                    
                    $canEdit = true;
                    $shareTime = '';
                    if($isSharedPage) {
                        $currentUserPivot = $note->sharedUsers->where('id', Auth::id())->first();
                        if($currentUserPivot) {
                            $canEdit = $currentUserPivot->pivot->permission == 'edit';
                            $shareTime = \Carbon\Carbon::parse($currentUserPivot->pivot->created_at)->timezone('Asia/Ho_Chi_Minh')->format('H:i d/m/Y');
                        }
                    }
                @endphp
                <div class="note-item col-xs-12 col-sm-6 col-md-6 col-lg-4">
                    <div class="panel panel-default" style="box-shadow: 0 2px 5px rgba(0,0,0,0.08); border-radius: 8px; border: 1px solid #e0e0e0; transition: 0.3s; margin-bottom: 20px; background-color: {{ $isLocked ? (Auth::user()->dark_mode == 1 ? '#3c4043' : '#f8f9fa') : (Auth::user()->note_color ? Auth::user()->note_color : '#ffffff') }} !important;">
                        <div class="panel-body" style="padding: 15px; position: relative;">
                            
                            <div style="position: absolute; top: -10px; left: -10px; display: flex; gap: 5px; z-index: 10;">
                                @if($isLocked) <span class="label label-danger" style="border-radius: 50%; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.2);"><i class="fas fa-lock"></i></span> @endif
                                @if(!$isSharedPage && $note->sharedUsers->count() > 0) <span class="label label-info" style="border-radius: 50%; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.2);" title="Đang chia sẻ"><i class="fas fa-share-alt"></i></span> @endif
                                @if($note->is_pinned) <span class="label label-warning" style="border-radius: 50%; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.2);" title="Ghi chú được ghim"><i class="fas fa-thumbtack"></i></span> @endif
                            </div>

                            @if(!$isSharedPage)
                            <button class="btn btn-link btn-xs pull-right btn-pin" data-id="{{ $note->id }}" style="color: {{ $note->is_pinned ? '#f1c40f' : '#999' }}; padding: 0; position: absolute; right: 25px; top: 15px; z-index: 10;"><i class="fas fa-thumbtack"></i></button>
                            @endif

                            @if($isLocked)
                                <div class="text-center" style="padding: 30px 10px;">
                                    <i class="fas fa-lock" style="font-size: 32px; color: #dadce0; margin-bottom: 15px;"></i>
                                    <h5 style="font-weight: bold; color: {{ Auth::user()->dark_mode == 1 ? '#e8eaed' : '#5f6368' }};">Ghi chú được bảo mật</h5>
                                    <button class="btn btn-default btn-sm btn-unlock" data-id="{{ $note->id }}" style="border-radius: 20px; font-weight: bold; margin-top: 10px; border-color: #dadce0; color: #1a73e8;">Mở khóa để xem</button>
                                </div>
                            @else
                                @if($note->attachments->count() > 0)
                                    <div class="note-image" style="margin: -15px -15px 15px -15px; display: flex; flex-wrap: wrap; gap: 2px;">
                                        @foreach($note->attachments as $index => $attachment)
                                            <div style="flex: 1 1 calc(50% - 2px); height: 160px;">
                                                <img src="{{ Storage::url($attachment->file_path) }}" style="width: 100%; height: 100%; object-fit: cover; border-top-left-radius: {{ $index == 0 ? '8px' : '0' }}; border-top-right-radius: {{ ($index == 1 || $note->attachments->count() == 1) ? '8px' : '0' }};">
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <h4 style="font-weight: bold; margin-top: 0; color: {{ Auth::user()->dark_mode == 1 ? '#ffffff' : '#333' }}; font-size: 16px; padding-right: 20px;">{{ $note->title }}</h4>
                                <p style="color: {{ Auth::user()->dark_mode == 1 ? '#e8eaed' : '#666' }}; word-wrap: break-word; font-size: {{ Auth::user()->note_font_size ? Auth::user()->note_font_size : '14px' }}; margin-bottom: 10px;">{!! nl2br(e($note->content)) !!}</p>
                                
                                @if($note->labels->count() > 0)
                                    <div style="margin-top: 10px;">
                                        @foreach($note->labels as $label)
                                            <span style="display: inline-block; background-color: rgba(0,0,0,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 4px 10px; font-size: 11px; font-weight: bold; margin-right: 5px; margin-bottom: 5px;"><i class="fas fa-tag"></i> {{ $label->name }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            @endif
                        </div>
                        
                        <div class="panel-footer" style="background: transparent; border-top: 1px solid rgba(0,0,0,0.05); text-align: right; padding: 8px 15px;">
                            @if(!$isSharedPage)
                                @if(!$isLocked)
                                    <button type="button" class="btn btn-default btn-xs btn-set-password" data-id="{{ $note->id }}" style="border: none; background: transparent; color: {{ $note->password ? '#e74c3c' : '#999' }}; float: left;"><i class="fas {{ $note->password ? 'fa-lock' : 'fa-unlock-alt' }}"></i></button>
                                    @if($note->password)
                                        <button type="button" class="btn btn-default btn-xs btn-lock-now" data-id="{{ $note->id }}" style="border: none; background: transparent; color: #2ecc71; float: left; margin-left: 10px;"><i class="fas fa-user-lock"></i> Khóa lại</button>
                                    @endif
                                    
                                    <button class="btn btn-default btn-xs btn-share-modal" data-id="{{ $note->id }}" style="border: none; background: transparent; color: #1a73e8;" title="Quản lý Chia sẻ"><i class="fas fa-user-plus"></i></button>
                                    
                                    <button type="button" class="btn btn-default btn-xs btn-edit-note" 
                                        data-id="{{ $note->id }}" data-title="{{ $note->title }}" data-content="{{ $note->content }}" 
                                        data-labels="{{ $note->labels->pluck('id')->implode(',') }}" data-update-url="{{ route('notes.update', $note->id) }}" data-images="{{ $imgDataString }}"
                                        style="border: none; background: transparent; color: #f39c12;" title="Sửa ghi chú">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <form action="{{ route('notes.destroy', $note->id) }}" method="POST" style="display:inline;">
                                        {{ csrf_field() }} {{ method_field('DELETE') }}
                                        <button type="submit" class="btn btn-default btn-xs" style="border: none; background: transparent; color: #e74c3c;" onclick="return confirm('Em có chắc chắn muốn xóa?')"><i class="fas fa-trash"></i></button>
                                    </form>
                                @endif
                            @else
                                <div style="float: left; text-align: left; margin-top: 2px;">
                                    <div style="font-size: 11px; color: #2ecc71; font-weight: bold; margin-bottom: 4px;"><i class="fas fa-users"></i> Đồng tác giả</div>
                                    <div style="font-size: 10px; color: #1a73e8; background: #e8f0fe; padding: 3px 8px; border-radius: 12px; display: inline-block;">
                                        <i class="fas fa-paper-plane"></i> Từ: {{ isset($note->user) ? $note->user->email : 'Không xác định' }}<br>
                                        <span style="color: #666;"><i class="fas fa-clock"></i> {{ $shareTime }} | <i class="fas {{ $canEdit ? 'fa-pen' : 'fa-eye' }}"></i> {{ $canEdit ? 'Có thể sửa' : 'Chỉ xem' }}</span>
                                    </div>
                                </div>
                                
                                @if(!$isLocked)
                                    <form action="{{ route('notes.removeShared', $note->id) }}" method="POST" style="display:inline; float: right; margin-top: 8px; margin-left: 10px;">
                                        {{ csrf_field() }}
                                        <button type="submit" class="btn btn-default btn-xs" title="Xóa khỏi danh sách" style="border: none; background: transparent; color: #e74c3c;" onclick="return confirm('Bạn có chắc muốn gỡ ghi chú này khỏi danh sách không?')"><i class="fas fa-trash"></i></button>
                                    </form>

                                    @if($canEdit)
                                        <button type="button" class="btn btn-default btn-xs btn-edit-note" 
                                            data-id="{{ $note->id }}" data-title="{{ $note->title }}" data-content="{{ $note->content }}" 
                                            data-labels="{{ $note->labels->pluck('id')->implode(',') }}" data-update-url="{{ route('notes.update', $note->id) }}" data-images="{{ $imgDataString }}"
                                            title="Cùng chỉnh sửa (Real-time)" style="border: none; background: transparent; color: #f39c12; float: right; margin-top: 8px;">
                                            <i class="fas fa-edit"></i> Sửa
                                        </button>
                                    @endif
                                    
                                    @if($note->password)
                                        <button type="button" class="btn btn-default btn-xs btn-lock-now" data-id="{{ $note->id }}" style="border: none; background: transparent; color: #2ecc71; float: right; margin-top: 8px; margin-right: 5px;"><i class="fas fa-user-lock"></i> Khóa lại</button>
                                    @endif
                                @endif
                                <div style="clear: both;"></div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div> 

<div class="modal fade" id="advancedShareModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="border-radius: 8px;">
            <div class="modal-header" style="background-color: #f1f3f4; border-radius: 8px 8px 0 0;">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" style="font-weight: bold;"><i class="fas fa-user-plus" style="color: #1a73e8;"></i> Quản lý Chia sẻ</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="share-modal-note-id">
                <div class="form-group">
                    <label>Thêm người dùng mới (Nhập Email, cách nhau bằng dấu phẩy)</label>
                    <input type="text" id="share-emails-input" class="form-control" placeholder="nguyenvan@gmail.com, tranthi@yahoo.com">
                </div>
                <div class="form-group" style="display: flex; gap: 10px;">
                    <select id="share-permission-select" class="form-control" style="width: 150px;">
                        <option value="read">Chỉ xem</option>
                        <option value="edit">Cho phép sửa</option>
                    </select>
                    <button class="btn btn-primary" id="btn-submit-share" style="border-radius: 4px;">Chia sẻ</button>
                </div>
                <div id="share-msg" style="margin-bottom: 15px; font-weight: bold;"></div>
                <hr>
                <label>Những người đang có quyền truy cập</label>
                <div id="shared-users-list" style="max-height: 200px; overflow-y: auto;"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="unifiedNoteModal" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content" id="unifiedModalContent" style="border-radius: 8px; text-align: left; background-color: {{ Auth::user()->note_color ? Auth::user()->note_color : '#ffffff' }} !important; box-shadow: 0 15px 35px rgba(0,0,0,0.2);">
            <form id="unifiedNoteForm" action="" method="POST" enctype="multipart/form-data">
                {{ csrf_field() }}
                <input type="hidden" name="_method" id="unifiedMethod" value="POST">
                <div id="deleted-images-container"></div>
                <div class="modal-header" style="border-bottom: 1px solid rgba(0,0,0,0.05);">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                    <input type="text" name="title" id="unifiedTitle" class="form-control auto-save-input" placeholder="Tiêu đề..." style="border: none; box-shadow: none; font-weight: bold; font-size: 18px; padding: 0; background: transparent; color: inherit;">
                </div>
                <div class="modal-body">
                    <div id="unified-image-preview-container" style="display: none; position: relative; margin-bottom: 15px; border: 1px dashed #ccc; border-radius: 8px; padding: 10px; background: rgba(0,0,0,0.02);">
                        <div id="preview-images-wrapper" style="display: flex; flex-wrap: wrap; gap: 10px;"></div>
                    </div>
                    <textarea name="content" id="unifiedContent" class="form-control auto-save-input" rows="6" placeholder="Tạo ghi chú mới..." style="border: none; box-shadow: none; resize: none; font-size: {{ Auth::user()->note_font_size ? Auth::user()->note_font_size : '14px' }}; padding: 0; background: transparent; color: inherit;"></textarea>
                </div>
                <div class="modal-footer" style="border-top: none; display: flex; justify-content: space-between; align-items: center;">
                    <div style="display: flex; align-items: center;">
                        <label class="btn btn-default btn-sm" style="border-radius: 20px; border: none; background: rgba(0,0,0,0.05); cursor: pointer; margin-right: 5px; margin-bottom: 0;">
                            <i class="far fa-image"></i>
                            <input type="file" name="images[]" id="unified-image-input" accept="image/*" multiple style="display: none;">
                        </label>
                        <div class="dropdown dropup" style="display: inline-block;">
                            <button class="btn btn-default btn-sm dropdown-toggle" type="button" data-toggle="dropdown" style="border-radius: 20px; border: none; background: rgba(0,0,0,0.05);"><i class="fas fa-tags"></i> Nhãn</button>
                            <ul class="dropdown-menu" style="padding: 10px; min-width: 200px; text-align: left;">
                                @if(isset($labels))
                                    @foreach($labels as $label)
                                    <li><label style="font-weight: normal; display: block; cursor: pointer; padding: 5px 10px; margin: 0;"><input type="checkbox" name="labels[]" class="auto-save-input unified-label-cb" value="{{ $label->id }}"> {{ $label->name }}</label></li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                    </div>
                    <div style="display: flex; align-items: center;">
                        <span class="auto-save-status" style="margin-right: 15px; font-size: 13px; font-weight: bold;"></span>
                        <button type="button" class="btn btn-default" data-dismiss="modal" style="border-radius: 20px; padding: 6px 20px; font-weight: bold; background: rgba(0,0,0,0.05); border: none;">Đóng</button>
                        <button type="submit" id="btn-manual-save" class="btn btn-warning" style="border-radius: 20px; font-weight: bold; padding: 6px 20px; background-color: #202124; border-color: #202124; color: white; margin-left: 5px;"><i class="fas fa-save"></i> Lưu thủ công</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@if(isset($labels) && !$isSharedPage)
<div class="modal fade" id="manageLabelsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content" style="border-radius: 8px;">
            <div class="modal-header" style="border-bottom: 1px solid #eee;">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" style="font-weight: bold; font-size: 16px;">Chỉnh sửa nhãn</h4>
            </div>
            <div class="modal-body">
                <form action="{{ route('labels.store') }}" method="POST" style="margin-bottom: 20px;">
                    {{ csrf_field() }}
                    <div class="input-group">
                        <input type="text" name="name" class="form-control" placeholder="Tạo nhãn mới..." required style="border-radius: 4px 0 0 4px; box-shadow: none;">
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="submit" style="border-radius: 0 4px 4px 0;"><i class="fas fa-check" style="color: #5f6368;"></i></button>
                        </span>
                    </div>
                </form>
                <div class="label-list">
                    @foreach($labels as $label)
                    <div style="margin-bottom: 10px; display: flex; align-items: center; justify-content: space-between;">
                        <form action="{{ route('labels.update', $label->id) }}" method="POST" style="flex-grow: 1; margin-right: 10px;">
                            {{ csrf_field() }} {{ method_field('PUT') }}
                            <div class="input-group" style="width: 100%;">
                                <span class="input-group-addon" style="background: transparent; border: none; padding-left: 0;"><i class="fas fa-tag" style="color: #999;"></i></span>
                                <input type="text" name="name" value="{{ $label->name }}" class="form-control input-sm" style="border: none; background: #f1f3f4; border-radius: 4px; box-shadow: none;" onchange="this.form.submit()" title="Sửa tên và Enter">
                            </div>
                        </form>
                        <form action="{{ route('labels.destroy', $label->id) }}" method="POST" style="margin: 0;">
                            {{ csrf_field() }} {{ method_field('DELETE') }}
                            <button type="submit" class="btn btn-link btn-xs text-danger" style="padding: 0; margin-top: 5px;"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer" style="border-top: none;">
                <button type="button" class="btn btn-default" data-dismiss="modal" style="border-radius: 20px; font-weight: bold; border: none; background: #f1f3f4;">Hoàn tất</button>
            </div>
        </div>
    </div>
</div>
@endif

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="https://js.pusher.com/7.0/pusher.min.js"></script>

<script>
    // =========================================================================
    // KHAI BÁO BIẾN TOÀN CỤC CHUẨN XÁC ĐỂ TRÁNH SẬP MODAL
    // =========================================================================
    var dt = new DataTransfer(); 
    var isClosingAndSaving = false; 
    var timeoutId; 
    var activeEditingNoteId = null; // BIẾN NÀY LÀ CỨU CÁNH CỦA EM ĐÂY!
    var pusher = null;

    // Các hàm phụ trợ
    window.setGridView = function() { 
        $('#notes-container').addClass('grid-active');
        $('.note-item').removeClass('col-sm-12 col-md-12 col-lg-12').addClass('col-sm-6 col-md-6 col-lg-4'); 
        $('#btn-grid').addClass('active').css({'font-weight':'bold','color':'#333'}); 
        $('#btn-list').removeClass('active').css({'font-weight':'normal','color':'#777'}); 
    };
    
    window.setListView = function() { 
        $('#notes-container').removeClass('grid-active');
        $('.note-item').removeClass('col-sm-6 col-md-6 col-lg-4').addClass('col-sm-12 col-md-12 col-lg-12'); 
        $('#btn-list').addClass('active').css({'font-weight':'bold','color':'#333'}); 
        $('#btn-grid').removeClass('active').css({'font-weight':'normal','color':'#777'}); 
    };

    window.openAddModal = function() {
        var form = $('#unifiedNoteForm'); 
        form.attr('action', '{{ route('notes.store') }}'); 
        $('#unifiedMethod').val('POST');
        $('#unifiedTitle').val(''); 
        $('#unifiedContent').val(''); 
        $('.unified-label-cb').prop('checked', false);
        $('#deleted-images-container').empty(); 
        
        isClosingAndSaving = false; 
        activeEditingNoteId = null; // Đặt về rỗng chuẩn xác
        
        dt.items.clear();
        $('#unified-image-input')[0].files = dt.files; 
        renderPreviewImages(''); 
        $('.auto-save-status').text('');
        
        $('#unifiedNoteModal').modal('show');
    };

    function renderPreviewImages(existingImagesStr) {
        var wrapper = $('#preview-images-wrapper'); wrapper.empty(); var hasImages = false;
        if (existingImagesStr && existingImagesStr.trim() !== '') {
            var imagesArr = existingImagesStr.split(',');
            imagesArr.forEach(function(item) {
                var parts = item.split('|'); var id = parts[0]; var url = parts[1];
                wrapper.append(`
                    <div class="preview-img-item" style="position: relative; flex: 1 1 calc(33% - 10px); height: 140px;">
                        <img src="${url}" style="width: 100%; height: 100%; object-fit: contain; background: #f8f9fa; border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                        <button type="button" class="btn btn-danger btn-xs remove-existing-img" data-id="${id}" style="position: absolute; top: -5px; right: -5px; border-radius: 50%; width: 22px; height: 22px; padding: 0;"><i class="fas fa-times"></i></button>
                    </div>
                `);
            });
            hasImages = true;
        }
        if (dt.files.length > 0) {
            Array.from(dt.files).forEach(function(file, index) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    wrapper.append(`
                        <div class="preview-img-item" style="position: relative; flex: 1 1 calc(33% - 10px); height: 140px;">
                            <img src="${e.target.result}" style="width: 100%; height: 100%; object-fit: contain; background: #f8f9fa; border-radius: 6px; border: 2px dashed #1a73e8; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                            <button type="button" class="btn btn-danger btn-xs remove-pending-img" data-index="${index}" style="position: absolute; top: -5px; right: -5px; border-radius: 50%; width: 22px; height: 22px; padding: 0;"><i class="fas fa-times"></i></button>
                        </div>
                    `);
                };
                reader.readAsDataURL(file);
            });
            hasImages = true;
        }
        if (hasImages) { $('#unified-image-preview-container').slideDown('fast'); } else { $('#unified-image-preview-container').slideUp('fast'); }
    }

    function processOfflineQueue() {
        if (navigator.onLine) {
            var queue = JSON.parse(localStorage.getItem('offline_sync_queue'));
            if (queue && queue.length > 0) {
                $('.auto-save-status').text('Đang đẩy dữ liệu Offline lên Server... ⏳').css('color', '#1a73e8');
                var promises = [];
                queue.forEach(function(item) {
                    var p = $.ajax({ type: 'POST', url: item.url, data: { _token: '{{ csrf_token() }}', _method: item.method, title: item.title, content: item.content }});
                    promises.push(p);
                });
                $.when.apply($, promises).done(function() {
                    localStorage.removeItem('offline_sync_queue');
                    alert('✅ Đã đồng bộ thành công tất cả ghi chú bạn tạo/sửa lúc mất mạng!');
                    window.location.reload();
                }).fail(function() {
                    alert('❌ Đồng bộ Offline có lỗi xảy ra. Vui lòng kiểm tra lại mạng!');
                });
            }
        }
    }

    function loadSharedUsers(noteId) {
        $('#shared-users-list').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Đang tải...</div>');
        $.ajax({
            type: 'GET', url: '/notes/' + noteId + '/shared-users',
            success: function(users) {
                var html = '';
                if(users.length === 0) { html = '<p class="text-muted">Chưa chia sẻ cho ai.</p>'; }
                else {
                    users.forEach(function(u) {
                        var isEdit = u.permission === 'edit' ? 'selected' : '';
                        var isRead = u.permission === 'read' ? 'selected' : '';
                        html += `
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px; border-bottom: 1px solid #eee;">
                                <div><strong>${u.email}</strong><br><small class="text-muted">Từ: ${u.formatted_time}</small></div>
                                <div style="display: flex; gap: 5px;">
                                    <select class="form-control input-sm select-permission" data-id="${u.id}" style="width: 110px;">
                                        <option value="read" ${isRead}>Chỉ xem</option>
                                        <option value="edit" ${isEdit}>Được sửa</option>
                                    </select>
                                    <button class="btn btn-danger btn-sm btn-revoke" data-id="${u.id}"><i class="fas fa-times"></i></button>
                                </div>
                            </div>
                        `;
                    });
                }
                $('#shared-users-list').html(html);
            }
        });
    }

    // TÁCH HÀM LƯU ĐỘC LẬP - SIÊU AN TOÀN
    function saveNoteAndReload() {
        if (isClosingAndSaving) return;
        isClosingAndSaving = true;

        $('.auto-save-status').text('Đang đóng ghi chú... ⏳').css('color', '#f39c12');
        $('#btn-manual-save').html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);

        var form = $('#unifiedNoteForm')[0];
        var formData = new FormData(form);

        $.ajax({
            type: 'POST',
            url: $(form).attr('action'),
            data: formData,
            processData: false,
            contentType: false,
            success: function() { window.location.reload(); },
            error: function() { window.location.reload(); }
        });
    }

    // =========================================================================
    // KHỞI ĐỘNG CÁC SỰ KIỆN KHI TRANG TẢI XONG
    // =========================================================================
    $(document).ready(function() {
        
        // Cài đặt Pusher ở mức Global để dùng mượt mà
        pusher = new Pusher('bb8b78ec0431e0a186a8', {
            cluster: 'ap1'
        });

        $('.dropdown-toggle').on('click', function(e) { 
            e.preventDefault(); 
            $('.dropdown-menu').not($(this).next('.dropdown-menu')).hide(); 
            $(this).next('.dropdown-menu').toggle(); 
        });

        $(document).on('click', function(e) { 
            if (!$(e.target).closest('.dropdown').length && !$(e.target).closest('.dropup').length) { 
                $('.dropdown-menu').hide(); 
            } 
        });

        $('.dropdown-menu input, .dropdown-menu label').click(function(e) { 
            e.stopPropagation(); 
        });

        $('.btn-share-modal').click(function() {
            var noteId = $(this).data('id'); $('#share-modal-note-id').val(noteId);
            $('#share-emails-input').val(''); $('#share-msg').text('');
            loadSharedUsers(noteId); $('#advancedShareModal').modal('show');
        });

        $('#btn-submit-share').click(function() {
            var noteId = $('#share-modal-note-id').val(); var emails = $('#share-emails-input').val(); var perm = $('#share-permission-select').val();
            $('#share-msg').html('<i class="fas fa-spinner fa-spin"></i> Đang xử lý...').css('color', '#f39c12');
            $.ajax({
                type: 'POST', url: '/notes/share/' + noteId,
                data: { _token: '{{ csrf_token() }}', emails: emails, permission: perm },
                success: function(res) {
                    $('#share-msg').text(res.message).css('color', res.success ? '#28a745' : '#e74c3c');
                    $('#share-emails-input').val(''); loadSharedUsers(noteId);
                }
            });
        });

        $(document).on('change', '.select-permission', function() {
            var noteId = $('#share-modal-note-id').val(); var userId = $(this).data('id'); var perm = $(this).val();
            $.ajax({ type: 'POST', url: '/notes/' + noteId + '/update-share', data: { _token: '{{ csrf_token() }}', user_id: userId, permission: perm } });
        });

        $(document).on('click', '.btn-revoke', function() {
            if(!confirm('Thu hồi quyền truy cập của người này?')) return;
            var noteId = $('#share-modal-note-id').val(); var userId = $(this).data('id');
            $.ajax({ type: 'POST', url: '/notes/' + noteId + '/revoke-share', data: { _token: '{{ csrf_token() }}', user_id: userId }, success: function() { loadSharedUsers(noteId); } });
        });

        $('#unified-image-input').on('change', function() {
            for(var i = 0; i < this.files.length; i++) { dt.items.add(this.files[i]); }
            this.files = dt.files; 
            var existingStr = $('#unifiedMethod').val() === 'PUT' ? $('.btn-edit-note[data-id="'+activeEditingNoteId+'"]').data('images') : '';
            renderPreviewImages(existingStr);
            var statusText = $('.auto-save-status'); clearTimeout(timeoutId);
            statusText.text('Đã gắn hình ảnh ✅').css('color', '#28a745'); timeoutId = setTimeout(function() { statusText.text(''); }, 2000);
        });

        $(document).on('click', '.remove-pending-img', function() {
            var indexToRemove = $(this).data('index'); var newDt = new DataTransfer(); var files = dt.files;
            for(var i = 0; i < files.length; i++) { if(i !== indexToRemove) { newDt.items.add(files[i]); } }
            dt = newDt; $('#unified-image-input')[0].files = dt.files;
            var existingStr = $('#unifiedMethod').val() === 'PUT' ? $('.btn-edit-note[data-id="'+activeEditingNoteId+'"]').data('images') : '';
            renderPreviewImages(existingStr);
            var statusText = $('.auto-save-status'); clearTimeout(timeoutId);
            statusText.text('Đã gỡ hình ảnh ✅').css('color', '#28a745'); timeoutId = setTimeout(function() { statusText.text(''); }, 2000);
        });

        $(document).on('click', '.remove-existing-img', function() {
            var id = $(this).data('id');
            $('#deleted-images-container').append('<input type="hidden" name="deleted_images[]" value="'+id+'">');
            $(this).parent('.preview-img-item').remove();
            var btn = $('.btn-edit-note[data-id="'+activeEditingNoteId+'"]'); var existingStr = btn.data('images');
            if(existingStr) { var newArr = existingStr.split(',').filter(item => !item.startsWith(id + '|')); btn.data('images', newArr.join(',')); }
            var statusText = $('.auto-save-status'); clearTimeout(timeoutId);
            statusText.text('Đã xóa hình ảnh ✅').css('color', '#28a745'); timeoutId = setTimeout(function() { statusText.text(''); }, 2000);
            if ($('#unifiedMethod').val() === 'PUT' && navigator.onLine) { $.ajax({ type: 'POST', url: $('#unifiedNoteForm').attr('action'), data: $('#unifiedNoteForm').serialize(), success: function() {} }); }
        });

        $('.btn-edit-note').click(function() {
            var btn = $(this); var form = $('#unifiedNoteForm'); activeEditingNoteId = btn.data('id'); isClosingAndSaving = false;
            form.attr('action', btn.data('update-url')); $('#unifiedMethod').val('PUT');
            $('#unifiedTitle').val(btn.data('title')); $('#unifiedContent').val(btn.data('content')); $('#deleted-images-container').empty();
            var labelsStr = String(btn.data('labels')); var labelArr = labelsStr ? labelsStr.split(',') : [];
            $('.unified-label-cb').each(function() { $(this).prop('checked', labelArr.includes($(this).val())); });
            dt.items.clear(); $('#unified-image-input')[0].files = dt.files;
            renderPreviewImages(btn.data('images')); $('.auto-save-status').text('');
            $('#unifiedNoteModal').modal('show');
        });

        $('.auto-save-input').on('input change', function() {
            if (!navigator.onLine) {
                $('.auto-save-status').text('Đang nháp Offline... ⚠️').css('color', '#e74c3c');
                return;
            }
            var isCreatingMode = $('#unifiedMethod').val() === 'POST';
            var form = $('#unifiedNoteForm'); clearTimeout(timeoutId);
            
            if (isCreatingMode) {
                $('.auto-save-status').text('Đang tự động lưu... ⏳').css('color', '#f39c12');
                timeoutId = setTimeout(function() { 
                    $('.auto-save-status').text('Ghi chú đã tự động lưu ✅').css('color', '#28a745'); 
                    setTimeout(function() { $('.auto-save-status').text(''); }, 2000); 
                }, 1000);
            } else {
                $('.auto-save-status').text('Đang tự động lưu... ⏳').css('color', '#f39c12');
                timeoutId = setTimeout(function() {
                    $.ajax({
                        type: 'POST', url: form.attr('action'), data: form.serialize(), 
                        success: function(res) {
                            if(res.success) {
                                $('.auto-save-status').text('Ghi chú đã tự động lưu ✅').css('color', '#28a745');
                                $('#deleted-images-container').empty(); 
                                setTimeout(function() { $('.auto-save-status').text(''); }, 2000);
                            }
                        },
                        error: function() { $('.auto-save-status').text('Lỗi kết nối! ❌').css('color', '#e74c3c'); }
                    });
                }, 1000);
            }
        });

        // BẮT SỰ KIỆN SUBMIT TỪ NÚT LƯU HOẶC NHẤN ENTER
        $('#unifiedNoteForm').on('submit', function(e) {
            e.preventDefault(); 
            var title = $('#unifiedTitle').val().trim(); 
            var content = $('#unifiedContent').val().trim(); 
            var hasFiles = dt.files.length > 0;

            if (title === '' && content === '' && !hasFiles) {
                $('#unifiedNoteModal').modal('hide');
                return;
            }

            if (!navigator.onLine) {
                var queue = JSON.parse(localStorage.getItem('offline_sync_queue')) || [];
                queue.push({ method: $('#unifiedMethod').val(), url: $(this).attr('action'), title: title, content: content });
                localStorage.setItem('offline_sync_queue', JSON.stringify(queue));
                alert('⚠️ BẠN ĐANG OFFLINE!\nGhi chú đã được lưu nháp vào máy.');
                
                // CỰC KỲ QUAN TRỌNG: Xóa trắng form để không bị "lưu đúp"
                $('#unifiedTitle').val('');
                $('#unifiedContent').val('');
                dt.items.clear();
                
                $('#unifiedNoteModal').modal('hide');
                return;
            }

            saveNoteAndReload();
        });

        // BẮT SỰ KIỆN KHI TẮT KHUNG
        $('#unifiedNoteModal').on('hide.bs.modal', function (e) {
            // Hủy đăng ký Pusher an toàn
            if(activeEditingNoteId !== null && pusher !== null) {
                pusher.unsubscribe('note.' + activeEditingNoteId);
            }

            var title = $('#unifiedTitle').val().trim(); 
            var content = $('#unifiedContent').val().trim(); 
            var hasFiles = dt.files.length > 0;

            // Nếu trống -> cho đóng tự nhiên
            if (title === '' && content === '' && !hasFiles) {
                activeEditingNoteId = null; 
                return; 
            }

            if (!navigator.onLine) {
                var queue = JSON.parse(localStorage.getItem('offline_sync_queue')) || [];
                queue.push({ method: $('#unifiedMethod').val(), url: $('#unifiedNoteForm').attr('action'), title: title, content: content });
                localStorage.setItem('offline_sync_queue', JSON.stringify(queue));
                alert('⚠️ BẠN ĐANG OFFLINE!\nGhi chú đã được lưu nháp vào máy.');
                
                // Xóa trắng form an toàn
                $('#unifiedTitle').val('');
                $('#unifiedContent').val('');
                dt.items.clear();
                activeEditingNoteId = null; 
                return;
            }

            // Có dữ liệu thì chặn tắt, bắt buộc Lưu
            if (!isClosingAndSaving) {
                e.preventDefault(); 
                saveNoteAndReload();
            }
        });

        var currentPusherChannel = null;

        $('#unifiedNoteModal').on('shown.bs.modal', function (e) {
            var formAction = $('#unifiedNoteForm').attr('action'); 
            var isEditMode = $('#unifiedMethod').val() === 'PUT';
            
            if(isEditMode && formAction && formAction.includes('/notes/')) {
                activeEditingNoteId = formAction.split('/').pop();
                
                currentPusherChannel = pusher.subscribe('note.' + activeEditingNoteId);
                
                currentPusherChannel.bind('App\\Events\\NoteUpdated', function(data) {
                    var titleInput = $('#unifiedTitle'); 
                    var contentInput = $('#unifiedContent'); 
                    var statusText = $('.auto-save-status');
                    
                    var changed = false;
                    var currentTitle = (titleInput.val() || '').replace(/\r\n/g, '\n').normalize();
                    var serverTitle = (data.title || '').replace(/\r\n/g, '\n').normalize();
                    var currentContent = (contentInput.val() || '').replace(/\r\n/g, '\n').normalize();
                    var serverContent = (data.content || '').replace(/\r\n/g, '\n').normalize();
                    
                    if(currentTitle !== serverTitle) { 
                        var tStart = titleInput[0].selectionStart;
                        var tEnd = titleInput[0].selectionEnd;
                        titleInput.val(data.title); 
                        if(titleInput.is(':focus')) titleInput[0].setSelectionRange(tStart, tEnd);
                        changed = true; 
                    }
                    
                    if(currentContent !== serverContent) { 
                        var cStart = contentInput[0].selectionStart;
                        var cEnd = contentInput[0].selectionEnd;
                        contentInput.val(data.content); 
                        if(contentInput.is(':focus')) contentInput[0].setSelectionRange(cStart, cEnd);
                        changed = true; 
                    }
                    
                    if(changed) {
                        statusText.text('⚡ Đồng bộ Live!').css('color', '#9b59b6');
                        setTimeout(function() { statusText.text(''); }, 2000);
                    }
                });
            }
        });

        $('.btn-pin').click(function() { var btn = $(this); var noteId = btn.data('id'); btn.css('opacity', '0.5'); $.ajax({ type: 'POST', url: '/notes/pin/' + noteId, data: { _token: '{{ csrf_token() }}' }, success: function(res) { if(res.success) window.location.reload(); } }); });
        $('.btn-set-password').click(function() { var noteId = $(this).data('id'); var isLocked = $(this).find('i').hasClass('fa-lock'); var titleMsg = isLocked ? "🔐 ĐỔI MẬT KHẨU MỚI:" : "🔐 CÀI ĐẶT BẢO MẬT:"; var pass = prompt(titleMsg + "\n- Nhập mật khẩu mới.\n- Để trống và bấm OK để GỠ mật khẩu."); if (pass !== null) { $.ajax({ type: 'POST', url: '/notes/set-password/' + noteId, data: { _token: '{{ csrf_token() }}', password: pass }, success: function(res) { if(res.success) { alert(res.message); window.location.reload(); } } }); } });
        $('.btn-unlock').click(function() { var noteId = $(this).data('id'); var pass = prompt("🔒 Ghi chú này đã được khóa. Vui lòng nhập mật khẩu để mở:"); if (pass) { $.ajax({ type: 'POST', url: '/notes/unlock/' + noteId, data: { _token: '{{ csrf_token() }}', password: pass }, success: function(res) { if(res.success) { window.location.reload(); } else { alert(res.message); } } }); } });
        $('.btn-lock-now').click(function() { var noteId = $(this).data('id'); $.ajax({ type: 'POST', url: '/notes/lock/' + noteId, data: { _token: '{{ csrf_token() }}' }, success: function(res) { if(res.success) window.location.reload(); } }); });

        processOfflineQueue();
        window.addEventListener('online', function() { processOfflineQueue(); });
        window.addEventListener('offline', function() { $('.auto-save-status').text('Mất mạng! (Bật chế độ nháp máy) ⚠️').css('color', '#e74c3c'); });
    });
</script>
@endsection