<?php


namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\GacCatTipoSolicitud;
use App\Models\GacBitacoraEventos;


class GacTipoSolicitudController extends BaseController
{

    public function getAllTiposSolicitudes(Request $request)
    {
        try {
            $tiposSolicitud = GacCatTipoSolicitud::get()->all();
            return $this->sendResponse($tiposSolicitud);
        } catch (\Throwable $th) {
            return $this->sendError('Error', $th, 500);
        }
    }

    public function setTiposSolicitud(Request $request)
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
            $solicitud = GacCatTipoSolicitud::create($set);
            $setEvnto['evento'] = 'Registro de tipo de solicitud';
            $setEvnto['descripcion'] = "Se ha registrado la solicitud {$request->nombre} con id {$solicitud->id}";
            $setEvnto['id_usuario'] = $request->id_usuario;
            GacBitacoraEventos::create($setEvnto);
            return $this->sendResponse('El tipo de solicitud se ha registrado exitosamente');
        } catch (\Throwable $th) {
            return $this->sendError('Error', $th, 500);
        }
    }

    public function editTiposSolicitud(Request $request)
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
            $tiposSolicitud = GacCatTipoSolicitud::where('id', $request->id)->get()->first();
            if(!$tiposSolicitud){
                return $this->sendError('El tipo de solicitud que desea actualizar no existe', $validator->errors(), 204);
            }
            $tiposSolicitud->clave = $request->clave;
            $tiposSolicitud->nombre = $request->nombre;
            $tiposSolicitud->descripcion = $request->descripcion;
            $tiposSolicitud->id_usuario = $request->id_usuario;
            $tiposSolicitud->save();
            $setEvnto['evento'] = 'Actualización de tipo de solicitud';
            $setEvnto['descripcion'] = "Se ha atualizado la solicitud {$request->nombre}, con id {$tiposSolicitud->id}";
            $setEvnto['id_usuario'] = $request->id_usuario;
            GacBitacoraEventos::create($setEvnto);
            return $this->sendResponse('El tipo de solicitud se ha actualizado con exito');
        } catch (\Throwable $th) {
            return $this->sendError('Error', $th, 500);
        }
    }


    public function deleteTiposSolicitud(Request $request)
    {
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                'id' => 'required'
            ]);
            if ($validator->fails()) {
                return $this->sendError('Todos los campos son requeridos', $validator->errors(), 500);
            }
            $tiposSolicitud = GacCatTipoSolicitud::where('id', $request->id)->get()->first();
            if(!$tiposSolicitud){
                return $this->sendError('El tipo de solicitud que desea actualizar no existe', $validator->errors(), 204);
            }
            $estatus = $tiposSolicitud->estatus === 0 ? 1 : 0;
            $tiposSolicitud->estatus = $estatus;
            $tiposSolicitud->save();
            $setEvnto['evento'] = 'Actualización del estatus de tipo de solicitud';
            $setEvnto['descripcion'] = "Se ha atualizado el estatus del tipo de solicitud {$tiposSolicitud->nombre}, con id {$tiposSolicitud->id}, con el estatus {$estatus}";
            $setEvnto['id_usuario'] = $request->id_usuario;
            GacBitacoraEventos::create($setEvnto);
            return $this->sendResponse('El tipo de solicitud se ha eliminado con exito');
        } catch (\Throwable $th) {
            return $this->sendError('Error', $th, 500);
        }
    }



}


