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

use Carbon\Carbon;



// use app\Models\Ref_kecamtan;

class HomeController extends Controller
{

    public function load_data(Request $request) {
        $year = $this->new_year();
        $month = $this->new_month();
        $moreData = $this->getMoreData();
        $perHari = $this->per_hari($request);

        return response()->json([ 'year' => $year,'month' => $month,'more_data' => $moreData,'per_hari' => $perHari]);
    }

    public function new_year() {
        $yearNow = Carbon::now();
        $yearRange = $yearNow->format('Y') - Settings::first()->range_tahun;
        $yearFirst = Carbon::parse('1/1/'.$yearRange)->startOfYear()->format('Y-m-d');
        $yearLast = $yearNow->endOfYear()->format('Y-m-d');

        $arrayYears = $years = array_reverse(range(Carbon::now()->format('Y'), $yearRange),true);

        $sppt = DB::connection('oracle')->table('PEMBAYARAN_SPPT')->selectRaw('coalesce(SUM(jml_sppt_yg_dibayar), 0) as total, extract(YEAR from tgl_pembayaran_sppt) as tahun')
        ->groupByRaw('extract(YEAR from tgl_pembayaran_sppt)')
        ->whereBetween('tgl_pembayaran_sppt', [$yearFirst,$yearLast])->get()->toArray();

        $simpad = DB::connection('mysql_simpad')->table('sptpd_reguler')->selectRaw('coalesce(SUM(pajak_terhutang), 0) as total, YEAR(tgl_bayar) as tahun')->where('status', 3)
        ->groupByRaw('YEAR(tgl_bayar)')
        ->whereBetween('tgl_bayar', [$yearFirst,$yearLast])->get()->toArray();

        $simpadTarget = TargetPenerimaan::selectRaw('coalesce(SUM(target), 0) as total ,tahun')->whereBetween('tahun', [$yearFirst,$yearLast])->groupBy('tahun')->get()->toArray();

        $bphtb = DB::connection('mysql_bphtb')->table('pembayaran_bphtb')->selectRaw('coalesce(SUM(jumlah_bayar), 0) as total, YEAR(tanggal_pembayaran) as tahun')->where('status', 1)
        ->groupByRaw('YEAR(tanggal_pembayaran)')
        ->whereBetween('tanggal_pembayaran', [$yearFirst,$yearLast])->get()->toArray();

        $bphtbTarget = TargetBphtb::selectRaw('coalesce(SUM(target), 0) as total, tahun')->whereBetween('tahun', [$yearFirst,$yearLast])->groupBy('tahun')->get()->toArray();


        $allData = [];

        $totalSppt = 0;
        $totalSimpad = 0;
        $totalBphtb = 0;
        $totalRealisasi = 0;

        $totalSpptTarget = 0;
        $totalSimpadTarget = 0;
        $totalBphtbTarget = 0;
        $totalTarget = 0;

        $allData['total_penerimaan'] = 0;
        $allData['total_target'] = 0;



        foreach ($arrayYears as $value) {
            
            $allData['realisasi_pertahun'][$value] = 0;
            $allData['target_pertahun'][$value] = 0;

            //REALISASI
            $key_sppt = array_search($value, array_column($sppt, 'tahun'));

            if($key_sppt !== false) {
                $allData['penerimaan_sppt'][$value] = $sppt[$key_sppt]->total;
                $totalSppt+= $sppt[$key_sppt]->total;
                $allData['total_penerimaan']+= $sppt[$key_sppt]->total;
                $allData['realisasi_pertahun'][$value]+=$sppt[$key_sppt]->total;
                
                
            }else{
                $allData['penerimaan_sppt'][$value] = "0";
            }

            $key_bphtb = array_search($value, array_column($bphtb, 'tahun'));

            if($key_bphtb !== false) {
                $allData['penerimaan_bphtb'][$value] = $bphtb[$key_bphtb]->total;
                $totalBphtb+= $bphtb[$key_bphtb]->total;
                $allData['total_penerimaan']+= $bphtb[$key_bphtb]->total;
                $allData['realisasi_pertahun'][$value]+=$bphtb[$key_bphtb]->total;
            }else{
                $allData['penerimaan_bphtb'][$value] = "0";
            }

            $key_simpad = array_search($value, array_column($simpad, 'tahun'));

            if($key_simpad !== false) {
                $allData['penerimaan_simpad'][$value] = $simpad[$key_simpad]->total;
                $totalSimpad+= $simpad[$key_simpad]->total;
                $allData['total_penerimaan']+= $simpad[$key_simpad]->total;
                $allData['realisasi_pertahun'][$value]+=$simpad[$key_simpad]->total;
            }else{
                $allData['penerimaan_simpad'][$value] = "0";
            }

            //TARGET

            $key_simpad_target = array_search($value, array_column($simpadTarget, 'tahun'));

            if($key_simpad_target !== false) {
                $allData['penerimaan_simpad_target'][$value] = $simpadTarget[$key_simpad_target]['total'];
                $totalSimpadTarget+= $simpadTarget[$key_simpad_target]['total'];
                $allData['total_target'] += $simpadTarget[$key_simpad_target]['total'];
                $allData['target_pertahun'][$value] += $simpadTarget[$key_simpad_target]['total'];
            }else{
                $allData['penerimaan_simpad_target'][$value] = "0";
            }

            $key_bphtb_target = array_search($value, array_column($bphtbTarget, 'tahun'));

            if($key_bphtb_target !== false) {
                $allData['penerimaan_bphtb_target'][$value] = $bphtbTarget[$key_bphtb_target]['total'];
                $totalBphtbTarget+= $bphtbTarget[$key_bphtb_target]['total'];
                $allData['total_target'] += $bphtbTarget[$key_bphtb_target]['total'];
                $allData['target_pertahun'][$value] += $bphtbTarget[$key_bphtb_target]['total'];
            }else{
                $allData['penerimaan_bphtb_target'][$value] = "0";
            }
        }


        $returnData = [
             // SPPT
             'realisasi_pertahun_sppt'=> array_values($allData['penerimaan_sppt']),
             'realisasi_sppt' => "$totalSppt",
             'target_pertahun_sppt' => [],
             'target_sppt' => "$totalSpptTarget",
             //SIMPAD
             'realisasi_pertahun_simpad' => array_values($allData['penerimaan_simpad']),
             'realisasi_simpad' => "$totalSimpad",
             'target_pertahun_simpad' => array_values($allData['penerimaan_simpad_target']),
             'target_simpad' => "$totalSimpadTarget",
             // BPHTB
             'realisasi_pertahun_bphtb' => array_values($allData['penerimaan_bphtb']),
             'realisasi_bphtb' => "$totalBphtb",
             'target_pertahun_bphtb' => array_values($allData['penerimaan_bphtb_target']),
             'target_bphtb' => "$totalBphtbTarget",
             //TOTAL DATA
             'total_realisasi' => $allData['total_penerimaan'],
             'total_target' => $allData['total_target'],
             'realisasi_pertahun_total' => array_values($allData['realisasi_pertahun']),
             'target_pertahun_total' => array_values($allData['target_pertahun'])
        ];

        return $returnData;

        
        // return response()->json([
        //     // SPPT
        //     'realisasi_pertahun_sppt'=> array_values($allData['penerimaan_sppt']),
        //     'realisasi_sppt' => "$totalSppt",
        //     'target_pertahun_sppt' => [],
        //     'target_sppt' => "$totalSpptTarget",
        //     //SIMPAD
        //     'realisasi_pertahun_simpad' => array_values($allData['penerimaan_simpad']),
        //     'realisasi_simpad' => "$totalSimpad",
        //     'target_pertahun_simpad' => array_values($allData['penerimaan_simpad_target']),
        //     'target_simpad' => "$totalSimpadTarget",
        //     // BPHTB
        //     'realisasi_pertahun_bphtb' => array_values($allData['penerimaan_bphtb']),
        //     'realisasi_bphtb' => "$totalBphtb",
        //     'target_pertahun_bphtb' => array_values($allData['penerimaan_bphtb_target']),
        //     'target_bphtb' => "$totalBphtbTarget",
        //     //TOTAL DATA
        //     'total_realisasi' => $allData['total_penerimaan'],
        //     'total_target' => $allData['total_target'],
        //     'realisasi_pertahun_total' => array_values($allData['realisasi_pertahun']),
        //     'target_pertahun_total' => array_values($allData['target_pertahun'])

        // ]);
    }

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

