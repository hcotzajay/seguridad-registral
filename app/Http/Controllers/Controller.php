<?php

namespace App\Http\Controllers;

use App\Http\Traits\NuSoapRGP;
use App\log_bitacora;
use App\log_bitacora_busquedas;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, NuSoapRGP;

    /**
     * @param Collection $collection Colección de datos que contiene la propiedad con el ID de Seguridad del empleado
     *                                    [
     *                                        "id_turno" => 1
     *                                        "no_documento" => "22R123456789"
     *                                        "id_asesor" => 348
     *                                    ]
     *
     * @param String $nombrePropiedad La propiedad de la colección que contiene el ID de Seguridad
     *                                  "id_asesor"
     *
     * @return Collection               Colección de datos con una nueva propiedad "nombre_colaborador"
     *                                    [
     *                                        "id_turno" => 1
     *                                        "no_documento" => "22R123456789"
     *                                        "id_asesor" => 348
     *                                        "nombre_colaborador" => "Marlon André  Pineda Hernández"
     *                                    ]
     * @IMPORTANTE                      Validar que lo que retorne sea una colección
     *                                  $result = agregarNombreDelColaborador($miCollection, 'id_asesor')
     *                                  if(!is_a($result, 'Illuminate\Database\Eloquent\Collection')) {
     *                                      return $result;
     *                                   }
     */
    public function agregarNombreDelColaborador($collection, $nombrePropiedad)
    {
        $unique = $collection->unique(function ($item) use ($nombrePropiedad) {
            return $item->$nombrePropiedad;
        });

        $cod_string = "";
        foreach ($unique as $usuario) {
            $cod_string = $cod_string . $usuario->$nombrePropiedad . ",";
        }
        $cod_string = substr($cod_string, 0, -1);

        $this->setWsdl(env('SERVER_APLICACIONES_WSDL'));
        $client = $this->init();
        $datosusuarios = $client->call('DatosUsuarioSS', ['codigos' => $cod_string]);

        // validar si responde de manera correcta el ws
        if (empty($datosusuarios)) {
            return response([
                'message' => 'Error en la comunicación con el WS -DatosUsuarioSS-'
            ], 503);
        }

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->loadXML($datosusuarios);
        $datosusuarios = $dom->getElementsByTagName('usuario');

        $nombres_usuarios = [];
        foreach ($datosusuarios as $usuario) {
            $nombres_usuarios[intval($usuario->getAttribute("codigoss"))] = $usuario->textContent;
        }

        // Búsqueda y adición del nombre
        $collection = $collection->map(function ($item) use ($nombres_usuarios, $nombrePropiedad) {
//            $item->nombre_colaborador = $nombres_usuarios[$item->$nombrePropiedad];
//            return $item;
            if ($item->$nombrePropiedad != null) {
                if (array_key_exists($item->$nombrePropiedad, $nombres_usuarios)) {
                    $item->nombre_colaborador = $nombres_usuarios[$item->$nombrePropiedad];
                } else {
                    $item->nombre_colaborador = 'Problemas con este id';
                }
            }
            return $item;
        });

        return $collection;
    }

    public function generarToken()
    {
        $url = env('API_SERVER') . env('API_PORT') . env('API_URI');
        $curl = curl_init();
        $user = env('API_USER');
        $pass = env('API_PASS');
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, TRUE);
        $headers = [
            'user:' . $user,
            'pass:' . $pass,
            'Content-Length:0',
            'Host: nginx'
        ];
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        $data = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            return response([
                'message' => 'Error en la comunicación con el WS -Renap-.' . $err
            ], 503);
        } else {

        }
    }

    // Controlador que se encarga de ejecutar la bitacora
    public function ingresarBitacora($resultadoBusqueda, $numeroConsulta, $idUSS)
    {
        $log_bitacora = new log_bitacora_busquedas();
        $log_bitacora->numero_consulta = $numeroConsulta;
        $log_bitacora->resultado_busqueda = $resultadoBusqueda;
        $log_bitacora->id_usuario_ss = $idUSS;
        if ($log_bitacora->save()) {
            return true;
        }
        return false;
    }
}
