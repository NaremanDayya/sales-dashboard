<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientRequest extends Model
{
   protected $fillable = [
        'client_id',
        'sales_rep_id',
        'message',
        'status',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function salesRep()
    {
        return $this->belongsTo(User::class, 'sales_rep_id');
    }
}
