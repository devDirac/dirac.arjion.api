<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\BannerCarruselInfoController;
use App\Http\Controllers\API\GacConceptosController;
use App\Http\Controllers\API\GacTipoSolicitudController;
use App\Http\Controllers\API\GacFormaPagoController;
use App\Http\Controllers\API\GacCatalogosController;
use App\Http\Controllers\API\GacActualizaMonedasController;
use App\Http\Controllers\API\GacDocumentosCotroller;
use App\Http\Controllers\API\GacUserController;
use App\Http\Controllers\API\GacSolicitudController;
use App\Http\Controllers\API\GacProveedoresController;
use App\Http\Controllers\API\AsistenteInteligenteController;

/* info carrusel and banner */
Route::post('setInfoBanner', [BannerCarruselInfoController::class, 'setInfoBanner']);
Route::get('getInfoBanner', [BannerCarruselInfoController::class, 'getInfoBanner']);
Route::post('updateInfoBanner', [BannerCarruselInfoController::class, 'updateInfoBanner']);
Route::get('getPlayList', [BannerCarruselInfoController::class, 'getPlayList']);
Route::delete('deleteInfoBanner', [BannerCarruselInfoController::class, 'deleteInfoBanner']);
Route::post('addPiso', [BannerCarruselInfoController::class, 'addPiso']);
Route::post('editPiso', [BannerCarruselInfoController::class, 'editPiso']);
Route::delete('deletePiso', [BannerCarruselInfoController::class, 'deletePiso']);
Route::get('getContenidoParaAsignar', [BannerCarruselInfoController::class, 'getContenidoParaAsignar']);
Route::post('asignarContenido', [BannerCarruselInfoController::class, 'asignarContenido']);
Route::get('getContenidoInformacion', [BannerCarruselInfoController::class, 'getContenidoInformacion']);

/* Catalago de conceptos */
Route::get('getAllConceptos', [GacConceptosController::class, 'getAllConceptos']);
Route::post('setConcepto', [GacConceptosController::class, 'setConcepto']);
Route::put('editConcepto', [GacConceptosController::class, 'editConcepto']);
Route::put('deleteConcepto', [GacConceptosController::class, 'deleteConcepto']);


/* Catalago de tipos de solicitud */
Route::get('getAllTiposSolicitudes', [GacTipoSolicitudController::class, 'getAllTiposSolicitudes']);
Route::post('setTiposSolicitud', [GacTipoSolicitudController::class, 'setTiposSolicitud']);
Route::put('editTiposSolicitud', [GacTipoSolicitudController::class, 'editTiposSolicitud']);
Route::put('deleteTiposSolicitud', [GacTipoSolicitudController::class, 'deleteTiposSolicitud']);


/* Catalago forma de pago */
Route::get('getAllFormasPago', [GacFormaPagoController::class, 'getAllFormasPago']);
Route::post('setFormaPago', [GacFormaPagoController::class, 'setFormaPago']);
Route::put('editFormaPago', [GacFormaPagoController::class, 'editFormaPago']);
Route::put('deleteoFrmaPago', [GacFormaPagoController::class, 'deleteoFrmaPago']);

/* Catalogos genericos no especiales */
Route::get('getGacEquivalenciaMonedaExtDol', [GacCatalogosController::class, 'getGacEquivalenciaMonedaExtDol']);
Route::get('getGacCatFormaPago', [GacCatalogosController::class, 'getGacCatFormaPago']);
Route::get('getGacCatConceptos', [GacCatalogosController::class, 'getGacCatConceptos']);
Route::get('getGacProyectosSgi', [GacCatalogosController::class, 'getGacProyectosSgi']);
Route::get('getGacBeneficiarios', [GacCatalogosController::class, 'getGacBeneficiarios']);
Route::get('getGactodosLosUsuarios', [GacCatalogosController::class, 'getGactodosLosUsuarios']);
Route::get('getGacEmpresas', [GacCatalogosController::class, 'getGacEmpresas']);
Route::get('getGacTipoCambioDolar', [GacCatalogosController::class, 'getGacTipoCambioDolar']);
Route::get('getGacCatPerfiles', [GacCatalogosController::class, 'getGacCatPerfiles']);
Route::get('getGacProveedores', [GacCatalogosController::class, 'getGacProveedores']);

/* Para los proveedores */
Route::post('setProveedor', [GacProveedoresController::class, 'setProveedor']);

