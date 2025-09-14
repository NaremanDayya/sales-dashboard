<?php

namespace App\Http\Controllers\Admin;

use App\Exports\SalesRepsExport;
use App\Http\Controllers\Controller;
use App\Models\AgreementEditRequest;
use App\Models\ClientEditRequest;
use App\Models\ClientRequest;
use App\Models\SalesRep;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Permission;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Illuminate\Validation\Rules\Password;


class SalesRepController extends Controller
{

    public function index()
    {
        $salesReps = SalesRep::all()->map(function ($rep) {
            return [
                'id' => $rep->id,
                'name' => $rep->name,
                'start_work_date' => $rep->start_work_date->format('Y-m-d'),
                'work_duration' => $rep->work_duration,
                'target_customers' => $rep->clients->count(),
                'late_customers' => $rep->lateCustomers('interested')+$rep->lateCustomers('not interested')+$rep->lateCustomers('neutral'),
                'interested_late_customers' => $rep->lateCustomers('interested'),
                'not_interested_late_customers' => $rep->lateCustomers('not interested'),
                'neutral_late_customers' => $rep->lateCustomers('neutral'),
                'total_orders' => $rep->totalOrders,
                'pending_orders' => $rep->totalPendedRequests,
                'interested_customers' => $rep->interestedClients->count(),
                'active_agreements_count' =>(int) ($rep->active_agreements_count),
		'inactive_agreements_count' => (int) ($rep->inactive_agreements_count),
		'personal_image' => !empty($rep->user?->personal_image)
    ? asset('storage/' . $rep->user->personal_image)
    : 'https://ui-avatars.com/api/?name=' . urlencode($rep->user?->name ?? 'User') . '&background=random',

                'account_status' => $rep->user->account_status,

            ];
        });


        return view('salesRep.index', data: compact('salesReps'));
    }


    public function create()
    {
        $allPermissions = Permission::where('name', 'like', 'sales_rep_%')->get();
        return view('salesRep.create', [
            'salesRep' => new SalesRep(),
            'allPermissions' => $allPermissions
        ]);
    }


public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'start_work_date' => 'required|date',
        'email' => 'required|email|unique:users,email',
        'interested_customers' => 'nullable|integer',
        'phone' => 'nullable|string|digits:10',
        'permissions' => 'nullable|array',
        'permissions.*' => 'exists:permissions,id',
        'password' => 'required|string|min:8',
        'birthday' => 'required|date|before:today',
        'gender' => 'required|in:male,female',
        'id_card' => 'required|digits:10|regex:/^[12][0-9]{9}$/|unique:users,id_card',
        'personal_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        'remove_personal_image' => 'nullable|boolean',
    ]);

    $age = Carbon::parse($request->birthday)->age;

    // Check for existing user
    $existingUser = User::where('name', $request->name)
        ->where('nationality', $request->nationality)
        ->whereDate('birthday', $request->birthday)
        ->where('email', $request->email)
        ->whereJsonContains('contact_info->phone', $request->phone)
        ->first();

    if ($existingUser) {
        return back()->withErrors(['error' => 'المستخدم موجود بالفعل بنفس البيانات.'])->withInput();
    }

    // Create user
    $user = User::create([
        'name' => $request->name,
        'username' => strtolower(str_replace(' ', '.', $request->name)) . rand(1, 999),
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => 'salesRep',
        'email_verified_at' => now(),
        'contact_info' => json_encode(['phone' => $request->input('phone')]),
        'account_status' => $request->status,
        'age' => $age,
        'nationality' => $request->nationality,
        'gender' => $request->gender,
        'id_card' => $request->id_card,
        'birthday' => $request->birthday,
    ]);
	$csvPath = 'exports/sales_reps_credentials.csv';

$credentials = [
    'name' => $user->name,
    'email' => $user->email,
    'password' => $request->password, // كلمة المرور الأصلية
];

