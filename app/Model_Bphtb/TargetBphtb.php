<?php

namespace App\Model_Bphtb;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TargetBphtb extends Model
{

    protected $connection = 'mysql_bphtb';
    protected $table = 'targets';

}
