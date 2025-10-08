<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgreementEditRequest extends Model
{
    protected $fillable = [
        'agreement_id',
        'sales_rep_id',
        'client_id',
        'status',
        'request_type',
        'description',
        'response_date',
        'notes',
        'payload',
        'edited_field',
    ];
    protected $casts =[
        'payload' => 'array',

    ];

    public function agreement()
    {
        return $this->belongsTo(Agreement::class);
    }

    public function salesRep()
    {
        return $this->belongsTo(User::class, foreignKey: 'sales_rep_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
