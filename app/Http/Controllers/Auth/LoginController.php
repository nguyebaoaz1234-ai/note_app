<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    // ========================================================
    // ĐÃ MỞ CỔNG: CHO PHÉP TÀI KHOẢN CHƯA KÍCH HOẠT ĐĂNG NHẬP 
    // (Cập nhật theo mục 2.1 Account Management)
    // ========================================================
    protected function authenticated(Request $request, $user)
    {
        // Thầy đã comment (vô hiệu hóa) đoạn code chặn người dùng dưới đây
        /*
        // Nếu cột is_active trong Database vẫn bằng 0 (Chưa click link trong mail)
        if ($user->is_active == 0) {
            
            Auth::logout(); // Đá văng tài khoản ra ngoài ngay lập tức
            
            // Trả về trang đăng nhập kèm theo dòng chữ cảnh báo màu đỏ
            return redirect('/login')->with('warning', 'Tài khoản của bạn chưa được kích hoạt! Vui lòng kiểm tra Email để lấy link.');
        }
        */
        
        // Hiện tại hàm này được để trống. Laravel sẽ tự động cho phép người dùng đăng nhập 
        // và chuyển hướng họ đến đường dẫn $redirectTo (tức là /home).
    }
}