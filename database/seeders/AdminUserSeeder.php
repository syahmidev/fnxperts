<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Administrator',
                'password' => 'password', // hashed automatically via the model's cast
            ],
        );

        if ($admin->email_verified_at === null) {
            $admin->forceFill(['email_verified_at' => now()])->save();
        }
    }
}
