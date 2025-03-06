<?php


namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\GacCatFormaPago;
use App\Models\GacBitacoraEventos;


class GacPerfilesController extends BaseController
{

    public function getAllPerfiles(Request $request)
    {
        try {
            $catFormaPago = GacCatFormaPago::get()->all();
            return $this->sendResponse($catFormaPago);
        } catch (\Throwable $th) {
            return $this->sendError('Error', $th, 500);
        }
    }

    public function setPerfil(Request $request)
    {
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                'clave' => 'required',
                'nombre' => 'required',
                'descripcion' => 'required',
                'id_usuario' => 'required'

            ]);
            if ($validator->fails()) {
                return $this->sendError('Todos los campos son requeridos', $validator->errors(), 500);
            }
            $set['clave'] = $request->clave;
            $set['nombre'] = $request->nombre;
            $set['descripcion'] = $request->descripcion;
            $set['estatus'] = 1;
            $set['fecha_registro'] = now();
            $set['id_usuario'] = $request->id_usuario;
            $formaPago = GacCatFormaPago::create($set);
            $setEvnto['evento'] = 'Registro de forma de pago';
            $setEvnto['descripcion'] = "Se ha registrado la forma de pago {$request->nombre} con id {$formaPago->id}";
            $setEvnto['id_usuario'] = $request->id_usuario;
            GacBitacoraEventos::create($setEvnto);
            return $this->sendResponse('El forma de pago se ha registrado exitosamente');
        } catch (\Throwable $th) {
            return $this->sendError('Error', $th, 500);
        }
    }

    public function editPerfil(Request $request)
    {
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                'clave' => 'required',
                'nombre' => 'required',
                'descripcion' => 'required',
                'id_usuario' => 'required',
                'id' => 'required'

            ]);
            if ($validator->fails()) {
                return $this->sendError('Todos los campos son requeridos', $validator->errors(), 500);
            }
            $formaPago = GacCatFormaPago::where('id', $request->id)->get()->first();
            if(!$formaPago){
                return $this->sendError('La forma de pago que desea actualizar no existe', $validator->errors(), 204);
            }
            $formaPago->clave = $request->clave;
            $formaPago->nombre = $request->nombre;
            $formaPago->descripcion = $request->descripcion;
            $formaPago->id_usuario = $request->id_usuario;
            $formaPago->save();
            $setEvnto['evento'] = 'Actualización de forma de pago';
            $setEvnto['descripcion'] = "Se ha atualizado la forma de pago {$request->nombre}, con id {$formaPago->id}";
            $setEvnto['id_usuario'] = $request->id_usuario;
            GacBitacoraEventos::create($setEvnto);
            return $this->sendResponse('La forma de pago se ha actualizado con exito');
        } catch (\Throwable $th) {
            return $this->sendError('Error', $th, 500);
        }
    }

    public function deleteoPerfil(Request $request)
    {
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                'id' => 'required'
            ]);
            if ($validator->fails()) {
                return $this->sendError('Todos los campos son requeridos', $validator->errors(), 500);
            }
            $formaPago = GacCatFormaPago::where('id', $request->id)->get()->first();
            if(!$formaPago){
                return $this->sendError('La forma de pago que desea actualizar no existe', $validator->errors(), 204);
            }
            $estatus = $formaPago->estatus === 0 ? 1 : 0;
            $formaPago->estatus = $estatus;
            $formaPago->save();
            $setEvnto['evento'] = 'Actualización del estatus de forma de pago';
            $setEvnto['descripcion'] = "Se ha atualizado el estatus del forma de pago {$formaPago->nombre}, con id {$formaPago->id}, con el estatus {$estatus}";
            $setEvnto['id_usuario'] = $request->id_usuario;
            GacBitacoraEventos::create($setEvnto);
            return $this->sendResponse('La forma de pago se ha eliminado con exito');
        } catch (\Throwable $th) {
            return $this->sendError('Error', $th, 500);
        }
    }

}


