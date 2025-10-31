<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

use Illuminate\Validation\ValidationException;

class SimuladorController extends Controller
{
    public function simular(Request $req)

        /**
     * GET /api/simular?producto_id=&monto=&plazo_meses=
     * Calcula la cuota usando sistema francés.
     * - Usa TEA (annual) -> i_mensual = (1+TEA)^(1/12) - 1
     * - Si TEA no existe, cae a TCEA referencial (solo como aproximación).
     */
    {
        $data = $req->validate([
            'producto_id'  => 'required|integer|exists:productos,id',
            'monto'        => 'required|numeric|min:0.01',
            'plazo_meses'  => 'required|integer|min:1|max:600',
        ], [
            'producto_id.required' => 'Selecciona un producto',
            'monto.required'       => 'Ingresa el monto',
            'plazo_meses.required' => 'Ingresa el plazo en meses',
        ]);

        $producto = Producto::findOrFail($data['producto_id']);

        $monto = (float) $data['monto'];
        $n     = (int) $data['plazo_meses'];

        // Tasa anual base (TEA preferente; si no, TCEA referencial)
        $tea = null;
        if (!is_null($producto->tea_minimo) && $producto->tea_minimo > 0) {
            $tea = $producto->tea_minimo / 100.0;
        } elseif (!is_null($producto->tcea_referencial) && $producto->tcea_referencial > 0) {
            $tea = $producto->tcea_referencial / 100.0;
        } else {
            throw ValidationException::withMessages([
                'producto_id' => 'El producto no tiene tasa configurada (TEA/TCEA).',
            ]);
        }

        // Tasa mensual efectiva
        $i = pow(1 + $tea, 1/12) - 1; // Ej: 30% anual => ~2.208% mensual

        // Cuota sistema francés: P * i / (1 - (1+i)^-n)
        $cuota_base = $monto * $i / (1 - pow(1 + $i, -$n));

        // Seguros (si aplica)
        $seguro_desgrav_mensual = 0.0;
        if (!is_null($producto->seguro_desgravamen_mensual) && $producto->seguro_desgravamen_mensual > 0) {
            // % mensual sobre saldo inicial (simplificado; puedes migrar a saldo deudor si deseas)
            $seguro_desgrav_mensual = $monto * ($producto->seguro_desgravamen_mensual / 100.0);
        }

        $seguro_extra_mensual = 0.0;
        if (!is_null($producto->seguro_extra) && $producto->seguro_extra > 0) {
            // Interpretación: % mensual adicional
            $seguro_extra_mensual = $monto * ($producto->seguro_extra / 100.0);
        }

        $cuota_total = $cuota_base + $seguro_desgrav_mensual + $seguro_extra_mensual;

        return response()->json([
            'ok'        => 1,
            'params'    => [
                'producto_id' => $producto->id,
                'monto'       => round($monto, 2),
                'plazo_meses' => $n,
            ],
            'producto'  => [
                'id'                   => $producto->id,
                'nombre_comercial'     => $producto->nombre_comercial,
                'banco_id'             => $producto->banco_id,
                'tea_usada'            => round($tea * 100, 4),     // %
                'seguro_desgrav_mensual_%' => (float) $producto->seguro_desgravamen_mensual,
                'seguro_extra_%'           => (float) $producto->seguro_extra,
            ],
            'resultado' => [
                'tasa_mensual'           => round($i * 100, 6),    // %
                'cuota_base'             => round($cuota_base, 2),
                'seguro_desgrav_mensual' => round($seguro_desgrav_mensual, 2),
                'seguro_extra_mensual'   => round($seguro_extra_mensual, 2),
                'cuota_total'            => round($cuota_total, 2),
                'total_pagar'            => round($cuota_total * $n, 2),
            ]
        ]);
    }
}
