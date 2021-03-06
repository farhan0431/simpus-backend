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
use App\PemeriksaanGigi;
use App\Dokumen;
use App\Odontogram;
use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Facedes\WithHeadings;


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
            if($request->pemeriksaan[0]['type'] != 1) {
                foreach ($request->pemeriksaan as $row) {
                    PemeriksaanUmum::create([
                        'no_rm' => $request->no_rm,
                        'subjek' => $row['subjek'],
                        'objek' => $row['objek'],
                        'anamnesa' => $row['anamnesa'],
                        'perawatan' => $row['perawatan'],
                        'diagnosa' => $row['diagnosa'],
                        'dokter' => '',
                        'id_rm' => $store->id
                    ]);
     
                 }
            }else{
                foreach ($request->pemeriksaan as $row) {
                    PemeriksaanGigi::create([
                        'no_rm' => $request->no_rm,
                        'gigi' => $row['gigi'],
                        'perawatan' => $row['perawatan'],
                        'diagnosa' => $row['diagnosa'],
                        'dokter' => '',
                        'id_rm' => $store->id
                    ]);
     
                 }
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

    public function laporan(Request $request) {
        $tahun = $request->tahun == "0" ? Carbon::now()->format('Y') : $request->tahun;
        $bulan = $request->bulan == "0" ? Carbon::now()->format('m') : $request->bulan;

        // $transaksi = TransaksiKhusus::whereYear('tanggal',$tahun)->whereMonth('tanggal',$bulan);

        // $count = $transaksi->count();
        // $total = $transaksi->get()->sum(function($value) {
        //     return $value->jumlah;
        // });
        

        // $fileName = "Laporan-Excel-$tahun-$bulan.xlsx";

        return Excel::download(new UsersExport(100,$tahun,$bulan,300),'Laporan-Tahun-'.$tahun.'-Bulan-'.$bulan.'.xlsx');
    }

    public function testing(Request $request)
    {
        $tahun = $request->tahun == "0" ? Carbon::now()->format('Y') : $request->tahun;
        $bulan = $request->bulan == "0" ? Carbon::now()->format('m') : $request->bulan;

        $rekamMedis = RekamMedis::whereYear('created_at',$tahun)->whereMonth('created_at',$bulan)->get();

        $data = [];

        

        $data['kasus_baru'] = [];
       

        for ($i=0; $i < 20 ; $i++) { 
            $data['kasus_baru'][$i] =[];
            $data['kasus_baru'][$i]['pria'] = [];
            $data['kasus_baru'][$i]['pria'][1] = [];
            $data['kasus_baru'][$i]['pria'][2] = [];
            $data['kasus_baru'][$i]['pria'][3] = [];
            $data['kasus_baru'][$i]['pria'][4] = [];

            $data['kasus_baru'][$i]['wanita'] = [];
            $data['kasus_baru'][$i]['wanita'][1] = [];
            $data['kasus_baru'][$i]['wanita'][2] = [];
            $data['kasus_baru'][$i]['wanita'][3] = [];
            $data['kasus_baru'][$i]['wanita'][4] = [];
        }


        $data['kasus_lama'] = [];

        for ($i=0; $i < 20 ; $i++) { 
            $data['kasus_lama'][$i] =[];
            $data['kasus_lama'][$i]['pria'] = [];
            $data['kasus_lama'][$i]['pria'][1] = [];
            $data['kasus_lama'][$i]['pria'][2] = [];
            $data['kasus_lama'][$i]['pria'][3] = [];
            $data['kasus_lama'][$i]['pria'][4] = [];


            $data['kasus_lama'][$i]['wanita'] = [];
            $data['kasus_lama'][$i]['wanita'][1] = [];
            $data['kasus_lama'][$i]['wanita'][2] = [];
            $data['kasus_lama'][$i]['wanita'][3] = [];
            $data['kasus_lama'][$i]['wanita'][4] = [];
        }




        $diagnosaPria['data'] = [];
        foreach ($rekamMedis as $row) {
            $pemeriksaanGigi = PemeriksaanGigi::where('id_rm',$row->id)->get();
            foreach ($pemeriksaanGigi as $rowPemeriksaan) {
                $identitas = Identitas::where('no_rm',$rowPemeriksaan->no_rm)->get();
                foreach ($identitas as $rowIden) {
                    if($rowIden->jenis_kelamin == 1)
                    {

                        $umur = Carbon::parse($rowIden['tanggal_lahir'])->age;

                        if($umur <= 7) {


                            if($rowPemeriksaan->diagnosa == 'K00')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][0]['pria'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][0]['pria'][1],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K01')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][1]['pria'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][1]['pria'][1],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K02')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][2]['pria'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][2]['pria'][1],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K03')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][3]['pria'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][3]['pria'][1],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K04')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][4]['pria'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][4]['pria'][1],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K05')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][5]['pria'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][5]['pria'][1],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K06')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][6]['pria'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][6]['pria'][1],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K07')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][7]['pria'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][7]['pria'][1],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K08')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][8]['pria'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][8]['pria'][1],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K09')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][9]['pria'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][9]['pria'][1],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K10')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][10]['pria'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][10]['pria'][1],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K011')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][11]['pria'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][11]['pria'][1],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K12')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][12]['pria'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][12]['pria'][1],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K13')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][13]['pria'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][13]['pria'][1],'baru');

                                }
                            }

                            else if($rowPemeriksaan->diagnosa == 'K14')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][14]['pria'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][14]['pria'][1],'baru');

                                }
                            }

                            else if($rowPemeriksaan->diagnosa == 'C06.9')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][15]['pria'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][15]['pria'][1],'baru');

                                }
                            }

                            else if($rowPemeriksaan->diagnosa == 'Q35')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][16]['pria'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][16]['pria'][1],'baru');

                                }
                            }

                            else if($rowPemeriksaan->diagnosa == 'Q36')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][17]['pria'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][17]['pria'][1],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'Q37')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][18]['pria'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][18]['pria'][1],'baru');

                                }
                            }else {
                                array_push($data['kasus_baru'][19]['pria'][1],'baru');
                                array_push($data['kasus_lama'][19]['pria'][1],'baru');
                            }

                            
                        

                        }else if($umur > 7 && $umur <= 15) {
                            
                           
                            if($rowPemeriksaan->diagnosa == 'K00')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][0]['pria'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][0]['pria'][2],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K01')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][1]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][1]['pria'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K02')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][2]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][2]['pria'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K03')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][3]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][3]['pria'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K04')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][4]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][4]['pria'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K05')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][5]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][5]['pria'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K06')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][6]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][6]['pria'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K07')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][7]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][7]['pria'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K08')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][8]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][8]['pria'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K09')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][9]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][9]['pria'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K10')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][10]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][10]['pria'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K011')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][11]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][11]['pria'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K12')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][12]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][12]['pria'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K13')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][13]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][13]['pria'][3],'baru');

                                }
                            }

                            else if($rowPemeriksaan->diagnosa == 'K14')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][14]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][14]['pria'][3],'baru');

                                }
                            }

                            else if($rowPemeriksaan->diagnosa == 'C06.9')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][15]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][15]['pria'][3],'baru');

                                }
                            }

                            else if($rowPemeriksaan->diagnosa == 'Q35')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][16]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][16]['pria'][3],'baru');

                                }
                            }

                            else if($rowPemeriksaan->diagnosa == 'Q36')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][17]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][17]['pria'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'Q37')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][18]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][18]['pria'][3],'baru');

                                }
                            }else {
                                array_push($data['kasus_baru'][19]['pria'][3],'baru');
                                array_push($data['kasus_lama'][19]['pria'][3],'baru');
                            }

                            
                        

                        }else if($umur > 15 && $umur < 59) {
                            if($rowPemeriksaan->diagnosa == 'K00')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][0]['pria'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][0]['pria'][2],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K01')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][1]['pria'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][1]['pria'][2],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K02')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][2]['pria'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][2]['pria'][2],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K03')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][3]['pria'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][3]['pria'][2],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K04')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][4]['pria'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][4]['pria'][2],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K05')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][5]['pria'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][5]['pria'][2],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K06')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][6]['pria'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][6]['pria'][2],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K07')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][7]['pria'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][7]['pria'][2],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K08')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][8]['pria'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][8]['pria'][2],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K09')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][9]['pria'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][9]['pria'][2],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K10')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][10]['pria'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][10]['pria'][2],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K011')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][11]['pria'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][11]['pria'][2],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K12')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][12]['pria'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][12]['pria'][2],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K13')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][13]['pria'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][13]['pria'][2],'baru');

                                }
                            }

                            else if($rowPemeriksaan->diagnosa == 'K14')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][14]['pria'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][14]['pria'][2],'baru');

                                }
                            }

                            else if($rowPemeriksaan->diagnosa == 'C06.9')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][15]['pria'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][15]['pria'][2],'baru');

                                }
                            }

                            else if($rowPemeriksaan->diagnosa == 'Q35')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][16]['pria'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][16]['pria'][2],'baru');

                                }
                            }

                            else if($rowPemeriksaan->diagnosa == 'Q36')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][17]['pria'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][17]['pria'][2],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'Q37')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][18]['pria'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][18]['pria'][2],'baru');

                                }
                            }else {
                                array_push($data['kasus_baru'][19]['pria'][2],'baru');
                                array_push($data['kasus_lama'][19]['pria'][2],'baru');
                            }

                            
                        }else if($umur >= 60) {

                            if($rowPemeriksaan->diagnosa == 'K00')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][0]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][0]['pria'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K01')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][1]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][1]['pria'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K02')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][2]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][2]['pria'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K03')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][3]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][3]['pria'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K04')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][4]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][4]['pria'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K05')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][5]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][5]['pria'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K06')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][6]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][6]['pria'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K07')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][7]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][7]['pria'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K08')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][8]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][8]['pria'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K09')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][9]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][9]['pria'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K10')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][10]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][10]['pria'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K011')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][11]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][11]['pria'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K12')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][12]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][12]['pria'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K13')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][13]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][13]['pria'][3],'baru');

                                }
                            }

                            else if($rowPemeriksaan->diagnosa == 'K14')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][14]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][14]['pria'][3],'baru');

                                }
                            }

                            else if($rowPemeriksaan->diagnosa == 'C06.9')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][15]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][15]['pria'][3],'baru');

                                }
                            }

                            else if($rowPemeriksaan->diagnosa == 'Q35')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][16]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][16]['pria'][3],'baru');

                                }
                            }

                            else if($rowPemeriksaan->diagnosa == 'Q36')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][17]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][17]['pria'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'Q37')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][18]['pria'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][18]['pria'][3],'baru');

                                }
                            }else {
                                array_push($data['kasus_baru'][19]['pria'][3],'baru');
                                array_push($data['kasus_lama'][19]['pria'][3],'baru');
                            }

                            
                        
                        }
                        

                        // array_push($data['umur'],$umur);
                        // $totalLaki += 1;

                        // array_push($diagnosaPria['data'], $rowPemeriksaan->diagnosa);


                        


                    }else{
                        $umur = Carbon::parse($rowIden['tanggal_lahir'])->age;

                        if($umur <= 7) {


                            if($rowPemeriksaan->diagnosa == 'K00')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][0]['wanita'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][0]['wanita'][1],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K01')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][1]['wanita'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][1]['wanita'][1],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K02')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][2]['wanita'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][2]['wanita'][1],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K03')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][3]['wanita'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][3]['wanita'][1],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K04')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][4]['wanita'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][4]['wanita'][1],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K05')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][5]['wanita'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][5]['wanita'][1],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K06')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][6]['wanita'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][6]['wanita'][1],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K07')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][7]['wanita'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][7]['wanita'][1],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K08')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][8]['wanita'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][8]['wanita'][1],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K09')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][9]['wanita'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][9]['wanita'][1],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K10')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][10]['wanita'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][10]['wanita'][1],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K011')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][11]['wanita'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][11]['wanita'][1],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K12')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][12]['wanita'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][12]['wanita'][1],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K13')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][13]['wanita'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][13]['wanita'][1],'baru');

                                }
                            }

                            else if($rowPemeriksaan->diagnosa == 'K14')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][14]['wanita'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][14]['wanita'][1],'baru');

                                }
                            }

                            else if($rowPemeriksaan->diagnosa == 'C06.9')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][15]['wanita'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][15]['wanita'][1],'baru');

                                }
                            }

                            else if($rowPemeriksaan->diagnosa == 'Q35')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][16]['wanita'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][16]['wanita'][1],'baru');

                                }
                            }

                            else if($rowPemeriksaan->diagnosa == 'Q36')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][17]['wanita'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][17]['wanita'][1],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'Q37')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][18]['wanita'][1],'baru');

                                }else{
                                    array_push($data['kasus_lama'][18]['wanita'][1],'baru');

                                }
                            }else {
                                array_push($data['kasus_baru'][19]['wanita'][1],'baru');
                                array_push($data['kasus_lama'][19]['wanita'][1],'baru');
                            }

                            


                        }else if($umur > 7 && $umur <= 15) {
                            
                        
                            if($rowPemeriksaan->diagnosa == 'K00')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][0]['wanita'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][0]['wanita'][2],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K01')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][1]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][1]['wanita'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K02')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][2]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][2]['wanita'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K03')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][3]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][3]['wanita'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K04')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][4]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][4]['wanita'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K05')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][5]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][5]['wanita'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K06')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][6]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][6]['wanita'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K07')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][7]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][7]['wanita'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K08')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][8]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][8]['wanita'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K09')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][9]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][9]['wanita'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K10')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][10]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][10]['wanita'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K011')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][11]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][11]['wanita'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K12')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][12]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][12]['wanita'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K13')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][13]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][13]['wanita'][3],'baru');

                                }
                            }

                            else if($rowPemeriksaan->diagnosa == 'K14')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][14]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][14]['wanita'][3],'baru');

                                }
                            }

                            else if($rowPemeriksaan->diagnosa == 'C06.9')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][15]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][15]['wanita'][3],'baru');

                                }
                            }

                            else if($rowPemeriksaan->diagnosa == 'Q35')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][16]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][16]['wanita'][3],'baru');

                                }
                            }

                            else if($rowPemeriksaan->diagnosa == 'Q36')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][17]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][17]['wanita'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'Q37')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][18]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][18]['wanita'][3],'baru');

                                }
                            }else {
                                array_push($data['kasus_baru'][19]['wanita'][3],'baru');
                                array_push($data['kasus_lama'][19]['wanita'][3],'baru');
                            }

                            


                        }else if($umur > 15 && $umur < 59) {
                            if($rowPemeriksaan->diagnosa == 'K00')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][0]['wanita'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][0]['wanita'][2],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K01')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][1]['wanita'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][1]['wanita'][2],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K02')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][2]['wanita'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][2]['wanita'][2],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K03')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][3]['wanita'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][3]['wanita'][2],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K04')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][4]['wanita'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][4]['wanita'][2],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K05')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][5]['wanita'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][5]['wanita'][2],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K06')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][6]['wanita'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][6]['wanita'][2],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K07')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][7]['wanita'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][7]['wanita'][2],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K08')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][8]['wanita'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][8]['wanita'][2],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K09')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][9]['wanita'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][9]['wanita'][2],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K10')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][10]['wanita'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][10]['wanita'][2],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K011')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][11]['wanita'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][11]['wanita'][2],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K12')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][12]['wanita'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][12]['wanita'][2],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K13')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][13]['wanita'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][13]['wanita'][2],'baru');

                                }
                            }

                            else if($rowPemeriksaan->diagnosa == 'K14')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][14]['wanita'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][14]['wanita'][2],'baru');

                                }
                            }

                            else if($rowPemeriksaan->diagnosa == 'C06.9')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][15]['wanita'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][15]['wanita'][2],'baru');

                                }
                            }

                            else if($rowPemeriksaan->diagnosa == 'Q35')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][16]['wanita'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][16]['wanita'][2],'baru');

                                }
                            }

                            else if($rowPemeriksaan->diagnosa == 'Q36')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][17]['wanita'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][17]['wanita'][2],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'Q37')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][18]['wanita'][2],'baru');

                                }else{
                                    array_push($data['kasus_lama'][18]['wanita'][2],'baru');

                                }
                            }else {
                                array_push($data['kasus_baru'][19]['wanita'][2],'baru');
                                array_push($data['kasus_lama'][19]['wanita'][2],'baru');
                            }

                            
                        }else if($umur >= 60) {

                            if($rowPemeriksaan->diagnosa == 'K00')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][0]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][0]['wanita'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K01')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][1]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][1]['wanita'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K02')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][2]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][2]['wanita'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K03')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][3]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][3]['wanita'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K04')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][4]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][4]['wanita'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K05')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][5]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][5]['wanita'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K06')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][6]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][6]['wanita'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K07')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][7]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][7]['wanita'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K08')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][8]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][8]['wanita'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K09')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][9]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][9]['wanita'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K10')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][10]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][10]['wanita'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K011')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][11]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][11]['wanita'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K12')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][12]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][12]['wanita'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'K13')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][13]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][13]['wanita'][3],'baru');

                                }
                            }

                            else if($rowPemeriksaan->diagnosa == 'K14')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][14]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][14]['wanita'][3],'baru');

                                }
                            }

                            else if($rowPemeriksaan->diagnosa == 'C06.9')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][15]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][15]['wanita'][3],'baru');

                                }
                            }

                            else if($rowPemeriksaan->diagnosa == 'Q35')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][16]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][16]['wanita'][3],'baru');

                                }
                            }

                            else if($rowPemeriksaan->diagnosa == 'Q36')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][17]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][17]['wanita'][3],'baru');

                                }
                            }
                            else if($rowPemeriksaan->diagnosa == 'Q37')
                            {
                                $check = PemeriksaanGigi::where('no_rm',$rowIden->no_rm)->where('diagnosa','K00')->count();
                                if($check == 1){
                                    array_push($data['kasus_baru'][18]['wanita'][3],'baru');

                                }else{
                                    array_push($data['kasus_lama'][18]['wanita'][3],'baru');

                                }
                            }else {
                                array_push($data['kasus_baru'][19]['wanita'][3],'baru');
                                array_push($data['kasus_lama'][19]['wanita'][3],'baru');
                            }

                            

                        }


                        // array_push($data['umur'],$umur);
                        // $totalLaki += 1;

                        // array_push($diagnosawanita['data'], $rowPemeriksaan->diagnosa);






                    }
                }

            }
            

        }

        return response()->json(['status' => 'success', 'data' => $data]);

    }

}