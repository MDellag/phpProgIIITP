<?php

namespace Entities;

use DB\DAO;
use DB\DAOEmpleados;
use Exception;
use stdClass;

class Empleado
{

    private $_personalInfo;
    // private $_idEmpleado;

    function __construct($name, $lastname, $dni)
    {
        $this->_personalInfo = new stdClass();
        $this->_personalInfo->name = $name;
        $this->_personalInfo->lastname = $lastname;
        $this->_personalInfo->dni = $dni;
    }

    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
    }

    static function AltaEmpleadoDB($empleado)
    {
        $db = new DAOEmpleados('heroku_5c10d497ff4fe74');
        if (!$db->existsEmployeeDB($empleado->dni)) {
            $db->saveEmpleadoDB($empleado);
        } else
            throw new Exception("Empleado con dni: " . $empleado->dni . " ya existe.");
    }

    static function getAllEmployees()
    {
        $db = new DAOEmpleados('heroku_5c10d497ff4fe74');
        return $db->getAllEmployeesDB();
    }

    static function getEmployees()
    {
        $db = new DAOEmpleados('heroku_5c10d497ff4fe74');
        return $db->getEmployeesDB();
    }

    static function getEmployeeByDni($dni)
    {
        $db = new DAOEmpleados('heroku_5c10d497ff4fe74');
        $res = $db->getEmployeeByDniDB($dni);
        if(!$res)
            throw new Exception('No se Encontro al Empleado');
        else
        return $res;
    }

    static function deleteEmployeeByDni($dni){
        $db = new DAOEmpleados('heroku_5c10d497ff4fe74');
        return $db->deleteEmployeeDB($dni);
    }

    static function updateEmployee($empleado){
        $db = new DAOEmpleados('heroku_5c10d497ff4fe74');
         if(!$db->updateEmployeeDB($empleado))
         throw new Exception('No se ha logrado Updatear al Empleado');
    }
}
