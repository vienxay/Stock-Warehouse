<?php

namespace App\Notifications;

use App\Models\StockRequest;
use Illuminate\Notifications\Notification;

class NewStockRequest extends Notification
{
    public function __construct(public StockRequest $stockRequest) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type'       => 'new_request',
            'request_id' => $this->stockRequest->id,
            'request_no' => $this->stockRequest->request_no,
            'requester'  => $this->stockRequest->requester->name ?? '—',
            'purpose'    => $this->stockRequest->purpose,
            'item_count' => $this->stockRequest->items->count(),
            'url'        => route('requests.show', $this->stockRequest->id),
        ];
    }
}
