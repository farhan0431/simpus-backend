<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use DB;
use App\Model_Oracle\Sppt;

use App\Model_Bphtb\ObjekPajak;
use App\Model_Bphtb\PembayaranBphtb;
use App\Model_Bphtb\TargetBphtb;

use App\User;
use App\Settings;
use App\TargetPenerimaanSppt;

use App\Model_Simpad\TargetPenerimaan;
use App\Model_Simpad\SptpdReguler;
use App\Model_Simpad\WajibPajak;
use App\Model_Simpad\ObjekPajakSimpad;
use App\Model_Simpad\JenisPajakSimpad;


use Carbon\Carbon;



// use app\Models\Ref_kecamtan;

class RealisasiPendapatan extends Controller
{

    public function index(Request $request)
    {
        $monthNow = $request->month == '' ? Carbon::now()->format('m') : $request->month;
        $yearNow = $request->year == '' ? Carbon::now()->format('Y') : $request->year;
        $lastYear = $yearNow - 1;
        $data = [];
        $dataTahunLalu = [];

        $totalYearNow = 0;
        $totalLastYear = 0;
        $totalMonthNow = 0;
        $totalMonthLastYear = 0;
        $index = 0;

        $realisasiBulanan = [];
        $namaPajak = [];
        $kodeRekening = [];


        $jenisPajak = JenisPajakSimpad::where('level','3')->get();

       
        foreach ($jenisPajak as $key => $value) {
            $nama_pajak = $value->nama_rekening;
            $kode_rekening = $value->kode_rekening;
            $sptpd = 0;
            $sptpdLastYear = 0;
            $sptpdMonth = 0;
            $sptpdLastYearMonth = 0;
            $objekPajak = ObjekPajakSimpad::where('jenis_pajak_id',$value->id)->get();
            if(count($objekPajak) > 0) {
                foreach ($objekPajak as $key_objek => $value_objek) {
                    $sptpd = SptpdReguler::where('status','3')->where('objek_pajak_id',$value_objek->id)->whereYear('tgl_bayar',$yearNow)->get()->sum(function($item) {
                        return $item->pajak_terhutang;
                    });;

                    $sptpdMonth = SptpdReguler::where('status','3')->where('objek_pajak_id',$value_objek->id)->whereYear('tgl_bayar',$yearNow)->whereMonth('tgl_bayar',$monthNow)->get()->sum(function($item) {
                        return $item->pajak_terhutang;
                    });;

                    $sptpdLastYear = SptpdReguler::where('status','3')->where('objek_pajak_id',$value_objek->id)->whereYear('tgl_bayar',$lastYear)->get()->sum(function($item) {
                        return $item->pajak_terhutang;
                    });;

                    $sptpdLastYearMonth = SptpdReguler::where('status','3')->where('objek_pajak_id',$value_objek->id)->whereYear('tgl_bayar',$lastYear)->whereMonth('tgl_bayar',$monthNow)->get()->sum(function($item) {
                        return $item->pajak_terhutang;
                    });;

                    $totalYearNow = $totalYearNow + $sptpd;
                    $totalLastYear = $totalLastYear + $sptpdLastYear;
                    $totalMonthNow = $totalMonthNow + $sptpdMonth;
                    $totalMonthLastYear = $totalMonthLastYear + $sptpdLastYearMonth;
            

                    
                }
            }else{
                $sptpd = 0;
                $sptpdLastYear = 0;
                $sptpdMonth = 0;
                $sptpdLastYearMonth = 0;
            }

            $index++;




            $fullData = [
                'index' => $index,
                'nama_pajak' => $nama_pajak,
                'tahun_ini' => $sptpd,
                'tahun_lalu' => $sptpdLastYear,
                'bulan_ini' => $sptpdMonth,
                'bulan_tahun_lalu' => $sptpdLastYearMonth,
                'kode_rekening' => $kode_rekening
            ];

           

            // $data[$nama_pajak] = $sptpd;

            array_push($namaPajak,$nama_pajak);
            array_push($kodeRekening,$kode_rekening);
            array_push($realisasiBulanan,$sptpdMonth);
            array_push($data,$fullData);
        }

        $grafik = [
            'nama_pajak' => $namaPajak,
            'realisasi' => $realisasiBulanan,
            'kode_rekening' => $kodeRekening
        ];

        
        return response()->json([ 
            'data' => $data,
            'total_tahun_ini' => $totalYearNow, 
            'total_tahun_lalu' => $totalLastYear,
            'total_bulan_ini' => $totalMonthNow,
            'total_bulan_tahun_lalu' => $totalMonthLastYear,

            'bulan' =>  namedMonth($monthNow),
            'bulan_nomor' => $monthNow,
            'tahun_ini' => $yearNow,
            'tahun_lalu' => $lastYear,
            'grafik' => $grafik
        ],200);
    }

