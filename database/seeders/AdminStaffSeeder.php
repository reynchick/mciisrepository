<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AdminStaffSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'Administrator']);
        $staffRole = Role::firstOrCreate(['name' => 'MCIIS Staff']);

        $users = [
            [
                'first_name' => 'Elah Marvinelie',
                'middle_name' => 'D.',
                'last_name' => 'Menil',
                'contact_number' => '09123456789',
                'email' => 'emdmenil00759@usep.edu.ph',
                'role_id' => $adminRole->id,
            ],
            [
                'first_name' => 'Gloren Joy',
                'middle_name' => 'E.',
                'last_name' => 'Roque',
                'contact_number' => '09987654321',
                'email' => 'gjeroque00800@usep.edu.ph',
                'role_id' => $staffRole->id,
            ],
        ];

        foreach ($users as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    ...$data,
                    'student_id' => null,
                    // Strong random placeholder (never shared)
                    'password' => Hash::make(Str::random(40)),
                    // If IT has already verified identity, set verified now:
                    // 'email_verified_at' => now(),
                    // Otherwise, keep null so they must verify via email:
                    'email_verified_at' => null,
                ],
            );

            // Send a password reset link so they set their password securely
            Password::sendResetLink(['email' => $user->email]);
        }
    }
}
