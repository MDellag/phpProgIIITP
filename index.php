<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;

require __DIR__ . '/vendor/autoload.php';
header('Content-Type: application/json');

$app = AppFactory::create();
$app->setBasePath('/php/LaComanda');

$app->addErrorMiddleware(true, false, false);

$app->get('/', function (Request $request, Response $response, $args) {

    $json = new stdClass();
    $json->status = 200;
    $json->method = 'GET';
    $json = json_encode($json);
    $response->getBody()->write($json);
    return $response;
});


$app->group('/users', function(RouteCollectorProxy $group){
    $group->get('/{id}[/]', function (Request $req, Response $res, $args) {

        $bod = json_encode($args);
        $res->getBody()->write($bod);
        return $res;
    });
});


$app->run();