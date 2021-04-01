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

        set_time_limit(0);

        // $request->month == '' ? Carbon::now()->format('m') : $request->month;

        $monthNow = $request->month == '' ? Carbon::now()->format('m') : $request->month;
        $yearNow = $request->year == '' ? Carbon::now()->format('Y') : $request->year;

        $date = $yearNow.'/'.$monthNow.'/01';

        $days = $request->month == '' ? Carbon::now()->daysInMonth : Carbon::parse($date)->daysInMonth;

        $dataDays = [];
        $realisasi = [];
        $allData = [];
        $totalRealisasi = 0;
        $totalData = 0;


        // $countSpptQuery = Sppt::whereBetween('tgl_pembayaran_sppt',[$date,Carbon::parse($yearNow.'/'.$monthNow.'/'.$days)->format('Y-m-d')])->count();

        // $spptQuery = Sppt::limit(100)->whereBetween('tgl_pembayaran_sppt',[$date,Carbon::parse($yearNow.'/'.$monthNow.'/'.$days)->format('Y-m-d')])->chunk(100, function($items) {
        //     $dataQuery = [];
        //     foreach ($items as $item) {
        //         array_push($dataQuery,$item->get());
        //     }
        //     return $dataQuery;
        // });


        for ($i=1; $i <= $days ; $i++) { 
            $daysDate = Carbon::parse($yearNow.'/'.$monthNow.'/'.$i)->format('Y-m-d');



            // SIMPAD
            $sptpd = SptpdReguler::where('status','3')->whereDate('tgl_bayar',$daysDate)->get()->sum(function($item) {
                return $item->pajak_terhutang;
            });
            $count = SptpdReguler::where('status','3')->whereDate('tgl_bayar',$daysDate)->count();

            
            // BPHTB
            $bphtb = PembayaranBphtb::where('status','1')->whereDate('tanggal_pembayaran',$daysDate)->get()->sum(function($item) {
                return $item->jumlah_bayar;
            });
            $countBphtb = PembayaranBphtb::where('status','1')->whereDate('tanggal_pembayaran',$daysDate)->count();

            // SPPT

            // $sppt = Sppt::chunk(100, function($items) {
                
            //     foreach($items as $item) {
            //         $item::whereYear('tgl_pembayaran_sppt','2019')->get()->sum(function($value) {
            //             return $value->jml_sppt_yg_dibayar;
            //         });
            //     }
            // });


            // $sppt = Sppt::whereDate('tgl_pembayaran_sppt',$daysDate)->limit(500)->chunk(100, function($items) {
            //     $spptPay = 0;
            //     foreach($items as $item) {
            //         $item::get()->sum(function($item) {
            //             $spptPay + $item->jml_sppt_yg_dibayar;
            //         });
            //     }

            //     return $spptPay;
            // });
           
            $daysQuerySppt = Sppt::whereDate('tgl_pembayaran_sppt',$daysDate)->limit(100);
            
            // $Query = $daysQuerySppt->count();
            $countSppt = 100;
            $sppt = 0;

            $sppt = $daysQuerySppt->get()->sum(function($value) {
                $value->jml_sppt_yg_dibayar;
            });

            // if($Query > 100)
            // {
            //     $sppt = $daysQuerySppt->chunk(100, function($items) {
            //         $totalPembayaran = 0;
            //         foreach($items as $item)
            //         {
            //             $item::get()->sum(function($value) {
            //                 $totalPembayaran += $value->jml_sppt_yg_dibayar;   
            //             });
            //         }
            //         return $totalPembayaran;
            //     });
            // }else{
                // $sppt = $daysQuerySppt->get()->sum(function($value) {
                //     $value->jml_sppt_yg_dibayar;
                // });
            // }
            
            // $Query = 0;

            // $querySppt = Sppt::whereDate('tgl_pembayaran_sppt',Carbon::parse('2020-07-22')->format('Y-m-d'))->chunk(100, function($items,$Query) {
            //     foreach($items as $item) 
            //     {
            //         $Query + $item::count();
            //     }

            //     return $countSppt;
            // });

            $fullData = [
                // 'tanggal' => $i.' '.namedMonth($monthNow).' '.$yearNow,
                'tanggal' => $daysDate,
                'realisasi' => $sptpd + $bphtb + $sppt,
                'total' => $count + $countBphtb + $countSppt
            ];

            $totalData = $totalData + $count + $countBphtb + $countSppt;
            $totalRealisasi = $totalRealisasi + $sptpd + $bphtb + $sppt;

            array_push($allData,$fullData);
            array_push($realisasi,$sptpd + $bphtb + $sppt);
            array_push($dataDays,$i);

           
        }


        return response()->json([
            'bulan' => namedMonth($monthNow),
            'bulan_nomor' => $monthNow,
            'tahun' => $yearNow,
            'tanggal' => $date,
            'total_hari' => $days,
            'realisasi' => $realisasi,
            'hari' => $dataDays,
            'full_data' => $allData,
            'total_data' => $totalData,
            'total_realisasi' => $totalRealisasi,
            'memory' => memory_get_usage()
            
        ]);

    }

}
