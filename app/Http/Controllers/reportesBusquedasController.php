<?php

namespace App\Http\Controllers;

use App\c_tipo;
use App\consulta;
use App\contador;
use App\cui;
use App\datos;
use App\nombres;
use App\Token\token;
use Facade\Ignition\SolutionProviders\DefaultDbNameSolutionProvider;
use Illuminate\Http\Request;

class reportesBusquedasController extends Controller
{
    public function generarReporteCui(Request $request)
    {
        $params = json_decode($request->json);
        $datosEnviados = $params->cui;
        $fechaConsulta = $params->datosObtenidos->consulta->fecha;
        $horaConsulta = $params->datosObtenidos->consulta->hora;
        $datosRecibidos = $params->datosObtenidos->consulta->data[0];
        $respuestaDatosObtenidos = $params->datosObtenidos->consulta->result;
        if ($respuestaDatosObtenidos == false) {
            echo '< script type = "text/javascript" >';
            echo 'alert("No existe información válida para generar el reporte, por favor intente de nuevo.")';  //not showing an alert box.
            echo '</script >';
            echo "<script languaje='javascript'type='text / javascript'>window.close();</script>";
            return;
        } else {
            $fechaCreacionReporte = now();
            $titulo = 'REGISTRO GENERAL DE LA PROPIEDAD';
            $tipoReporte = 'SISTEMA DE SEGURIDAD REGISTRAL';
            $tipo = 'CONSULTA A RENAP';
            $view = \View::make('reporteCui', compact('datosRecibidos', 'titulo', 'tipoReporte', 'tipo', 'fechaCreacionReporte', 'datosEnviados', 'fechaConsulta', 'horaConsulta'))->render();
            $pdf = \PDF::loadHTML($view);
            $pdf->setPaper('letter');
            $pdf->getDomPDF()->set_option("enable_php", true);
            $fecha = date("Y-m-d H:i:s");
            return $pdf->stream('Reporte timbres devueltos' . "$fecha" . '.pdf');
        }
    }

    public function generarReporteNombres(Request $request)
    {
        $params = json_decode($request->json);
        $datosEnviados = [
            'primerNombre'    => $params->primerNombre,
            'segundoNombre'   => $params->segundoNombre,
            'primerApellido'  => $params->primerApellido,
            'segundoApellido' => $params->segundoApellido,
            'fechaNacimiento' => $params->fechaNacimiento,
        ];
        $fechaConsulta = $params->datosObtenidos->consulta->fecha;

        $horaConsulta = $params->datosObtenidos->consulta->hora;
        $datosRecibidos = $params->datosObtenidos->consulta->data[0];
        $respuestaDatosObtenidos = $params->datosObtenidos->consulta->result;
        if ($respuestaDatosObtenidos == false) {
            echo '< script type = "text/javascript" >';
            echo 'alert("No existe información válida para generar el reporte, por favor intente de nuevo.")';  //not showing an alert box.
            echo '</script >';
            echo "<script languaje='javascript'type='text / javascript'>window.close();</script>";
            return;
        } else {
            $fechaCreacionReporte = now();
            $titulo = 'REGISTRO GENERAL DE LA PROPIEDAD';
            $tipoReporte = 'SISTEMA DE SEGURIDAD REGISTRAL';
            $tipo = 'CONSULTA A RENAP';
            $view = \View::make('reporteNombres', compact('datosRecibidos', 'titulo', 'tipoReporte', 'tipo', 'fechaCreacionReporte', 'datosEnviados', 'fechaConsulta', 'horaConsulta'))->render();
            $pdf = \PDF::loadHTML($view);
            $pdf->setPaper('letter');
            $pdf->getDomPDF()->set_option("enable_php", true);
            $fecha = date("Y-m-d H:i:s");
            return $pdf->stream('Reporte timbres devueltos' . "$fecha" . '.pdf');
        }
    }

