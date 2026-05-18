<?php

namespace Database\Seeders;

use App\Models\PurchaseOrder;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

class PurchaseOrderSeeder extends Seeder
{
    public function run(): void
    {
        $vendorA = Vendor::where('priority', 1)->first();
        $vendorB = Vendor::where('priority', 2)->first();
        $vendorC = Vendor::where('priority', 3)->first();

        $this->seedPendingPurchaseOrder($vendorA);
        $this->seedPartiallyFulfilledPurchaseOrder($vendorA, $vendorB);
        $this->seedFulfilledPurchaseOrder($vendorA, $vendorB, $vendorC);
    }

    private function seedPendingPurchaseOrder(?Vendor $vendor): void
    {
        $po = PurchaseOrder::updateOrCreate(
            ['po_number' => 'PO-SAMPLE-001'],
            [
                'required_date' => now()->addDays(7)->toDateString(),
                'status' => 'pending',
            ]
        );

        $this->syncItems($po, [
            ['item_name' => 'Hydraulic Pump Assembly', 'quantity' => 25, 'fulfilled_quantity' => 0],
            ['item_name' => 'Control Valve Kit', 'quantity' => 10, 'fulfilled_quantity' => 0],
        ]);

        if ($vendor) {
            $this->upsertVendorAssignment($po, $vendor, 35, null, 'pending');
        }
    }

    private function seedPartiallyFulfilledPurchaseOrder(?Vendor $vendorA, ?Vendor $vendorB): void
    {
        $po = PurchaseOrder::updateOrCreate(
            ['po_number' => 'PO-SAMPLE-002'],
            [
                'required_date' => now()->addDays(10)->toDateString(),
                'status' => 'partially_fulfilled',
            ]
        );

        $this->syncItems($po, [
            ['item_name' => 'Bearing Housing', 'quantity' => 40, 'fulfilled_quantity' => 20],
            ['item_name' => 'Seal Ring Set', 'quantity' => 20, 'fulfilled_quantity' => 0],
        ]);

        if ($vendorA) {
            $this->upsertVendorAssignment($po, $vendorA, 60, 20, 'partially_fulfilled');
        }

        if ($vendorB) {
            $this->upsertVendorAssignment($po, $vendorB, 40, null, 'pending');
        }
    }

    private function seedFulfilledPurchaseOrder(?Vendor $vendorA, ?Vendor $vendorB, ?Vendor $vendorC): void
    {
        $po = PurchaseOrder::updateOrCreate(
            ['po_number' => 'PO-SAMPLE-003'],
            [
                'required_date' => now()->addDays(14)->toDateString(),
                'status' => 'fulfilled',
            ]
        );

        $this->syncItems($po, [
            ['item_name' => 'Gearbox Coupling', 'quantity' => 12, 'fulfilled_quantity' => 12],
            ['item_name' => 'Drive Shaft', 'quantity' => 8, 'fulfilled_quantity' => 8],
        ]);

        if ($vendorA) {
            $this->upsertVendorAssignment($po, $vendorA, 20, 0, 'declined');
        }

        if ($vendorB) {
            $this->upsertVendorAssignment($po, $vendorB, 20, 12, 'partially_fulfilled');
        }

        if ($vendorC) {
            $this->upsertVendorAssignment($po, $vendorC, 8, 8, 'fulfilled');
        }
    }

    private function syncItems(PurchaseOrder $po, array $items): void
    {
        $po->items()->delete();
        $po->items()->createMany($items);
    }

    private function upsertVendorAssignment(
        PurchaseOrder $po,
        Vendor $vendor,
        int $quantityAssigned,
        ?int $quantityFulfilled,
        string $status
    ): void {
        if ($po->vendors()->where('vendor_id', $vendor->id)->exists()) {
            $po->vendors()->updateExistingPivot($vendor->id, [
                'quantity_assigned' => $quantityAssigned,
                'quantity_fulfilled' => $quantityFulfilled,
                'status' => $status,
            ]);

            return;
        }

        $po->vendors()->attach($vendor->id, [
            'quantity_assigned' => $quantityAssigned,
            'quantity_fulfilled' => $quantityFulfilled,
            'status' => $status,
        ]);
    }
}
