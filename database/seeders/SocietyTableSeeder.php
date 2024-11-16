<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Society; // Ensure you include the model

class SocietyTableSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Society::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $societies = [
            [
                'uuid'        => (string) Str::uuid(),
                'name'        => 'Society one',
                'address'     => 'Delhi',
                'status'      => 1,
                'created_by'  => 1,
                'updated_by'  => null,
            ],
            [
                'uuid'        => (string) Str::uuid(),
                'name'        => 'Society two',
                'address'     => 'Jaipur',
                'status'      => 1,
                'created_by'  => 1, // Ensure this ID exists in your users table
                'updated_by'  => null,
            ],
        ];

        foreach ($societies as $society) {
            Society::create($society);
        }
    }
}
