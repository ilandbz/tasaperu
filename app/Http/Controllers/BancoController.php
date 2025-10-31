<?php

namespace App\Http\Controllers;

use App\Models\Banco;
use Illuminate\Http\Request;

class BancoController extends Controller
{
    public function index()
    {
        // Devuelve todos los bancos ordenados por nombre
        $data = Banco::query()
            ->orderBy('nombre')
            ->get(['id','nombre']);

        return response()->json($data);
    }
}