/* Para actualizar catalogos de moneda */
Route::post('setGacEquivalenciaMonedaExtDol', [GacActualizaMonedasController::class, 'setGacEquivalenciaMonedaExtDol']);
Route::post('setGacTipoCambioDolar', [GacActualizaMonedasController::class, 'setGacTipoCambioDolar']);

/* Para el analisis de los documentos  */
Route::post('gacAddMedia', [GacDocumentosCotroller::class, 'gacAddMedia']);
Route::post('getCritscoAnalisis', [GacDocumentosCotroller::class, 'getCritscoAnalisis']);
Route::delete('deleteDocument', [GacDocumentosCotroller::class, 'deleteDocument']);


/* Para la gestion del usuario que entra al sistema */
Route::get('getUserIdHash', [GacUserController::class, 'getUserIdHash']);
Route::get('gacGetUserData', [GacUserController::class, 'gacGetUserData']);
Route::get('getGetUsuariosAdministradores', [GacUserController::class, 'getGetUsuariosAdministradores']);
Route::get('getGetUsuariosNomina', [GacUserController::class, 'getGetUsuariosNomina']);
Route::get('getGetUsuariosPerfilesSolicitud', [GacUserController::class, 'getGetUsuariosPerfilesSolicitud']);
Route::post('setPerfilSolicitud', [GacUserController::class, 'setPerfilSolicitud']);
Route::post('setPerfilSolicitudNomina', [GacUserController::class, 'setPerfilSolicitudNomina']);
Route::get('gacGetSolicitudesJefesArea', [GacUserController::class, 'gacGetSolicitudesJefesArea']);
Route::get('gacGetSolicitudesAdmins', [GacUserController::class, 'gacGetSolicitudesAdmins']);
Route::post('addBanco', [GacUserController::class, 'addBanco']);

/* Para lo relacionado con la solicitud perse */
Route::post('setSolicitud', [GacSolicitudController::class, 'setSolicitud']);
Route::get('getDetalleSolicitud', [GacSolicitudController::class, 'getDetalleSolicitud']);
Route::post('setDocumentoSolicitud', [GacSolicitudController::class, 'setDocumentoSolicitud']);
Route::post('apruebaSolicitudJefeDirecto', [GacSolicitudController::class, 'apruebaSolicitudJefeDirecto']);
Route::post('cambioEnSolicitudAutorizador', [GacSolicitudController::class, 'cambioEnSolicitudAutorizador']);
Route::post('solicitaAprobacionDireccionGeneral', [GacSolicitudController::class, 'solicitaAprobacionDireccionGeneral']);
Route::post('notificaRevisoresFiscales', [GacSolicitudController::class, 'notificaRevisoresFiscales']);
Route::post('handleDocumentosRevisorRevisa', [GacSolicitudController::class, 'handleDocumentosRevisorRevisa']);
Route::post('cambioEnSolicitudRevisor', [GacSolicitudController::class, 'cambioEnSolicitudRevisor']);
Route::post('cambioEnSolicitudPagador', [GacSolicitudController::class, 'cambioEnSolicitudPagador']);
Route::post('generarZipSolicitud', [GacSolicitudController::class, 'generarZipSolicitud']);
Route::post('firmarDocumento', [GacSolicitudController::class, 'firmarDocumento']);
Route::put('atualizaTipoSolicitud', [GacSolicitudController::class, 'atualizaTipoSolicitud']);
Route::put('actualizaIdConcepto', [GacSolicitudController::class, 'actualizaIdConcepto']);
Route::post('solicitaCargaDocumental', [GacSolicitudController::class, 'solicitaCargaDocumental']);
Route::post('notificaRevisoresFiscalesAutorizador', [GacSolicitudController::class, 'notificaRevisoresFiscalesAutorizador']);
Route::post('notificaNomina', [GacSolicitudController::class, 'notificaNomina']);
Route::get('notificaPorDias', [GacSolicitudController::class, 'notificaPorDias']);
/* Asistente inteligente*/
Route::post('guardaPregunta', [AsistenteInteligenteController::class, 'almacenarContenidoConEmbedding']);
Route::post('chat', [AsistenteInteligenteController::class, 'chat']);
Route::post('setPreguntaCorrecta', [AsistenteInteligenteController::class, 'setPreguntaCorrecta']);