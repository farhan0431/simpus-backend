<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekamMedis extends Model
{

    protected $table = 'rekam_medis';
    protected $guarded = [];
    protected $appends = [
        // 'kwitansi_link'
    ];
    // public function getKwitansiLinkAttribute()
    // {
    //     if ($this->kwitansi) {
    //         return url('kwitansi/' . $this->kwitansi);
    //     }
    //     return url('belum-ada');
    // }


}