// إنشاء الملف إذا مش موجود مع العنوان
if (!Storage::exists($csvPath)) {
    Storage::put($csvPath, "Name,Email,Password\n");
}

// قراءة المحتوى الحالي
$existingContent = Storage::get($csvPath);
$lines = explode("\n", $existingContent);
$headers = array_shift($lines);

$updated = false;
$newContent = [$headers];

// تحديث أو إضافة السطر الجديد
foreach ($lines as $line) {
    if (empty(trim($line))) continue;

    $data = str_getcsv($line);

    if (count($data) >= 2 && $data[1] === $credentials['email']) {
        $newContent[] = implode(',', [
            $credentials['name'],
            $credentials['email'],
            $credentials['password'],
        ]);
        $updated = true;
    } else {
        $newContent[] = $line;
    }
}

if (!$updated) {
    $newContent[] = implode(',', [
        $credentials['name'],
        $credentials['email'],
        $credentials['password'],
    ]);
}

Storage::put($csvPath, implode("\n", $newContent));

    // Handle image upload
    if ($request->hasFile('personal_image')) {
        $path = $request->file('personal_image')->store('profile-photos', 'public');
        $user->personal_image = $path;
        $user->save();
    }

    // Sync permissions
    $this->syncPermissions($user, $request->permissions ?? []);

    // Work duration calculation
    $startDate = Carbon::parse($request->start_work_date);
    $diff = $startDate->diff(Carbon::now());

    // Create sales rep record
    $salesRep = SalesRep::create([
        'name' => $request->name,
        'start_work_date' => $request->start_work_date,
        'interested_customers' => 0,
        'work_duration' => "{$diff->y} years, {$diff->m} months, {$diff->d} days",
        'user_id' => $user->id,
        'target_customers' => 0,
        'late_customers' => 0,
        'total_orders' => 0,
        'pending_orders' => 0
    ]);

    return redirect()->route('sales-reps.index')
        ->with('success', "تم إضافة المندوب {$salesRep->name} بنجاح");
}
    private function syncPermissions(User $user, array $permissions)
    {
        $permissionNames = Permission::whereIn('id', $permissions)->pluck('name')->toArray();
        $user->syncPermissions($permissionNames);
    }

 public function bulkActivate(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:sales_representatives,id'
        ]);

        try {
            SalesRep::whereIn('id', $request->ids)
                ->with('user')
                ->get()
                ->each(function ($rep) {
                    $rep->user->update(['account_status' => 'active']);
                });

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function bulkDeactivate(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:sales_representatives,id'
        ]);

        try {
            SalesRep::whereIn('id', $request->ids)
                ->with('user')
                ->get()
                ->each(function ($rep) {
                    $rep->user->update(['account_status' => 'inactive']);
                });

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function edit(SalesRep $salesRep)
    {
        $selectedPermissions = $salesRep->permissions->pluck('id')->toArray();
        $allPermissions = Permission::where('name', 'like', 'sales_rep_%')->get();
        return view('salesRep.edit', compact('salesRep', 'allPermissions', 'selectedPermissions'));
    }

public function update(Request $request, SalesRep $salesRep)
{
    // Validate all fields
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,'.$salesRep->user->id,
        'password' => 'nullable|string|min:8',
        'birthday' => 'nullable|date',
        'id_card' => 'nullable|string|max:20',
        'nationality' => 'nullable|string|max:50',
        'gender' => 'nullable|in:male,female',
        'start_work_date' => 'required|date',
        'status' => 'required|in:active,inactive',
        'phone' => 'required|string|digits:10',
        'personal_image' => 'nullable|image|mimes:jpeg,png,jpg,webp',
        'remove_personal_image' => 'sometimes|boolean',
    ]);

    // Handle image operations first
  if ($request->boolean('remove_personal_image')) {
        if ($salesRep->user->personal_image && Storage::exists('public/' . $salesRep->user->personal_image)) {
            Storage::delete('public/' . $salesRep->user->personal_image);
        }
        $validated['personal_image'] = null;
    }
    if ($request->hasFile('personal_image')) {
        // Delete old image if exists
if ($salesRep->user->personal_image && Storage::exists('public/' . $salesRep->user->personal_image)) {
    Storage::delete('public/' . $salesRep->user->personal_image);
}

// Store new personal image
$image = $request->file('personal_image');
$imageName = time() . '_' . $image->getClientOriginalName();
$path = $request->personal_image->storeAs('profile-images', $imageName, 'public');
$validated['personal_image'] = $path;
    }

    // Prepare user data
    $userData = [
        'name' => $validated['name'],
        'email' => $validated['email'],
        'birthday' => $validated['birthday'],
        'id_card' => $validated['id_card'],
        'nationality' => $validated['nationality'],
        'gender' => $validated['gender'],
        'contact_info' => json_encode([
            'phone' => $validated['phone'],
        ]),
    ];

    // Only update personal_image if it was modified
    if (array_key_exists('personal_image', $validated)) {
        $userData['personal_image'] = $validated['personal_image'];
    }

    // Handle password update if provided
    if (!empty($validated['password'])) {
        $userData['password'] = Hash::make($validated['password']);
    }

    // Update user record
    $salesRep->user->update($userData);
