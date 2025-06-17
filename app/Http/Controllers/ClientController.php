<?php

namespace App\Http\Controllers;

use App\Exports\ClientsExport;
use Illuminate\Support\Str;
use App\Models\Client;
use App\Models\Conversation;
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
class ClientController extends Controller
{
    public function allClients()
    {

        $Clients = Client::all()->map(function ($client) {
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
                'has_request' => $client->allEditRequests()->isNotEmpty() ? 'يوجد' : 'لا يوجد',
                'request_type' => $client->allEditRequests()->sortByDesc('created_at')->first()?->request_type ?? '—',
                'response_status' => $client->allEditRequests()->sortByDesc('created_at')->first()?->status ?? '—',
                'sales_rep_id' => $client->sales_rep_id,
                'contact_days_left' => $client->late_days,

            ];
        });

        return view('clients.table', data: compact('Clients'));

    }
    public function index(SalesRep $salesRep)
    {
        //$this->authorize('viewAny', Client::class);

        $Clients = Client::where('sales_rep_id', $salesRep->id)->get()->map(function ($client) {
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
                'has_request' => $client->allEditRequests()->isNotEmpty() ? 'يوجد' : 'لا يوجد',
                'request_type' => $client->allEditRequests()->sortByDesc('created_at')->first()?->request_type ?? '—',
                'response_status' => $client->allEditRequests()->sortByDesc('created_at')->first()?->status ?? '—',
                'sales_rep_id' => $client->sales_rep_id,
                'contact_days_left' => $client->late_days,

            ];
        });

        return view('clients.table', data: compact('Clients'));

    }

    // Show form to create a new client
    public function create(SalesRep $salesRep)
    {
        //$this->authorize('create', Client::class);
        return view('clients.create');
    }

    public function storeRequest(Request $request, Client $client)
    {
        $validated = $request->validate([

            'request_message' => 'required|string',
        ]);
        $authenticatedUserId = Auth::id();
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
        Message::create(
            [
                'conversation_id' => $conversation->id,
                'sender_id' => $authenticatedUserId,
                'receiver_id' => $conversation->sender_id === $authenticatedUserId ? $conversation->receiver_id : $conversation->sender_id,
                'message' => $request->request_message,
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
        //$this->authorize('create', Client::class);
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'company_logo' => 'required|image|mimes:jpeg,png,jpg,gif',
            'address' => 'required|string',
            'contact_person' => 'required|string|max:255',
            'contact_position' => 'nullable|string|max:255',
            'phone' => 'required|string|max:20',
            'interest_status' => 'required|in:pending,interested,not interested',
        ]);

        // Handle file upload
        if ($request->hasFile('company_logo')) {
            $path = $request->file('company_logo')->store('company_logos', 'public');
            $validated['company_logo'] = $path;
        }
        $validated['sales_rep_id'] = Auth::user()->salesRep->id;
        $cleanedNumber = $this->generateSaudiNumber($validated['phone']);
        $validated['saudi_number'] = $cleanedNumber;
        $validated['whatsapp_link'] = 'https://wa.me/' . ltrim($cleanedNumber, '+');
        $exists = Client::where('contact_position', $validated['contact_position'])
            ->where('contact_person', $validated['contact_person'])
            ->where('phone', $validated['phone'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['duplicate' => 'هذا العميل موجود مسبقًا بنفس البيانات.']);
        }
        $client = Client::create($validated);
        // Notify admin
        $admin = User::where('role', 'admin')->first();
        if ($admin) {
            $admin->notify(new NewClientNotification($client));
        }
        return redirect()->route('sales-reps.clients.index', $client->sales_rep_id)->with('success', 'تمت إضافة العميل بنجاح.');

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
        // Save message in the conversation
        Message::create(
            [
                'conversation_id' => $conversation->id,
                'sender_id' => $authenticatedUserId,
                'receiver_id' => $conversation->sender_id === $authenticatedUserId ? $conversation->receiver_id : $conversation->sender_id,
                'message' => $request->update_message,
            ]
        );


        return back()->with('success', 'Last contact date and update message saved successfully.');
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
}
