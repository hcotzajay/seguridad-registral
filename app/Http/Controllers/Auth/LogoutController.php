<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    public function logout()
    {
        //session()->forget('id_usuario');
        session_unset();
        session_destroy();

        return response([
            'title'   => 'Sessión finalizada.',
            'message' => 'Hasta pronto.'
        ]);
    }
}
