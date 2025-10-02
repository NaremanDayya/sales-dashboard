<?php

namespace App\Http\Controllers;

use App\Models\Agreement;
use App\Models\AgreementEditRequest;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\RequestType;
use App\Models\User;
use App\Notifications\NewAgreementEditRequestNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

class AgreementEditRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = AgreementEditRequest::with(['agreement', 'client'])
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

        return view('agreementRequests.salesRep.index', compact('requests'));
    }

    public function create(Agreement $agreement)
    {
        return view('agreementRequests.salesRep.create', compact('agreement'));
    }

    public function store(Request $request, Agreement $agreement)
    {
        $columns = [
            'service_id',
            'signing_date',
            'duration_years',
            'end_date',
            'termination_type',
            'notice_months',
            'notice_status',
            'product_quantity',
            'price',
            'agreement_status',
            'implementation_date',
        ];
 $fieldTranslations = [
        'service_id' => 'الخدمة',
        'signing_date' => 'تاريخ التوقيع',
        'duration_years' => 'مدة العقد (بالسنوات)',
        'end_date' => 'تاريخ الانتهاء',
        'termination_type' => 'نوع الإنهاء',
        'notice_months' => 'عدد أشهر الإشعار',
        'notice_status' => 'حالة الإشعار',
        'product_quantity' => 'الكمية',
        'price' => 'السعر',
        'agreement_status' => 'حالة الاتفاقية',
        'implementation_date' => 'تاريخ التنفيذ',
    ];
        $validated = $request->validate([
            'update_message' => 'required|string',
            'edited_field' => ['required', 'in:' . implode(',', $columns)],
        ]);

        $agreement_edit_request = AgreementEditRequest::create([
            'agreement_id' => $agreement->id,
            'client_id' => $agreement->client_id,
            'sales_rep_id' => $agreement->salesRep->user->id,
            'request_type' => 'agreement_data_change',
            'description' => $validated['update_message'] ?? null,
            'status' => 'pending',
            'edited_field' => $validated['edited_field'],
        ]);

        $admins = User::where('role', 'admin')->get();
        Notification::send($admins, new NewAgreementEditRequestNotification($agreement_edit_request));
        return redirect()
            ->route('salesrep.agreements.show', [
                'agreement' => $agreement->id,
                'salesrep' => Auth::user()->salesRep->id,
            ])
            ->with([
                'success',
                'تم إرسال طلب تعديل الاتفاقية بنجاح.',
                'refreshChatList',
                true
            ]);
    }

    public function show(Agreement $agreement, AgreementEditRequest $agreement_request)
    {
 $agreement_request->load(['client','agreement', 'salesRep']);
        $editedFieldLabel = $this->getFieldLabel($agreement_request->edited_field);
        return view('agreementRequests.admin.review', compact('agreement_request', 'editedFieldLabel'));
     }
}
