<?php

namespace App\Http\Controllers;

use App\Models\admin\Miembros_model;
use Illuminate\Http\Request;
use App\Models\admin\Orientes_model;
use App\Models\admin\Valles_model;
use App\Models\admin\Importar_miembros_model;
//use App\Models\Logs_model;
use App\Models\admin\Membrecia_model;
use Session;
use Auth;
use App\Traits\DatagridTrait;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\MiembrosImport;
use Illuminate\Support\Facades\Validator;

class Importar_miembros extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '009';
    public $controlador = 'importar_miembros';
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
        $data['valles'] = Valles_model::getVallesArray($this->oriente, $this->valle); //---+++
        $data['oris'] = Orientes_model::getOrientesArray($this->oriente);
        /*     varibles de pagina* */
        return view('admin.importar_miembros', $data);
    }
    public function get_datos(Request $request)
    {
        $page = $request->input('page', 1);
        $rows = $request->input('rows', 20);
        $ori = $this->validar('idValle', Session::get($this->controlador . '.oriente', 0));
        $vall = $this->validar('idValle', Session::get($this->controlador . '.valle', 0));
        $qry = Importar_miembros_model::getItems($page, $rows, Auth::user()->id, $ori, $vall);
        $total = Importar_miembros_model::getNumItems(Auth::user()->id, $ori, $vall);
        $qry2 = (object) ['total' => $total, 'rows' => $qry];
        return response()->json($qry2);
    }
    //////////------------------------  funcoiens extras
    public function upload_test(Request $request)
    {
        $cdup = 0;
        $cnue = 0;
        $ctot = 0;


        $validator = Validator::make(
            $request->all(),
            [
                'archivoLee' => 'required|mimes:xlsx,csv,xls|max:2048'
            ],
        );

        if ($validator->fails()) {
            return response()->json(['success' => false, 'Msg' => trans('mess.errfile')]);

        } else {

            $file = $request->file('archivoLee');
            $rows = Excel::toArray(new MiembrosImport, $file);
            foreach ($rows[0] as $auxdatos) {
                if (strlen(trim($auxdatos['nombres'])) > 1 && (strlen(trim($auxdatos['paterno'])) > 1 || strlen(trim($auxdatos['materno'])) > 1)) { //si es una linea con datos
                    $ctot++;
                    unset($datat);
                    $datat = array();
                    $texto1 = $auxdatos['paterno'] . ' ' . $auxdatos['materno'].' '.$auxdatos['nombres'];
                    $texto2 = str_replace(array('\'', '"'), '', $texto1);
                    $new_str = str_replace(' ', '', $texto2);
                    $idnombre = strtoupper($new_str);
                    //carga array
                    $datat['idOriente'] = Session::get($this->controlador . '.oriente', 1);
                    $datat['Paterno'] = $auxdatos['paterno'];
                    $datat['Materno'] = $auxdatos['materno'];
                    $datat['Nombres'] = $auxdatos['nombres'];
                    $datat['Grado'] = $auxdatos['grado'];
                    $datat['NombreCompleto'] = $texto2;
                    $datat['NombreCompletoID'] = $idnombre;
                    if (isset($datat['Miembro']))
                        $datat['Miembro'] = $auxdatos['miembro'];
                    else
                        $datat['Miembro'] = 'Regular';
                    $datat['LogiaActual'] = $auxdatos['logiaactual'];
                    $usuario = self::crearUsuario($auxdatos['paterno'], $auxdatos['materno'], $auxdatos['nombres'], $auxdatos['logiaactual']);
                    $datat['username'] = $usuario;
                    $datat['clave'] = md5('amistad');
                    $datat['Estado'] = 1;
                    if (strlen($auxdatos['fechainiciacion']) > 2)
                        $datat['FechaIniciacion'] = date('Y-m-d', strtotime($auxdatos['fechainiciacion']));
                    $datat['LogiaIniciacion'] = $auxdatos['logiainiciacion'];
                    if (strlen($auxdatos['fechaaumentosalario']) > 2)
                        $datat['FechaAumentoSalario'] = date('Y-m-d', strtotime($auxdatos['fechaaumentosalario']));
                    $datat['LogiaAumento'] = $auxdatos['logiaaumento'];
                    if (strlen($auxdatos['fechaexaltacion']) > 2)
                        $datat['FechaExaltacion'] = date('Y-m-d', strtotime($auxdatos['fechaexaltacion']));
                    $datat['LogiaExaltacion'] = $auxdatos['logiaexaltacion'];
                    if (strlen($auxdatos['fechahonorario']) > 2)
                        $datat['FechaHonorario'] = date('Y-m-d', strtotime($auxdatos['fechahonorario']));
                    $datat['LogiaHonorario'] = $auxdatos['logiahonorario'];
                    $datat['DecretoHonorario'] = $auxdatos['decretohonorario'];
                    if (strlen($auxdatos['fechaadvitam']) > 2)
                        $datat['FechaAdVitam'] = date('Y-m-d', strtotime($auxdatos['fechaadvitam']));
                    $datat['LogiaAdVitam'] = $auxdatos['logiaadvitam'];
                    $datat['DecretoAdVitam'] = $auxdatos['decretoadvitam'];
                    $id = Importar_miembros_model::checkNombre($idnombre);
                    if ($id > 0) {
                        $cdup++;
                        $datat['duplicado'] = 1;

                    } else {
                        $cnue++;
                        //inserta en miembros
                        Membrecia_model::insert($datat);
                    }
                    $datat['idUsuario'] = Auth::user()->id;
                    Importar_miembros_model::insert($datat);
                }
            }
            if ($ctot > 0)
                return response()->json(['success' => true, 'Msg' => trans('mess.ok'), 'cargados' => $cnue, 'duplicados' => $cdup, 'sinid' => $ctot]);
            else
                return response()->json(['success' => false, 'Msg' => 'Error']);
        }
    }

    public function subir_archivo(Request $request)
    { //sube un archivo al servidor
        $request->validate(
            [
                'archivoLee' => 'required|mimes:xlsx,csv,xls|max:2048'
            ]
        );
        if ($request->file('archivoLee')) {
            try {
                $file = $request->file('archivoLee');
                $filename = time() . md5($file->getClientOriginalName()); //nombre nuevo
                // File upload location
                $location = 'import';
                // Upload file
                $file->move($location, $filename);
                return response()->json(['success' => 'true', 'Msg' => 'archivo:' . $filename]);
            } catch (\Exception $e) {
                return response()->json(['success' => 0, 'Msg' => trans('mess.errupload') . $filename]);
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => 'Error']);
        }
    }
    private function crearUsuario($apat, $amat, $nom, $logia)
    {
        $nome1 = self::preparaTexto($nom);
        $nome = strtolower($nome1);
        $ini = substr($nome, 0, 1);
        if (strlen($apat) > 1) {
            $medio1 = self::preparaTexto($apat);
            $medio = strtolower($medio1);
            if (strlen($amat) > 1) {
                $amat1 = self::preparaTexto($amat);
                $amat2 = strtolower($amat1);
                $fin = substr($amat2, 0, 1);
            } else {
                $fin = '';
            }
        } elseif (strlen($amat) > 1) {
            $medio1 = self::preparaTexto($amat);
            $medio = strtolower($medio1);
            $fin = '';
        } else {
            $medio = 'error';
            $fin = '';
        }
        $user = $ini . $medio . $fin . $logia;
        return $user;
    }

}
