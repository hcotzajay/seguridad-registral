<?php

namespace App\Http\Controllers\FirmaE;

use App\Http\Controllers\Controller;
use App\Http\Traits\NuSoapRGP;
use Illuminate\Http\Request;

class PINController extends Controller
{
    use NuSoapRGP;

    public function cambiaPIN(Request $request)
    {
        $this->setWsdl(env('SERVER_APLICACIONES_WSDL'));
        $client = $this->init();

        $params = [
            'idusuario' => $_SESSION['id_usuario'],
            'password'  => $request->password,
            'pin'       => $request->pin,
        ];

        $result = $client->call('CambiaPin', $params);

        if (empty($result)) {
            return response([
                'message' => 'Error en la comunicación con el WS -CambiaPin-'
            ], 503);
        }

        $result_of_login = xmlToArray(simplexml_load_string($result));

        if ($result_of_login['mensaje']['_clase'] != 'ok') {
            return response([
                'message' => $result_of_login['mensaje']['$']
            ], 403);
        }

        return response()->json([
            'message' => $result_of_login['mensaje']['$'],
        ], 200);
    }
}
