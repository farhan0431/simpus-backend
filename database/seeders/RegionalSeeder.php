<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Regency;
use App\District;
use App\Village;
use Illuminate\Support\Facades\DB;
use App\Province;

class RegionalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Province::truncate();
        Regency::truncate();
        District::truncate();
        Village::truncate();
        DB::unprepared(file_get_contents(base_path('database/seeders/indonesia/provinces.sql')));
        DB::unprepared(file_get_contents(base_path('database/seeders/indonesia/regencies.sql')));
        DB::unprepared(file_get_contents(base_path('database/seeders/indonesia/district.sql')));
        DB::unprepared(file_get_contents(base_path('database/seeders/indonesia/villages.sql')));

        Regency::where('name', 'KABUPATEN SIDENRENG RAPPANG')->update(['name' => 'KABUPATEN SIDRAP']);
    }
}
