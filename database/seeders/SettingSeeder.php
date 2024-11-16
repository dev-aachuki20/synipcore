<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::truncate();
        $createDate = Carbon::now()->format('Y-m-d H:i:s');
        $settings = [
            [
                'key'       => 'site_title',
                'value'     => 'SynipCore',
                'setting_type'  => 'text',
                'display_name'  => 'Site Title',
                'group'         => 'web',
                'details'       => null,
                'status'        => 1,
                'position'      => 1,
                'created_at'    => $createDate,
                'created_by'    => 1,
            ],
            [
                'key'           => 'site_logo',
                'value'         => null,
                'setting_type'  => 'image',
                'details'       => null,
                'display_name'  => 'Site Logo',
                'group'         => 'web',
                'status'        => 1,
                'position'      => 2,
                'created_at'    => $createDate,
                'created_by'    => 1,
            ],
            [
                'key'           => 'splash_logo',
                'value'         => null,
                'setting_type'  => 'image',
                'details'       => null,
                'display_name'  => 'Splash Logo',
                'group'         => 'web',
                'status'        => 1,
                'position'      => 2,
                'created_at'    => $createDate,
                'created_by'    => 1,
            ],
            [
                'key'    => 'favicon',
                'value'  => null,
                'setting_type'   => 'image',
                'details' => null,
                'display_name' => 'Favicon Icon',
                'group'  => 'web',
                'status' => 1,
                'position' => 3,
                'created_at' => $createDate,
                'created_by' => 1,
            ],
            [
                'key'    => 'terms_condition',
                'value'  => null,
                'setting_type'   => 'file',
                'details' => null,
                'display_name' => 'Terms And Condition',
                'group'  => 'web',
                'status' => 1,
                'position' => 4,
                'created_at' => $createDate,
                'created_by' => 1,
            ],
            [
                'key'    => 'privacy_policy',
                'value'  => '',
                'setting_type'   => 'file',
                'display_name'  => 'Privacy Policy',
                'group'  => 'api',
                'details' => null,
                'status' => 1,
                'position' => 1,
                'created_at' => $createDate,
                'created_by' => 1,
            ],
            [
                'key'    => 'about_us',
                'value'  => '',
                'setting_type'   => 'file',
                'display_name'  => 'About Us',
                'group'  => 'api',
                'details' => null,
                'status' => 1,
                'position' => 1,
                'created_at' => $createDate,
                'created_by' => 1,
            ],
            [
                'key'    => 'currency_code',
                'value'  => 'USD',
                'setting_type'   => 'text',
                'display_name'  => 'Currency Code',
                'group'  => 'api',
                'details' => null,
                'status' => 1,
                'position' => 1,
                'created_at' => $createDate,
                'created_by' => 1,
            ],
            [
                'key'    => 'currency_icon',
                'value'  => '$',
                'setting_type'   => 'text',
                'display_name'  => 'Currency Icon',
                'group'  => 'api',
                'details' => null,
                'status' => 1,
                'position' => 1,
                'created_at' => $createDate,
                'created_by' => 1,
            ],
            [
                'key'    => 'tax_in_percent',
                'value'  => '10',
                'setting_type'   => 'text',
                'display_name'  => 'Tax In Percent',
                'group'  => 'api',
                'details' => null,
                'status' => 1,
                'position' => 1,
                'created_at' => $createDate,
                'created_by' => 1,
            ],
            [
                'key'    => 'support_email',
                'value'  => 'admin@example.com',
                'setting_type'   => 'text',
                'display_name'  => 'Support Email',
                'group'  => 'api',
                'details' => null,
                'status' => 1,
                'position' => 1,
                'created_at' => $createDate,
                'created_by' => 1,
            ],
            [
                'key'    => 'support_contact',
                'value'  => '8181818118',
                'setting_type'   => 'text',
                'display_name'  => 'Support Contact',
                'group'  => 'api',
                'details' => null,
                'status' => 1,
                'position' => 1,
                'created_at' => $createDate,
                'created_by' => 1,
            ],
            [
                'key'    => 'fire_alert',
                'value'  => '8181818118',
                'setting_type'   => 'text',
                'display_name'  => 'Fire Alert',
                'group'  => 'api',
                'details' => null,
                'status' => 1,
                'position' => 1,
                'created_at' => $createDate,
                'created_by' => 1,
            ],
            [
                'key'    => 'lift_alert',
                'value'  => '8181818118',
                'setting_type'   => 'text',
                'display_name'  => 'Lift Alert',
                'group'  => 'api',
                'details' => null,
                'status' => 1,
                'position' => 1,
                'created_at' => $createDate,
                'created_by' => 1,
            ],
            [
                'key'    => 'animal_alert',
                'value'  => '8181818118',
                'setting_type'   => 'text',
                'display_name'  => 'Animal Alert',
                'group'  => 'api',
                'details' => null,
                'status' => 1,
                'position' => 1,
                'created_at' => $createDate,
                'created_by' => 1,
            ],
            [
                'key'    => 'visitor_alert',
                'value'  => '8181818118',
                'setting_type'   => 'text',
                'display_name'  => 'Visitor Alert',
                'group'  => 'api',
                'details' => null,
                'status' => 1,
                'position' => 1,
                'created_at' => $createDate,
                'created_by' => 1,
            ]
        ];
        Setting::insert($settings);
    }
}
