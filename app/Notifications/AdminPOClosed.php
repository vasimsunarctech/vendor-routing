<?php

namespace App\Notifications;

use App\Models\PurchaseOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminPOClosed extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected PurchaseOrder $purchaseOrder) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $totalQuantity = $this->purchaseOrder->items()->sum('quantity');
        $fulfilledQuantity = $this->purchaseOrder->items()->sum('fulfilled_quantity');
        $status = $totalQuantity <= $fulfilledQuantity ? 'Fully Fulfilled' : 'Partially Fulfilled (Vendors Exhausted)';

        return (new MailMessage)
            ->subject('PO Closed: '.$this->purchaseOrder->po_number)
            ->line('The Purchase Order '.$this->purchaseOrder->po_number.' has been closed.')
            ->line('Final Status: '.$status)
            ->line('Total Required: '.$totalQuantity)
            ->line('Total Fulfilled: '.$fulfilledQuantity)
            ->action('View Final Summary', route('admin.purchase-orders.show', $this->purchaseOrder->id));
    }
}
