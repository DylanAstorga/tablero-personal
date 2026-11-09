<?php

/*
 * tipocambio.php
 * --------------------------------------------------
 * Endpoint REST propio para consultar
 * el tipo de cambio.
 *
 * Este archivo:
 *
 * 1. Comprueba que exista una sesión activa.
 * 2. Consulta ExchangeRate API.
 * 3. Obtiene USD → CRC.
 * 4. Calcula EUR → CRC.
 * 5. Devuelve la información en formato JSON.
 * --------------------------------------------------
 */


/*
 * session_start() debe ir primero.
 */

session_start();


/*
 * Indicar que la respuesta será JSON.
 */

header(
    "Content-Type: application/json; charset=UTF-8"
);


/*
 * Verificar que el usuario haya iniciado sesión.
 */

if (!isset($_SESSION["usuario_id"])) {

    /*
     * Código HTTP 401:
     * usuario no autorizado.
     */

    http_response_code(401);


    echo json_encode([

        "ok" => false,

        "error" => "Usuario no autenticado."

    ]);


    exit;

}


/*
 * --------------------------------------------------
 * API KEY
 * --------------------------------------------------
 *
 * Aquí debes colocar tu API Key
 * de ExchangeRate API.
 *
 * Por ahora déjala como TU_API_KEY.
 */

$apiKey = "TU_API_KEY";


/*
 * Comprobar que se haya configurado
 * una API Key.
 */

if ($apiKey === "TU_API_KEY") {


    http_response_code(500);


    echo json_encode([

        "ok" => false,

        "error" => "Debe configurar la API Key."

    ]);


    exit;

}


/*
 * --------------------------------------------------
 * URL DE LA API EXTERNA
 * --------------------------------------------------
 *
 * Utilizamos USD como moneda base.
 */

$url =
    "https://v6.exchangerate-api.com/v6/"
    . rawurlencode($apiKey)
    . "/latest/USD";


/*
 * Inicializar cURL.
 */

$ch = curl_init($url);


/*
 * Configurar la petición HTTP.
 */

curl_setopt_array(

    $ch,

    [

        /*
         * Devolver la respuesta como texto
         * en vez de mostrarla directamente.
         */

        CURLOPT_RETURNTRANSFER => true,


        /*
         * Tiempo máximo de espera.
         */

        CURLOPT_TIMEOUT => 10,


        /*
         * Permitir redirecciones.
         */

        CURLOPT_FOLLOWLOCATION => true,


        /*
         * Solicitar respuesta JSON.
         */

        CURLOPT_HTTPHEADER => [

            "Accept: application/json"

        ]

    ]

);


/*
 * Ejecutar la solicitud.
 */

$respuesta = curl_exec($ch);


/*
 * Obtener código HTTP recibido.
 */

$codigoHttp = curl_getinfo(

    $ch,

    CURLINFO_HTTP_CODE

);


/*
 * Obtener posibles errores de cURL.
 */

$errorCurl = curl_error($ch);


/*
 * Cerrar la conexión.
 */

curl_close($ch);


/*
 * --------------------------------------------------
 * VALIDAR LA CONEXIÓN
 * --------------------------------------------------
 */

if (
    $respuesta === false
    ||
    $errorCurl !== ""
    ||
    $codigoHttp < 200
    ||
    $codigoHttp >= 300
) {


    http_response_code(502);


    echo json_encode([

        "ok" => false,

        "error" =>
            "No se pudo consultar la API externa."

    ]);


    exit;

}


/*
 * Convertir el JSON recibido
 * a un arreglo de PHP.
 */

$data = json_decode(

    $respuesta,

    true

);


/*
 * Verificar que la respuesta
 * sea válida.
 */

if (
    !is_array($data)
    ||
    ($data["result"] ?? "") !== "success"
) {


    http_response_code(502);


    echo json_encode([

        "ok" => false,

        "error" =>
            "La API externa devolvió una respuesta inválida."

    ]);


    exit;

}


/*
 * Obtener las tasas de conversión.
 */

$tasas =
    $data["conversion_rates"]
    ?? [];


/*
 * --------------------------------------------------
 * USD → CRC
 * --------------------------------------------------
 *
 * Como la moneda base es USD,
 * la tasa CRC indica directamente
 * cuántos colones equivalen a 1 dólar.
 */

$usdCrc =
    $tasas["CRC"]
    ?? null;


/*
 * --------------------------------------------------
 * USD → EUR
 * --------------------------------------------------
 *
 * Esta tasa será utilizada para
 * calcular posteriormente EUR → CRC.
 */

$usdEur =
    $tasas["EUR"]
    ?? null;


/*
 * Comprobar que ambas tasas existan.
 */

if (
    $usdCrc === null
    ||
    $usdEur === null
    ||
    (float) $usdEur === 0.0
) {


    http_response_code(502);


    echo json_encode([

        "ok" => false,

        "error" =>
            "No están disponibles las tasas necesarias."

    ]);


    exit;

}


/*
 * --------------------------------------------------
 * CALCULAR EUR → CRC
 * --------------------------------------------------
 *
 * Ejemplo:
 *
 * USD → CRC / USD → EUR
 *
 * Esto nos permite saber cuántos colones
 * equivalen a un euro.
 */

$eurCrc =

    (float) $usdCrc

    /

    (float) $usdEur;


/*
 * Obtener fecha de actualización.
 */

$fecha =
    $data["time_last_update_utc"]
    ?? date("Y-m-d H:i:s");


/*
 * --------------------------------------------------
 * RESPUESTA JSON
 * --------------------------------------------------
 *
 * Esta información será consumida
 * desde index.php utilizando fetch().
 */

echo json_encode(

    [

        "ok" => true,

        "USD" => round(
            (float) $usdCrc,
            2
        ),

        "EUR" => round(
            $eurCrc,
            2
        ),

        "fecha" => $fecha

    ],

    JSON_UNESCAPED_UNICODE

);

?>