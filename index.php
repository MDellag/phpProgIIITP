<?php

include './api/Entities/Empleados.php';
include './api/Entities/Pedidos.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use \Entities\Empleado;
use \Entities\Pedido;



require __DIR__ . '/vendor/autoload.php';

header('Content-Type: application/json');
date_default_timezone_set("America/Argentina/Buenos_Aires");


$app = AppFactory::create();
// $app->setBasePath('/php/LaComanda');

$app->addErrorMiddleware(true, false, false);
$app->addBodyParsingMiddleware(); //Este se encarga de parsear los Json del Body Request. Si no, no podriamos enviar Json.


$app->get('/', function (Request $request, Response $response, $args) {

    $json = new stdClass();
    $json->status = 200;
    $json->method = 'GET';
    $json = json_encode($json);
    $response->getBody()->write($json);
    return $response;
});


$app->group('/users', function (RouteCollectorProxy $group) {
    $group->get('/{id}[/]', function (Request $req, Response $res, $args) {

        $bod = json_encode($args);
        $res->getBody()->write($bod);
        return $res;
    });

    $group->get('/', function (Request $req, Response $res, $args) {
    });
});


$app->group('/empleados', function (RouteCollectorProxy $group) {
    $group->get('/', function (Request $req, Response $res, $args) {

        $bod = Empleado::getEmployees();
        $res->getBody()->write(json_encode($bod));
        return $res;
    });

    $group->get('/{dni}[/]', function (Request $req, Response $res, $args) {

        try {
            $bod = Empleado::getEmployeeByDni($args['dni']);
            $res->getBody()->write(json_encode($bod));
        } catch (\Exception $th) {
            $msj = new stdClass();
            $msj->date = date('Y-m-d  H:m');
            $msj->message = $th->getMessage();
            $res->getBody()->write(json_encode($msj));
        }
        return $res;
    });

    $group->post('/', function (Request $req, Response $res, $args) {

        $body = $req->getParsedBody();

        $response = new stdClass();
        $response->date = date("Y/m/d  H:m");

        try {
            if (strlen((string)$body['dni']) != 8) throw new Exception('El Dni es mayor o menor a 8 digitos');
            $employee = new Empleado($body['name'], $body['lastname'], $body['dni']);
            Empleado::AltaEmpleadoDB($employee->_personalInfo);

            $response->employee = $employee->_personalInfo;
            $res->getBody()->write(json_encode($response));
        } catch (\exception  $th) {
            $response->status = 'server internal Error';
            $response->message = $th->getMessage();
            $res->getBody()->write(json_encode($response));
        }
        return $res;
    });

    $group->put('/{dni}[/]', function (Request $req, Response $res, $args) {

        $body = $req->getParsedBody();

        $response = new stdClass();
        $response->date = date("Y/m/d  H:m");

        try {
            if (strlen((string)$args['dni']) != 8) throw new Exception('El Dni es mayor o menor a 8 digitos');

            $employee = new stdClass();
            $employee->name = $body['name'];
            $employee->lastname = $body['lastname'];
            $employee->dni = $args['dni'];

            Empleado::updateEmployee($employee);

            $response->employee = $employee;
            $res->getBody()->write(json_encode($response));
        } catch (\exception  $th) {
            $response->status = 'server internal Error';
            $response->message = $th->getMessage();
            $res->getBody()->write(json_encode($response));
        }
        return $res;
    });

    $group->delete('/{dni}[/]', function (Request $req, Response $res, $args) {

        $response = new stdClass();
        $response->date = date("Y/m/d  H:m");

        try {
            Empleado::deleteEmployeeByDni($args['dni']);

            $response->message = "Empleado Dado de baja con dni: " . $args['dni'];
            $res->getBody()->write(json_encode($response));
        } catch (\exception  $th) {
            $response->status = 'server internal Error';
            $response->message = $th->getMessage();
            $res->getBody()->write(json_encode($response));
        }
        return $res;
    });
});

$app->group('/pedidos', function (RouteCollectorProxy $group) {

    $group->get('/', function (Request $req, Response $res, $args) {

        $response = Pedido::obtenerPedidos();
        $res->getBody()->write(json_encode($response));
        return $res;
    });

    $group->post('/', function (Request $req, Response $res, $args) {

        $bod = $req->getParsedBody();
        $randID = Empleado::GetIdEmpleadoRandom();
        $pedido = new Pedido($bod['orden'], $randID ,$bod['id_mesa'], Pedido::GenerateCode(), );
        // $pedido->ordenarPedido();
        $res->getBody()->write(json_encode($pedido->_pedidoInfo));
        return $res;
    });
});


$app->run();
