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

        $years = $request->years;
        $data = [];
        foreach ($years as $year => $value) {
            $results = Sppt::whereYear('tgl_pembayaran_sppt',$value)->sum('jml_sppt_yg_dibayar'); 
            array_push($data,$results);
        }

        // return response()->json(['status' => 'success', 'data' => $online]);
        return response()->json(['tahun'=> $data]);
    }

    public function month() {
        $results = Sppt::whereYear('tgl_pembayaran_sppt','2019')->get()->groupBy(function($val) {
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
            }
            
        }


        return response()->json(['penerimaan' => $data]);
    }
}
