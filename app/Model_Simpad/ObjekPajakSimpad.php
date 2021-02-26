<?php

namespace App\Model_Simpad;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ObjekPajakSimpad extends Model
{
    protected $connection = 'mysql_simpad';
    protected $table = 'objek_pajak';
    protected $guarded = [];

    public function wajib_pajak() 
    {
        return $this->belongsTo(WajibPajak::class, 'wajib_pajak_id','id');
    }


    public function jenis_pajak() 
    {
        return $this->belongsTo(WajibPajak::class, 'jenis_pajak_id','id');
    }


}