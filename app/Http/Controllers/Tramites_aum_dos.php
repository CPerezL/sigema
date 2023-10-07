<?php

namespace App\Http\Controllers;

use App\Models\admin\Logias_model;
use App\Models\admin\Valles_model;
use App\Models\tramites\Aumento_model;
use App\Models\tramites\Ceremonias_model;
use App\Traits\DatagridTrait;
use Illuminate\Http\Request;
use Session;

class Tramites_aum_dos extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '081';
    public $controlador = 'tramites_aum_dos';
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
        $data['_folder'] = url('/') . '/';
        $data['palabra'] = Session::get($this->controlador . '.palabra', '');
        $data['logias'] = Logias_model::getlogiasArray($this->oriente, $this->valle, $this->taller);
        $data['valles'] = Valles_model::getVallesArray($this->oriente, $this->valle);
        /*     varibles de pagina* */
        return view('tramites.aum_dos', $data);
    }
    public function get_datos(Request $request)
    {
        $page = $request->input('page', 1);
        $rows = $request->input('rows', 20);
        $log = Session::get($this->controlador . '.taller');
        $val = Session::get($this->controlador . '.valle');
        $qry = Aumento_model::getTramitesListos($page, $rows, Session::get($this->controlador . '.palabra', ''), $log, $val, 1);
        $total = Aumento_model::getNumTramitesListos(Session::get($this->controlador . '.palabra', ''), $log, $val, 1);
        $qry2 = (object) ['total' => $total, 'rows' => $qry];
        return response()->json($qry2);
    }

    public function update_ceremonia(Request $request)
    {
        $id1 = $request->input('idMiembro1', '0');
        $id2 = $request->input('idMiembro2', '0');
        $id3 = $request->input('idMiembro3', '0');
        $id4 = $request->input('idMiembro4', '0');
        $fc = $request->input('fechaCeremonia', '00-00-0000');
        if ($id1 > 0) {
            $idcer = Ceremonias_model::updateCeremonia(Session::get($this->controlador . '.taller', ''), $fc); //devuleve id de la ceremonia
        } else {
            $idcer = 0;
        }
        // $archivo1 = $this->upload_doc('fileup1');
        // $archivo2 = $this->upload_doc('fileup2');
        $datas = array(
            'idCeremonia' => $idcer,
            'nivelActual' => '1',
            // 'depositoGLB' => $archivo1,
            // 'depositoGDR' => $archivo2,
            'fechaModificacion' => date('Y-m-d'),
        );

        if ($idcer > 0) {
            if ($id1 > 0) {
                Aumento_model::where('idMiembro', $id1)->update($datas);
            }
            if ($id2 > 0) {
                Aumento_model::where('idMiembro', $id2)->update($datas);
            }
            if ($id3 > 0) {
                Aumento_model::where('idMiembro', $id3)->update($datas);
            }
            if ($id4 > 0) {
                Aumento_model::where('idMiembro', $id4)->update($datas);
            }
                $salida = ['success' => 'true', 'Msg' => 'Ceremonia creada  correctamente'];
        } else {
            $salida = ['success' => 0, 'Msg' => 'Ningun tramite en lista'];
        }

        return response()->json($salida);
    }
    public function get_tramites(Request $request)
    {
        $tramites = $request->input('ids');
        if (count($tramites) > 0) {
            $salida = Aumento_model::getListaAumentados($tramites);
        } else {
            $salida = ['success' => 0, 'Msg' => trans('mess.noid')];
        }
        return response()->json($salida);
    }
}
