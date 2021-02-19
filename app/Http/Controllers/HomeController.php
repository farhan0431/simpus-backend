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
use Carbon\Carbon;



// use app\Models\Ref_kecamtan;

class HomeController extends Controller
{

    public function year() {
        $yearNow = Carbon::now()->format('Y');
        
        
       
        $range = $yearNow - Settings::first()->range_tahun;
        $years = array_reverse(range(date("Y"), $range),true);


        // ALL DATA CHART
        // SPPT
        $dataTargetSppt = [];
        $dataRealisasiPerTahunSppt = [];
        // SIMPAD
        $dataTargetPertahunSimpad = [];
        $dataRealisasiPertahunSimpad = [];
        // BPHTB
        $dataTargetBphtb = [];
        $dataRealisasiBphtb = [];

        $realisasi_pertahun_total = [];
        $target_pertahun_total = [];
        
        foreach ($years as $year => $value) {
        
            // SPPT
                //Realisasi
            $resultRealisasiSppt = Sppt::whereYear('tgl_pembayaran_sppt',$value)->sum('jml_sppt_yg_dibayar');
            array_push($dataRealisasiPerTahunSppt,$resultRealisasiSppt);
             
                //Target
            $resultTargetSppt = TargetPenerimaanSppt::where('tahun',$value)->get()->sum(function($item) {
                return $item->target;
            });
            array_push($dataTargetSppt,$resultTargetSppt);
           
            // SIMPAD
                //Realisasi
            $resultRealisasiSimpad = TargetPenerimaan::where('tahun',$value)->get()->sum(function($item) {
                return $item->target;
            });
            array_push($dataRealisasiPertahunSimpad,$resultRealisasiSimpad);
            
                //Target
            $resultTargetSimpad = SptpdReguler::where('status',3)->whereYear('tgl_bayar', $value)->get()->sum(function($item) {
                return $item->pajak_terhutang;
            });
            array_push($dataTargetPertahunSimpad,$resultTargetSimpad);
            // BPHTB
                //Realisasi
            $resultRealisasiBphtb = PembayaranBphtb::where('status',1)->whereYear('tanggal_pembayaran',$value)->get()->sum(function($item) {
                return $item->jumlah_bayar;
            });
            array_push($dataRealisasiBphtb,$resultRealisasiBphtb);
            
                //Target
            $resultTargetBphtb = TargetBphtb::where('tahun',$value)->get()->sum(function($item) {
                return $item->target;
            });
            array_push($dataTargetBphtb,$resultTargetBphtb);


            array_push($realisasi_pertahun_total,$resultRealisasiSppt+$resultRealisasiSimpad+$resultRealisasiBphtb);
            array_push($target_pertahun_total,$resultTargetSppt+$resultTargetSimpad+$resultTargetBphtb);

        }

        // END ALL DATA CHART

        // SPPT
        // Data Realisasi Tahunan
        
        $realisasiSpptTahun = Sppt::whereYear('tgl_pembayaran_sppt',Carbon::now()->year)->sum('jml_sppt_yg_dibayar');
        // Data Target Tahunan
        
        $targetSpptTahun = TargetPenerimaanSppt::where('tahun',$yearNow)->get()->sum(function($item) {
            return $item->target;
        });
        
        // END SPPT

        // SIMPAD
        // Data Total Target Tahun Ini
        $targetSimpadTahun = TargetPenerimaan::where('tahun', $yearNow)->get()->sum(function($item) {
            return $item->target;
        });

        // Data Target Pertahun


        // Data Total Realisasi Tahun Ini
        $realisasiSimpad = SptpdReguler::where('status',3)->whereYear('tgl_bayar', $yearNow)->get()->sum(function($item) {
            return $item->pajak_terhutang;
        });
        // END SIMPAD

        // BHPTB

        // Data Realisasi
        $realisasiBphtb = PembayaranBphtb::where('status',1)->whereYear('tanggal_pembayaran',$yearNow)->get()->sum(function($item) {
            return $item->jumlah_bayar;
        });

        // Data Target
        $targetBphtpTahun = TargetBphtb::where('tahun',$yearNow)->get()->sum(function($item) {
            return $item->target;
        });

        // END BHPTB

       

        return response()->json(
            [
                // SPPT
                'realisasi_pertahun_sppt'=> $dataRealisasiPerTahunSppt,
                'realisasi_sppt' => "$realisasiSpptTahun",
                'target_pertahun_sppt' => $dataTargetSppt,
                'target_sppt' => "$targetSpptTahun",
                //SIMPAD
                'realisasi_pertahun_simpad' => $dataRealisasiPertahunSimpad,
                'realisasi_simpad' => "$realisasiSimpad",
                'target_pertahun_simpad' => $dataTargetPertahunSimpad,
                'target_simpad' => "$targetSimpadTahun",
                // BPHTB
                'realisasi_pertahun_bphtb' => $dataRealisasiBphtb,
                'realisasi_bphtb' => "$realisasiBphtb",
                'target_pertahun_bphtb' => $dataTargetBphtb,
                'target_bphtb' => "$targetBphtpTahun",
                //TOTAL DATA
                'total_realisasi' => strval($realisasiSimpad+$realisasiSpptTahun+$realisasiBphtb),
                'total_target' => strval($targetSpptTahun+$targetSimpadTahun+$targetBphtpTahun),
                'realisasi_pertahun_total' => $realisasi_pertahun_total,
                'target_pertahun_total' => $target_pertahun_total
             ]);
    }

