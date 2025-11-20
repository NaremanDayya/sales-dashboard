<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class GenericClientRequestApprovedNotification extends Notification implements ShouldBroadcastNow
{
    use Queueable;
    protected $clientRequest;

    public function __construct($clientRequest)
    {
        $this->clientRequest = $clientRequest;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => "تمت الموافقة على طلب العميل  {$this->clientRequest->client->company_name}.",
            'client_request_id' => $this->clientRequest->id,
            'client_id' => $this->clientRequest->client_id,
            'request_type' => $this->clientRequest->request_type,
            'status' => 'approved',
	    'url' =>route('sales-reps.clientRequests.show', [
		'client' => $this->clientRequest->client_id,
 		'client_request' => $this->clientRequest->id
	]),
		];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'message' => "تمت الموافقة على طلب العميل  {$this->clientRequest->client->company_name}.",
            'client_request_id' => $this->clientRequest->id,
            'client_id' => $this->clientRequest->client_id,
            'request_type' => $this->clientRequest->request_type,
            'status' => 'approved',
'url' =>route('sales-reps.clientRequests.show', [
                'client' => $this->clientRequest->client_id,
                'client_request' => $this->clientRequest->id
        ]),
        ], true);
    }

    public function broadcastOn()
    {
        return [
            new PrivateChannel('client.request.approved.' . $this->clientRequest->salesRep->id)
        ];
    }

    public function broadcastAs()
    {
        return 'client.request.approved';
    }

}
