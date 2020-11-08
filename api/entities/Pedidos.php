<?php

namespace Entities;

use stdClass;
use DB\DAOPedidos;
define("DBNAME", "comanda");

class Pedido
{

    private $_pedidoInfo;

    function __construct($orden, $idEmpleado, $idMesa, $code, $status = 'solicitado')
    {
        $this->_pedidoInfo = new stdClass();
        $this->_pedidoInfo->orden = $orden;
        $this->_pedidoInfo->id_empleado = $idEmpleado;
        $this->_pedidoInfo->id_mesa = $idMesa;
        $this->_pedidoInfo->code = $code;
        $this->_pedidoInfo->hora = date("H:m:s");
        $this->_pedidoInfo->fecha = date("Y-m-d");
        $this->_pedidoInfo->status = $status;
    }

    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
    }


    public function ordenarPedido()
    {   
        $db = new DAOPedidos(DBNAME);
        $db->cargarPedidoDB($this->_pedidoInfo);
    }

    public static function GenerateCode()
    {
        define("CHARCODE", "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ");
        return substr(str_shuffle(CHARCODE), 0, 5);
    }

    public static function obtenerPedidos()
    {   
        $db = new DAOPedidos(DBNAME);
        return $db->getPedidosDB();
    }
}
