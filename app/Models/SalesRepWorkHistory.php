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

    public function getPeriodAttribute()
    {
        if (!$this->start_date || !$this->end_date) {
            return null;
        }

        $diff = Carbon::parse($this->start_date)->diff(Carbon::parse($this->end_date));
        $duration = "{$diff->y} years, {$diff->m} months, {$diff->d} days";

        $replacements = [
            'years' => 'سنة',
            'year' => 'سنة',
            'months' => 'شهر',
            'month' => 'شهر',
            'days' => 'يوم',
            'day' => 'يوم',
            ',' => '،',
        ];

        return strtr($duration, $replacements);
    }
}
