<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth; // Thầy bổ sung thư viện Auth ở đây

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    protected function create(array $data)
    {
        $token = Str::random(40); // Tạo chuỗi ngẫu nhiên 40 ký tự
        $activationLink = url('/activate/' . $token); // Tạo link kích hoạt
        
        // GỬI EMAIL THỰC TẾ
        Mail::send('emails.activation', ['link' => $activationLink], function ($message) use ($data) {
            $message->to($data['email'], $data['name'])
                    ->subject('Vui lòng kích hoạt tài khoản Ghi chú của bạn!');
        });

        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'is_active' => 0, // Mặc định khóa
            'activation_token' => $token
        ]);
    }

    // ========================================================
    // ĐÃ CHỈNH SỬA: TỰ ĐỘNG ĐĂNG NHẬP SAU KHI ĐĂNG KÝ
    // (Cập nhật theo mục 2.1 Account Management)
    // ========================================================
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();
        
        // Tạo User, gửi Mail và HỨNG LẤY biến $user vừa tạo
        $user = $this->create($request->all());

        // Tự động đăng nhập ngay lập tức cho user này
        Auth::login($user);

        // Chuyển hướng thẳng vào trang chủ (Biển báo màu vàng ở Bước 3 sẽ đón người dùng ở đây)
        return redirect($this->redirectTo);
    }
}