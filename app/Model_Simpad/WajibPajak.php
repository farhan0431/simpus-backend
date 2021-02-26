<?php

namespace App\Model_Simpad;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class WajibPajak extends Model
{
    protected $connection = 'mysql_simpad';
    protected $table = 'wajib_pajak';
    protected $guarded = [];


}