    public function new_month() {
        $yearNow = Carbon::now()->format('Y');

        $data = [
            ['bulan' => 'Jan', 'target' => 0, 'penerimaan' => 0],
            ['bulan' => 'Feb', 'target' => 0, 'penerimaan' => 0],
            ['bulan' => 'Mar', 'target' => 0, 'penerimaan' => 0],
            ['bulan' => 'Apr', 'target' => 0, 'penerimaan' => 0],
            ['bulan' => 'Mei', 'target' => 0, 'penerimaan' => 0],
            ['bulan' => 'Jun', 'target' => 0, 'penerimaan' => 0],
            ['bulan' => 'Jul', 'target' => 0, 'penerimaan' => 0],
            ['bulan' => 'Agu', 'target' => 0, 'penerimaan' => 0],
            ['bulan' => 'Sep', 'target' => 0, 'penerimaan' => 0],
            ['bulan' => 'Okt', 'target' => 0, 'penerimaan' => 0],
            ['bulan' => 'Nov', 'target' => 0, 'penerimaan' => 0],
            ['bulan' => 'Des', 'target' => 0, 'penerimaan' => 0],
        ];

        //sppt

        $sppt = DB::connection('oracle')->table('PEMBAYARAN_SPPT')->selectRaw('coalesce(SUM(jml_sppt_yg_dibayar), 0) as total, extract(MONTH from tgl_pembayaran_sppt) as bulan')
        ->groupByRaw('extract(MONTH from tgl_pembayaran_sppt)')
        ->whereYear('tgl_pembayaran_sppt', $yearNow)->get()->toArray();
        $simpad = DB::connection('mysql_simpad')->table('sptpd_reguler')->selectRaw('coalesce(SUM(pajak_terhutang), 0) as total, MONTH(tgl_bayar) as bulan')->where('status', 3)
        ->groupByRaw('MONTH(tgl_bayar)')
        ->whereYear('tgl_bayar', $yearNow)->get()->toArray();

        $simpadTarget = TargetPenerimaan::selectRaw('coalesce(SUM(target), 0) as total, bulan, tahun')->where('tahun', $yearNow)->groupBy('bulan', 'tahun')->get()->toArray();
        

        $bphtb = DB::connection('mysql_bphtb')->table('pembayaran_bphtb')->selectRaw('coalesce(SUM(jumlah_bayar), 0) as total, MONTH(tanggal_pembayaran) as bulan')->where('status', 1)
        ->groupByRaw('MONTH(tanggal_pembayaran)')
        ->whereYear('tanggal_pembayaran', $yearNow)->get()->toArray();

        $bphtbTarget = TargetBphtb::selectRaw('coalesce(SUM(target), 0) as total, bulan, tahun')->where('tahun', $yearNow)->groupBy('bulan', 'tahun')->get()->toArray();


        $totalSppt = 0;
        $totalSimpad = 0;
        $totalBphtb = 0;
        $totalRealisasi = 0;

        $totalSpptTarget = 0;
        $totalSimpadTarget = 0;
        $totalBphtbTarget = 0;
        $totalTarget = 0;

        $allData = [];

        for ($i=1; $i <= 12 ; $i++) { 

            // REALISASI

            $spptPerbulan = 0;
            $simpadPerbulan = 0;
            $bphtbPerbulan = 0;

            
        

            //SPPT
            $key_sppt = array_search($i, array_column($sppt, 'bulan'));
            if($key_sppt !== false){
                
                $allData['penerimaan_sppt'][$i] = $sppt[$key_sppt]->total;
                $totalSppt += $sppt[$key_sppt]->total;
                $spptPerbulan = $sppt[$key_sppt]->total;
            }else{
                $allData['penerimaan_sppt'][$i] = "0";
            }

            // if(isset($sppt[0]) && $i == 1){

            //     $allData['penerimaan_sppt'][1] = $sppt[0]->total;
            //     $spptPerbulan = $sppt[0]->total;
            //     $totalSppt += $sppt[0]->total;
            // }
            // if($key_sppt == 0) {
                
                // $allData['penerimaan_sppt'][1] = $sppt[0]->total;
                // $spptPerbulan = $sppt[0]->total;
                // $totalSppt += $sppt[0]->total;
            // }

            //SIMPAD
            $key_simpad = array_search($i, array_column($simpad, 'bulan'));
            if($key_simpad !== false) {
                $allData['penerimaan_simpad'][$i] = $simpad[$key_simpad]->total;
                $totalSimpad+=$simpad[$key_simpad]->total;
                $simpadPerbulan = $simpad[$key_simpad]->total;
            }else{
                $allData['penerimaan_simpad'][$i] = "0";
            }
            // if(isset($simpad[0]) && $i == 1) {
            //     $allData['penerimaan_simpad'][1] = $simpad[0]->total;
            //     $totalSimpad+=$simpad[0]->total;
            //     $simpadPerbulan = $simpad[0]->total;
            // }

            //BPHTB
            $key_bphtb = array_search($i, array_column($bphtb, 'bulan'));
            if($key_bphtb !== false) {
                $allData['penerimaan_bphtb'][$i] = $bphtb[$key_bphtb]->total;
                $totalBphtb+=$bphtb[$key_bphtb]->total;
                $bphtbPerbulan= $bphtb[$key_bphtb]->total;
            }else{
                $allData['penerimaan_bphtb'][$i] = "0";
            }
            // if(isset($bphtb[0]) && $i == 1) {
            //     $allData['penerimaan_bphtb'][1] = $bphtb[0]->total;
            //     $totalBphtb+=$bphtb[0]->total;
            //     $bphtbPerbulan = $bphtb[0]->total;
            // }


            $allData['total_penerimaan_perbulan'][$i] = $spptPerbulan+$simpadPerbulan+$bphtbPerbulan;
            // $allData['total_penerimaan'] = 

            $totalRealisasi = $totalRealisasi +$spptPerbulan+$simpadPerbulan+$bphtbPerbulan;

            // TARGET

            $spptPerbulanTarget = 0;
            $simpadPerbulanTarget = 0;
            $bphtbPerbulanTarget = 0;

            $num_padded = sprintf("%02d", $i);

            $key_simpad_target = array_search($num_padded, array_column($simpadTarget, 'bulan'));
            if($key_simpad_target !== false) {
                $allData['target_simpad'][$i] = $simpadTarget[$key_simpad_target]['total'];
                $totalSimpadTarget+=$simpadTarget[$key_simpad_target]['total'];
                $simpadPerbulanTarget= $simpadTarget[$key_simpad_target]['total'];
            }else{
                $allData['target_simpad'][$i] = "0";
            }
            // if(isset($simpadTarget[0]) && $i == 1) {
            //     $allData['target_simpad'][1] = $simpadTarget[0]['total'];
            //     $totalSimpadTarget+=$simpadTarget[0]['total'];
            //     $simpadPerbulanTarget = $simpadTarget[0]['total'];
            // }

            $key_bhptb_target = array_search($num_padded, array_column($bphtbTarget, 'bulan'));
            if($key_bhptb_target !== false) {
                $allData['target_bphtb'][$i] = $bphtbTarget[$key_bhptb_target]['total'];
                $totalBphtbTarget+=$bphtbTarget[$key_bhptb_target]['total'];
                $simpadPerbulanTarget= $bphtbTarget[$key_bhptb_target]['total'];
            }else{
                $allData['target_bphtb'][$i] = "0";
            }
            // if(isset($bphtbTarget[0]) && $i == 1) {
            //     $allData['target_bphtb'][1] = $bphtbTarget[0]['total'];
            //     $totalBphtbTarget+=$bphtbTarget[0]['total'];
            //     $simpadPerbulanTarget = $bphtbTarget[0]['total'];
            // }


            $allData['total_target_perbulan'][$i] = $spptPerbulanTarget+$simpadPerbulanTarget+$bphtbPerbulanTarget;

            $totalTarget = $totalTarget+$spptPerbulanTarget+$simpadPerbulanTarget+$bphtbPerbulanTarget;


        }
        

        // $collect = collect($data);

        $returnData = [
            //SPPT
            'realisasi_perbulan_sppt' => array_values($allData['penerimaan_sppt']),
            'total_realisasi_sppt' => $totalSppt,
            'target_perbulan_sppt' => [],
            'total_target_sppt' => $totalSpptTarget,
            //SIMPAD
            'realisasi_perbulan_simpad' => array_values($allData['penerimaan_simpad']),
            'total_realisasi_simpad' => $totalSimpad,
            'target_perbulan_simpad' => array_values($allData['target_simpad']),
            'total_target_simpad' => $totalSimpadTarget,
            //BPHP
            'realisasi_perbulan_bphtb' => array_values($allData['penerimaan_bphtb']),
            'total_realisasi_bphtb' => $totalBphtb,
            'target_perbulan_bphtb' => array_values($allData['target_bphtb']),
            'total_target_bphtb' => $totalBphtbTarget,
            //TOTAL
            'data_realisasi_perbulan' => array_values($allData['total_penerimaan_perbulan']),
            'total_realisasi_perbulan' => $totalRealisasi,
            'data_target_perbulan' => array_values($allData['total_target_perbulan']),
            'total_target_perbulan' => $totalTarget,
        ];

        return $returnData;

        // return response()->json([
        //     //SPPT
        //     'realisasi_perbulan_sppt' => array_values($allData['penerimaan_sppt']),
        //     'total_realisasi_sppt' => $totalSppt,
        //     'target_perbulan_sppt' => [],
        //     'total_target_sppt' => $totalSpptTarget,
        //     //SIMPAD
        //     'realisasi_perbulan_simpad' => array_values($allData['penerimaan_simpad']),
        //     'total_realisasi_simpad' => $totalSimpad,
        //     'target_perbulan_simpad' => array_values($allData['target_simpad']),
        //     'total_target_simpad' => $totalSimpadTarget,
        //     //BPHP
        //     'realisasi_perbulan_bphtb' => array_values($allData['penerimaan_bphtb']),
        //     'total_realisasi_bphtb' => $totalBphtb,
        //     'target_perbulan_bphtb' => array_values($allData['target_bphtb']),
        //     'total_target_bphtb' => $totalBphtbTarget,
        //     //TOTAL
        //     'data_realisasi_perbulan' => array_values($allData['total_penerimaan_perbulan']),
        //     'total_realisasi_perbulan' => $totalRealisasi,
        //     'data_target_perbulan' => array_values($allData['total_target_perbulan']),
        //     'total_target_perbulan' => $totalTarget,
            
        // ]);


    }

