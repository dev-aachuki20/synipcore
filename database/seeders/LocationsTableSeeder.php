<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Log;

class LocationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sqlFile = database_path('seeds/sql/locations.sql');

        // disable forign key checks.
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');   

        DB::table('locations')->truncate();
        
        // Re-enable forign key checks.
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        if (file_exists($sqlFile)) {
            $sql = file_get_contents($sqlFile);
            DB::unprepared($sql);
        } else {
            \Log::error("SQL file not found: " . $sqlFile);
        }
    }
}
