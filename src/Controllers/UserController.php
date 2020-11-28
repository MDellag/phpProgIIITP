<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Models\User;
use App\Models\RegistroEmpleados;
use Illuminate\Database\Eloquent\JsonEncodingException;
use Exception;
use stdClass;
use \Firebase\JWT\JWT;

define('KEY', "tpProgIII2020");
class UserController
{

    public function getAllPedidos(Request $request, Response $response, $args)
    {
        $pedido = User::get();

        $response->getBody()->write(json_encode($pedido));
        return $response;
    }


    public function register(Request $request, Response $response, $args)
    {
        $req = $request->getParsedBody();
        $user = new User;
        $pass = base64_encode($req['password']);
        $resp = new stdClass;
        $resp->date = date('Y-m-d  H:m:s');
        try {

            if($req['type_user'] != 1 && $req['type_user'] != 2) throw new Exception('Tipo de usuario invalido');

            $user->username = $req['username'];
            $user->password = $pass;
            $user->mail = $req['mail'];
            $user->type_user = $req['type_user'];

            $user->save();

            $resp->data = $user;
            $response->getBody()->write(json_encode($resp));
            return $response->withStatus(200);
        } catch (\Throwable $th) {
            $resp->status = 500;
            $resp->message = $th->getMessage();
            $response->getBody()->write(json_encode($resp));
            return $response->withStatus(500);
        }

    }


    public function login(Request $request, Response $response, $args)
    {
        $req = $request->getParsedBody();
        $user = $req['username'];
        $pass = $req['password'];
        $usr = $this->userIsRegistered($user, $pass);
        $resp = new stdClass;
        $resp->date = date('Y-m-d H:m:s');
        $typeusr = new stdClass;


        try {

            if ($usr) {
                if ($usr->type_user == 1) $typeusr->type = 'admin';
                else if ($usr->type_user == 2) $typeusr->type = 'user';
                else throw new Exception('Tipo de usuario inexistente');

                $payload = array(
                    "iat" => 1356999524,
                    "nbf" => 1357000000,
                    "id" => $usr->id,
                    "username" => $usr->username,
                    "email" => $usr->mail,
                    "type" => $typeusr->type
                );

                $jwt = JWT::encode($payload, KEY);

                if ($typeusr->type == 'user') {
                    $registro = new RegistroEmpleados;
                    $registro->username = $usr->username;
                    $registro->email = $usr->mail;
                    $registro->date = date('Y-m-d');
                    $registro->time = date('H:m:s');
                    $registro->save();
                }

                $resp->access_token = $jwt;
            } else
                throw new Exception("Usuario Inexistente. Por favor revise los datos");
        } catch (\Throwable $th) {
            $resp->message = $th->getMessage();
        }

        $response->getBody()->write(json_encode($resp));
        return $response;
    }


    private function userIsRegistered($username, $password)
    {
        $user = User::where('username', $username)->get();
        $usr = null;
        foreach ($user as $value) {
            $pass = base64_decode($value->password);

            if ($value->username == $username && $password == $pass) {
                $usr = $value;
                break;
            }
        }
        return $usr;
    }

    public function typeUser($id)
    {
        return User::find($id)->type_user;
    }

    public function verifyUser(Request $request, Response $response, $args)
    {
        $asd = $this->typeUser($args['id']);
        $response->getBody()->write(json_encode($asd));
        return $response;
    }
}
