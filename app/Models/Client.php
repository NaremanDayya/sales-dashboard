<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Namu\WireChat\Traits\Chatable;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

class Client extends Model
{
    use HasFactory, Chatable;

    protected $fillable = [
        'company_name',
        'company_logo',
        'address',
        'contact_person',
        'contact_position',
        'phone',
        'whatsapp_link',
        'interest_status',
        'sales_rep_id',
        'last_contact_date',
        'contact_count',
        'interested_service',
        'interested_service_count',
        'country_code',
        'contact_details',
    ];
    protected $casts = [
        'last_contact_date' => 'date',
    ];

    public function salesRep()
    {
        return $this->belongsTo(SalesRep::class);
    }
    public function agreements()
    {
        return $this->hasMany(Agreement::class);
    }
    public function clientEditRequests()
    {
        return $this->hasMany(ClientEditRequest::class);
    }
      public function agreementEditRequests()
    {
        return $this->hasMany(AgreementEditRequest::class);
    }
     public function clientRequests()
    {
        return $this->hasMany(ClientRequest::class);
    }
    public function allEditRequests()
    {
        return $this->clientEditRequests->merge($this->agreementEditRequests)->merge($this->clientRequests);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isLateCustomer()
    {
        if (!$this->last_contact_date) {
            return false;
        }

        $lateDays = Setting::where('key', 'late_customer_days')->value('value') ?? 3;

        $time = now()->copy();
        $daysCounted = 0;

        while ($daysCounted < $lateDays) {
            $time->subDay();
            // Carbon: 0 = الأحد ... 5 = الجمعة, 6 = السبت
            if (!in_array($time->dayOfWeek, [5, 6])) {
                $daysCounted++;
            }
        }

        return $this->last_contact_date <= $time;
    }
    public function conversations()
    {
        return $this->hasMany(Conversation::class,'client_id');
    }
    public function getLateDaysAttribute()
    {
        $last_contact_date = Carbon::Parse($this->last_contact_date);
        return (int)$last_contact_date->diffInDays(now());
    }
//    public function getCompanyLogoAttribute()
//    {
//        $logo = $this->attributes['company_logo'] ?? null;
//
//        if (!$logo) {
//            return $this->getDefaultLogo();
//        }
//
//        // Remove any storage path prefixes that might be stored in database
//        $logo = str_replace('storage/app/private/', '', $logo);
//        $logo = str_replace('private/', '', $logo);
//        $logo = ltrim($logo, '/');
//
//        // Define possible storage paths to check
//        $pathsToCheck = [
//            $logo, // original path
//            'private/' . $logo, // with private prefix
//            'temp/' . $logo, // in temp directory
//            'private/temp/' . $logo, // in private/temp directory
//        ];
//
//        // Check public disk first
//        if (Storage::disk('public')->exists($logo)) {
//            return asset('storage/' . $logo);
//        }
//
//        // Check all possible private paths
//        foreach ($pathsToCheck as $path) {
//            if (Storage::disk('local')->exists($path)) {
//                return route('file.serve', ['filename' => $path]);
//            }
//        }
//
//        return $this->getDefaultLogo();
//    }
    public function getCompanyLogoAttribute()
    {
        $logo = $this->attributes['company_logo'] ?? null;

        if (!$logo) {
            return null;
        }

        if (Storage::disk('s3')->exists($logo)) {
            return Storage::disk('s3')->temporaryUrl($logo, now()->addMinutes(1));
        }

        if (Storage::disk('public')->exists($logo)) {
            return asset('storage/' . $logo);
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->attributes['company_name']) . '&background=random';
    }
    protected function getDefaultLogo()
    {
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->attributes['company_name'] ?? 'Company') . '&background=random';
    }

}