    public function consultarTokensUtilizados(Request $request)
    {
        $fechaInicio = $request['fechaInicio'];
        $fechaFin = $request['fechaFin'];
        $obtenerInfoToken = token::select()
//            ->join('consulta', 'token.id_token', '=', 'consulta.id_token')
            ->where('fecha_token', '>=', $fechaInicio . ' 00:00:00')
            ->where('fecha_token', '<=', $fechaFin . ' 23:59:59')
            ->get();

        $obtenerInfoToken->toArray();
//        $nombreColaborador = $this->agregarNombreDelColaborador($obtenerInfoToken, 'id_ss');
//        if (!is_a($nombreColaborador, 'Illuminate\Database\Eloquent\Collection')) {
//            return $nombreColaborador;
//        }


        if ($obtenerInfoToken->isEmpty()) {
            return response()->json([
                'title'   => 'Atención!',
                'message' => 'No se encontraron resultados con los parámetros ingresados.'
            ], 500);
        }


        foreach ($obtenerInfoToken as &$valor) {
            $fecha = date('d/m/Y H:i:s', strtotime($valor['fecha_token']));
            $valor['fecha_token'] = $fecha;
        }

        return response()->json([
            'resultados' => $obtenerInfoToken
        ], 200);
    }

    public function consultarRangoFechas(Request $request)
    {
        $fechaInicio = $request['fechaInicio'];
        $fechaFin = $request['fechaFin'];
        $obtenerInfoConsulta = consulta::select()
            ->join('resultado', 'consulta.id_resultado', '=', 'resultado.id_resultado')
            ->where('fecha_busqueda', '>=', $fechaInicio . ' 00:00:00')
            ->where('fecha_busqueda', '<=', $fechaFin . ' 23:59:59')
            ->get();

        $obtenerInfoConsulta->toArray();
        $nombreColaborador = $this->agregarNombreDelColaborador($obtenerInfoConsulta, 'id_ss');
        if (!is_a($nombreColaborador, 'Illuminate\Database\Eloquent\Collection')) {
            return $nombreColaborador;
        }

        if ($obtenerInfoConsulta->isEmpty()) {
            return response()->json([
                'title'   => 'Atención!',
                'message' => 'No se encontraron resultados con los parámetros ingresados.'
            ], 500);
        }

        foreach ($obtenerInfoConsulta as &$valor) {
            $fecha = date('d/m/Y H:i:s', strtotime($valor['fecha_token']));
            $tipoBusqueda = $valor['id_tipo'];
            $valor['id_tipo'] = $this->consultarTipoBusqueda($tipoBusqueda);
            $valor['fecha_token'] = $fecha;
            if ($valor['result'] == 1) {
                $valor['result'] = "Sí";
            } else {
                $valor['result'] = "No";
            }
        }

        return response()->json([
            'resultados' => $obtenerInfoConsulta
        ], 200);
    }

    public function consultarTipoBusqueda($idTipoBusqueda)
    {
        $tipoBusqueda = c_tipo::select()
            ->where('id_tipo', '=', $idTipoBusqueda)
            ->first();
        $busqueda = $tipoBusqueda['tipo'];
        return $busqueda;
    }

    public function consultarDatosRespuesta(Request $request)
    {
        $idDatos = $request->idResultado;
        $obtenerDatos = datos::select()
            ->where('id_datos', '=', $idDatos)
            ->get();

        $datosBuscados = $obtenerDatos[0]['data_json'];
        $datos = json_decode($datosBuscados);
        $fecha = $datos->fecha;
        $hora = $datos->hora;
        return response()->json([
            'datos' => $datos->data[0],
            'fecha' => $fecha,
            'hora'  => $hora
        ], 200);
    }

