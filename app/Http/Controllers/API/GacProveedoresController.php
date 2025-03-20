<?php


namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\GacBitacoraEventos;
use App\Models\ComProveedores;

class GacProveedoresController extends BaseController
{

    public function getAllProveedores(Request $request)
    {
        try {
            $proveedores = ComProveedores::get()->all();
            return $this->sendResponse($proveedores);
        } catch (\Throwable $th) {
            return $this->sendError('Error', $th, 500);
        }
    }

    public function setProveedor(Request $request)
    {
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                'nombre' => 'required'

            ]);
            if ($validator->fails()) {
                return $this->sendError('Todos los campos son requeridos', $validator->errors(), 500);
            }
            $set['nombre'] = $request->nombre;
            $proveedor = ComProveedores::create($set);
            $setEvnto['evento'] = 'Registro de proveedor';
            $setEvnto['descripcion'] = "Se ha registrado el provedor {$request->nombre}, con id {$proveedor->id}";
            $setEvnto['id_usuario'] = $request->id_usuario;
            GacBitacoraEventos::create($setEvnto);
            return $this->sendResponse('El proveedor se ha registrado exitosamente');
        } catch (\Throwable $th) {
            return $this->sendError('Error', $th, 500);
        }
    }

    public function editProveedor(Request $request)
    {
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                'nombre' => 'required'
            ]);
            if ($validator->fails()) {
                return $this->sendError('Todos los campos son requeridos', $validator->errors(), 500);
            }
            $proveedor = ComProveedores::where('id', $request->id)->get()->first();
            if(!$proveedor){
                return $this->sendError('El proveedor que desea actualizar no existe', $validator->errors(), 204);
            }
            $proveedor->nombre = $request->nombre;
            $proveedor->save();
            $setEvnto['evento'] = 'Actualización de un proveedor';
            $setEvnto['descripcion'] = "Se ha atualizado el proveedor {$request->nombre}, con id {$proveedor->id}";
            $setEvnto['id_usuario'] = $request->id_usuario;
            GacBitacoraEventos::create($setEvnto);
            return $this->sendResponse('El proveedor se ha actualizado con exito');
        } catch (\Throwable $th) {
            return $this->sendError('Error', $th, 500);
        }
    }


    /* public function deleteProveedor(Request $request)
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
    } */



}


