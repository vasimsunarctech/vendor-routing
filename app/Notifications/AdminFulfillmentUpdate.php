<?php

namespace App\Notifications;

use App\Models\PurchaseOrder;
use App\Models\Vendor;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminFulfillmentUpdate extends Notification
{
    use Queueable;

    public function __construct(
        protected PurchaseOrder $purchaseOrder,
        protected Vendor $vendor,
        protected int $fulfilledQuantity
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $url = route('admin.purchase-orders.show', $this->purchaseOrder->id);

        return (new MailMessage)
            ->subject('Fulfillment Update for PO: '.$this->purchaseOrder->po_number)
            ->line('Vendor '.$this->vendor->name.' has responded to PO '.$this->purchaseOrder->po_number.'.')
            ->line('They have committed to fulfilling '.$this->fulfilledQuantity.' units.')
            ->action('View PO Status', $url);
    }

    public function toArray($notifiable): array
    {
        return [
            'purchase_order_id' => $this->purchaseOrder->id,
            'po_number' => $this->purchaseOrder->po_number,
            'vendor_id' => $this->vendor->id,
            'vendor_name' => $this->vendor->name,
            'fulfilled_quantity' => $this->fulfilledQuantity,
            'message' => $this->vendor->name.' updated '.$this->purchaseOrder->po_number.' with '.$this->fulfilledQuantity.' units.',
        ];
    }
}
