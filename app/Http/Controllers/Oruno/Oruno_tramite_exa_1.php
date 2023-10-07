<?php
namespace App\Http\Controllers\Oruno;
use App\Http\Controllers\Controller;

use App\Models\admin\Logias_model;
use App\Models\oruno\Exaltacion_model;
use App\Traits\DatagridTrait;
use Illuminate\Http\Request;
use App\Models\Config_model;
use Session;

class Oruno_tramite_exa_1 extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '085';
    public $controlador = 'oruno_tramite_exa_1';
    public $numExtraT = 6;
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
        $data['logias'] = Logias_model::getlogiasArray($this->oriente, $this->valle, $this->taller);
        /*     variables de pagina* */
        return view('oruno.exa_1', $data);
    }
    public function get_datos(Request $request)
    {
        $compames = Config_model::getValue('mesesExaltacion');
        $apreasis = Config_model::getValue('asisExaltacion');
        $regu = Config_model::getValue('mesesDeuda');
        $page = $request->input('page', 1);
        $rows = $request->input('rows', 20);
        $log = Session::get($this->controlador . '.taller', 0);
        $salida = Exaltacion_model::getCompaneros($page, $rows, $log, $regu, $compames);
        $total = Exaltacion_model::getNumCompaneros($log, $regu);
        $ntenidas = Exaltacion_model::getTenidasMeses($log, $compames); //24
        foreach ($salida as $key => $ver) { //lista de aprendices de la logia
            $condasis = $apreasis;
            $ord = Exaltacion_model::getAsistenciaMeses($log, $ver->id, $compames);
            $ase = Exaltacion_model::getAsisExtraMeses($ver->id, $compames);
            $salida[$key]->antiguedad = $ver->antiguedad;
            $salida[$key]->ntenidas = $ntenidas;
            $salida[$key]->asiste = $ord;
            $asis = $ord + $ase;
            //tenidas
            if ($ntenidas > 0) {
                $porcen = (($asis / $ntenidas) * 100);
                if ($porcen > 100) {
                    $salida[$key]->ordinaria = '100';
                } else {
                    $salida[$key]->ordinaria = round($porcen);
                }
            } else {
                $salida[$key]->ordinaria = 0;
            }
            //--------------------------------------------------
            if ($salida[$key]->ordinaria >= $condasis) {
                if ($ver->antOk > 0) {
                    $salida[$key]->antOk = 1;
                } else {
                    $salida[$key]->antOk = 0;
                }
            } else {
                if ($salida[$key]->antOk > 0) {
                }
                $salida[$key]->antOk = 0;
            }
            $salida[$key]->ordinaria = $salida[$key]->ordinaria . '%';
        }
        $qry2 = (object) ['total' => $total, 'rows' => $salida];
        return response()->json($qry2);
    }
    public function get_datos_old(Request $request)
    {
        $ges = date("Y");
        $hoy = date("Y-m-d");
        $fecha = date("Y-m", strtotime("$hoy -24 months")) . '-01';
        $page = $request->input('page', 1);
        $rows = $request->input('rows', 20);
        $log = Session::get($this->controlador . '.taller');
        $salida = Exaltacion_model::getCompaneros($page, $rows, Session::get($this->controlador . '.palabra', ''), $log, 0, 1);
        $total = Exaltacion_model::getNumCompaneros(Session::get($this->controlador . '.palabra', ''), $log, 0, 1);
        ///*****************************************************************************************************************************
        $ntenidas = Exaltacion_model::getCantidadTenidas($ges, Session::get($this->controlador . '.taller', ''));
        foreach ($salida as $key => $ver) {
            $min = Exaltacion_model::getMinimo($ver->id, $fecha); //
            $salida[$key]->minimo = $min;
            //*************************************************************************************************************************************************************
            $ord = Exaltacion_model::getAsistencia($ges, Session::get($this->controlador . '.taller', ''), $ver->id);
            $ext = Exaltacion_model::getExtraTemplos($ges, Session::get($this->controlador . '.taller', ''), $ver->id);
            $ase = Exaltacion_model::getAsisExtra($ges, $ver->id);
            $extratgdr = Exaltacion_model::getAsisExtra($ges, $ver->id, 1);
            $asis = $ord + $ase;
            //tenidas
            if ($ntenidas > 0) {
                $porcen = (($asis / $ntenidas) * 100);
                if ($porcen > 100) {
                    $salida[$key]->ordinaria = '100';
                } else {
                    $salida[$key]->ordinaria = round($porcen);
                }

            } else {
                $salida[$key]->ordinaria = 0;
            }
            //extratem
            if ($ext > 0) {
                $porcent = round(100 * (($ext + $extratgdr) / $this->numExtraT), 0);
                if ($porcent > 100) {
                    $salida[$key]->extratemplo = '100';
                } else {
                    $salida[$key]->extratemplo = $porcent;
                }

            } else {
                $salida[$key]->extratemplo = 0;
            }
            //---------------------------------------------------
            $ges2 = $ges - 1;
            $ext2 = Exaltacion_model::getExtraTemplos($ges2, Session::get($this->controlador . '.taller', ''), $ver->id);
            $extratgdr2 = Exaltacion_model::getAsisExtra($ges2, $ver->id, 1);
            $porcent2 = round(100 * (($ext2 + $extratgdr2) / $this->numExtraT), 0);
            if ($porcent2 > 100) {
                $salida[$key]->extratemplo2 = '100';
            } else {
                $salida[$key]->extratemplo2 = $porcent2;
            }
            //--------------------------------------------------
            if ((($salida[$key]->extratemplo >= 60 || $salida[$key]->extratemplo2 >= 60) && ($salida[$key]->ordinaria >= 70 || $salida[$key]->minimo >= 30))) {
                if ((int) $salida[$key]->antOk > 0) {
                    $salida[$key]->antOk = '1';
                }
            } else {
                if ($salida[$key]->antOk > 0) {
                    $salida[$key]->antiguedad = 'Incompleta';
                }
                $salida[$key]->antOk = '0';
            }
            $salida[$key]->ordinaria = $salida[$key]->ordinaria.'%';
        }
        $qry2 = (object) ['total' => $total, 'rows' => $salida];
        return response()->json($qry2);
    }
    public function get_datosrev(Request $request)
    {
        $page = $request->input('page', 1);
        $rows = $request->input('rows', 20);
        $log = Session::get($this->controlador . '.taller');
        $qry = Exaltacion_model::getCompaneros($page, $rows, Session::get($this->controlador . '.palabra', ''), $log, 0, 1);
        $total = Exaltacion_model::getNumCompaneros(Session::get($this->controlador . '.palabra', ''), $log, 0, 1);
        $qry2 = (object) ['total' => $total, 'rows' => $qry];
        return response()->json($qry2);
    }

    public function get_tramite(Request $request)
    {
        $tramite = $request->input('id', 0);
        if ($tramite > 0) {
            $salida = Exaltacion_model::getTramite($tramite);
            if (is_null($salida)) {
                $salida = Exaltacion_model::getExaTramite($tramite);
            }
            return response()->json($salida);
        } else {
            return response()->json(['success' => 0, 'Msg' => 'No encontrado"']);
        }
    }

    public function save_tramite(Request $request)
    {
        $tramite = $request->input('idTramite', 0);
        if ($tramite > 0) {
            $data = array(
                'logia' => Session::get($this->controlador . '.taller'),
                'fechaIniciacion' => \DateTime::createFromFormat('d/m/Y', $request->input('fechaIniciacion', '00-00-0000'))->format('Y-m-d'),
                'actaIniciacion' => $request->input('actaIniciacion'),
                'fechaAumento' => \DateTime::createFromFormat('d/m/Y', $request->input('fechaAumento', '00-00-0000'))->format('Y-m-d'),
                'actaAumento' => $request->input('actaAumento'),
                'fechaPase' => \DateTime::createFromFormat('d/m/Y', $request->input('fechaPase', '00-00-0000'))->format('Y-m-d'),
                'actaPase' => $request->input('actaPase'),
                'fechaExamen' => \DateTime::createFromFormat('d/m/Y', $request->input('fechaExamen', '00-00-0000'))->format('Y-m-d'),
                'fechaExaltacion' => \DateTime::createFromFormat('d/m/Y', $request->input('fechaExaltacion', '00-00-0000'))->format('Y-m-d'),
                'actaExamen' => $request->input('actaExamen'),
                'fechaModificacion' => date('Y-m-d'),
            );
            $resu = Exaltacion_model::where('idTramite', $tramite)->update($data);
            if ($resu > 0) {
                $salida = ['success' => 'true', 'Msg' => 'Datos actualizados correctamente'];
            } else {
                $salida = ['success' => 0, 'Msg' => 'Ocurrio un error al salvar los datos'];
            }
        } elseif ($request->input('idMiembro', 0) > 0 && Session::get($this->controlador . '.taller') > 0) {
            $data = array(
                'nivelActual' => '0',
                'idMiembro' => $request->input('idMiembro'),
                'logia' => Session::get($this->controlador . '.taller'),
                'fechaIniciacion' => \DateTime::createFromFormat('d/m/Y', $request->input('fechaIniciacion', '00-00-0000'))->format('Y-m-d'),
                'actaIniciacion' => $request->input('actaIniciacion'),
                'fechaAumento' => \DateTime::createFromFormat('d/m/Y', $request->input('fechaAumento', '00-00-0000'))->format('Y-m-d'),
                'actaAumento' => $request->input('actaAumento'),
                'fechaPase' => \DateTime::createFromFormat('d/m/Y', $request->input('fechaPase', '00-00-0000'))->format('Y-m-d'),
                'actaPase' => $request->input('actaPase'),
                'fechaExamen' => \DateTime::createFromFormat('d/m/Y', $request->input('fechaExamen', '00-00-0000'))->format('Y-m-d'),
                'fechaExaltacion' => \DateTime::createFromFormat('d/m/Y', $request->input('fechaExaltacion', '00-00-0000'))->format('Y-m-d'),
                'actaExamen' => $request->input('actaExamen'),
                'fechaCreacion' => date('Y-m-d'),
                'fechaModificacion' => date('Y-m-d'),
            );
            $resu = Exaltacion_model::insert($data);
            if ($resu > 0) {
                $salida = ['success' => 'true', 'Msg' => 'Datos insertados correctamente'];
            } else {
                $salida = ['success' => 0, 'Msg' => 'Ocurrio un error al salvar los datos'];
            }

        } else {
            $salida = ['success' => 0, 'Msg' => 'Datos incompletos'];
        }
        return response()->json($salida);
    }
}
