<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $username = env('PURPLE_BLEND_ADMIN_USERNAME');
        $password = env('PURPLE_BLEND_ADMIN_PASSWORD');

        if (!$username || !$password) {
            throw new RuntimeException('Set PURPLE_BLEND_ADMIN_USERNAME and PURPLE_BLEND_ADMIN_PASSWORD before running AdminSeeder.');
        }

        Admin::updateOrCreate(
            ['username' => $username],
            [
                'first_name' => 'Store',
                'last_name' => 'Administrator',
                'email' => null,
                'password_hash' => Hash::make($password),
                'role' => 'manager',
                'is_active' => true,
            ]
        );
    }
}
