<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class c_tipo extends Model
{
    // en dado caso no se llame el modelo igual a la tabla
    protected $table = "c_tipo";
    //esto es solo si el la llave primaria de la tabla no es id
    protected $primaryKey = "id_tipo";
    public $timestamps = false;
}
