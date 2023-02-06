<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class log_bitacora_busquedas extends Model
{
    // en dado caso no se llame el modelo igual a la tabla
    protected $table = "log_bitacora_busquedas";
    //esto es solo si el la llave primaria de la tabla no es id
    protected $primaryKey = "id_log_busqueda";
    public $timestamps = false;
}
