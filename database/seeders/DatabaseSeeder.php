<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        /*
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);*/


        $this->call([            
            RolesTableSeeder::class,
            PermissionsTableSeeder::class,
            PermissionRoleTableSeeder::class,
            LanguageTableSeeder::class,
            SocietyTableSeeder::class,
            LocationsTableSeeder::class,
            UsersTableSeeder::class,
            RoleUserTableSeeder::class,
            SettingSeeder::class,            
        ]);
    }
}
