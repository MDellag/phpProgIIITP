<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Models\Pedido;
use App\Models\Menu;

use App\Controllers\MenuController;

use Exception;
use stdClass;

class PedidoController
{

    public function getAllPedidos(Request $request, Response $response, $args)
    {
        $pedido = Pedido::get();

        $response->getBody()->write(json_encode($pedido));
        return $response;
    }

    public function getPedidoByCode(Request $request, Response $response, $args)
    {

        $res = new stdClass;
        $date = date('Y-m-d H:m:s');
        $res->date = $date;
        try {
            if (!$this->codeExists($args['code'])) throw new Exception('No Existe el Pedido');
            $pedido = Pedido::find($args['code']);
            $res->status = 200;
            $res->data = $pedido;
        } catch (\Throwable $th) {
            $res->status = 500;
            $res->message = $th->getMessage();
        }

        $response->getBody()->write(json_encode($res));
        return $response;
    }


    public function productoMasVendido(Request $request, Response $response, $args)
    {
        $resp = new stdClass;
        $pedido = Menu::orderBy('veces_solicitado', 'desc')->first();


        $resp->msg = 'Producto mas Vendido';
        $resp->data = $pedido;


        $response->getBody()->write(json_encode($resp));
        return $response;
    }

    public function productoMenosVendido(Request $request, Response $response, $args)
    {
        $resp = new stdClass;
        $pedido = Menu::orderBy('veces_solicitado', 'asc')->first();


        $resp->msg = 'Producto mas Vendido';
        $resp->data = $pedido;


        $response->getBody()->write(json_encode($resp));
        return $response;
    }

    public function getProductsByStatus(Request $request, Response $response, $args)
    {
        $resp = new stdClass;

        try {
            if($args['status'] != "entregado" && $args['status'] != "tarde" && $args['status'] != "cancelado" )
                throw new Exception('Status no existe');
            $pedido = Pedido::where('status', $args['status'])->get();

            $resp->msg = 'Productos Entregados: ' . $args['status'];
            $resp->data = $pedido;
        } catch (\Throwable $th) {
            $resp->message = $th->getMessage();
        }


        $response->getBody()->write(json_encode($resp));
        return $response;
    }

    public function addPedido(Request $request, Response $response, $args)
    {
        $pedido = new Pedido;
        $req = $request->getParsedBody();
        $resp = new stdClass();
        $date = date('Y-m-d');
        $hour = date('H:m:s');
        $code = $this->generateCode();

        $idEmpleado =  EmpleadoController::getIDEmployeesMozos();
        try {
            if (!$this->verifyItemExistence($req['orden'])) throw new Exception('un item de la orden no existe en el Menu');
            $precioTotal = $this->getTotalPrice($req['orden']);

            $orden = implode(",", $req['orden']);
            $pedido->orden = $orden;
            $pedido->idEmpleado = $idEmpleado->idEmpleado;
            $pedido->idMesa = $req['id_mesa'];
            $pedido->codigo = $code;
            $pedido->hora = $hour;
            $pedido->date = $date;
            $pedido->status = 'solicitado';
            $pedido->precio = $precioTotal;

            $pedido->save();
            MenuController::setProductToSector($req['orden']);
            MenuController::incrementProduct($req['orden']);

            $resp->status = 200;
            $resp->data = $pedido;
        } catch (\Throwable $th) {
            $resp->status = 500;
            $resp->message = $th->getMessage();
        }
        $response->getBody()->write(json_encode($resp));
        return $response;
    }


    public function updatePedido(Request $request, Response $response, $args)
    {
        $resp = new stdClass;
        $req = $request->getParsedBody();

        try {
           
            if($req['status'] != "tarde" && $req['status'] != "entregado") throw new Exception('Status invalido');
            $pedido = Pedido::find($args['code']);

            $pedido->status = $req['status'];
            $pedido->save();

            $resp->msg = 'Pedido Actualizado';
            $resp->data = $pedido;
        } catch (\Throwable $th) {
           $resp->message = $th->getMessage();
        }

        $response->getBody()->write(json_encode($resp));
        return $response;
    }

    public function updateCancelarPedido(Request $request, Response $response, $args)
    {
        $resp = new stdClass;

        try {
            $pedido = Pedido::find($args['code']);

            $pedido->status = 'cancelado';
            $pedido->save();

            $resp->msg = 'Pedido Actualizado';
            $resp->data = $pedido;
        } catch (\Throwable $th) {
           $resp->message = $th->getMessage();
        }

        $response->getBody()->write(json_encode($resp));
        return $response;
    }


    private function codeExists($code)
    {
        $pedido = Pedido::find($code);
        if ($pedido) return true;
        else return false;
    }



    private function generateCode()
    {
        $str = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $a = substr(str_shuffle($str), 0, 5);
        return $a;
    }

    private function getTotalPrice($order)
    {
        $price = 0;
        foreach ($order as $item) {
            $it = Menu::find($item);
            $price += $it->precio;
        }

        return $price;
    }

    private function verifyItemExistence($order)
    {
        $price = true;
        foreach ($order as $item) {
            $it = Menu::find($item);
            if (!$it) $price = false;
        }

        return $price;
    }
}
