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
        return response()->json(['tahun'=> $data],401);
    }
}
