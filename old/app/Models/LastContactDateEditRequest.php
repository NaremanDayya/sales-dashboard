<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LastContactDateEditRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'sales_rep_id',
        'client_id',
        'status',
        'request_type',
        'description',
        'response_date',
        'notes',
        'current_last_contact_date',
    ];

    // Relationships
    public function salesRep()
    {
        return $this->belongsTo(User::class, 'sales_rep_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
