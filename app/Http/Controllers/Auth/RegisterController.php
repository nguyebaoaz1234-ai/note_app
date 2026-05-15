<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth; 

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
        
        // ========================================================
        // GỬI MAIL QUA GOOGLE APPS SCRIPT BẰNG PHP THUẦN
        // ========================================================
        
        // 1. Dịch giao diện email thành mã HTML
        $htmlContent = view('emails.activation', ['link' => $activationLink])->render();

        // 2. Đóng gói dữ liệu thành chuẩn JSON (Đã chuẩn hóa key 'email')
        $postData = json_encode([
            'email' => $data['email'], 
            'subject' => 'Vui lòng kích hoạt tài khoản Ghi chú của bạn!',
            'body' => $htmlContent
        ]);

        // 3. Cấu hình luồng bắn dữ liệu HTTP POST và VƯỢT RÀO SSL
        $options = [
            'http' => [
                'header'  => "Content-type: application/json\r\n",
                'method'  => 'POST',
                'content' => $postData,
            ],
            // THÊM ĐOẠN NÀY ĐỂ VƯỢT RÀO SSL TRÊN LOCALHOST
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ]
        ];
        $context  = stream_context_create($options);

        // 4. Bắn dữ liệu thẳng sang Webhook của Google (Đã chuẩn hóa tên biến môi trường)
        $gasUrl = env('GAS_WEBHOOK_URL');
        if ($gasUrl) {
            @file_get_contents($gasUrl, false, $context);
        }

        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'is_active' => 0, // Mặc định khóa
            'activation_token' => $token
        ]);
    }

    // ========================================================
    // TỰ ĐỘNG ĐĂNG NHẬP SAU KHI ĐĂNG KÝ
    // ========================================================
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();
        
        // Tạo User, gửi Mail và HỨNG LẤY biến $user vừa tạo
        $user = $this->create($request->all());

        // Tự động đăng nhập ngay lập tức cho user này
        Auth::login($user);

        // Chuyển hướng thẳng vào trang chủ
        return redirect($this->redirectTo);
    }
}