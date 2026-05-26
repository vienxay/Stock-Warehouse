<?php

namespace App\Notifications;

use App\Models\StockRequest;
use Illuminate\Notifications\Notification;

class StockRequestUpdated extends Notification
{
    public function __construct(
        public StockRequest $stockRequest,
        public string $action // 'approved' | 'rejected' | 'issued'
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $labels = [
            'approved' => 'ຄຳຮ້ອງຂອງທ່ານຖືກອະນຸມັດແລ້ວ',
            'rejected' => 'ຄຳຮ້ອງຂອງທ່ານຖືກປະຕິເສດ',
            'issued'   => 'ສິນຄ້າຕາມຄຳຮ້ອງຂອງທ່ານຖືກຈ່າຍແລ້ວ',
        ];

        return [
            'type'             => 'request_' . $this->action,
            'request_id'       => $this->stockRequest->id,
            'request_no'       => $this->stockRequest->request_no,
            'message'          => $labels[$this->action] ?? '',
            'rejection_reason' => $this->stockRequest->rejection_reason,
            'url'              => route('requests.show', $this->stockRequest->id),
        ];
    }
}
