<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientEditRequest extends Model
{
    protected $fillable = [
        'client_id',
        'sales_rep_id',
        'status',
        'request_type',
        'description',
        'response_date',
        'notes',
        'edited_field',
        'new_value',
        'payload',
    ];
    protected $casts = [
    'response_date' => 'datetime',
        'payload' => 'array',
];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function salesRep()
    {
        return $this->belongsTo(User::class, foreignKey: 'sales_rep_id');
    }
}
