<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;

use App\Settings;
use App\JenisPajak;

class SettingsController extends Controller
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
        $settings = Settings::first();
        return response()->json(['status' => 'success', 'data' => $settings]);

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
