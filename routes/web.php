<?php

use Illuminate\Support\Facades\Route;

Route::middleware('authRGP')->group(function () {
    Route::GET('/menu', 'UserController@menu');

    ROUTE::POST('/personal/cambiaPIN', 'FirmaE\PINController@cambiaPIN');
    ROUTE::POST('/personal/cambiaPassword', 'UserController@cambiaPassword');

    ROUTE::POST('/consultaCui', 'Renap\consultaController@consultaCui');
    ROUTE::POST('/consultaNombres', 'Renap\consultaNombresController@consultaNombres');
    ROUTE::POST('/Token/generarToken', 'Token\tokenController@generarToken');
    ROUTE::GET('/logout', 'Auth\LogoutController@logout');
    ROUTE::GET('/generarReporteCui', 'reportesBusquedasController@generarReporteCui');
    ROUTE::GET('/generarReporteNombres', 'reportesBusquedasController@generarReporteNombres');
    ROUTE::GET('/consultarTokensUtilizados', 'reportesBusquedasController@consultarTokensUtilizados');
    ROUTE::GET('/consultarRangoFechas', 'reportesBusquedasController@consultarRangoFechas');
    ROUTE::GET('/consultarDatosRespuesta', 'reportesBusquedasController@consultarDatosRespuesta');
    ROUTE::GET('/generarReporteRangoFechas', 'reportesBusquedasController@generarReporteRangoFechas');
    ROUTE::GET('/generarReporteResultadoBusqueda', 'reportesBusquedasController@generarReporteResultadoBusqueda');
    ROUTE::GET('/consultarContadorRangoFechas', 'reportesBusquedasController@consultarContadorRangoFechas');
    ROUTE::GET('/generarReporteRangoFechasContador', 'reportesBusquedasController@generarReporteRangoFechasContador');
});


Route::POST('/login', 'Auth\LoginController@login');
Route::POST('/login/password-reset', 'Auth\LoginController@passwordReset');

Route::GET('/{any}', function () {
    return view('spa');
})->where('any', '.*');
