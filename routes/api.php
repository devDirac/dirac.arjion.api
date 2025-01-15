<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\BannerCarruselInfoController;

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
