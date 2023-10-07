<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\admin\Valles_datos_model;
use App\Models\admin\Valles_model;
use App\Models\admin\Orientes_model;
use App\Traits\DatagridTrait;
use Session;

class Valles_datos extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '012';
    public $controlador = 'valles_datos';
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
        $data['oris'] = Orientes_model::getOrientesArray($this->oriente);
        /*     varibles de pagina* */
        return view('admin.valles_datos', $data);
    }
    public function get_datos(Request $request)
    {
        $page = $request->input('page', 1);
        $rows = $request->input('rows', 20);
        $qry = Valles_datos_model::getItems($page, $rows,Session::get($this->controlador . '.palabra', ''), Session::get($this->controlador . '.papelera', 0), Session::get($this->controlador . '.oriente', 0));
        $total = Valles_datos_model::getNumItems(Session::get($this->controlador . '.palabra', ''), Session::get($this->controlador . '.papelera', 0), Session::get($this->controlador . '.oriente', 0));
        $qry2 = (object) ['total' => $total, 'rows' => $qry];
        return response()->json($qry2);
    }

    public function save_datos(Request $request)
    {
        $valle = $request->input('valle', '');
        if (strlen($valle) > 4) {
            $data = array(
                'tipo' => $request->input('tipo', 1),
                'idOriente' => $request->input('idOriente'),
                'valle' => $valle);
            $resu = Valles_datos_model::insert($data);
            if ($resu > 0) {
                return response()->json(['success' => 'true', 'Msg' => trans('mess.okinsert')]);
            } else {
                return response()->json(['success' => 0, 'Msg' => trans('mess.errinsert')]);
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errordata')]);
        }
    }
    public function update_datos(Request $request)
    {
        $id = $request->input('idValle');
        $tarea = $request->input('task', 0);
        if ($tarea == 1 && $id > 0 && strlen($request->input('nombreCompleto')) > 2)//personales
        {
          $data = array(
            'nombreCompleto' => $request->input('nombreCompleto'),
            'fechaModificacion' => date('Y-m-d')
          );
          $datadata = array(
            'fundacion' => self::getFecha($request->input('fundacion')),//date
            'direccion' => $request->input('direccion', ''),
            'departamento' => $request->input('departamento', ''),
            'localidad' => $request->input('localidad', ''),
            'telefonos' => $request->input('telefonos', ''),
            'fechaModificacion' => date('Y-m-d')
          );
          if ($request->hasFile('foto')) {
            $foto = self::subir_imagen($request, 'foto');
            if (strlen($foto) > 5){
            $data['logo'] = $foto;
            $datadata['logo'] = $foto;
        }
        }
        $res = Valles_datos_model::updateValleData($id, $datadata);
        $resu = Valles_model::where("idValle", $id)->update($data)+$res;//
        }
        else
        {
          $resu = 0;
        }
            if ($resu > 0) {
                return response()->json(['success' => 'true', 'Msg' => trans('mess.okchange')]);
            } else {
                return response()->json(['success' => 0, 'Msg' => trans('mess.errchange')]);
            }
    }

    public function destroy_datos(Request $request)
    { //manda a palera
        $id = $request->input('id', 0);
        if ($id > 0) {
            $bor = $request->input('flag', 0);
            if ($bor > 0)
                $resu = Valles_datos_model::where("idValle", $id)->update(['borrado' => 0]);
            else
                $resu = Valles_datos_model::where("idValle", $id)->update(['borrado' => 1]);
            if ($resu > 0) {
                return response()->json(['success' => 'true', 'Msg' => trans('mess.okdel')]);
            } else {
                return response()->json(['success' => 0, 'Msg' => trans('mess.errdel')]);
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errid')]);
        }
    }
    public function show_papelera()
    {
        $papelera = Session::get($this->controlador . '.papelera');
        if ($papelera == 1)
            Session::put($this->controlador . '.papelera', 0);
        else
            Session::put($this->controlador . '.papelera', 1);
        return response()->json(['success' => 'true', 'Msg' => trans('mess.wait')]);
    }
    public function get_form(Request $request)
    {
        $id = $request->input('id');
        $task = $request->input('task');
        $salida = Valles_datos_model::getForm($id, $task);
        return response()->json($salida);
    }
    private function subir_imagen(Request $request, $foto, $folder = 'media')
    { //sube un archivo al servidor
        $request->validate(
            [
                "$foto" => 'required|image|mimes:png,jpg,jpeg|max:2048'
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
