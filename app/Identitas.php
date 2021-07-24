<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Dokumen;
use App\PemeriksaanUmum;
use App\RekamMedis;


class Identitas extends Model
{

    protected $table = 'identitas';
    protected $guarded = [];
    protected $appends = [
        'umur',
        'dokumen_link',
        'riwayat_pemeriksaan',
        'rekam_medis'
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

    public function getDokumenLinkAttribute()
    {
        $dokumen = Dokumen::where('no_rm',$this->no_rm)->orderBy('created_at','desc')->first();

        if($dokumen != null)
        {
            return url('dokumen/'.$dokumen['dokumen']);
        }
        return null;
    }

    public function getRiwayatPemeriksaanAttribute()
    {
        return PemeriksaanUmum::where('no_rm',$this->no_rm)->orderBy('created_at','asc')->get();
    }

    public function getRekamMedisAttribute()
    {
        return RekamMedis::where('no_rm',$this->no_rm)->orderBy('created_at','desc')->first();
    }


}
