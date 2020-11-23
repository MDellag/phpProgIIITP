<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Empleado extends Model
{
     protected $table = 'empleados';
     protected $primaryKey = 'dni';
   

    const CREATED_AT = 'creationDate';
    const UPDATED_AT = null;
    const LAST_UPDATE = null;
   
}


