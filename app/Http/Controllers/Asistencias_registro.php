<?php

namespace App\Http\Controllers;

use App\Models\admin\Logias_model;
use App\Models\admin\Ritos_model;
use App\Models\asistencias\Asistenciadata_model;
use App\Models\asistencias\Asistenciaextra_model;
use App\Models\asistencias\Asistencias_model;
use App\Models\Config_model;
use App\Traits\DatagridTrait;
use Illuminate\Http\Request;
use PDF;
use Session;

class Asistencias_registro extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '057';
    public $controlador = 'asistencias_registro';
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
        if ($this->taller > 0) {
            Session::put($this->controlador . '.tenida', $dia = Asistencias_model::getDiaTaller($this->taller));
        }
        $data['_mid'] = $this->idmod; //---
        $data['year'] = date('Y'); //---
        $data['month'] = date('m');
        Session::put($this->controlador . '.mes', date('m'));
        Session::put($this->controlador . '.gestion', date('Y'));
        $data['meses'] = $this->getMeses();
        $data['_controller'] = $this->controlador;
        $data['logias'] = Logias_model::getlogiasArray($this->oriente, $this->valle, $this->taller);
        /*     varibles de pagina* */
        return view('asistencias.registro', $data);
    }
    public function get_dias(Request $request)
    {
        $dia = Session::get($this->controlador . '.tenida'); //revisar
        if ($dia > 0) {
            $tal = self::validar('idLogia', Session::get($this->controlador . '.taller'));
            $faper = Config_model::getValue('fechaApertura');
            $fcier = Config_model::getValue('fechaCierre');
            $ges = Session::get($this->controlador . '.gestion');
            $mes = Session::get($this->controlador . '.mes');
            Asistencias_model::setRegular(Config_model::getValue('mesesDeuda'));
            $salida = Asistencias_model::getDiaTenida($faper, $fcier, $ges, $mes, $dia, $tal);
            $total = count($salida);

            $qry2 = (object) ['total' => $total, 'rows' => $salida];
        } else {
            $qry2 = (object) ['total' => 0, 'rows' => ''];
        }

        return response()->json($qry2);
    }
    public function get_datos(Request $request)
    {
        $diat = Session::get($this->controlador . '.ftenida', 0);
        if ($diat > 0) {
            $ges = Session::get($this->controlador . '.gestion');
            $mes = Session::get($this->controlador . '.mes');
            $tal = self::validar('idLogia', Session::get($this->controlador . '.taller'));
            Asistencias_model::setRegular(Config_model::getValue('mesesDeuda'));
            $salida = Asistencias_model::getListaAsistencia($tal, $ges, $mes, $diat);
            $qry2 = (object) ['total' => 0, 'rows' => $salida];
        } else {
            $qry2 = (object) ['total' => 0, 'rows' => ''];
        }
        return response()->json($qry2);
    }
    public function get_visitas(Request $request)
    {
        $taller = self::validar('idLogia', Session::get($this->controlador . '.taller'));
        $diat = Session::get($this->controlador . '.ftenida', 0);
        $ges = Session::get($this->controlador . '.gestion');
        $mes = Session::get($this->controlador . '.mes');
        $fvisita = $ges . '-' . $mes . '-' . $diat;
        if ($taller > 0 && strlen($fvisita) > 6) {
            $salida = Asistencias_model::getVisitas($fvisita, $taller);
            $qry2 = (object) ['total' => 0, 'rows' => $salida];
        } else {
            $qry2 = (object) ['total' => 0, 'rows' => ''];
        }
        return response()->json($qry2);
    }
    public function get_oficiales(Request $request)
    {
        $taller = self::validar('idLogia', Session::get($this->controlador . '.taller'));
        $salida = Asistencias_model::getOficiales($taller);
        if (is_null($salida)) {
            $qry2 = (object) ['total' => 0, 'rows' => ''];
        } else {
            $total = count($salida);
            $qry2 = (object) ['total' => $total, 'rows' => $salida];
        }
        return response()->json($qry2);
    }
    public function get_miembros(Request $request)
    {
        $taller = self::validar('idLogia', Session::get($this->controlador . '.taller'));
        if ($taller > 0) {
            $filter = $request->input('filterRules');
            if (strlen($filter) > 2) {
                $salida = Asistencias_model::getMiembros(Config_model::getValue('mesesDeuda'), $taller, $filter);
                $total = 0;
            } else {
                $salida = Asistencias_model::getMiembros(Config_model::getValue('mesesDeuda'), $taller);
                $total = 0;
            }
            $qry2 = (object) ['total' => $total, 'rows' => $salida];
        } else {
            $qry2 = (object) ['total' => 0, 'rows' => ''];
        }
        return response()->json($qry2);
    }
    public function filter_diatenida(Request $request)
    {
        if ($request->input('ftenida', 0) > 0 && Session::get($this->controlador . '.taller') > 0) {
            Session::put($this->controlador . '.ftenida', $request->input('ftenida', 0));
            $diat = Session::get($this->controlador . '.ftenida', 0);
            $ges = Session::get($this->controlador . '.gestion');
            $mes = Session::get($this->controlador . '.mes');
            $gest = "/" . $ges;
            $fechatt = $ges . '-' . $mes . '-' . $diat;
            $dataten = Asistencias_model::getDataAsisSimple(Session::get($this->controlador . '.taller'), $fechatt);
            $grado = 0;
            if (is_null($dataten)) {
                $datasis = "Fecha: $fechatt<br>GRADO : No asignado<br>Nro Acta: Sin numero";
            } else {
                $datasis = "Fecha: $fechatt<br>GRADO : $dataten->grado<br>Nro Acta: $dataten->numeroActa1, $dataten->numeroActa2, $dataten->numeroActa3 " . $gest;
                $grado = $dataten->grado;
            }
            $data = array($this->controlador . '.ftenida' => $request->input('ftenida'));
            Session::put($data);
            $qry2 = (object) ['success' => 'true', 'Msg' => $datasis, 'Grado' => $grado, 'uno' => $dataten->numeroActa1, 'dos' => $dataten->numeroActa2, 'tres' => $dataten->numeroActa3];
            return response()->json($qry2);
        } else {
            return response()->json(['success' => 0, 'Msg' => 'Wait...']);
        }
    }
    public function filter_taller(Request $request)
    {
        $taller = $request->input('taller', 0);
        if ($taller > 0 && $taller != Session::get($this->controlador . '.taller')) {
            $dia = Asistencias_model::getDiaTaller($taller);
            $data = array($this->controlador . '.taller' => $taller, $this->controlador . '.tenida' => $dia);
            Session::put($data);
            return response()->json(['success' => 'true', 'Msg' => 'Wait...']);
        } else {
            return response()->json(['success' => 0, 'Msg' => 'Wait...']);
        }
    }
    public function get_datasis(Request $request)
    {
        $dia = $request->input('id');
        Session::put($this->controlador . '.diat', $dia);
        $fechat = Session::get($this->controlador . '.gestion') . '-' . Session::get($this->controlador . '.mes') . '-' . $dia;
        $taller = self::validar('idLogia', Session::get($this->controlador . '.taller'));
        $salida = Asistencias_model::getDataAsis($taller, $fechat);
        // $qry2 = (object) ['total' => 0, 'rows' => $salida];
        return response()->json($salida);
    }
    public function update_datasis(Request $request)
    {
        $id = $request->input('idAsistenciaData');
        $fechat = Session::get($this->controlador . '.gestion') . '-' . Session::get($this->controlador . '.mes') . '-' . Session::get($this->controlador . '.diat');
        if ($id > 0) //actualiza
        {
            $datas = array(
                'grado' => $request->input('grado'),
                'numeroActa1' => $request->input('numeroActa1', 0),
                'numeroActa2' => $request->input('numeroActa2', 0),
                'numeroActa3' => $request->input('numeroActa3', 0),
            );
            $resu = Asistenciadata_model::where('idAsistenciaData', $id)->update($datas);
        } else //crea
        {
            $fechacierre = date("Y-m-d", strtotime("$fechat +2 weeks"));
            $datas = array(
                'idLogia' => Session::get($this->controlador . '.taller'),
                'fechaTenida' => $fechat,
                'fechaCierre' => $fechacierre,
                'grado' => $request->input('grado'),
                'numeroActa1' => $request->input('numeroActa1', 0),
                'numeroActa2' => $request->input('numeroActa2', 0),
                'numeroActa3' => $request->input('numeroActa3', 0),
            );
            $resu = Asistenciadata_model::insert($datas);
        }
        if ($resu > 0) {
            $datasis = 'Fecha: ' . $fechat . '<br>GRADO : ' . $request->input('grado') . '<br>Nro Acta: ' . $request->input('numeroActa1') . ' - ' . $request->input('numeroActa2') . ' - ' . $request->input('numeroActa3');
            return response()->json(['success' => 'true', 'Msg' => trans('mess.okchange'), 'Ret' => $datasis]);
        } else {
            return response()->json(['success' => 0, 'Msg' => 'No se modifico nada']);
        }
    }
    public function update_asis(Request $request)
    {
        $id = $request->input('idmiembro');
        if ($id > 0) {
            $check = Asistencias_model::checkAsis(Session::get($this->controlador . '.taller'), Session::get($this->controlador . '.gestion'), $id, Session::get($this->controlador . '.gestion') . '-' . Session::get($this->controlador . '.mes') . '-' . Session::get($this->controlador . '.ftenida'));
            if ($check > 0) {
                Asistencias_model::where("idAsistencia", $check)->delete();
                return response()->json(['success' => 'true', 'Msg' => 'Asistencia quitada']);
            } else {
                $datas = array(
                    'idLogia' => Session::get($this->controlador . '.taller'),
                    'gestion' => Session::get($this->controlador . '.gestion'),
                    'idMiembro' => $id,
                    'grado' => $request->input('idgrado', 1),
                    'fechaTenida' => Session::get($this->controlador . '.gestion') . '-' . Session::get($this->controlador . '.mes') . '-' . Session::get($this->controlador . '.ftenida'),
                    'fechaAlta' => date("Y-m-d", time()),
                );
                $resu = Asistencias_model::insert($datas);
                if ($resu > 0) {
                    return response()->json(['success' => 'true', 'Msg' => 'Asistencia asignada correctamente']);
                } else {
                    return response()->json(['success' => 0, 'Msg' => trans('mess.errinsert')]);
                }
            }
        } else {
            return response()->json(['success' => 0, 'Msg' => trans('mess.errid')]);
        }
    }
    public function add_visita(Request $request)
    {
        $idm = $request->input('id');
        $taller = Session::get($this->controlador . '.taller');
        $fvisita = Session::get($this->controlador . '.gestion') . '-' . Session::get($this->controlador . '.mes') . '-' . Session::get($this->controlador . '.ftenida');
        $fechacierre = Asistencias_model::getFechaCierre($taller, $fvisita); //fecha de cierre de datos
        $date2 = strtotime(date("Y-m-d"));
        $date4 = strtotime($fechacierre);
        if ($date4 >= $date2) {
            $fvalida = 2;
        } else {
            $fvalida = 0;
        }
        //fecha pasada
        if ($fvalida > 0) {
            if (strlen($fvisita) > 6 && $idm > 0 && $taller > 0 && $fvalida > 0) {
                $check = Asistenciaextra_model::checkAsisExtra(Session::get($this->controlador . '.gestion'), $idm, $fvisita);
                $grado = Asistencias_model::getGrado($idm);
                if ($check) {
                    $datas = array(
                        'idMiembro' => $idm,
                        'grado' => $grado,
                        'idLogia' => $taller,
                        'motivo' => 1,
                        'fechaExtra' => $fvisita,
                        'fechaAlta' => date("Y-m-d"),
                        'gestion' => Session::get($this->controlador . '.gestion'),
                    );
                    $resu = Asistenciaextra_model::insert($datas);

                    if ($resu > 0) {
                        $data = ["success" => 'true', "Msg" => trans('mess.okinsert')];
                    } else {
                        $data = ["success" => 0, "Msg" => trans('mess.errinsert')];
                    }
                } else {
                    $data = ["success" => 0, "Msg" => "Ya tiene una asistencia en esta semana"];
                }
            } else {
                $data = ["success" => 0, "Msg" => trans('mess.errdata')];
            }
        } else {
            $data = ["success" => 0, "Msg" => "Fecha Pasada, no se puede adicionar visitas"];
        }
        return response()->json($data);
    }
    public function quitar_visita(Request $request)
    {
        $idm = $request->input('id');
        if ($idm) {
            $resu = Asistenciaextra_model::where('idExtra', $idm)->delete();
            if ($resu > 0) {
                $data = ["success" => 'true', "Msg" => "Visita eliminada correctamente"];
            } else {
                $data = ["success" => 0, "Msg" => trans('mess.errdel')];
            }
        } else {
            $data = ["success" => 0, "Msg" => trans('mess.errid')];
        }
        return response()->json($data);
    }

    public function set_oficialpt(Request $request)
    {
        $idm = $request->input('idmiembro');
        $idc = $request->input('idcargo');
        if ($idm > 0 && $idc > 0) {
            $check = Asistencias_model::checkAsis(Session::get($this->controlador . '.taller'), Session::get($this->controlador . '.gestion'), $idm, Session::get($this->controlador . '.gestion') . '-' . Session::get($this->controlador . '.mes') . '-' . Session::get($this->controlador . '.ftenida'));
            $checkc = Asistencias_model::checkCargo(Session::get($this->controlador . '.taller'), Session::get($this->controlador . '.gestion'), $idc, Session::get($this->controlador . '.gestion') . '-' . Session::get($this->controlador . '.mes') . '-' . Session::get($this->controlador . '.ftenida'));
            if ($checkc > 0) {
                $data = ["success" => 0, "Msg" => 'Ya tiene asignado esta oficialidad'];
            } else {
                if ($check > 0) //ya tiene asistencia
                {
                    $datas = array(
                        'idOficialPT' => $idc,
                    );
                    Asistencias_model::where('idAsistencia', $check)->update($datas);
                    $data = ["success" => 'true', "Msg" => "Cargo asignado correctamente"];
                } else {
                    $datas = array(
                        'idLogia' => Session::get($this->controlador . '.taller'),
                        'gestion' => Session::get($this->controlador . '.gestion'),
                        'idMiembro' => $idm,
                        'idOficialPT' => $idc,
                        'fechaTenida' => Session::get($this->controlador . '.gestion') . '-' . Session::get($this->controlador . '.mes') . '-' . Session::get($this->controlador . '.ftenida'),
                        'fechaAlta' => date("Y-m-d", time()),
                    );
                    $resu = Asistencias_model::insert($datas);
                    if ($resu > 0) {
                        $data = ["success" => 'true', "Msg" => "Asistencia y cargo asignados correctamente"];
                    } else {
                        $data = ["success" => 0, "Msg" => trans('mess.okinsert')];
                    }
                }
            }
        } else {
            $data = ["success" => 0, "Msg" => trans('mess.errid')];
        }

        return response()->json($data);
    }
    public function gen_planilla(Request $request)
    {
        $plagrado = $request->input('grado');
        $ames[1] = 'Enero';
        $ames[2] = 'Febrero';
        $ames[3] = 'Marzo';
        $ames[4] = 'Abril';
        $ames[5] = 'Mayo';
        $ames[6] = 'Junio';
        $ames[7] = 'Julio';
        $ames[8] = 'Agosto';
        $ames[9] = 'Septiembre';
        $ames[10] = 'Octubre';
        $ames[11] = 'Noviembre';
        $ames[12] = 'Diciembre';
        $agrado[0] = 'NO ASIGNADO';
        $agrado[1] = 'PRIMERO';
        $agrado[2] = 'SEGUNDO';
        $agrado[3] = 'TERCERO';
        $agrado[4] = 'MAESTROS INSTALADOS';
        $mes = (int) Session::get($this->controlador . '.mes');
        $gest = '/' . Session::get($this->controlador . '.gestion');
        $fechat = Session::get($this->controlador . '.gestion') . '-' . Session::get($this->controlador . '.mes') . '-' . Session::get($this->controlador . '.ftenida');
        $dtenida = Asistencias_model::getDataAsisSimple(Session::get($this->controlador . '.taller'), $fechat);
        $ftenida = Session::get($this->controlador . '.gestion') . '-' . Session::get($this->controlador . '.mes') . '-' . Session::get($this->controlador . '.ftenida');
        $datoslog = Asistencias_model::getDatosLogia(Session::get($this->controlador . '.taller'));
        $ritosdata = Ritos_model::find($datoslog->rito);
        if ($plagrado == 3) {
            $nacta = $dtenida->numeroActa3;
            if ($datoslog->rito == 3) {
                $data['honor'] = "<h2></h2>";
            } else {
                $data['honor'] = "<h2>A:. L:. G:. D:. A:.</h2>
                       <h2>S:. F:. U:.</h2>";
            }
        } elseif ($plagrado == 2) {
            $nacta = $dtenida->numeroActa2;
            if ($datoslog->rito == 3) {
                $data['honor'] = "<h2></h2>";
            } else {
                $data['honor'] = "<h2>A:. L:. G:. D:. G:. G:. D:. U:.</h2>
                       <h2>S:. F:. U:.</h2>";
            }
        } else {
            $nacta = $dtenida->numeroActa1;
            if ($datoslog->rito == 3) {
                $data['honor'] = "<h2></h2>";
            } else {
                $data['honor'] = "<h2>A:. L:. G:. D:. G:. A:. D:. U:.</h2>
                       <h2>S:. F:. U:.</h2>";
            }
        }
        $data['dtenida'] = 'ACTA No.: <b>' . $nacta . $gest . '</b><br>GRADO: <b>' . $agrado[$plagrado] . '</b><br>FECHA DE TENIDA: <b>' . Session::get($this->controlador . '.ftenida') . '/' . $ames[$mes] . '/' . Session::get($this->controlador . '.gestion') . '</b><br>FECHA APROBACION DE ACTA: ___ - ____________ - <b>' . Session::get($this->controlador . '.gestion') . '</b>';

        if ($datoslog->rito == 1) {
            $data['exvm'] = 'Ex VENERABLES MAESTROS';
        } else {
            $data['exvm'] = 'PAST MASTERS';
        }
        $data['taller'] = $datoslog->nombreCompleto;
        $data['ritotexto'] = ' en el <b>' . $ritosdata->textoPlanillas . '</b>';
        $data['oficiales'] = $this->listOficiales(Session::get($this->controlador . '.gestion'), Session::get($this->controlador . '.taller'), $ftenida, $plagrado, $datoslog->rito);
        $data['asistencia'] = $this->listAsistencia(Session::get($this->controlador . '.gestion'), Session::get($this->controlador . '.taller'), $ftenida, $plagrado);
        $visita = '';
        if (strlen($dtenida->visitadores) > 2 && $plagrado < 2) {
            $visita .= $dtenida->visitadores . ' ';
        }
        if (strlen($dtenida->visitadores2) > 2 && $plagrado < 3) {
            $visita .= $dtenida->visitadores2 . ' ';
        }
        if (strlen($dtenida->visitadores3) > 2) {
            $visita .= $dtenida->visitadores3 . ' ';
        }
        if (strlen($dtenida->visitadores4) > 2) {
            $visita .= $dtenida->visitadores4 . ' ';
        }
        $visita .= ' ' . Asistenciaextra_model::getListaVisitas($fechat, Session::get($this->controlador . '.taller'), $plagrado);
        if (strlen($visita) > 5) {
        } else {
            $visita = 'Ninguno.';
        }
        $data['visitadores'] = $visita;
        $data['nvalle'] = $datoslog->valle;
        if (strlen($datoslog->logo) > 5) {
            $data['logo'] = $datoslog->logo;
        } else {
            $data['logo'] = 'glb-150.png';
        }
        $pltenida = Session::get($this->controlador . '.ftenida') . '-' . Session::get($this->controlador . '.mes') . '-' . Session::get($this->controlador . '.gestion');
        $plafe = \DateTime::createFromFormat('d-m-Y', $pltenida)->format('dmY');
        $nomefile = 'GLSP-' . Session::get($this->controlador . '.taller') . '-' . $plagrado . '-' . $nacta . '-' . $plafe;
        $pdf = PDF::loadView('pdfs.pdf_planilla_asis', $data);
        $pdf->set_paper('letter', 'portrait');
        return $pdf->download($nomefile . '.pdf');
    }

    private function &listAsistencia($ges, $log, $ften, $grado = 1)
    {
        $past = '';
        $maes = '';
        $comp = '';
        $apre = '';
        $lista = Asistencias_model::getAsisMiembros($ges, $log, $ften, $grado);
        foreach ($lista as $ver) {
            if ($ver->idOficialPT > 0) // si esta comooficial pt antes
            {
            } else {
                if ($ver->Grado == 4) {
                    $past .= ucwords(mb_strtolower($ver->NombreCompleto)) . '<br>';
                } elseif ($ver->Grado == 3) {
                    $maes .= ucwords(mb_strtolower($ver->NombreCompleto)) . '<br>';
                } elseif ($ver->Grado == 2) {
                    $comp .= ucwords(mb_strtolower($ver->NombreCompleto)) . '<br>';
                } else {
                    $apre .= ucwords(mb_strtolower($ver->NombreCompleto)) . '<br>';
                }
            }
        }
        $ret = '<tr style="font-size:12px;"><td valign="top">' . $past . '</td><td valign="top">' . $maes . '</td><td valign="top">' . $comp . '</td><td valign="top">' . $apre . '</td></tr>';
        return $ret;
    }
    private function &listOficiales($ges, $log, $ften, $gradot = 3, $rito = 1)
    {
        if ($rito == 3) //emulacion
        {
            $grado[4] = 'V.H.';
            $grado[3] = 'H.M.';
            $grado[2] = 'H.C.';
            $grado[1] = 'H.A.';
        } else {
            $grado[4] = 'R:.H:.';
            $grado[3] = 'H:.M:.';
            $grado[2] = 'H:.C:.';
            $grado[1] = 'H:.A:.';
        }
        $cc = 0;
        $col1a = '';
        $col2b = '';
        $lista = Asistencias_model::getAsisOficiales($ges, $log, $ften, $rito);
        foreach ($lista as $ver) {
            if ($ver->Grado >= $gradot) {
                $cc++;
                if ($cc > 8) {
                    $col2b .= '<tr><td valign="top">' . $ver->oficial . '</td><td valign="top">' . $grado[$ver->Grado] . ' ' . ucwords(mb_strtolower($ver->NombreCompleto)) . '</td></tr>';
                } else {
                    $col1a .= '<tr><td valign="top">' . $ver->oficial . '</td><td valign="top">' . $grado[$ver->Grado] . ' ' . ucwords(mb_strtolower($ver->NombreCompleto)) . '</td></tr>';
                }
            }
        }
        $ret = '<tr style="font-size:12px;"><td width="50%"><table width="100%" border="0" cellspacing="0" cellpadding="0">' . $col1a . '</table></td><td><table width="100%" border="0" cellspacing="0" cellpadding="0">' . $col2b . '</table></td></tr>';
        return $ret;
    }
    private function getMeses()
    {
        $monthArray[1] = "Enero";
        $monthArray[2] = "Febrero";
        $monthArray[3] = "Marzo";
        $monthArray[4] = "Abril";
        $monthArray[5] = "Mayo";
        $monthArray[6] = "Junio";
        $monthArray[7] = "Julio";
        $monthArray[8] = "Agosto";
        $monthArray[9] = "Septiembre";
        $monthArray[10] = "Octubre";
        $monthArray[11] = "Noviembre";
        $monthArray[12] = "Diciembre";
        return $monthArray;
    }
}
