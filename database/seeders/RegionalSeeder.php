<?php

namespace Database\Seeders;

use App\Province;
use App\Regency;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

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
        DB::unprepared(file_get_contents(base_path('database/seeders/indonesia/provinces.sql')));
        DB::unprepared(file_get_contents(base_path('database/seeders/indonesia/regencies.sql')));

        Regency::where('name', 'KABUPATEN SIDENRENG RAPPANG')->update(['name' => 'KABUPATEN SIDRAP']);

    }
}
