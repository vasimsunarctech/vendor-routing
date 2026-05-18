<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class HomeController extends Controller
{
    public function index(): RedirectResponse
    {
        if (auth()->user()?->isAdmin()) {
            return redirect()->route('admin.purchase-orders.index');
        }

        return redirect()->route('vendor.dashboard');
    }
}
