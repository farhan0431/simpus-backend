<?php

namespace App\Model_Simpad;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TargetPenerimaan extends Model
{

    protected $connection = 'mysql_simpad';
    protected $guarded = [];
    protected $table = 'target_penerimaan';
    protected $appends = ['bulan_label', 'target_format'];

    public function getBulanLabelAttribute()
    {
        return namedMonth($this->bulan);
        // $month = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        // return $month[(int)$this->bulan - 1];
    }
    
    public function jenis_pajak()
    {
        return $this->belongsTo(Rekening::class, 'jenis_pajak_id');
    }

    public function getTargetFormatAttribute()
    {
        return number_format($this->target);
    }
}