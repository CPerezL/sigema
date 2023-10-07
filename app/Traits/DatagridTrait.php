<?php

namespace App\Traits;

use Auth;
use Illuminate\Http\Request;
use Session;

trait DatagridTrait
{
    /**
     * filtrar
     * funcion que fija filtros para los dtagris usano sesiones para las vistas
     *
     * @param Request $request
     */

    public function permiso($ok)
    {
        $debug = env('APP_DEBUG');
        $time = time() - $ok;//compara hora de envio de pagina 2 horas
        if ((strlen($ok) > 10 && $time < 7200) || $debug) {

        } else {
            return abort(404);
        }
    }
    public function filtrar(Request $request)
    {
        $valor = $request->input('valor', 0);
        $campo = $request->input('filtro', 0);
        Session::put($this->controlador . '.' . $campo, $valor);
        //  return response()->json(['success' => 'true', 'Msg' => $this->controlador . '.' . $campo.'...'.Session::get($this->controlador . '.' . $campo)]);
        return response()->json(['success' => 1, 'Msg' => 'Buscando...']);
    }
    public function validar($tipo, $valor)
    {
        $val = Auth::user()->$tipo;
        if ($val > 0) {
            return $val;
        } elseif ($val == 0) {
            return $valor;
        } else {
            return 1000;
        }
    }
    public function getFecha($valor, $desde = 'd/m/Y', $hasta = 'Y-m-d')
    {
        if (strlen($valor) > 4) {
            $val = \DateTime::createFromFormat($desde, $valor)->format($hasta);
            return $val;
        } else {
            return null;
        }
    }
    public function preparaTexto($texto)
    {
        $comAcentos = array(' ', 'à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ü', 'ú', 'ÿ', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'O', 'Ù', 'Ü', 'Ú');
        $semAcentos = array('', 'a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'y', 'A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U');
        $nova_string = str_replace($comAcentos, $semAcentos, $texto);
        return $nova_string;
    }

    public function iniciarModulo($oriente = 0, $valle = 0, $taller = 0)
    {
        Session::forget($this->controlador);
        $this->oriente = $this->validar('idOriente', $oriente);
        $this->valle = $this->validar('idValle', $valle);
        $this->taller = $this->validar('idLogia', $taller);
        Session::put([$this->controlador . '.oriente' => $this->oriente, $this->controlador . '.valle' => $this->valle, $this->controlador . '.taller' => $this->taller]);
    }
    public function iniciarModuloAll($oriente = 0, $valle = 0, $taller = 0)
    {
        Session::forget($this->controlador);
        $this->oriente =  $oriente;
        $this->valle = $valle;
        $this->taller =  $taller;
        Session::put([$this->controlador . '.oriente' => $this->oriente, $this->controlador . '.valle' => $this->valle, $this->controlador . '.taller' => $this->taller]);
    }
}
