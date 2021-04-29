<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Settings;
class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Settings::create([
            'rumah_sakit' => 'RSU YAPIKA',
            'deskripsi_rumah_sakit' => 'Sendi Sehat Semangat Gowes',
            'inisial_rumah_sakit' => 'BAPENDA',
            'kota' => '2',
            'provinsi' => '1',
            'alamat' => 'Jl. Jend Sudirman, Terang-Terang, Ujung Bulu',
            'logo' => 'logo.png',
            // 'kode_rekening' => '131-01-006',
            // 'uraian_rekening' => 'BEA PEROLEHAN HAK ATAS TANAH DAN BANGUNAN (BPHTB)',
            'range_tahun' => 4,
            
            // 'kode_wilayah' => '7375',
            // 'pembatas_nomor' => '/'
        ]);
    }
}
