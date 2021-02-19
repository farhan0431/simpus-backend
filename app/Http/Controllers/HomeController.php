<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use DB;
use App\Sppt;
use App\User;
use Carbon\Carbon;



// use app\Models\Ref_kecamtan;

class HomeController extends Controller
{

    public function year(Request $request) {
        $tahun = [];
        $type = '';

        try {
            DB::connection('oracle')->getPdo();
            $years = $request->years;
            $tahun = [];
            foreach ($years as $year => $value) {
                $results = Sppt::whereYear('tgl_pembayaran_sppt',$value)->sum('jml_sppt_yg_dibayar'); 
                array_push($tahun,$results);
            }
            $type = 'real';
        } catch (\Exception $e) {
            $tahun = ['123123','124234234','123123123','123123'];
            $type = 'dummy';
        }

        

        // return response()->json(['status' => 'success', 'data' => $online]);
        return response()->json(['tahun'=> $tahun,'type' => $type]);
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
}
