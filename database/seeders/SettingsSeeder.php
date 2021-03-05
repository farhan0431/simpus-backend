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
            'pemerintah' => 'PEMERINTAH KABUPATEN BULUKUMBA',
            'deskripsi_pemerintah' => 'Ayo Bersama Membangun Daerah Dengan Pajak',
            'kantor_badan' => 'BADAN PENDAPATAN DAERAH',
            'inisial_kantor_badan' => 'BAPENDA',
            'kota' => '2',
            'slogan' => 'BAPENDA',
            'provinsi' => '1',
            'alamat' => 'Jl. Jend Sudirman, Terang-Terang, Ujung Bulu',
            'logo' => 'logo.png',
            'inisial' => 'testing',
            // 'kode_rekening' => '131-01-006',
            // 'uraian_rekening' => 'BEA PEROLEHAN HAK ATAS TANAH DAN BANGUNAN (BPHTB)',
            'range_tahun' => 4,
            
            // 'kode_wilayah' => '7375',
            // 'pembatas_nomor' => '/'
        ]);
    }
}
