<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\ApmBitacoraEventos;
use Illuminate\Support\Facades\Auth;
use App\Models\ApmFrentes;
use App\Models\ApmMedia;
use App\Models\ApmNotasFrente;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\IOFactory as WordReader;
use Illuminate\Filesystem\Filesystem;
use Smalot\PdfParser\Parser as PdfParser;
use thiagoalessio\TesseractOCR\TesseractOCR;

class GacDocumentosCotroller extends BaseController
{

    private function extractFromPdf(string $filePath): ?string
    {
        $parser = new PdfParser();
        $pdf = $parser->parseFile($filePath);
        return $pdf->getText();
    }

    private function parseXML($xmlFilePath) {
        // Cargar el XML
        $xmlContent = simplexml_load_file($xmlFilePath);
        if ($xmlContent === false) {
            return "Sin información disponible";
        }
        $json = json_encode($xmlContent);
        $array = json_decode($json, true);
        return $array;
    }

    public function gacAddMedia(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'file' => 'required'
            ]);
            if ($validator->fails()) {
                return $this->sendError('Todos los valores son requeridos', $validator->errors());
            }
            $file = new Filesystem;
            $file->cleanDirectory('storage/app/documentos/GAC/tmp');

            $file = $request->file('file');
            $extension = $file->extension();
            $nombre_archivo = $file->getClientOriginalName()/*  . '.' . $extension */;
            $file->storeAs("documentos/GAC/tmp", $nombre_archivo);
            asset("documentos/GAC/{$nombre_archivo}");
            $ruta = "storage/app/documentos/GAC/tmp/{$nombre_archivo}";
            $rutaWord = "app/documentos/GAC//tmp/{$nombre_archivo}";
            $data = [];
            if ($extension === 'pdf') {
                $directory = storage_path($rutaWord);
                $textoPdf = $this->extractFromPdf($directory);
                $toEmbedding = 'Contenido del documento PDF:' . $textoPdf;
                $messagesPdf[] = [
                    "role" => "user",
                    "content" => [
                        [
                            'type' => 'text',
                            'text' => "Eres un experto en contador y de finanzas",
                        ],
                        [
                            
                            "type" => "text",
                            "text" => "busca informacion como 'importe' solo en cantidad numerica, 'moneda' el tipo de moneda y dame una 'descripcion'  del documento y regresame la información en español en un json como este  {importe:'importe encontrado', descripcion :'descripcion encontrada', valido:'si o no', moneda:'dolar, peso etc', motivo_valido:''}, en propiedad 'valido' indicame a tu consideracíon y se muy estricto en esta elección si crees que es o no una factura y en el atributo 'motivo_valido' el porque crees que es o no valido , regresame un JSON y solo json nadamas nada de texto adicional, si no encuentras lo que se te pide mandame el json indicado pero con los valores en nullo y el campo 'valido' con un 'no' "
                        ],
                        [
                            "type" => "text",
                            "text" => $toEmbedding
                        ]
                    ]
                ];
                $responseDescripcionPdf = Http::withToken('')->withOptions(['verify' => false])->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o',
                    'messages' => $messagesPdf,
                    'temperature' => 0.2, 
                ]);
                $jsonString = preg_replace('/^```json\\n|```$/', '', $responseDescripcionPdf['choices'][0]['message']['content']);
                $decodedJson = json_decode($jsonString, true);
                $data['json'] = $decodedJson;
            }

            if ($extension === 'xml') {
                $directory = storage_path($rutaWord);
                $textoWord = $this->parseXML($directory);
                $toEmbedding = $textoWord;
                $messagesXml[] = [
                    "role" => "user",
                    "content" => [
                        [
                            'type' => 'text',
                            'text' => "Eres un experto en contador y de finanzas",
                        ],
                        [
                            "type" => "text",
                            "text" => "busca informacion como 'importe' solo en cantidad numerica, 'moneda' el tipo de moneda  y dame una 'descripcion' del documento y regresame la información en español en un json como este  {importe:'importe encontrado', descripcion :'descripcion encontrada', valido:'si o no', moneda:'dolar, peso etc', motivo_valido:''}, en propiedad 'valido' indicame a tu consideracíon y se muy estricto en esta elección si crees que es o no una factura y en el atributo 'motivo_valido' el porque crees que es o no valido , regresame un JSON y solo json nadamas nada de texto adicional, si no encuentras lo que se te pide mandame el json indicado pero con los valores en nullo y el campo 'valido' con un 'no'  "
                        ],
                        [
                            "type" => "text",
                            "text" => print_r($toEmbedding, true)
                        ]
                    ]
                ];
                $responseDescripcionXml = Http::withToken('')->withOptions(['verify' => false])->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o',
                    'messages' => $messagesXml,
                    'temperature' => 0.2, 
                ]);
                $jsonString = preg_replace('/^```json\\n|```$/', '', $responseDescripcionXml['choices'][0]['message']['content']);
                $decodedJson = json_decode($jsonString, true);
                $data['json'] = $decodedJson;
            }

            return $this->sendResponse($data);
        } catch (\Throwable $th) {
            return $this->sendError('Error al agregar el archivo', $th, 500);
        }
    }
    


}
