<?php

namespace App\Http\Controllers;

use App\Token;
use Illuminate\Support\Facades\DB;

class Consulta_Controller
{
    public function realizarConsulta()
    {
        $id_usuario = $_SESSION['id_usuario'];
        $correlativo = Token::getToken($id_usuario)
            ->get();
        $correlativo->toArray();

        return response([
            'title' => 'Finalizado.',
            'message' => 'Corte realizado',
        ]);
    }
}
