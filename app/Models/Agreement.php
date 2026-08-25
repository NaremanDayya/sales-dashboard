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
        'finish_date',
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
        'finish_date' => 'date',
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

    /**
     * An agreement's current term is considered concluded in exactly two cases:
     * the sales rep ends it manually (finish()), or the notice period lapses
     * with no cancellation notice received, in which case it renews (renew()).
     */
    public function isFinished(): bool
    {
        return in_array($this->agreement_status, ['terminated', 'expired'], true);
    }

    /**
     * Manually finish the agreement (sales rep chose a finish date via the popup,
     * which may be earlier than the originally planned end_date).
     */
    public function finish(string $finishDate): void
    {
        $this->update([
            'agreement_status' => 'terminated',
            'finish_date' => $finishDate,
        ]);
    }

    /**
     * Auto-renew the agreement for another term because no cancellation notice
     * was received within the allowed notice period.
     */
    public function renew(): void
    {
        $newImplementationDate = Carbon::parse($this->end_date)->addDay();
        $newEndDate = $newImplementationDate->copy()->addYears((int) $this->duration_years);

        $this->update([
            'signing_date' => now(),
            'implementation_date' => $newImplementationDate,
            'end_date' => $newEndDate,
            'notice_date' => null,
            'notice_status' => 'not_sent',
        ]);
    }
}
