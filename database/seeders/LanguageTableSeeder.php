<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class LanguageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Language::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $created_at = date('Y-m-d H:i:s');
        $languages = [
            [
                'name' => 'English',
                'code' => 'en',
                'status' => true,
                'created_at' => $created_at,
            ],
            [
                'name' => 'Chinese',  //  '繁體中文', Traditional Chinese
                'code' => 'cn',
                'status' => true,
                'created_at' => $created_at,
            ],
            [
                'name' => 'Japanese', // '简体中文', Simplified Chinese
                'code' => 'jp',
                'status' => true,
                'created_at' => $created_at,
            ],
        ];
        foreach ($languages as $language) {
            Language::create($language);
        }
    }
}
