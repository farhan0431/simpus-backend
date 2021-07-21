<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class Identitas extends Model
{

    protected $table = 'identitas';
    protected $guarded = [];
    protected $appends = [
        'umur'
    ];
    // public function getKwitansiLinkAttribute()
    // {
    //     if ($this->kwitansi) {
    //         return url('kwitansi/' . $this->kwitansi);
    //     }
    //     return url('belum-ada');
    // }

    public function getUmurAttribute()
    {

        $birthDate = Carbon::parse($this->tanggal_lahir)->format('m/d/Y');
        //explode the date to get month, day and year
        $birthDate = explode("/", $birthDate);
        //get age from date or birthdate
        $age = (date("md", date("U", mktime(0, 0, 0, $birthDate[0], $birthDate[1], $birthDate[2]))) > date("md")
          ? ((date("Y") - $birthDate[2]) - 1)
          : (date("Y") - $birthDate[2]));


        return $age;
    }


}
