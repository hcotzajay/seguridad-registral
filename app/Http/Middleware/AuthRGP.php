<?php
namespace App\Http\Middleware;
session_start();

use Closure;

class AuthRGP
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /*$array = [];
        $array['sessionValid'] = $request->getSession()->isValidId('MotuZcrQo52Uwgelj23qTK0T9M2qusUn8yLTgb9G');
        $array['sessionContent'] = $request->session()->all();
        $array['sessionContent2'] = file_get_contents('../storage/framework/sessions/MotuZcrQo52Uwgelj23qTK0T9M2qusUn8yLTgb9G');// $request->session()->all();*/

        //if (!\request()->session()->has('id_usuario')) {
        //dd($_SESSION);
        if (!isset($_SESSION['id_usuario'])) {
            return response([
                'message' => 'User no logeado.'
            ], 401);
        }

        return $next($request);
    }
}
