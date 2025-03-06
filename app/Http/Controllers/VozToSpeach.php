<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\API\BaseController as BaseController;
class VozToSpeach extends Controller
{

    public function index(){
        return view('voz');
    }
    
    public function metricas(Request $request){
        $data = DB::select('select COUNT(*) as notas from  apm_reportes_voz where id_usuario= ? and ids_resumen_generado is null', [$request->user]);
        return view('metricas',['notas'=>$data[0]->notas]);
    }

    public function consultaAdmin(){
        return view('consultaAdmin',[]);
    }

    public function getData(Request $request){
        try {
            $responses = new BaseController();
            $data = DB::select('select * from  apm_reportes_voz where id_usuario= ? and ids_resumen_generado is null', [$request->user]);
            return $responses->sendResponse($data);
        } catch (\Throwable $th) {
            return $responses->sendError('Error', $th, 500);
        }
    }

    public function getDataQuery(Request $request){
        try {
            $responses = new BaseController();
            $query_o = '';
            $query_r = '';
            if($request->has('tipoReporte')){
                $query_o .= " and a.tipo = '".$request->tipoReporte."'";
            }
            if($request->has('queryPalabras_original')){
                $query_o .= ' and ';
                $query_o .= $request->queryPalabras_original;
            }
            if($request->has('queryPalabras_resumen')){
                $query_r .= ' or ';
                $query_r .= $request->queryPalabras_resumen;
            }
            $query_fechas = '';
            if($request->has('fechaInicio') && $request->has('fechaFin')){
                $query_fechas .= " and date(a.fecha_registro) BETWEEN '".$request->fechaInicio."' AND '".$request->fechaFin."'";
            }
            if(!$request->has('fechaInicio') && $request->has('fechaFin')){
                $query_fechas .= " and date(a.fecha_registro) <= '".$request->fechaFin."'";
            }
            if($request->has('fechaInicio') && !$request->has('fechaFin')){
                $query_fechas .= " and date(a.fecha_registro) >= '".$request->fechaInicio."'";
            }
            
            $data = DB::select(" SELECT a.*,b.obra,c.usuario,
                                        d.id_contrato as contrato,
                                        e.frente, f.concepto,g.cantidad_visual,
                                        g.cantidad_confirmada,g.fecha_hora as fecha_avance,
                                        g.fecha_hora_c as fecha_confirmacion,
                                        g.id_estimacion
                                        FROM apm_reportes_voz a 
                                        left join apm_obras b
                                        on a.id_proyecto = b.id
                                        left join users c 
                                        on a.id_usuario = c.id
                                        left join apm_contratos d 
                                        on a.id_contrato = d.id
                                        left join apm_frentes e 
                                        on a.id_frente = e.id 
                                        left join apm_conceptos f 
                                        on a.id_concepto = f.id
                                        left join apm_avance g 
                                        on a.id_avance = g.id where a.id_usuario= '".$request->user ."'".$query_o.$query_r.$query_fechas . " and ids_resumen_generado is null", []);
            return $responses->sendResponse($data);
        } catch (\Throwable $th) {
            return $responses->sendError('Error', $th, 500);
        }
    }

    public function getDataQueryGlobal(Request $request){
        try {
            $responses = new BaseController();
            $query_fechas = '';
            if($request->has('fechaInicio') && $request->has('fechaFin')){
                $query_fechas .= " where date(fecha_registro) BETWEEN '".$request->fechaInicio."' AND '".$request->fechaFin."'";
            }
            if(!$request->has('fechaInicio') && $request->has('fechaFin')){
                $query_fechas .= " where date(fecha_registro) <= '".$request->fechaFin."'";
            }
            if($request->has('fechaInicio') && !$request->has('fechaFin')){
                $query_fechas .= " where date(fecha_registro) >= '".$request->fechaInicio."'";
            }
            $data = DB::select("select * from apm_reportes_voz ".$query_fechas . " ", []);
            return $responses->sendResponse($data);
        } catch (\Throwable $th) {
            return $responses->sendError('Error', $th, 500);
        }
    }


    public function getDataQueryReportesCreados(Request $request){
        try {
            $responses = new BaseController();
            $query_fechas = '';
            if($request->has('fechaInicio') && $request->has('fechaFin')){
                $query_fechas .= " and date(fecha_registro) BETWEEN '".$request->fechaInicio."' AND '".$request->fechaFin."'";
            }
            if(!$request->has('fechaInicio') && $request->has('fechaFin')){
                $query_fechas .= " and date(fecha_registro) <= '".$request->fechaFin."'";
            }
            if($request->has('fechaInicio') && !$request->has('fechaFin')){
                $query_fechas .= " and date(fecha_registro) >= '".$request->fechaInicio."'";
            }
            $data = DB::select("select * from  apm_reportes_voz where id_usuario= '".$request->user ."'".$query_fechas . " and ids_resumen_generado is not null", []);
            return $responses->sendResponse($data);
        } catch (\Throwable $th) {
            return $responses->sendError('Error', $th, 500);
        }
    }

}
