<?php

namespace DB;

use \PDO;

class DAOEmpleados
{

    private $_connectionString;
    private $_dbName;
    private $_user;
    private $_password;
    private $_dao;

    public function __construct($dbname, $user = 'root', $password = '')
    {
        $this->_dbName = $dbname;
        $this->_user = $user;
        $this->_password = $password;
        $this->_connectionString = 'mysql:host=heroku_5c10d497ff4fe74; dbname=' . $this->_dbName;
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

    public function saveEmpleadoDB($empleado)
    {
        if ($this->connectDatabase()) {
            $date = date('Y-m-d');
            $query = $this->_dao->prepare("INSERT INTO comanda.empleados (name, lastname, dni, creationDate) VALUE (:name, :lastname, :dni, :creationDate)");
            $query->bindParam(':name', $empleado->name);
            $query->bindParam(':lastname', $empleado->lastname);
            $query->bindParam(':dni', $empleado->dni);
            $query->bindParam(':creationDate', $date);

            return $query->execute();
        }
    }

    public function getAllEmployeesDB()
    {
        if ($this->connectDatabase()) {
            $query = $this->_dao->prepare("SELECT * FROM comanda.empleados");
            $query->execute();
            return $query->fetchAll(PDO::FETCH_OBJ);
        }
    }

    public function getEmployeesDB()
    {
        if ($this->connectDatabase()) {
            $query = $this->_dao->prepare("SELECT * FROM comanda.empleados WHERE dropDate IS NULL");
            $query->execute();
            return $query->fetchAll(PDO::FETCH_OBJ);
        }
    }

    public function getEmployeeByDniDB($dni)
    {
        if ($this->connectDatabase()) {
            $query = $this->_dao->prepare("SELECT * FROM comanda.empleados where dni = :dni");
            $query->bindParam(':dni', $dni);
            $query->execute();
            return $query->fetch(PDO::FETCH_OBJ);
        }
    }

    public function existsEmployeeDB($dni)
    {
        $res = $this->getAllEmployeesDB();
        $bool = false;
        foreach ($res as $empleado) {
            if ($empleado->dni == $dni) {
                $bool = true;
                break;
            }
        }
        return $bool;
    }

    public function updateEmployeeDB($empleado)
    {
        if ($this->connectDatabase()) {
            $query = $this->_dao->prepare("UPDATE comanda.empleados set name = :name, lastname = :lastname where dni = :dni");
            $query->bindParam(':name', $empleado->name);
            $query->bindParam(':lastname', $empleado->lastname);
            $query->bindParam(':dni', $empleado->dni);

            return $query->execute();
        }
    }

    public function deleteEmployeeDB($dni)
    {
        if ($this->connectDatabase()) {
            $query = $this->_dao->prepare("DELETE FROM comanda.empleados where dni = :dni");
            $query->bindParam(':dni', $dni);
            return $query->execute();
        }
    }

    public function dropEmployeeDB($dni)
    {
        if ($this->connectDatabase()) {
            $date = date('Y-m-d');
            $query = $this->_dao->prepare("UPDATE comanda.empleados set dropDate = :dropDate  where dni = :dni");
            $query->bindParam(':dropDate', $date);
            $query->bindParam(':dni', $dni);
            return $query->execute();
        }
    }
}
