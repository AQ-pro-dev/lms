<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'email' => 'admin@example.com',
                'microsoft_id' => null,
                'microsoft_account' => false,
                'first_name' => 'Admin',
                'last_name' => 'User',
                'username' => 'admin',
                'phone' => '1234567890',
                'bio' => 'I am the administrator of this platform.',
                'password' => Hash::make('12345678'), 
                'pfp' => null,
                'cover_photo' => null,
                'role_id' => 1,
                'email_verified_at' => now(),
                'verification_token' => now(),
                'timezone' => 'UTC',
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
        // DB::table('roles_users')->insert([
        //     ['user_id' => 1, 'role_id' => 1], // Admin
        //     // ['user_id' => 2, 'role_id' => 2], // Tutor
        //     // ['user_id' => 3, 'role_id' => 2], // Tutor
        //     // ['user_id' => 4, 'role_id' => 3], // Learner
        //     // ['user_id' => 5, 'role_id' => 3], // Learner
        // ]);
    }
}
