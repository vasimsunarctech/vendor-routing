<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'is_admin' => true,
            ]
        );

        $vendorsData = [
            ['name' => 'Vendor A', 'priority' => 1, 'email' => 'vendorA@example.com'],
            ['name' => 'Vendor B', 'priority' => 2, 'email' => 'vendorB@example.com'],
            ['name' => 'Vendor C', 'priority' => 3, 'email' => 'vendorC@example.com'],
        ];

        foreach ($vendorsData as $vendorData) {
            $user = User::firstOrCreate(
                ['email' => $vendorData['email']],
                [
                    'name' => $vendorData['name'],
                    'password' => Hash::make('password'),
                ]
            );

            Vendor::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'name' => $vendorData['name'],
                    'priority' => $vendorData['priority'],
                ]
            );
        }
    }
}
