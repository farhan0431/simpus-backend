<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TargetPenerimaanSimpad extends Model
{

    protected $table = 'target_penerimaan_simpad';
    protected $guarded = [];
    protected $appends = [
        'nama_bulan'
    ];

    public function getNamaBulanAttribute()
    {
        $bulan = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember'        
        ];

        return $bulan[$this->bulan];
    }

}
