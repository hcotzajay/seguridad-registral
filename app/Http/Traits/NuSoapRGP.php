<?php
/**
 * Created by PhpStorm.
 * User: lorozco
 * Date: 20/03/2019
 * Time: 04:36 PM
 * ref: https://packagist.org/packages/econea/nusoap
 */

namespace App\Http\Traits;

trait NuSoapRGP
{
    protected static $wsdl;

    function init()
    {
        $wsdlUrl = self::getWsdl();
        $instance = new \nusoap_client($wsdlUrl, true);
        self::setOptions($instance);

        return $instance;
    }

    /**
     * @param $webService
     * @param null $server
     * @param string $port
     * @return string
     */
    function setWsdl($webService, $server = NULL, $port = '')
    {
        $server = !isset($server) ? $server = env('NAME_SERVER_APLICACIONES') : $server;
        return self::$wsdl = $server . $port . $webService;
    }

    function setWsdl2($webService)
    {
        return self::$wsdl = $webService;
    }

    function setOptions($instance)
    {
        $instance->soap_defencoding = 'UTF-8';
        $instance->decode_utf8 = false;

        return $instance;
    }

    function getWsdl()
    {
        return self::$wsdl;
    }
}
