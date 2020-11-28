<?php

namespace App\Middlewares;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
use \Firebase\JWT\JWT;
use stdClass;

// use Psr\Http\Message\ResponseInterface as Response;
define('KEY2', "tpProgIII2020");
class AuthMiddleware
{
    /**
     * Example middleware invokable class
     *
     * @param  ServerRequest  $request PSR-7 request
     * @param  RequestHandler $handler PSR-15 request handler
     *
     * @return Response
     */

    private $_typeuser;

    public function __construct($type_user)
    {
        $this->_typeuser = $type_user;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {

        $headers = getallheaders();
       
        $token = $headers['Token'];

        $res = new stdClass;
        $res->date = date('Y-m-d');
        $resp = new Response();

        try {
           
            $jwt = JWT::decode($token, KEY2, array('HS256'));
            
            if ($jwt->type == $this->_typeuser) {
                
                $response = $handler->handle($request);
                $existingContent = (string) $response->getBody();
                
                $resp->getBody()->write($existingContent);
               
                return $resp;
            } else {
                $res->message = "Tipo de Usuario de Usuario Invalido";
                
                $resp->getBody()->write(json_encode($res));
            }
        } catch (\Throwable $th) {
            
            $res->message = $th->getMessage();
            $resp->getBody()->write(json_encode($res));
        } 
        return $resp;
    }
}
