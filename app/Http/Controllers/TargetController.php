<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;

use App\Settings;
use App\TargetPenerimaanSppt;
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
        


        $target = TargetPenerimaanSppt::orderBy('tahun','DESC')->when(request()->q, function($query) {
            $query->where('tahun','LIKE','%'.request()->q.'%')->orWhere('bulan','LIKE','%'.request()->q.'%')->orWhere('target','LIKE','%'.request()->q.'%');
        })
        ->paginate(10);
        return response()->json(['status' => 'success', 'data' => $target]);

    }

    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'tahun' => 'required',
            'bulan' => 'required',
            'target' => 'required|integer|min:1'
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), 500);
        }



        $check = TargetPenerimaanSppt::where('tahun',$request->tahun)->where('bulan',$request->bulan)->count();


        $nameMonth = namedMonth($request->bulan);

        if($check > 0)
        {
            return response()->json(['bulan' => ["Bulan $nameMonth Pada Tahun $request->tahun Telah Dipakai"]], 500);
        }

        TargetPenerimaanSppt::create([
            'tahun' => $request->tahun,
            'bulan' => $request->bulan,
            'target' => $request->target
        ]);
    

        

        return response()->json(['status' => 'success']);

    }

    public function delete($id)
    {

        $role = TargetPenerimaanSppt::find($id);
        $role->delete();
        // logActivity('Menghapus Role');
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
