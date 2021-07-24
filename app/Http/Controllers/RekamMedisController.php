<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;

use App\Settings;
use App\RekamMedis;
use App\User;
use App\Identitas;
use App\PemeriksaanUmum;
use App\Dokumen;
use App\Odontogram;

// namespace App\Events;

// use App\Events\SendNotif;

use Carbon\Carbon;

class RekamMedisController extends Controller
{

    public function index()
    {
        $data = Identitas::orderBy('no_rm','ASC')->when(request()->q, function($query) {
            $query->where('no_rm','LIKE','%'.request()->q.'%')->OrWhere('nama','LIKE','%'.request()->q.'%');
        })->paginate(10);

        return response()->json(['status' => 'success', 'data' => $data]);
    }

    public function get_rm()
    {
        $data = Identitas::orderBy('created_at','desc')->first();

        $rm = $data != null ? $data['no_rm'] + 1 : 1;

        $rm = str_pad($rm, 8,'0',STR_PAD_LEFT);

        return response()->json(['status'=>'success','data' => $rm]);
    }

   

    
    
    public function search(Request $request)
    {

        $no_rm = (int)$request->no_rm;

        $data = Identitas::where('no_rm',$no_rm)->first();

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
            // 'riwayat_alergi' => 'required',
            'no_rm' => 'required',
            'status' => 'required',
            'jenis_kelamin' => 'required'
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
            'jenis_kelamin' => $request->jenis_kelamin
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

    public function insert(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'berat_badan' => 'required',
            'tinggi_badan' => 'required',
            'tekanan_darah' => 'required',
            'nadi' => 'required',
            'lingkar_perut' => 'required',
            'suhu_badan' => 'required',
            'nafas' => 'required',
            'no_rm' => 'required'
        ]); 

        if ($validate->fails()) {
            return response()->json($validate->errors(), 500);
        }



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

        if(count($request->pemeriksaan) > 0) {
            foreach ($request->pemeriksaan as $row) {
               PemeriksaanUmum::create([
                   'no_rm' => $request->no_rm,
                   'subjek' => $row['subjek'],
                   'objek' => $row['objek'],
                   'anamnesa' => $row['anamnesa'],
                   'perawatan' => $row['perawatan'],
                   'diagnosa' => $row['diagnosa'],
                   'dokter' => ''
               ]);

            }
        }

        if($request->odontogram != null)
        {
            Odontogram::create([
               'no_rm' => $request->no_rm,

               'a18' => $request->odontogram['a'],
               'a17' => $request->odontogram['e'],
               'a16' => $request->odontogram['i'],
               'a15_55' => $request->odontogram['m'],
               'a14_54' => $request->odontogram['q'],
               'a13_53' => $request->odontogram['u'],
               'a12_52' => $request->odontogram['y'],
               'a11_51' => $request->odontogram['ac'],

               'a28' => $request->odontogram['b'],
               'a27' => $request->odontogram['f'],
               'a26' => $request->odontogram['j'],
               'a25_65' => $request->odontogram['n'],
               'a24_64' => $request->odontogram['r'],
               'a23_63' => $request->odontogram['v'],
               'a22_62' => $request->odontogram['z'],
               'a21_61' => $request->odontogram['ad'],

               'a38' => $request->odontogram['c'],
               'a37' => $request->odontogram['g'],
               'a36' => $request->odontogram['k'],
               'a35_75' => $request->odontogram['o'],
               'a34_74' => $request->odontogram['s'],
               'a33_73' => $request->odontogram['w'],
               'a32_72' => $request->odontogram['aa'],
               'a31_71' => $request->odontogram['ae'],

               'a48' => $request->odontogram['d'],
               'a47' => $request->odontogram['h'],
               'a46' => $request->odontogram['l'],
               'a45_85' => $request->odontogram['p'],
               'a44_84' => $request->odontogram['t'],
               'a43_83' => $request->odontogram['x'],
               'a41_84' => $request->odontogram['ab'],
               'a41_81' => $request->odontogram['af'],
            ]);
        }



        return response()->json([
            'status' => 'success',
            'perawatan' => $request->pemeriksaan
        ],200);
    }

    public function dokumen(Request $request)
    {
        if($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = 'dokumen-'.rand(0,100).'-'.$request->no_rm.'.'.$file->extension();

            move_uploaded_file($file, base_path('public/dokumen/'.$filename));

            $store = Dokumen::create([
                'no_rm' => $request->no_rm,
                'dokumen' => $filename
            ]);

            return response()->json(['status' => 'sukses']);

        }
    }

}