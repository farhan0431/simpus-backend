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


        $jenisPajak = JenisPajakSimpad::where('level','3')->get();

       
        foreach ($jenisPajak as $key => $value) {
            $nama_pajak = $value->nama_rekening;
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
                'bulan_tahun_lalu' => $sptpdLastYearMonth
            ];

           

            // $data[$nama_pajak] = $sptpd;

            array_push($data,$fullData);
        }

        
        return response()->json([ 
            'data' => $data,
            'total_tahun_ini' => $totalYearNow, 
            'total_tahun_lalu' => $totalLastYear,
            'total_bulan_ini' => $totalMonthNow,
            'total_bulan_tahun_lalu' => $totalMonthLastYear,

            'bulan' =>  namedMonth($monthNow),
            'tahun_ini' => $yearNow,
            'tahun_lalu' => $lastYear
        ],200);
    }

}
