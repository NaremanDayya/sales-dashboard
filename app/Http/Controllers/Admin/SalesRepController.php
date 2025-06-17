<?php

namespace App\Http\Controllers\Admin;

use App\Exports\SalesRepsExport;
use App\Http\Controllers\Controller;
use App\Models\AgreementEditRequest;
use App\Models\ClientEditRequest;
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


class SalesRepController extends Controller
{

    public function index()
    {
        $salesReps = SalesRep::all()->map(function ($rep) {
            return [
                'id' => $rep->id,
                'name' => $rep->name,
                'start_work_date' => $rep->start_work_date->format('Y-m-d'),
                'work_duration' => $rep->translateDurationToArabic($rep->work_duration),
                'target_customers' => $rep->clients->count(),
                'late_customers' => $rep->lateCustomers,
                'total_orders' => $rep->totalOrders,
                'pending_orders' => $rep->totalPendedRequests,
                'interested_customers' => $rep->interestedClients->count(),
                'achieved_target_percentage' => (int) ($rep->currentMonthAchievedPercentage()),
                'achieved_target_amount' =>(int) ($rep->currentMonthAchievedAmount())
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
        $request->validate([
            'name' => 'required|string|max:255',
            'start_work_date' => 'required|date',
            'email' => 'required|email|unique:users,email',
            'interested_customers' => 'nullable|integer',
            'phone' => 'nullable|string|max:20',
            'permissions' => 'nullable|array', // checkboxes may not be selected
            'permissions.*' => 'exists:permissions,id',
        ]);

        $plainPassword = Str::random(8);

        // Create user
        $user = User::create([
            'name' => $request->name,
            'username' => strtolower(str_replace(' ', '.', $request->name)) . rand(1, 999),
            'email' => $request->email,
            'password' => Hash::make("Password"),
            'role' => 'salesRep',
            'email_verified_at' => now(),
            'contact_info' => json_encode([
                'phone' => $request->input('phone')
            ]),
            'account_status' => 'active',
        ]);

        // Sync permissions
        $this->syncPermissions($user, $request->permissions ?? []);

        // Work duration
        $startDate =Carbon::parse($request->start_work_date);
        $diff = $startDate->diff(Carbon::now());

        // Prepare SalesRep data
        $salesRepData = $request->only(['name', 'start_work_date', 'interested_customers']);
        $salesRepData['work_duration'] = "{$diff->y} years, {$diff->m} months, {$diff->d} days";
        $salesRepData['user_id'] = $user->id;

        // Default calculated values
        $salesRepData['target_customers'] = 0;
        $salesRepData['late_customers'] = 0;
        $salesRepData['total_orders'] = 0;
        $salesRepData['pending_orders'] = 0;

        $salesRep = SalesRep::create($salesRepData);

        // $credentials = [
        //     'name' => $request->name,
        //     'username' => $user->username,
        //     'email' => $request->email,
        //     'password' => $plainPassword,
        // ];

        // $csvPath = 'exports/sales_reps_credentials.csv';
        // if (!Storage::exists($csvPath)) {
        //     Storage::put($csvPath, "Name,Username,Email,Password\n");
        // }
        // Storage::append($csvPath, implode(',', $credentials) . "\n");

        return redirect()->route('sales-reps.index')
            ->with('success', "Sales Representative {$salesRep->name} created successfully.");
    }
    private function syncPermissions(User $user, array $permissions)
    {
        $permissionNames = Permission::whereIn('id', $permissions)->pluck('name')->toArray();
        $user->syncPermissions($permissionNames);
    }


    public function edit(SalesRep $salesRep)
    {
        $selectedPermissions = $salesRep->permissions->pluck('id')->toArray();
        $allPermissions = Permission::where('name', 'like', 'sales_rep_%')->get();
        return view('salesRep.edit', compact('salesRep', 'allPermissions', 'selectedPermissions'));
    }

    public function update(Request $request, SalesRep $salesRep)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_work_date' => 'required|date',
            'interested_customers' => 'nullable|integer',
        ]);

        // Update user info
        $salesRep->user->update([
            'name' => $request->name,
            'email' => $request->email,
            'contact_info' => json_encode([
                'phone' => $request->input('phone'),
            ]),
        ]);

        $startDate = Carbon::parse($request->start_work_date);
        $diff = $startDate->diff(Carbon::now());

        // Prepare updated sales rep data
        $salesRepData = $request->only(['name', 'start_work_date', 'interested_customers']);
        $salesRepData['work_duration'] = "{$diff->y} years, {$diff->m} months, {$diff->d} days";

        $salesRep->update($salesRepData);

        return redirect()->route('sales-reps.index')
            ->with('success', "Sales Representative {$salesRep->name} updated successfully.");
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

        return redirect()->route('salesrep.index')
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
    public function allPendingRequests()
    {
        $clientRequests = ClientEditRequest::with(['client', 'salesRep'])
            ->where('status', 'pending')
            ->latest()
            ->get();

        $agreementRequests = AgreementEditRequest::with(['agreement', 'client', 'salesRep'])
            ->where('status', 'pending')
            ->latest()
            ->get();

        return view('SalesRep.pendedRequests', compact('clientRequests', 'agreementRequests'));
    }
    public function myPendingRequests(SalesRep $salesRep)
    {

        $userId = Auth::id();
        // dd($userSalesRep);

        $clientRequests = ClientEditRequest::with(['client', 'salesRep'])
            ->where('status', 'pending')
            ->where('sales_rep_id', $userId)
            ->latest()
            ->get();

        $agreementRequests = AgreementEditRequest::with(['agreement', 'client', 'salesRep'])
            ->where('status', 'pending')
            ->where('sales_rep_id', $userId)
            ->latest()
            ->get();

        return view('SalesRep.pendedRequests', compact('clientRequests', 'agreementRequests', 'salesRep'));
    }
}
