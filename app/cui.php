<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class cui extends Model
{
    // en dado caso no se llame el modelo igual a la tabla
    protected $table = "cui";
    //esto es solo si el la llave primaria de la tabla no es id
    protected $primaryKey = "id_cui";
    public $timestamps = false;
}
