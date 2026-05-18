<?php

namespace App\Services;

use App\Models\PurchaseOrder;
use App\Models\User;
use App\Models\Vendor;
use App\Notifications\AdminFulfillmentUpdate;
use App\Notifications\AdminPOClosed;
use App\Notifications\VendorNewPO;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class PurchaseOrderService
{
    public function assignToNextVendor(PurchaseOrder $po): void
    {
        $totalQuantity = $po->items()->sum('quantity');
        $fulfilledQuantity = $po->items()->sum('fulfilled_quantity');
        $remainingQuantity = $totalQuantity - $fulfilledQuantity;

        if ($remainingQuantity <= 0) {
            $this->closePurchaseOrder($po, true);

            return;
        }

        $assignedVendorIds = $po->vendors()->pluck('vendors.id');

        $nextVendor = Vendor::whereNotNull('priority')
            ->whereNotIn('id', $assignedVendorIds)
            ->orderBy('priority')
            ->first();

        if ($nextVendor) {
            $po->vendors()->attach($nextVendor->id, ['quantity_assigned' => $remainingQuantity]);
            Log::info("PO #{$po->po_number} assigned to Vendor #{$nextVendor->id} ({$nextVendor->name})");

            if ($nextVendor->user) {
                Notification::send($nextVendor->user, new VendorNewPO($po));
            }

            return;
        }

        Log::info("No more vendors for PO #{$po->po_number}. Closing.");
        $this->closePurchaseOrder($po, false);
    }

    public function processVendorResponse(PurchaseOrder $po, Vendor $vendor, int $fulfilledQuantity): void
    {
        $assignedPO = $po->vendors()->where('vendor_id', $vendor->id)->firstOrFail();
        $pivot = $assignedPO->pivot;
        $pivot->quantity_fulfilled = $fulfilledQuantity;

        $status = 'declined';
        if ($fulfilledQuantity > 0) {
            $status = $fulfilledQuantity >= $pivot->quantity_assigned ? 'fulfilled' : 'partially_fulfilled';
        }

        $pivot->status = $status;
        $pivot->save();

        if ($fulfilledQuantity > 0) {
            $this->updatePOItemsFulfilledQuantity($po, $fulfilledQuantity);
        }

        $admin = User::where('is_admin', true)->first();
        if ($admin) {
            Notification::send($admin, new AdminFulfillmentUpdate($po, $vendor, $fulfilledQuantity));
        }

        $this->assignToNextVendor($po);
    }

    private function updatePOItemsFulfilledQuantity(PurchaseOrder $po, int $newlyFulfilled): void
    {
        $po->load('items');

        foreach ($po->items as $item) {
            $needed = $item->quantity - $item->fulfilled_quantity;
            if ($newlyFulfilled > 0 && $needed > 0) {
                $fulfill = min($newlyFulfilled, $needed);
                $item->fulfilled_quantity += $fulfill;
                $item->save();
                $newlyFulfilled -= $fulfill;
            }
        }
    }

    private function closePurchaseOrder(PurchaseOrder $po, bool $isFulfilled): void
    {
        $po->status = $isFulfilled ? 'fulfilled' : 'closed';
        $po->save();

        $admin = User::where('is_admin', true)->first();
        if ($admin) {
            Notification::send($admin, new AdminPOClosed($po));
        }
    }
}
