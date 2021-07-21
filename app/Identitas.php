<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Identitas extends Model
{

    protected $table = 'identitas';
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
