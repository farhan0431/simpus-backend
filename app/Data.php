<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Data extends Model
{

    protected $table = 'data';
    protected $fillable = ['id_laporan','jenis_identitas','no_identitas','nama','jenis_kecelakaan','kondisi_korban'];

    

}
