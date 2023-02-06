<?php

namespace App\Http\Controllers;

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
}
