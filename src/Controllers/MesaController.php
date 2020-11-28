<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Models\Pedido;
use App\Models\Sector;
use App\Controllers\EmpleadoController;
use Illuminate\Database\Eloquent\JsonEncodingException;
use Exception;
use Illuminate\Support\Facades\Storage;
use stdClass;

class MesaController
{
    public function getUsoDeMesas(Request $request, Response $response, $args)
    {
        $resp = new stdClass;
        $resp1 = $this->getUsoMesas('desc', $_GET['date']);
        $resp2 = $this->getUsoMesas('asc', $_GET['date']);

        if($resp1) $resp->data1 = $resp1;
        else $resp->data1 = "No se econtraron Coincidencias";
        if($resp2) $resp->data2 = $resp2;
        else $resp->data2 = "No se econtraron Coincidencias";
        
        

        $response->getBody()->write(json_encode($resp));
        return $response;
    }



    public function getFacturacionMesas(Request $request, Response $response, $args)
    {
        $resp = $this->getMesaFacturacion();

        $response->getBody()->write(json_encode($resp));
        return $response;
    }

    public function getFacturacionTotalMesas(Request $request, Response $response, $args)
    {
        $pedido = Pedido::select('idMesa', 'precio')->get();

        $resp = new stdClass;
        $resp->data1 = new stdClass;
        $resp->data2 = new stdClass;
        $resp1 = $this->mesaTotalImportes($pedido, 'desc');
        $resp2 = $this->mesaTotalImportes($pedido);


        $resp->date = date('Y-m-d H:m:s');
        $resp->data1->message = 'Mesa con Mayor Ventas';
        $resp->data1->data = $resp1;
        $resp->data2->message = 'Mesa con menor Ventas';
        $resp->data2->data = $resp2;

        $response->getBody()->write(json_encode($resp));
        return $response;
    }

    public function getFacturacionMesaByIdAndDate(Request $request, Response $response, $args)
    {
        $pedido = Pedido::select('idMesa', 'precio', 'date')->get();
        // $req = $request->getParsedBody();
    
        $mesa = $this->mesaTotalImporteBetweenDate($pedido, $args['dateStart'], $args['dateEnd']);
        $resp = new stdClass;

        $resp->message = 'Mesa con mas Ventas';
        $resp->data = $mesa;

        $response->getBody()->write(json_encode($resp));
        return $response;
    }


    private function getUsoMesas($ascOrDesc, $date)
    {
        $resp = new stdClass;
        $resp->date = date('Y-m-d H:m:s');
        $mesas = Pedido::select('idMesa', 'date')->get();
        $count_values = array();

        foreach ($mesas as $mesa) {
            if ($mesa->date == $date) {
                @$count_values[$mesa->idMesa]++;
            }
        }

        if ($ascOrDesc == 'asc')   asort($count_values);
        else if ($ascOrDesc == 'desc')   arsort($count_values);

        foreach ($count_values as $x => $x_value) {
            $resp->data = "Mesa= " . $x . ", Veces Usada= " . $x_value;
            break;
        }
        return $resp;
    }

    private function getMesaFacturacion()
    {
        $mesas = Pedido::select('idMesa', 'precio', 'date')->get();
        $flag = true;
        $min =  0;
        $max = 0;
        $resp = new stdClass;

        foreach ($mesas as $mesa) {
            if ($mesa->date >= $_GET['dateStart'] && $mesa->date <= $_GET['dateEnd']) {
                if ($flag) {
                    $min = $mesa->precio;
                    $max = $mesa->precio;
                    $flag = false;
                    $resp->recaudacion_Maxima = $mesa;
                    $resp->recaudacion_Minima = $mesa;
                }

                if ($mesa->precio > $max) {
                    $max = $mesa->precio;
                    $resp->recaudacion_Maxima = $mesa;
                } else if ($mesa->precio < $min) {
                    $min = $mesa->precio;
                    $resp->recaudacion_Minima = $mesa;
                }
            }
        }

        return $resp;
    }

    private function mesaTotalImportes($pedido, $order = 'asc')
    {
        $total = array();
        $str = '';
        foreach ($pedido as $value) {
            if (!isset($total[$value->idMesa])) {
                $total[$value->idMesa] = 0;
            }
            $total[$value->idMesa] += $value->precio;
        }

        if ($order == 'asc') asort($total);
        if ($order == 'desc') arsort($total);

        foreach ($total as $key => $value) {
            $str = "Mesa: " . $key . " total: " . $value;
            break;
        }

        return $str;
    }

    private function mesaTotalImporteBetweenDate($pedido, $date1, $date2)
    {
        $total = array();
        $str = '';
        foreach ($pedido as $value) {
            if ($value->date >= $date1 && $value->date <= $date2) {
                if (!isset($total[$value->idMesa])) {
                    $total[$value->idMesa] = 0;
                }
                $total[$value->idMesa] += $value->precio;
            }
        }

        arsort($total);

        foreach ($total as $key => $value) {
            $str = "Mesa: " . $key . " total: " . $value;
            break;
        }

        return $str;
    }
}