$csvPath = 'exports/sales_reps_credentials.csv';

$credentials = [
    'name' => $userData['name'],
    'email' => $userData['email'],
    // إذا تم تحديث كلمة المرور، استعملها الأصلية (غير مشفرة) لو موجودة، أو خلي القيمة السابقة
    'password' => $validated['password'] ?? null,
];

// اقرأ الملف، أنشئه إذا مش موجود
if (!Storage::exists($csvPath)) {
    Storage::put($csvPath, "Name,Email,Password\n");
}

$existingContent = Storage::get($csvPath);
$lines = explode("\n", $existingContent);
$headers = array_shift($lines);

$updated = false;
$newContent = [$headers];

foreach ($lines as $line) {
    if (empty(trim($line))) continue;

    $data = str_getcsv($line);

    if (count($data) >= 2 && $data[1] === $credentials['email']) {
        // إذا فيه كلمة مرور جديدة، حدّثها، وإلا خليك على القديمة
        $password = $credentials['password'] ?? $data[2];

        $newContent[] = implode(',', [
            $credentials['name'],
            $credentials['email'],
            $password,
        ]);
        $updated = true;
    } else {
        $newContent[] = $line;
    }
}

if (!$updated) {
    // لو السطر مش موجود، أضفه
    $newContent[] = implode(',', [
        $credentials['name'],
        $credentials['email'],
        $credentials['password'] ?? '',
    ]);
}

