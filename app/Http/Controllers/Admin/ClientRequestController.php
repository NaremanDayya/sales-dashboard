<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientRequest;
use App\Models\User;
use Illuminate\Http\Request;
use App\Notifications\GenericClientRequestApprovedNotification;
use App\Notifications\GenericClientRequestRejectedNotification;

class ClientRequestController extends Controller
{
    public function index()
    {
        $clientRequests = ClientRequest::with(['client', 'salesRep'])->latest()->get();
        return view('clientRequests.admin.indexRequest', compact('clientRequests'));
    }

    public function review(Client $client, ClientRequest $client_request)
    {
        $client_request->load(['client', 'salesRep']);
        return view('clientRequests.admin.reviewRequest', compact('client_request'));
    }

    public function edit(Client $client, ClientRequest $client_request)
    {
        return view('clientRequests.admin.editRequest', compact('client', 'client_request'));
    }

    public function update(Request $request, Client $client, ClientRequest $client_request)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        $client_request->update([
            'status' => $request->status,
            'updated_at' => now(),
        ]);

        $user = User::find($client_request->sales_rep_id);

        if ($request->status === 'approved') {
            $user->notify(new GenericClientRequestApprovedNotification($client_request));
        } elseif ($request->status === 'rejected') {
            $user->notify(new GenericClientRequestRejectedNotification($client_request));
        }
	return back()->with('success', 'تم تقييم الطلب بنجاح.');


    }
}
