<?php

namespace App\Http\Controllers;

use App\Models\admin\Logias_model;
use App\Models\admin\Membrecia_model;
use App\Models\admin\Miembrosdata_model;
use App\Models\admin\Miembrosestado_model;
use App\Models\admin\Miembrostipo_model;
use App\Models\admin\Orientes_model;
use App\Models\admin\Valles_model;
use App\Models\admin\Profesiones_model;
use App\Models\Paises_model;
use App\Models\Config_model;
use App\Models\Logs_model;
use App\Traits\DatagridTrait;
use Auth;
use Illuminate\Http\Request;
use Session;

class Membrecia extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '010';
    public $controlador = 'membrecia';
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
        if (Auth::user()->nivel > 3) {
            $data['editable'] = '';
        } else {
            $data['editable'] = ' disabled="true"';
        }
        $data['_mid'] = $this->idmod; //---
        $data['_controller'] = $this->controlador;
        $data['oris'] = Orientes_model::getOrientesArray($this->oriente);
        $data['valles'] = Valles_model::getVallesArray($this->oriente, $this->valle);
        $data['logias'] = Logias_model::getlogiasArray($this->oriente, $this->valle, $this->taller);
        $data['miembros'] = Miembrostipo_model::getMiembrosArray();
        $data['profes'] = Profesiones_model::getProfesionesArray();
        $data['paises'] = Paises_model::getPaisesArray();
        $data['estados'] = Miembrosestado_model::getEstadosArray();
        /*     varibles de pagina* */
        return view('admin.membrecia', $data);
    }
    public function get_datos(Request $request)
    {
        $page = $request->input('page', 1);
        $rows = $request->input('rows', 20);
        $or = self::validar('idOriente', Session::get($this->controlador . '.oriente'));
        $val = self::validar('idValle', Session::get($this->controlador . '.valle'));
        $log = self::validar('idLogia', Session::get($this->controlador . '.taller'));
        Membrecia_model::$regular = Config_model::regular();
        $qry = Membrecia_model::getItems($page, $rows, Session::get($this->controlador . '.palabra', ''), $or, $val, $log, Session::get($this->controlador . '.grado', 0), Session::get($this->controlador . '.estado', 4));
        $total = Membrecia_model::getNumItems(Session::get($this->controlador . '.palabra', ''), $or, $val, $log, Session::get($this->controlador . '.grado', 0), Session::get($this->controlador . '.estado', 4));
        $qry2 = (object) ['total' => $total, 'rows' => $qry];
        return response()->json($qry2);
    }
    public function get_form(Request $request)
    {
        $id = $request->input('id');
        $task = $request->input('task');
        $salida = Membrecia_model::getForm($id, $task);
        return response()->json($salida);
    }
    public function save_datos(Request $request)
    {
        $ncom = ($request->input('Paterno') . ' ' . $request->input('Materno') . ' ' . $request->input('Nombres'));
        $ncomid = str_replace(' ', '', $ncom);
        $existe = Membrecia_model::where("username", $request->input('username'))->exists();
        if ($existe == 1) {
            return response()->json(['success' => 0, 'Msg' => 'El Documento de Identidad ya existe']);
        } else {
            $data = array(
                'idOriente' => $request->input('oriente', 1),
                'Materno' => $request->input('Materno', ''),
                'Paterno' => $request->input('Paterno', ''),
                'Nombres' => $request->input('Nombres', 'error'),
                'Miembro' => $request->input('Miembro', 'Regular'),
                'Grado' => $request->input('Grado'),
                'LogiaActual' => $request->input('LogiaActual'),
                'jurisdiccion' => $request->input('jurisdiccion', ''),
                'NombreCompleto' => ($ncom),
                'NombreCompletoID' => strtoupper($ncomid),
                'username' => $request->input('username', 'error'),
                'fechaModificacion' => date('Y-m-d'),
                'clave' => sha1('amistad'),
            );
            $datadata = array(
                'CI' => $request->input('username', 'error'),
            );
            $resu = Membrecia_model::insertGetId($data);
            if ($resu > 0) {
                Miembrosdata_model::updateMiembroData($resu, $datadata);
                return response()->json(['success' => 'true', 'Msg' => trans('mess.okinsert')]);
            } else {
                return response()->json(['success' => 0, 'Msg' => trans('mess.errinsert')]);
            }
        }
    }
    public function update_datos(Request $request)
    {
        $id = $request->input('id', 0);
        $tarea = $request->input('task', 0);
        if ($tarea == 1 && $id > 0 && strlen($request->input('Nombres')) > 2) //personales
        {
            $data = array(
                'Estado' => $request->input('Estado', 0),
                'fechaModificacion' => date('Y-m-d'),
            );
            if (Auth::user()->nivel > 3) //si se puede grabar
            {
                $ncom = $request->input('Paterno') . ' ' . $request->input('Materno') . ' ' . $request->input('Nombres');
                $data['NombreCompleto'] = $ncom;
                $data['Materno'] = $request->input('Materno', '');
                $data['Paterno'] = $request->input('Paterno', '');
                $data['Nombres'] = $request->input('Nombres', '');
                $data['username'] = $request->input('CI', '');
            }
            if ($request->hasFile('foto')) {
                $foto = self::subir_imagen($request, 'foto');
                if (strlen($foto) > 5) {
                    $data['foto'] = $foto;
                }
            }
            $datadata = array(
                'FechaNacimiento' => self::getFecha($request->input('FechaNacimiento')),
                'ProfesionOficio' => $request->input('ProfesionOficio', ''),
                'CI' => $request->input('CI', ''),
                'email' => $request->input('email', ''),
                'TelefonoOficina' => $request->input('TelefonoOficina', ''),
                'Cargo' => $request->input('Cargo', ''),
                'Trabajo' => $request->input('Trabajo', ''),
                'Pais' => $request->input('Pais', ''),
                'LugarNacimiento' => $request->input('LugarNacimiento', ''),
                'Domicilio' => $request->input('Domicilio', ''),
                'TelefonoDomicilio' => $request->input('TelefonoDomicilio', ''),
                'Celular' => $request->input('Celular', ''),
                'EstadoCivil' => $request->input('EstadoCivil', 'Soltero'),
                'NombreEsposa' => $request->input('NombreEsposa', ''),
                'NombrePadre' => $request->input('NombrePadre', ''),
                'NombreMadre' => $request->input('NombreMadre', ''),
            );
            $resu1 = Miembrosdata_model::updateMiembroData($id, $datadata);
            $resu2 = Membrecia_model::where("id", $id)->update($data);
            $resu = $resu1 + $resu2;
        } elseif ($tarea == 2 && $id > 0 && $request->input('LogiaActual', 0) > 0) //masonicos
        {
            $ncom = '';
            $control = explode('_', $request->input('control', ''));
            $data = array(
                'jurisdiccion' => $request->input('jurisdiccion', 0),
                'LogiaIniciacion' => $request->input('LogiaIniciacion'),
                'LogiaAumento' => $request->input('LogiaAumento'),
                'LogiaExaltacion' => $request->input('LogiaExaltacion'),
                'LogiaAfiliada' => $request->input('LogiaAfiliada', 0),
                'DecretoAfiliacion' => $request->input('DecretoAfiliacion', ''),
                'LogiaInspector' => $request->input('LogiaInspector', 0),
                'FechaHonorario' => self::getFecha($request->input('FechaHonorario')),
                'DecretoHonorario' => $request->input('DecretoHonorario'),
                'LogiaHonorario' => $request->input('LogiaHonorario'),
                'FechaAdMeritum' => self::getFecha($request->input('FechaAdMeritum')),
                'DecretoAdMeritum' => $request->input('DecretoAdMeritum'),
                'LogiaAdMeritum' => $request->input('LogiaAdMeritum'),
                'FechaAdVitam' => self::getFecha($request->input('FechaAdVitam')),
                'DecretoAdVitam' => $request->input('DecretoAdVitam'),
                'LogiaAdVitam' => $request->input('LogiaAdVitam'),
                'observaciones' => $request->input('observaciones', ''),
                'Estado' => $request->input('Estado', 1),
                'socio' => $request->input('socio'),
                'mesesprofano' => $request->input('mesesprofano'),
                'fechaModificacion' => date('Y-m-d'),
            );
            if (Auth::user()->nivel > 3) //si se puede grabar
            {
                $data['FechaIniciacion'] = self::getFecha($request->input('FechaIniciacion'));
                $data['FechaAumentoSalario'] = self::getFecha($request->input('FechaAumentoSalario'));
                $data['FechaExaltacion'] = self::getFecha($request->input('FechaExaltacion'));
                $data['Miembro'] = $request->input('Miembro', 'Regular');
                if ($control[1] != $request->input('Grado')) {
                    Logs_model::autoLog('2', Auth::user()->id, '-', $id, 0, 0, 'Grado_' . $id . '_' . $control[1]); //----
                    $data['Grado'] = $request->input('Grado');
                }
            }
            if (count($control) == 4) //datos importantres
            {
                //autoLog($tipo, $usuario, $accion, $miembro = 0, $logia = 0, $valle = 0, $params = '')//2021
                if ($control[0] != $request->input('LogiaActual')) {
                    $data['LogiaActual'] = $request->input('LogiaActual');
                    Logs_model::autoLog('7', Auth::user()->id, '-', $id, 0, 0, 'LogiaActual_' . $id . '_' . $control[0]); //----
                }

                if ($control[3] != $request->input('mesesprofano')) {
                    $data['mesesprofano'] = $request->input('mesesprofano');
                }
            }
            $resu = Membrecia_model::where("id", $id)->update($data);
        } elseif ($tarea == 3 && $id > 0 && (strlen($request->input('Nombres')) > 2 || $request->input('claven', '') > 2)) //usuario
        {
            $ncom = $request->input('Paterno') . ' ' . $request->input('Materno') . ' ' . $request->input('Nombres');

            $data = array(
                'NombreCompleto' => ($ncom),
                'fechaModificacion' => date('Y-m-d'),
            );
            $clave = $request->input('claven', '');
            if (strlen($clave) > 3) {
                $data['clave'] = sha1($request->input('claven', 'amistad'));
            }
            if (Auth::user()->nivel > 3) //si se puede grabar
            {
                $data['Kardex'] = $request->input('Kardex', 0);
                $data['Materno'] = $request->input('Materno', '');
                $data['Paterno'] = $request->input('Paterno', '');
                $data['Nombres'] = $request->input('Nombres');
                $data['username'] = $request->input('username', 'error');
            }
            $datam = array(
                'email' => $request->input('email'),
                'CI' => $request->input('username', 'error')
            );
            $resu1 = Miembrosdata_model::updateMiembroData($id, $datam);
            $resu = Membrecia_model::where("id", $id)->update($data);
        } else {
            $resu = 0;
        }

        if ($resu > 0) {
            return response()->json(['success' => 'true', 'Msg' => trans('mess.okchange')]);
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.nochange')]);
        }
    }

    public function destroy_datos(Request $request)
    {
        $id = $request->input('id', 0);
        if ($id > 0) {
            $resu = Membrecia_model::where("idOriente", $id)->delete();
            if ($resu > 0) {
                return response()->json(['success' => 'true', 'Msg' => trans('mess.okdel')]);
            } else {
                return response()->json(['success' => 0, 'Msg' => trans('mess.errdel')]);
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errid')]);
        }
    }
    private function subir_imagen(Request $request, $foto, $folder = 'media/miembros')
    { //sube un archivo al servidor
        $request->validate(
            [
                "$foto" => 'required|image|mimes:png,jpg,jpeg|max:4048',
            ]
        );
        if ($request->file($foto)) {
            try {
                $file = $request->file($foto);
                $filename = time() . md5($file->getClientOriginalName()) . '.' . $file->getClientOriginalExtension(); //nombre nuevo
                // File upload location
                $location = $folder;
                //$location = 'import';
                // Upload file
                $file->move($location, $filename);
                return $filename;
            } catch (\Exception $e) {
                return 2;
            }
        } else {
            return 1;
        }
    }
}
