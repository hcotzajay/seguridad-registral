<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class consulta extends Model
{
    // en dado caso no se llame el modelo igual a la tabla
    protected $table = "consulta";
    //esto es solo si el la llave primaria de la tabla no es id
    protected $primaryKey = "id_consulta";
    public $timestamps = false;
}
