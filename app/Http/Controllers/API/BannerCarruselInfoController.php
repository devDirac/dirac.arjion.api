<?php


namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class BannerCarruselInfoController extends BaseController
{

    public function getInfoBanner(Request $request)
    {
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                'piso' => 'required'
            ]);
            if ($validator->fails()) {
                return $this->sendError('Todos los campos son requeridos', $validator->errors(), 500);
            }
            $elementos = DB::connection('mysql_banner')->table('info_carrusel_banner')->where('piso', 'LIKE', "{$request->piso},%")
                ->orWhere('piso', 'LIKE', "%,{$request->piso},%")
                ->orWhere('piso', 'LIKE', "%,{$request->piso}")
                ->orWhere('piso', $request->piso)->get()->all();
            return $this->sendResponse($elementos);
        } catch (\Throwable $th) {
            return $this->sendError('Error', $th, 500);
        }
    }

    public function setInfoBanner(Request $request)
    {
        try {
            $elemento = DB::connection('mysql_banner')->table('info_carrusel_banner')->insertGetId([
                'texto' => $request->has('texto') ? $request->texto : null,
                'piso' => $request->has('piso') ? $request->piso : null,
                'fecha_registro' => now(),
                'id_usuario'=> $request->user_id
            ]);
            if ($request->has('file')) {
                $file = $request->file('file');
                $file->storeAs("documentos", $elemento . '_' . $file->getClientOriginalName());
                asset("documentos/{$elemento}_{$file->getClientOriginalName()}");
                $ruta = "storage/app/documentos/{$elemento}_{$file->getClientOriginalName()}";
                DB::connection('mysql_banner')->select("update info_carrusel_banner set ruta_media = ? where id=? ", [
                    $ruta,
                    $elemento,
                ]);

            }
            return $this->sendResponse($elemento);
        } catch (\Throwable $th) {
            return $this->sendError('Error', $th, 500);
        }
    }

    public function updateInfoBanner(Request $request)
    {
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                'id' => 'required',
                // 'piso' => 'required'
            ]);
            if ($validator->fails()) {
                return $this->sendError('Todos los campos son requeridos', $validator->errors(), 500);
            }
            $elemento = DB::connection('mysql_banner')->table('info_carrusel_banner')->where('id', $request->id)->update([
                'texto' => $request->has('texto') ? $request->texto : null,
                //'piso' => $request->piso,
                'fecha_registro' => now()
            ]);
            if ($request->has('file')) {
                $elementoEdit = $elemento = DB::connection('mysql_banner')->table('info_carrusel_banner')->where('id', $request->id)->get()->first();
                $ruta = str_replace('storage/app/', '', $elementoEdit->ruta_media !== null ? $elemento->ruta_media : '');
                if (Storage::exists("{$ruta}")) {
                    Storage::delete("{$ruta}");
                }
                $file = $request->file('file');
                $file->storeAs("documentos", $file->getClientOriginalName());
                asset("documentos/{$file->getClientOriginalName()}");
                $ruta = "storage/app/documentos/{$file->getClientOriginalName()}";
                DB::connection('mysql_banner')->select("update info_carrusel_banner set ruta_media = ? where id=? ", [
                    $ruta,
                    $request->id,
                ]);
            }
            return $this->sendResponse('exito al actualizar el registro');
        } catch (\Throwable $th) {
            return $this->sendError('Error', $th, 500);
        }
    }

    public function removeNumberFromString($string, $numberToRemove)
    {
        $array = explode(',', $string);
        $filteredArray = array_filter($array, function ($value) use ($numberToRemove) {
            return $value != $numberToRemove;
        });
        return implode(',', $filteredArray);
    }

    public function deleteInfoBanner(Request $request)
    {
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                'id' => 'required',
                'idPlayList' => 'required'
            ]);
            if ($validator->fails()) {
                return $this->sendError('Todos los campos son requeridos', $validator->errors(), 500);
            }
            $elemento = DB::connection('mysql_banner')->table('info_carrusel_banner')->where('id', $request->id)->get()->first();
            if (!$elemento) {
                return $this->sendError('El elemento que desea eliminar no existe', [], 500);
            }
            $ruta = str_replace('storage/app/', '', $elemento->ruta_media !== null ? $elemento->ruta_media : '');
            if (Storage::exists("{$ruta}")) {
                Storage::delete("{$ruta}");
            }
            DB::connection('mysql_banner')->table('info_carrusel_banner')->delete([
                'id' => $elemento->id
            ]);
            return $this->sendResponse('exito al eliminar el registro');
        } catch (\Throwable $th) {
            return $this->sendError('Error', $th, 500);
        }
    }

    public function getPlayList(Request $request)
    {
        try {
            $elementos = DB::connection('mysql_banner')->table('pisos')->get()->all();
            foreach ($elementos as $key => $value) {
                $medias = DB::connection('mysql_banner')->table('info_carrusel_banner')->where('piso', 'LIKE', "{$value->id},%")
                    ->orWhere('piso', 'LIKE', "%,{$value->id},%")
                    ->orWhere('piso', 'LIKE', "%,{$value->id}")
                    ->orWhere('piso', $value->id)->get()->all();
                $elementos[$key]->elementos = count($medias);
            }
            return $this->sendResponse($elementos);
        } catch (\Throwable $th) {
            return $this->sendError('Error', $th, 500);
        }
    }

    public function addPiso(Request $request)
    {
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                'piso' => 'required',
                'user_id'=> 'required'
            ]);
            if ($validator->fails()) {
                return $this->sendError('Todos los campos son requeridos', $validator->errors(), 500);
            }
            DB::connection('mysql_banner')->table('pisos')->insertGetId([
                'piso' => $request->piso,
                'fecha_registro' => now(),
                'id_usuario'=> $request->user_id
            ]);
            return $this->sendResponse('Exito al agregar el elemento');
        } catch (\Throwable $th) {
            return $this->sendError('Error', $th, 500);
        }
    }

    public function editPiso(Request $request)
    {
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                'id' => 'required',
                'piso' => 'required'
            ]);
            if ($validator->fails()) {
                return $this->sendError('Todos los campos son requeridos', $validator->errors(), 500);
            }
            $elemento = DB::connection('mysql_banner')->table('pisos')->where('id', $request->id)->get()->first();
            if (!$elemento) {
                return $this->sendError('El elemento que desea eliminar no existe', [], 500);
            }
            DB::connection('mysql_banner')->table('pisos')->where('id', $request->id)->update([
                'piso' => $request->piso
            ]);
            return $this->sendResponse('Exito al editar el elemento');
        } catch (\Throwable $th) {
            return $this->sendError('Error', $th, 500);
        }
    }


    public function deletePiso(Request $request)
    {
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                'id' => 'required'
            ]);
            if ($validator->fails()) {
                return $this->sendError('Todos los campos son requeridos', $validator->errors(), 500);
            }
            $play = DB::connection('mysql_banner')->table('pisos')->where('id', $request->id)->get()->first();
            if (!$play) {
                return $this->sendError('El elemento que desea eliminar no existe', [], 500);
            }
            $medias = DB::connection('mysql_banner')->table('info_carrusel_banner')->where('piso', 'LIKE', "{$play->id},%")
                ->orWhere('piso', 'LIKE', "%,{$play->id},%")
                ->orWhere('piso', 'LIKE', "%,{$play->id}")
                ->orWhere('piso', $play->id)->get()->all();

            if (count($medias) === 0) {
                DB::connection('mysql_banner')->table('pisos')->where('id', $request->id)->delete();
            } else {
                foreach ($medias as $value) {
                    $originalString = $value->piso;
                    $numberToRemove = $request->id;
                    $result = $this->removeNumberFromString($originalString, $numberToRemove);
                    DB::connection('mysql_banner')->table('info_carrusel_banner')->where('id', $value->id)->update([
                        'piso' => $result === '' ? null : $result

                    ]);
                }
                DB::connection('mysql_banner')->table('pisos')->where('id', $request->id)->delete();
            }
            return $this->sendResponse('Exito al eliminar el elemento');
        } catch (\Throwable $th) {
            return $this->sendError('Error', $th, 500);
        }
    }

    public function containsNumber($string, $number)
    {
        $array = explode(',', $string);
        return in_array($number, $array);
    }

    public function getContenidoParaAsignar(Request $request)
    {
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                'id' => 'required'
            ]);
            if ($validator->fails()) {
                return $this->sendError('Todos los campos son requeridos', $validator->errors(), 500);
            }
            $mediasDos = DB::connection('mysql_banner')->table('info_carrusel_banner')->get()->all();
            foreach ($mediasDos as $key => $value) {
                $mediasDos[$key]->check = $this->containsNumber($value->piso, $request->id);
            }
            $result = $mediasDos;
            return $this->sendResponse($result);
        } catch (\Throwable $th) {
            return $this->sendError('Error', $th, 500);
        }
    }

    public function getContenidoInformacion(Request $request)
    {
        try {
            $mediasDos = DB::connection('mysql_banner')->table('info_carrusel_banner')->get()->all();
            foreach ($mediasDos as $key => $value) {
                $mediasDos[$key]->check = false;
            }
            $result = $mediasDos;
            return $this->sendResponse($result);
        } catch (\Throwable $th) {
            return $this->sendError('Error', $th, 500);
        }
    }

    public function asignarContenido(Request $request)
    {
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                'elementos' => 'required',
                'id_play' => 'required'
            ]);
            if ($validator->fails()) {
                return $this->sendError('Todos los campos son requeridos', $validator->errors(), 500);
            }
            foreach ($request->elementos as $value) {
                if ($value['piso'] !== null) {
                    $originalString = $value['piso'];
                    $numberToRemove = $request->id_play;
                    $result = $this->removeNumberFromString($originalString, $numberToRemove);
                    DB::connection('mysql_banner')->table('info_carrusel_banner')->where('id', $value['id'])->update([
                        'piso' => $result === '' ? null : $result
                    ]);
                }
                if ($value['check']) {
                    $elementoEdit = DB::connection('mysql_banner')->table('info_carrusel_banner')->where('id', $value['id'])->get()->first();
                    DB::connection('mysql_banner')->table('info_carrusel_banner')->where('id', $elementoEdit->id)->update([
                        'piso' => $elementoEdit->piso === null ? $request->id_play : "{$elementoEdit->piso},{$request->id_play}"
                    ]);
                }
            }
            return $this->sendResponse('La operación se realizo exitosamente');
        } catch (\Throwable $th) {
            return $this->sendError('Error', $th, 500);
        }
    }

}


