<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DivisaTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'tipo',
        'medio',
        'moneda_original',
        'monto_original',
        'tasa_cambio',
        'monto',
        'descripcion',
        'fecha',
    ];

    protected $casts = [
        'fecha' => 'date',
        'monto' => 'decimal:2',
        'monto_original' => 'decimal:2',
        'tasa_cambio' => 'decimal:4',
    ];
}
