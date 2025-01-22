<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CarruselController extends Controller
{

    public function obtenerTipoArchivo($rutaArchivo)
    {
        $extension = pathinfo($rutaArchivo, PATHINFO_EXTENSION);
        $extensionesImagen = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
        $extensionesVideo = ['mp4', 'avi', 'mov', 'wmv', 'flv', 'mkv', 'webm'];
        if (in_array(strtolower($extension), $extensionesImagen)) {
            return 'image';
        } elseif (in_array(strtolower($extension), $extensionesVideo)) {
            return 'video';
        } else {
            return '';
        }
    }

    public function show(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'id' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->sendError('Todos los campos son requeridos', $validator->errors(), 500);
        }
        $mediaItems = DB::connection('mysql_banner')->table('info_carrusel_banner')->where('texto', null)->where('piso', 'LIKE', "{$request->id},%")
            ->orWhere('piso', 'LIKE', "%,{$request->id},%")
            ->orWhere('piso', 'LIKE', "%,{$request->id}")
            ->orWhere('piso', $request->id)->get()->all();
        foreach ($mediaItems as $key => $value) {
            $tipo = $this->obtenerTipoArchivo($value->ruta_media);
            $mediaItems[$key]->type = $tipo;
            $mediaItems[$key]->duration = 3000;
        }
        $banner = DB::connection('mysql_banner')->table('info_carrusel_banner')->where('ruta_media', null)
            ->where('piso', 'LIKE', "{$request->id},%")
            ->orWhere('piso', 'LIKE', "%,{$request->id},%")
            ->orWhere('piso', 'LIKE', "%,{$request->id}")
            ->orWhere('piso', $request->id)->get()->all();
        $apiUrlDocumentos = "http://localhost/dirac.arjion.api/";
        return view('infodirac', ['elements' => $mediaItems, 'apiUrlDocumentos' => $apiUrlDocumentos, 'banner' => $banner]);
    }

    public function selectPlayList()
    {
        $elementos = DB::connection('mysql_banner')->table('pisos')->get()->all();
        foreach ($elementos as $key => $value) {
            $medias = DB::connection('mysql_banner')->table('info_carrusel_banner')->where('piso', 'LIKE', "{$value->id},%")
                ->orWhere('piso', 'LIKE', "%,{$value->id},%")
                ->orWhere('piso', 'LIKE', "%,{$value->id}")
                ->orWhere('piso', $value->id)->get()->all();
            $elementos[$key]->elementos = count($medias);
        }
        return view('carrusel', ['elements' => $elementos]);
    }
}