    public function generarReporteRangoFechas(Request $request)
    {
        $params = json_decode($request->json);
        $datosReporte = $params->datosObtenidos;
        $fechaInicio = $params->fechaInicial;
        $fechaFinal = $params->fechaFinal;
        $fechaFormatInicio = $this->convertirFecha($fechaInicio);
        $fechaFormatFinal = $this->convertirFecha($fechaFinal);
        if (count($params->datosObtenidos) < 1) {
            echo '< script type = "text/javascript" >';
            echo 'alert("No existe información válida para generar el reporte, por favor intente de nuevo.")';  //not showing an alert box.
            echo '</script >';
            echo "<script languaje='javascript'type='text / javascript'>window.close();</script>";
            return;
        } else {
            $encabezado = array(
                "numero"             => "#",
                "fecha_busqueda"     => "Fecha de consulta",
                "id_tipo"            => "Tipo de Búsqueda",
                "ip"                 => "Ip de Búsqueda",
                "nombre_colaborador" => "Nombre del Colaborador",
                "result"             => "Obtuvo resultado",
            );

            $fechaCreacionReporte = now();
            $titulo = 'REGISTRO GENERAL DE LA PROPIEDAD';
            $tipoReporte = 'SISTEMA DE SEGURIDAD REGISTRAL';
            $tipo = 'REPORTE RANGO DE FECHAS DEL: ' . $fechaFormatInicio . ' AL: ' . $fechaFormatFinal;
            $view = \View::make('reporteRangoFechas', compact('datosReporte', 'titulo', 'tipoReporte', 'tipo', 'fechaCreacionReporte', 'encabezado'))->render();
            $pdf = \PDF::loadHTML($view);
            $pdf->setPaper('letter');
            $pdf->getDomPDF()->set_option("enable_php", true);
            $fecha = date("Y-m-d H:i:s");
            return $pdf->stream('Reporte timbres devueltos' . "$fecha" . '.pdf');
        }
    }

    public function generarReporteResultadoBusqueda(Request $request)
    {
        $params = json_decode($request->json);
        $datosRecibidos = $params->datosBusqueda;
        $fechaConsulta = $params->fecha;
        $horaConsulta = $params->hora;
        if ($datosRecibidos->CUI == null) {
            echo '<script type = "text/javascript" >';
            echo 'alert("No existe información válida para generar el reporte, por favor intente de nuevo.")';  //not showing an alert box.
            echo '</script >';
            echo "<script languaje='javascript' type='text / javascript'>window.close();</script>";
            return;
        } else {
            $fechaCreacionReporte = now();
            $titulo = 'REGISTRO GENERAL DE LA PROPIEDAD';
            $tipoReporte = 'SISTEMA DE SEGURIDAD REGISTRAL';
            $tipo = 'DATOS DE BÚSQUEDA';
            $view = \View::make('datosBusqueda', compact('datosRecibidos', 'titulo', 'tipoReporte', 'tipo', 'fechaCreacionReporte', 'fechaConsulta', 'horaConsulta'))->render();
            $pdf = \PDF::loadHTML($view);
            $pdf->setPaper('letter');
            $pdf->getDomPDF()->set_option("enable_php", true);
            $fecha = date("Y-m-d H:i:s");
            return $pdf->stream('Reporte timbres devueltos' . "$fecha" . '.pdf');
        }
    }

