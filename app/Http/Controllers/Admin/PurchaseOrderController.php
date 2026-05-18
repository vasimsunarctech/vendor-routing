<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Services\PurchaseOrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PurchaseOrderController extends Controller
{
    public function index(): View
    {
        $purchaseOrders = PurchaseOrder::with('items')->latest()->paginate(10);

        return view('admin.purchase_orders.index', compact('purchaseOrders'));
    }

    public function create(): View
    {
        return view('admin.purchase_orders.create');
    }

    public function store(Request $request, PurchaseOrderService $poService): RedirectResponse
    {
        $request->validate([
            'required_date' => 'required|date|after_or_equal:today',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $po = PurchaseOrder::create([
            'po_number' => 'PO-'.Str::upper(Str::random(8)),
            'required_date' => $request->required_date,
        ]);

        foreach ($request->items as $item) {
            $po->items()->create([
                'item_name' => $item['name'],
                'quantity' => $item['quantity'],
            ]);
        }

        $poService->assignToNextVendor($po);

        return redirect()
            ->route('admin.purchase-orders.index')
            ->with('success', 'Purchase Order created and sent to the first vendor.');
    }

    public function show(PurchaseOrder $purchaseOrder): View
    {
        $purchaseOrder->load('items', 'vendors.user');

        return view('admin.purchase_orders.show', compact('purchaseOrder'));
    }
}
