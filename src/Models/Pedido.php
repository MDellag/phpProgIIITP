<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $table = 'pedidos';
    protected $primaryKey = 'codigo';
    public $incrementing = false;
 
   // protected $keyType = 'string';
   // public $timestamps = false;

   const CREATED_AT = null;
   const UPDATED_AT = null;
   const LAST_UPDATE = null;
}
