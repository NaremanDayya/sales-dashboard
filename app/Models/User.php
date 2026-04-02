<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Namu\WireChat\Traits\Chatable;
use Spatie\Permission\Traits\HasRoles;
use Carbon\Carbon;

class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasRoles;
    use Chatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'username',
        'role',
        'privileges',
        'account_status',
        'contact_info',
	'birthday',
                'age',
                'id_card',
                'nationality',
                'gender',
                'personal_image',
        'impersonating_manager_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];
    protected $casts = [
        'contact_info' => 'array',
	'birthday' => 'date',
    ];
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function clients()
    {
        return $this->hasMany(Client::class, 'sales_rep_id');
    }
    public function salesRep()
    {
        return $this->hasOne(SalesRep::class, 'user_id');
    }

    public function impersonatingManager()
    {
        return $this->belongsTo(SalesRep::class, 'impersonating_manager_id');
    }

    public function isImpersonatingManager()
    {
        return !is_null($this->impersonating_manager_id);
    }

    public function startImpersonating(SalesRep $manager)
    {
        $this->update(['impersonating_manager_id' => $manager->id]);
    }

    public function stopImpersonating()
    {
        $this->update(['impersonating_manager_id' => null]);
    }

    public function getEffectiveSalesRep()
    {
        if ($this->isImpersonatingManager()) {
            return $this->impersonatingManager;
        }
        return $this->salesRep;
    }

    public function managerClientChatsAsSalesRep()
    {
        return $this->hasMany(ManagerClientChat::class, 'sales_rep_id');
    }

    public function managerClientChatsAsManager()
    {
        return $this->hasMany(ManagerClientChat::class, 'manager_id');
    }

    public function allManagerClientChats()
    {
        return ManagerClientChat::where('sales_rep_id', $this->id)
            ->orWhere('manager_id', $this->id)
            ->get();
    }

    public function receivesBroadcastNotificationsOn(): array
    {
        $channels = [
            'agreement.request.approved.' . $this->id,
            'agreement.request.rejected.' . $this->id,
            'client.request.approved.' . $this->id,
            'client.request.rejected.' . $this->id,
            'target.achieved.' . $this->id,
            'late.customer.' . $this->id,
            'agreement.notice.' . $this->id,

        ];

        // If the user is an admin, include the admin-specific channel
        if ($this->isAdmin()) {
            $channels[] = [
                'client.request.sent.' . $this->id,
                'agreement.request.sent.' . $this->id,
                'new-client.' . $this->id,
                'new-agreement.' . $this->id,
                'agreement-renewed.' . $this->id,
                'pended-request.notice.'. $this->id,
		'salesrep-login-ip.'. $this->id,
		'birthday'. $this->id,
            ];
        }

        return $channels;
    }
    public function getPersonalImageAttribute()
    {
        $path = $this->attributes['personal_image'] ?? null;

        if (!$path) {
            return asset('images/default-avatar.png');
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        try {
            if (Storage::disk('public')->exists($path)) {
                return Storage::url($path);
            }

            $pathsToCheck = [
                $path,
                'private/' . $path,
                'profile-photos/' . $path,
                'profile_photos/' . $path,
                'private/profile-photos/' . $path,
                'private/profile_photos/' . $path,
            ];

            foreach ($pathsToCheck as $checkPath) {
                if (Storage::disk('local')->exists($checkPath)) {
                    return route('file.serve', ['filename' => $checkPath]);
                }
            }

        } catch (\Exception $e) {
            \Log::warning('Error accessing storage for personal image: ' . $e->getMessage());
        }

        return asset('images/default-avatar.png');
    }

    public function temporaryPermissions()
    {
        return $this->hasMany(TemporaryPermission::class);
    }
    public function hasActiveEditPermission($model, $field)
    {
        return $this->temporaryPermissions()
            ->where('permissible_id', $model->id)
            ->where('permissible_type', get_class($model))
            ->where('field', $field)
            ->where('used', false)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->exists();
    }
    public function activeEditPermission($model, $field)
    {
        return $this->temporaryPermissions()
            ->where('permissible_id', $model->id)
            ->where('permissible_type', $model->getMorphClass())
            ->where('field', $field)
            ->where('used', false)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })->first();
    }
    public function canCreateChats(): bool
    {
        return true;
    }
    public function canCreateGroups(): bool
    {
        return true;
    }
      public function chats()
    {
        return $this->hasMany(Conversation::class, 'sender_id')->orWhere('receiver_id', $this->id)->whereNotDeleted();
    }

    public function getAge()
    {
        if (!$this->birthday) {
            return null;
        }

        return Carbon::parse($this->birthday)->age;
    }

	public function isAdmin()
{
	    return $this->role === 'admin';

}


}
