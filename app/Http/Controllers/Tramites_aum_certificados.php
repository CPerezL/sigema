<?php

namespace App\Http\Controllers;

use App\Models\admin\Logias_model;
use App\Models\admin\Valles_model;
use App\Models\Config_model;
use App\Models\tramites\Aumento_model;
use App\Traits\DatagridTrait;
use Illuminate\Http\Request;
use Session;

class Tramites_aum_certificados extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '032';
    public $controlador = 'tramites_aum_certificados';
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
        $data['_controller'] = $this->controlador;
        $data['valles'] = Valles_model::getVallesArray($this->oriente, $this->valle);
        /*     varibles de pagina* */
        return view('tramites.aum_certificados', $data);
    }
    public function get_datos(Request $request)
    {
        $page = $request->input('page', 1);
        $rows = $request->input('rows', 20);
        $val = self::validar('idValle', Session::get($this->controlador . '.valle'));
        $log = self::validar('idLogia', Session::get($this->controlador . '.taller'));
        $qry = Aumento_model::getItems($page, $rows, Session::get($this->controlador . '.palabra', ''), $log, $val);
        $total = Aumento_model::getNumItems(Session::get($this->controlador . '.palabra', ''), $log, $val);
        $qry2 = (object) ['total' => $total, 'rows' => $qry];
        return response()->json($qry2);
    }

    public function get_logias(Request $request)
    {
        $or = self::validar('idOriente', Session::get($this->controlador . '.oriente'));
        $val = self::validar('idValle', $request->input('valleid', 0));
        $log = self::validar('idLogia', 0);
        $qry = Logias_model::getLogias($or, $val, $log);
        return $qry;
    }
    public function get_tramites(Request $request)
    {
        $tramites = $request->input('ids');
        if (count($tramites) > 0) {
            $salida = Aumento_model::getTramites($tramites);
            return response()->json($salida);
        } else {
            return response()->json(['success' => 0, 'Msg' => 'Error...']);
        }
    }
    public function update_ceremonia(Request $request)
    {
        $id1 = $request->input('idMiembro1', '0');
        if ($id1 > 0) {
            $fc = $request->input('fechaCeremonia1', '00-00-0000');
            $num = $request->input('numero1', 1);
            $fccc = \DateTime::createFromFormat('d/m/Y', $fc)->format('Y-m-d');
            $codges = Config_model::getValue('codGes');
            $data = array(
                'certificado' => 1,
                'codGes' => $codges,
                'numeroCertificado' => $num,
                'fechaCertificado' => $fccc,
                'fechaModificacion' => date('Y-m-d'),
            );
            $resu = Aumento_model::where("idTramite", $id1)->update($data);
            if ($resu > 0) {
                return response()->json(['success' => 'true', 'Msg' => trans('mess.okinsert')]);
            } else {
                return response()->json(['success' => 0, 'Msg' => trans('mess.errinsert')]);
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errid')]);
        }
    }

}
