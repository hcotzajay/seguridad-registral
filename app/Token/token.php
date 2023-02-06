<?php

namespace App\Token;

use Illuminate\Database\Eloquent\Model;

class token extends Model
{
    // en dado caso no se llame el modelo igual a la tabla
    protected $table = "token";
    //esto es solo si el la llave primaria de la tabla no es id
    protected $primaryKey = "id_token";
    public $timestamps = false;
}
