<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{

    protected $fillable = ['receiver_id', 'sender_id', 'message', 'is_admin', 'conversation_id', 'read_at', 'receiver_deleted_at', 'sender_deleted_at'];
    protected $dates = [
        'read_at',
        'receiver_deleted_at',
        'sender_deleted_at',

    ];

    /* relationship */

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function isRead(): bool
    {

        return $this->read_at != null;
    }
}
