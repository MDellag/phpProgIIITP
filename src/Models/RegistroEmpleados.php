<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistroEmpleados extends Model
{
    protected $table = 'registro_ingreso_empleados';
    protected $primaryKey = 'email';
    public $timestamps = false;
}