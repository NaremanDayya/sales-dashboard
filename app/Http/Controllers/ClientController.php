<?php

namespace App\Http\Controllers;

use App\Exports\ClientsExport;
use App\Models\Setting;
use Illuminate\Support\Str;
use App\Models\Client;
use App\Models\Conversation;
use App\Models\ClientRequest;
use App\Models\Message;
use App\Models\RequestType;
use App\Models\SalesRep;
use App\Models\User;
use App\Notifications\LateCustomerNotification;
use App\Notifications\NewClientNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use App\Models\Service;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller
{
//    test
    public function allClients(Request $request)
    {
        $query = Client::query();

        // Apply search filter
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('company_name', 'LIKE', "%{$search}%")
                    ->orWhere('contact_person', 'LIKE', "%{$search}%")
                    ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        // Apply interest status filter
        if ($request->has('interest_status') && !empty($request->interest_status)) {
            $status = $request->interest_status;
            $lateDays = $request->get('late_days', 7); // Default to 7 days

            if (in_array($status, ['interested', 'not interested', 'neutral'])) {
                $query->where('interest_status', $status);
            } else {
                $lateThreshold = now()->subDays($lateDays);

                $lateCondition = function($q) use ($lateThreshold) {
                    $q->whereNull('last_contact_date')
                        ->orWhere('last_contact_date', '<', $lateThreshold);
                };

                $interestMap = [
                    'late_interested' => 'interested',
                    'late_not_interested' => 'not interested',
                    'late_neutral' => 'neutral'
                ];

                if ($status === 'late') {
                    $query->where($lateCondition);
                } elseif (array_key_exists($status, $interestMap)) {
                    $query->where('interest_status', $interestMap[$status])
                        ->where($lateCondition);
                }
            }
        }

        // Apply service type filter
        if ($request->has('service') && !empty($request->service)) {
            $query->where('interested_service', $request->service);
        }

        // Apply date range filter
        if ($request->has('from_date') && !empty($request->from_date)) {
            $query->whereDate('last_contact_date', '>=', $request->from_date);
        }

        if ($request->has('to_date') && !empty($request->to_date)) {
            $query->whereDate('last_contact_date', '<=', $request->to_date);
        }
        if ($request->has('created_from_date') && !empty($request->created_from_date)) {
            $query->whereDate('created_at', '>=', $request->created_from_date);
        }

        if ($request->has('created_to_date') && !empty($request->created_to_date)) {
            $query->whereDate('created_at', '<=', $request->created_to_date);
        }
        if ($request->has('sales_rep') && !empty($request->sales_rep)) {
            $query->whereHas('salesRep', function($q) use ($request) {
                $q->where('name', $request->sales_rep); // Changed $query to $q
            });
        }

        // Apply late customer filter
        if ($request->has('late_customer') && !empty($request->late_customer)) {
            $lateDays = Setting::where('key', 'late_customer_days')->value('value') ?? 3;

            if ($request->late_customer === 'late') {
                $query->where(function($q) use ($lateDays) {
                    $q->whereNull('last_contact_date')
                        ->orWhereRaw("DATEDIFF(CURDATE(), last_contact_date) > ?", [$lateDays]);
                });
            } else {
                $query->whereNotNull('last_contact_date')
                    ->whereRaw("DATEDIFF(CURDATE(), last_contact_date) <= ?", [$lateDays]);
            }
        }

        $Clients = $query->orderByRaw('last_contact_date IS NULL, last_contact_date ASC')
            ->get()
            ->map(function ($client) {
                return [
                    'client_id' => $client->id,
                    'company_logo' => $client->company_logo,
                    'company_name' => $client->company_name,
                    'address' => $client->address,
                    'contact_person' => $client->contact_person,
                    'client_created_at' => $client->created_at,
                    'contact_position' => $client->contact_position,
                    'phone' => $client->phone,
                    'whatsapp_link' => $client->whatsapp_link,
                    'interest_status' => $client->interest_status,
                    'last_contact_date' => $client->last_contact_date?->format('Y-m-d'),
                    'is_late_customer' => $client->isLateCustomer(),
                    'contact_count' => $client->contact_count,
                    'requests_count' => $client->allEditRequests()->count(),
                    'sales_rep_id' => $client->sales_rep_id,
                    'contact_days_left' => $client->late_days,
                    'interested_service' => Service::where('id', $client->interested_service)->value('name'),
                    'agreements_count' => $client->agreements()->count(),
                    'interested_service_count' => $client->interested_service_count,
                    'sales_rep_name' => $client->salesRep->name,
                ];
            });

        $services = Service::all();
        $isAdmin = auth()->user()->role === 'admin';
        $sales_rep_names = SalesRep::pluck('name');
        $userRole = Auth::user()->role;

        return view('clients.table', compact('Clients', 'services', 'isAdmin', 'sales_rep_names', 'userRole'));
    }

    public function index(Request $request, SalesRep $salesRep)
    {
        $query = Client::where('sales_rep_id', $salesRep->id);

        // Apply the same filters as above
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('company_name', 'LIKE', "%{$search}%")
                    ->orWhere('contact_person', 'LIKE', "%{$search}%")
                    ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }
        if ($request->has('created_from_date') && !empty($request->created_from_date)) {
            $query->whereDate('created_at', '>=', $request->created_from_date);
        }

        if ($request->has('created_to_date') && !empty($request->created_to_date)) {
            $query->whereDate('created_at', '<=', $request->created_to_date);
        }
        if ($request->has('interest_status') && !empty($request->interest_status)) {
            $status = $request->interest_status;
            $lateDays = $request->get('late_days', 7); // Default to 7 days

            if (in_array($status, ['interested', 'not interested', 'neutral'])) {
                $query->where('interest_status', $status);
            } else {
                $lateThreshold = now()->subDays($lateDays);

                $lateCondition = function($q) use ($lateThreshold) {
                    $q->whereNull('last_contact_date')
                        ->orWhere('last_contact_date', '<', $lateThreshold);
                };

                $interestMap = [
                    'late_interested' => 'interested',
                    'late_not_interested' => 'not interested',
                    'late_neutral' => 'neutral'
                ];

                if ($status === 'late') {
                    $query->where($lateCondition);
                } elseif (array_key_exists($status, $interestMap)) {
                    $query->where('interest_status', $interestMap[$status])
                        ->where($lateCondition);
                }
            }
        }

        if ($request->has('service') && !empty($request->service)) {
            $query->where('interested_service', $request->service);
        }
        if ($request->has('sales_rep') && !empty($request->sales_rep)) {
            $query->whereHas('salesRep', function($q) use ($request) {
                $q->where('name', $request->sales_rep);
            });
        }

        if ($request->has('from_date') && !empty($request->from_date)) {
            $query->whereDate('last_contact_date', '>=', $request->from_date);
        }

        if ($request->has('to_date') && !empty($request->to_date)) {
            $query->whereDate('last_contact_date', '<=', $request->to_date);
        }

        if ($request->has('late_customer') && !empty($request->late_customer)) {
            $lateDays = Setting::where('key', 'late_customer_days')->value('value') ?? 3;

            if ($request->late_customer === 'late') {
                $query->where(function($q) use ($lateDays) {
                    $q->whereNull('last_contact_date')
                        ->orWhereRaw("DATEDIFF(CURDATE(), last_contact_date) > ?", [$lateDays]);
                });
            } else {
                $query->whereNotNull('last_contact_date')
                    ->whereRaw("DATEDIFF(CURDATE(), last_contact_date) <= ?", [$lateDays]);
            }
        }

        $Clients = $query->orderByRaw('last_contact_date IS NULL, last_contact_date ASC')
            ->get()
            ->map(function ($client) {
                return [
                    'client_id' => $client->id,
                    'company_logo' => $client->company_logo,
                    'company_name' => $client->company_name,
                    'client_created_at' => $client->created_at,
                    'address' => $client->address,
                    'contact_person' => $client->contact_person,
                    'contact_position' => $client->contact_position,
                    'phone' => $client->phone,
                    'whatsapp_link' => $client->whatsapp_link,
                    'interest_status' => $client->interest_status,
                    'last_contact_date' => $client->last_contact_date?->format('Y-m-d'),
                    'is_late_customer' => $client->isLateCustomer(),
                    'contact_count' => $client->contact_count,
                    'requests_count' => $client->allEditRequests()->count(),
                    'sales_rep_id' => $client->sales_rep_id,
                    'interested_service' => Service::where('id', $client->interested_service)->value('name'),
                    'contact_days_left' => $client->late_days,
                    'agreements_count' => $client->agreements()->count(),
                    'interested_service_count' => $client->interested_service_count,
                    'sales_rep_name' => $client->salesRep->name,
                ];
            });

        $isAdmin = auth()->user()->role === 'admin';
        $services = Service::all();
        $sales_rep_names = SalesRep::pluck('name');
        $userRole = Auth::user()->role;

        return view('clients.table', compact('Clients', 'services', 'isAdmin', 'sales_rep_names', 'userRole'));
    }

    // Show form to create a new client
    public function create(SalesRep $salesRep)
    {
    $services = Service::all();
        return view('clients.create',compact('services'));
    }

    public function storeRequest(Request $request, Client $client)
    {
        $validated = $request->validate([

            'request_message' => 'required|string',
        ]);
        $authenticatedUserId = Auth::id();
  // Store the request in the new table
    ClientRequest::create([
        'client_id' => $client->id,
        'sales_rep_id' => $authenticatedUserId,
        'message' => $validated['request_message'],
        'status' => 'pending',
    ]);
        // dd($authenticatedUserId);
        $conversation = $client->conversations()
            ->where(function ($query) use ($authenticatedUserId) {
                $query->where('sender_id', $authenticatedUserId)
                    ->orWhere('receiver_id', $authenticatedUserId);
            })->first();

        if (!$conversation) {
            $adminUserId = User::where('role', 'admin')->first()->id;
            $conversation = Conversation::create([
                'sender_id' => $authenticatedUserId,
                'receiver_id' => $adminUserId,
                'client_id' => $client->id,
            ]);
        }
        $message = "طلب خاص بالعميل:\n"
            . "🏢 الشركة: {$client->company_name}\n"
            . "📝 الطلب: {$request->request_message}";
        Message::create(
            [
                'conversation_id' => $conversation->id,
                'sender_id' => $authenticatedUserId,
                'receiver_id' => $conversation->sender_id === $authenticatedUserId ? $conversation->receiver_id : $conversation->sender_id,
                'message' => $message,
            ]
        );
        return back()->with([
            'success',
            "تم إرسال طلب العميل $client->company_name بنجاح",
            'refreshChatList',
            true
        ]);
    }
    public function store(Request $request)
    {
        try {
            // Handle temporary upload for preview
            if ($request->hasFile('company_logo')) {
                $tempPath = $request->file('company_logo')->store('temp', 'public');
                session()->put('temp_company_logo', $tempPath);
                $request->merge(['company_logo_temp' => $tempPath]);
            }

            $hasTempLogo = $request->filled('company_logo_temp');

            // Validation
            $validated = $request->validate([
                'company_name' => 'required|string|max:255',
                'company_logo' => [$hasTempLogo ? 'nullable' : 'required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
                'address' => 'required|string',
                'contact_person' => 'required|string|max:255',
                'contact_position' => 'nullable|string|max:255',
                'country_code' => 'required|digits_between:1,5',
                'phone' => 'required|digits_between:6,12',
                'interest_status' => 'required|in:interested,not interested,neutral',
                'last_contact_date' => 'required|date|before_or_equal:today',
                'interested_service' => 'required|exists:services,id',
                'interested_service_count' => 'required|integer|min:0',
                'contact_details' => 'required|string',
            ], [
                'phone.digits' => 'يجب أن يتكون رقم الجوال من 10 أرقام',
                'company_logo.required' => 'يجب اختيار شعار للشركة',
                'last_contact_date.required' => 'يرجى إدخال تاريخ آخر تواصل.',
                'last_contact_date.before_or_equal' => 'لا يمكن اختيار تاريخ في المستقبل.',
            ]);

        if ($request->hasFile('company_logo')) {
    $file = $request->file('company_logo');

    // Store directly into "public/company_logos"
    $path = $file->store('company_logos', 'public');

    $validated['company_logo'] = $path;

} elseif ($hasTempLogo) {
    // Move the temp public file into final "company_logos" directory
    $tempPath = $request->input('company_logo_temp');
    $filename = basename($tempPath);
    $newPath = 'company_logos/' . $filename;

    if (Storage::disk('public')->exists($tempPath)) {
        // Move the file (no need to re-read contents like S3)
        Storage::disk('public')->move($tempPath, $newPath);

        $validated['company_logo'] = $newPath;
    }

    // Clean session
    session()->forget('temp_company_logo');
}

            // Check for duplicate client info
            $exists = Client::where('company_name', $validated['company_name'])
                ->where('contact_person', $validated['contact_person'])
                ->where('contact_position', $validated['contact_position'])
                ->where('phone', $this->generateWhatsappNumber($request->country_code, $request->phone))
                ->where('interested_service', $validated['interested_service'])
                ->exists();

            if ($exists) {
                return back()->withInput()->withErrors(['duplicate' => 'هذا العميل موجود مسبقًا بنفس البيانات.']);
            }

            $serviceConflict = Client::where('company_name', $validated['company_name'])
                ->where('contact_person', $validated['contact_person'])
                ->where('contact_position', $validated['contact_position'])
                ->where('phone', $this->generateWhatsappNumber($request->country_code, $request->phone))
                ->where('interested_service', '!=', $validated['interested_service'])
                ->exists();

            if ($serviceConflict) {
                return back()->withInput()->withErrors(['duplicate' => 'هذا العميل مهتم بخدمة مسبقا.']);
            }

            // Create client
            $validated['sales_rep_id'] = Auth::user()->salesRep->id;
            $validated['phone'] = $this->generateWhatsappNumber($request->country_code, $request->phone);
            $validated['whatsapp_link'] = $this->generateWhatsappLink($request->country_code, $request->phone);
            $validated['contact_count'] = 1;
            $client = Client::create($validated);

            // Notify admin
            $admin = User::where('role', 'admin')->first();
            if ($admin) {
                $admin->notify(new NewClientNotification($client));
            }

            $authenticatedUserId = Auth::id();

            // Find or create conversation
            $conversation = $client->conversations()
                ->where(function ($query) use ($authenticatedUserId) {
                    $query->where('sender_id', $authenticatedUserId)
                        ->orWhere('receiver_id', $authenticatedUserId);
                })->first();

            if (!$conversation) {
                $adminUserId = User::where('role', 'admin')->first()->id;
                $conversation = Conversation::create([
                    'sender_id' => $authenticatedUserId,
                    'receiver_id' => $adminUserId,
                    'client_id' => $client->id,
                ]);
            }

            // Send system message
            $message = "📌 تم إضافة عميل جديد إلى النظام:\n"
                . "🏢 الشركة: {$client->company_name}\n"
                . "✉️ التفاصيل: {$request->contact_details}";

            Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $authenticatedUserId,
                'receiver_id' => $conversation->sender_id === $authenticatedUserId ? $conversation->receiver_id : $conversation->sender_id,
                'message' => $message,
            ]);

            return redirect()
                ->route('sales-reps.clients.index', $client->sales_rep_id)
                ->with('success', 'تمت إضافة العميل بنجاح.');

        } catch (ValidationException $e) {
            return redirect()->back()->withInput()->withErrors($e->validator);
        }
    }

    private function generateWhatsappNumber($countryCode, $phone)
    {
        $digits = preg_replace('/\D/', '', $phone);
        $countryCode = preg_replace('/\D/', '', $countryCode);

        $digits = ltrim($digits, '0');

        $fullNumber = $countryCode . $digits;

        return '+' . $fullNumber;
    }
    private function generateWhatsappLink($countryCode, $phone)
    {
        $digits = preg_replace('/\D/', '', $phone);
        $countryCode = preg_replace('/\D/', '', $countryCode);

        $digits = ltrim($digits, '0');


        $fullNumber = $countryCode . $digits;

        return "https://wa.me/" . $fullNumber;
    }


    // Show a single client
    public function show(SalesRep $salesRep, Client $client)
    {
        //$this->authorize('view', $client);
        $requestTypes = RequestType::all();
        $columns = [
            'company_name' => 'اسم الشركة',
            'logo' => 'الشعار',
            'address' => 'العنوان',
            'contact_person' => ' الشخص المسؤول',
            'contact_position' => ' منصب الشخص المسؤول',
            'interest_status' => 'حالة الاهتمام',
            'phone' => 'رقم الجوال',
            'interested_service' => 'الخدمة المهتم بها',
            'interested_service_count' => 'عدد الخدمة المهتم بها',
        ];
        $approvedEditRequest = $client->clientEditRequests()
            ->where('sales_rep_id', $salesRep->user->id)
            ->where('status', 'approved')
            ->where('request_type', 'client_data_change')
            ->latest()
            ->first();
        $services = Service::all();
        $editableField = $approvedEditRequest ? $approvedEditRequest->edited_field : null;
        // dd($editableField);
        return view('clients.show', compact('client', 'salesRep', 'requestTypes', 'columns', 'editableField','services'));
    }

    // Show form to edit client
    public function edit(SalesRep $salesRep, Client $client)
    {
        //$this->authorize('update', $client);
        return view('clients.edit', compact('client', 'salesRep'));
    }

    // Update client
    public function update(Request $request, SalesRep $salesRep, Client $client)
    {
        //$this->authorize('update', $client);
        // Get the approved request
        $approvedEditRequest = $client->clientEditRequests()
            ->where('sales_rep_id', Auth::id())
            ->where('status', 'approved')
            ->where('request_type', 'client_data_change')
            ->latest()
            ->first();

        // If no approved request, block update
        if (!$approvedEditRequest) {
            return back()->with('error', 'لا يوجد طلب تعديل معتمد.');
        }

        $editableField = $approvedEditRequest->edited_field;

        // Build validation rule only for the editable field
        $rules = [];

        switch ($editableField) {
            case 'company_name':
            case 'contact_person':
                $rules[$editableField] = 'required|string|max:255';
                break;
            case 'logo':
                $rules[$editableField] = 'nullable|image|mimes:jpg,jpeg,png';
                break;
            case 'phone':
                $rules['phone'] = 'required|string|max:20';
                $rules['country_code'] = 'required|digits_between:1,5';
                break;
	    case 'interest_status':
                $rules[$editableField] = 'nullable|in:interested,not interested,neutral';
                break;
            case 'contact_position':
            case 'address':
                $rules[$editableField] = 'nullable|string';
                break;
            case 'interested_service':
                $rules[$editableField] = 'nullable|exists:services,id';
                break;
            case 'interested_service_count':
                $rules[$editableField] = 'nullable|int';
                break;
            default:
                return back()->with('error', 'لا يمكن تعديل هذا الحقل.');
        }

        // Validate only that field
        $validated = $request->validate($rules);

        // Special handling for logo
        if ($editableField === 'logo' && $request->hasFile('company_logo')) {
            $validated['company_logo'] = $request->file('company_logo')->store('clients/logos', 'public');
        }

        // Special case for phone: generate whatsapp link
        if ($editableField === 'phone') {

            $validated['phone'] = $this->generateWhatsappNumber($request->country_code,$request->phone);
            $validated['whatsapp_link'] = $this->generateWhatsappLink($request->country_code,$request->phone);

            }

        $client->update($validated);
        $newValue = $client->$editableField;
        $approvedEditRequest->update([
            'payload'       => [
                'new_value' => $newValue,
            ],
    ]);
        $permission = $client->salesRep->myLastPermission($client, $editableField);
        $permission->update(['used' => true]);
        return redirect()->route('sales-reps.clients.index', $client->sales_rep_id)->with('success', 'تم تحديث الحقل بنجاح.');
    }


    // Delete client
    public function destroy(Client $client)
    {
        $client->delete();

        return redirect()->route('sales-reps.clients.index', Auth::user()->salesRep->id)->with('success', 'Client deleted.');
    }


    public function startChat($clientId, $conversationId)
    {
        // Validate and load client and conversation
        $client = Client::findOrFail($clientId);
        $conversation = Conversation::findOrFail($conversationId, $clientId);

        // Return view or Livewire component with both
        return view('chat.show', compact('client', 'conversation'));
    }
    public function updateLastContact(Request $request, Client $client)
    {
        $request->validate([
            'last_contact_date' => 'required|date',
            'update_message' => 'required|string',
        ]);

        // Update client last contact date
        $client->update([
            'last_contact_date' => $request->last_contact_date,
            'contact_count' => $client->contact_count + 1
        ]);

        // Mark permission as used
        $permission = $client->salesRep->myLastPermission($client, 'last_contact_date');
        if ($permission) {
            $permission->update(['used' => true]);
        }

        // Notify if late customer
        if ($client->isLateCustomer()) {
            $client->salesRep->user->notify(new LateCustomerNotification($client));
        }

        $authenticatedUserId = Auth::id();

        // Find or create conversation between sales rep (sender) and admin (receiver)
        $conversation = $client->conversations()
            ->where(function ($query) use ($authenticatedUserId) {
                $query->where('sender_id', $authenticatedUserId)
                    ->orWhere('receiver_id', $authenticatedUserId);
            })->first();

        if (!$conversation) {
            // Get admin user ID (adjust this query based on your app logic)
            $adminUserId = User::where('role', 'admin')->first()->id;

            $conversation = Conversation::create([
                'sender_id' => $authenticatedUserId,
                'receiver_id' => $adminUserId,
                'client_id' => $client->id,
            ]);
        }
  $date = \Carbon\Carbon::parse($request->last_contact_date)->format('Y-m-d');
    $reason = $request->update_message;
        $message = "📞 تحديث بيانات التواصل مع العميل:\n"
            . "🏢 الشركة: {$client->company_name}\n"
            . "📅 آخر تواصل: {$date}\n"
            . "📝 السبب: {$reason}";

        Message::create(
            [
                'conversation_id' => $conversation->id,
                'sender_id' => $authenticatedUserId,
                'receiver_id' => $conversation->sender_id === $authenticatedUserId ? $conversation->receiver_id : $conversation->sender_id,
                'message' => $message,
            ]
        );


        return back()->with('success', 'تم تحديث اخر تاريخ تواصل مع العميل {$client->company_name} بنجاح.');
    }
    public function chatClients()
    {
        return Client::with('salesRep:id,name') // eager load with only needed fields
            ->select('id', 'company_name', 'sales_rep_id') // make sure sales_rep_id is selected
            ->get()
            ->map(function ($client) {
                return [
                    'id' => $client->id,
                    'company_name' => $client->company_name,
                    'sales_rep_name' => optional($client->salesRep)->name ?? 'غير محدد',
                ];
            });

    }

 public function sharedCompanies()
    {
        // Get only company names that are shared across multiple sales reps
        $sharedCompanyNames = Client::select('company_name')
            ->groupBy('company_name')
            ->havingRaw('COUNT(DISTINCT sales_rep_id) > 1')
            ->pluck('company_name');

        $sharedCompanies = $sharedCompanyNames->map(function ($companyName) {
            $clients = Client::with('salesRep.user') // eager load
                ->where('company_name', $companyName)
                ->get();

            $repGroups = $clients->map(function ($client) {
                return [
                    'sales_rep_name' => $client->salesRep?->user?->name ?? 'غير معروف',
                    'interest_status' => $client->interest_status,
                    'client_id' => $client->id,
                    'company_logo' => $client->company_logo,
                    'sales_rep_id' => $client->sales_rep_id,
                    'last_contact_date' => optional($client->last_contact_date)->format('Y-m-d') ?? '—',

                ];
            });

            return [
                'company_name' => $companyName,
                'clients' => $repGroups,
            ];
        });

        return view('clients.shared-companies', compact('sharedCompanies'));
    }
    public function suggestCompanyNames(Request $request)
    {
        $query = $request->input('term');

        $suggestions = Client::select('company_name')
            ->where('company_name', 'LIKE', "%{\$query}%")
            ->groupBy('company_name')
            ->limit(10)
            ->pluck('company_name');

        return response()->json($suggestions);
    }


    public function inlineUpdate(Request $request, Client $client)
    {
        $validated = $request->validate([
            'company_name'      => 'sometimes|required|string|max:255',
            'address'           => 'sometimes|nullable|string|max:500',
            'contact_person'    => 'sometimes|nullable|string|max:255',
            'contact_position'  => 'sometimes|nullable|string|max:255',
            'phone'             => 'sometimes|nullable|string|max:20',
            'interest_status'   => 'sometimes|nullable|string|in:interested,not interested,neutral',
            'country_code'      => 'sometimes|nullable|string|max:10',
            'company_logo'      => 'sometimes|nullable|url|max:500',
            'whatsapp_link'     => 'sometimes|nullable|url|max:500',
            'interested_service' => 'sometimes|nullable|string|max:255',
            'contact_count'     => 'sometimes|nullable|integer|min:0',
            'interested_service_count'    => 'sometimes|nullable|integer|min:0',
            'last_contact_date' => 'sometimes|nullable|date',
        ]);

        try {
            $phoneChanged = $request->has('phone') && $request->filled('phone');
            $countryCodeChanged = $request->has('country_code') && $request->filled('country_code');

            // Handle phone and WhatsApp link generation
            if ($phoneChanged || $countryCodeChanged) {
                $countryCode = $countryCodeChanged
                    ? $request->country_code
                    : $client->country_code;

                $phone = $phoneChanged
                    ? $request->phone
                    : $client->phone;

                if ($phone && $countryCode) {
                    $validated['phone'] = $this->generateWhatsappNumber($countryCode, $phone);
                    $validated['whatsapp_link'] = $this->generateWhatsappLink($countryCode, $phone);
//                    Log::debug("Generated WhatsApp Link: " . $this->generateWhatsappLink($countryCode, $phone));

                }


            }

            // Handle interested_service if it's an ID (convert to service name)
            if ($request->has('interested_service') && $request->filled('interested_service')) {
                $serviceId = $request->interested_service;

                // If it's numeric, assume it's a service ID and get the name
                if (is_numeric($serviceId)) {
                    $service = \App\Models\Service::find($serviceId);
                    if ($service) {
                        $validated['interested_service'] = $service->id;
//                        Log::debug("service: " . $service->name);
                    }
                }
            }

            // Handle contact_count and requests_count - ensure they're integers
            if ($request->has('contact_count')) {
                $validated['contact_count'] = ($request->contact_count);
//                Log::debug("conatct_count: " .$request->contact_count);

            }

            if ($request->has('interested_service_count')) {
                $validated['interested_service_count'] = ($request->interested_service_count);
            }

            // Update the client
            $client->update($validated);
            Log::debug("Client WhatsApp Link: " . $client->whatsapp_link);
            // Return the complete updated client data with relationships
            return response()->json([
                'success' => true,
                'message' => 'تم تحديث البيانات بنجاح',
                'data'    => $client->fresh()->load('salesRep')->toArray()
            ]);

        } catch (\Exception $e) {
            Log::error('Client update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء التحديث: ' . $e->getMessage()
            ], 500);
        }
    }
}