    public function month() {

        $yearNow = Carbon::now()->format('Y');

        $months = ['01' => 0,'02' => 0,'03' => 0,'04' => 0,'05' => 0,'06' => 0,'07' => 0,'08' => 0,'09' => 0,'10' => 0,'11' => 0,'12' => 0
        ];

        // ALL DATA CHART
        // SPPT
        $dataTargetPerBulanSppt = [];
        $totalTargetSppt = 0;
        $dataRealisasiPerBulanSppt = [];
        $totalRealisasiSppt = 0;
        $spptRealisasi = Sppt::whereYear('tgl_pembayaran_sppt','2010')->get()->groupBy(function($val) {
            return Carbon::parse($val->tgl_pembayaran_sppt)->format('m');
        });
        $spptTarget =  TargetPenerimaanSppt::where('tahun',$yearNow)->orderBy('bulan')->get();
        // SIMPAD
        $dataTargetPerBulanSimpad = [];
        $totalTargetSimpad = 0;
        $dataRealisasiPerBulanSimpad = [];
        $totalRealisasiSimpad = 0;
        $simpadRealisasi = SptpdReguler::where('status',3)->whereYear('tgl_bayar',$yearNow)->orderBy('tgl_bayar','ASC')->get()->groupBy(function($item) {
            return Carbon::parse($item->tgl_bayar)->format('m');
        });
        $simpadTarget = TargetPenerimaan::where('tahun',$yearNow)->orderBy('bulan','ASC')->get();
        // BPHTB
        $dataTargetPerBulanBphtb = [];
        $totalTargetBphtb = 0;
        $dataRealisasiPerBulanBphtb = [];
        $totalRealisasiBphtb = 0;
        $targetBphtb = TargetBphtb::where('tahun','2020')->orderBy('bulan','ASC')->get();
        $realisasiBphtb = PembayaranBphtb::where('status',1)->whereYear('tanggal_pembayaran','2020')->orderBy('tanggal_pembayaran','ASC')->get()->groupBy(function($item){
            return Carbon::parse($item->tanggal_pembayaran)->format('m');
        });

        $realisasi_perbulan_total = [];
        $realisasi_total = 0;
        $target_perbulan_total = [];
        $target_total = 0;





        foreach ($months as $key => $value) {
            $index = ltrim($key, '0');
            $index--;

            $sumSpptRealisasi = 0;
            $sumSpptTarget = 0;
            $sumSimpadRealisasi = 0;
            $sumSimpadTarget = 0;
            $sumBphtbRealisasi = 0;
            $sumBphtbTarget = 0;
            
            

            if(isset($spptRealisasi[$key])){
                $sum = 0;
                foreach ($spptRealisasi[$key] as $keyRes => $valueRes) {
                    $totalRealisasiSppt+=$valueRes['jml_sppt_yg_dibayar'];
                    $realisasi_total+=$valueRes['jml_sppt_yg_dibayar'];
                    $sum += $valueRes['jml_sppt_yg_dibayar'];
                }
                array_push($dataRealisasiPerBulanSppt,$sum);
                $sumSpptRealisasi = $sum;
            }else{
                array_push($dataRealisasiPerBulanSppt,0);
            }
            if(isset($spptTarget[$index])){
                $sum = 0;
                $totalTargetSppt += $spptTarget[$index]['target'];
                $target_total += $spptTarget[$index]['target'];
                $sum += $spptTarget[$index]['target'];
                array_push($dataTargetPerBulanSppt,$sum);
                $sumSpptTarget = $sum;
            }else{
                array_push($dataTargetPerBulanSppt,0);
            }
            if(isset($simpadRealisasi[$key]))
            {

                $sum = 0;
                foreach ($simpadRealisasi[$key] as $keyRes=>$valueRes)
                {
                    $totalRealisasiSimpad += $valueRes['pajak_terhutang'];
                    $realisasi_total += $valueRes['pajak_terhutang'];
                    $sum += $valueRes['pajak_terhutang'];
                
                }
                array_push($dataRealisasiPerBulanSimpad,$sum);
                $sumSimpadRealisasi = $sum;
            }else{
                array_push($dataRealisasiPerBulanSimpad,0);
            }
            if(isset($simpadTarget[$index]))
            {
                $sum = 0;
                $totalTargetSimpad += $simpadTarget[$index]['target'];
                $sum += $simpadTarget[$index]['target'];
                $target_total += $simpadTarget[$index]['target'];
                array_push($dataTargetPerBulanSimpad,$sum);
                $sumSimpadTarget = $sum;
            }else{
                array_push($dataTargetPerBulanSimpad,0);
            }
            if(isset($realisasiBphtb[$key]))
            {
                $sum = 0;
                foreach($realisasiBphtb[$key] as $keyRes=>$valueRes)
                {
                    $totalRealisasiBphtb += $valueRes['jumlah_bayar'];
                    $sum += $valueRes['jumlah_bayar'];
                    $realisasi_total += $valueRes['jumlah_bayar'];
                }
                array_push($dataRealisasiPerBulanBphtb,$sum);
                $sumBphtbRealisasi = $sum;
            }else{
                array_push($dataRealisasiPerBulanBphtb,$sum);
            }
            if(isset($targetBphtb[$index])) {
                $sum = 0;
                $totalTargetBphtb += $targetBphtb[$index]['target'];
                $sum += $targetBphtb[$index]['target'];
                $target_total += $targetBphtb[$index]['target'];
                array_push($dataTargetPerBulanBphtb,$sum);
                $sumBphtbTarget= $sum;
            }else{
                array_push($dataTargetPerBulanBphtb,0);
            }


            array_push($realisasi_perbulan_total,$sumSimpadRealisasi+$sumSpptRealisasi+$sumBphtbRealisasi);
            array_push($target_perbulan_total,$sumSpptTarget+$sumSimpadTarget+$sumBphtbTarget);

        }

        return response()->json([
            //SPPT
            'realisasi_perbulan_sppt' => $dataRealisasiPerBulanSppt,
            'total_realisasi_sppt' => $totalRealisasiSppt,
            'target_perbulan_sppt' => $dataTargetPerBulanSppt,
            'total_target_sppt' => $totalTargetSppt,
            //SIMPAD
            'realisasi_perbulan_simpad' => $dataRealisasiPerBulanSimpad,
            'total_realisasi_simpad' => $totalRealisasiSimpad,
            'target_perbulan_simpad' => $dataTargetPerBulanSimpad,
            'total_target_simpad' => $totalTargetSimpad,
            //BPHP
            'realisasi_perbulan_bphtb' => $dataRealisasiPerBulanBphtb,
            'total_realisasi_bphtb' => $totalRealisasiBphtb,
            'target_perbulan_bphtb' => $dataTargetPerBulanBphtb,
            'total_target_bphtb' => $totalTargetBphtb,
            //TOTAL
            'data_realisasi_perbulan' => $realisasi_perbulan_total,
            'total_realisasi_perbulan' => $realisasi_total,
            'data_target_perbulan' => $target_perbulan_total,
            'total_target_perbulan' => $target_total
        ]);


    }

  

