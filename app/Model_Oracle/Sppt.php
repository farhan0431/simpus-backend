<?php

namespace App\Model_Oracle;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sppt extends Model
{

    protected $connection = 'oracle';
    protected $table = 'PEMBAYARAN_SPPT';
    protected $hidden = [
        'kd_propinsi',
    	'kd_dati2',
    	'kd_kecamatan',
    	'kd_kelurahan',
    	'kd_blok',
    	'no_urut',
    	'kd_jns_op',
    	'kd_kanwil_bank',
    	'kd_kppbb_bank',
    	'kd_bank_tunggal',
    	'kd_bank_persepsi',
    	'kd_tp',
        'siklus_sppt'	,
        'npwp_sppt'	,
        'no_persil_sppt'	,
        'kd_kls_tanah'	,
        'thn_awal_kls_tanah'	,
        'kd_kls_bng'	,
        'thn_awal_kls_bng'	,
        'njop_sppt'	,
        'njoptkp_sppt'	,
        'njkp_sppt'	,
        'pbb_terhutang_sppt',
        'faktor_pengurang_sppt',
        'status_tagihan_sppt',
        'status_cetak_sppt'	,
        'tgl_terbit_sppt'	,
        'tgl_cetak_sppt'	,
        'ketetapan_awal'	,
        'nip_pencetak_sppt'	,
        'nip_rekam_byr_sppt'
    ];

}
