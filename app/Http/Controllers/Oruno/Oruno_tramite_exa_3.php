<?php

namespace App\Http\Controllers\Oruno;
use App\Http\Controllers\Controller;

use App\Models\admin\Logias_model;
use App\Models\mecom\Pagos_extra_model;
use App\Models\mecom\Pagos_montos_model;
use App\Models\mecom\Pagos_registros_model;
use App\Models\oruno\Exaltacion_model;
use App\Models\tramites\Pagosqr_model;
use App\Traits\DatagridTrait;
use Auth;
use Illuminate\Http\Request;
use Response;
use Session;

class Oruno_tramite_exa_3 extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '090';
    public $controlador = 'oruno_tramite_exa_3';
    public $tipoqr = 4;
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
        $data['pagos'] = Pagos_montos_model::getPagosExtra($this->valle, $this->taller);
        /* variables de sintesis */
        $data['linkiframe'] = env('QR_LINKFRAME'); //enalce del pago por ahora esta directo
        $data['entidad'] = env('QR_ENTIDAD'); //enalce del pago por ahora esta directo
        $data['linkaccion'] = env('QR_LINKACCION'); //
        /*     variables de pagina     */
        return view('oruno.exa_3', $data);
    }
    public function get_datos(Request $request)
    {
        $palabra = Session::get($this->controlador . '.palabra');
        $pago = Session::get($this->controlador . '.pago');
        $valle = self::validar('idValle', Session::get($this->controlador . '.valle'));
        $taller = self::validar('idLogia', Session::get($this->controlador . '.taller'));
        $page = $request->input('page', 1);
        $rows = $request->input('rows', 20);
        $salida = Exaltacion_model::getTramitesListos($page, $rows, $palabra, $taller, $valle, 1);
        $total = Exaltacion_model::getNumTramitesListos($page, $rows, $palabra, $taller, $valle, 1);
        $qry2 = (object) ['total' => $total, 'rows' => $salida];
        return response()->json($qry2);
    }
    public function get_ceremonia(Request $request)
    {
        $cere = $request->input('id');
        if ($cere > 0) {
            $salida = Exaltacion_model::getTramiteExa($cere);
        } else {
            $salida = ['success' => 0, 'Msg' => 'Error'];
        }
        return response()->json($salida);
    }

    public function update_tramite(Request $request)
    {
        $id = $request->input('id');
        if ($id > 0 && $request->hasFile('depositoGDR')) {
            $antef = $this->upload_file($request, 'depositoGDR', 'media/tramites', 2);
            if (strlen($antef) > 5) {
                //deberia de borrar foto anterior
                $data['depositoGDR'] = $antef;
                $data['fechaDepoGDR'] = \DateTime::createFromFormat('d/m/Y', $request->input('fechaDepoGDR', '00-00-0000'))->format('Y-m-d');
                $data['nivelActual'] = 4;
                $data['fechaModificacion'] = date('Y-m-d');
                $salida = ['success' => 'true', 'Msg' => 'Pago registrado'];
                Exaltacion_model::where('idTramite', $id)->update($data);
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
