<?php

namespace App\Http\Controllers\Oruno;

use App\Http\Controllers\Controller;
use App\Models\admin\Logias_model;
use App\Models\Config_model;
use App\Models\oruno\Iniciacion_model;
use App\Traits\DatagridTrait;
use Illuminate\Http\Request;
use Response;
use Session;

class Oruno_tramite_ini_5 extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '095';
    public $controlador = 'oruno_tramite_ini_5';
    public $tipoqr = 2;
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     * revisar el monot generado y el monto a cobrar
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
        /*     variables de pagina     */
        return view('oruno.ini_5', $data);
    }
    public function get_datos(Request $request)
    {
        $val = self::validar('idValle', Session::get($this->controlador . '.valle'));
        $log = self::validar('idLogia', Session::get($this->controlador . '.taller'));
        $page = $request->input('page', 1);
        $rows = $request->input('rows', 20);
        $dias = Config_model::getValue('diasCircular');
        /*deberia d pasar el circual y recin depositar eso hay que aregñlar */
        $salida = Iniciacion_model::getTramitesPagar($page, $rows, Session::get($this->controlador . '.palabra', ''), $log, $val, 3,0,$dias);//emcircularizacioh
        $total = Iniciacion_model::getNumTramitesPagar(Session::get($this->controlador . '.palabra', ''), $log, $val, 3,0,$dias);
        $qry2 = (object) ['total' => $total, 'rows' => $salida];
        return response()->json($qry2);
    }
    public function get_ceremonia(Request $request)
    {
        $id = $request->input('id');
        if ($id > 0) {
            $salida = Iniciacion_model::getTramiteIni($id, 2);
        } else {
            $salida = ['success' => 0, 'Msg' => 'Error'];
        }
        return response()->json($salida);
    }
    public function update_tramite(Request $request)
    {
        $id = $request->input('id');
        if ($id > 0 && $request->hasFile('docdeposito')) {
            $antef = $this->upload_file($request, 'docdeposito', 'media/tramites', 2);
            if (strlen($antef) > 5) {
                //deberia de borrar foto anterior
                $data['docDepositoDer'] = $antef;
                $data['fechaPagoDerechos'] = \DateTime::createFromFormat('d/m/Y', $request->input('fechaPagoDerechos', '00-00-0000'))->format('Y-m-d');
                $data['nivelActual'] = 6;
                $data['fechaModificacion'] = date('Y-m-d');
                $salida = ['success' => 'true', 'Msg' => 'Pago registrado'];
                Iniciacion_model::where('idTramite', $id)->update($data);
            } else { $salida = ['success' => 0, 'Msg' => 'Error de datos'];}
        } else {
            $salida = ['success' => 0, 'Msg' => 'Error'];
        }
        return response()->json($salida);
    }
    private function upload_file(Request $request, $file, $folder = 'media/fotos', $opcion = 0)
    { //sube un archivo al servidor
        if ($request->hasFile($file)) {
            if ($opcion == 2) {
                $request->validate(["$file" => 'required|mimes:png,jpg,jpeg,pdf|max:10000']);
            } elseif ($opcion == 1) {
                $request->validate(["$file" => 'required|mimetypes:application/pdf|max:10000']);
            } else {
                $request->validate(["$file" => 'required|image|mimes:png,jpg,jpeg|max:5048']);
            }
            if ($request->file($file)) {
                try {
                    $file = $request->file($file);
                    $filename = time() . md5($file->getClientOriginalName()) . '.' . $file->getClientOriginalExtension(); //nombre nuevo
                    // File upload location
                    $location = $folder;
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
