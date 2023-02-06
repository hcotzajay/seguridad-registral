<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class nombres extends Model
{
    // en dado caso no se llame el modelo igual a la tabla
    protected $table = "nombres";
    //esto es solo si el la llave primaria de la tabla no es id
    protected $primaryKey = "id_nombres";
    public $timestamps = false;
}
