<?php

// use Psr\Http\Message\ResponseInterface as Response;
// use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use App\Middlewares\JsonMiddleware;
use App\Middlewares\AuthMiddleware;
use \App\Controllers\EmpleadoController;
use App\Controllers\MesaController;
use \App\Controllers\PedidoController;
use \App\Controllers\UserController;
use Config\Database;


require __DIR__ . '/vendor/autoload.php';

header('Content-Type: application/json');
date_default_timezone_set("America/Argentina/Buenos_Aires");

$conn = new Database;
$app = AppFactory::create();


$app->addErrorMiddleware(true, false, false);
$app->addBodyParsingMiddleware(); //Este se encarga de parsear los Json del Body Request. Si no, no podriamos enviar Json.



$app->group('/users', function (RouteCollectorProxy $group) {
    $group->post('/register[/]', UserController::class . ":register");
    
    $group->post('/login[/]', UserController::class . ":login");

})->add(new JsonMiddleware);


$app->group('/empleados', function (RouteCollectorProxy $group) {
    $group->get('[/]', EmpleadoController::class . ":getActiveEmployees");
    
    $group->get('/all[/]', EmpleadoController::class . ":getAll");

    $group->get('/ingreso[/]', EmpleadoController::class . ":ingresoSistema");

    $group->get('/ingresoDate[/]', EmpleadoController::class . ":ingresoSistemaByDate");

    $group->get('/ingresoDates[/]', EmpleadoController::class . ":ingresoSistemaBetweenDate");

    $group->get('/{dni}', EmpleadoController::class . ":getOneEmployee");

    $group->get('/operaciones/{idSector}[/]', EmpleadoController::class . ":operacionesBySector"); //modificar para fecha

    $group->get('/operaciones/{idSector}/empleados/{idEmpl}[/]', EmpleadoController::class . ":operacionesBySectorAndEmployee"); //modificar para fecha

    $group->get('/operaciones/empleados/{idEmpl}[/]', EmpleadoController::class . ":operacionesByEmployee"); //modificar para fecha
    
    $group->post('[/]', EmpleadoController::class . ":addEmployee");

    $group->put('/{dni}', EmpleadoController::class . ":updateEmployee");

    $group->delete('/{dni}', EmpleadoController::class . ":dropEmployee");

})->add(new JsonMiddleware);


$app->group('/pedidos', function (RouteCollectorProxy $group) {
    $group->get('/masvendido[/]', PedidoController::class . ":productoMasVendido"); //modificar para fecha

    $group->get('/menosvendido[/]', PedidoController::class . ":productoMenosVendido"); //modificar para fecha
    
    $group->get('/status/{status}[/]', PedidoController::class . ":getProductsByStatus"); //modificar para fecha

    $group->get('/all[/]', PedidoController::class . ":getAllPedidos");

    $group->get('/{code}', PedidoController::class . ":getPedidoByCode");

    $group->post('[/]', PedidoController::class . ":addPedido");

    $group->put('/{code}', PedidoController::class . ":updatePedido");

    $group->delete('/cancelar/{code}', PedidoController::class . ":updateCancelarPedido");
    
})->add(new JsonMiddleware); //add(new AuthMiddleware("admin"))->


$app->group('/mesas', function (RouteCollectorProxy $group) {
    $group->get('/usoMesas[/]', MesaController::class . ":getUsoDeMesas"); 

    $group->get('/facturacion[/]', MesaController::class . ":getFacturacionMesas"); 

    $group->get('/facturacion/total[/]', MesaController::class . ":getFacturacionTotalMesas"); 

    $group->get('/facturacion/total/fechas/{dateStart}/{dateEnd}[/]', MesaController::class . ":getFacturacionMesaByIdAndDate"); 

    
})->add(new JsonMiddleware); //add(new AuthMiddleware("admin"))->

$app->run();
