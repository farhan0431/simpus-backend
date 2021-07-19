<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{

    protected $table = 'laporan';
    protected $appends = [
        'tipe_identitas',
        'status_teks',
        'jenis_kecelakaan_teks',
        'gambar_link',
        'kwitansi_link',
        'perincian_link',
        'polisi_link',
        'garansi_link'
    ];
    protected $fillable = ['status','status_laporan','nama','jenis_identitas','no_identitas','kondisi_korban','id_pembuat','lat','lng','foto','jenis_kecelakaan','kwitansi','perincian','surat_polisi','garansi'];

    public function getKwitansiLinkAttribute()
    {
        if ($this->kwitansi) {
            return url('kwitansi/' . $this->kwitansi);
        }
        return url('belum-ada');
    }

    public function getPerincianLinkAttribute()
    {
        if ($this->perincian) {
            return url('perincian/' . $this->perincian);
        }
        return url('belum-ada');
    }

    public function getPolisiLinkAttribute()
    {
        if ($this->perincian) {
            return url('polisi/' . $this->surat_polisi);
        }
        return url('belum-ada');
    }

    public function getGaransiLinkAttribute()
    {
        if ($this->perincian) {
            return url('garansi/' . $this->garansi);
        }
        return url('belum-ada');
    }


    public function getGambarLinkAttribute()
    {

        return url('uploads/' . $this->foto);
    }

    public function getTipeIdentitasAttribute()
    {
        $jenis = [
            '0' => 'KTP',
            '1' => 'SIM',
            '2' => 'Tidak Ada'      
        ];

        return $jenis[$this->jenis_identitas];
    }

    public function getStatusTeksAttribute()
    {

        $data = [
            '0' => 'Laporan Terkirim',
            '1' => 'Laporan Diterima',
            '2' => 'Ambulance Menuju TKP',
            '3' => 'Pasien Dibawa Kerumah Sakit',
            '4' => 'Pasien Ditangani',
            '5' => 'Selesai'
        ];

        return $data[$this->status];

    }

    public function getJenisKecelakaanTeksAttribute()
    {

        $data = [
            '0' => 'Kecelakaan Tunggal',
            '1' => 'Kecelakaan Ganda',
        ];

        return $data[$this->jenis_kecelakaan];

    }

}
