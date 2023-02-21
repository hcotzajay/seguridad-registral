<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Rango de Fechas</title>
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
                <span class="letra">{{$tipoReporte}}</span><br>
                <span class="letra">{{$tipo}}</span>
            </th>
        </tr>
    </table>
</div>

<hr>
<div class="contenido">
    <table>
        <thead>
        <tr class="bordeTabla">
            @foreach ($encabezado as $columnas)
                <th class="thTabla">{{$columnas}}</th>
            @endforeach
        </tr>
        </thead>
        <tbody>
        <?php
        $contador = 0;
        ?>
        @foreach ($datosReporte as $info)
            <tr>
                <td style="width: 25px; text-align: center"><?php echo $contador = $contador + 1; ?></td>
                <td style="width: 150px; text-align: center">{{$info->fecha_busqueda}}</td>
                <td style="width: 150px; text-align: center">{{$info->id_tipo}}</td>
                <td style="width: 100px; text-align: center">{{$info->ip}}</td>
                <td style="width: 175px; text-align: center">{{$info->nombre_colaborador}}</td>
                <td style="width: 100px; text-align: center">{{$info->result}}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        </tfoot>
    </table>
</div>

<div class="referenciasReporte">
    <span>Reporte Generado el {{$fechaCreacionReporte}}</span> <br>
    <span>Por: {{$_SESSION['nombre']}}</span>
</div>
<footer class="centrar">
    <script type="text/php">
    if (isset($pdf))
    {
        $text = "{{$fechaCreacionReporte}}                                                               © RGP - Sistema de Timbres                                                            Página {PAGE_NUM} / {PAGE_COUNT}";
        $size = 9;
        $font = $fontMetrics->getFont("Verdana");
        $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
        $x = ($pdf->get_width() - $width) / 12;
        $y = $pdf->get_height() - 25;
        $pdf->page_text($x, $y, $text, $font, $size);
    }





















    </script>

</footer>
</body>
</html>