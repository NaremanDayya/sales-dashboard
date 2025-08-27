<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientRequest;
use App\Models\ClientEditRequest;
use App\Models\TemporaryPermission;
use App\Models\User;
use App\Notifications\ClientEditRequestApprovedNotification;
use App\Notifications\ClientEditRequestRejectedNotification;
use Illuminate\Http\Request;

class ClientEditRequestController extends Controller
{
    public function index()
    {
        $clientEditRequests = ClientEditRequest::with(['client', 'salesRep'])
            ->latest()
            ->paginate(10); // Adjust per-page limit as needed

        return view('clientRequests.admin.index', compact('clientEditRequests'));
    }
    public function edit(Client $client, ClientEditRequest $client_request)
    {
        $columns = [
            'company_name' => 'اسم الشركة',
            'logo' => 'الشعار',
            'address' => 'العنوان',
            'contact_person' => ' الشخص المسؤول',
            'contact_position' => ' منصب الشخص المسؤول',
            'interest_status' => 'حالة الاهتمام',
            'phone' => 'رقم الجوال',
            'last_contact_date' => ' تاريخ اخر تواصل',
        ];
        return view('clientRequests.admin.edit', compact('client', 'client_request', 'columns'));
    }

    public function update(Request $request, Client $client, ClientEditRequest $client_request)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        $client_request->update([
            'status' => $request->status,
            'response_status' => $request->status,
            'response_date' => now(),
        ]);
        $user = User::where('id', $client_request->sales_rep_id)->first();

        if ($client_request->status == 'approved') {
            TemporaryPermission::create([
                'user_id' => $client_request->sales_rep_id,
                'permissible_type' => Client::class,
                'permissible_id' => $client_request->client_id,
                'field' => $client_request->edited_field,
                'expires_at' => now()->addHours(24),
            ]);
            $user->notify(new ClientEditRequestApprovedNotification($client_request));
        } else if ($client_request->status == 'rejected') {
            $user->notify(new ClientEditRequestRejectedNotification($client_request));
        }
        return redirect()->route('admin.allRequests');
    }

    public function review(Client $client, ClientEditRequest $client_request)
    {
        $client_request->load(['client', 'salesRep']);
        $editedFieldLabel = $this->getFieldLabel($client_request->edited_field);
        return view('clientRequests.admin.review', compact('client_request', 'editedFieldLabel'));
    }
 public function reviewRequest(Client $client, ClientRequest $client_request)
    {
        $client_request->load(['client', 'salesRep']);
        return view('clientRequests.admin.reviewRequest', compact('client_request'));
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

    public function show(ClientEditRequest $clientEditRequest)
    {
        $clientEditRequest->load(['client', 'salesRep']);

        return view('clientRequests.admin.review', compact('clientEditRequest'));
    }
    public function pendedRequests()
    {
        $pendedRequests = ClientEditRequest::with(['client', 'salesRep'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(10);
        return view('clientRequests.admin.pended', compact('pendedRequests'));
    }
	   public function destroy(ClientRequest $client_request)
    {

dd('test');
        $client_request->delete();


        return redirect()->back()->with('success', 'تم حذف طلب تعديل العميل بنجاح.');
    }
}
