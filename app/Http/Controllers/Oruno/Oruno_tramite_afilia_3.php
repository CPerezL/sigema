<?php
namespace App\Http\Controllers\Oruno;

use App\Http\Controllers\Controller;
use App\Models\admin\Logias_model;
use App\Models\Logs_model;
use App\Models\oruno\Afiliacion_model;
use App\Traits\DatagridTrait;
use Auth;
use Illuminate\Http\Request;
use Response;
use Session;

class Oruno_tramite_afilia_3 extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $tipoqr = 5;
    public $idmod = '074';
    public $controlador = 'oruno_tramite_afilia_3';
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
        /*     variables de pagina     */
        return view('oruno.afilia_3', $data);
    }
    public function get_datos(Request $request)
    {
        $valle = self::validar('idValle', Session::get($this->controlador . '.valle'));
        $taller = self::validar('idLogia', Session::get($this->controlador . '.taller'));
        $page = $request->input('page', 1);
        $rows = $request->input('rows', 20);
        $palabra = $request->input('palabra', '');
        $salida = Afiliacion_model::getListaTramites($page, $rows, $palabra, $valle, $taller, 3);
        $total = Afiliacion_model::getNumeroTramites($palabra, $valle, $taller, 3);
        $qry2 = (object) ['total' => $total, 'rows' => $salida];
        return response()->json($qry2);
    }

    public function registra_pago(Request $request)
    {
        $id = $request->input('id', 0);
        $resu = 0;
        $msg = '';
        if ($id > 0) {

            if ($request->hasFile('fileup2')) {
                $archivo2 = $this->upload_file($request, 'fileup2');
            } else {
                $archivo2 = '';
            }
            if (strlen($archivo2) > 10) {
                $data = array(
                    'archivo2' => $archivo2,
                    'estado' => 4,
                    'fechaDeposito' => \DateTime::createFromFormat('d/m/Y', $request->input('fDeposito', '00-00-0000'))->format('Y-m-d'),
                    'fechaJuramento' => \DateTime::createFromFormat('d/m/Y', $request->input('fechaJuramento', '00-00-0000'))->format('Y-m-d'),
                    'fechaModificacion' => date('Y-m-d h:m:s'),
                );
                $resu = Afiliacion_model::where('id', $id)->update($data);
                if ($resu == 1) {
                    Logs_model::insertLog('9', Auth::user()->id, 'Registro de pago', '', 0, 0);
                    return response()->json(['success' => 'true', 'Msg' => trans('mess.okinsert') . ' ' . $msg]);
                } else {
                    return response()->json(['success' => 0, 'Msg' => trans('mess.errinsert') . ' ' . $msg]);
                }
            } else {
                if ($request->input('okEnviar', 0) == 1) {
                    $data = array(
                        'estado' => 4,
                        'fechaDeposito' => \DateTime::createFromFormat('d/m/Y', $request->input('fDeposito', '00-00-0000'))->format('Y-m-d'),
                        'fechaJuramento' => \DateTime::createFromFormat('d/m/Y', $request->input('fechaJuramento', '00-00-0000'))->format('Y-m-d'),
                        'fechaModificacion' => date('Y-m-d h:m:s'),
                    );
                    $resu = Afiliacion_model::where('id', $id)->update($data);
                    return response()->json(['success' => 'true', 'Msg' => 'Tramite enviado correctamente']);
                } else {
                    return response()->json(['success' => 0, 'Msg' => "Archivo invalido"]);
                }
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errid')]);
        }
    }
    private function upload_file(Request $request, $fileup, $folder = 'media/tramites')
    { //sube un archivo al servidor
        $request->validate(
            [
                "$fileup" => 'required|mimes:png,jpg,jpeg,pdf|max:2048',
            ]
        );
        if ($request->file($fileup)) {
            try {
                $file = $request->file($fileup);
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
    }
}
