<?php
namespace App\Http\Controllers\Oruno;

use App\Http\Controllers\Controller;
use App\Models\admin\Logias_model;
use App\Models\oruno\Regularizacion_model;
use App\Models\admin\Profesiones_model;
use App\Models\Paises_model;
use App\Traits\DatagridTrait;
use Illuminate\Http\Request;
use Session;

class Oruno_tramite_regu_1 extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '10';
    public $controlador = 'oruno_tramite_regu_1';
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
        $data['profes'] = Profesiones_model::getProfesionesArray();
        $data['paises'] = Paises_model::getPaisesArray();
        /*     varibles de pagina* */
        return view('oruno.regu_1', $data);
    }
    public function get_datos(Request $request)
    {
        $page = $request->input('page', 1);
        $rows = $request->input('rows', 20);
        $log = Session::get($this->controlador . '.taller');
        $qry = Regularizacion_model::getRegistros($page, $rows, Session::get($this->controlador . '.palabra', ''), $log, 0, 1);
        $total = Regularizacion_model::getNumRegistros(Session::get($this->controlador . '.palabra', ''), $log, 0, 1);
        $qry2 = (object) ['total' => $total, 'rows' => $qry];
        return response()->json($qry2);
    }

    public function get_tramite(Request $request)
    {
        $tramite = $request->input('idTra', 0);
        if ($tramite > 0) {
            $salida = Regularizacion_model::getTramiteRegu($tramite);
            return response()->json($salida);
        } else {
            return response()->json(['success' => 0, 'Msg' => 'Wait"']);
        }
    }
    public function get_nlogia()
    {
        $tal = Session::get($this->controlador . '.taller', '');
        if ($tal > 0) {
            $salida = Regularizacion_model::getnlogia($tal);
            return response()->json($salida);
        } else {
            return response()->json(['success' => 0, 'Msg' => 'Error no selecciono Logia"']);
        }
    }

    public function save_tramite(Request $request)
    {
        $log = Session::get($this->controlador . '.taller');
        if (strlen($request->input('documento')) > 3 && strlen($request->input('nombres')) > 3 && strlen($request->input('maestro')) > 3 && Session::get($this->controlador . '.taller') > 0 && strlen($request->input('lugarNac')) > 2 && strlen($request->input('profesion')) > 2 && strlen($request->input('fInsinuacion')) > 2 && $log > 0) {
            $proceso = Regularizacion_model::checkObservacion($request->input('documento'));
            if (!empty($proceso)) {
                $salida = ['success' => 0, 'Msg' => 'Profano : ' . $proceso[0]->profano . ' esta observado por: ' . $proceso[0]->descripcion];
            } else {
                if ($request->hasFile('fotografia')) {

                    $valle = Logias_model::select('valle')->firstWhere('numero', $log)->valle;
                    $foto = $this->subir_imagen($request, 'fotografia');
                    $cuaderf = $this->subir_imagen($request, 'cuadernillo', 'media/tramites', 1);
                    $antef = $this->subir_imagen($request, 'antecedentes', 'media/tramites', 1);
                    $antef2 = $this->subir_imagen($request, 'antecedentes2', 'media/tramites', 1);

                    if (strlen($request->input('fechaNac')) > 2) {
                        $data = array(
                            'nivelActual' => '1', //pasa directo al 1
                            'logia' => $log,
                            'valle' => $valle,
                            'logiaName' => $request->input('logiaName'),
                            'foto' => $foto,
                            'nombres' => $request->input('nombres'),
                            'apPaterno' => $request->input('apPaterno'),
                            'apMaterno' => $request->input('apMaterno'),
                            'fechaNac' => \DateTime::createFromFormat('d/m/Y', $request->input('fechaNac', '01-01-1800'))->format('Y-m-d'),
                            'nacionalidad' => $request->input('nacionalidad'),
                            'lugarNac' => $request->input('lugarNac'),
                            'profesion' => $request->input('profesion'),
                            'documento' => $request->input('documento'),
                            'domicilio' => $request->input('domicilio'),
                            'fonoDomicilio' => $request->input('fonoDomicilio'),
                            'celular' => $request->input('celular'),
                            'email' => $request->input('email'),
                            'estadoCivil' => $request->input('estadoCivil'),
                            'esposa' => $request->input('esposa'),
                            'padre' => $request->input('padre'),
                            'madre' => $request->input('madre'),
                            'empresa' => $request->input('empresa'),
                            'direccionEmpresa' => $request->input('direccionEmpresa'),
                            'fonoEmpresa' => $request->input('fonoEmpresa'),
                            'cargo' => $request->input('cargo'),
                            'resideBolivia' => $request->input('resideBolivia'),
                            'aval1' => $request->input('aval1'),
                            'aval1Logia' => $request->input('aval1Logia'),
                            'aval2' => $request->input('aval2'),
                            'aval2Logia' => $request->input('aval2Logia'),
                            'aval3' => $request->input('aval3'),
                            'aval3Logia' => $request->input('aval3Logia'),
                            'maestro' => $request->input('maestro'),
                            'maestro2' => $request->input('maestro2'),
                            'grado' => $request->input('grado'),
                            'antecedentes' => $antef,
                            'antecedentes2' => $antef2,
                            'cuadernillo' => $cuaderf,
                            'fInsinuacion' => \DateTime::createFromFormat('d/m/Y', $request->input('fInsinuacion', '01-01-1900'))->format('Y-m-d'),
                            'actaInsinuacion' => $request->input('actaInsinuacion'),
                            'fechaAprobPase' => \DateTime::createFromFormat('d/m/Y', $request->input('fechaAprobPase', '01-01-1900'))->format('Y-m-d'),
                            'actaAprobPase' => $request->input('actaAprobPase'),
                            'fechaCreacion' => date('Y-m-d'),
                            'fechaModificacion' => date('Y-m-d'),
                            'fechaJuramento' => \DateTime::createFromFormat('d/m/Y', $request->input('fechaIniciacion', date('d/m/Y')))->format('Y-m-d'),
                        );
                        $resu = Regularizacion_model::insert($data);
                        if ($resu > 0) {
                            $salida = ['success' => 'true', 'Msg' => 'Datos insertados correctamente'];
                        } else {
                            $salida = ['success' => 0, 'Msg' => 'Ocurrio un error al salvar los datos'];
                        }
                    } else {
                        $salida = ['success' => 0, 'Msg' => 'Datos incompletos'];
                    }
                } else {
                    $salida = ['success' => 0, 'Msg' => 'Archivo no valido o demasiado grande (Max 4MB), La Foto es obligatoria'];
                }
            }
        } else {
            $salida = ['success' => 0, 'Msg' => 'Datos incompletos'];
        }
        //return response()->json();
        return response()->json($salida);
    }
    public function update_tramite(Request $request)
    {
        $id = $request->input('idTramite', 0);
        if ($id > 0 && strlen($request->input('nombres')) > 3 && strlen($request->input('lugarNac')) > 2 && strlen($request->input('profesion')) > 3) {
            $data = array(
                'nombres' => $request->input('nombres'),
                'apPaterno' => $request->input('apPaterno'),
                'apMaterno' => $request->input('apMaterno'),
                'fechaNac' => \DateTime::createFromFormat('d/m/Y', $request->input('fechaNac', '00-00-0000'))->format('Y-m-d'),
                'nacionalidad' => $request->input('nacionalidad'),
                'lugarNac' => $request->input('lugarNac'),
                'profesion' => $request->input('profesion'),
                'documento' => $request->input('documento'),
                'domicilio' => $request->input('domicilio'),
                'fonoDomicilio' => $request->input('fonoDomicilio'),
                'celular' => $request->input('celular'),
                'email' => $request->input('email'),
                'estadoCivil' => $request->input('estadoCivil',0),
                'esposa' => $request->input('esposa'),
                'padre' => $request->input('padre'),
                'madre' => $request->input('madre'),
                'empresa' => $request->input('empresa'),
                'direccionEmpresa' => $request->input('direccionEmpresa'),
                'fonoEmpresa' => $request->input('fonoEmpresa'),
                'cargo' => $request->input('cargo'),
                'resideBolivia' => $request->input('resideBolivia'),
                'aval1' => $request->input('aval1'),
                'aval1Logia' => $request->input('aval1Logia'),
                'aval2' => $request->input('aval2'),
                'aval2Logia' => $request->input('aval2Logia'),
                'aval3' => $request->input('aval3'),
                'aval3Logia' => $request->input('aval3Logia'),
                'maestro' => $request->input('maestro'),
                'maestro2' => $request->input('maestro2'),
                'grado' => $request->input('grado'),
                'fInsinuacion' => \DateTime::createFromFormat('d/m/Y', $request->input('fInsinuacion', '00-00-0000'))->format('Y-m-d'),
                'actaInsinuacion' => $request->input('actaInsinuacion'),
                'fechaAprobPase' => \DateTime::createFromFormat('d/m/Y', $request->input('fechaAprobPase', '00-00-0000'))->format('Y-m-d'),
                'actaAprobPase' => $request->input('actaAprobPase'),
                'fechaModificacion' => date('Y-m-d'),
                'fechaIniciacion' => \DateTime::createFromFormat('d/m/Y', $request->input('fechaIniciacion', date('d/m/Y')))->format('Y-m-d'),
            );

            if ($request->hasFile('fotografia')) {
                $foto = $this->subir_imagen($request, 'fotografia');
                if (strlen($foto) > 5) {
                    //deberia de borrar foto anterior
                    $data['foto'] = $foto;
                }
            }
            if ($request->hasFile('antecedentes')) {
                $antef = $this->subir_imagen($request, 'antecedentes', 'media/tramites', 1);
                if (strlen($antef) > 5) {
                    //deberia de borrar foto anterior
                    $data['antecedentes'] = $antef;
                }
            }
            if ($request->hasFile('antecedentes2')) {
                $antef = $this->subir_imagen($request, 'antecedentes2', 'media/tramites', 1);
                if (strlen($antef) > 5) {
                    //deberia de borrar foto anterior
                    $data['antecedentes2'] = $antef;
                }
            }
            if ($request->hasFile('cuadernillo')) {
                $cuaderf = $this->subir_imagen($request, 'cuadernillo', 'media/tramites', 1);
                if (strlen($cuaderf) > 5) {
                    //deberia de borrar foto anterior
                    $data['cuadernillo'] = $cuaderf;
                }
            }
            $resu = Regularizacion_model::where('idTramite', $id)->update($data);
            if ($resu > 0) {
                $salida = ['success' => 'true', 'Msg' => 'Datos actualizados correctamente'];
            } else {
                $salida = ['success' => 0, 'Msg' => 'Ocurrio un error al salvar los datos'];
            }
        } else {
            $salida = ['success' => 0, 'Msg' => 'Datos incompletos'];
        }
        return response()->json($salida);
    }
    private function subir_imagen(Request $request, $foto, $folder = 'media/fotos', $opcion = 0)
    { //sube un archivo al servidor
        if ($request->hasFile($foto)) {
            if ($opcion == 1) {
                $request->validate(["$foto" => 'required|mimetypes:application/pdf|max:10000']);
            } else {
                $request->validate(["$foto" => 'required|image|mimes:png,jpg,jpeg|max:5048']);
            }
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
        } else {return '';}
    }
}
