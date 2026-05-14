<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable 
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'is_active', 'activation_token', 'avatar', 'dark_mode', 'note_font_size', 'note_color',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function sharedNotes() {
        return $this->belongsToMany('App\Note', 'note_user', 'user_id', 'note_id');
    }

    /**
     * Ghi đè phương thức gửi mail Quên mật khẩu.
     * Bắn thẳng API sang Google Apps Script với Giao diện (Template) tuyệt đẹp.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        // 1. Tạo đường link khôi phục mật khẩu hoàn chỉnh
        $url = url(config('app.url').route('password.reset', [$token, 'email' => $this->getEmailForPasswordReset()], false));
        
        // 2. Thiết kế Giao diện Email bằng HTML và CSS Inline
        $htmlBody = <<<HTML
        <div style="font-family: Arial, sans-serif; background-color: #f8f9fa; padding: 50px 20px;">
            <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; padding: 40px 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); text-align: center;">
                <h2 style="color: #333333; font-size: 24px; margin-bottom: 20px;">Khôi phục mật khẩu Hệ thống Ghi chú!</h2>
                
                <p style="color: #555555; font-size: 16px; line-height: 1.6; margin-bottom: 30px;">
                    Cảm ơn bạn đã sử dụng hệ thống. Bạn nhận được email này vì chúng tôi đã nhận được yêu cầu khôi phục mật khẩu cho tài khoản của bạn. Vui lòng nhấn vào nút bên dưới để tiến hành đặt lại mật khẩu:
                </p>
                
                <a href="{$url}" style="display: inline-block; background-color: #212529; color: #ffffff; text-decoration: none; padding: 14px 40px; border-radius: 30px; font-weight: bold; font-size: 16px;">Đặt lại mật khẩu</a>
                
                <p style="color: #999999; font-size: 14px; margin-top: 40px; border-top: 1px solid #eeeeee; padding-top: 20px;">
                    Nếu bạn không thực hiện yêu cầu này, vui lòng bỏ qua email này.
                </p>
            </div>
        </div>
        HTML;

        // 3. Chuẩn bị gói hàng (Data) gửi cho Google Apps Script
        $data = [
            'email' => $this->getEmailForPasswordReset(),
            'subject' => 'Khôi phục mật khẩu - Hệ thống Ghi chú',
            'body' => $htmlBody
        ];

        // 4. Bắn dữ liệu sang Google Apps Script
        $options = [
            'http' => [
                'header'  => "Content-type: application/json\r\n",
                'method'  => 'POST',
                'content' => json_encode($data),
            ]
        ];
        
        $context = stream_context_create($options);
        @file_get_contents(env('GAS_WEBHOOK_URL'), false, $context);
    }
}