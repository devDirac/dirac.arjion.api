<?php


namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\GacSolicitud;
use App\Models\GacBitacoraEventos;
use App\Models\GacCatEstatusSolicitud;
use App\Models\GacTrenAutorizadoresSolicitud;
use App\Utils\SmsSend;
use App\Mail\CorreoSolicitudGac;
use App\Mail\CorreoSolicitudNotificaNominaGac;
use App\Models\GacDocumentosSolicitud;
use App\Models\GacCuentasBancariasUsuario;
use App\Models\GacPerfilSolicitud;
use Illuminate\Support\Facades\Mail;
use ZipArchive;
use File;
use App\Models\GacNotificaNomina;

class GacSolicitudController extends BaseController
{

    private $ultramsg_token = "gmxwvtq6ts9up00d";
    private $instance_id = "instance80546";
    private $senWhats = null;

    public function __construct(){
        $this->senWhats = new SmsSend($this->ultramsg_token, $this->instance_id);   
    }

    private function cifrarTexto($texto, $claveSecreta) {
        $metodo = 'AES-256-CBC';
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($metodo));
        $textoCifrado = openssl_encrypt($texto, $metodo, $claveSecreta, 0, $iv);
        return rtrim(strtr(base64_encode($textoCifrado . '::' . $iv), '+/', '-_'), '=');
    }
    
    private function descifrarTexto($textoCifrado, $claveSecreta) {
        $metodo = 'AES-256-CBC';
        $textoCifrado = base64_decode(strtr($textoCifrado, '-_', '+/') . str_repeat('=', (4 - strlen($textoCifrado) % 4) % 4));
        list($textoEncriptado, $iv) = explode('::', $textoCifrado, 2);
        return openssl_decrypt($textoEncriptado, $metodo, $claveSecreta, 0, $iv);
    }

    
    public function getDetalleSolicitud(Request $request){
        try{
            $input = $request->all();
            $validator = Validator::make($input, [
                'id_solicitud' => 'required', 
                'id_autorizador' => 'required', 
            ]);
            if ($validator->fails()) {
                return $this->sendError('Todos los campos son requeridos', $validator->errors(), 500);
            }
            $id_solicitud = $this->descifrarTexto($request->id_solicitud,env('CLAVE_HASHIG'));
            $solicitudFind = GacSolicitud::where('id',$id_solicitud)->get()->first();
            if(!$solicitudFind){
                return $this->sendError('La solicitud a la que estas intentando acceder no existe', $validator->errors(), 404);
            }
            $solicitud = DB::select('select a.*,b.requiere_aprobacion_revisor, b.nombre as tipo_solicitud, b.requiere_beneficiario, b.requiere_documentos, b.requiere_concepto,b.mostrar_pago_quincenas, b.muestra_notificar_nomina , c.nombre as forma_pago , 
                                     d.nombre as estatus, e.nombre as concepto , f.pais as pais_moneda,
                                     f.moneda,f.valor_en_dolar as valor_en_dolar_moneda,f.fecha as fecha_moneda
                                     from gac_solicitud a 
                                     inner join gac_cat_tipo_solicitud b on a.id_tipo_solicitud = b.id
                                     inner join gac_cat_forma_pago c on a.id_forma_pago = c.id
                                     inner join gac_cat_estatus_solicitud d on a.id_estatus = d.id
                                     left join gac_cat_conceptos e on a.id_concepto = e.id
                                     inner join gac_equivalencia_moneda_ext_dol f on a.id_moneda = f.id
                                     where a.id = ? ', [$id_solicitud]);
            foreach ($solicitud as $key => $value) {
                $documentos = GacDocumentosSolicitud::where('id_solicitud',$value->id)->get()->all();
                $solicitud[$key]->documentos = $documentos;
                $bitacoraSolicitud = GacBitacoraEventos::where('id_tabla','gac_solicitud')->where('id_ref',$value->id)->get()->all();
                $solicitud[$key]->bitacora = $bitacoraSolicitud;
                $autorizadores = GacTrenAutorizadoresSolicitud::where('id_solicitud',$value->id)->get()->all();
                foreach ($autorizadores as $keyAutorizadores => $valueAutorizadores) {
                    $usuarioAutorizador =  DB::connection('mysql_dirac')->table('usuarios_dirac')->where('id_usuario', $valueAutorizadores->id_usuario )->where('status', 1 )->get()->first();
                    $autorizadores[$keyAutorizadores]->nombreUsuario = $usuarioAutorizador->nombre .' '. $usuarioAutorizador->apellidos;
                }
                $solicitud[$key]->autorizadores = $autorizadores;
                $infoBancaria = GacCuentasBancariasUsuario::where('banco',$value->banco)->where('cuenta',$value->cuenta)->where('clabe',$value->clabe)->where('id_usuario',$value->solicita)->get()->first();
                $solicitud[$key]->infoBancaria = $infoBancaria;
            }
            $id_autorizador = $this->descifrarTexto($request->id_autorizador,env('CLAVE_HASHIG'));
            
            $autorizador = GacTrenAutorizadoresSolicitud::where('id_usuario',$id_autorizador)->where('id_solicitud',$id_solicitud)->get()->first();
            if($autorizador){
                $autorizador->fecha_visto = now();
                $autorizador->save();
            }
            return $this->sendResponse($solicitud);
        } catch (\Throwable $th) {
            return $this->sendError('Error', $th, 500);
        }
    }

    private function establecerValoresSolicitud($request){
        $estatus = GacCatEstatusSolicitud::where('nombre', 'En proceso')->get()->first();
        $set['solicita'] = $request->solicita; 
        $set['beneficiario'] = $request->has('id_beneficiario') && $request->id_beneficiario !== '' ?  $request->id_beneficiario : null; 
        $set['id_proyecto'] = $request->id_proyecto; 
        $set['id_moneda'] = $request->id_moneda;  
        $set['importe'] = $request->importe; 
        $set['importe_pesos'] = $request->importe_pesos;  
        $set['descripcion'] = $request->descripcion;  
        $set['id_tipo_solicitud'] = $request->id_tipo_solicitud;  
        $set['id_forma_pago'] = $request->id_forma_pago;  
        $set['banco'] = $request->banco;//ok
        $set['cuenta'] = $request->cuenta;//ok 
        $set['clabe'] = $request->clabe;//ok
        $set['fecha_pago'] = $request->has('fecha_pago') && $request->fecha_pago !== '' ? $request->fecha_pago : null;//ok
        $set['fecha_solicitud'] = $request->fecha_solicitud;//ok 
        $set['id_estatus'] = $estatus->id; // SE ASIGGNA EL ESTATUS 1 DE REGISTRADA ok 
        $set['proyecto_sr'] = $request->has('proyecto_sr') && $request->proyecto_sr !== '' ? $request->proyecto_sr : null;  
        $set['id_empresa'] = $request->id_empresa;  
        $set['proveedor'] = $request->has('proveedor') && $request->proveedor !== '' ? $request->proveedor : null;   
        $set['id_concepto'] = $request->has('id_concepto') && $request->id_concepto !== '' ? $request->id_concepto : null;  
        $set['id_usuario_revisor'] = null;
        $set['id_usuario_autorizador'] = null;
        $set['id_usuario_pagada'] = null;
        $set['quincenas_numero'] = $request->has('quincenas_numero') && $request->quincenas_numero !== '' ? $request->quincenas_numero  : null;
        $set['quincenas_valor'] = $request->has('quincenas_valor') && $request->quincenas_valor !== '' ? $request->quincenas_valor  : null;
        return $set;
    }

    private function guardAutorizadores($organigrama, $idSolicitud){
        $primerNotificacion = null;
        foreach ($organigrama as $key => $value) {
            $autorizadorInsert['id_usuario'] = $value['id_usuario'];
            $autorizadorInsert['autorizo'] = null;
            if($value['id_usuario'] === 2  && count($organigrama)>1 ){
                $autorizadorInsert['requiere_aprobacion'] = false;
            }else{
                $autorizadorInsert['requiere_aprobacion'] = true;
            }
            $autorizadorInsert['id_solicitud'] = $idSolicitud;
            $autorizadorInsert['fecha_registro'] = now();
            GacTrenAutorizadoresSolicitud::create($autorizadorInsert);
            if($value['orden'] === 1){
                $primerNotificacion = $value;
            }
        }
        return $primerNotificacion;
    }

    private function enviaMensajeSolicitaDescuentoNomina($user, $idSolicitud, $solicitudimporte, $solicitudsolicitante, $descripcion){
        $idUsiario = $this->cifrarTexto($user->id_usuario, env('CLAVE_HASHIG'));
        $idSolicitud_ = $this->cifrarTexto($idSolicitud, env('CLAVE_HASHIG'));
        $nombre = $user->nombre . ' ' . $user->apellidos;
        Mail::to($user->correo)->send(new CorreoSolicitudGac(
                $idUsiario,
                $user->nombre . ' ' . $user->apellidos, 
                "DAF solicita descuento vía nomina",
                "Se requiere de tu atención para el descuento correspondiente a la siguiente solicitud", 
                $idSolicitud_,
                $solicitudimporte,
                $solicitudsolicitante,
                $descripcion
            ));            
        $to =  "+52{$user->telefono}" ;
        $body = "Hola {$nombre}, arjion te notifica";
        $body1 = "DAF solicita descuento vía nomina";
        $body2 = 'Se requiere de tu atención para el descuento correspondiente a la siguiente solicitud link:';
        $bodyLink = "https://dirac.arjion.com/gac-detalle-solicitud?id={$idUsiario}&id_solicitud={$idSolicitud_}";
        $this->senWhats->sendChatMessage($to, $body);
        $this->senWhats->sendChatMessage($to, $body1);
        $this->senWhats->sendChatMessage($to, $body2);
        $this->senWhats->sendLinkMessage($to, $bodyLink);
    }

    private function enviaMensajeSolicitaAutorizacion($user, $idSolicitud, $solicitudimporte, $solicitudsolicitante, $solicitudbeneficiario){
        $idUsiario = $this->cifrarTexto($user['id_usuario'], env('CLAVE_HASHIG'));
        $idSolicitud_ = $this->cifrarTexto($idSolicitud, env('CLAVE_HASHIG'));
        $nombre = $user['nombre'] . ' ' . $user['apellidos'];
        Mail::to($user['correo'])->send(new CorreoSolicitudGac(
                $idUsiario,
                $user['nombre'] . ' ' . $user['apellidos'], 
                "Validacion de gasto a comprobar" , 
                "Se requiere de tu atención para la aprobación de una solicitud de gastos", 
                $idSolicitud_,
                $solicitudimporte,
                $solicitudsolicitante,
                $solicitudbeneficiario
            ));            
        $to = "+52{$user['telefono']}";
        $body = "Hola {$nombre}, arjion te notifica";
        $body1 = "Validacion de gasto a comprobar";
        $body2 = 'Se requiere de tu atención para la aprobación de una solicitud de gastos, ingresa al siguiente link:';
        $bodyLink = "https://dirac.arjion.com/gac-detalle-solicitud?id={$idUsiario}&id_solicitud={$idSolicitud_}";
        $this->senWhats->sendChatMessage($to, $body);
        $this->senWhats->sendChatMessage($to, $body1);
        $this->senWhats->sendChatMessage($to, $body2);
        $this->senWhats->sendLinkMessage($to, $bodyLink);
    }

    private function enviaMensajeSolicitaAutorizacionDos($user, $idSolicitud, $solicitudimporte, $solicitudsolicitante, $solicitudbeneficiario){
        $idUsiario = $this->cifrarTexto($user->id_usuario, env('CLAVE_HASHIG'));
        $idSolicitud_ = $this->cifrarTexto($idSolicitud, env('CLAVE_HASHIG'));
        $nombre = $user->nombre . ' ' . $user->apellidos;
        Mail::to($user->correo)->send(new CorreoSolicitudGac(
                $idUsiario,
                $user->nombre . ' ' . $user->apellidos, 
                "Validacion de gasto a comprobar" , 
                "Se requiere de tu atención para la aprobación de una solicitud de gastos", 
                $idSolicitud_,
                $solicitudimporte,
                $solicitudsolicitante,
                $solicitudbeneficiario
            ));            
        $to = "+52{$user->telefono}";
        $body = "Hola {$nombre}, arjion te notifica";
        $body1 = "Validacion de gasto a comprobar";
        $body2 = 'Se requiere de tu atención para la aprobación de una solicitud de gastos, ingresa al siguiente link:';
        $bodyLink = "https://dirac.arjion.com/gac-detalle-solicitud?id={$idUsiario}&id_solicitud={$idSolicitud_}";
        $this->senWhats->sendChatMessage($to, $body);
        $this->senWhats->sendChatMessage($to, $body1);
        $this->senWhats->sendChatMessage($to, $body2);
        $this->senWhats->sendLinkMessage($to, $bodyLink);
    }

    /* Mensaje al pagador */
    private function enviaMensajePagador($user, $idSolicitud, $solicitudimporte, $solicitudsolicitante, $solicitudbeneficiario, $nombreRevisor){
        $idUsiario = $this->cifrarTexto($user->id_usuario, env('CLAVE_HASHIG'));
        $idSolicitud_ = $this->cifrarTexto($idSolicitud, env('CLAVE_HASHIG'));
        $nombre = $user->nombre . ' ' . $user->apellidos;
        Mail::to($user->correo)->send(new CorreoSolicitudGac(
                $idUsiario,
                $user->nombre . ' ' . $user->apellidos, 
                "Solicitud de gasto aprobada por el revisor fiscal" , 
                "El revisor fiscal {$nombreRevisor} ha aprobado la solicitud de gasto", 
                $idSolicitud_,
                $solicitudimporte,
                $solicitudsolicitante,
                $solicitudbeneficiario
            ));            
        $to ="+52{$user->telefono}";
        $body = "Hola {$nombre}, arjion te notifica";
        $body1 = "Solicitud de gasto aprobada por el revisor fiscal";
        $body2 = "El revisor fiscal {$nombreRevisor} ha aprobado la solicitud de gasto, detalle en el siguiente link:";
        $bodyLink = "https://dirac.arjion.com/gac-detalle-solicitud?id={$idUsiario}&id_solicitud={$idSolicitud_}";
        $this->senWhats->sendChatMessage($to, $body);
        $this->senWhats->sendChatMessage($to, $body1);
        $this->senWhats->sendChatMessage($to, $body2);
        $this->senWhats->sendLinkMessage($to, $bodyLink);
    }

    private function enviaMensajeCreadorAprobacion($user, $idSolicitud, $solicitudimporte, $solicitudsolicitante, $solicitudbeneficiario, $mensaje ){
        $idUsiario = $this->cifrarTexto($user->id_usuario, env('CLAVE_HASHIG'));
        $idSolicitud_ = $this->cifrarTexto($idSolicitud, env('CLAVE_HASHIG'));
        $nombre = $user->nombre . ' ' . $user->apellidos;
        Mail::to($user->correo)->send(new CorreoSolicitudGac(
                $idUsiario,
                $user->nombre . ' ' . $user->apellidos, 
                "Este es el comprobante de tu solicitud ", 
                $mensaje, 
                $idSolicitud_,
                $solicitudimporte,
                $solicitudsolicitante,
                $solicitudbeneficiario
            ));
        $to = "+52{$user->telefono}";
        $body = "Hola {$nombre}, arjion te notifica";
        $body1 = $mensaje;
        $body2 = 'Este es el comprobante de tu solicitud ingresa al siguiente link:';
        $bodyLink = "https://dirac.arjion.com/gac-detalle-solicitud?id={$idUsiario}&id_solicitud={$idSolicitud_}";
        $this->senWhats->sendChatMessage($to, $body);
        $this->senWhats->sendChatMessage($to, $body1);
        $this->senWhats->sendChatMessage($to, $body2);
        $this->senWhats->sendLinkMessage($to, $bodyLink);
    }

    private function enviaMensajeCreador($user, $idSolicitud, $solicitudimporte, $solicitudsolicitante, $solicitudbeneficiario){
        $idUsiario = $this->cifrarTexto($user->id_usuario, env('CLAVE_HASHIG'));
        $idSolicitud_ = $this->cifrarTexto($idSolicitud, env('CLAVE_HASHIG'));
        $nombre = $user->nombre . ' ' . $user->apellidos;
        Mail::to( $user->correo)->send(new CorreoSolicitudGac(
                $idUsiario,
                $user->nombre . ' ' . $user->apellidos, 
                "Comprobante de solicitud de gasto a comprobar" , 
                "Este es el comprobante de tu solicitud ", 
                $idSolicitud_,
                $solicitudimporte,
                $solicitudsolicitante,
                $solicitudbeneficiario
            ));
        $to = "+52{$user->telefono}";
        $body = "Hola {$nombre}, arjion te notifica";
        $body1 = "Comprobante de solicitud de gasto a comprobar";
        $body2 = 'Este es el comprobante de tu solicitud ingresa al siguiente link:';
        $bodyLink = "https://dirac.arjion.com/gac-detalle-solicitud?id={$idUsiario}&id_solicitud={$idSolicitud_}";
        $this->senWhats->sendChatMessage($to, $body);
        $this->senWhats->sendChatMessage($to, $body1);
        $this->senWhats->sendChatMessage($to, $body2);
        $this->senWhats->sendLinkMessage($to, $bodyLink);
    }

    private function enviaMensajeBeneficiarioAprobacion($user, $idSolicitud, $solicitudimporte, $solicitudsolicitante, $solicitudbeneficiario, $mensaje){
        $idUsiario = $this->cifrarTexto($user->id_usuario, env('CLAVE_HASHIG'));
        $idSolicitud_ = $this->cifrarTexto($idSolicitud, env('CLAVE_HASHIG'));
        $nombre = $user->nombre . ' ' . $user->apellidos;
        Mail::to($user->correo)->send(new CorreoSolicitudGac(
                $idUsiario,
                $user->nombre . ' ' . $user->apellidos, 
                "Este es el comprobante de tu solicitud ", 
                $mensaje, 
                $idSolicitud_,
                $solicitudimporte,
                $solicitudsolicitante,
                $solicitudbeneficiario
            ));
            $to = "+52{$user->telefono}";
            $body = "Hola {$nombre}, arjion te notifica";
            $body1 = $mensaje;
            $body2 =  "Este es el comprobante de tu solicitud, ingresa al siguiente link: ";
            $bodyLink = "https://dirac.arjion.com/gac-detalle-solicitud?id={$idUsiario}&id_solicitud={$idSolicitud_}";
            $this->senWhats->sendChatMessage($to, $body);
            $this->senWhats->sendChatMessage($to, $body1);
            $this->senWhats->sendChatMessage($to, $body2);
            $this->senWhats->sendLinkMessage($to, $bodyLink);
    }

    private function enviaMensajeBeneficiario($user, $idSolicitud, $solicitudimporte, $solicitudsolicitante, $solicitudbeneficiario){
        $idUsiario = $this->cifrarTexto($user->id_usuario, env('CLAVE_HASHIG'));
        $idSolicitud_ = $this->cifrarTexto($idSolicitud, env('CLAVE_HASHIG'));
        $nombre = $user->nombre . ' ' . $user->apellidos;
        Mail::to($user->correo)->send(new CorreoSolicitudGac(
                $idUsiario,
                $user->nombre . ' ' . $user->apellidos, 
                "El usuario ".  $solicitudsolicitante . ", creo una solicitud para ti, "."Comprobante de solicitud de gasto a comprobar" , 
                "Este es el comprobante de tu solicitud ", 
                $idSolicitud_,
                $solicitudimporte,
                $solicitudsolicitante,
                $solicitudbeneficiario
            ));
            $to = "+52{$user->telefono}";
            $body = "Hola {$nombre}, arjion te notifica";
            $body1 = "El usuario ".  $solicitudsolicitante . ", creo una solicitud para ti, "."Comprobante de solicitud de gasto a comprobar";
            $body2 =  "Este es el comprobante de tu solicitud, ingresa al siguiente link: ";
            $bodyLink = "https://dirac.arjion.com/gac-detalle-solicitud?id={$idUsiario}&id_solicitud={$idSolicitud_}";
            $this->senWhats->sendChatMessage($to, $body);
            $this->senWhats->sendChatMessage($to, $body1);
            $this->senWhats->sendChatMessage($to, $body2);
            $this->senWhats->sendLinkMessage($to, $bodyLink);
    }

    public function setSolicitud(Request $request)  {
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                'solicita' => 'required',  
                //'id_beneficiario' => 'required',  
                'id_proyecto' => 'required',  
                'id_moneda' => 'required',  
                'importe' => 'required',
                'importe_pesos' => 'required', 
                'descripcion' => 'required',  
                'id_tipo_solicitud' => 'required',  
                'id_forma_pago' => 'required',  
                'banco' => 'required',  
                'cuenta' => 'required',  
                'clabe' => 'required',
                'fecha_solicitud' => 'required', 
                'id_empresa' => 'required',  
                //'id_concepto' => 'required',  
                'id_usuario' => 'required', 
                'organigrama' => 'required', 
            ]);
            if ($validator->fails()) {
                return $this->sendError('Todos los campos son requeridos', $validator->errors(), 500);
            }
            $set = $this->establecerValoresSolicitud($request);
            $solicitud = GacSolicitud::create($set);
            $notifica = $this->guardAutorizadores($request->organigrama, $solicitud->id);
            if( $notifica['correo'] !== ''){
            $usuario =  DB::connection('mysql_dirac')->table('usuarios_dirac')->where('id_usuario', $solicitud->solicita )->where('status', 1 )->get()->first();
                $usuarioBeneficiario =  DB::connection('mysql_dirac')->table('usuarios_dirac')->where('id_usuario', $solicitud->beneficiario )->where('status', 1 )->get()->first();
                $this->enviaMensajeSolicitaAutorizacion(
                    $notifica, 
                    $solicitud->id, 
                    $solicitud->importe_pesos,
                    $usuario->nombre .' ' . $usuario->apellidos,
                    $solicitud->descripcion,
                );

            }
            $usuario =  DB::connection('mysql_dirac')->table('usuarios_dirac')->where('id_usuario', $solicitud->solicita )->where('status', 1 )->get()->first();
            $usuarioBeneficiario =  DB::connection('mysql_dirac')->table('usuarios_dirac')->where('id_usuario', $solicitud->beneficiario )->where('status', 1 )->get()->first();
            $this->enviaMensajeCreador(
                $usuario, 
                $solicitud->id, 
                $solicitud->importe_pesos,
                $usuario->nombre .' ' . $usuario->apellidos,
                $solicitud->descripcion,
            );

            if($usuarioBeneficiario){
                if($usuario->id_usuario !== $usuarioBeneficiario->id_usuario){
                    $this->enviaMensajeBeneficiario(
                        $usuarioBeneficiario, 
                        $solicitud->id, 
                        $solicitud->importe_pesos,
                        $usuario->nombre .' ' . $usuario->apellidos,
                        $solicitud->descripcion,
                    );
                }
            }
            /* Se registra el evento */
            $setEvnto['evento'] = 'Alta de solicitud';
            $setEvnto['descripcion'] = "Se ha registrado la solicitud ";
            $setEvnto['id_usuario'] = $request->solicita;
            $setEvnto['tipo'] ='Solicitud alta';
            $setEvnto['id_tabla'] = 'gac_solicitud';
            $setEvnto['id_ref'] = $solicitud->id;
            GacBitacoraEventos::create($setEvnto);
            return $this->sendResponse($solicitud);
        } catch (\Throwable $th) {
            return $this->sendError('Error', $th, 500);
        }
    }

    public function setDocumentoSolicitud(Request $request){
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                'importe' => 'required',
                'fiscal_folio' => 'required',
                'rfc' => 'required',
                'nombre_corto' => 'required',
                'descripcion' => 'required',
                'tipo_moneda' => 'required',
                'documento_valido' => 'required',
                'descripcion_documento_validado' => 'required',
                'nombre_documento' => 'required',
                'id_solicitud' => 'required',
                'id_usuario' => 'required',
                'file' => 'required',
                'critsCoValidacion'=> 'required',
            ]);
            if ($validator->fails()) {
                return $this->sendError('Todos los campos son requeridos', $validator->errors(), 500);
            }
            $insertDocumento['importe'] = $request->importe;
            $insertDocumento['fiscal_folio'] = $request->fiscal_folio;
            $insertDocumento['rfc'] = $request->rfc;
            $insertDocumento['nombre_corto'] = $request->nombre_corto;
            $insertDocumento['descripcion'] = $request->descripcion;
            $insertDocumento['tipo_moneda'] = $request->tipo_moneda;
            $insertDocumento['documento_valido'] = $request->documento_valido;
            $insertDocumento['descripcion_documento_validado'] = $request->descripcion_documento_validado;
            $insertDocumento['nombre_documento'] = $request->nombre_documento;
            $insertDocumento['ruta'] = '';
            $insertDocumento['id_solicitud'] = $request->id_solicitud;
            $insertDocumento['id_usuario'] = $request->id_usuario;
            $insertDocumento['estatus'] = 1;
            $insertDocumento['critsCoValidacion'] = $request->critsCoValidacion;
            $insertDocumento['fecha_registro'] = now();
            $doc = GacDocumentosSolicitud::create($insertDocumento);
            /* Guarda documento */
            $file = $request->file('file');
            $file->storeAs("documentos/GAC/".$request->id_solicitud, $doc->id . '_' . $file->getClientOriginalName());
            asset("documentos/GAC/{$request->id_solicitud}/{$doc->id}_{$file->getClientOriginalName()}");
            $ruta = "storage/app/documentos/GAC/{$request->id_solicitud}/{$doc->id}_{$file->getClientOriginalName()}";
            $doc->ruta = $ruta;
            $doc->save();
            /* $usuarioCreador =  DB::connection('mysql_dirac')->table('usuarios_dirac')->where('id_usuario', $request->id_usuario )->where('status', 1 )->get()->first(); */
            return $this->sendResponse($doc);
        } catch (\Throwable $th) {
            return $this->sendError('Error', $th, 500);
        }
    }

    public function apruebaSolicitudJefeDirecto(Request $request){
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                'id_solicitud' => 'required', // el id de la solicitud que se va a aprobar
                'id_usuario' => 'required', // el id del usuario que aprueba o rechaza la solicitud
                'usuario_nombre' => 'required', // el nombre del usuario que aprueba o rechaza la solicitud
                'id_usuario_next' => 'required', // el id del siguiente en la lista de autorizadores
                'aprueba' => 'required', // para saber si aprueba o no  la solicitud y no andar ahi repitiendo cosas
                'comentarios' => 'required'// PARA GUARDAR los comentarios del jefe directo 
            ]);
            if ($validator->fails()) {
                return $this->sendError('Todos los campos son requeridos', $validator->errors(), 500);
            }
            $autorizador = GacTrenAutorizadoresSolicitud::where('id_usuario',$request->id_usuario)->where('id_solicitud',$request->id_solicitud)->get()->first();
            if(!$autorizador){
                return $this->sendError('La solicitud no se encontro', [], 404);
            }

            /* Actualiza el registro indicando que si se autorizo */
            $autorizador->fecha_accion = now();
            $autorizador->autorizo = $request->aprueba;
            $autorizador->comentarios = $request->comentarios;
            $autorizador->save();

            /* Se obtiene la solicitud para extraer los datos de solicita y beneficiario  pora mandarles mensaje*/
            $solicitud = GacSolicitud::where('id',$request->id_solicitud)->get()->first();
            if($request->aprueba === false){
                $solicitud->id_estatus = 3; // solicitud rechazada
                $solicitud->save();
            }
            
            $usuarioCreador =  DB::connection('mysql_dirac')->table('usuarios_dirac')->where('id_usuario', $solicitud->solicita )->where('status', 1 )->get()->first();
            $usuarioBeneficiario =  DB::connection('mysql_dirac')->table('usuarios_dirac')->where('id_usuario', $solicitud->beneficiario )->where('status', 1 )->get()->first();
            if($request->aprueba === true){
                /* Se extrae la informacion del usuario con id =  id_usuario_next para para pedirle su auutorizacion o rechazo*/
                /* manda mensaje al siguiente en la lista */
                if($request->id_usuario_next !== 0){
                    $usuarioNext =  DB::connection('mysql_dirac')->table('usuarios_dirac')->where('id_usuario', $request->id_usuario_next )->where('status', 1 )->get()->first();
                    if($usuarioNext){
                        $this->enviaMensajeSolicitaAutorizacionDos(
                            $usuarioNext, 
                            $solicitud->id, 
                            $solicitud->importe_pesos,
                            $usuarioCreador->nombre .' ' . $usuarioCreador->apellidos,
                            $solicitud->descripcion,
                        );
                    }
                }else{
                    $UsuariosRevisores = GacPerfilSolicitud::where('id_perfil', 2)->get()->first();
                    $usuarioNextRevisor =  DB::connection('mysql_dirac')->table('usuarios_dirac')->where('id_usuario', $UsuariosRevisores->id_usuario )->where('status', 1 )->get()->all();
                    foreach ($usuarioNextRevisor as $key => $value) {
                        if($value){
                            $this->enviaMensajeSolicitaAutorizacionDos(
                                $value, 
                                $solicitud->id, 
                                $solicitud->importe_pesos,
                                $usuarioCreador->nombre .' ' . $usuarioCreador->apellidos,
                                $solicitud->descripcion,
                            );
                        }
                    }
                }
            }
            /* Se manda mensaje al  creador*/
            $this->enviaMensajeCreadorAprobacion(
                $usuarioCreador, 
                $solicitud->id, 
                $solicitud->importe_pesos,
                $usuarioCreador->nombre .' ' . $usuarioCreador->apellidos,
                $solicitud->descripcion,
                $request->aprueba === true ? "La solicitud fue aprobada por: {$request->usuario_nombre}." : "La solicitud fue rechazada por: {$request->usuario_nombre}., por favor inicia una nueva solicitud" 
            );
            /* Si el solicitante y el beneficiario son personas diferentes al beneficiario tambien se le manda mensaje */
            if($usuarioBeneficiario){
                if($usuarioCreador->id_usuario !== $usuarioBeneficiario->id_usuario){
                    $this->enviaMensajeBeneficiarioAprobacion(
                        $usuarioBeneficiario, 
                        $solicitud->id, 
                        $solicitud->importe_pesos,
                        $usuarioCreador->nombre .' ' . $usuarioCreador->apellidos,
                        $solicitud->descripcion,
                        $request->aprueba === true ?  "La solicitud creada por el usuario: {$usuarioCreador->nombre} {$usuarioCreador->apellidos}, fue aprobada por: {$request->usuario_nombre}." : "La solicitud creada por el usuario: {$usuarioCreador->nombre} {$usuarioCreador->apellidos}, fue rechazada por: {$request->usuario_nombre}." , 
                    );
                }
            }
            /* Se registra el evento */
            $setEvnto['evento'] = $request->aprueba === true ? 'Aprobación de solicitud' : 'Rechazo de solicitud';
            $setEvnto['descripcion'] = $request->aprueba === true ?  "El usuario {$request->usuario_nombre}, ha aprobado la solicitud" : "El usuario {$request->usuario_nombre}, ha rechazado la solicitud ";
            $setEvnto['id_usuario'] = $request->id_usuario;
            $setEvnto['tipo'] = $request->aprueba === true ? 'Solicitud aprobación' : 'Solicitud rechazo';
            $setEvnto['id_tabla'] = 'gac_solicitud';
            $setEvnto['id_ref'] = $request->id_solicitud;
            GacBitacoraEventos::create($setEvnto);
            return $this->sendResponse($autorizador);        
        } catch (\Throwable $th) {
            return $this->sendError('Error', $th, 500);
        }
    }

    public function cambioEnSolicitudAutorizador(Request $request){
        try{
            $input = $request->all();
            $validator = Validator::make($input, [
                'id_solicitud' => 'required', // el id de la solicitud que se va a aprobar
                'id_usuario' => 'required', // el id del usuario que aprueba o rechaza la solicitud
                'aprueba' => 'required', // para saber si aprueba o no  la solicitud y no andar ahi repitiendo cosas
                'usuario_nombre' => 'required',
                'comentarios' => 'required', 
                'requiere_doumentos' => 'required',
                'requiere_aprobacion_revisor' => 'required'
            ]);
            if ($validator->fails()) {
                return $this->sendError('Todos los campos son requeridos', $validator->errors(), 500);
            }
            $solicitud = GacSolicitud::where('id',$request->id_solicitud)->get()->first();
            if(!$solicitud){
                return $this->sendError('La solicitud que desea actualizar no existe', [], 404);
            }
            /* Si el autorizador rechaza la solicitud */
            if($request->aprueba === false){

                $solicitud->id_usuario_autorizador = $request->id_usuario;
                $solicitud->fecha_id_usuario_autorizador = now();
                $solicitud->autorizo_usuario_autorizador = 0;
                $solicitud->comentarios_usuario_autorizador = $request->comentarios;
                $solicitud->id_estatus = 3;
                $solicitud->save();
                $usuarioCreador =  DB::connection('mysql_dirac')->table('usuarios_dirac')->where('id_usuario', $solicitud->solicita )->where('status', 1 )->get()->first();
                $usuarioBeneficiario =  DB::connection('mysql_dirac')->table('usuarios_dirac')->where('id_usuario', $solicitud->beneficiario )->where('status', 1 )->get()->first();
                /* Se manda mensaje al  creador*/
                $this->enviaMensajeCreadorAprobacion(
                    $usuarioCreador, 
                    $solicitud->id, 
                    $solicitud->importe_pesos,
                    $usuarioCreador->nombre .' ' . $usuarioCreador->apellidos,
                    $solicitud->descripcion,
                    "La solicitud fue rechazada por: {$request->usuario_nombre}., por favor inicia una nueva solicitud" 
                );
                /* Si el solicitante y el beneficiario son personas diferentes al beneficiario tambien se le manda mensaje */
                if($usuarioBeneficiario){
                    if($usuarioCreador->id_usuario !== $usuarioBeneficiario->id_usuario){
                        $this->enviaMensajeBeneficiarioAprobacion(
                            $usuarioBeneficiario, 
                            $solicitud->id, 
                            $solicitud->importe_pesos,
                            $usuarioCreador->nombre .' ' . $usuarioCreador->apellidos,
                            $solicitud->descripcion,
                           "La solicitud creada por el usuario: {$usuarioCreador->nombre} {$usuarioCreador->apellidos}, fue rechazada por: {$request->usuario_nombre}." , 
                        );
                    }
                }


                $autorizadores = GacTrenAutorizadoresSolicitud::where('id_solicitud',$solicitud->id)->where('requiere_aprobacion', 1)->get()->all();
                foreach ($autorizadores as $keyAutorizadores => $valueAutorizadores) {
                    $usuarioAutorizador =  DB::connection('mysql_dirac')->table('usuarios_dirac')->where('id_usuario', $valueAutorizadores->id_usuario )->where('status', 1 )->where('nivel', 'A' )->get()->first();
                    if($usuarioAutorizador){
                        $this->enviaMensajeCreadorAprobacion(
                            $usuarioAutorizador, 
                            $solicitud->id, 
                            $solicitud->importe_pesos,
                            $usuarioAutorizador->nombre .' ' . $usuarioAutorizador->apellidos,
                            $solicitud->descripcion,
                            "La solicitud fue rechazada por: {$request->usuario_nombre}." 
                        );
                    }
                }


                 /* Se registra el evento */
                $setEvnto['evento'] = 'Rechazo de solicitud';
                $setEvnto['descripcion'] = "El usuario {$request->usuario_nombre}, ha rechazado la solicitud ";
                $setEvnto['id_usuario'] = $request->id_usuario;
                $setEvnto['tipo'] = 'Solicitud rechazo';
                $setEvnto['id_tabla'] = 'gac_solicitud';
                $setEvnto['id_ref'] = $request->id_solicitud;
                GacBitacoraEventos::create($setEvnto);

                return $this->sendResponse('Se ha rechazado la solicitud con exito');
            }
            /* Si el usuario aprobo la solicitud  */
            $solicitud->id_usuario_autorizador = $request->id_usuario;
            $solicitud->fecha_id_usuario_autorizador = now();
            $solicitud->autorizo_usuario_autorizador = 1;
            $solicitud->id_estatus = 2;
            $solicitud->comentarios_usuario_autorizador = $request->comentarios;
            $solicitud->save();
            
            $documentos = GacDocumentosSolicitud::where('id_solicitud',$solicitud->id)->get()->all();
            
            /* Flujo cuando la solicitud no tiene documentos */
            if(count($documentos) === 0 && $request->requiere_doumentos === 1 ){

                $usuarioCreador =  DB::connection('mysql_dirac')->table('usuarios_dirac')->where('id_usuario', $solicitud->solicita )->where('status', 1 )->get()->first();
                $usuarioBeneficiario =  DB::connection('mysql_dirac')->table('usuarios_dirac')->where('id_usuario', $solicitud->beneficiario )->where('status', 1 )->get()->first();
                /* Se manda mensaje al  creador*/
                $this->enviaMensajeCreadorAprobacion(
                    $usuarioCreador, 
                    $solicitud->id, 
                    $solicitud->importe_pesos,
                    $usuarioCreador->nombre .' ' . $usuarioCreador->apellidos,
                    $solicitud->descripcion, 
                    "La solicitud fue aprobada por: {$request->usuario_nombre}, por favor carga los documentos requeridos para continuar con el proceso" 
                );
                
                /* Si el solicitante y el beneficiario son personas diferentes al beneficiario tambien se le manda mensaje */
                if($usuarioBeneficiario){
                    if($usuarioCreador->id_usuario !== $usuarioBeneficiario->id_usuario){
                        $this->enviaMensajeBeneficiarioAprobacion(
                            $usuarioBeneficiario, 
                            $solicitud->id, 
                            $solicitud->importe_pesos,
                            $usuarioCreador->nombre .' ' . $usuarioCreador->apellidos,
                            $solicitud->descripcion,
                           "La solicitud creada por el usuario: {$usuarioCreador->nombre} {$usuarioCreador->apellidos}, fue aprobada por: {$request->usuario_nombre}, por favor carga los documentos requeridos para continuar con el proceso" , 
                        );
                    }
                }
                  /* Se registra el evento */
                  $setEvnto['evento'] = 'Aprobación de solicitud sin documentos';
                  $setEvnto['descripcion'] = "El usuario autorizador {$request->usuario_nombre}, ha aprobado la solicitud sin documentos ";
                  $setEvnto['id_usuario'] = $request->id_usuario;
                  $setEvnto['tipo'] = 'Solicitud aprobación sin documentos';
                  $setEvnto['id_tabla'] = 'gac_solicitud';
                  $setEvnto['id_ref'] = $request->id_solicitud;
                  GacBitacoraEventos::create($setEvnto);
                  return $this->sendResponse('Se ha aprobado la solicitud con exito');
            }

            /* Flujo cuando la solicitud ya trae documentos */
            if(count($documentos) > 0 || $request->requiere_doumentos === 0){
                
                $usuarioCreador =  DB::connection('mysql_dirac')->table('usuarios_dirac')->where('id_usuario', $solicitud->solicita )->where('status', 1 )->get()->first();
                $usuarioBeneficiario =  DB::connection('mysql_dirac')->table('usuarios_dirac')->where('id_usuario', $solicitud->beneficiario )->where('status', 1 )->get()->first();
                $UsuariosRevisores = GacPerfilSolicitud::where('id_perfil', 1)->get()->all();
                
                if($request->requiere_aprobacion_revisor === 1){
                    foreach ($UsuariosRevisores as $key => $value) {
                        $usuarioNextRevisor =  DB::connection('mysql_dirac')->table('usuarios_dirac')->where('id_usuario', $value->id_usuario )->where('status', 1 )->get()->first();
                        if($usuarioNextRevisor){
                            $this->enviaMensajeSolicitaAutorizacionDos(
                                $usuarioNextRevisor, 
                                $solicitud->id, 
                                $solicitud->importe_pesos,
                                $usuarioCreador->nombre .' ' . $usuarioCreador->apellidos,
                                $solicitud->descripcion,
                            );
                        }
                    }
                }

                if($request->requiere_aprobacion_revisor === 0){
                    $solicitud->id_usuario_revisor = $request->id_usuario;
                    $solicitud->fecha_id_usuario_revisor = now();
                    $solicitud->autorizo_usuario_revisor = 1;
                    $solicitud->comentarios_usuario_revisor = $request->comentarios;
                    $solicitud->save();
                    $UsuariosPagadores = GacPerfilSolicitud::where('id_perfil', 3)->get()->all();
                    foreach ($UsuariosPagadores as $key => $value) {
                        $usuarioNextPagador =  DB::connection('mysql_dirac')->table('usuarios_dirac')->where('id_usuario', $value->id_usuario )->where('status', 1 )->get()->first();
    
                        $this->enviaMensajePagador(
                            $usuarioNextPagador, 
                            $solicitud->id, 
                            $solicitud->importe_pesos,
                            $usuarioCreador->nombre .' ' . $usuarioCreador->apellidos,
                            $solicitud->descripcion,
                            $request->usuario_nombre
                        );
                    }
                }

                /* Se registra el evento */
                $setEvnto['evento'] = $request->requiere_doumentos === 0 ?  'Aprobación de solicitud' : 'Aprobación de solicitud con documentos';
                $setEvnto['descripcion'] = $request->requiere_doumentos === 0 ? "El usuario autorizador {$request->usuario_nombre}, ha aprobado la solicitud" : "El usuario autorizador {$request->usuario_nombre}, ha aprobado la solicitud con documentos ";
                $setEvnto['id_usuario'] = $request->id_usuario;
                $setEvnto['tipo'] = 'Solicitud aprobación con documentos';
                $setEvnto['id_tabla'] = 'gac_solicitud';
                $setEvnto['id_ref'] = $request->id_solicitud;
                GacBitacoraEventos::create($setEvnto);
                return $this->sendResponse('Se ha aprobado la solicitud con exito');
            }

        } catch (\Throwable $th) {
            return $this->sendError('Error', $th, 500);
        }
    }

    public function solicitaAprobacionDireccionGeneral(Request $request){
        try{
            $input = $request->all();
            $validator = Validator::make($input, [
                'id_solicitud' => 'required'
            ]);
            if ($validator->fails()) {
                return $this->sendError('Todos los campos son requeridos', $validator->errors(), 500);
            }
            $autorizador = GacTrenAutorizadoresSolicitud::where('requiere_aprobacion',0)->where('id_solicitud',$request->id_solicitud)->get()->first();
            $solicitud = GacSolicitud::where('id',$request->id_solicitud)->get()->first();
            if(!$autorizador || !$solicitud){
                return $this->sendError('La solicitud no se encontro', [], 404);
            }
            $usuarioCreador =  DB::connection('mysql_dirac')->table('usuarios_dirac')->where('id_usuario', $solicitud->solicita )->where('status', 1 )->get()->first();
            $usuarioBeneficiario =  DB::connection('mysql_dirac')->table('usuarios_dirac')->where('id_usuario', $solicitud->beneficiario )->where('status', 1 )->get()->first();
            /* Actualiza el registro indicando que si se autorizo */
            $autorizador->requiere_aprobacion = 1;
            $autorizador->save();
            $usuarioNext =  DB::connection('mysql_dirac')->table('usuarios_dirac')->where('id_usuario', $autorizador->id_usuario )->where('status', 1 )->get()->first();
            if($usuarioNext){
                $this->enviaMensajeSolicitaAutorizacionDos(
                    $usuarioNext, 
                    $solicitud->id, 
                    $solicitud->importe_pesos,
                    $usuarioCreador->nombre .' ' . $usuarioCreador->apellidos,
                    $solicitud->descripcion,
                );
            }
             /* Se registra el evento */
             $UsuariosRevisores = GacPerfilSolicitud::where('id_perfil', 2)->get()->first();
             $setEvnto['evento'] = 'Solicitud de aprobación de dirección general';
             $setEvnto['descripcion'] = "Se ha solicitado la aprobación de dirección general para la solicitud ";
             $setEvnto['id_usuario'] = $UsuariosRevisores->id_usuario;
             $setEvnto['tipo'] =  'Solicitud de aprobación de dirección general';
             $setEvnto['id_tabla'] = 'gac_solicitud';
             $setEvnto['id_ref'] = $solicitud->id;
             GacBitacoraEventos::create($setEvnto);
            return $this->sendResponse('Se ha solicitado la aprobación de dirección general con exito');        
        } catch (\Throwable $th) {
            return $this->sendError('Error', $th, 500);
        }
    }

    public function notificaRevisoresFiscales (Request $request){
        try{
            $input = $request->all();
            $validator = Validator::make($input, [
                'id_solicitud' => 'required'
            ]);
            if ($validator->fails()) {
                return $this->sendError('Todos los campos son requeridos', $validator->errors(), 500);
            }
            $solicitud = GacSolicitud::where('id',$request->id_solicitud)->get()->first();
            $usuarioCreador =  DB::connection('mysql_dirac')->table('usuarios_dirac')->where('id_usuario', $solicitud->solicita )->where('status', 1 )->get()->first();
            $usuarioBeneficiario =  DB::connection('mysql_dirac')->table('usuarios_dirac')->where('id_usuario', $solicitud->beneficiario )->where('status', 1 )->get()->first();

            $UsuariosRevisores = GacPerfilSolicitud::where('id_perfil', 1)->get()->all();
            foreach ($UsuariosRevisores as $key => $value) {
                $usuarioNextRevisor =  DB::connection('mysql_dirac')->table('usuarios_dirac')->where('id_usuario', $value->id_usuario )->where('status', 1 )->get()->first();
                if($usuarioNextRevisor){
                    $this->enviaMensajeSolicitaAutorizacionDos(
                        $usuarioNextRevisor, 
                        $solicitud->id, 
                        $solicitud->importe_pesos,
                        $usuarioCreador->nombre .' ' . $usuarioCreador->apellidos,
                        $solicitud->descripcion,
                    );
                }
            }
            return $this->sendResponse('Exito al notificar a los revisores');
        }catch(\Throwable $th){
            return $this->sendError('Error', $th, 500);
        }
    }

    public function notificaRevisoresFiscalesAutorizador (Request $request){
        try{
            $input = $request->all();
            $validator = Validator::make($input, [
                'id_solicitud' => 'required'
            ]);
            if ($validator->fails()) {
                return $this->sendError('Todos los campos son requeridos', $validator->errors(), 500);
            }
            $solicitud = GacSolicitud::where('id',$request->id_solicitud)->get()->first();
            $usuarioCreador =  DB::connection('mysql_dirac')->table('usuarios_dirac')->where('id_usuario', $solicitud->solicita )->where('status', 1 )->get()->first();
            $usuarioBeneficiario =  DB::connection('mysql_dirac')->table('usuarios_dirac')->where('id_usuario', $solicitud->beneficiario )->where('status', 1 )->get()->first();

            $UsuariosRevisores = GacPerfilSolicitud::where('id_perfil', 2)->get()->all();
            foreach ($UsuariosRevisores as $key => $value) {
                $usuarioNextRevisor =  DB::connection('mysql_dirac')->table('usuarios_dirac')->where('id_usuario', $value->id_usuario )->where('status', 1 )->get()->first();
                if($usuarioNextRevisor){
                    $this->enviaMensajeSolicitaAutorizacionDos(
                        $usuarioNextRevisor, 
                        $solicitud->id, 
                        $solicitud->importe_pesos,
                        $usuarioCreador->nombre .' ' . $usuarioCreador->apellidos,
                        $solicitud->descripcion,
                    );
                }
            }
            return $this->sendResponse('Exito al notificar a los revisores');
        }catch(\Throwable $th){
            return $this->sendError('Error', $th, 500);
        }
    }

    public function handleDocumentosRevisorRevisa(Request $request){
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                'id_solicitud' => 'required',
                'documentos' => 'required',
                'aprueba' => 'required',
                'comentarios_supervisor' => 'required',
                'id_usuario' => 'required',
            ]);
            if ($validator->fails()) {
                return $this->sendError('Todos los campos son requeridos', $validator->errors(), 500);
            }
            if($request->aprueba === true){
                foreach($request->documentos as $key => $value){
                    $documento = GacDocumentosSolicitud::where('id',$value['id'])->get()->first();
                    if($documento){
                        $documento->es_valido_revisor = 1;
                        $documento->comentarios_supervisor = $request->comentarios_supervisor;
                        $documento->idrevisor = $request->id_usuario;
                        $documento->save();
                    }
                }
                $setEvnto['evento'] = 'Aprobación de documentos';
                $setEvnto['descripcion'] = "los revisores fiscales han aprobado documentos con los siguientes comentarios  {$request->comentarios_supervisor}";
                $setEvnto['id_usuario'] = $request->id_usuario;
                $setEvnto['tipo'] = 'Validación de documentos';
                $setEvnto['id_tabla'] = 'gac_solicitud';
                $setEvnto['id_ref'] = $request->id_solicitud;
                GacBitacoraEventos::create($setEvnto);
                return $this->sendResponse('Exito al aprobar los documentos');
            }

            if($request->aprueba === false){
                foreach($request->documentos as $key => $value){
                    $documento = GacDocumentosSolicitud::where('id',$value['id'])->get()->first();
                    if($documento){
                        $documento->es_valido_revisor = 0;
                        $documento->comentarios_supervisor = $request->comentarios_supervisor;
                        $documento->idrevisor = $request->id_usuario;
                        $documento->save();
                    }
                }
                $solicitud = GacSolicitud::where('id',$request->id_solicitud)->get()->first();
                if(!$solicitud){
                    return $this->sendError('La solicitud no existe', [], 404);
                }
                $usuarioCreador =  DB::connection('mysql_dirac')->table('usuarios_dirac')->where('id_usuario', $solicitud->solicita )->where('status', 1 )->get()->first();
                $usuarioBeneficiario =  DB::connection('mysql_dirac')->table('usuarios_dirac')->where('id_usuario', $solicitud->beneficiario )->where('status', 1 )->get()->first();
                /* Se manda mensaje al  creador*/
                $this->enviaMensajeCreadorAprobacion(
                    $usuarioCreador, 
                    $solicitud->id, 
                    $solicitud->importe_pesos,
                    $usuarioCreador->nombre .' ' . $usuarioCreador->apellidos,
                    $solicitud->descripcion,
                    "Los documentos de la solicitud fuerón rechazados por los revisores fiscales, con los siguientes comentarios  '{$request->comentarios_supervisor}'" 
                );
                /* Si el solicitante y el beneficiario son personas diferentes al beneficiario tambien se le manda mensaje */
                if($usuarioBeneficiario){
                    if($usuarioCreador->id_usuario !== $usuarioBeneficiario->id_usuario){
                        $this->enviaMensajeBeneficiarioAprobacion(
                            $usuarioBeneficiario, 
                            $solicitud->id, 
                            $solicitud->importe_pesos,
                            $usuarioCreador->nombre .' ' . $usuarioCreador->apellidos,
                            $solicitud->descripcion,
                           "Los documentos para la solicitud creada por el usuario: {$usuarioCreador->nombre} {$usuarioCreador->apellidos} fuerón rechazados por los revisores fiscales." , 
                        );
                    }
                }
                $setEvnto['evento'] = 'Rechazo de documentos';
                $setEvnto['descripcion'] = "los revisores fiscales han rechazado documentos con los siguientes comentarios  '{$request->comentarios_supervisor}'";
                $setEvnto['id_usuario'] = $request->id_usuario;
                $setEvnto['tipo'] = 'Validación de documentos';
                $setEvnto['id_tabla'] = 'gac_solicitud';
                $setEvnto['id_ref'] = $request->id_solicitud;
                GacBitacoraEventos::create($setEvnto);
                return $this->sendResponse('Exito al rechazar los documentos');
            }

        } catch (\Throwable $th) {
            return $this->sendError('Error', $th, 500);
        }
    }

    public function cambioEnSolicitudRevisor(Request $request){
        try{
            $input = $request->all();
            $validator = Validator::make($input, [
                'id_solicitud' => 'required', // el id de la solicitud que se va a aprobar
                'id_usuario' => 'required', // el id del usuario que aprueba o rechaza la solicitud
                'aprueba' => 'required', // para saber si aprueba o no  la solicitud y no andar ahi repitiendo cosas
                'usuario_nombre' => 'required',
                'comentarios' => 'required'
            ]);
            if ($validator->fails()) {
                return $this->sendError('Todos los campos son requeridos', $validator->errors(), 500);
            }
            $solicitud = GacSolicitud::where('id',$request->id_solicitud)->get()->first();
            if(!$solicitud){
                return $this->sendError('La solicitud que desea actualizar no existe', [], 404);
            }
            /* Si el autorizador rechaza la solicitud */
            if($request->aprueba === false){

                $solicitud->id_usuario_revisor = $request->id_usuario;
                $solicitud->fecha_id_usuario_revisor = now();
                $solicitud->autorizo_usuario_revisor = 0;
                $solicitud->comentarios_usuario_revisor = $request->comentarios;
                $solicitud->id_estatus = 3;
                $solicitud->save();
                $usuarioCreador =  DB::connection('mysql_dirac')->table('usuarios_dirac')->where('id_usuario', $solicitud->solicita )->where('status', 1 )->get()->first();
                $usuarioBeneficiario =  DB::connection('mysql_dirac')->table('usuarios_dirac')->where('id_usuario', $solicitud->beneficiario )->where('status', 1 )->get()->first();
                /* Se manda mensaje al  creador*/
                $this->enviaMensajeCreadorAprobacion(
                    $usuarioCreador, 
                    $solicitud->id, 
                    $solicitud->importe_pesos,
                    $usuarioCreador->nombre .' ' . $usuarioCreador->apellidos,
                    $solicitud->descripcion,
                    "La solicitud fue rechazada por el revisor: {$request->usuario_nombre}, con los siguientes comentarios ({$request->comentarios})., por favor inicia una nueva solicitud" 
                );
                /* Si el solicitante y el beneficiario son personas diferentes al beneficiario tambien se le manda mensaje */
                if($usuarioBeneficiario){
                    if($usuarioCreador->id_usuario !== $usuarioBeneficiario->id_usuario){
                        $this->enviaMensajeBeneficiarioAprobacion(
                            $usuarioBeneficiario, 
                            $solicitud->id, 
                            $solicitud->importe_pesos,
                            $usuarioCreador->nombre .' ' . $usuarioCreador->apellidos,
                            $solicitud->descripcion,
                           "La solicitud creada por el usuario: {$usuarioCreador->nombre} {$usuarioCreador->apellidos}, fue rechazada por el revisor: {$request->usuario_nombre}, con los siguientes comentarios ({$request->comentarios})." , 
                        );
                    }
                }

                $autorizadores = GacTrenAutorizadoresSolicitud::where('id_solicitud',$solicitud->id)->where('requiere_aprobacion', 1)->get()->all();
                foreach ($autorizadores as $keyAutorizadores => $valueAutorizadores) {
                    $usuarioAutorizador =  DB::connection('mysql_dirac')->table('usuarios_dirac')->where('id_usuario', $valueAutorizadores->id_usuario )->where('status', 1 )->where('nivel', 'A' )->get()->first();
                    if($usuarioAutorizador){
                        $this->enviaMensajeCreadorAprobacion(
                            $usuarioAutorizador, 
                            $solicitud->id, 
                            $solicitud->importe_pesos,
                            $usuarioAutorizador->nombre .' ' . $usuarioAutorizador->apellidos,
                            $solicitud->descripcion,
                            "La solicitud fue rechazada por: {$request->usuario_nombre}." 
                        );
                    }
                }

                 /* Se registra el evento */
                $setEvnto['evento'] = 'Rechazo de solicitud por revisor fiscal';
                $setEvnto['descripcion'] = "El revisor {$request->usuario_nombre}, ha rechazado la solicitud, con los siguientes comentarios ({$request->comentarios})";
                $setEvnto['id_usuario'] = $request->id_usuario;
                $setEvnto['tipo'] = 'Solicitud rechazo por revisor fiscal';
                $setEvnto['id_tabla'] = 'gac_solicitud';
                $setEvnto['id_ref'] = $request->id_solicitud;
                GacBitacoraEventos::create($setEvnto);

                return $this->sendResponse('Se ha rechazado la solicitud con exito');
            }
            /* Si el usuario aprobo la solicitud  */
            $solicitud->id_usuario_revisor = $request->id_usuario;
            $solicitud->fecha_id_usuario_revisor = now();
            $solicitud->autorizo_usuario_revisor = 1;
            $solicitud->comentarios_usuario_revisor = $request->comentarios;
            $solicitud->id_estatus = 2;
            $solicitud->save();
            $documentos = GacDocumentosSolicitud::where('id_solicitud',$solicitud->id)->get()->all();
            
            $usuarioCreador =  DB::connection('mysql_dirac')->table('usuarios_dirac')->where('id_usuario', $solicitud->solicita )->where('status', 1 )->get()->first();
            $usuarioBeneficiario =  DB::connection('mysql_dirac')->table('usuarios_dirac')->where('id_usuario', $solicitud->beneficiario )->where('status', 1 )->get()->first();
            /* Se manda mensaje al  creador*/
            $this->enviaMensajeCreadorAprobacion(
                $usuarioCreador, 
                $solicitud->id, 
                $solicitud->importe_pesos,
                $usuarioCreador->nombre .' ' . $usuarioCreador->apellidos,
                $solicitud->descripcion,
                "La solicitud fue aprobada por el  {$request->usuario_nombre}" 
            );
            /* Si el solicitante y el beneficiario son personas diferentes al beneficiario tambien se le manda mensaje */
            if($usuarioBeneficiario){
                if($usuarioCreador->id_usuario !== $usuarioBeneficiario->id_usuario){
                    $this->enviaMensajeBeneficiarioAprobacion(
                        $usuarioBeneficiario, 
                        $solicitud->id, 
                        $solicitud->importe_pesos,
                        $usuarioCreador->nombre .' ' . $usuarioCreador->apellidos,
                        $solicitud->descripcion,
                       "La solicitud creada por el usuario: {$usuarioCreador->nombre} {$usuarioCreador->apellidos}, fue aprobada por el revisor: {$request->usuario_nombre}" , 
                    );
                }
            }

            $UsuariosPagadores = GacPerfilSolicitud::where('id_perfil', 3)->get()->all();
                foreach ($UsuariosPagadores as $key => $value) {
                    $usuarioNextPagador =  DB::connection('mysql_dirac')->table('usuarios_dirac')->where('id_usuario', $value->id_usuario )->where('status', 1 )->get()->first();

                    $this->enviaMensajePagador(
                        $usuarioNextPagador, 
                        $solicitud->id, 
                        $solicitud->importe_pesos,
                        $usuarioCreador->nombre .' ' . $usuarioCreador->apellidos,
                        $solicitud->descripcion,
                        $request->usuario_nombre
                    );

                }

               /* Se registra el evento */
               $setEvnto['evento'] = 'Aprobación de solicitud por revisor fiscal';
               $setEvnto['descripcion'] = "El revisor {$request->usuario_nombre}, ha aprobado la solicitud, con los siguientes comentarios ({$request->comentarios})";
               $setEvnto['id_usuario'] = $request->id_usuario;
               $setEvnto['tipo'] = 'Aprobación rechazo por revisor fiscal';
               $setEvnto['id_tabla'] = 'gac_solicitud';
               $setEvnto['id_ref'] = $request->id_solicitud;
               GacBitacoraEventos::create($setEvnto);

              return $this->sendResponse('Se ha aprobado la solicitud con exito');
        } catch (\Throwable $th) {
            return $this->sendError('Error', $th, 500);
        }
    }

    public function cambioEnSolicitudPagador(Request $request){
        try{
            $input = $request->all();
            $validator = Validator::make($input, [
                'id_solicitud' => 'required', // el id de la solicitud que se va a aprobar
                'id_usuario' => 'required', // el id del usuario que aprueba o rechaza la solicitud
                'usuario_nombre' => 'required',
                'comentarios' => 'required'
            ]);
            if ($validator->fails()) {
                return $this->sendError('Todos los campos son requeridos', $validator->errors(), 500);
            }
            $solicitud = GacSolicitud::where('id',$request->id_solicitud)->get()->first();
            if(!$solicitud){
                return $this->sendError('La solicitud que desea actualizar no existe', [], 404);
            }
            /* Si el usuario aprobo la solicitud  */
            $solicitud->id_usuario_pagada = $request->id_usuario;
            $solicitud->fecha_id_usuario_pagada = now();
            $solicitud->autorizo_usuario_pagada = 1;
            $solicitud->comentarios_usuario_pagada = $request->comentarios;
            $solicitud->id_estatus = 5;
            $solicitud->save();
            $documentos = GacDocumentosSolicitud::where('id_solicitud',$solicitud->id)->get()->all();
            
            $usuarioCreador =  DB::connection('mysql_dirac')->table('usuarios_dirac')->where('id_usuario', $solicitud->solicita )->where('status', 1 )->get()->first();
            $usuarioBeneficiario =  DB::connection('mysql_dirac')->table('usuarios_dirac')->where('id_usuario', $solicitud->beneficiario )->where('status', 1 )->get()->first();
            /* Se manda mensaje al  creador*/
            $this->enviaMensajeCreadorAprobacion(
                $usuarioCreador, 
                $solicitud->id, 
                $solicitud->importe_pesos,
                $usuarioCreador->nombre .' ' . $usuarioCreador->apellidos,
                $solicitud->descripcion,
                "La solicitud fue aprobada por el pagador: {$request->usuario_nombre}, el proceso ha terminado" 
            );
            /* Si el solicitante y el beneficiario son personas diferentes al beneficiario tambien se le manda mensaje */
            if($usuarioBeneficiario){
                if($usuarioCreador->id_usuario !== $usuarioBeneficiario->id_usuario){
                    $this->enviaMensajeBeneficiarioAprobacion(
                        $usuarioBeneficiario, 
                        $solicitud->id, 
                        $solicitud->importe_pesos,
                        $usuarioCreador->nombre .' ' . $usuarioCreador->apellidos,
                        $solicitud->descripcion,
                       "La solicitud creada por el usuario: {$usuarioCreador->nombre} {$usuarioCreador->apellidos}, fue aprobada por el pagador: {$request->usuario_nombre}, el proceso ha terminado" , 
                    );
                }
            }
            /* Se registra el evento */
            $setEvnto['evento'] = 'Aprobación de solicitud por el pagador, proceso terminado';
            $setEvnto['descripcion'] = "El pagador {$request->usuario_nombre}, ha aprobado la solicitud, con los siguientes comentarios ({$request->comentarios})";
            $setEvnto['id_usuario'] = $request->id_usuario;
            $setEvnto['tipo'] = 'Aprobación rechazo por el pagador';
            $setEvnto['id_tabla'] = 'gac_solicitud';
            $setEvnto['id_ref'] = $request->id_solicitud;
            GacBitacoraEventos::create($setEvnto);
            $baseDirectory = storage_path("/app/documentos/GAC/{$request->id_solicitud}");
            $files = glob($baseDirectory . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    $text = basename($file);
                    preg_match('/^(\d+)_/', $text, $match);
                    $number = isset($match[1]) ? $match[1] : null;
                    if($number !== null){
                        $doc = GacDocumentosSolicitud::where('id', $number )->where('es_valido_revisor', 0)->where('id_solicitud', $request->id_solicitud)->first(); 
                        if($doc){
                            $doc->delete();
                            unlink($file);
                        }
                    }
                }
            }
            return $this->sendResponse('Se ha aprobado la solicitud con exito');
        } catch (\Throwable $th) {
            return $this->sendError('Error', $th, 500);
        }
    }

    public function generarZipSolicitud(Request $request){
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                'id_solicitud' => 'required'
            ]);
            if ($validator->fails()) {
                return $this->sendError('El id de la solicitud es requerido', $validator->errors(), 500);
            }
            $solicitud = GacSolicitud::where('id',$request->id_solicitud)->get()->first();
            if(!$solicitud){
                return $this->sendError('La solicitud que desea actualizar no existe', [], 404);
            }
            $zip = new ZipArchive;
            $fileName = time(). "documentos_solicitud_{$solicitud->id} .zip";
            if (!File::isDirectory(storage_path("/app/documentos/GAC/{$request->id_solicitud}"))) {
                return $this->sendError('Aun no hay documentos asociados a esta solicitud', [], 500);
            }
            $files = File::files(storage_path("/app/documentos/GAC/{$request->id_solicitud}"));
            $baseDirectory = storage_path("/app/documentos/GAC/{$request->id_solicitud}");
            //borramos los zip antes creados
             $s = glob($baseDirectory . '/*.zip');
             foreach ($s as $file) {
                 if (is_file($file)) {
                     unlink($file);
                 }
             }
            if ($zip->open(storage_path("app/documentos/GAC/{$request->id_solicitud}/{$fileName}"), ZipArchive::CREATE) === true) {
                $files = glob($baseDirectory . '/*');
                foreach ($files as $file) {
                    if (is_file($file)) {
                        $text = basename($file);
                        preg_match('/^(\d+)_/', $text, $match);
                        $number = isset($match[1]) ? $match[1] : null;
                        if($number !== null){
                            $doc = GacDocumentosSolicitud::where('id', $number )->where('es_valido_revisor', 1)->where('id_solicitud', $request->id_solicitud)->first(); 
                            if($doc){
                                $extension = pathinfo($file, PATHINFO_EXTENSION);
                                if (strtolower($extension) !== 'zip') {
                                    $zip->addFile($file, basename($file));
                                }
                            }
                        }
                    }
                }
                $zip->close();
            }
            $publicUrl = "storage/app/documentos/GAC/{$request->id_solicitud}/{$fileName}";
            return $this->sendResponse($publicUrl);
        } catch (\Throwable $th) {
            return $this->sendError('Error al generar el paquete', $th, 500);
        }
    }

    public function firmarDocumento(Request $request){
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                'id_usuario' => 'required',
                'firma' => 'required'
            ]);
            if ($validator->fails()) {
                return $this->sendError('El id de la solicitud es requerido', $validator->errors(), 500);
            }
            $firma =  DB::connection('mysql_dirac')->table('dcmx_firmas_dirac')->where('clave', $request->firma )->get()->first();
            if (!$firma) {
                return $this->sendError('La clave proporcionada es incorrecta, el acceso a este módulo es denegado', [], 404);
            }
            return $this->sendResponse(true);
        } catch (\Throwable $th) {
            return $this->sendError('Error al firmar la solicitud', $th, 500);
        }
    }

    public function atualizaTipoSolicitud(Request $request){
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                'id_solicitud' => 'required',
                'id_tipo_solicitud' => 'required'
            ]);
            if ($validator->fails()) {
                return $this->sendError('El id de la solicitud es requerido', $validator->errors(), 500);
            }
            $solicitud = GacSolicitud::where('id',$request->id_solicitud)->get()->first();
            if(!$solicitud){
                return $this->sendError('La solicitud que desea actualizar no existe', [], 404);
            }
            $solicitud->id_tipo_solicitud = $request->id_tipo_solicitud;
            $solicitud->save();
            return $this->sendResponse('Exito al actualizar el tipo de solicitud');
        } catch (\Throwable $th) {
            return $this->sendError('Error al actualizar el tipo de la solicitud', $th, 500);
        }
    }


    public function actualizaIdConcepto(Request $request){
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                'id_solicitud' => 'required',
                'id_concepto' => 'required'
            ]);
            if ($validator->fails()) {
                return $this->sendError('El id de la solicitud es requerido', $validator->errors(), 500);
            }
            $solicitud = GacSolicitud::where('id',$request->id_solicitud)->get()->first();
            if(!$solicitud){
                return $this->sendError('La solicitud que desea actualizar no existe', [], 404);
            }
            $solicitud->id_concepto = $request->id_concepto;
            $solicitud->save();
            return $this->sendResponse('Exito al actualizar el concepto');
        } catch (\Throwable $th) {
            return $this->sendError('Error al actualizar el concepto', $th, 500);
        }
    }

    public function solicitaCargaDocumental(Request $request){
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                'id_solicitud' => 'required'
            ]);
            if ($validator->fails()) {
                return $this->sendError('El id de la solicitud es requerido', $validator->errors(), 500);
            }
            $solicitud = GacSolicitud::where('id',$request->id_solicitud)->get()->first();
            if(!$solicitud){
                return $this->sendError('La solicitud que desea actualizar no existe', [], 404);
            }
            $solicitud->id_estatus = 6;
            $solicitud->save();
            $usuarioCreador =  DB::connection('mysql_dirac')->table('usuarios_dirac')->where('id_usuario', $solicitud->solicita )->where('status', 1 )->get()->first();
            $usuarioBeneficiario =  DB::connection('mysql_dirac')->table('usuarios_dirac')->where('id_usuario', $solicitud->beneficiario )->where('status', 1 )->get()->first();
            /* Se manda mensaje al  creador*/
            $this->enviaMensajeCreadorAprobacion(
                $usuarioCreador, 
                $solicitud->id, 
                $solicitud->importe_pesos,
                $usuarioCreador->nombre .' ' . $usuarioCreador->apellidos,
                $solicitud->descripcion, 
                "Por favor carga los documentos requeridos para continuar con el proceso" 
            );    
            /* Si el solicitante y el beneficiario son personas diferentes al beneficiario tambien se le manda mensaje */
            if($usuarioBeneficiario){
                if($usuarioCreador->id_usuario !== $usuarioBeneficiario->id_usuario){
                    $this->enviaMensajeBeneficiarioAprobacion(
                        $usuarioBeneficiario, 
                        $solicitud->id, 
                        $solicitud->importe_pesos,
                        $usuarioCreador->nombre .' ' . $usuarioCreador->apellidos,
                        $solicitud->descripcion,
                        "Por favor carga los documentos requeridos para continuar con el proceso" , 
                    );
                }
            }
        } catch (\Throwable $th) {
            return $this->sendError('Error al Solicitar la carga documental', $th, 500);
        }
    }


    public function notificaNomina(Request $request){
        try{ 
            $input = $request->all();
            $validator = Validator::make($input, [
                'id_solicitud' => 'required', 
                'id_usuario_notifica' => 'required',
                'importe' => 'required',
            ]);
            if ($validator->fails()) {
                return $this->sendError('Todos los campos son requeridos', $validator->errors(), 500);
            }
            $solicitud = GacSolicitud::where('id',$request->id_solicitud)->get()->first();
            $usuarioCreador =  DB::connection('mysql_dirac')->table('usuarios_dirac')->where('id_usuario', $solicitud->solicita )->where('status', 1 )->get()->first();
            $usuarioBeneficiario =  DB::connection('mysql_dirac')->table('usuarios_dirac')->where('id_usuario', $solicitud->beneficiario )->where('status', 1 )->get()->first();

            $usuariosNomina = GacPerfilSolicitud::where('id_perfil', 4)->get()->all();
            foreach ($usuariosNomina as $key => $value) {
                $usuaruiNextNomina =  DB::connection('mysql_dirac')->table('usuarios_dirac')->where('id_usuario', $value->id_usuario )->where('status', 1 )->get()->first();
                if($usuaruiNextNomina){
                    $this->enviaMensajeSolicitaDescuentoNomina(
                        $usuaruiNextNomina, 
                        $solicitud->id, 
                        $request->importe,
                        $usuarioCreador->nombre .' ' . $usuarioCreador->apellidos,
                        $solicitud->descripcion,
                    );
                    $notificaion['id_solicitud'] = $solicitud->id;
                    $notificaion['importe'] = $request->importe;
                    $notificaion['fecha_registro'] = now();
                    $notificaion['id_usuario_notifica'] = $request->id_usuario_notifica;
                    $notificaion['id_usuario_recibe_notificacion'] = $usuaruiNextNomina->id_usuario;
                    GacNotificaNomina::create($notificaion);
                }
            }
            return $this->sendResponse('Exito al notificar a nomina');
        }catch(\Throwable $th){
            return $this->sendError('Error', $th, 500);
        }
    }


    public function notificaPorDias(){
         try{ 
            $solicitudesDias =  DB::select('
                select a.*,b.dias_notifica_pago, DATEDIFF(NOW(), a.fecha_id_usuario_revisor) AS dias_transcurridos
                from gac_solicitud a 
                inner join gac_cat_tipo_solicitud b on a.id_tipo_solicitud = b.id
                where b.dias_notifica_pago != ? and DATEDIFF(NOW(), a.fecha_id_usuario_revisor) >= b.dias_notifica_pago', [0]
            );
            if(count($solicitudesDias) === 0){
                return $this->sendResponse(true);
            }
            foreach($solicitudesDias as $key => $value){
                $idCodificado = $this->cifrarTexto($value->id, env('CLAVE_HASHIG'));
                $solicitudesDias[$key]->id = $idCodificado;
            }
            $usuariosNomina = GacPerfilSolicitud::where('id_perfil', 2)->get()->all();
            foreach ($usuariosNomina as $key => $value) {
                $usuaruiNextNomina =  DB::connection('mysql_dirac')->table('usuarios_dirac')->where('id_usuario', $value->id_usuario )->where('status', 1 )->get()->first();
                if($usuaruiNextNomina){
                    $nombre = $usuaruiNextNomina->nombre . ' ' . $usuaruiNextNomina->apellidos;
                    $idUsuario = $this->cifrarTexto($usuaruiNextNomina->id_usuario, env('CLAVE_HASHIG'));
                    Mail::to($value->correo)->send(new CorreoSolicitudNotificaNominaGac(
                        $solicitudesDias, 
                        $nombre,
                        $idUsuario
                    ));
                    $to = "+52{$value->telefono}";
                    $body = "Hola {$nombre}, arjion te notifica";
                    $body1 = "Se han enviado a tu correo las solicitudes pendientes de descuento, por favor entra a tu correo electronico para revisar con detalle";
                    $this->senWhats->sendChatMessage($to, $body);
                    $this->senWhats->sendChatMessage($to, $body1);
                }
            }
            return $this->sendResponse(true);
	}catch(\Throwable $th){
            return $this->sendError('Error', $th, 500);
        } 
    }

}

