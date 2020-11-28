<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Models\Empleado;
use App\Models\RegistroEmpleados;
use App\Models\Sector;

use Exception;
use stdClass;

class EmpleadoController
{

    public function getAll(Request $request, Response $response, $args)
    {
        $rta = Empleado::get();

        $response->getBody()->write(json_encode($rta));
        return $response;
    }

    public function getActiveEmployees(Request $request, Response $response, $args)
    {
        $rta = Empleado::where('dropDate', null)->get();

        $response->getBody()->write(json_encode($rta));
        return $response;
    }

    public function getOneEmployee(Request $request, Response $response, $args)
    {
        $resp = new stdClass;
        try {
            if (!$this->existsEmployee($args['dni'])) throw new Exception("No existe el Empleado");
            $rta = Empleado::find($args['dni']);;

            $resp->status = 200;
            $resp->data = $rta;
        } catch (\Throwable $th) {
            $resp->message = $th->getMessage();
        }
        $response->getBody()->write(json_encode($resp));
        return $response;
    }

    public function addEmployee(Request $request, Response $response, $args)
    {
        $req = $request->getParsedBody();
        $date =  date('Y-m-d');
        $empleado = new Empleado;
        $rta = new stdClass();
        $rta->date = $date;

        try {
            if (!$this->dniIsValid($req['dni'])) throw new Exception("Dni no cumple con requisitos");
            if ($this->existsEmployee($req['dni'])) throw new Exception("Empleado ya registrado. Verifique los datos");


            $empleado->name = $req['name'];
            $empleado->lastname = $req['lastname'];
            $empleado->dni = $req['dni'];
            $empleado->creationDate = $date;
            $empleado->id_puesto = $req['id_puesto'];
           
            $empleado->save();
            $empl = Empleado::find($req['dni']);
            $rta->data = $empl;
        } catch (\Throwable $th) {
            $rta->status = '500 server Internal Error';
            $rta->message = $th->getMessage();
        }


        $response->getBody()->write(json_encode($rta));
        return $response;
    }

    public function updateEmployee(Request $request, Response $response, $args)
    {
        $req = $request->getParsedBody();
        $resp = new stdClass;

        try {
            if (!$this->existsEmployee($args['dni'])) throw new Exception("No existe el Empleado");

            $empl = Empleado::find($args['dni']);

            $empl->name = $req['name'];
            $empl->lastname = $req['lastname'];
            $empl->id_puesto = $req['id_puesto'];
            $empl->dropDate = null;
            $empl->save();

            
            $resp->status = 200;
            $resp->data = $empl;
        } catch (\Throwable $th) {
            $resp->status = '500 server Internal Error';
            $resp->message = $th->getMessage();
        }
        $response->getBody()->write(json_encode($resp));
        return $response;
    }

    public function dropEmployee(Request $request, Response $response, $args)
    {
        $req = $request->getParsedBody();
        $resp = new stdClass;
        $date = Date('Y-m-d');

        try {
            if (!$this->existsEmployee($args['dni'])) throw new Exception("No existe el Empleado");

            $empl = Empleado::find($args['dni']);

            $empl->dropDate = $date;
            $empl->save();


            $resp->status = 200;
            $resp->data = $empl;
            $resp->message = "Empleado dado de baja";
        } catch (\Throwable $th) {
            $resp->status = '500 server Internal Error';
            $resp->message = $th->getMessage();
        }
        $response->getBody()->write(json_encode($resp));
        return $response;
    }

    public function ingresoSistema(Request $request, Response $response, $args)
    {
        $regis = RegistroEmpleados::get();
        $data = new stdClass;
        $data->message = 'Registro del Ingreso de Empleados al Sistema';
        $data->empleados = $regis;

        $response->getBody()->write(json_encode($data));
        return $response;
    }

    public function ingresoSistemaByDate(Request $request, Response $response, $args)
    {
        $regis = RegistroEmpleados::get();
        $coincidencias = array();

        foreach ($regis as $reg) {

            if ($reg->date == $args['date'])
                array_push($coincidencias, $reg);
        }

        $data = new stdClass;
        $data->message = 'Registro del Ingreso de Empleados al Sistema';
        $data->empleados = $coincidencias;

        $response->getBody()->write(json_encode($data));
        return $response;
    }

    public function ingresoSistemaBetweenDate(Request $request, Response $response, $args)
    {
        $regis = RegistroEmpleados::get();
        $coincidencias = array();

        foreach ($regis as $reg) {

            if ($reg->date >= $args['date1'] && $reg->date <= $args['date2'])
                array_push($coincidencias, $reg);
        }

        $data = new stdClass;
        $data->message = 'Registro del Ingreso de Empleados al Sistema';
        $data->empleados = $coincidencias;

        $response->getBody()->write(json_encode($data));
        return $response;
    }

    public function operacionesBySector(Request $request, Response $response, $args)
    {
        $data = new stdClass;
        $data->date = date('Y-m-d');

        $sect = Sector::where('tipo_sector', $args['idSector'])->get();
        $data->data = $sect;
        $response->getBody()->write(json_encode($data));
        return $response;
    }

    public function operacionesBySectorAndEmployee(Request $request, Response $response, $args)
    {
        $data = new stdClass;
        $data->date = date('Y-m-d');

        $sect = Sector::where('tipo_sector', $args['idSector'])
            ->where('empleado_encargado', $args['idEmpl'])
            ->get();
        $data->data = $sect;
        $response->getBody()->write(json_encode($data));
        return $response;
    }

    public function operacionesByEmployee(Request $request, Response $response, $args)
    {
        $data = new stdClass;
        $data->date = date('Y-m-d');

        $sect = Sector::where('empleado_encargado', $args['idEmpl'])
            ->get();

        $data->data = $sect;
        $response->getBody()->write(json_encode($data));
        return $response;
    }

    private function dniIsValid($dni)
    {
        return strlen((string)$dni) == 8;
    }

    private function existsEmployee($dni)
    {
        $employee = Empleado::find($dni);
        if ($employee) return true;
        else return false;
    }

    public static function getIDEmployeesMozos()
    {
        $empl = Empleado::select('idEmpleado')->where('dropDate', null)->where('id_puesto', 5)->get();
        $empl = json_decode($empl);
        $keyRand = array_rand($empl);
        return $empl[$keyRand];
    }

    public static function getRandomEmployeeBySector($sector)
    {
        $empl =  Empleado::where('dropDate', null)
            ->where('id_puesto', $sector)
            ->get();

        $empl = json_decode($empl);
        $indx = array_rand($empl);

        return $empl[$indx];
    }

    private function filterByDate($list, $date1, $date2)
    {
        $newList = array();

        foreach($list as $item){
           
            if($item->date >= $date1 && $item->date <= $date2)
                array_push($newList, $item);
        }

        return $newList;
    }
}