    public function consultarContadorRangoFechas(Request $request)
    {
        $fechaInicio = $request['fechaInicio'];
        $fechaFin = $request['fechaFin'];

        $obtenerInfoContador = contador::select()
            ->join('consulta', 'contador.id_contador', '=', 'consulta.id_contador')
            ->where('fecha_contador', '>=', $fechaInicio . ' 00:00:00')
            ->where('fecha_contador', '<=', $fechaFin . ' 23:59:59')
            ->get();

        $obtenerInfoContador->toArray();
        $nombreColaborador = $this->agregarNombreDelColaborador($obtenerInfoContador, 'id_ss');
        if (!is_a($nombreColaborador, 'Illuminate\Database\Eloquent\Collection')) {
            return $nombreColaborador;
        }


        if ($obtenerInfoContador->isEmpty()) {
            return response()->json([
                'title'   => 'Atención!',
                'message' => 'No se encontraron resultados con los parámetros ingresados.'
            ], 500);
        }


        foreach ($obtenerInfoContador as &$valor) {
            if ($valor['id_cui'] != null && $valor['id_nombres'] == null) {
                $obtenerCuiBuscado = cui::select()
                    ->where('id_cui', '=', $valor['id_cui'])
                    ->get();
                $valor['cuiBuscado'] = $obtenerCuiBuscado[0]['cui'];
                $valor['nombreBusqueda'] = null;
            } elseif ($valor['id_cui'] == null && $valor['id_nombres'] != null) {
                $obtenerNombresBuscados = nombres::select()
                    ->where('id_nombres', '=', $valor['id_nombres'])
                    ->get();
                $valor['nombreBusqueda'] = $obtenerNombresBuscados[0]['primer_nombre'] . ' ' . $obtenerNombresBuscados[0]['segundo_nombre'] . ' ' . $obtenerNombresBuscados[0]['primer_apellido'] . ' ' . $obtenerNombresBuscados[0]['segundo_apellido'];
                $valor['cuiBuscado'] = null;
            }

            $obtenerUltimoRegistroContador = contador::latest('id_contador')->first();// Obtengo el ultimo registro que se haya ingresado
            $contador = $obtenerUltimoRegistroContador->contador;
            $contador = 25 - $contador; // consultas restantes

            $tipoBusqueda = $this->tipoBusqueda($valor['id_tipo']);
            $valor['tipoBusqueda'] = $tipoBusqueda;
            $fecha = date('d/m/Y H:i:s', strtotime($valor['fecha_token']));
            $valor['fecha_token'] = $fecha;
        }

        return response()->json([
            'resultados' => $obtenerInfoContador,
            'contador'   => $contador
        ], 200);
    }

    public function convertirFecha($fecha)
    {
        $fechaEntero = strtotime($fecha);
        $anio = date("Y", $fechaEntero);
        $mes = date("m", $fechaEntero);
        $dia = date("d", $fechaEntero);
        $formateandoFecha = $dia . '/' . $mes . '/' . $anio;
        return $formateandoFecha;
    }

    public function tipoBusqueda($idTipo)
    {
        $tipoBusqueda = c_tipo::select()
            ->where('id_tipo', '=', $idTipo)
            ->get();
        return $tipoBusqueda[0]['tipo'];
    }

    public function generarReporteRangoFechasContador(Request $request)
    {
        $params = json_decode($request->json);
        $datosRecibidos = $params->datosObtenidos;
        $fechaInicio = $params->fechaInicial;
        $fechaFinal = $params->fechaFinal;
        $fechaFormatInicio = $this->convertirFecha($fechaInicio);
        $fechaFormatFinal = $this->convertirFecha($fechaFinal);
        if (count($datosRecibidos) < 1) {
            echo '<script type = "text/javascript" >';
            echo 'alert("No existe información válida para generar el reporte, por favor intente de nuevo.")';  //not showing an alert box.
            echo '</script >';
            echo "<script languaje='javascript' type='text / javascript'>window.close();</script>";
            return;
        } else {
            $encabezado = array(
                "numero"             => "#",
                "contador"           => "Contador",
                "fecha_contador"     => "Fecha Contador",
                "nombre_colaborador" => "Nombre colaborador",
                "tipoBusqueda"       => "Tipo de búsqueda",
                "cuiBuscado"         => "Cui de búsqueda",
                "nombreBusqueda"     => "Nombre de búsqueda",
            );
            $fechaCreacionReporte = now();
            $titulo = 'REGISTRO GENERAL DE LA PROPIEDAD';
            $tipoReporte = 'SISTEMA DE SEGURIDAD REGISTRAL';
            $tipo = 'Reporte';
            $view = \View::make('reporteContador', compact('datosRecibidos', 'titulo', 'encabezado', 'tipoReporte', 'tipo', 'fechaCreacionReporte', ))->render();
            $pdf = \PDF::loadHTML($view);
            $pdf->setPaper('letter');
            $pdf->getDomPDF()->set_option("enable_php", true);
            $fecha = date("Y-m-d H:i:s");
            return $pdf->stream('Reporte timbres devueltos' . "$fecha" . '.pdf');
        }
    }
}
