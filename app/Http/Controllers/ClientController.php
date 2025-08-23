<?php

namespace App\Http\Controllers;

use App\Exports\ClientsExport;
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

class ClientController extends Controller
{
    public function allClients()
    {

        $Clients = Client::orderByRaw('last_contact_date IS NULL, last_contact_date ASC')->get()->map(function ($client) {
            return [
                'client_id' => $client->id,
                'company_logo' => $client->company_logo ? asset('storage/' . $client->company_logo) : null,
                'company_name' => $client->company_name,
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
                'contact_days_left' => $client->late_days,
'interested_service' => Service::where('id', $client->interested_service)->value('name'),
                'agreements_count' => $client->agreements()->count(),
            ];
        });
	  $services = Service::all();

        return view('clients.table', data: compact('Clients','services'));

    }
    public function index(SalesRep $salesRep)
    {
        //$this->authorize('viewAny', Client::class);

        $Clients = Client::where('sales_rep_id', $salesRep->id)
    		->orderByRaw('last_contact_date IS NULL, last_contact_date ASC')
    		->get()
		->map(function ($client) {
            return [
                'client_id' => $client->id,
                'company_logo' => $client->company_logo ? asset('storage/' . $client->company_logo) : null,
                'company_name' => $client->company_name,
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

            ];
        });

 $services = Service::all();

        return view('clients.table', data: compact('Clients','services'));

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
$message = "طلب خاص بالعميل {$client->company_name}، يريد: {$request->request_message}";         Message::create(
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
            if ($request->hasFile('company_logo')) {
                $tempPath = $request->file('company_logo')->store('temp', 'public');
                session()->put('temp_company_logo', $tempPath);
                $request->merge(['company_logo_temp' => $tempPath]);
            }
            $hasTempLogo = $request->filled('company_logo_temp');

            $validated = $request->validate([
                'company_name' => 'required|string|max:255',
                'company_logo' => [$hasTempLogo ? 'nullable' : 'required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
                'address' => 'required|string',
		'contact_person' => 'required|string|max:255',
                'contact_position' => 'nullable|string|max:255',
                'phone' => 'required|string|digits:10',
                'interest_status' => 'required|in:interested,not interested,neutral',
                'last_contact_date' => 'required|date|before_or_equal:today',
		'interested_service' => 'required|exists:services,id'
            ], [
                'phone.digits' => 'يجب أن يتكون رقم الجوال من 10 أرقام',
                'company_logo.required' => 'يجب اختيار شعار للشركة',
                'last_contact_date.required' => 'يرجى إدخال تاريخ آخر تواصل.',
                'last_contact_date.before_or_equal' => 'لا يمكن اختيار تاريخ في المستقبل.',
            ]);

            // Handle file upload
            if ($request->hasFile('company_logo')) {
                $tempPath = $request->file('company_logo')->store('temp', 'public');
                $validated['company_logo'] = $tempPath;
                session()->put('temp_company_logo', $tempPath);
            } elseif ($hasTempLogo) {
                $validated['company_logo'] = $request->input('company_logo_temp');
            }

            // Check for duplicate client
            $exists = Client::where('company_name', $validated['company_name'])
                ->where('contact_person', $validated['contact_person'])
                ->where('contact_position', $validated['contact_position'])
                ->where('phone', $this->generateSaudiNumber($request->phone))
		->where('interested_service', $validated['interested_service'])

                ->exists();

            if ($exists) {
                return back()
                    ->withInput()
                    ->withErrors(['duplicate' => 'هذا العميل موجود مسبقًا بنفس البيانات.']);
            }

$serviceConflict = Client::where('company_name', $validated['company_name'])
    ->where('contact_person', $validated['contact_person'])
    ->where('contact_position', $validated['contact_position'])
    ->where('phone', $this->generateSaudiNumber($request->phone))
    ->where('interested_service', '!=', $validated['interested_service'])
    ->exists();

if ($serviceConflict) {
return back()
                    ->withInput()
                    ->withErrors(['duplicate' => 'هذا العميل مهتم بخدمة مسبقا.']);

}
            if ($hasTempLogo) {
                $tempPath = $request->input('company_logo_temp');
                $filename = basename($tempPath);
                $newPath = 'company_logos/' . $filename;

                if (Storage::exists('public/' . $tempPath)) {
                    Storage::move('public/' . $tempPath, 'public/' . $newPath);
                    $validated['company_logo'] = $newPath;
                }
                session()->forget('temp_company_logo');
            }
            // Create client
            $validated['sales_rep_id'] = Auth::user()->salesRep->id;
            $validated['phone'] = $this->generateSaudiNumber($request->phone);
	    $validated['whatsapp_link'] = 'https://wa.me/+' . preg_replace('/\D/', '', $validated['phone']);
	    $validated['contact_count'] = 1;
            $client = Client::create($validated);

            // Notify admin
            $admin = User::where('role', 'admin')->first();
            if ($admin) {
                $admin->notify(new NewClientNotification($client));
            }

            return redirect()
                ->route('sales-reps.clients.index', $client->sales_rep_id)
                ->with('success', 'تمت إضافة العميل بنجاح.');

        } catch (ValidationException $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($e->validator);
        }
	        $authenticatedUserId = Auth::id();
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
$message = "📌 تم إضافة عميل جديد إلى النظام:
🏢 الشركة: {$client->company_name}
✉️ التفاصيل: {$request->contact_details}";
        Message::create(
            [
                'conversation_id' => $conversation->id,
                'sender_id' => $authenticatedUserId,
                'receiver_id' => $conversation->sender_id === $authenticatedUserId ? $conversation->receiver_id : $conversation->sender_id,
                'message' => $message,
            ]
        );
    }
    private function generateSaudiNumber($phone)
    {
        $digits = preg_replace('/\D/', '', $phone);

        if (Str::startsWith($digits, '05')) {
            $digits = '966' . substr($digits, 1);
        } elseif (Str::startsWith($digits, '5')) {
            $digits = '966' . $digits;
        } elseif (!Str::startsWith($digits, '966')) {
            $digits = '966' . ltrim($digits, '0');
        }

        return '+' . $digits;
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
        ];
        $approvedEditRequest = $client->clientEditRequests()
            ->where('sales_rep_id', $salesRep->user->id)
            ->where('status', 'approved')
            ->where('request_type', 'client_data_change')
            ->latest()
            ->first();
        $editableField = $approvedEditRequest ? $approvedEditRequest->edited_field : null;
        // dd($editableField);
        return view('clients.show', compact('client', 'salesRep', 'requestTypes', 'columns', 'editableField'));
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
                $rules[$editableField] = 'required|string|max:20';
                break;
	    case 'interest_status':
                $rules[$editableField] = 'nullable|in:interested,not interested,neutral';
                break;
            case 'contact_position':
            case 'address':
                $rules[$editableField] = 'nullable|string';
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
            $validated['whatsapp_link'] = 'https://wa.me/+' . preg_replace('/\D/', '', $validated['phone']);
        }

        $client->update($validated);
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
$message = "📞 تحديث بيانات التواصل مع العميل $client->company_name:
📅 آخر تواصل: {$date}
📝: {$reason}";
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
                    'company_logo' => $client->company_logo ? asset('storage/' . $client->company_logo) : null,
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
}
