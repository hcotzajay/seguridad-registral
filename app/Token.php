<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Token extends Model
{

    protected $table = null;


    public static function getToken($id) {
        Model::unguard();
        $token = DB::select(DB::raw("exec getToken :Param1"),[
            ':Param1' => $id,
        ]);
        Model::reguard();
        return new $token[0];
    }

    public static function setToken($t) {
        Model::unguard();
        $token = DB::select(DB::raw("exec SetToken :Param1"),[
            ':Param1' => $t,
        ]);
        Model::reguard();
        return new $token[0];
    }

}



