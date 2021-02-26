<?php

namespace App\Model_Simpad;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class JenisPajak extends Model
{
    protected $connection = 'mysql_simpad';
    protected $table = 'jenis_pajak';
    protected $guarded = [];



}