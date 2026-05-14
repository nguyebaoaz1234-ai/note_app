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

    public function sendPasswordResetNotification($token)
    {
        $link = url('/password/reset/' . $token);
        $body = "<h3>Khôi phục mật khẩu</h3>
                 <p>Vui lòng click vào link dưới để đặt lại mật khẩu:</p>
                 <p><a href='" . $link . "'>Đặt lại mật khẩu</a></p>";
        \App\Http\Controllers\Controller::sendGasEmail($this->email, "Khôi phục mật khẩu", $body);
    }
}