<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Namu\WireChat\Traits\Chatable;
use Spatie\Permission\Traits\HasRoles;
use Carbon\Carbon;

class SalesRep extends Model
{
    use HasFactory, HasRoles, Notifiable, Chatable;
    protected $table = "sales_representatives";

    protected $fillable = [
        'id',
        'name',
        'start_work_date',
        'work_duration',
        'target_customers',
        'late_customers',
        'total_orders',
        'pending_orders',
        'interested_customers',
        'user_id',
    ];
protected $appends = ['active_agreements_count', 'inactive_agreements_count'];

    protected $casts = [
        'start_work_date' => 'date',
    ];

	public function loginIps()
{
    return $this->hasMany(SalesRepLoginIp ::class);
}	

    public function clients()
    {
        return $this->hasMany(Client::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function agreements()
    {
        return $this->hasMany(Agreement::class, 'sales_rep_id');
    }
public function getActiveAgreementsCountAttribute()
{
    return $this->agreements()->where('agreement_status', 'active')->count();
}

public function getInactiveAgreementsCountAttribute()
{
    return $this->agreements()->where('agreement_status', 'expired')->count();
}
    public function commissions()
    {
        return $this->hasMany(Commission::class, 'sales_rep_id');
    }
public function clientRequest()
{
    return $this->hasMany(ClientEditRequest::class, 'sales_rep_id', 'user_id');
}

public function agreementRequest()
{
    return $this->hasMany(AgreementEditRequest::class, 'sales_rep_id', 'user_id');
}
public function chatClientRequest()
{
    return $this->hasMany(ClientRequest::class, 'sales_rep_id', 'user_id');
}

    public function getTotalOrdersAttribute()
    {
        return $this->clientRequest()->count() + $this->agreementRequest()->count() + $this->chatClientRequest()->count();
    }

    public function getLateCustomersAttribute()
    {
        $time = now()->subDays(3);
        return $this->clients()->where('last_contact_date', '<=', $time)->count();
    }

    public function targets()
    {
        return $this->hasMany(Target::class, 'sales_rep_id');
    }
    public function currentMonthAchievedAmount()
    {
        $now = now();

        return $this->targets()
            ->where('month', $now->month)
            ->where('year', $now->year)
            ->value('achieved_amount');
    }
    public function serviceMonthAchievedAmount(Service $service)
    {
        $now = now();

        return $this->targets()
            ->where('month', $now->month)
            ->where('year', $now->year)
            ->where('service_id', $service->id)
            ->value('achieved_amount');
    }
    public function currentMonthTargetAmount()
    {
        $now = now();

        return $this->targets()
            ->where('month', $now->month)
            ->where('year', $now->year)
            ->value('achieved_amount');
    }
    public function currentMonthAchievedPercentage()
    {
        $now = now();

        return $this->targets()
            ->where('month', $now->month)
            ->where('year', $now->year)
            ->value('achieved_percentage');
    }
    public function currentMonthAchievedCommission()
    {
        $now = now();

        return $this->commissions()
            ->where('month', $now->month)
            ->where('year', $now->year)
            ->value('commission_amount');
    }
    public function pendedRequest()
    {
        return $this->clientRequest()
            ->where('status', 'pending');
    }
    public function getTotalPendedRequestsAttribute()
    {
        return $this->pendedRequest()->count() +
            $this->agreementRequest()->where('status', 'pending')->count() +
 $this->chatClientRequest()->where('status', 'pending')->count();
    }
    public function interestedClients()
    {
        return $this->clients()->where('interest_status', 'interested');
    }

    public function temporaryPermissions()
    {
        return $this->hasMany(TemporaryPermission::class, 'user_id', 'user_id');
    }
    public function myLastPermission(Model $model, string $field): ?TemporaryPermission
    {
        return $this->temporaryPermissions()
            ->where('permissible_id', $model->id)
            ->where('permissible_type', $model->getMorphClass())   // respects morphMap
            ->where('field', $field)
            ->where('used', false)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->latest()
            ->first();
    }
    function translateDurationToArabic($duration)
    {
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
	public function getWorkDurationAttribute()
{
    if (!$this->start_work_date) { 
        return null;
    }

    $startDate = Carbon::parse($this->start_work_date);
    $diff = $startDate->diff(Carbon::now());

    $duration = "{$diff->y} years, {$diff->m} months, {$diff->d} days";

    return $this->translateDurationToArabic($duration);
}
}
