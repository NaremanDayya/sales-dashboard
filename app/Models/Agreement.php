<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agreement extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'client_id',
        'sales_rep_id',
        'service_id',
        'signing_date',
        'duration_years',
        'end_date',
        'termination_type',
        'notice_months',
        'notice_date',
        'notice_status',
        'product_quantity',
        'price',
        'total_amount',
        'agreement_status',
        'implementation_date',
        'return_value'
    ];
    protected $casts = [
        'signing_date' => 'date',
        'end_date' => 'date',
        'implementation_date' => 'date',
        'notice_date' => 'date',


    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function salesRep()
    {
        return $this->belongsTo(SalesRep::class, 'sales_rep_id');
    }

    public function editRequests()
    {
        return $this->hasMany(AgreementEditRequest::class);
    }
    public function isNoticedAtTime(): bool
    {
        if (!$this->notice_date) {
            return false;
        }

           return Carbon::parse($this->notice_date)->lessThanOrEqualTo($this->getRequiredNoticeDate());
    }
    public function isWithinNoticePeriod(): bool
    {
        $noticeDeadline = Carbon::parse($this->end_date)->subMonths($this->notice_months);
        return now()->lessThanOrEqualTo($noticeDeadline);
    }
    public function getRequiredNoticeDate()
    {
        $noticeDeadline = Carbon::parse($this->end_date)->subMonths($this->notice_months);
        return $noticeDeadline;
    }
}
