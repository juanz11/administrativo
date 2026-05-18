<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banco extends Model
{
    protected $fillable = ['nombre', 'rif', 'numero_cuenta', 'saldo'];
}
