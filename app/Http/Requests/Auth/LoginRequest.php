<?php

namespace App\Http\Requests\Auth;
use Stevebauman\Location\Facades\Location;
use Illuminate\Support\Facades\Hash;
use App\Models\SalesRepLoginIp;
use Illuminate\Support\Facades\Request;
use App\Notifications\BlockedIpLoginAttempt;
use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;


class LoginRequest extends FormRequest
{


    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
public function authenticate()
{
    $this->ensureIsNotRateLimited();

    // Step 1: Get user by email
    $user = User::where('email', $this->email)->first();

    if (!$user) {
        RateLimiter::hit($this->throttleKey());
        throw ValidationException::withMessages([
            'email' => 'المستخدم غير موجود في النظام',
        ]);
    }

    if ($user->account_status !== 'active') {
        RateLimiter::hit($this->throttleKey());
        throw ValidationException::withMessages([
            'email' => 'حسابك غير مفعل تواصل مع المدير لتفعيله',
        ]);
    }

    if (!Hash::check($this->password, $user->password)) {
        RateLimiter::hit($this->throttleKey());
        throw ValidationException::withMessages([
            'email' => 'كلمة المرور غير صحيحة',
        ]);
    }

    // Get and properly convert IP address
    // $ip = Request::ip();
    // $currentIp = $this->convertToIPv4($ip);

    // $adminIp = '129.208.193.133';

    // if ($user->salesRep && $currentIp !== $adminIp) {
    //     $isValidIp = $this->validateSalesRepIp($user, $currentIp);

    //     if (!$isValidIp) {
    //         $this->handleInvalidIp($user, $currentIp);
    //         RateLimiter::hit($this->throttleKey());
    //         throw ValidationException::withMessages([
    //             'email' => 'هذا الجهاز غير مصرح له بتسجيل الدخول إلى النظام.',
    //         ]);
    //     }
    // }

    // Successful login
    Auth::login($user, $this->boolean('remember'));
    RateLimiter::clear($this->throttleKey());

    return redirect()->route('dashboard');
}

/**
 * Convert IPv6 to IPv4 if possible, otherwise return original IP
 */
protected function convertToIPv4($ip): string
{
    // If it's already IPv4, return as is
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        return $ip;
    }

    // Handle IPv6 mapped IPv4 addresses (::ffff:192.168.1.1)
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
        // Check if it's an IPv4-mapped IPv6 address
        if (strpos($ip, '::ffff:') === 0) {
            $ipv4 = substr($ip, 7);
            if (filter_var($ipv4, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                return $ipv4;
            }
        }
        
        // Check if it's an IPv4-compatible IPv6 address (deprecated but still possible)
        if (strpos($ip, '::') === 0) {
            $ipv4 = substr($ip, 2);
            if (filter_var($ipv4, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                return $ipv4;
            }
        }
    }

    // If conversion not possible, return the original IP
    return $ip;
}

protected function validateSalesRepIp($user, $currentIp): bool
{
    $locationData = Location::get($currentIp);
    // dd($locationData);
    // Check main IP
    $mainIp = SalesRepLoginIp::where('sales_rep_id', $user->salesRep->id)
        ->where('is_allowed', true)
        ->where('is_temporary', false)
        ->first();

    if (!$mainIp) {
        SalesRepLoginIp::create([
            'sales_rep_id' => $user->salesRep->id,
            'ip_address' => $currentIp,
            'is_allowed' => true,
            'is_temporary' => false,
            'location'     => $locationData->countryName . ' - ' . $locationData->cityName . ' - ' . $locationData->regionName,
        ]);
        return true;
    }

    if ($mainIp->ip_address === $currentIp) {
        return true;
    }

    // Check temporary IPs
    return SalesRepLoginIp::where('sales_rep_id', $user->salesRep->id)
        ->where('ip_address', $currentIp)
        ->where('is_allowed', true)
        ->where('is_temporary', true)
        ->where(function ($q) {
            $q->whereNull('allowed_until')
              ->orWhere('allowed_until', '>=', now());
        })
        ->exists();
}

protected function handleInvalidIp($user, $currentIp)
{

    $locationData = Location::get($currentIp);

//    dd($locationData);


// dd($locationData,$currentIp);

    // Save IP attempt to DB
    SalesRepLoginIp::firstOrCreate([
        'sales_rep_id' => $user->salesRep->id,
        'ip_address'   => $currentIp,
    ], [
        'is_allowed'   => false,
        'is_temporary' => false,
        'is_blocked'   => false,
        'location'     => $locationData->countryName . ' - ' . $locationData->cityName . ' - ' . $locationData->regionName,
    ]);
    User::where('role', 'admin')->get()->each(function ($admin) use ($user, $currentIp) {
        $admin->notify(new BlockedIpLoginAttempt($user->salesRep, $currentIp));
    });

    Auth::logout();
}
    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
