<?php


namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\GacCatConceptos;
use App\Models\GacBitacoraEventos;

class GacConceptosController extends BaseController
{

    public function getAllConceptos(Request $request)
    {
        try {
            $conceptos = GacCatConceptos::get()->all();
            return $this->sendResponse($conceptos);
        } catch (\Throwable $th) {
            return $this->sendError('Error', $th, 500);
        }
    }

    public function setConcepto(Request $request)
    {
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                'clave' => 'required',
                'nombre' => 'required',
                'descripcion' => 'required',
                //'categoria' => 'required',
                'id_usuario' => 'required'

            ]);
            if ($validator->fails()) {
                return $this->sendError('Todos los campos son requeridos', $validator->errors(), 500);
            }
            $set['clave'] = $request->clave;
            $set['nombre'] = $request->nombre;
            $set['descripcion'] = $request->descripcion;
            $set['categoria'] = $request->has('categoria') ? $request->categoria : null;
            $set['estatus'] = 1;
            $set['fecha_registro'] = now();
            $set['id_usuario'] = $request->id_usuario;
            $concepto = GacCatConceptos::create($set);
            $setEvnto['evento'] = 'Registro de concepto';
            $setEvnto['descripcion'] = "Se ha registrado el concepto {$request->nombre}, con id {$concepto->id}";
            $setEvnto['id_usuario'] = $request->id_usuario;
            GacBitacoraEventos::create($setEvnto);
            return $this->sendResponse('El concepto se ha registrado exitosamente');
        } catch (\Throwable $th) {
            return $this->sendError('Error', $th, 500);
        }
    }

    public function editConcepto(Request $request)
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
            $concepto = GacCatConceptos::where('id', $request->id)->get()->first();
            if(!$concepto){
                return $this->sendError('El concepto que desea actualizar no existe', $validator->errors(), 204);
            }
            $concepto->clave = $request->clave;
            $concepto->nombre = $request->nombre;
            $concepto->descripcion = $request->descripcion;
            $concepto->categoria = $request->has('categoria') ? $request->categoria : null;
            $concepto->id_usuario = $request->id_usuario;
            $concepto->save();
            $setEvnto['evento'] = 'Actualización de un concepto';
            $setEvnto['descripcion'] = "Se ha atualizado el concepto {$request->nombre}, con id {$concepto->id}";
            $setEvnto['id_usuario'] = $request->id_usuario;
            GacBitacoraEventos::create($setEvnto);
            return $this->sendResponse('El concepto se ha actualizado con exito');
        } catch (\Throwable $th) {
            return $this->sendError('Error', $th, 500);
        }
    }


    public function deleteConcepto(Request $request)
    {
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                'id' => 'required'
            ]);
            if ($validator->fails()) {
                return $this->sendError('Todos los campos son requeridos', $validator->errors(), 500);
            }
            $concepto = GacCatConceptos::where('id', $request->id)->get()->first();
            if(!$concepto){
                return $this->sendError('El concepto que desea actualizar no existe', $validator->errors(), 204);
            }
            $estatus = $concepto->estatus === 0 ? 1 : 0;
            $concepto->estatus = $estatus;
            $concepto->save();
            $setEvnto['evento'] = 'Actualización del estatus de concepto';
            $setEvnto['descripcion'] = "Se ha atualizado el estatus del concepto {$concepto->nombre}, con id {$concepto->id}, con el estatus {$estatus}";
            $setEvnto['id_usuario'] = $request->id_usuario;
            GacBitacoraEventos::create($setEvnto);
            return $this->sendResponse('El concepto se ha eliminado con exito');
        } catch (\Throwable $th) {
            return $this->sendError('Error', $th, 500);
        }
    }



}