    public function month() {
        
        $penerimaan = [];
        $type = [];
        try {
            DB::connection('oracle')->getPdo();
            
            $results = Sppt::whereYear('tgl_pembayaran_sppt','2010')->get()->groupBy(function($val) {
                return Carbon::parse($val->tgl_pembayaran_sppt)->format('m');
            });
            $data = [
                '01' => 0,
                '02' => 0,
                '03' => 0,
                '04' => 0,
                '05' => 0,
                '06' => 0,
                '07' => 0,
                '08' => 0,
                '09' => 0,
                '10' => 0,
                '11' => 0,
                '12' => 0
            ];
    
            
            foreach ($data as $key => $value) {
                
                if(isset($results[$key])){
                    $sum = 0;
                    foreach ($results[$key] as $keyRes => $valueRes) {
                        $sum += $valueRes['jml_sppt_yg_dibayar'];
                    }
                    $data[$key] = $sum;
                    array_push($penerimaan,$sum);
                }else{
                    array_push($penerimaan,'0');
                }
                
            }
            
            $type = 'real';
        } catch (\Exception $e) {
            $penerimaan = ['12','12','12','12','12','123','123','234','234','234','123','234'];
            $type = 'dummy';
        }
        


        return response()->json(['bulan' => $penerimaan,'type'=>$type]);
    }

    public function getMoreData(Request $request)
    {

        $yearNow = Carbon::now()->format('Y');
        $range = $yearNow - Settings::first()->range_tahun;
        $years =array_reverse(range(date("Y"), $range),true);

        $months = [
            '01' => 0,
            '02' => 0,
            '03' => 0,
            '04' => 0,
            '05' => 0,
            '06' => 0,
            '07' => 0,
            '08' => 0,
            '09' => 0,
            '10' => 0,
            '11' => 0,
            '12' => 0
        ];


        // SIMPAD

        $targetSimpad = TargetPenerimaan::where('tahun', $yearNow)->orderBy('bulan','ASC');


        // Data Total Target Tahun
        $targetSimpadTahun = $targetSimpad->get()->sum(function($item) {
            return $item->target;
        });

        // Data Target Pertahun



        // Data Target Perbulan
        $targetSimpadBulan = $targetSimpad->get();
        $dataTargetSimpadBulan = [];
        foreach($months as $key => $month) {
            $index = ltrim($key, '0');
            if(isset($targetSimpadBulan[($index -= 1)]))
            {
                array_push($dataTargetSimpadBulan,$targetSimpadBulan[$index]['target']);
            }else{
                array_push($dataTargetSimpadBulan,0);
            }
        }
        // END SIMPAD

        // BPHTB
        $objekPajakBphtb = ObjekPajak::count();
        // END BPHTP

        return response()->json(['total_wp_bphtp' => "$objekPajakBphtb",'total_target_simpad_tahun' => "$targetSimpadTahun",'target_simpad_perbulan' => $dataTargetSimpadBulan, 'years' => $years]);


        
    }
}
