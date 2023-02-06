<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class resultado extends Model
{
    // en dado caso no se llame el modelo igual a la tabla
    protected $table = "resultado";
    //esto es solo si el la llave primaria de la tabla no es id
    protected $primaryKey = "id_resultado";
    public $timestamps = false;
}
