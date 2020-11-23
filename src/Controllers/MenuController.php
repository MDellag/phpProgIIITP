<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Models\Menu;
use App\Models\Sector;
use App\Controllers\EmpleadoController;


class MenuController
{
    public function getMenu(Request $request, Response $response, $args)
    {
        $menu = Menu::get();

        $response->getBody()->write(json_encode($menu));
        return $response;
    }

    public static function setProductToSector($order)
    {
        foreach ($order as $product) {
            $sec = new Sector;
            $item = Menu::find($product);
            $empl = EmpleadoController::getRandomEmployeeBySector($item->categoria);
            $sec->producto = $item->detalle;
            $sec->tipo_sector = $item->categoria;
            $sec->empleado_encargado = $empl->idEmpleado;

            $sec->save();
        }
    }

    public static function incrementProduct($order)
    {
        foreach($order as $product){

            $menu = Menu::find($product);
            $menu->veces_solicitado += 1;
            $menu->save();
        }
    }

}
