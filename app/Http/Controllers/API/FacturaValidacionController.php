<?php

namespace App\Http\Controllers\API;;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class FacturaValidacionController extends BaseController
{
    public function validar(Request $request)
    {
        $request->validate([
            'uuid' => 'required|string'
        ]);

        $uuid = $request->uuid;

        $usuario = 'dirac123'; //env('FACTURAMA_USER');     // Tu email de acceso a Facturama
        $password = 'Admin123';// env('FACTURAMA_PASS');    // Tu API Key o contraseña

        $response = Http::withBasicAuth($usuario, $password)
            ->get("https://api.facturama.mx/received/{$uuid}");

        if ($response->ok()) {
            $datos = $response->json();

            return response()->json([
                'estatus' => 'válido',
                'mensaje' => 'La factura fue encontrada en Facturama.',
                'datos' => $datos
            ]);
        }

        return response()->json([
            'estatus' => 'inválido',
            'mensaje' => 'La factura no fue encontrada o es inválida.'
        ], 404);
    }
}
