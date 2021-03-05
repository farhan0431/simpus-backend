<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;

use App\Settings;
use App\JenisPajak;

class JenisPajakController extends Controller
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
        


        $jenisPajak = JenisPajak::orderBy('created_at','DESC')->when(request()->q, function($query) {
            $query->where('nama_pajak','LIKE','%'.request()->q.'%');
        })
        ->paginate(10);
        return response()->json(['status' => 'success', 'data' => $jenisPajak]);

    }

    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'nama_pajak' => 'required|unique:jenis_pajak,nama_pajak'
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), 500);
        }


        JenisPajak::create([
            'nama_pajak' => $request->nama_pajak
        ]);
    

        

        return response()->json(['status' => 'success','data' => $request->nama_pajak]);

    }

    public function delete($id)
    {

        $data = JenisPajak::find($id);
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



    //
}
