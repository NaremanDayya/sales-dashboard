<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SalesRepWorkHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'sales_rep_id',
        'sales_rep_name',
        'start_date',
        'end_date',
        'recorded_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function salesRep()
    {
        return $this->belongsTo(SalesRep::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function getIsActiveAttribute()
    {
        return is_null($this->end_date);
    }

    public function getDurationInDaysAttribute()
    {
        if (!$this->start_date) {
            return 0;
        }

        $end = $this->end_date ? Carbon::parse($this->end_date) : Carbon::now();

        return Carbon::parse($this->start_date)->diffInDays($end);
    }

    public function getPeriodAttribute()
    {
        if (!$this->start_date) {
            return null;
        }

        $end = $this->end_date ? Carbon::parse($this->end_date) : Carbon::now();
        $diff = Carbon::parse($this->start_date)->diff($end);

        $parts = [];
        if ($diff->y > 0) {
            $parts[] = "{$diff->y} سنة";
        }
        if ($diff->m > 0) {
            $parts[] = "{$diff->m} شهر";
        }
        if ($diff->d > 0 || empty($parts)) {
            $parts[] = "{$diff->d} يوم";
        }

        return implode(' و ', $parts);
    }
}
