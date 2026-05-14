<?php

namespace App\Events;

use App\Note;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

// Phải có chữ implements ShouldBroadcast để Laravel hiểu đây là WebSocket
class NoteUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $note;

    public function __construct(Note $note)
    {
        $this->note = $note;
    }

    // Phát sóng vào một kênh chung có tên là note.ID
    public function broadcastOn()
    {
        return new Channel('note.' . $this->note->id);
    }

    // Dữ liệu được gửi qua đường truyền WebSocket
    public function broadcastWith()
    {
        return [
            'id' => $this->note->id,
            'title' => $this->note->title,
            'content' => $this->note->content,
        ];
    }
}