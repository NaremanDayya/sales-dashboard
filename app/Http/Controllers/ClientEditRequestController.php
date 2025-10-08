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
use App\Models\ClientRequest;

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
            'interested_service',
            'interested_service_count',
        ];
 $fieldTranslations = [
        'company_name' => 'اسم الشركة',
        'logo' => 'الشعار',
        'address' => 'العنوان',
        'contact_person' => 'الشخص المسؤول',
        'interest_status' => 'حالة الاهتمام',
        'phone' => 'رقم الهاتف',
        'contact_position' => 'منصب المسؤول',
    ];
        $validated = $request->validate([

            'update_message' => 'required|string',
            'edited_field' => ['required', 'in:' . implode(',', $columns)],
        ]);
        $editedField = $validated['edited_field'];
        $oldValue = $client->$editedField;
        $clientEditRequest = ClientEditRequest::create([
            'client_id'     => $client->id,
            'sales_rep_id'  => $client->salesRep->user->id,
            'request_type'  => 'client_data_change',
            'description'   => $validated['updated_message'] ?? null,
            'status'        => 'pending',
            'edited_field'  => $editedField,
            'payload'       => [
                'old_value' => $oldValue,
            ],
        ]);
//        dd($clientEditRequest->payload);
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
public function showRequest(Client $client, ClientRequest $client_request)
    {
        $client_request->load(['client', 'salesRep']);
        return view('clientRequests.admin.review', compact('client_request'));
    }
}
