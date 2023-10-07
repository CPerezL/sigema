<?php

namespace App\Http\Controllers;

use App\Models\admin\Control_model;
use App\Models\admin\Logias_model;
use App\Traits\DatagridTrait;
// use App\Models\User;
// use App\Models\Valles_model;
use Auth;
use Illuminate\Http\Request;
use Session;

class Modificacion_registro extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '048';
    public $controlador = 'modificacion_registro';
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
        $data['logias'] = Logias_model::getlogiasArray($this->oriente, $this->valle, $this->taller);
        /*     varibles de pagina* */
        return view('tramites.modificacion_registro', $data);
    }
    public function get_datos(Request $request)
    {
        $page = $request->input('page', 1);
        $rows = $request->input('rows', 15); //, $palabra, $valle = 0, $taller = 0, $grado = 0)
        $val = self::validar('idValle', Session::get($this->controlador . '.valle'));
        $tal = Session::get($this->controlador . '.taller');
        $qry = Control_model::getModifica($page, $rows, Session::get($this->controlador . '.palabra', ''), $val, $tal);
        $total = Control_model::getNumModifica(Session::get($this->controlador . '.palabra', ''), $val, $tal);
        $qry2 = (object) ['total' => $total, 'rows' => $qry];
        return response()->json($qry2);
    }
    public function get_form(Request $request)
    {
        $id = $request->input('id');
        $task = $request->input('task');
        $salida = Control_model::getFormMiembro($id, $task);
        return response()->json($salida);
    }
    public function update_datos(Request $request)
    {
        $id = $request->input('id');
        $tarea = $request->input('task', 0);
        if ($tarea == 1 && $id > 0) //personales
        {
            $ncom = $request->input('Paterno') . ' ' . $request->input('Materno') . ' ' . $request->input('Nombres');
            $ant = Control_model::getValores($id, 'A.NombreCompleto,A.Paterno,A.Materno,A.Nombres');
            //---
            $data['idMiembro'] = $id;
            $data['tabla'] = 'sgm_miembros';
            $data['accion'] = 'Modificacion  de Nombre';
            $data['tipo'] = 1;
            $data['modificacion'] = $ant->NombreCompleto . ' a ' . $ncom;
            $data['campos'] = 'NombreCompleto|Paterno|Materno|Nombres';
            $data['valorAntes'] = "$ant->NombreCompleto|$ant->Paterno|$ant->Materno|$ant->Nombres";
            $data['valorCambio'] = $ncom . '|' . $request->input('Paterno', '') . '|' . $request->input('Materno', '') . '|' . $request->input('Nombres', '');
            $data['estado'] = 1;
            $data['usuarioCambia'] = Auth::user()->id;
            $data['usuarioAprueba'] = 0;
            $data['taller'] = $ant->taller;
            $data['valle'] = $ant->valle;
        } elseif ($tarea == 2 && $id > 0) //grado
        {
            $gg[0] = 'Vacio';
            $gg[1] = 'Aprendiz';
            $gg[2] = 'Compañero';
            $gg[3] = 'Maestro';
            $gg[4] = 'V.M./Ex V.M.';
            $ant = Control_model::getValores($id, 'A.Grado');
            $data['idMiembro'] = $id;
            $data['tabla'] = 'sgm_miembros';
            $data['accion'] = 'Modificacion de Grado';
            $data['tipo'] = 2;
            $data['campos'] = 'Grado';
            $data['modificacion'] = $gg[$ant->Grado] . ' a ' . $gg[$request->input('Grado', '0')];
            $data['valorAntes'] = $ant->Grado;
            $data['valorCambio'] = $request->input('Grado', '0');
            $data['estado'] = 1;
            $data['usuarioCambia'] = Auth::user()->id;
            $data['usuarioAprueba'] = 0;
            $data['taller'] = $ant->taller;
            $data['valle'] = $ant->valle;
        } elseif ($tarea == 3 && $id > 0) //fini
        {
            $ant = Control_model::getValores($id, 'A.FechaIniciacion');
            $data['idMiembro'] = $id;
            $data['tabla'] = 'sgm_miembros';
            $data['accion'] = 'Mod F. de iniciacion';
            $data['tipo'] = 3;
            $data['campos'] = 'FechaIniciacion';
            $data['modificacion'] = $ant->FechaIniciacion . ' a ' . self::getFecha($request->input('Fecha'));
            $data['valorAntes'] = $ant->FechaIniciacion;
            $data['valorCambio'] = self::getFecha($request->input('Fecha'));
            $data['estado'] = 1;
            $data['usuarioCambia'] = Auth::user()->id;
            $data['usuarioAprueba'] = 0;
            $data['taller'] = $ant->taller;
            $data['valle'] = $ant->valle;
        } elseif ($tarea == 4 && $id > 0) //faume
        {
            $ant = Control_model::getValores($id, 'A.FechaAumentoSalario');
            $data['idMiembro'] = $id;
            $data['tabla'] = 'sgm_miembros';
            $data['accion'] = 'Mod F. de Aumento';
            $data['tipo'] = 4;
            $data['campos'] = 'FechaAumentoSalario';
            $data['modificacion'] = $ant->FechaAumentoSalario . ' a ' . self::getFecha($request->input('Fecha'));
            $data['valorAntes'] = $ant->FechaAumentoSalario;
            $data['valorCambio'] = self::getFecha($request->input('Fecha'));
            $data['estado'] = 1;
            $data['usuarioCambia'] = Auth::user()->id;
            $data['usuarioAprueba'] = 0;
            $data['taller'] = $ant->taller;
            $data['valle'] = $ant->valle;
        } elseif ($tarea == 5 && $id > 0) //fexa
        {
            $ant = Control_model::getValores($id, 'A.FechaExaltacion');
            $data['idMiembro'] = $id;
            $data['tabla'] = 'sgm_miembros';
            $data['accion'] = 'Mod F. de Exaltacion';
            $data['tipo'] = 5;
            $data['campos'] = 'FechaExaltacion';
            $data['modificacion'] = $ant->FechaExaltacion . ' a ' . self::getFecha($request->input('Fecha'));
            $data['valorAntes'] = $ant->FechaExaltacion;
            $data['valorCambio'] = self::getFecha($request->input('Fecha'));
            $data['estado'] = 1;
            $data['usuarioCambia'] = Auth::user()->id;
            $data['usuarioAprueba'] = 0;
            $data['taller'] = $ant->taller;
            $data['valle'] = $ant->valle;
        } elseif ($tarea == 6 && $id > 0) //tipo miembro
        {
            $mm = $request->input('Miembro');
            if ($mm == 'Honorario') {
                $ant = Control_model::getValores($id, 'A.Miembro,A.DecretoHonorario,A.FechaHonorario');
                $data['campos'] = 'Miembro|DecretoHonorario|FechaHonorario';
                $data['valorAntes'] = $ant->Miembro . '|' . $ant->DecretoHonorario . '|' . $ant->FechaHonorario;
                $data['valorCambio'] = $request->input('Miembro') . '|' . $request->input('Decreto') . '|' . self::getFecha($request->input('Fecha'));
            } elseif ($mm == 'Ad-Vitam') {
                $ant = Control_model::getValores($id, 'A.Miembro,A.DecretoAdVitam,A.FechaAdVitam');
                $data['campos'] = 'Miembro|DecretoAdVitam|FechaAdVitam';
                $data['valorAntes'] = $ant->Miembro . '|' . $ant->DecretoAdVitam . '|' . $ant->FechaAdVitam;
                $data['valorCambio'] = $request->input('Miembro') . '|' . $request->input('Decreto') . '|' . self::getFecha($request->input('Fecha'));
            } else {
                $ant = Control_model::getValores($id, 'A.Miembro');
                $data['campos'] = 'Miembro';
                $data['valorAntes'] = $ant->Miembro;
                $data['valorCambio'] = $request->input('Miembro');
            }
            $data['idMiembro'] = $id;
            $data['tabla'] = 'sgm_miembros';
            $data['accion'] = 'Mod. Tipo de Miembro';
            $data['tipo'] = 6;
            $data['modificacion'] = $ant->Miembro . ' a ' . $request->input('Miembro');
            $data['estado'] = 1;
            $data['usuarioCambia'] = Auth::user()->id;
            $data['usuarioAprueba'] = 0;
            $data['taller'] = $ant->taller;
            $data['valle'] = $ant->valle;
        }

        if ($id > 0) {
            $resu = Control_model::insert($data);
        } else {
            $resu = 0;
        }
        if ($resu > 0) {
            return response()->json(['success' => 'true', 'Msg' =>trans('mess.okinsert')]);
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errinsert')]);
        }
    }
    public function estado(Request $request)
    {
        $id = $request->input('id', 0);
        if ($id > 0) {
            $ndata['estado'] = 2;
            $ndata['usuarioAprueba'] = Auth::user()->id;
            $ndata['fechaModificacion'] = date('Y-m-d h:m:s');
            $res = Control_model::where('idControl', $id)->update($ndata);
            if ($res > 0) {
                return response()->json(['success' => 'true', 'Msg' => trans('mess.okdel')]);
            } else {
                return response()->json(['success' => 0, 'Msg' => trans('mess.errdel')]);
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errid')]);
        }
    }
}
