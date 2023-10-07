<?php

namespace App\Http\Controllers;

use App\Models\admin\Logias_model;
use App\Models\asistencias\Asistencias_model;
use App\Traits\DatagridTrait;
// use App\Models\User;
// use App\Models\Valles_model;
use Illuminate\Http\Request;
use Session;

class Asistencias_estado extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '056';
    public $controlador = 'asistencias_estado';
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        self::permiso($request->input('_'));
        self::iniciarModulo();
        $data['_mid'] = $this->idmod; //---
        $data['year'] = date('Y'); //---
        $data['_controller'] = $this->controlador;
        $data['logias'] = Logias_model::getlogiasArray($this->oriente, $this->valle, $this->taller);
        return view('asistencias.estado', $data);
    }
    public function get_datos(Request $request)
    {
        $tal = self::validar('idLogia', Session::get($this->controlador . '.taller'));
        $ges = Session::get($this->controlador . '.gestion', date('Y'));
        $datos = array();
        if ($tal > 0 && $ges > 0) {
            $cc = 0;
            $salida = Asistencias_model::getAsistencia($tal, $ges);
            foreach ($salida as $fec) {
                $datos[$cc]['fechaTenida'] = $fec->fechaTenida;
                $datos[$cc]['idLogia'] = $fec->idLogia;
                $datos[$cc]['numerovenes'] = Asistencias_model::getNumero($tal, $fec->fechaTenida, 4);
                $datos[$cc]['numeromaes'] = Asistencias_model::getNumero($tal, $fec->fechaTenida, 3);
                $datos[$cc]['numerocomp'] = Asistencias_model::getNumero($tal, $fec->fechaTenida, 2);
                $datos[$cc]['numeroapre'] = Asistencias_model::getNumero($tal, $fec->fechaTenida, 1);
                $datos[$cc]['numerototal'] = Asistencias_model::getNumeroTotal($tal, $fec->fechaTenida);
                if ($fec->numeroActa1 > 0) {
                    $datos[$cc]['numeroActa1'] = $fec->numeroActa1;
                } else {
                    $datos[$cc]['numeroActa1'] = '';
                }

                if ($fec->numeroActa2 > 0) {
                    $datos[$cc]['numeroActa2'] = $fec->numeroActa2;
                } else {
                    $datos[$cc]['numeroActa2'] = '';
                }

                if ($fec->numeroActa3 > 0) {
                    $datos[$cc]['numeroActa3'] = $fec->numeroActa3;
                } else {
                    $datos[$cc]['numeroActa3'] = '';
                }
                $cc++;
            }
        }
        $qry2 = (object) ['total' => 0, 'rows' => $datos];
        return response()->json($qry2);
    }
}
