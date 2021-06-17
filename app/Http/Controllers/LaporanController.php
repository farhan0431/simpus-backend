<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;

use App\Settings;
use App\Laporan;
use App\Data;
use App\User;

// namespace App\Events;

use App\Events\ExampleEvent;

use Carbon\Carbon;

class LaporanController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function index()
    {
        

        event(new ExampleEvent(true));

        $laporan = Laporan::orderBy('created_at','DESC')->when(request()->q, function($query) {
            $query->where('no_identitas','LIKE','%'.request()->q.'%');
        })
        ->paginate(10);
        return response()->json(['status' => 'success', 'data' => $laporan]);

    }

    public function store(Request $request)
    {
        
        $validate = Validator::make($request->all(), [
            'jenis_kecelakaan' => 'required',
            'kondisi_korban' => 'required',
            'file' => 'required',
            'nama_file' => 'required',
            'jenis_identitas' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), 500);
        }

        $file = base64_decode($request->file);

        $fileExt = explode('.',$request->nama_file);

        $fileName = rand(1,1001).'-'.$fileExt[0].'.'.$fileExt[1];
        $upload = file_put_contents(base_path('public/uploads/'.$fileName), $file);

        $jenisIdentitas;
        $jenisKecelakaan = 0;

        if($request->jenis_identitas == 'KTP')
        {
            $jenisIdentitas = '0';
        }elseif($request->jenis_identitas == 'SIM')
        {
            $jenisIdentitas = '1';
        }else{
            $jenisIdentitas = '2';
        }

        if($request->jenis_kecelakaan == 'Kecelakaan Tunggal')
        {
            $jenisKecelakaan = '0';
        }elseif($request->jenis_kecelakaan == 'Kecelakaan Ganda'){
            $jenisKecelakaan = '1';
        }
        

        
        $store = Laporan::create([
            'jenis_identitas' => $jenisIdentitas,
            'no_identitas' => $request->no_identitas,
            'nama' => $request->nama,
            'kondisi_korban' => $request->kondisi_korban,
            'jenis_kecelakaan' => $jenisKecelakaan,
            'foto' => $fileName,
            'id_pembuat' => $request->user()->id,
            'lat' => $request->lat,
            'lng' => $request->lng,
            'status_laporan' => 0
        ]);
    

        return response()->json([
            // 'data' => $upload,
            'status' => 'success',
            'test' => $jenisKecelakaan
        ],200);

    }

    public function delete($id)
    {

        $data = Laporan::find($id);
        $data->delete();
        // logActivity('Menghapus Role');
        return response()->json(['status' => 'success']);
    }

    public function update(Request $request)
    {

       

        $validate = Validator::make($request->all(), [
            'nama_pajak' => 'required|unique:jenis_pajak,nama_pajak,' . $request->id
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), 500);
        }
        

        $jenisPajak = JenisPajak::find($request->id);

        $jenisPajak->update([
            'nama_pajak' => $request->nama_pajak
        ]);
        return response()->json(['status' => 'success']);

    }

    public function status(Request $request)
    {
        $laporan = Laporan::find($request->id);

        $laporan->update([
            'status' => $request->status
        ]);
        return response()->json(['status' => 'success']);
    }

    public function asuransi(Request $request)
    {

        $data = Data::create([
            'id_laporan' => $request->id,
            'jenis_identitas' => $request->jenis_identitas,
            'no_identitas' => $request->no_identitas,
            'nama' => $request->nama,
            'kondisi_korban' => $request->kondisi_korban,
            'jenis_kecelakaan' => $request->jenis_kecelakaan
        ]);

        $laporan = Laporan::find($request->id);
        $laporan->update([
            'status_laporan' => 1
        ]);
    

        

        return response()->json(['status' => 'success','data' => 'ok']);
    }

    public function laporanSaya(Request $request)
    {
        $laporan = Laporan::where('id_pembuat',$request->user()->id)->get();


        return response()->json(['status' => 'success','data' => $laporan],200);


    }

    public function informasiStatus($id)
    {
        $laporan = Laporan::where('id',$id)->first();

        return response()->json(['status' => 'success','data' => $laporan],200);

    }


    public function bulanIni() {
        $tanggal = Carbon::now();
        $tahun = $tanggal->format('Y');
        $bulan = $tanggal->format('m');


        $dataLaporan = Laporan::get();


        $total = Laporan::count();

        $kecelakaanBulanIni = Laporan::whereYear('created_at',$tahun)->whereMonth('created_at',$bulan)->count();

        $tunggalBulanIni = Laporan::whereYear('created_at',$tahun)->whereMonth('created_at',$bulan)->where('jenis_kecelakaan',0)->count();

        $gandaBulanIni = Laporan::whereYear('created_at',$tahun)->whereMonth('created_at',$bulan)->where('jenis_kecelakaan',1)->count();

        $user = User::count();

        return response()->json([
            'tahun' => $tahun,
            'bulan' => $bulan,
            'kecelakaan_bulan_ini' => $kecelakaanBulanIni,
            'tunggal_bulan_ini' => $tunggalBulanIni,
            'ganda_bulan_ini' => $gandaBulanIni,
            'total' => $total,
            'data' => $dataLaporan,
            'user' => $user

        ],200);
    }

    public function updateNopol(Request $request) {

        $laporan = Laporan::where('id', $request->id);
        $laporan->update([
            'nopol' => $request->nopol
        ]);
        return response()->json(['status' => 'eheem','data' => $laporan]);

    }



    //
}
