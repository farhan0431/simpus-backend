<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use App\Settings;
use App\TargetPenerimaanSppt;


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

    //
}