    public function getMoreData()
    {

        $sptpd = SptpdReguler::where('status',3)->orderBy('objek_pajak_id','ASC')->get();

        $dataSptpd = [];
        $objek_pajak_id_sebelumnya = "0";
        foreach($sptpd as $row) {
            $sum = 0;
            $objek_pajak_id = $row['objek_pajak_id'];
            if($objek_pajak_id == $objek_pajak_id_sebelumnya) {
                $objek_pajak_id_sebelumnya = $row['objek_pajak_id'];
                $sum += $row['sdh_dibayar'];
                $dataSptpd["$objek_pajak_id_sebelumnya"] = $sum+$dataSptpd["$objek_pajak_id_sebelumnya"];
            }else{
                $objek_pajak_id_sebelumnya = $row['objek_pajak_id'];
                $sum += $row['sdh_dibayar'];
                $dataSptpd["$objek_pajak_id_sebelumnya"] = $sum;
            }
        }

        arsort($dataSptpd);
        $dataSptpd10 = array_slice($dataSptpd,0,10,true);
        $dataObjekPajak = [];
        foreach ($dataSptpd10 as $key => $value) {
            $objekPajak = ObjekPajakSimpad::with('wajib_pajak')->where('id',$key)->first();
            $dataObjekPajak[$key] = $objekPajak;
        }

        $jenisPajak = JenisPajakSimpad::where('level',3)->get();

        

        // SELECT ingredient_id, ingredient_name, date_ordered, "Quantity & Unit" AS Quantity, unit_price * quantity AS "Total Amount" FROM ingredient WHERE extract(month from date_ordered) = 11 ORDER BY 'date_ordered' DESC;

        $returnData= [ 'top' => $dataObjekPajak,'pajak' => $jenisPajak];

        return $returnData;

        // return response()->json([ 'top' => $dataObjekPajak,'pajak' => $jenisPajak]);
    }

    public function per_hari($request)
    {


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



        $returnData = [
            // 'sppt' => $sppt,
            'test' => array_sum(array_values($allData['count'])),
            // 'bphtb' => $bphtb,
            // 'all_data' => $allData,
            // 'bulan' => namedMonth($monthNow),
            'bulan_nomor' => $monthNow,
            'tahun' => $yearNow,
            'tanggal' => $date,
            'total_hari' => $days,
            'realisasi' => array_values($allData['realisasi']),
            'hari' => $dataDays,
            'full_data' => array_values($allData['full_data']),
            'total_data' => array_sum(array_values($allData['count'])),
            'total_realisasi' => array_sum(array_values($allData['realisasi'])),
            
        ];


        return $returnData;
    }
}
