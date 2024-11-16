<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class PermissionRoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Truncate the permission_role table
        DB::table('role_has_permission')->truncate();

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $roles = Role::all();
        $admin_permission_ids = Permission::all()->pluck('id')->toArray();

        foreach ($roles as $role) {
            switch ($role->id) {
                case 1:
                    $role->permissions()->sync($admin_permission_ids);
                    break;
                default:
                    break;
            }
        }
    }
}
