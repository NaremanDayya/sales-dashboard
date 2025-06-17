<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientEditRequest;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\RequestType;
use App\Models\User;
use App\Notifications\ClientEditRequestNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientEditRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = ClientEditRequest::with('client')
            ->where('sales_rep_id', Auth::user()->salesRep->id);

        // Filter by status
        if ($request->filled('status') && in_array($request->status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $request->status);
        }

        // Search by client name or description
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('client', function ($q) use ($search) {
                $q->where('company_name', 'like', "%$search%");
            })->orWhere('description', 'like', "%$search%");
        }

        $requests = $query->latest()->paginate(10)->appends($request->all());

        return view('clientRequests.salesRep.index', compact('requests'));
    }


    public function create(Client $client)
    {
        return view('clientRequests.salesRep.create', compact('client'));
    }

    public function store(Request $request, Client $client)
    {

        $columns = [
            'company_name',
            'logo',
            'address',
            'contact_person',
            'interest_status',
            'phone',
            'contact_position',
        ];

        $validated = $request->validate([

            'update_message' => 'required|string',
            'edited_field' => ['required', 'in:' . implode(',', $columns)],
        ]);
        $clientEditRequest = ClientEditRequest::create([
            'client_id' => $client->id,
            'sales_rep_id' => $client->salesRep->user->id,
            'request_type' => 'client_data_change',
            'description' => $validated['updated_message'] ?? null,
            'status' => 'pending',
            'edited_field' => $validated['edited_field'],
        ]);
        $authenticatedUserId = Auth::id();
        // dd($authenticatedUserId);
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
        $user = User::where('role', 'admin')->first();
        $user->notify(new ClientEditRequestNotification($clientEditRequest));
        return redirect()
            ->route('sales-reps.clients.show', [
                'client' => $client->id,
                'sales_rep' => Auth::user()->salesRep->id,
            ])
            ->with([
                'success',
                'تم إرسال طلب التعديل بنجاح.',
                'refreshChatList',
                true
            ]);
    }

    public function show(Client $client, ClientEditRequest $client_request)
    {
        $client_request->load(['client', 'salesRep']);
        $editedFieldLabel = $this->getFieldLabel($client_request->edited_field);
        return view('clientRequests.admin.review', compact('client_request', 'editedFieldLabel'));
    }
    protected function getFieldLabel($field)
    {
        $labels = [
            'company_name' => 'اسم الشركة',
            'logo' => 'الشعار',
            'address' => 'العنوان',
            'contact_person' => 'اسم الشخص المسؤول ',
            'interest_status' => 'حالة الاهتمام',
            'phone' => 'رقم الهاتف',
            'contact_position' => 'منصب الشخص المسؤول ',
        ];

        return $labels[$field] ?? $field;
    }
}
