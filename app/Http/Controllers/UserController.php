<?php

namespace App\Http\Controllers;

use App\Http\Traits\NuSoapRGP;
use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use NuSoapRGP;

    public function menu()
    {
        $user = new User();
        //$user_id_SS = \request()->session()->get('id_usuario');
        $user_id_SS = $_SESSION['id_usuario'];

        $menu = $user->getMenu($user_id_SS);
        if (!is_array($menu)) {
            return $menu;
        }

        $datos_user_RH = $user->getPerfilUsuario($user_id_SS);
        if (!is_array($datos_user_RH)) {
            return $datos_user_RH;
        }

        return response([
            'menu'  => $menu,
            'datos' => $datos_user_RH
        ]);
    }

    public function cambiaPassword(Request $request)
    {
        $this->validate($request, [
            'password'     => 'string|required',
            'new_password' => 'string|required'
        ]);

        $this->setWsdl(env('SERVER_APLICACIONES_WSDL'));
        $client = $this->init();

        $params = [
            'idusuario'    => $_SESSION['id_usuario'],
            'password'     => $request->password,
            'new_password' => $request->new_password,
        ];

        $result = $client->call('CambiaPass', $params);

        if (empty($result)) {
            return response([
                'message' => 'Error en la comunicación con el WS -CambiaPin-'
            ], 503);
        }

        $result_of_change = xmlToArray(simplexml_load_string($result));

        if ($result_of_change['mensaje']['_clase'] != 'ok') {
            return response([
                'message' => $result_of_change['mensaje']['$']
            ], 403);
        }

        return response()->json([
            'message' => $result_of_change['mensaje']['$'],
        ], 200);
    }
}
