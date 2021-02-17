<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sppt extends Model
{

    protected $connection = 'oracle';
    protected $table = 'PEMBAYARAN_SPPT';

}
