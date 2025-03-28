<?php

namespace App\Http\Controllers\API;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Services\ClusteringService;
use App\Services\SimilarityService;
use App\Utils\SmsSend;


date_default_timezone_set('America/Mexico_City');
class AsistenteInteligenteController extends BaseController
{

    public function almacenarContenidoConEmbedding(Request $request)
    {
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                'titulo' => 'required',
                'contenido' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => 'el id es requerido'], 500);
            }
            // Generar embedding del contenido usando la API de OpenAI
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
            ])->post('https://api.openai.com/v1/embeddings', [
                        //'input' => $request->contenido,
                        'input' => $request->titulo,
                        'model' => 'text-embedding-ada-002'
                    ]);
            $embedding = $response->json()['data'][0]['embedding'];
            DB::table('gac_contenido_embeddings')->insert([
                'titulo' => $request->titulo,
                'contenido' => $request->contenido,
                'embedding' => json_encode($embedding)
            ]);
        } catch (\Throwable $th) {
            return $this->sendError('Error', $th, 500);
        }
    }

    public function setPreguntaCorrecta(Request $request)
    {
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                'pregunta' => 'required',
                'embedding' => 'required',
                'id_contenido' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => 'el id es requerido'], 500);
            }
            DB::table('gac_preguntas_asitentente')->insert([
                'pregunta' => $request->pregunta,
                'embedding' => json_encode($request->embedding),
                'id_contenido' => $request->id_contenido
            ]);
            return $this->sendResponse('Exito al guardar');
        } catch (\Throwable $th) {
            return $this->sendError('Error', $th, 500);
        }
    }

    private function buscarContenidoMasCercanoEnBaseDeDatos($userEmbedding)
    {
        $resultados = DB::table('gac_contenido_embeddings')->get();
        $mejorSimilitud = -1;  // Inicializamos la mejor similitud con un valor muy bajo
        $mejorResultado = null;
        foreach ($resultados as $resultado) {
            // embedding guardado de JSON a array
            $embeddingGuardado = json_decode($resultado->embedding, associative: true);
            // Calculamos la similitud coseno entre el embedding del usuario y el almacenado
            $similitud = $this->similitudCoseno($userEmbedding, $embeddingGuardado);
            // Si esta es la mejor similitud encontrada, actualizamos el resultado
            if ($similitud > $mejorSimilitud) {
                $mejorSimilitud = $similitud;
                $mejorResultado = $resultado;
                $mejorResultado->similitud = $mejorSimilitud;
            }
        }
        // Retornamos el contenido más cercano
        return $mejorResultado;
    }

    private function buscarContenidoMasCercanoEnBaseDeDatosParaPregunta($userEmbedding)
    {
        $resultados = DB::table('gac_preguntas_asitentente')->get();
        $mejorSimilitud = -1;  // Inicializamos la mejor similitud con un valor muy bajo
        $mejorResultado = null;
        foreach ($resultados as $resultado) {
            // embedding guardado de JSON a array
            $embeddingGuardado = json_decode($resultado->embedding, associative: true);
            // Calculamos la similitud coseno entre el embedding del usuario y el almacenado
            $similitud = $this->similitudCoseno($userEmbedding, $embeddingGuardado);
            // Si esta es la mejor similitud encontrada, actualizamos el resultado
            if ($similitud > $mejorSimilitud) {
                $mejorSimilitud = $similitud;
                $mejorResultado = $resultado;
                $mejorResultado->similitud = $mejorSimilitud;
            }
        }
        // Retornamos el contenido más cercano
        return $mejorResultado;
    }

    public static function normalize(array $vector): array
    {
        $magnitude = sqrt(array_sum(array_map(fn($val) => $val ** 2, $vector)));
        if ($magnitude == 0) {
            return $vector; // Retorna el vector original si la magnitud es cero
        }
        return array_map(fn($val) => $val / $magnitude, $vector);
    }

    public static function cosineSimilarity(array $vectorA, array $vectorB): float
    {
        // Normalizar ambos vectores
        $vectorA = self::normalize($vectorA);
        $vectorB = self::normalize($vectorB);
        // Producto punto
        $dotProduct = array_sum(array_map(fn($a, $b) => $a * $b, $vectorA, $vectorB));
        return $dotProduct; // La similitud coseno será un valor entre -1 y 1
    }

    function similitudCoseno($embedding1, $embedding2)
    {
        $dotProduct = 0;
        $magnitude1 = 0;
        $magnitude2 = 0;
        for ($i = 0; $i < count($embedding1); $i++) {
            $dotProduct += $embedding1[$i] * $embedding2[$i];
            $magnitude1 += pow($embedding1[$i], 2);
            $magnitude2 += pow($embedding2[$i], 2);
        }
        $magnitude1 = sqrt($magnitude1);
        $magnitude2 = sqrt($magnitude2);
        if ($magnitude1 == 0 || $magnitude2 == 0) {
            return 0;
        } else {
            return $dotProduct / ($magnitude1 * $magnitude2);
        }
    }

    public function chat(Request $request)
    {
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                'mensaje' => 'required',
                'name' => 'required'
            ]);
            if ($validator->fails()) {
                return $this->sendError('Todos los campos son requeridos', $validator->errors(), 500);
            }

            $resultados_ = DB::table('gac_contenido_embeddings')->where('titulo', $request->mensaje)->get()->first();
            if($resultados_){
                $respuestaInteligente = $this->generaRespuestaCreativaExistente($request->mensaje, $resultados_, $request->name);
                $response['respuestaInteligente'] = $respuestaInteligente;
                $response['sql'] = $resultados_;
                $response['pregunta'] = $request->mensaje;
                $response['existente_pregunta'] = true;
                return $this->sendResponse($response);
            }



            $userEmbeddingResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
            ])->post('https://api.openai.com/v1/embeddings', [
                        'input' => $request->mensaje . ' En ARJION',
                        'model' => 'text-embedding-ada-002'
                    ]);

            $userEmbedding = $userEmbeddingResponse->json()['data'][0]['embedding'];
            /* Primero buscamos la pregunta en la tabla de gac_preguntas_asitentente, si ya guardamos esa pregunta exactamente como se hizo, entonces la vamos a enviar por defecto */
            $buscarPorPregunta = DB::table('gac_preguntas_asitentente')->where('pregunta', $request->mensaje)->get()->first();
            if ($buscarPorPregunta) {
                $resultados = DB::table('gac_contenido_embeddings')->where('id', $buscarPorPregunta->id_contenido)->get()->first();
                $respuestaInteligente = $this->generaRespuestaCreativaExistente($request->mensaje, $resultados, $request->name);
                $response['respuestaInteligente'] = $respuestaInteligente;
                $response['sql'] = $resultados;
                $response['embedding_pregunta'] = $userEmbedding;
                $response['pregunta'] = $request->mensaje;
                $response['existente_pregunta'] = true;
                return $this->sendResponse($response);
            }
            /* paso 2 vamos a buscar esa pregunta por similitud de embedding
                si la pregunta tiene una similitud de mas de 0.89 que es bastante alto le vamos a dar el contenido embedding asociado
            */
            $bestMatchPregunta = $this->buscarContenidoMasCercanoEnBaseDeDatosParaPregunta($userEmbedding);
            $umbralMinimoPregunta = 0.90;
            if ($bestMatchPregunta && $bestMatchPregunta->similitud >= $umbralMinimoPregunta) {
                $resultados = DB::table('gac_contenido_embeddings')->where('id', $bestMatchPregunta->id_contenido)->get()->first();
                $resultados->similitud = $bestMatchPregunta->similitud;
                $respuestaInteligente = $this->generaRespuestaCreativaExistente($request->mensaje, $resultados, $request->name);
                $response['respuestaInteligente'] = $respuestaInteligente;
                $response['sql'] = $resultados;
                $response['embedding_pregunta'] = $userEmbedding;
                $response['pregunta'] = $request->mensaje;
                $response['pregunta_similar'] = $bestMatchPregunta->pregunta;
                $response['existente_similitud_pregunta'] = true;
                if($respuestaInteligente === 'Lo siento, no pude encontrar información relevante. Investigaré un poco más al respecto y cuando tenga una respuesta te la haré saber.'){
                    $ultramsg_token = env('ULTRA_SMS_TOKEN');
                    $instance_id = "instance80546";
                    $sendMail = new SmsSend($ultramsg_token, $instance_id);
                    $to = "+525635309370";
                    $body = 'Hola ARJION, te notifica';
                    $body1 = "se le hizo la siguiente pregunta al asistente virtual de GAC: '{$request->mensaje}', la cual el sistema no supo responder, favor de notificarlo con el desarrollador del sitio";
                    $sendMail->sendChatMessage($to, $body);
                    $sendMail->sendChatMessage($to, $body1);
                }else{
                    DB::table('gac_preguntas_asitentente')->insert([
                        'pregunta' => $request->mensaje,
                        'embedding' => json_encode($userEmbedding),
                        'id_contenido' => $resultados->id
                    ]);
                }
                return $this->sendResponse($response);
            }
            $bestMatch = $this->buscarContenidoMasCercanoEnBaseDeDatos($userEmbedding);
            $umbralMinimo = 0.80;
            
            if ($bestMatch && $bestMatch->similitud >= $umbralMinimo) {
                $respuestaInteligente = $bestMatch->similitud >= 0.90 ? $this->generaRespuestaCreativaExistente($request->mensaje, $bestMatch, $request->name) : $this->generaRespuestaCreativa($request->mensaje, $bestMatch, $request->name);
                $response['respuestaInteligente'] = $respuestaInteligente;
                $response['sql'] = $bestMatch;
                $response['embedding_pregunta'] = $userEmbedding;
                $response['pregunta'] = $request->mensaje;
                if($respuestaInteligente === 'Lo siento, no pude encontrar información relevante. Investigaré un poco más al respecto y cuando tenga una respuesta te la haré saber.'){
                    $ultramsg_token = env('ULTRA_SMS_TOKEN');
                    $instance_id = "instance80546";
                    $sendMail = new SmsSend($ultramsg_token, $instance_id);
                    $to = "+525635309370";
                    $body = 'Hola Arjion, te notifica';
                    $body1 = "se le hizo la siguiente pregunta al asistente virtual de GAC: '{$request->mensaje}', la cual el sistema no supo responder, favor de notificarlo con el desarrollador del sitio";
                    $sendMail->sendChatMessage($to, $body);
                    $sendMail->sendChatMessage($to, $body1);
                }
                return $this->sendResponse($response);
            } else {
                    $ultramsg_token = env('ULTRA_SMS_TOKEN');
                    $instance_id = "instance80546";
                    $sendMail = new SmsSend($ultramsg_token, $instance_id);
                    $to = "+525635309370";
                    $body = 'Hola Arjion, te notifica';
                    $body1 = "El usuario: {$request->name}, realizo la siguiente pregunta al asistente virtual: '{$request->mensaje}', la cual el sistema no supo responder, favor de notificarlo con el desarrollador del sitio";
                    $sendMail->sendChatMessage($to, $body);
                    $sendMail->sendChatMessage($to, $body1);
                    $response['sql'] = $bestMatch;
                    $response['respuestaInteligente'] = 'Lo siento, no pude encontrar información relevante. Investigaré un poco más al respecto y cuando tenga una respuesta te la haré saber.';
                return $this->sendResponse($response);
            }
        } catch (\Throwable $th) {
            return $this->sendError('Error', $th, 500);
        }
    }

    private function generaRespuestaCreativa($userInput, $bestMatch, $name)
    {
        $userLog = Auth::user();
        $openaiResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
        ])->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => "Eres un asistente especializado educado y amigable. 
                                         dirigete a ellos por su nombre que es este {$name}, 
                                         Solo puedes responder basándote estrictamente en la información proporcionada en el contexto. 
                                         Si no tienes suficiente información para responder, usa esta respuesta: 
                                         'Lo siento, no pude encontrar información relevante. Investigaré un poco más al respecto y cuando tenga una respuesta te la haré saber.'. 
                                         No inventes datos ni uses fuentes externas."
                        ],
                        ['role' => 'user', 'content' => $userInput . "Aquí está el contexto para que respondas: {$bestMatch->contenido}"],
                        // El contexto lo extraemos del contenido de la base de datos más relevante
                        ['role' => 'assistant', 'content' => "contexto para responder: {$bestMatch->contenido}, no uses otra fuente de información"]
                    ],
                    'temperature' => 0.3,  // Ajustar temperatura para respuestas más creativas
                ]);
        return $openaiResponse->json()['choices'][0]['message']['content'];
    }


    private function generaRespuestaCreativaExistente($userInput, $bestMatch, $name)
    {
        $userLog = Auth::user();
        $openaiResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
        ])->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => "Eres un asistente especializado en modulo de gastos a comprobar de arjion, educado y amigable. 
                                         dirigete a ellos por su nombre que es este {$name},  dame una sitensis de este texto {$bestMatch->contenido} "
                        ],
                    ],
                    'temperature' => 0.4,  // Ajustar temperatura para respuestas más creativas
                    'logit_bias' => [
                        "1234" => -100,  // Desactiva un token específico.
                        "5678" => 2,     // Favorece un token específico.
                    ]
                ]);
        return $openaiResponse->json()['choices'][0]['message']['content'];
    }

}