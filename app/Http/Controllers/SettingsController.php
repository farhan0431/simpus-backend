<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use App\Settings;
use App\JenisPajak;

use App\Province;
use App\Regency;

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

        // $file = file_get_contents(base_path('public/img/logo.png'), true);
        return response()->json(['status' => 'success', 'data' => $settings]);

    }


    public function update(Request $request)
    {

       

        $validate = Validator::make($request->all(), [
            'pemerintah' => 'required',
            'deskripsi_pemerintah' => 'required',
            'slogan' => 'required',
            'kantor_badan' => 'required',
            'inisial' => 'required',
            'provinsi' => 'required',
            'kota' => 'required',
            'alamat' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), 500);
        }

        // $image_64 = $request->logo[0]['dataURL'];

        // if (preg_match('/^data:image\/(\w+);base64,/', $image_64)) {
        //     $data = substr($image_64, strpos($image_64, ',') + 1);
        
        //     $data = base64_decode($data);

        //     unlink(base_path('public/img/logo.png'));

        //     file_put_contents(base_path('public/img/logo.png'), $data);
        // }

        $settings = Settings::find(1);

        $settings->update([
            'pemerintah' => $request->pemerintah,
            'deskripsi_pemerintah' => $request->deskripsi_pemerintah,
            'slogan' => $request->slogan,
            'kantor_badan' => $request->kantor_badan,
            'inisial' => $request->inisial,
            'provinsi' => $request->provinsi,
            'kota' => $request->kota,
            'alamat' => $request->alamat,
            'logo' => 'logo.png'
        ]);
        return response()->json(['status' => 'success','data' => $request->all()]);

    }

    public function uploadLogo(Request $request)
    {
        if ($request->hasFile('file')) {
            
            $file = $request->file('file');
            $filename = 'logo.png';
            
            move_uploaded_file($file, base_path('public/img/' . $filename));
            // $file->storeAs(base_path('public/img'), $filename);

            // $canvas = Image::canvas(200, 200);
            // $resizeImage  = Image::make($file)->resize(200, 200, function($constraint) {
            //     $constraint->aspectRatio();
            // });
        
            // $canvas->insert($resizeImage, 'center');
            // $canvas->save(base_path('public/img/' . $filename));
        }
        return response()->json(['status' => 'success']);
    }

    public function getProvinsi()
    {
        $provinsi = Province::get();
        return response()->json(['status' => 'success','data' => $provinsi]);
    }

    public function getKota($id)
    {
        $kota = Regency::where('province_id',$id)->get();
        return response()->json(['status' => 'success','data' => $kota]);
    }



    //
}
