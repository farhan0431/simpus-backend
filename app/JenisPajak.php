<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisPajak extends Model
{

    protected $table = 'jenis_pajak';
    protected $fillable = ['nama_pajak'];

    

}
