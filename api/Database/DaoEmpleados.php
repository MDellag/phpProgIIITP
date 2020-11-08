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
    private $_dbPlusTable;

    public function __construct($dbname, $user = 'b6a01266d7dd99', $password = '0fac4371')
    {
        $this->_dbName = $dbname;
        $this->_user = $user;
        $this->_password = $password;
        $this->_connectionString = 'mysql:host=us-cdbr-east-02.cleardb.com; dbname=' . $this->_dbName;
        $this->_dbPlusTable = $this->_dbName . ".empleados";
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
            $query = $this->_dao->prepare("INSERT INTO  $this->_dbPlusTable (name, lastname, dni, creationDate) VALUE (:name, :lastname, :dni, :creationDate)");
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
            $query = $this->_dao->prepare("SELECT * FROM  $this->_dbPlusTable");
            $query->execute();
            return $query->fetchAll(PDO::FETCH_OBJ);
        }
    }

    public function getEmployeesDB()
    {
        if ($this->connectDatabase()) {
            $query = $this->_dao->prepare("SELECT * FROM  $this->_dbPlusTable WHERE dropDate IS NULL");
            $query->execute();
            return $query->fetchAll(PDO::FETCH_OBJ);
        }
    }

    public function getEmployeeByDniDB($dni)
    {
        if ($this->connectDatabase()) {
            $query = $this->_dao->prepare("SELECT * FROM  $this->_dbPlusTable where dni = :dni");
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
            $query = $this->_dao->prepare("UPDATE  $this->_dbPlusTable set name = :name, lastname = :lastname where dni = :dni");
            $query->bindParam(':name', $empleado->name);
            $query->bindParam(':lastname', $empleado->lastname);
            $query->bindParam(':dni', $empleado->dni);

            return $query->execute();
        }
    }

    public function deleteEmployeeDB($dni)
    {
        if ($this->connectDatabase()) {
            $query = $this->_dao->prepare("DELETE FROM  $this->_dbPlusTable where dni = :dni");
            $query->bindParam(':dni', $dni);
            return $query->execute();
        }
    }

    public function dropEmployeeDB($dni)
    {
        if ($this->connectDatabase()) {
            $date = date('Y-m-d');
            $query = $this->_dao->prepare("UPDATE  $this->_dbPlusTable set dropDate = :dropDate  where dni = :dni");
            $query->bindParam(':dropDate', $date);
            $query->bindParam(':dni', $dni);
            return $query->execute();
        }
    }

    public function getIdEmpleados(){
        if($this->connectDatabase()){
            $query = $this->_dao->prepare("SELECT idEmpleado FROM $this->_dbPlusTable");
            $query->execute();
            return $query->fetchALL(PDO::FETCH_OBJ);
        }
    }
}
