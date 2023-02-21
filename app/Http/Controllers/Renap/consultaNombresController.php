<?php

namespace App\Http\Controllers\Renap;

use App\consulta;
use App\contador;
use App\cui;
use App\datos;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Token\generarTokenController;
use App\log_bitacora_busquedas;
use App\nombres;
use App\resultado;
use App\Token\token;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class consultaNombresController extends generarTokenController
{
    //
    public function consultaNombres(Request $request)
    {

        try {
            DB::connection('mysql')->beginTransaction();
            $primerNombre = $request->params['primerNombre'];
            $segundoNombre = $request->params['segundoNombre'];
            $primerApellido = $request->params['primerApellido'];
            $segundoApellido = $request->params['segundoApellido'];
            $fechaNacimiento = $request->params['fechaNacimiento'];
            $tipoConsulta = $request->params['tipoConsulta'];

            if (empty($fechaNacimiento) === true) {
                $fechaNacimiento = null;
            }

            //Variables para cambios de estados en las tablas al momento de guardar un nuevo registro
            $cambioEstadoToken = false;
            $cambioContador = false;
            $situacionToken = false;

            $verificarExisteToken = $this->verificarToken();
            if ($verificarExisteToken->isEmpty()) {
                $obtenerNuevoToken = $this->generarToken();
                $token = $obtenerNuevoToken->data->token;
                $fechaExpiracion = $obtenerNuevoToken->data->expiracion;
                $fechaToken = $obtenerNuevoToken->fecha;

                $tokenAnterior = token::latest('id_token')->first();// Obtengo el ultimo registro que se haya ingresado para cambiar el estado del anterior
                if ($tokenAnterior == null) {
                    $guardarToken = new token();
                    $guardarToken->token = $token;
                    $guardarToken->estado = 1;
                    if ($guardarToken->save()) {
                        $situacionToken = true;
                    } else {
                        $situacionToken = false;
                    }
                } else {
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
//        transformar la fecha de nacimiento a un formato entendible para el api

//            $anio = substr($fechaNacimiento, 0, 4);
//            $mes = substr($fechaNacimiento, 5, -3);
//            $dia = substr($fechaNacimiento, 8);
//            $fechaNacimientoNuevoFormato = $dia . "/" . $mes . "/" . $anio;


            $consultaNombres = $this->realizarConsultaNombres($primerNombre, $segundoNombre, $primerApellido, $segundoApellido, $fechaNacimiento, $tokenActivo);

//            $consultaNombres = '{"result":true,"fecha":"08\/02\/2023","responseCode":200,"hora":"08:46:46","mensaje":"Se muestran los resultados encontrados.","data":[{"CUI":"1969891081506","PRIMER_NOMBRE":"Valeria","SEGUNDO_NOMBRE":null,"TERCER_NOMBRE":null,"PRIMER_APELLIDO":"Morales","SEGUNDO_APELLIDO":null,"FECHA_NACIMIENTO":"15\/12\/1922","GENERO":"F","ESTADO_CIVIL":"S","NACIONALIDAD":"GUATEMALA","FECHA_DEFUNCION":null,"OCUPACION":"OFICIOS DOMESTICOS","VECINDAD":"BAJA VERAPAZ, EL CHOL"}]}';
//            $consultaNombres = json_decode($consultaNombres);
            $contadorAnterior = contador::latest('id_contador')->first();// Obtengo el ultimo registro que se haya ingresado para cambiar el estado del anterior
            $idContador = $contadorAnterior->id_contador;
            $fechaUltimoContador = $contadorAnterior->fecha_contador;


            $fechaActual = date('d-m-Y');
            $fechaEntero = strtotime($fechaActual);
            $mesActual = date("m", $fechaEntero);

            $fechaEnteroUltimoContador = strtotime($fechaUltimoContador);
            $mesUltimoContador = date("m", $fechaEnteroUltimoContador);
            if ($mesUltimoContador != $mesActual) {
                $estodoContadorAnterior = $this->cambiarEstadoContador($idContador);
                $contadorInicialMes = new contador();
                $contadorInicialMes->contador = 1;
                $contadorInicialMes->estado = 1;
                if ($contadorInicialMes->save()) {
                    $nuevoContador = true;
                } else {
                    $nuevoContador = false;
                }
            } else {
                //Cambio de estado del contador Anterior
                $estodoContadorAnterior = $this->cambiarEstadoContador($idContador);
                //Almacenar nuevo contador para el registro
                $contadorActual = $contadorAnterior->contador;
                $nuevoContador = $this->nuevoContador($contadorActual);
            }


            $consultaNombresAlmacenar = json_encode($consultaNombres);

            //Almacenar en la tabla Datos
            $almacenarDatos = false;
            $datos = new datos();
            $datos->data_json = $consultaNombresAlmacenar;
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
            $result = $consultaNombres->result;
            $fecha = $consultaNombres->fecha;
            $responseCode = $consultaNombres->responseCode;
            $hora = $consultaNombres->hora;
            $mensaje = $consultaNombres->mensaje;

            //almacenar en la tabla resultado
            $almacenarResultado = $this->almacenarResultado($result, $fecha, $responseCode, $hora, $idDatos, $mensaje);
            $almacenarNombresBuscado = $this->almacenarNombres($primerNombre, $segundoNombre, $primerApellido, $segundoApellido);
            $almacenarConsulta = '';

            if ($almacenarNombresBuscado == true && $almacenarDatos == true && $nuevoContador == true && $almacenarResultado == true) {
                $idSS = $_SESSION['id_usuario'];
                $obtenerUltimoRegistroNombres = nombres::latest('id_nombres')->first();// Obtengo el ultimo registro que se haya ingresado
                $idNombres = $obtenerUltimoRegistroNombres->id_nombres;

                $idCui = null;
                $idTipo = $tipoConsulta;

                $obtenerUltimoRegistroContador = contador::latest('id_contador')->first();// Obtengo el ultimo registro que se haya ingresado
                $idContador = $obtenerUltimoRegistroContador->id_contador;

                $obtenerUltimoRegistroResultado = resultado::latest('id_resultado')->first();// Obtengo el ultimo registro que se haya ingresado
                $idResultado = $obtenerUltimoRegistroResultado->id_resultado;

                $almacenarConsulta = $this->almacenarConsultaNombres($idSS, $idCui, $idNombres, $idTipo, $idContador, $idResultado, $idTokenActivo);
                if ($almacenarConsulta == true) {
                    $consultaCuiBitacora = json_encode($consultaNombres);
                    if ($consultaNombres !== 'Ha ocurrido un error en la comunicación con el WS') {
                        $obtenerUltimoRegistro = log_bitacora_busquedas::latest('id_log_busqueda')->first();// Obtengo el ultimo registro que se haya ingresado
                        $numeroConsulta = "";
                        if ($obtenerUltimoRegistro !== null) {
                            $numeroConsulta = $obtenerUltimoRegistro->numero_consulta;
                            $numeroConsulta = $numeroConsulta + 1;
                        } else {
                            $numeroConsulta = 1;
                        }

                        if ($mensaje !== "Token inválido BD, por favor verificar") {
                            $idUSS = $_SESSION['id_usuario'];
                            $ingresarBitacora = $this->ingresarBitacora($consultaCuiBitacora, $numeroConsulta, $idUSS);
                        }
                    }

                    DB::connection('mysql')->commit();
                    return response()->json([
                        'title'    => 'Listo!',
                        'message'  => $mensaje,
                        'consulta' => $consultaNombres
                    ], 200);
                } else {
                    $consultaCuiBitacora = json_encode($consultaNombres);
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
                    DB::connection('mysql')->rollBack();
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

    public function realizarConsultaNombres($primerNombre, $segundoNombre, $primerApellido, $segundoApellido, $fechaNacimiento, $tokenActivo)
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
//        $paramsBusquedaCui = '{"busquedaCui":{"cui": ' . $cui . '}}';
        if ($fechaNacimiento === "//") {
            $paramsBusquedaNombres = '{"busquedaCui":{"cui": null},"busquedaNombres":{"primerNombre":"' . $primerNombre . '","segundoNombre":"' . $segundoNombre . '","primerApellido":"' . $primerApellido . '","segundoApellido":"' . $segundoApellido . '","fechaNacimiento":null}}';
        } else {
            $paramsBusquedaNombres = '{"busquedaCui":{"cui": null},"busquedaNombres":{"primerNombre":"' . $primerNombre . '","segundoNombre":"' . $segundoNombre . '","primerApellido":"' . $primerApellido . '","segundoApellido":"' . $segundoApellido . '","fechaNacimiento":"' . $fechaNacimiento . '"}}';
        }

        curl_setopt($curl, CURLOPT_POSTFIELDS, $paramsBusquedaNombres);
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

    public function almacenarNombres($primerNombre, $segundoNombre, $primerApellido, $segundoApellido)
    {
        $nombresAlmacenar = false;
        $almacenarNombres = new nombres();
        $almacenarNombres->primer_nombre = $primerNombre;
        $almacenarNombres->segundo_nombre = $segundoNombre;
        $almacenarNombres->primer_apellido = $primerApellido;
        $almacenarNombres->segundo_apellido = $segundoApellido;
        if ($almacenarNombres->save()) {
            return $nombresAlmacenar = true;
        }
        return $nombresAlmacenar = false;
    }

    public function almacenarConsultaNombres($idSS, $idCui, $idNombres, $idTipo, $idContador, $idResultado, $idToken)
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
}

