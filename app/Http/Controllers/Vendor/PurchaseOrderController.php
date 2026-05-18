<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Services\PurchaseOrderService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PurchaseOrderController extends Controller
{
    public function index(): View
    {
        $vendor = Auth::user()->vendor;
        $purchaseOrders = $vendor
            ? $vendor->purchaseOrders()->wherePivot('status', 'pending')->latest()->paginate(10)
            : new LengthAwarePaginator([], 0, 10);

        return view('vendor.dashboard', compact('purchaseOrders'));
    }

    public function show(PurchaseOrder $purchaseOrder): View
    {
        $vendor = Auth::user()->vendor;
        $assignedPO = $vendor?->purchaseOrders()->where('purchase_order_id', $purchaseOrder->id)->first();

        if (!$assignedPO) {
            abort(403, 'You are not authorized to view this Purchase Order.');
        }

        return view('vendor.purchase_orders.show', ['purchaseOrder' => $assignedPO]);
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder, PurchaseOrderService $poService): RedirectResponse
    {
        $vendor = Auth::user()->vendor;
        $assignedPO = $vendor?->purchaseOrders()->where('purchase_order_id', $purchaseOrder->id)->first();

        if (!$assignedPO || $assignedPO->pivot->status !== 'pending') {
            return redirect()->route('vendor.dashboard')->with('error', 'This PO cannot be updated.');
        }

        $maxQuantity = $assignedPO->pivot->quantity_assigned;

        $request->validate([
            'fulfilled_quantity' => "required|integer|min:0|max:{$maxQuantity}",
        ]);

        $poService->processVendorResponse($purchaseOrder, $vendor, $request->fulfilled_quantity);

        return redirect()->route('vendor.dashboard')->with('success', 'Fulfillment quantity updated successfully.');
    }
}
