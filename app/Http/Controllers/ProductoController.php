<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function index(Request $req)
    {
        $q = Producto::query()
            ->with('banco:id,nombre')
            ->orderBy('nombre_comercial');

        if ($req->filled('banco_id')) {
            $q->where('banco_id', (int)$req->banco_id);
        }

        // Para la app, conviene no mandar todas las columnas
        $data = $q->get([
            'id','banco_id','codigo','tipo','nombre_comercial','moneda',
            'monto_minimo','monto_maximo',
            'plazo_minimo_meses','plazo_maximo_meses',
            'tea_minimo','tea_maximo',
            'tcea_referencial',
            'seguro_desgravamen_mensual','seguro_extra'
        ]);

        return response()->json($data);
    }
}
