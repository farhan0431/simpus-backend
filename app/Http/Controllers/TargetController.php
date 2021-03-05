<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;

use App\Settings;
use App\TargetPenerimaan;
use App\JenisPajak;
use App\TargetPenerimaanSimpad;
use App\TargetPenerimaanBphtb;


class TargetController extends Controller
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
        


        $target = TargetPenerimaan::with('jenis_pajak')->orderBy('tahun','DESC')->when(request()->q, function($query) {
            $query->where('tahun','LIKE','%'.request()->q.'%')->orWhere('bulan','LIKE','%'.request()->q.'%')->orWhere('target','LIKE','%'.request()->q.'%');
        })
        ->paginate(10);
        return response()->json(['status' => 'success', 'data' => $target]);

    }

    public function jenis_pajak()
    {
        $pajak = JenisPajak::get();
        return response()->json(['statis'=> 'success', 'data' => $pajak]);
    }

    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'tahun' => 'required',
            'bulan' => 'required',
            'target' => 'required|integer|min:1',
            'jenis_pajak_id' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), 500);
        }


        $jenisPajak = JenisPajak::where('id',$request->jenis_pajak_id)->first();
        $check = TargetPenerimaan::where('tahun',$request->tahun)->where('bulan',$request->bulan)->where('jenis_pajak_id',$request->jenis_pajak_id)->count();


        $nameMonth = namedMonth($request->bulan);

        if($check > 0)
        {
            return response()->json(['jenis_pajak_id' => ["$jenisPajak->nama_pajak Pada Bulan $nameMonth dan Tahun $request->tahun Telah Dipakai"]], 500);
        }

        TargetPenerimaan::create([
            'tahun' => $request->tahun,
            'bulan' => $request->bulan,
            'target' => $request->target,
            'jenis_pajak_id' => $request->jenis_pajak_id
        ]);
    

        

        return response()->json(['status' => 'success']);

    }

    public function delete($id)
    {

        $data = TargetPenerimaan::find($id);
        $data->delete();
        // logActivity('Menghapus Role');
        return response()->json(['status' => 'success']);
    }

    public function update(Request $request)
    {

        // return response()->json(['status' => 'success','data' => $request->all()['lama']]);
        $dataBaru = $request->all()['baru'];
        $dataLama = $request->all()['lama'];

        $validate = Validator::make($dataBaru, [
            'tahun' => 'required',
            'bulan' => 'required',
            'target' => 'required|integer|min:1',
            'jenis_pajak_id' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), 500);
        }
        
        $jenisPajak = JenisPajak::where('id',$dataBaru['jenis_pajak_id'])->first();
        $check = TargetPenerimaan::where('tahun',$dataBaru['tahun'])->where('bulan',$dataBaru['bulan'])->where('jenis_pajak_id',$dataBaru['jenis_pajak_id']);
        $dataTarget = $check->first();
        $nameMonth = namedMonth($dataBaru['bulan']);

        if($check->count() > 0)
        {
            if($dataLama['tahun'] == $dataTarget['tahun'] && $dataLama['bulan'] == $dataTarget['bulan'] && $dataLama['jenis_pajak_id'] == $dataTarget['jenis_pajak_id'])
            {
                
            }else{
                return response()->json(['jenis_pajak_id' => [$jenisPajak->nama_pajak." Pada Bulan $nameMonth dan Tahun ".$dataBaru['tahun']." Telah Dipakai"]], 500);
            }
            
        }



        $target = TargetPenerimaan::find($dataLama['id']);

        $target->update([
            'tahun' => $dataBaru['tahun'],
            'bulan' => $dataBaru['bulan'],
            'target' => $dataBaru['target']
        ]);
        return response()->json(['status' => 'success']);

    }


    public function index_simpad() {

        $target = TargetPenerimaanSimpad::orderBy('tahun','DESC')->when(request()->q, function($query) {
            $query->where('tahun','LIKE','%'.request()->q.'%')->orWhere('bulan','LIKE','%'.request()->q.'%')->orWhere('target','LIKE','%'.request()->q.'%');
        })
        ->paginate(10);
        return response()->json(['status' => 'success', 'data' => $target]);

    }

    public function store_simpad(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'tanggal' => 'required|date',
            'target' => 'required|integer'
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), 500);
        }

        if($request->target  <= 0) {
            return response()->json(['target' => ['Target Harus Diatas 0']], 500);
        }

        $month = date('m',strtotime($request->tanggal));
        $year = date('Y',strtotime($request->tanggal));

        $check = TargetPenerimaanSimpad::where('tahun',$year)->where('bulan',$month)->count();



        if($check > 0)
        {
            return response()->json(['tanggal' => ['Tahun dan Bulan Telah Dipakai']], 500);
        }

        TargetPenerimaanSimpad::create([
            'tahun' => $year,
            'bulan' => $month,
            'target' => $request->target
        ]);
    

        

        return response()->json(['status' => 'success']);

    }

    public function index_bphtb() {

        $target = TargetPenerimaanBphtb::orderBy('tahun','DESC')->when(request()->q, function($query) {
            $query->where('tahun','LIKE','%'.request()->q.'%')->orWhere('bulan','LIKE','%'.request()->q.'%')->orWhere('target','LIKE','%'.request()->q.'%');
        })
        ->paginate(10);
        return response()->json(['status' => 'success', 'data' => $target]);

    }

    public function store_bphtb(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'tanggal' => 'required|date',
            'target' => 'required|integer'
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), 500);
        }

        if($request->target  <= 0) {
            return response()->json(['target' => ['Target Harus Diatas 0']], 500);
        }

        $month = date('m',strtotime($request->tanggal));
        $year = date('Y',strtotime($request->tanggal));

        $check = TargetPenerimaanBphtb::where('tahun',$year)->where('bulan',$month)->count();



        if($check > 0)
        {
            return response()->json(['tanggal' => ['Tahun dan Bulan Telah Dipakai']], 500);
        }

        TargetPenerimaanBphtb::create([
            'tahun' => $year,
            'bulan' => $month,
            'target' => $request->target
        ]);
    

        

        return response()->json(['status' => 'success']);

    }

    //
}
