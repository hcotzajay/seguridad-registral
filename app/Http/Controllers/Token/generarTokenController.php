<?php

namespace App\Http\Controllers\Token;

use App\Http\Controllers\Controller;
use App\tabla;
use App\Token\token;
use Facade\Ignition\SolutionProviders\DefaultDbNameSolutionProvider;
use Illuminate\Http\Request;

class generarTokenController extends Controller
{
    public function generarToken()
    {
        $url = env('API_SERVER') . env('API_PORT') . env('API_URI') . env('API_TOKEN');
        $curl = curl_init();
        $user = env('API_USER');
        $pass = env('API_PASS');
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, TRUE);
        $headers = [
            'user:' . $user,
            'pass:' . $pass,
            'Content-Length:0',
            'Host: nginx'
        ];
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            return response([
                'message' => 'Error en la comunicación con el WS -Renap-.' . $err
            ], 503);
        } else {
            $datos = json_decode($data);
            return $datos;
        }
    }

    public function verificarToken()
    {
        $fechaActual = date('Y-m-d');
        $fechaFin = date('Y-m-d');
        $fechaInicio = $fechaActual . ' 00:00:00';
        $fechaFin = $fechaFin . ' 23:59:59';
        $verificar = token::select()
            ->where('fecha_token', '>=', $fechaInicio)
            ->where('fecha_token', '<=', $fechaFin)
            ->where('estado', '=', 1)
            ->orderby('id_token', 'desc')
            ->get();
        return $verificar;
    }
}
