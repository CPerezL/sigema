<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\DatagridTrait;
use Auth;
use Illuminate\Http\Request;

class Inicio extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $data['title'] = ' SIGEMA G∴L∴S∴P∴';
        $data['temas'] = $this->getTemplates();
        $data['pagina'] = 'Bienvenido'; //MENSAJE DE INICIO
        return view('layouts.easyuipage', $data);
    }
    public function update_template(Request $request)
    {
        $temp = $request->input('template', 1);
        $ret = User::where('id', Auth::user()->id)->update(['template' => $temp]);
        if ($ret > 0) {
            return response()->json(['success' => 'true', 'Msg' => 'Tema cambiado']);
        } else {
            return response()->json(['success' => 0, 'Msg' => 'El tema es el actual']);
        }
    }
    private function getTemplates()
    {
        $temp = array();
        $temp[1] = 'Tema SIGEMA 2';
        $temp[2] = 'Tema Claro';
        $temp[3] = 'Tema Verde';
        $temp[4] = 'Tema Rojo';
        $temp[5] = 'Tema Negro';
        return $temp;
    }
    public function update_clave(Request $request)
    {
        $cc1 = $request->input('clave1', '');
        $cc2 = $request->input('clave2', '');
        $cc3 = $request->input('clave3', '');

        $ret = $this->checkClave($cc1);
        if ($ret) {
            if ($cc2 == $cc3) {
                User::where('id', Auth::user()->id)->update(['userpassword' => bcrypt($cc2)]);
                return response()->json(['success' => 'true', 'Msg' => 'Clave actualizada correctamente']);
            } else {
                return response()->json(['success' => 0, 'Msg' => 'Clave nueva diferente']);
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => 'Error en clave ingresada']);
        }
    }
    private function checkClave($clave)
    {
        $ret = User::where('id', Auth::user()->id)->first('userpassword');
        $cla = \Hash::check($clave, $ret->userpassword);
        return $cla;
    }
    public function get_datos(Request $request)
    {
        $page = $request->input('page', 1);
        $rows = $request->input('rows', 20);
        $val = self::validar('idValle', 0);
        $log = self::validar('idLogia', 0);
        $qry = User::getUltimos($log, $val);
        $total = count($qry);
        $qry2 = (object) ['total' => $total, 'rows' => $qry];
        return response()->json($qry2);
    }
}
