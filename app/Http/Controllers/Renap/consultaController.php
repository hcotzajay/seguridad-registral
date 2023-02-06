<?php

namespace App\Http\Controllers\Renap;

use App\consulta;
use App\contador;
use App\cui;
use App\datos;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Token\generarTokenController;
use App\log_bitacora_busquedas;
use App\resultado;
use App\Token\token;
use Facade\Ignition\SolutionProviders\DefaultDbNameSolutionProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class consultaController extends generarTokenController
{
    public function consultaCui(Request $request)
    {
        try {
            DB::connection('mysql')->beginTransaction();
            //Variables para cambios de estados en las tablas al momento de guardar un nuevo registro
            $cambioEstadoToken = false;
//        $cambioEtadoContador = false;
            $cambioContador = false;
            $situacionToken = false;
            $cui = $request->params['cui'];
            $tipoConsulta = $request->params['tipoConsulta'];
            $verificarExisteToken = $this->verificarToken();
            if ($verificarExisteToken->isEmpty()) {
                $obtenerNuevoToken = $this->generarToken();
                $token = $obtenerNuevoToken->data->token;
                $fechaExpiracion = $obtenerNuevoToken->data->expiracion;
                $fechaToken = $obtenerNuevoToken->fecha;

                $tokenAnterior = token::latest('id_token')->first();// Obtengo el ultimo registro que se haya ingresado para cambiar el estado del anterior
                $idToken = $tokenAnterior->id_token;
                $cambiarEstadoTokenAnterior = token::find($idToken);
                $cambiarEstadoTokenAnterior->estado = 0;
                if ($cambiarEstadoTokenAnterior->save()) {
                    $cambioEstadoToken = true;
                }

                if ($cambiarEstadoTokenAnterior == true) {
                    $guardarToken = new token();
                    $guardarToken->token = $token;
                    $guardarToken->estado = 1;
                    if ($guardarToken->save()) {
                        $situacionToken = true;
                    } else {
                        $situacionToken = false;
                    }
                }
            }


            //Obtenemos token activo para poder insertarlo en la consulta necesaria
            $fechaInicio = date('Y-m-d');
            $fechaFin = date('Y-m-d');
            $fechaInicio = $fechaInicio . ' 00:00:00';
            $fechaFin = $fechaFin . ' 23:59:59';
            $obtenerToken = token::select()
                ->where('fecha_token', '>=', $fechaInicio)
                ->where('fecha_token', '<=', $fechaFin)
                ->where('estado', '=', 1)
                ->orderby('id_token', 'desc')
                ->first();

            $tokenActivo = $obtenerToken->token;
            $idTokenActivo = $obtenerToken->id_token;

            //Se realiza la consulta y se envia como parametro el cui escrito por el usuario y el token activo actualmente.
            $consultaCui = $this->realizarConsulta($cui, $tokenActivo);
//        Guardar en las distintas tablas previo a realizar la consulta
//        Tabla contador
            $contadorAnterior = contador::latest('id_contador')->first();// Obtengo el ultimo registro que se haya ingresado para cambiar el estado del anterior
            $idContador = $contadorAnterior->id_contador;

            //Cambio de estado del contador Anterior
            $estodoContadorAnterior = $this->cambiarEstadoContador($idContador);

            //Almacenar nuevo contador para el registro
            $contadorActual = $contadorAnterior->contador;
            $nuevoContador = $this->nuevoContador($contadorActual);

            $consultaCuiAlmacenar = json_encode($consultaCui);

            //Almacenar en la tabla Datos
            $almacenarDatos = false;
            $datos = new datos();
            $datos->data_json = $consultaCuiAlmacenar;
            if ($datos->save()) {
                $almacenarDatos = true;
            } else {
                $almacenarDatos = false;
            }

            //Obtener el ultimo id de datos para relacionarlo
            $idDatos = '';
            if ($almacenarDatos == true) {
                $obtenerUltimoRegistro = datos::latest('id_datos')->first();// Obtengo el ultimo registro que se haya ingresado
                $idDatos = $obtenerUltimoRegistro->id_datos;
            }

            //Variables que se necesitan para almacenar en la tabla resultado

            $result = $consultaCui->result;
            $fecha = $consultaCui->fecha;
            $responseCode = $consultaCui->responseCode;
            $hora = $consultaCui->hora;
            $mensaje = $consultaCui->mensaje;

            //almacenar en la tabla resultado
            $almacenarResultado = $this->almacenarResultado($result, $fecha, $responseCode, $hora, $idDatos, $mensaje);
            $almacenarCuiBuscado = $this->almacenarCui($cui);
            $almacenarConsulta = '';
            if ($almacenarCuiBuscado == true && $almacenarDatos == true && $nuevoContador == true && $almacenarResultado == true) {
                $idSS = $_SESSION['id_usuario'];
                $obtenerUltimoRegistroCui = cui::latest('id_cui')->first();// Obtengo el ultimo registro que se haya ingresado
                $idCui = $obtenerUltimoRegistroCui->id_cui;

                $idNombres = null;
                $idTipo = $tipoConsulta;

                $obtenerUltimoRegistroContador = contador::latest('id_contador')->first();// Obtengo el ultimo registro que se haya ingresado
                $idContador = $obtenerUltimoRegistroContador->id_contador;

                $obtenerUltimoRegistroResultado = contador::latest('id_contador')->first();// Obtengo el ultimo registro que se haya ingresado
                $idResultado = $obtenerUltimoRegistroResultado->id_resultado;

                $almacenarConsulta = $this->almacenarConsulta($idSS, $idCui, $idNombres, $idTipo, $idContador, $idResultado, $idTokenActivo);
                if ($almacenarConsulta == true) {
                    DB::connection('mysql')->commit();

                    $consultaCuiBitacora = json_encode($consultaCui);
                    if ($consultaCui !== 'Ha ocurrido un error en la comunicación con el WS') {
                        $obtenerUltimoRegistro = log_bitacora_busquedas::latest('id_log_busqueda')->first();// Obtengo el ultimo registro que se haya ingresado
                        $numeroConsulta = "";
                        if ($obtenerUltimoRegistro !== null) {
                            $numeroConsulta = $obtenerUltimoRegistro->numero_consulta;
                            $numeroConsulta = $numeroConsulta + 1;
                        } else {
                            $numeroConsulta = 1;
                        }

                        $idUSS = $_SESSION['id_usuario'];
                        $ingresarBitacora = $this->ingresarBitacora($consultaCuiBitacora, $numeroConsulta, $idUSS);
                    }

                    return response()->json([
                        'title'    => 'Listo!',
                        'message'  => $mensaje,
                        'consulta' => $consultaCui
                    ], 200);
                } else {
                    DB::connection('mysql')->rollBack();

                    $consultaCuiBitacora = json_encode($consultaCui);
                    if ($consultaCui !== 'Ha ocurrido un error en la comunicación con el WS') {
                        $obtenerUltimoRegistro = log_bitacora_busquedas::latest('id_log_busqueda')->first();// Obtengo el ultimo registro que se haya ingresado
                        $numeroConsulta = "";
                        if ($obtenerUltimoRegistro !== null) {
                            $numeroConsulta = $obtenerUltimoRegistro->numero_consulta;
                            $numeroConsulta = $numeroConsulta + 1;
                        } else {

                            $numeroConsulta = 1;
                        }

                        $idUSS = $_SESSION['id_usuario'];
                        $ingresarBitacora = $this->ingresarBitacora($consultaCuiBitacora, $numeroConsulta, $idUSS);
                    }
                    return response()->json([
                        'title'   => 'Atención!',
                        'message' => $mensaje
                    ], 403);
                }
            }
        } catch (\PDOException $e) {
            DB::connection('mysql')->rollBack();
            return response()->json([
                'title'   => 'Atención!',
                'message' => $mensaje,
                'error'   => $e
            ], 500);
        }
    }

    public function realizarConsulta($cui, $tokenActivo)
    {
        $url = env('API_SERVER') . env('API_PORT') . env('API_URI') . env('API_BUSQUEDAS');
        $curl = curl_init();
        $user = env('API_USER');
        $pass = env('API_PASS');
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, TRUE);
        $headers = [
            'Authorization: Bearer ' . $tokenActivo,
            'Content-Type: application/json',
            'Accept: application/json',
            'Host: nginx'
        ];
        $paramsBusquedaCui = '{"busquedaCui":{"cui": ' . $cui . '}}';
        curl_setopt($curl, CURLOPT_POSTFIELDS, $paramsBusquedaCui);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {

            return "Ha ocurrido un error en la comunicación con el WS";
//            return response([
//                'message' => 'Error en la comunicación con el WS -Renap-.',
//                'error'   => $err
//            ], 503);
        } else {
            $datosPersona = json_decode($data);
            return $datosPersona;
        }
    }

    public function cambiarEstadoContador($idContador)
    {
        $cambioEstadoContador = false;
        //cambiar estado contador anterior
        $cambiarEstadoContadorAnterior = contador::find($idContador);
        $cambiarEstadoContadorAnterior->estado = 0;
        if ($cambiarEstadoContadorAnterior->save()) {
            return $cambioEstadoContador = true;
        } else {
            return $cambioEstadoContador = false;
        }
    }

    public function nuevoContador($contadorActual)
    {
        $cambioContador = false;
        $contadorActual = $contadorActual + 1;
        $estadoContadorActual = 1;

        $guardarContadorActual = new contador();
        $guardarContadorActual->contador = $contadorActual;
        $guardarContadorActual->estado = 1;
        if ($guardarContadorActual->save()) {
            return $cambioContador = true;
        } else {
            return $cambioContador = false;
        }
    }

    public function almacenarDatos($consultaCui)
    {
        $almacenarDatos = false;
        $datos = new datos();
        $datos->data_json = $consultaCui;
        if ($datos->save()) {
            return $almacenarDatos = true;
        }
        return $almacenarDatos = false;
    }

    public function almacenarResultado($result, $fecha, $responseCode, $hora, $idDatos, $mensaje)
    {
        $almacenarResultado = false;
        $resultado = new resultado();
        $resultado->result = $result;
        $resultado->fecha = $fecha;
        $resultado->response_code = $responseCode;
        $resultado->hora = $hora;
        $resultado->mensaje = $mensaje;
        $resultado->id_datos = $idDatos;
        if ($resultado->save()) {
            return $almacenarResultado = true;
        }
        return $almacenarResultado = false;
    }

    public function almacenarCui($cui)
    {
        $cuiAlmacenar = false;
        $almacernarCui = new cui();
        $almacernarCui->cui = $cui;
        if ($almacernarCui->save()) {
            return $cuiAlmacenar = true;
        }
        return $cuiAlmacenar = false;
    }

    public function almacenarConsulta($idSS, $idCui, $idNombres, $idTipo, $idContador, $idResultado, $idToken)
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $guardarConsulta = new consulta();
        $guardarConsulta->id_ss = $idSS;
        $guardarConsulta->id_cui = $idCui;
        $guardarConsulta->id_nombres = $idNombres;
        $guardarConsulta->id_tipo = $idTipo;
        $guardarConsulta->id_contador = $idContador;
        $guardarConsulta->id_resultado = $idResultado;
        $guardarConsulta->id_token = $idToken;
        $guardarConsulta->ip = $ip;
        $guardarConsulta->estado = 1;
        if ($guardarConsulta->save()) {
            return true;
        }
        return false;
    }

    public function consultarContadorBitacora()
    {
        $obtenerUltimoRegistroResultado = contador::latest('id_contador')->first();// Obtengo el ultimo registro que se haya ingresado
        $idResultado = $obtenerUltimoRegistroResultado->id_resultado;
    }

    public function consultarUltimoRegistroTabla($tabla, $idTabla, $valorRetornar)
    {
        $obtenerUltimoRegistro = $tabla::latest($idTabla)->first();
        return $idUltimoRegistro = $obtenerUltimoRegistro->$valorRetornar;
    }

}
