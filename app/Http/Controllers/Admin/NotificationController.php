<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->paginate(15);

        return view('admin.notifications.index', compact('notifications'));
    }

    public function read(Request $request, DatabaseNotification $notification): RedirectResponse
    {
        abort_unless((string) $notification->notifiable_id === (string) $request->user()->id, 403);

        $notification->markAsRead();

        $purchaseOrderId = $notification->data['purchase_order_id'] ?? null;
        if ($purchaseOrderId) {
            return redirect()->route('admin.purchase-orders.show', $purchaseOrderId);
        }

        return redirect()->route('admin.notifications.index');
    }

    public function markAllRead(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return redirect()->route('admin.notifications.index')->with('success', 'All notifications marked as read.');
    }
}
