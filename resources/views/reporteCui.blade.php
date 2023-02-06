<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm"
          crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
            crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
            crossorigin="anonymous"></script>
    <title>Búsqueda por CUI</title>
    <style>
        h1 {
            text-align: center;
            text-transform: uppercase;
        }

        .contenido {
            font-size: 10px;
        }

        .centrar {
            text-align: left;
        }

        .ancho {
            width: auto;
        }

        .letra {
            font-size: 13px;
        }

        td, thTabla {
            border: 1px solid gray;
        }

        html {
            font-family: "Helvetica", sans-serif;
            width: 100%;
        }

        .referenciasReporte {
            font-size: 10px;
            text-align: right;
            margin-top: 5%;
        }


    </style>
</head>
<body>
<div>
    <table class="centrar">
        <tr>
            <th class="ancho"><img src="{{ asset('storage/assets/img/escudoRGP.png')}}" width="100px"></th>
            <th class="centrar">
                <span class="letra">{{$titulo}}</span> <br>
                <span class="letra">{{$tipoReporte}}</span> <br>
                <span class="letra">{{$tipo}}</span> <br>
            </th>
        </tr>
    </table>
</div>

<div style="margin-top: 3%;">
    <div class="card">
        <div class="card-body">
            <h5 style="text-align: center">Datos de Consulta</h5>
            <p style="color: #4E4F4F; font-size: 12px;"><u><b>CUI</b></u></p>
            <p style="font-size: 13px; color: black"><b>{{$datosEnviados}}</b></p>
            <span style="font-size: 10px">Consulta generada el:</span>
            <span style="font-size: 10px;"> {{$fechaConsulta}}</span>
            <span style="font-size: 10px;">{{$horaConsulta}}</span>
        </div>
    </div>
</div>


<div style="margin-top: 3%">
    <div class="card">
        <div class="card-body">
            <h5 style="text-align: center;">Datos de la Persona</h5>
            <div>
                <table style="border-color: red">
                    <tr>
                        <th style="width:150px; color: #4E4F4F; font-size: 12px"><u>CUI</u></th>
                        <th style="width:200px; color: #4E4F4F; font-size: 12px"><u>Primer Nombre</u></th>
                        <th style="width:200px; color: #4E4F4F; font-size: 12px"><u>Segundo Nombre</u></th>
                        <th style="width:150px; color: #4E4F4F; font-size: 12px"><u>Tercer Nombre</u></th>
                        <hr>
                    </tr>
                    <tr>
                        <th style="width:100px;font-size: 13px; color: black">{{$datosRecibidos->CUI}}</th>
                        <th style="width:200px;font-size: 13px; color: black">{{$datosRecibidos->PRIMER_NOMBRE}}</th>
                        <th style="width:200px;font-size: 13px; color: black">{{$datosRecibidos->SEGUNDO_NOMBRE}}</th>
                        <th style="width:150px;font-size: 13px; color: black">{{$datosRecibidos->TERCER_NOMBRE==null?'NULL':$datosRecibidos->TERCER_NOMBRE}}</th>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <table>
                <tr>
                    <th style="width:100px; color: #4E4F4F; font-size: 12px"><u>Primer Apellido</u></th>
                    <th style="width:150px; color: #4E4F4F; font-size: 12px"><u>Segundo Apellido</u></th>
                    <th style="width:50px; color: #4E4F4F; font-size: 12px"><u>Fecha de nacimiento</u></th>
                    <th style="width:50px; color: #4E4F4F; font-size: 12px"><u>Genero</u></th>
                    <th style="width:50px; color: #4E4F4F; font-size: 12px"><u>Estado Civil</u></th>
                    <hr>
                </tr>
                <tr>
                    <th style="width:200px;font-size: 13px;color: black">{{$datosRecibidos->PRIMER_APELLIDO}}</th>
                    <th style="width:175px;font-size: 13px;color: black">{{$datosRecibidos->SEGUNDO_APELLIDO}}</th>
                    <th style="width:125px;font-size: 13px;color: black">{{$datosRecibidos->FECHA_NACIMIENTO}}</th>
                    <th style="width:100px;font-size: 13px;color: black">{{$datosRecibidos->GENERO}}</th>
                    <th style="width:50px;font-size: 13px;color:black">{{$datosRecibidos->ESTADO_CIVIL}}</th>
                    <hr>
                </tr>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <table>
                <tr>
                    <th style="width:100px; color: #4E4F4F; font-size: 12px"><u>Nacionalidad</u></th>
                    <th style="width:150px; color: #4E4F4F; font-size: 12px"><u>Fecha de Defunción</u></th>
                    <th style="width:50px; color: #4E4F4F; font-size: 12px"><u>Ocupación</u></th>
                    <th style="width:50px; color: #4E4F4F; font-size: 12px"><u>Vecindad</u></th>
                    <hr>
                </tr>
                <tr>
                    <th style="width:200px;font-size: 13px;color: black">{{$datosRecibidos->NACIONALIDAD}}</th>
                    <th style="width:175px;font-size: 13px;color: black">{{$datosRecibidos->FECHA_DEFUNCION==null?'NULL':$datosRecibidos->FECHA_DEFUNCION}}</th>
                    <th style="width:200px;font-size: 13px;color: black">{{$datosRecibidos->OCUPACION}}</th>
                    <th style="width:100px;font-size: 13px;color: black">{{$datosRecibidos->VECINDAD}}</th>
                    <hr>
                </tr>
            </table>
        </div>
    </div>
</div>

<div class="referenciasReporte">
    <span>Reporte Generado el {{$fechaCreacionReporte}}</span> <br>
    <span>Por: {{$_SESSION['nombre']}}</span>
</div>
<footer class="centrar">
    <script type="text/php">
    if (isset($pdf))
    {
        $text = "{{$fechaCreacionReporte}}                                                           ©RGP Sistema de Seguridad Registral                                                    Página {PAGE_NUM} / {PAGE_COUNT}";
        $size = 9;
        $font = $fontMetrics->getFont("Verdana");
        $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
        $x = ($pdf->get_width() - $width) / 12;
        $y = $pdf->get_height() - 25;
        $pdf->page_text($x, $y, $text, $font, $size);
    }
    </script>
    {{--    <script src="resources/js/components/consulta/respuesta.vue"></script>--}}
</footer>
</body>
</html>
{{--<script>--}}
{{--    import Respuesta from "../js/components/consulta/respuesta";--}}

{{--    export default {--}}
{{--        components: {Respuesta}--}}
{{--    }--}}
{{--</script>--}}