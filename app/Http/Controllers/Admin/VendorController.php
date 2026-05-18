<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class VendorController extends Controller
{
    public function index(): View
    {
        $vendors = Vendor::with('user')
            ->withCount('purchaseOrders')
            ->orderByRaw('priority is null')
            ->orderBy('priority')
            ->orderBy('name')
            ->paginate(10);

        return view('admin.vendors.index', compact('vendors'));
    }

    public function create(): View
    {
        return view('admin.vendors.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'priority' => ['nullable', 'integer', 'min:1', 'unique:vendors,priority'],
        ]);

        DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'is_admin' => false,
            ]);

            Vendor::create([
                'user_id' => $user->id,
                'name' => $data['name'],
                'priority' => $data['priority'] ?? null,
            ]);
        });

        return redirect()->route('admin.vendors.index')->with('success', 'Vendor created successfully.');
    }

    public function edit(Vendor $vendor): View
    {
        $vendor->load('user');

        return view('admin.vendors.edit', compact('vendor'));
    }

    public function update(Request $request, Vendor $vendor): RedirectResponse
    {
        $vendor->load('user');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($vendor->user_id),
            ],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'priority' => [
                'nullable',
                'integer',
                'min:1',
                Rule::unique('vendors', 'priority')->ignore($vendor->id),
            ],
        ]);

        DB::transaction(function () use ($data, $vendor) {
            $vendor->update([
                'name' => $data['name'],
                'priority' => $data['priority'] ?? null,
            ]);

            $userData = [
                'name' => $data['name'],
                'email' => $data['email'],
            ];

            if (!empty($data['password'])) {
                $userData['password'] = Hash::make($data['password']);
            }

            $vendor->user->update($userData);
        });

        return redirect()->route('admin.vendors.index')->with('success', 'Vendor updated successfully.');
    }

    public function destroy(Vendor $vendor): RedirectResponse
    {
        DB::transaction(function () use ($vendor) {
            $user = $vendor->user;
            $vendor->delete();
            $user?->delete();
        });

        return redirect()->route('admin.vendors.index')->with('success', 'Vendor deleted successfully.');
    }
}
