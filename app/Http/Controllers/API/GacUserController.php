<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\GacSolicitud;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\IOFactory as WordReader;
use Illuminate\Filesystem\Filesystem;
use Smalot\PdfParser\Parser as PdfParser;
use thiagoalessio\TesseractOCR\TesseractOCR;
use App\Models\GacPerfilSolicitud;
use App\Models\GacBitacoraEventos;
use App\Models\GacTrenAutorizadoresSolicitud;
use App\Models\GacDocumentosSolicitud;

class GacUserController extends BaseController
{

    /* Funcion para obtener la gerarquia de jefes */
    function obtenerJefes($usuario, $jefes = [], $orden =1)
    {
        if ($usuario && $usuario->jefe_inmediato != $usuario->id_usuario) {
            $jefe = DB::connection('mysql_dirac')->table('usuarios_dirac')->where('id_usuario', $usuario->jefe_inmediato )->where('status', 1 )->get()->first();
            if ($jefe) {
                $jefe->orden = $orden;
                $jefes[] = $jefe;
                return $this->obtenerJefes($jefe, $jefes, $orden+1);
            }
        }
        return $jefes;
    }


    public function gacGetSolicitudesJefesArea(Request $request){
        /* try { */
            $validator = Validator::make($request->all(), [
                'id_usuario' => 'required'

            ]);
            if ($validator->fails()) {
                return $this->sendError('Todos los valores son requeridos', $validator->errors());
            }
            $solicitudes = DB::select('select a.*, b.nombre as tipo_solicitud, c.nombre as forma_pago , 
            d.nombre as estatus, e.nombre as concepto , f.pais as pais_moneda,
            f.moneda,f.valor_en_dolar as valor_en_dolar_moneda,f.fecha as fecha_moneda,
            g.autorizo, g.requiere_aprobacion
            from gac_solicitud a 
            inner join gac_cat_tipo_solicitud b on a.id_tipo_solicitud = b.id
            inner join gac_cat_forma_pago c on a.id_forma_pago = c.id
            inner join gac_cat_estatus_solicitud d on a.id_estatus = d.id
            inner join gac_cat_conceptos e on a.id_concepto = e.id
            inner join gac_equivalencia_moneda_ext_dol f on a.id_moneda = f.id
            left join gac_tren_autorizadores_solicitud  g on a.id = g.id_solicitud
            where g.id_usuario = ? and g.requiere_aprobacion = ? ', [$request->id_usuario, 1]);

            foreach ($solicitudes as $key => $value) {
                $autorizadores = GacTrenAutorizadoresSolicitud::where('id_solicitud',$value->id)->get()->all();
                foreach ($autorizadores as $keyAutorizadores => $valueAutorizadores) {
                    $usuarioAutorizador =  DB::connection('mysql_dirac')->table('usuarios_dirac')->where('id_usuario', $valueAutorizadores->id_usuario )->where('status', 1 )->get()->first();
                    $autorizadores[$keyAutorizadores]->nombreUsuario = $usuarioAutorizador->nombre .' '. $usuarioAutorizador->apellidos;
                }
                $solicitudes[$key]->autorizadores = $autorizadores;
                $documentos = GacDocumentosSolicitud::where('id_solicitud',$value->id)->get()->all();
                $solicitudes[$key]->documentos = $documentos;
            }
            return $this->sendResponse($solicitudes);
       /*  } catch (\Throwable $th) {
            return $this->sendError('Error al al obtener la información', $th, 500);
        } */
    }

    public function gacGetSolicitudesAdmins(Request $request){
        /* try { */
            $validator = Validator::make($request->all(), [
                'id_usuario' => 'required'

            ]);
            if ($validator->fails()) {
                return $this->sendError('Todos los valores son requeridos', $validator->errors());
            }
            $solicitudes = DB::select('select a.*, b.nombre as tipo_solicitud, c.nombre as forma_pago , 
            d.nombre as estatus, e.nombre as concepto , f.pais as pais_moneda,
            f.moneda,f.valor_en_dolar as valor_en_dolar_moneda,f.fecha as fecha_moneda,null as autorizo,null as requiere_aprobacion
            from gac_solicitud a 
            inner join gac_cat_tipo_solicitud b on a.id_tipo_solicitud = b.id
            inner join gac_cat_forma_pago c on a.id_forma_pago = c.id
            inner join gac_cat_estatus_solicitud d on a.id_estatus = d.id
            inner join gac_cat_conceptos e on a.id_concepto = e.id
            inner join gac_equivalencia_moneda_ext_dol f on a.id_moneda = f.id
            where a.solicita != ?  or a.beneficiario != ?', [$request->id_usuario, $request->id_usuario]);
            foreach ($solicitudes as $key => $value) {
                $autorizadores = GacTrenAutorizadoresSolicitud::where('id_solicitud',$value->id)->where('requiere_aprobacion', 1)->where(function ($query) {
                    $query->where('autorizo', 1)->orWhere('autorizo', null);
                })->get()->all();
                $solicitudes[$key]->faltaAutorizacionJefes = count($autorizadores);

                $autorizadores = GacTrenAutorizadoresSolicitud::where('id_solicitud',$value->id)->get()->all();
                foreach ($autorizadores as $keyAutorizadores => $valueAutorizadores) {
                    $usuarioAutorizador =  DB::connection('mysql_dirac')->table('usuarios_dirac')->where('id_usuario', $valueAutorizadores->id_usuario )->where('status', 1 )->get()->first();
                    $autorizadores[$keyAutorizadores]->nombreUsuario = $usuarioAutorizador->nombre .' '. $usuarioAutorizador->apellidos;
                }
                $solicitudes[$key]->autorizadores = $autorizadores;
                $documentos = GacDocumentosSolicitud::where('id_solicitud',$value->id)->get()->all();
                $solicitudes[$key]->documentos = $documentos;
            }
            return $this->sendResponse($solicitudes);
        /* } catch (\Throwable $th) {
            return $this->sendError('Error al al obtener la información', $th, 500);
        } */
    }

  
    public function gacGetUserData(Request $request)
    {
        /* try { */
            $validator = Validator::make($request->all(), [
                'id' => 'required', 
            ]);
            if ($validator->fails()) {
                return $this->sendError('Todos los valores son requeridos', $validator->errors());
            }
            $usuario =  DB::connection('mysql_dirac')->table('usuarios_dirac')->where('id_usuario', $request->id )->where('status', 1 )->get()->first();
            if(!$usuario){
                return $this->sendError('Este usuario usuario ya no esta registrado en nuestra base de datos', [],404);
            }
            $usuario->jerarquiaJefes = $this->obtenerJefes($usuario,[]);
            $solicitudes = DB::select('select a.*, b.nombre as tipo_solicitud, c.nombre as forma_pago , 
                                            d.nombre as estatus, e.nombre as concepto , f.pais as pais_moneda,
                                            f.moneda,f.valor_en_dolar as valor_en_dolar_moneda,f.fecha as fecha_moneda
                                            from gac_solicitud a 
                                            inner join gac_cat_tipo_solicitud b on a.id_tipo_solicitud = b.id
                                            inner join gac_cat_forma_pago c on a.id_forma_pago = c.id
                                            inner join gac_cat_estatus_solicitud d on a.id_estatus = d.id
                                            inner join gac_cat_conceptos e on a.id_concepto = e.id
                                            inner join gac_equivalencia_moneda_ext_dol f on a.id_moneda = f.id
                                            where a.solicita = ?  or a.beneficiario = ?  ', [$usuario->id_usuario, $usuario->id_usuario]);
            foreach ($solicitudes as $key => $value) {
                $documentos = GacDocumentosSolicitud::where('id_solicitud',$value->id)->get()->all();
                $solicitudes[$key]->documentos = $documentos;
                $bitacoraSolicitud = GacBitacoraEventos::where('id_tabla','gac_solicitud')->where('id_ref',$value->id)->get()->all();
                $solicitudes[$key]->bitacora = $bitacoraSolicitud;
                $autorizadores = GacTrenAutorizadoresSolicitud::where('id_solicitud',$value->id)->get()->all();
                foreach ($autorizadores as $keyAutorizadores => $valueAutorizadores) {
                    $usuarioAutorizador =  DB::connection('mysql_dirac')->table('usuarios_dirac')->where('id_usuario', $valueAutorizadores->id_usuario )->where('status', 1 )->get()->first();
                    $autorizadores[$keyAutorizadores]->nombreUsuario = $usuarioAutorizador->nombre .' '. $usuarioAutorizador->apellidos;
                }
                $solicitudes[$key]->autorizadores = $autorizadores;
            }
            $usuario->solicitudesCreadas = $solicitudes;
            return $this->sendResponse($usuario);
        /* } catch (\Throwable $th) {
            return $this->sendError('Error al agregar el archivo', $th, 500);
        } */
    }


    public function getGetUsuariosAdministradores(Request $request)
    {
        try {
            $usuarios =  DB::connection('mysql_dirac')->table('v_usuarios_dirac')->where('id_area', 5 )->where('status', 1 )->get()->all();
            return $this->sendResponse($usuarios);
        } catch (\Throwable $th) {
            return $this->sendError('Error al obtener los usuarios', $th, 500);
        }
    }

    

    public function getGetUsuariosPerfilesSolicitud(Request $request)
    {
        try {
            $usuariosPerfil =  GacPerfilSolicitud::get()->all();
            return $this->sendResponse($usuariosPerfil);
        } catch (\Throwable $th) {
            return $this->sendError('Error al agregar el archivo', $th, 500);
        }
    }

    public function setPerfilSolicitud(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'right' => 'required'
            ]);
            if ($validator->fails()) {
                return $this->sendError('Todos los valores son requeridos', $validator->errors());
            }
            GacPerfilSolicitud::where('id_perfil', 1)->delete();
            foreach ($request->right as $key => $value) {
                $insert['id_usuario']  = $value['id'];
                $insert['id_perfil']  = 1;
                $insert['fecha_registro']  = now();
                GacPerfilSolicitud::create($insert);
            }
            return $this->sendResponse('Exito al guardar los perfiles');
        } catch (\Throwable $th) {
            return $this->sendError('Error al asignar los perfiles de revisores', $th, 500);
        }
    }

}