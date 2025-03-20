<?php


namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\GacEquivalenciaMonedaExtDol;
use App\Models\GacCatFormaPago;
use App\Models\GacCatConceptos;
use App\Models\GacTipoCambioDolar;
use App\Models\GacCatPerfiles;
use App\Models\ComProveedores;

class GacCatalogosController extends BaseController
{

    public function getGacEquivalenciaMonedaExtDol()
    {
        try {
            $equivalenciaMonedaExtDol = GacEquivalenciaMonedaExtDol::get()->all();
            return $this->sendResponse($equivalenciaMonedaExtDol);
        } catch (\Throwable $th) {
            return $this->sendError('Error', $th, 500);
        }
    }

    public function getGacCatFormaPago()
    {
        try {
            $catFormaPago = GacCatFormaPago::get()->all();
            return $this->sendResponse($catFormaPago);
        } catch (\Throwable $th) {
            return $this->sendError('Error', $th, 500);
        }
    }
    public function getGacCatConceptos()
    {
        try {
            $catConceptos = GacCatConceptos::get()->all();
            return $this->sendResponse($catConceptos);
        } catch (\Throwable $th) {
            return $this->sendError('Error', $th, 500);
        }
    }


    public function getGacProyectosSgi(){
        try {
           $proyectos_sgi =  DB::connection('mysql_dirac')->table('proyectos_sgi')->where('dashboard', 1)->get()->all();
           return $this->sendResponse($proyectos_sgi);
        } catch (\Throwable $th) {
            return $this->sendError('Error', $th, 500);
        }
    }


    public function getGacBeneficiarios(Request $request){
        try {
            $input = $request->all();
            /* iNOUT DE  QUE MANBDA  EL USUARIO LA FECHA */
            $validator = Validator::make($input, [
                'id_director_area' => 'required'
            ]);
            if ($validator->fails()) {
                return $this->sendError('El id del area es requerido', $validator->errors(), 500);
            }
           $proyectos_sgi =  DB::connection('mysql_dirac')->table('usuarios_dirac')->where('id_director_area', $request->id_director_area )->where('status', 1 )->get()->all();
           return $this->sendResponse($proyectos_sgi);
        } catch (\Throwable $th) {
            return $this->sendError('Error', $th, 500);
        }
    }


    public function getGactodosLosUsuarios(Request $request){
        try {
           $users =  DB::connection('mysql_dirac')->table('usuarios_dirac')->where('status', 1 )->get()->all();
           return $this->sendResponse($users);
        } catch (\Throwable $th) {
            return $this->sendError('Error', $th, 500);
        }
    }
    

    public function getGacEmpresas(){
        try {
           $catEmpresas =  DB::connection('mysql_dirac')->table('cat_empresas')->get()->all();
           return $this->sendResponse($catEmpresas);
        } catch (\Throwable $th) {
            return $this->sendError('Error', $th, 500);
        }        
    }

    public function getGacTipoCambioDolar(){
        try {
           $catEmpresas =  GacTipoCambioDolar::orderBy('fecha', 'desc')->first();
           return $this->sendResponse($catEmpresas);
        } catch (\Throwable $th) {
            return $this->sendError('Error', $th, 500);
        }
    }

    public function getGacCatPerfiles(){
        try {
           $catPerfiles =  GacCatPerfiles::get()->all();
           return $this->sendResponse($catPerfiles);
        } catch (\Throwable $th) {
            return $this->sendError('Error', $th, 500);
        }
    }

    public function getGacProveedores(){
        try {
           $proveedores =  ComProveedores::get()->all();
           return $this->sendResponse($proveedores);
        } catch (\Throwable $th) {
            return $this->sendError('Error', $th, 500);
        }
    }
 
    public function a(){

    }
    

}


