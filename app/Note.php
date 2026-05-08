<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Note extends Model {
    //
public function attachments() {
    // Một ghi chú có thể có nhiều ảnh đính kèm
    return $this->hasMany('App\NoteAttachment', 'note_id');
}
public function labels() {
        // Ghi chú này thuộc về nhiều Nhãn (thông qua bảng trung gian label_note)
    return $this->belongsToMany('App\Label', 'label_note', 'note_id', 'label_id');
}

public function sharedUsers() {
    return $this->belongsToMany('App\User', 'note_user', 'note_id', 'user_id')
                ->withPivot('permission')
                ->withTimestamps();
}

// Bổ sung hàm này để Ghi chú nhận diện được Chủ sở hữu (Người gửi)
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
    }


