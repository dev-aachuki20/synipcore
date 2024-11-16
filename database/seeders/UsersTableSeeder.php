<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\DB;


class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Truncate the users table
        User::truncate();

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $email_verified_at = date('Y-m-d H:i:s');
        $users = [
            [
                'name'              => 'Super Admin',
                'email'             => 'superadmin@gmail.com',
                'username'          =>  'superadmin',
                'password'          => bcrypt('12345678'),
                'mobile_number'     => '9876543210',
                'mobile_verified'   => 1,
                'is_verified'       => 1,
                'is_featured'       => 0,
                'language_id'       => 1,
                'society_id'        => 1,
                'remember_token'    => null,
                'email_verified_at' => $email_verified_at,
                'status'            => 1,
                'created_by'        => 1
            ]
        ];
        foreach ($users as $key => $user) {
            $createdUser =  User::create($user);
        }
    }
}
