<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{

    protected $table = 'settings';
    protected $with = [
        'nama_provinsi',
        'nama_kota'
    ];
    protected $fillable = ['pemerintah'];



    public function nama_provinsi()
    {

        return $this->belongsTo(Province::class,'provinsi');
    }

    public function nama_kota()
    {

        return $this->belongsTo(Regency::class,'kota');
    }

}
