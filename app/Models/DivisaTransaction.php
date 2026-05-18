<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DivisaTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'tipo',
        'monto',
        'descripcion',
        'fecha',
    ];

    protected $casts = [
        'fecha' => 'date',
        'monto' => 'decimal:2',
    ];
}
