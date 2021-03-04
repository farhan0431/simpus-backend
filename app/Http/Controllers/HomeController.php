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
use App\Model_Simpad\JenisPajak;

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

        $yearNow = Carbon::now()->format('Y');

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

  

    public function getMoreData(Request $request)
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

        $jenisPajak = JenisPajak::where('level',3)->get();



        return response()->json([ 'top' => $dataObjekPajak,'pajak' => $jenisPajak]);
    }
}
