<?php

namespace App\Http\Controllers\Renap;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class tokenController
{
    public function getToken()
    {
        $client = new Client(["base_uri" => env('API_SERVER') . ':' . env('API_PORT')]);

        $options = [
                'user' => env('API_USER') ,
                'pass' => env('API_PASS'),
        ];

        $response = $client->post(env('API_URI'), $options);
        $response = $response->getBody()->getContents();

        $respuesta = json_decode($response, true);


        return $respuesta;
    }
/*
    public function setCertificadoSelected(Request $request)
    {
        if ($request->certificado != NULL) {
            $_SESSION['serial'] = base64_decode($request->certificado);
            $_SESSION['expiracion_certificado'] = base64_decode($request->valido_hasta);
        }

        return response()->json([
            'title'   => 'Selección correcta!',
            'message' => 'Se ha seleccionado el certificado correctamente',
        ], 200);
    }

    public function isSelected()
    {
        if (!isset($_SESSION['serial'])) {
            return response()->json([
                'selected' => null,
            ], 200);
        }

        return response()->json([
            'selected' => $_SESSION['serial'],
        ], 200);
    }
*/
}
