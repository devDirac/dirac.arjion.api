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

class GacActualizaMonedasController extends BaseController
{

    public function setGacEquivalenciaMonedaExtDol(Request $request)
    {
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                'fecha' => 'required'
            ]);
            if ($validator->fails()) {
                return $this->sendError('La fecha es requerida', $validator->errors(), 500);
            }
            $date = explode("-", $request->fecha);
            $year = $date[0];
            $month = $date[1];
            $day = $date[2];
            // URL del documento
            $url = "https://www.dof.gob.mx/nota_detalle.php?codigo=5746761&fecha=" . $day . "/" . $month . "/" . $year . "#gsc.tab=0";
            $options = [
                "ssl" => [
                    "verify_peer" => false,
                    "verify_peer_name" => false,
                ],
            ];
            $html = file_get_contents($url, false, stream_context_create($options));
            if ($html === false) {
                return $this->sendError('Error al obtener el contenido de la página', [], 500);
            }
            $dom = new \DOMDocument();
            libxml_use_internal_errors(true); // Suprimir errores de HTML mal formado
            $dom->loadHTML($html);
            libxml_clear_errors();
            $xpath = new \DOMXPath($dom);
            $table = $xpath->query('//table[contains(@style, "border-collapse:collapse;")]')->item(0);
            if ($table === null) {
                return $this->sendError("No se encontró la tabla con el estilo 'border-collapse:collapse;' en la página.", [], 500);
            }
            $data = [];
            $rows = $table->getElementsByTagName('tr');
            foreach ($rows as $row) {
                $cells = $row->getElementsByTagName('td');
                $rowData = [];
                foreach ($cells as $cell) {
                    $rowData[] = trim($cell->textContent); // Obtener el texto de cada celda
                }
                if (!empty($rowData)) {
                    $data[] = $rowData; // Agregar la fila a los datos
                }
            }
            foreach ($data as $key => $value) {
                if ($key >= 3) {
                    $_dato = GacEquivalenciaMonedaExtDol::where('pais', $value[0] )->get()->first();// $gac->getCurrencyEquiv(" pais = '" . $value[0] . "'");
                    if (!$_dato) {//No existe el valor en la tabla, lo insertamos
                        $_data = array();
                        $_data["pais"] = $value[0];
                        $_data["moneda"] = $value[1];
                        $_data["valor_en_dolar"] = $value[2];
                        $_data["fecha"] = $request->fecha;
                        $_data["fecha_registro"] = now();
                        GacEquivalenciaMonedaExtDol::create($_data);
                    } else {//Si el valor ya existe verificamos que la equivalencia sea la misma
                        if (floatval($value[2]) !== floatval($_dato->valor_en_dolar)) {//Si la equivalencia es diferente actualizamos
                            $_dato->valor_en_dolar = $value[2];
                            $_dato->fecha =  $request->fecha;
                            $_dato->save();
                        }
                    }
                }
            }
            return $this->sendResponse('Exito al actualizar la información');
        } catch (\Throwable $th) {
            return $this->sendError('Error', $th, 500);
        }
    }
    

    public function setGacTipoCambioDolar(Request $request){
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                'fecha' => 'required'
            ]);
            if ($validator->fails()) {
                return $this->sendError('La fecha es requerida', $validator->errors(), 500);
            }
            $date = explode("-", $request->fecha);
            $year = $date[0];
            $month = $date[1];
            $day = $date[2];
            $url = 'https://www.dof.gob.mx/index_111.php?year=' . $year . '&month=' . $month . '&day=' . $day;
            $options = [
                "ssl" => [
                    "verify_peer" => false,
                    "verify_peer_name" => false,
                ],
            ];
            $htmlContent = file_get_contents($url, false, stream_context_create($options));
            if ($htmlContent === false) {
                return $this->sendError("Error al obtener el contenido de la página", [], 500);
            }
            $dom = new \DOMDocument();
            libxml_use_internal_errors(true); // Evitar errores por HTML mal formado
            $dom->loadHTML($htmlContent);
            libxml_clear_errors();

            $xpath = new \DOMXPath($dom);
            $paragraphs = $xpath->query('//p[@style="display:block;float:left;width:45%; font-size: 10pt;"]');
            $da=[];
            foreach ($paragraphs as $paragraph) {
                $span = $paragraph->getElementsByTagName('span')->item(0);
                if ($span && $span->getAttribute('class') === 'tituloBloque4' && trim($span->textContent) === 'DOLAR') {
                    $value = trim(str_replace('DOLAR', '', $paragraph->textContent));
                    //return $value;
                    $da[] = $value;
                    break;
                }
            }
            if(count($da)> 0){
                $dta['pesos_dolar']= $da[0];
                $dta['fecha']=$request->fecha;
                $dta['fecha_registro']=now();
                GacTipoCambioDolar::create($dta);
            }
            return $this->sendResponse($da);
        } catch (\Throwable $th) {
            return $this->sendError('Error', $th, 500);
        }
    }


}


