<?php
/**
 * Created by PhpStorm.
 * User: lorozco
 * Date: 20/12/2018
 * Time: 10:18 AM
 */

namespace App\Helpers;

class Helper
{
    static function getFotografia($codigo_RH)
    {
        $url_fotos = env('NAME_SERVER_APLICACIONES') . '/humanos/cod/fotos/';

        $fotografia = [
            'P' . $codigo_RH . '.JPG',
            'P' . $codigo_RH . '.jpg'
        ];

        foreach ($fotografia as $foto) {
            if (!strpos(get_headers($url_fotos . $foto)[0], 'Not Found')) {
                return $url_fotos . $foto;
                break;
            }
        }

        return '../storage/assets/img/default.png';
    }
}
