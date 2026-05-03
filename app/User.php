<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable 
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'is_active', 'activation_token',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function sharedNotes() {
        return $this->belongsToMany('App\Note', 'note_user', 'user_id', 'note_id');
    }
}