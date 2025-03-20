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
use App\Models\GacCuentasBancariasUsuario;

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
        try {
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
            left join gac_cat_conceptos e on a.id_concepto = e.id
            inner join gac_equivalencia_moneda_ext_dol f on a.id_moneda = f.id
            left join gac_tren_autorizadores_solicitud  g on a.id = g.id_solicitud
            where g.id_usuario = ? and g.requiere_aprobacion = ? ', [$request->id_usuario, 1]);


            /* Aqui hay que validar los datos de información */
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
        } catch (\Throwable $th) {
            return $this->sendError('Error al al obtener la información', $th, 500);
        }
    }

    public function gacGetSolicitudesAdmins(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'id_usuario' => 'required',
                'esAdmin' => 'required'
            ]);
            if ($validator->fails()) {
                return $this->sendError('Todos los valores son requeridos', $validator->errors());
            }
            $solicitudes = DB::select('select a.*, b.nombre as tipo_solicitud, b.requiere_beneficiario, b.requiere_documentos, b.requiere_concepto,
            b.mostrar_pago_quincenas,            c.nombre as forma_pago , 
            d.nombre as estatus, e.nombre as concepto , f.pais as pais_moneda,
            f.moneda,f.valor_en_dolar as valor_en_dolar_moneda,f.fecha as fecha_moneda,null as autorizo,null as requiere_aprobacion
            from gac_solicitud a 
            inner join gac_cat_tipo_solicitud b on a.id_tipo_solicitud = b.id
            inner join gac_cat_forma_pago c on a.id_forma_pago = c.id
            inner join gac_cat_estatus_solicitud d on a.id_estatus = d.id
            left join gac_cat_conceptos e on a.id_concepto = e.id
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

                $usuario =  DB::connection('mysql_dirac')->table('usuarios_dirac')->where('id_usuario', $value->solicita )->get()->first();
                
                $area =  DB::connection('mysql_dirac')->table('cat_areas')->where('id', $usuario->id_area )->get()->first();
                $solicitudes[$key]->area = $area ? $area->nombre : null;
                $solicitudes[$key]->nombre_solicitante = $usuario->nombre . ' ' . $usuario->apellidos;
                

            }
            return $this->sendResponse($solicitudes);
        } catch (\Throwable $th) {
            return $this->sendError('Error al al obtener la información', $th, 500);
        }
    }

    private function cifrarTexto($texto, $claveSecreta) {
        $metodo = 'AES-256-CBC';
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($metodo));
        $textoCifrado = openssl_encrypt($texto, $metodo, $claveSecreta, 0, $iv);
        
        // Codificar en Base64 URL-safe
        return rtrim(strtr(base64_encode($textoCifrado . '::' . $iv), '+/', '-_'), '=');
    }
    
    private function descifrarTexto($textoCifrado, $claveSecreta) {
        $metodo = 'AES-256-CBC';
    
        // Reconvertir de Base64 URL-safe a Base64 normal
        $textoCifrado = base64_decode(strtr($textoCifrado, '-_', '+/') . str_repeat('=', (4 - strlen($textoCifrado) % 4) % 4));

        list($textoEncriptado, $iv) = explode('::', $textoCifrado, 2);
        return openssl_decrypt($textoEncriptado, $metodo, $claveSecreta, 0, $iv);
    }

    public function getUserIdHash(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required'
            ]);
            if ($validator->fails()) {
                return $this->sendError('Todos los valores son requeridos', $validator->errors());
            }
            $usuario = $this->descifrarTexto($request->id,env('CLAVE_HASHIG'));
            return $this->sendResponse($usuario);
        } catch (\Throwable $th) {
            return $this->sendError('Error al al obtener el id de usuario', $th, 500);
        }
    }

    public function gacGetUserData(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required', 
                'esAdmin' => 'required'
            ]);
            if ($validator->fails()) {
                return $this->sendError('Todos los valores son requeridos', $validator->errors());
            }
            $usuario =  DB::connection('mysql_dirac')->table('usuarios_dirac')->where('id_usuario', $request->id )->where('status', 1 )->get()->first();
            if(!$usuario){
                return $this->sendError('Este usuario usuario ya no esta registrado en nuestra base de datos', [],404);
            }
            $usuario->jerarquiaJefes = $this->obtenerJefes($usuario,[]);
            $bancos = GacCuentasBancariasUsuario::where('id_usuario', $usuario->id_usuario)->get()->all();
            $usuario->bancos = $bancos;

            if($request->esAdmin === 'admin'){
                $solicitudes = DB::select('select a.*, b.nombre as tipo_solicitud, b.requiere_beneficiario, b.requiere_documentos, b.requiere_concepto,b.mostrar_pago_quincenas,
                c.nombre as forma_pago , 
                d.nombre as estatus, e.nombre as concepto , f.pais as pais_moneda,
                f.moneda,f.valor_en_dolar as valor_en_dolar_moneda,f.fecha as fecha_moneda, "test" as agrupaadmin
                from gac_solicitud a 
                inner join gac_cat_tipo_solicitud b on a.id_tipo_solicitud = b.id
                inner join gac_cat_forma_pago c on a.id_forma_pago = c.id
                inner join gac_cat_estatus_solicitud d on a.id_estatus = d.id
                left join gac_cat_conceptos e on a.id_concepto = e.id
                inner join gac_equivalencia_moneda_ext_dol f on a.id_moneda = f.id', []);
            }else{
                $solicitudes = DB::select('select a.*, b.nombre as tipo_solicitud, b.requiere_beneficiario, b.requiere_documentos, b.requiere_concepto,b.mostrar_pago_quincenas,
                c.nombre as forma_pago , 
                d.nombre as estatus, e.nombre as concepto , f.pais as pais_moneda,
                f.moneda,f.valor_en_dolar as valor_en_dolar_moneda,f.fecha as fecha_moneda, "test" as agrupaadmin
                from gac_solicitud a 
                inner join gac_cat_tipo_solicitud b on a.id_tipo_solicitud = b.id
                inner join gac_cat_forma_pago c on a.id_forma_pago = c.id
                inner join gac_cat_estatus_solicitud d on a.id_estatus = d.id
                left join gac_cat_conceptos e on a.id_concepto = e.id
                inner join gac_equivalencia_moneda_ext_dol f on a.id_moneda = f.id
                where a.solicita = ?  or a.beneficiario = ?  ', [$usuario->id_usuario, $usuario->id_usuario]);
            }
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
                $area =  DB::connection('mysql_dirac')->table('cat_areas')->where('id', $usuario->id_area )->get()->first();
                $solicitudes[$key]->area = $area ? $area->nombre : null;
                $solicitudes[$key]->nombre_solicitante = $usuario->nombre . ' ' . $usuario->apellidos;
                $solicitudes[$key]->id_hash = $this->cifrarTexto($value->id, env('CLAVE_HASHIG'));
            }
            $usuario->solicitudesCreadas = $solicitudes;
            $usuario->id_hash = $this->cifrarTexto($usuario->id_usuario, env('CLAVE_HASHIG'));
            $usuario->fecha = now();
            return $this->sendResponse($usuario);
        } catch (\Throwable $th) {
            return $this->sendError('Error al agregar el archivo', $th, 500);
        }
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

    public function getGetUsuariosNomina(Request $request)
    {
        try {
            $usuarios =  DB::connection('mysql_dirac')->table('v_usuarios_dirac')->where('id_area', 6 )->where('status', 1 )->get()->all();
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

    public function setPerfilSolicitudNomina(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'right' => 'required'
            ]);
            if ($validator->fails()) {
                return $this->sendError('Todos los valores son requeridos', $validator->errors());
            }
            GacPerfilSolicitud::where('id_perfil', 4)->delete();
            foreach ($request->right as $key => $value) {
                $insert['id_usuario']  = $value['id'];
                $insert['id_perfil']  = 4;
                $insert['fecha_registro']  = now();
                GacPerfilSolicitud::create($insert);
            }
            return $this->sendResponse('Exito al guardar los perfiles');
        } catch (\Throwable $th) {
            return $this->sendError('Error al asignar los perfiles de revisores', $th, 500);
        }
    }

    public function addBanco(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'banco' => 'required',
                'cuenta' => 'required',
                'clabe' => 'required',
                'alias' => 'required',
                'id_usuario' => 'required'
            ]);
            if ($validator->fails()) {
                return $this->sendError('Todos los valores son requeridos', $validator->errors());
            }
            $existe = GacCuentasBancariasUsuario::where('banco', $request->banco)
            ->where('cuenta', $request->cuenta)
            ->where('clabe', $request->clabe)
            ->where('id_usuario', $request->id_usuario)->get()->first();
            if($existe){
                return $this->sendError('Esa información ya la tenemos registrada', [], 500);
            }
            $banco['banco'] = $request->banco;
            $banco['cuenta'] = $request->cuenta;
            $banco['clabe'] = $request->clabe;
            $banco['alias'] = $request->alias;
            $banco['id_usuario'] = $request->id_usuario;
            $nuevoBanco = GacCuentasBancariasUsuario::create($banco);
            if($request->has('file')){
                $file = $request->file('file');
                $nombre_archivo = $file->getClientOriginalName();
                $file->storeAs("documentos/GAC/usuarios/{$request->id_usuario}/documentos_cuentas_bancarias/{$nuevoBanco->id}", $nombre_archivo);
                asset("documentos/GAC/usuarios/{$request->id_usuario}/documentos_cuentas_bancarias/{$nuevoBanco->id}/{$nombre_archivo}");
                $ruta = "storage/app/documentos/GAC/usuarios/{$request->id_usuario}/documentos_cuentas_bancarias/{$nuevoBanco->id}/{$nombre_archivo}";
                $nuevoBanco->ruta = $ruta;
                $nuevoBanco->save();
            }
            return $this->sendResponse('Exito al guardar los perfiles');
        } catch (\Throwable $th) {
            return $this->sendError('Error al guardar el banco', $th, 500);
        }
    }

}