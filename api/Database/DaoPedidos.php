<?php

namespace DB;

use PDO;

class DAOPedidos
{


    private $_connectionString;
    private $_dbName;
    private $_user;
    private $_password;
    private $_dao;
    private $_dbPlusTable;


    public function __construct($dbname, $user = 'b6a01266d7dd99', $password = '0fac4371')
    {
        $this->_dbName = $dbname;
        $this->_user = $user;
        $this->_password = $password;
        $this->_connectionString = 'mysql:host=us-cdbr-east-02.cleardb.com; dbname=' . $this->_dbName;
        $this->_dbPlusTable = $this->_dbName . ".pedidos";
    }

    private function connectDatabase()
    {
        try {
            $this->_dao = new PDO($this->_connectionString, $this->_user, $this->_password);
            return true;
        } catch (\Throwable $th) {
            echo 'Could not connect Database';
            return false;
        }
    }

    public function cargarPedidoDB($pedido)
    {
        if ($this->connectDatabase()) {
            $orden = implode(" ", $pedido->orden);
            $query = $this->_dao->prepare("INSERT INTO $this->_dbPlusTable (orden, idEmpleado, idMesa, code, hora, date, status) VALUES(:orden, :idEmpleado, :idMesa, :code, :hora, :fecha, :status) ");
            $query->bindParam(':orden', $orden);
            $query->bindParam(':idEmpleado', $pedido->id_empleado);
            $query->bindParam(':idMesa', $pedido->id_mesa);
            $query->bindParam(':code', $pedido->code);
            $query->bindParam(':hora', $pedido->hora);
            $query->bindParam(':fecha', $pedido->fecha);
            $query->bindParam(':status', $pedido->status);
            return $query->execute();
        }
    }


    public function cambiarEstadoPedido($orden)
    {
        if ($this->connectDatabase()) {
            $date = date('H-m-s');

            if ($orden->status == 'entregado') {
                $query = $this->_dao->prepare("UPDATE $this->_dbPlusTable SET status = :estado, horaEntrega = :hora WHERE code = :code");
                $query->bindParam(':estado', $orden->status);
                $query->bindParam(':code', $orden->code);
                $query->bindParam(':hora', $date);
                return $query->execute();
            } else {
                $query = $this->_dao->prepare("UPDATE $this->_dbPlusTable SET status = :estado WHERE code = :code");
                $query->bindParam(':estado', $orden->status);
                $query->bindParam(':code', $orden->code);
                return $query->execute();
            }
        }
    }


    public function getPedidosDB()
    {
        if ($this->connectDatabase()) {
            $query = $this->_dao->prepare("SELECT * FROM $this->_dbPlusTable");
            $query->execute();
            return $query->fetchAll(PDO::FETCH_OBJ);
        }
    }
}
