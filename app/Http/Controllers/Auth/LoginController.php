<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Traits\NuSoapRGP;
use App\User;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use NuSoapRGP;

    public function login(Request $request)
    {
        $this->validate($request, [
            'usuario'  => 'string|required',
            'password' => 'string|required'
        ]);

        $this->setWsdl(env('SERVER_APLICACIONES_WSDL'));
        $client = $this->init();

        $params = [
            'usuario'  => $request->usuario,
            'password' => $request->password,
            'ip'       => $request->getClientIp(),
            'sistema'  => env('SISTEMA_ID')
        ];

        $result_of_login = $client->call('Login', $params);

        if (empty($result_of_login)) {
            return response([
                'message' => 'Error en la comunicación con el WS -Login-'
            ], 503);
        }

        $result_of_login = xmlToArray(simplexml_load_string($result_of_login));

        if ($result_of_login['mensaje']['_clase'] != 'ok') {
            return response([
                'message' => $result_of_login['mensaje']['$']
            ], 403);
        }

        $user_id_SS = $result_of_login['mensaje']['$'];
        $user = new User();
        $datos_user_RH = $user->getPerfilUsuario($user_id_SS);
        //Se crea la sessión
        //Sesión local de laravel
        /*\session()->put('id_usuario', $user_id_SS);*/
        //sesión global
        session_start();
        $_SESSION['id_usuario'] = $user_id_SS;
        $_SESSION['agencia_id'] = $datos_user_RH['usuario']['agencia'];
        $_SESSION['nombre'] = $datos_user_RH['usuario']['nombre'];

        return response([
            'message' => $datos_user_RH['usuario']['nombre'],
            'datos'   => $datos_user_RH
        ]);
    }

    public function passwordReset(Request $request)
    {
        $this->validate($request, [
            'usuario' => 'string|required',
        ]);

        $this->setWsdl(env('SERVER_APLICACIONES_WSDL'));
        $client = $this->init();

        $params = [
            'ip'      => $request->ip(),
            'usuario' => $request->usuario
        ];

        $result = $client->call('RedefinePass', $params);

        if (empty($result)) {
            return response([
                'message' => 'Error en la comunicación con el WS -Login-'
            ], 503);
        }
        $result_of_reset = xmlToArray(simplexml_load_string($result));

        if ($result_of_reset['mensaje']['_clase'] != 'ok') {
            return response([
                'message' => $result_of_reset['mensaje']['$']
            ], 403);
        }

        return response()->json([
            'message' => $result_of_reset['mensaje']['$'],
        ], 200);
    }
}
