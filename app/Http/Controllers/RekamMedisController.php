<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;

use App\Settings;
use App\RekamMedis;
use App\User;
use App\Identitas;

// namespace App\Events;

// use App\Events\SendNotif;

use Carbon\Carbon;

class RekamMedisController extends Controller
{

    public function index()
    {
        
    }

    public function get_rm()
    {
        $data = RekamMedis::orderBy('created_at','desc')->first();

        $rm = $data['no_rm'] + 1;

        $rm = str_pad($rm, 8,'0',STR_PAD_LEFT);

        return response()->json(['status'=>'success','data' => $rm]);
    }
    
    public function search(Request $request)
    {

        $no_rm = (int)$request->no_rm;

        $data = RekamMedis::where('no_rm',$no_rm)->first();

        if($data) {
            return response()->json(['status'=>'success','data' => $data]);
        }else{
            return response()->json(['status'=>'success','data' => null]);
        }
    }

    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'nama' => 'required',
            'ktp' => 'required',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required',
            'telp' => 'required',
            'berat_badan' => 'required',
            'tinggi_badan' => 'required',
            'tekanan_darah' => 'required',
            'nadi' => 'required',
            'rujukan_poli' => 'required',
            'lingkar_perut' => 'required',
            'suhu_badan' => 'required',
            'nafas' => 'required',
            'riwayat_alergi' => 'required',
            'no_rm' => 'required',
            'status' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), 500);
        }

        $indentitas = Identitas::create([
            'no_rm' => $request->no_rm,
            'nama' => $request->nama,
            'nik' => $request->ktp,
            'tanggal_lahir' => $request->tanggal_lahir,
            'alamat' => $request->alamat,
            'telp' => $request->telp,
            'status_pembayaran' => $request->status,
            'riwayat_alergi' => $request->riwayat_alergi,
        ]);


        $store = RekamMedis::create([
            'no_rm' => $request->no_rm,
            'tinggi_badan' => $request->tinggi_badan,
            'tekanan_darah' => $request->tekanan_darah,
            'nadi' => $request->nadi,
            'lingkar_perut' => $request->lingkar_perut,
            'berat_badan' => $request->berat_badan,
            'suhu' => $request->suhu_badan,
            'nafas' => $request->nafas,
            'rujukan_poli' => $request->rujukan_poli,

        ]);

        return response()->json([
            'status' => 'success'
        ],200);
    }

}