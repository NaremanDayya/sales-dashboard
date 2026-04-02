<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManagerClientChat extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'sales_rep_id',
        'manager_id',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function salesRep()
    {
        return $this->belongsTo(User::class, 'sales_rep_id');
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function messages()
    {
        return $this->hasMany(ManagerChatMessage::class);
    }

    public function latestMessage()
    {
        return $this->hasOne(ManagerChatMessage::class)->latestOfMany();
    }

    public function unreadMessagesFor($userId)
    {
        return $this->messages()
            ->where('sender_id', '!=', $userId)
            ->whereNull('read_at')
            ->count();
    }
}
