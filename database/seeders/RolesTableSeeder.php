<?php

namespace Database\Seeders;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Role::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $updateDate = $createDate = date('Y-m-d H:i:s');
        $roles = [
            [
                'id'         => 1,
                'name'      => 'Super Admin',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'id'         => 2,
                'name'      => 'Admin',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'id'         => 3,
                'name'      => 'Customer',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'id'         => 4,
                'name'      => 'Provider',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'id'         => 5,
                'name'      => 'Guard',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ],
            [
                'id'         => 6,
                'name'      => 'Resident',
                'created_at' => $createDate,
                'updated_at' => $updateDate,
            ]
        ];
        Role::insert($roles);
    }
}
