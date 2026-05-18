<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Banco;

class BancoController extends Controller
{
    public function index()
    {
        $bancos = Banco::orderBy('id', 'desc')->get();
        return view('bancos.index', compact('bancos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'rif' => 'nullable|string|max:50',
        ]);

        $banco = Banco::create($request->all());

        return response()->json([
            'success' => true,
            'banco' => $banco
        ]);
    }
}