    public function per_hari(Request $request)
    {

        
        $monthNow = $request->month == '' ? Carbon::now()->format('m') : $request->month;
        $yearNow = $request->year == '' ? Carbon::now()->format('Y') : $request->year;

        $date = $yearNow.'/'.$monthNow.'/01';

        $days = $request->month == '' ? Carbon::now()->daysInMonth : Carbon::parse($date)->daysInMonth;

        $dataDays = [];
        $realisasi = [];
        $allData = [];
        $totalRealisasi = 0;
        $totalData = 0;

        $sppt = DB::connection('oracle')->table('PEMBAYARAN_SPPT')->selectRaw('coalesce(SUM(jml_sppt_yg_dibayar), 0) as total, extract(DAY from tgl_pembayaran_sppt) as tanggal,COUNT(id) as jumlah_data')
        ->groupByRaw('extract(DAY from tgl_pembayaran_sppt)')
        ->whereBetween('tgl_pembayaran_sppt', [$date,Carbon::parse($yearNow.'/'.$monthNow.'/'.$days)->format('Y-m-d')])->get()->toArray();

        $simpad = DB::connection('mysql_simpad')->table('sptpd_reguler')->selectRaw('coalesce(SUM(pajak_terhutang), 0) as total, DAY(tgl_bayar) as tanggal, COUNT(id) as jumlah_data')->where('status', 3)
        ->groupByRaw('DAY(tgl_bayar)')
        ->whereBetween('tgl_bayar',[$date,Carbon::parse($yearNow.'/'.$monthNow.'/'.$days)->format('Y-m-d')])->get()->toArray();
        
        $bphtb = DB::connection('mysql_bphtb')->table('pembayaran_bphtb')->selectRaw('coalesce(SUM(jumlah_bayar), 0) as total, DAY(tanggal_pembayaran) as tanggal, COUNT(id) as jumlah_data')->where('status', 1)
        ->groupByRaw('DAY(tanggal_pembayaran)')
        ->whereBetween('tanggal_pembayaran', [$date,Carbon::parse($yearNow.'/'.$monthNow.'/'.$days)->format('Y-m-d')])->get()->toArray();

        $allData = [];

        for($i=1; $i <= $days ; $i++) {
            $daysDate = Carbon::parse($yearNow.'/'.$monthNow.'/'.$i)->format('Y-m-d');

            $allData['realisasi'][$i] = 0;
            $allData['count'][$i] = 0;

            $key_sppt = array_search($i, array_column($sppt, 'tanggal'));

            if($key_sppt !== false) {
                $allData['realisasi'][$i]+=$sppt[$key_sppt]->total; 
                $allData['count'][$i]+= $sppt[$key_sppt]->jumlah_data; 
            }

            $key_simpad = array_search($i, array_column($simpad, 'tanggal'));

            if($key_simpad !== false) {
                $allData['realisasi'][$i]+=$simpad[$key_simpad]->total; 
                $allData['count'][$i]+= $simpad[$key_simpad]->jumlah_data; 
            }

            $key_bphtb = array_search($i, array_column($bphtb, 'tanggal'));

            if($key_bphtb !== false) {
                $allData['realisasi'][$i]+=$bphtb[$key_bphtb]->total; 
                $allData['count'][$i]+= $bphtb[$key_bphtb]->jumlah_data; 
            }


            $allData['full_data'][$i] = [
                // 'tanggal' => $i.' '.namedMonth($monthNow).' '.$yearNow,
                'tanggal' => $daysDate,
                'realisasi' => $allData['realisasi'][$i],
                'total' => $allData['count'][$i]
            ];

            

            array_push($dataDays,$i);

        }


        return response()->json([
            'bulan_nomor' => $monthNow,
            'bulan' => namedMonth($monthNow),
            'tahun' => $yearNow,
            'tanggal' => $date,
            'total_hari' => $days,
            'realisasi' => array_values($allData['realisasi']),
            'hari' => $dataDays,
            'full_data' => array_values($allData['full_data']),
            'total_data' => array_sum(array_values($allData['count'])),
            'total_realisasi' => array_sum(array_values($allData['realisasi'])),
            
        ]);

    }

}
