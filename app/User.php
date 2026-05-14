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
     * Bắn thẳng API sang Google Apps Script, bỏ qua hệ thống Notification rườm rà.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        // 1. Tạo đường link khôi phục mật khẩu hoàn chỉnh
        $url = url(config('app.url').route('password.reset', [$token, 'email' => $this->getEmailForPasswordReset()], false));
        
        // 2. Chuẩn bị gói hàng (Data) gửi cho Google Apps Script
        $data = [
            'email' => $this->getEmailForPasswordReset(),
            'subject' => 'Khôi phục mật khẩu - NoteApp',
            'body' => "<h3>Chào bạn!</h3><p>Vui lòng bấm vào link dưới đây để đặt lại mật khẩu của bạn:</p><a href='{$url}'>{$url}</a>"
        ];

        // 3. Sử dụng PHP thuần để gửi HTTP POST Request thẳng sang Google
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