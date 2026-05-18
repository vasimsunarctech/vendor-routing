<?php

namespace App\Notifications;

use App\Models\PurchaseOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VendorNewPO extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected PurchaseOrder $purchaseOrder) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $url = route('vendor.purchase-orders.show', $this->purchaseOrder->id);
        $assignedQuantity = $this->purchaseOrder
            ->vendors()
            ->where('vendor_id', $notifiable->vendor->id)
            ->first()
            ->pivot
            ->quantity_assigned;

        return (new MailMessage)
            ->subject('New Purchase Order Assigned: '.$this->purchaseOrder->po_number)
            ->line('A new Purchase Order has been assigned to you.')
            ->line('PO Number: '.$this->purchaseOrder->po_number)
            ->line('Quantity Required: '.$assignedQuantity)
            ->action('View Purchase Order', $url)
            ->line('Please update the quantity you can fulfill in the vendor portal.');
    }

    public function toArray($notifiable): array
    {
        return [
            'purchase_order_id' => $this->purchaseOrder->id,
            'po_number' => $this->purchaseOrder->po_number,
            'message' => 'A new PO has been assigned to you.',
        ];
    }
}