Storage::put($csvPath, implode("\n", $newContent));
     //dd($salesRep->user);
    // Calculate work duration
    $startDate = Carbon::parse($validated['start_work_date']);
    $diff = $startDate->diff(Carbon::now());
    $workDuration = "{$diff->y} سنوات, {$diff->m} أشهر, {$diff->d} أيام";

    // Update sales rep data
    $salesRep->update([
        'start_work_date' => $validated['start_work_date'],
        'status' => $validated['status'],
        'work_duration' => $workDuration,
    ]);

    return redirect()->route('sales-reps.index')
        ->with('success', "تم تحديث بيانات المندوب {$salesRep->name} بنجاح");
}
 public function updatePhoto(Request $request, SalesRep $salesRep)
    {
        $request->validate([
            'profile_photo_url' => 'required|image|max:2048', // 2MB max
        ]);

        $user = $salesRep->user;

        // Delete old photo if exists
        if ($user->profile_photo_url && Storage::exists('public/' . $user->profile_photo_url)) {
            Storage::delete('public/' . $user->profile_photo_url);
        }

        $path = $request->file('profile_photo_url')->store('profile-photos', 'public');

        $user->profile_photo_url = $path;
        $user->save();

        return redirect()->back()->with('success', 'تم تحديث الصورة الشخصية بنجاح.');
    }
    public function show(SalesRep $salesRep)
    {
        $user = $salesRep->user;

        // Eager load permissions relation on the user model
        $user->load('permissions');

        // Map permissions to translated names (assuming 'name' field)
        $translatedPermissions = $user->permissions->map(function ($permission) {
            return __($permission->name);
        });

        return view('profile.show', compact('salesRep', 'user', 'translatedPermissions'));
    }


    public function destroy(SalesRep $salesRep)
    {
        $salesRep->delete();

        return redirect()->route('sales-reps.index')
            ->with('success', 'Sales Representative deleted successfully.');
    }



    public function export(Request $request)
    {
        $allColumnLabels = [
            'name' => 'اسم المندوب',
            'start_work_date' => 'تاريخ بدء العمل',
            'work_duration' => 'مدة العمل',
            'target_customers' => 'العملاء المستهدفون',
            'late_customers' => 'العملاء المتأخرون',
            'total_agreements' => 'إجمالي العقود',
            'pending_requests' => 'الطلبات المعلقة',
            'interested_customers' => 'العملاء المهتمون',
            'current_target_status' => 'حالة الهدف الحالية',
            'total_requests' => 'إجمالي الطلبات',
            'email' => 'البريد الإلكتروني',
        ];

        $validated = $request->validate([
            'columns' => 'required|array',
            'columns.*' => 'string|in:' . implode(',', array_keys($allColumnLabels)),
        ]);

        $selectedColumns = $request->input('columns', []);
        $selectedLabels = array_intersect_key($allColumnLabels, array_flip($selectedColumns));

        // Eager load all required relationships
        $salesReps = \App\Models\SalesRep::with([
            'user',
            'clients',
            'agreements',
            'clientRequest' // Make sure this matches your relationship name
        ])->get();

        $fileName = 'sales_reps_export_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $callback = function () use ($salesReps, $selectedColumns, $selectedLabels) {
            $file = fopen('php://output', 'w');

            // Add UTF-8 BOM for Arabic support
            fwrite($file, "\xEF\xBB\xBF");

            // Write Arabic headers
            fputcsv($file, array_values($selectedLabels));

            // Write data rows
            foreach ($salesReps as $salesRep) {
                $row = [];

                foreach ($selectedColumns as $col) {
                    switch ($col) {
                        case 'name':
                            $row[] = $salesRep->name;
                            break;

                        case 'start_work_date':
                            $row[] = optional($salesRep->start_work_date)->format('Y-m-d');
                            break;

                        case 'work_duration':
                            $row[] = $salesRep->work_duration;
                            break;

                        case 'email':
                            $row[] = $salesRep->user->email ?? '';
                            break;

                        case 'total_agreements':
                            $row[] = $salesRep->agreements->count();
                            break;

                        case 'pending_requests':
                            $row[] = $salesRep->clientRequest->where('status', 'pending')->count();
                            break;

                        case 'target_customers':
                        case 'interested_customers':
                            $row[] = $salesRep->clients->where('interest_status', 'interested')->count();
                            break;

                        case 'late_customers':
                            $row[] = $salesRep->late_customers;
                            break;

                        case 'total_requests':
                            $row[] = $salesRep->clientRequest->count();
                            break;

                        case 'current_target_status':
                            // Add your logic for target status
                            $row[] = 'حالة الهدف'; // Replace with actual value
                            break;

                        default:
                            $row[] = $salesRep->{$col} ?? '';
                    }
                }

                fputcsv($file, $row);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
    public function allRequests()
    {
        $clientRequests = ClientEditRequest::with(['client', 'salesRep'])
            ->orderBy('created_at', 'desc')
            ->get();

        $agreementRequests = AgreementEditRequest::with(['agreement', 'client', 'salesRep'])
            ->orderBy('created_at', 'desc')
            ->get();

        $chatClientRequests = ClientRequest::with(['client', 'salesRep'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Combine all requests into one collection
        $allRequests = $clientRequests->concat($agreementRequests)->concat($chatClientRequests)
            ->sortByDesc('created_at');

        return view('salesRep.pendedRequests', compact('clientRequests', 'agreementRequests', 'chatClientRequests', 'allRequests'));
    }
    public function myRequests(SalesRep $salesRep)
    {

        $userId = $salesRep->user->id;
        // dd($userSalesRep);

        $clientRequests = ClientEditRequest::with(['client', 'salesRep'])
            ->where('sales_rep_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        $agreementRequests = AgreementEditRequest::with(['agreement', 'client', 'salesRep'])
            ->where('sales_rep_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        $chatClientRequests = ClientRequest::with(['client', 'salesRep'])
            ->where('sales_rep_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('salesRep.pendedRequests', compact('clientRequests','chatClientRequests', 'agreementRequests', 'salesRep'));
    }
 public function updatePassword(Request $request, SalesRep $salesrep)
{
    try {
        $validated = $request->validate([
            'salesrepPassword' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
            ],
        ]);
        $user = $salesrep->user;

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم العثور على مستخدم مرتبط بهذا المندوب.'
            ], 404);
        }

        // Update the password
        $user->update([
            'password' => Hash::make($validated['salesrepPassword']),
        ]);

        // Handle CSV file update
        $csvPath = 'exports/sales_reps_credentials.csv';
        $credentials = [
            'name' => $salesrep->name,
            'email' => $user->email,
            'password' => $validated['salesrepPassword'],
            'updated_at' => now()->toDateTimeString()
        ];

        // Check if file exists, create if not with headers
        if (!Storage::exists($csvPath)) {
            Storage::put($csvPath, "Name,Email,Password,Updated At\n");
        }

        // Read existing content
        $existingContent = Storage::get($csvPath);
        $lines = explode("\n", $existingContent);
        $headers = array_shift($lines); // Remove header row

        // Find and update existing entry or append new one
        $updated = false;
        $newContent = [$headers];

        foreach ($lines as $line) {
            if (empty(trim($line))) continue;

            $data = str_getcsv($line);
            if (count($data) >= 2 && $data[1] === $salesrep->email) {
                // Update existing entry
                $newContent[] = implode(',', [
                    $credentials['name'],
                    $credentials['email'],
                    $credentials['password'],
                ]);
                $updated = true;
            } else {
                // Keep other entries unchanged
                $newContent[] = $line;
            }
        }

        // If no existing entry was found, append new one
        if (!$updated) {
            $newContent[] = implode(',', [
                $credentials['name'],
                $credentials['email'],
                $credentials['password'],
            ]);
        }

        // Save updated content
        Storage::put($csvPath, implode("\n", $newContent));

        return response()->json([
            'success' => true,
            'message' => 'تم تغيير كلمة مرور المندوب بنجاح وتحديث سجل البيانات.'
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'errors' => $e->errors(),
            'message' => 'التحقق من صحة البيانات فشل'
        ], 422);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ أثناء تحديث كلمة المرور: ' . $e->getMessage()
        ], 500);
    }
}

    public function impersonate(User $salesRep)
    {
//        dd($salesRep->id);
        $admin = Auth::user();

        if ($admin->role !== 'admin') {
            abort(403, 'Access denied');
        }

        session([
            'impersonator_id' => $admin->id,
            'sales_Rep_name' => $salesRep->name,
            ]);


        Auth::login($salesRep);

        return redirect('/dashboard');
    }
    public function stopImpersonate()
    {
//        dd(session('impersonator_id'));
        if (session()->has('impersonator_id')) {
            $admin = User::find(session('impersonator_id'));
            Auth::login($admin);
            session()->forget('impersonator_id');
        }

        return redirect('/dashboard');
    }
}
