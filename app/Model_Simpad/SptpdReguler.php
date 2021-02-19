<?php

namespace App\Model_Simpad;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SptpdReguler extends Model
{
    protected $connection = 'mysql_simpad';
    protected $table = 'sptpd_reguler';
    protected $guarded = [];
    protected $appends = [
        'nomor_sspd',
        'nomor_skpd',
        'tgl_bayar_format',
        'tanggal_terima_format',
        'masa_pajak_awal_format', 
        'masa_pajak_akhir_format', 
        'masa_pajak_month', 
        'jatuh_tempo_format', 
        'dasar_pengenaan_format', 
        'pajak_terhutang_format', 
        'status_label',
        'denda_calc'
    ];
}