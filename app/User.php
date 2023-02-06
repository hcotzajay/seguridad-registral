<?php

namespace App;

use App\Helpers\Helper;
use App\Http\Traits\NuSoapRGP;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable, NuSoapRGP;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function getMenu($user_id_SS)
    {
        $this->setWsdl(env('SERVER_APLICACIONES_WSDL'));
        $client = $this->init();

        //Menú según Sistema de Seguridad
        $menu = $client->call('GetMenuUsuarioSistema', ['usuario' => $user_id_SS, 'sistema' => env('SISTEMA_ID')]);

        if (empty($menu)) {
            return response([
                'message' => 'Error en la comunicación con el WS -GetMenuUsuarioSistema-'
            ], 503);
        }

        return xmlToArray(simplexml_load_string($menu), ['autoArray' => false, 'attributePrefix' => '']);
    }

    public function getPerfilUsuario($user_id_SS)
    {
        $this->setWsdl(env('SERVER_APLICACIONES_WSDL'));
        $client = $this->init();

        //Datos del usuario según RRHH
        $datos_user_RH = $client->call('DatosUsuario', ['idusuario' => $user_id_SS]);

        if (empty($datos_user_RH)) {
            return response([
                'message' => 'Error en la comunicación con el WS -DatosUsuario-'
            ], 503);
        }

        //Datos del usuario registrados en RRHH
        $datos_user_RH = xmlToArray(simplexml_load_string($datos_user_RH));
        //Fotografía del usuario
        $datos_user_RH['usuario']['foto'] = Helper::getFotografia($datos_user_RH['usuario']['codigo']);

        return $datos_user_RH;
    }
}
