<?php

namespace App\Http\Controllers;

use App\Models\admin\Logias_model;
use App\Models\comap\Kardex_model;
use App\Models\admin\Membrecia_model;
use App\Traits\DatagridTrait;
use App\Models\admin\Miembrosestado_model;
use Illuminate\Http\Request;
use PDF;
use Session;

class Kardex extends Controller
{
    use DatagridTrait; //trait de funciones de datagrid
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $idmod = '016';
    public $controlador = 'kardex';
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
        Session::put($this->controlador . '.estado', 4);
        $data['_mid'] = $this->idmod; //---
        $data['_controller'] = $this->controlador;
        $data['logias'] = Logias_model::getlogiasArray($this->oriente, $this->valle, $this->taller);
        $data['estados'] = Miembrosestado_model::getEstadosArray();
        /*     varibles de pagina* */
        // $data['jscripts'] = 'kardex.js'; //---
        return view('comap.kardex', $data);
    }
    public function get_datos(Request $request)
    {
        $page = $request->input('page', 1);
        $rows = $request->input('rows', 20);
        $val = self::validar('idValle', Session::get($this->controlador . '.valle'));
        $log = self::validar('idLogia', Session::get($this->controlador . '.taller'));
        $or = self::validar('idOriente', Session::get($this->controlador . '.oriente'));
        $qry = Membrecia_model::getItems($page, $rows, Session::get($this->controlador . '.palabra', ''), $or, $val, $log, Session::get($this->controlador . '.grado', 0), Session::get($this->controlador . '.estado', 4));
        $total = Membrecia_model::getNumItems(Session::get($this->controlador . '.palabra', ''), $or, $val, $log, Session::get($this->controlador . '.grado', 0), Session::get($this->controlador . '.estado', 4));

        // $qry = Kardex_model::getItems($page, $rows, Session::get($this->controlador . '.palabra', ''), $val, $log, Session::get($this->controlador . '.grado', 0), Session::get($this->controlador . '.estado', 0));
        // $total = Kardex_model::getNumItems(Session::get($this->controlador . '.palabra', ''), $val, $log, Session::get($this->controlador . '.grado', 0), Session::get($this->controlador . '.estado', 0));
        $qry2 = (object) ['total' => $total, 'rows' => $qry];
        return response()->json($qry2);
    }
    public function ver_kardex(Request $request)
    {
        $id = $request->input('id');
        $data['pagina'] = self::verKardex($id);
        return response()->view('pagina', $data);
    }
    private function &verKardex($id)
    {
        $mas = Kardex_model::getMiembro($id);
        $list = array("$mas->LogiaAfiliada", "$mas->LogiaIniciacion", "$mas->LogiaAumento", "$mas->LogiaExaltacion", "$mas->LogiaHonorario", "$mas->LogiaAdMeritum", "$mas->LogiaAdVitam");
        $talleres = Kardex_model::getTalleres($list);
        if (strlen($mas->foto) > 10) {
            $ff = $mas->foto;
        } else {
            $ff = 'foto.jpg';
        }
        if ($mas->Estado == 0) {
            $color = 'red';
        } else {
            $color = '#0066CC';
        }

        $ver = '<div id="mytab" style="padding:5px;">
        <table width="100%" border="0"><tbody>
      <tr><td rowspan="6"><img src="media/miembros/' . $ff . '" height="200"></td>
        <td colspan="2" bgcolor="#0066CC"><div align="center" class="textoKardex">Apellido Paterno</div></td>
        <td colspan="2" bgcolor="#0066CC"><div align="center" class="textoKardex">Apellido Materno</div></td>
        <td colspan="2" bgcolor="#0066CC"><div align="center" class="textoKardex">Nombre(s)</div></td>
      </tr>
      <tr><td colspan="2">' . $mas->Paterno . '</td><td colspan="2">' . $mas->Materno . '</td><td colspan="2">' . $mas->Nombres . '</td></tr>
      <tr>
        <td bgcolor="#0066CC"><div align="center" class="textoKardex">Pais</div></td>
        <td bgcolor="#0066CC"><div align="center" class="textoKardex">Lugar de Nacimiento</div></td>
        <td bgcolor="#0066CC"><div align="center" class="textoKardex">Fecha de Nacimiento</div></td>
        <td bgcolor="#0066CC"><div align="center" class="textoKardex">Edad</div></td>
        <td bgcolor="#0066CC"><div align="center" class="textoKardex">Estado Civil</div></td>
        <td bgcolor="#0066CC"><div align="center" class="textoKardex">C.I.</div></td>
      </tr>
      <tr>
        <td><div align="center">' . $mas->Pais . '</div></td>
        <td><div align="center">' . $mas->LugarNacimiento . '</div></td>
        <td><div align="center">' . $mas->FechaNacimiento . '</div></td>
        <td><div align="center">' . $this->calculaedad($mas->FechaNacimiento) . ' (a&ntilde;os) </div></td>
        <td><div align="center">' . $mas->EstadoCivil . '</div></td>
        <td><div align="center">' . $mas->CI . '</div></td>
      </tr>
      <tr>
        <td colspan="2" bgcolor="#0066CC"><div align="center" class="textoKardex">Profesion u Oficio </div></td>
        <td colspan="2" bgcolor="#0066CC"><div align="center" class="textoKardex">Direcci&oacute;n Domicilio</div></td>
        <td colspan="2" bgcolor="#0066CC"><div align="center" class="textoKardex">Telf. Domicilio</div></td>
      </tr>
      <tr>
        <td colspan="2"><div align="center">' . $mas->ProfesionOficio . '</div></td>
        <td colspan="2"><div align="center">' . $mas->Domicilio . '</div></td>
        <td colspan="2"><div align="center">' . $mas->TelefonoDomicilio . '</div></td>
      </tr>
      <tr bgcolor="#0066CC">
        <td colspan="3"><div align="center" class="textoKardex">Oficina donde trabaja</div></td>
        <td><div align="center" class="textoKardex">Telf. Oficina </div></td>
        <td><div align="center" class="textoKardex">Celular</div></td>
        <td colspan="2"><div align="center" class="textoKardex">Email</div></td>
      </tr>
      <tr>
        <td colspan="3"><div align="center">' . $mas->Trabajo . '</div></td>
        <td><div align="center">' . $mas->TelefonoOficina . '</div></td>
        <td><div align="center">' . $mas->Celular . '</div></td>
        <td colspan="2"><div align="center">' . $mas->email . '</div></td>
      </tr>
      <tr>
        <td colspan="7" bgcolor="' . $color . '"><div align="center"><strong>DATOS MAS&Oacute;NICOS (' . strtoupper($mas->Estadotxt) . ')</strong></div></td>
      </tr>
      <tr bgcolor="#0066CC">
        <td colspan="3"><div align="center" class="textoKardex">Logia Actual</div></td>
        <td colspan="2"><div align="center" class="textoKardex">Valle</div></td>
        <td><div align="center" class="textoKardex">Grado</div></td>
        <td><div align="center" class="textoKardex">Miembro</div></td>
      </tr>
      <tr>
        <td colspan="3"><div align="center">R:.L:.S:. ' . $mas->logia . ' Nro. ' . $mas->LogiaActual . '</div></td>
        <td colspan="2"><div align="center">' . $mas->valle . '</div></td>
        <td><div align="center">' . $mas->GradoActual . '</div></td>
        <td><div align="center">' . $mas->Miembro . '</div></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td bgcolor="#0066CC"><div align="center" class="textoKardex">Fecha</div></td>
        <td colspan="3" bgcolor="#0066CC"><div align="center" class="textoKardex">Logia</div></td>
        <td bgcolor="#0066CC"><div align="center" class="textoKardex">Fecha Certificado </div></td>
        <td bgcolor="#0066CC"><div align="center" class="textoKardex">Decreto/Certif.</div></td>
      </tr>
      <tr>
        <td>Iniciaci&oacute;n</td>
        <td><div align="center">' . $mas->FechaIniciacion . '</div></td>
        <td colspan="3"><div align="center">' . $talleres[(int) $mas->LogiaIniciacion] . '</div></td>
        <td><div align="center">--</div></td>
        <td><div align="center">-</div></td>
      </tr>
      <tr>
        <td>Aumento de Salario </td>
        <td><div align="center">' . $mas->FechaAumentoSalario . '</div></td>
        <td colspan="3"><div align="center">' . $talleres[(int) $mas->LogiaAumento] . '</div></td>
        <td><div align="center">--</div></td>
        <td><div align="center">-</div></td>
      </tr>
      <tr>
        <td>Exaltacion</td>
        <td><div align="center">' . $mas->FechaExaltacion . '</div></td>
        <td colspan="3"><div align="center">' . $talleres[(int) $mas->LogiaExaltacion] . '</div></td>
        <td><div align="center">--</div></td>
        <td><div align="center">-</div></td>
      </tr>
      <tr>
        <td>Honorario</td>
        <td><div align="center">' . $mas->FechaHonorario . '</div></td>
        <td colspan="3"><div align="center">' . $talleres[(int) $mas->LogiaHonorario] . '</div></td>
        <td><div align="center">--</div></td>
        <td><div align="center">' . $mas->DecretoHonorario . '</div></td>
      </tr>
      <tr>
        <td>Ad-Meritum</td>
        <td><div align="center">' . $mas->FechaAdMeritum . '</div></td>
        <td colspan="3"><div align="center">' . $talleres[(int) $mas->LogiaAdMeritum] . '</div></td>
        <td><div align="center">--</div></td>
        <td><div align="center">' . $mas->DecretoAdMeritum . '</div></td>
      </tr>
      <tr>
        <td>Ad-Vitam</td>
        <td><div align="center">' . $mas->FechaAdVitam . '</div></td>
        <td colspan="3"><div align="center">' . $talleres[(int) $mas->LogiaAdVitam] . '</div></td>
        <td><div align="center">--</div></td>
        <td><div align="center">' . $mas->DecretoAdVitam . '</div></td>
      </tr>';
        $ver .= '<tr bgcolor="' . $color . '"><td colspan="7"><div align="center" class="textoKardex">OBSERVACIONES</div></td></tr>';
        /*********  Observaciones  *********/
        if ($mas->LogiaAfiliada > 0) {
            $ver .= '<tr><td colspan="7"> Afiliado a ' . $talleres[(int) $mas->LogiaAfiliada] . '<br>' . $mas->observaciones . '</td></tr>';
        }
        if (strlen($mas->observaciones) > 2) {
            $ver .= '<tr><td colspan="7">' . $mas->observaciones . '</td><tr>';
        }
        if ($mas->Estado == 0) {
            $ver .= '<tr><td colspan="7">' . $mas->Estadotxt . ' (Libro Negro)</td><tr>';
        } else {
            $ver .= '<tr><td colspan="7">' . $mas->Estadotxt . '</td><tr>';
        }

        /***** fin obsevaciones *****/
        $ver .= '<tr bgcolor="#0066CC"><td colspan="7"><div align="center" class="textoKardex">Ultimo mes pagado : ' . strtoupper($mas->ultimoPago) . ' </div></td></tr>';
        $cargos = Kardex_model::getCargos($id);
        if (!empty($cargos)) {
            $ver .= '    <tr>
        <td colspan="7"><div align="center"><strong>CARGOS REALIZADOS EN LOGIA</strong></div></td>
      </tr><tr>
      <td bgcolor="#0066CC"><div align="center" class="textoKardex">Gestion</div></td>
        <td colspan="3" bgcolor="#0066CC"><div align="center" class="textoKardex">Cargo</div></td>
        <td colspan="3" bgcolor="#0066CC"><div align="center" class="textoKardex">Logia</div></td>
        </tr>';
            foreach ($cargos as $dta) {
                $ver .= '<tr><td>' . $dta->gestion . '</td>
        <td colspan="3">' . $dta->oficial . '</td>
        <td colspan="3">R:.L:.S:. ' . $dta->logia . ' Nro. ' . $dta->idlogia . '</td></tr>';
            }
        }
        //----  cargos glb
        $cargosg = Kardex_model::getCargosGLB($id);
        if (!empty($cargosg)) {
            $ver .= '<tr><td colspan="7"><div align="center"><strong>CARGOS REALIZADOS EN LA GLB</strong></div></td></tr><tr>
      <td bgcolor="#0066CC"><div align="center" class="textoKardex">Gestion</div></td>
        <td colspan="3" bgcolor="#0066CC"><div align="center" class="textoKardex">Cargo</div></td>
        <td colspan="3" bgcolor="#0066CC"><div align="center" class="textoKardex">Comisión</div></td>
        </tr>';
            foreach ($cargosg as $dta) {
                $ver .= '<tr><td>' . $dta->gestiontxt . '</td>
        <td colspan="3">' . $dta->oficial . '</td>
        <td colspan="3">' . $dta->lugar . ' - ' . $dta->descripcion . '</td></tr>';
            }
        }
        $ver .= '</tbody>
        </table>
      </div>';
        return $ver;
    }
    public function print_kardex(Request $request)
    {
        $id = $request->input('id');
        $mas = Kardex_model::getMiembro($id);
        if (strlen($mas->foto) > 10) {
            $ff = $mas->foto;
        } else {
            $ff = 'foto.jpg';
        }
        $list = array("$mas->LogiaAfiliada", "$mas->LogiaIniciacion", "$mas->LogiaAumento", "$mas->LogiaExaltacion", "$mas->LogiaHonorario", "$mas->LogiaAdMeritum", "$mas->LogiaAdVitam");
        $talleres = Kardex_model::getTalleres($list);
        if ($mas->Estado == 0) {
            $color = 'red';
        } else {
            $color = '#F4F4F4';
        }
        $data['pagina'] = '<div id="mytab" style="padding:5px;">
      <table width="100%" border="0"><tbody>
    <tr><td rowspan="6" style="padding:0;" width="100"><img src="media/miembros/' . $ff . '"/ width="150"></td>
      <td colspan="2" bgcolor="#ABABAB"><div align="center">Apellido Paterno</div></td>
      <td colspan="2" bgcolor="#ABABAB"><div align="center">Apellido Materno</div></td>
      <td colspan="2" bgcolor="#ABABAB"><div align="center">Nombre(s)</div></td>
    </tr>
    <tr>
      <td colspan="2">' . $mas->Paterno . '</td>
      <td colspan="2">' . $mas->Materno . '</td>
      <td colspan="2">' . $mas->Nombres . '</td>
    </tr>
    <tr>
      <td bgcolor="#ABABAB"><div align="center">Pais</div></td>
      <td bgcolor="#ABABAB"><div align="center">Lugar de Nacimiento</div></td>
      <td bgcolor="#ABABAB"><div align="center">Fecha de Nacimiento</div></td>
      <td bgcolor="#ABABAB"><div align="center">Edad</div></td>
      <td bgcolor="#ABABAB"><div align="center">Estado Civil</div></td>
      <td bgcolor="#ABABAB"><div align="center">C.I.</div></td>
    </tr>
    <tr>
      <td><div align="center">' . $mas->Pais . '</div></td>
      <td><div align="center">' . $mas->LugarNacimiento . '</div></td>
      <td><div align="center">' . $mas->FechaNacimiento . '</div></td>
      <td><div align="center">' . $this->calculaedad($mas->FechaNacimiento) . ' (a&ntilde;os) </div></td>
      <td><div align="center">' . $mas->EstadoCivil . '</div></td>
      <td><div align="center">' . $mas->CI . '</div></td>
    </tr>
    <tr>
      <td colspan="2" bgcolor="#ABABAB"><div align="center">Profesion u Oficio </div></td>
      <td colspan="2" bgcolor="#ABABAB"><div align="center">Direcci&oacute;n Domicilio</div></td>
      <td colspan="2" bgcolor="#ABABAB"><div align="center">Telf. Domicilio</div></td>
    </tr>
    <tr>
      <td colspan="2"><div align="center">' . $mas->ProfesionOficio . '</div></td>
      <td colspan="2"><div align="center">' . $mas->Domicilio . '</div></td>
      <td colspan="2"><div align="center">' . $mas->TelefonoDomicilio . '</div></td>
    </tr>
    <tr bgcolor="#ABABAB">
      <td colspan="3"><div align="center">Oficina donde trabaja</div></td>
      <td><div align="center">Telf. Oficina </div></td>
      <td><div align="center">Celular</div></td>
      <td colspan="2"><div align="center">Email</div></td>
    </tr>
    <tr>
      <td colspan="3"><div align="center">' . $mas->Trabajo . '</div></td>
      <td><div align="center">--</div></td>
      <td><div align="center">' . $mas->Celular . '</div></td>
      <td colspan="2"><div align="center">' . $mas->email . '</div></td>
    </tr>
    <tr>
      <td colspan="7" bgcolor="' . $color . '"><div align="center"><strong>DATOS MAS&Oacute;NICOS (' . strtoupper($mas->Estadotxt) . ')</strong></div></td>
    </tr>
    <tr bgcolor="#ABABAB">
      <td colspan="3"><div align="center">Logia Actual</div></td>
      <td colspan="2"><div align="center">Valle</div></td>
      <td><div align="center">Grado</div></td>
      <td><div align="center">Miembro</div></td>
    </tr>
    <tr>
      <td colspan="3"><div align="center">R:.L:.S:. ' . $mas->logia . ' Nro. ' . $mas->LogiaActual . '</div></td>
      <td colspan="2"><div align="center">' . $mas->valle . '</div></td>
      <td><div align="center">' . $mas->GradoActual . '</div></td>
      <td><div align="center">' . $mas->Miembro . '</div></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td bgcolor="#ABABAB"><div align="center">Fecha</div></td>
      <td colspan="2" bgcolor="#ABABAB"><div align="center">Logia</div></td>
      <td bgcolor="#ABABAB"><div align="center">Certificado</div></td>
      <td bgcolor="#ABABAB"><div align="center">Fecha Certificado </div></td>
      <td bgcolor="#ABABAB"><div align="center">Decreto</div></td>
    </tr>
    <tr>
      <td bgcolor="#ABABAB">Iniciaci&oacute;n</td>
      <td><div align="center">' . $mas->FechaIniciacion . '</div></td>
      <td colspan="2"><div align="center">' . $talleres[(int) $mas->LogiaIniciacion] . '</div></td>
      <td><div align="center">--</div></td>
      <td><div align="center">--</div></td>
      <td><div align="center">-</div></td>
    </tr>
    <tr>
      <td bgcolor="#ABABAB">Aumento de Salario </td>
      <td><div align="center">' . $mas->FechaAumentoSalario . '</div></td>
      <td colspan="2"><div align="center">' . $talleres[(int) $mas->LogiaAumento] . '</div></td>
      <td><div align="center">--</div></td>
      <td><div align="center">--</div></td>
      <td><div align="center">-</div></td>
    </tr>
    <tr>
      <td bgcolor="#ABABAB">Exaltacion</td>
      <td><div align="center">' . $mas->FechaExaltacion . '</div></td>
      <td colspan="2"><div align="center">' . $talleres[(int) $mas->LogiaExaltacion] . '</div></td>
      <td><div align="center">--</div></td>
      <td><div align="center">--</div></td>
      <td><div align="center">-</div></td>
    </tr>
    <tr>
      <td bgcolor="#ABABAB">Honorario</td>
      <td><div align="center">' . $mas->FechaHonorario . '</div></td>
      <td colspan="2"><div align="center">' . $talleres[(int) $mas->LogiaHonorario] . '</div></td>
      <td><div align="center">--</div></td>
      <td><div align="center">--</div></td>
      <td><div align="center">' . $mas->DecretoHonorario . '</div></td>
    </tr>
    <tr>
      <td bgcolor="#ABABAB">Ad-Meritum</td>
      <td><div align="center">' . $mas->FechaAdMeritum . '</div></td>
      <td colspan="2"><div align="center">' . $talleres[(int) $mas->LogiaAdMeritum] . '</div></td>
      <td><div align="center">--</div></td>
      <td><div align="center">--</div></td>
      <td><div align="center">' . $mas->DecretoAdMeritum . '</div></td>
    </tr>
    <tr>
      <td bgcolor="#ABABAB">Ad-Vitam</td>
      <td><div align="center">' . $mas->FechaAdVitam . '</div></td>
      <td colspan="2"><div align="center">' . $talleres[(int) $mas->LogiaAdVitam] . '</div></td>
      <td><div align="center">--</div></td>
      <td><div align="center">--</div></td>
      <td><div align="center">' . $mas->DecretoAdVitam . '</div></td>
    </tr>
    <tr bgcolor="' . $color . '">
      <td bgcolor="#ABABAB" colspan="7"><div align="center">OBSERVACIONES</div></td>
    </tr>';
        if ($mas->LogiaAfiliada > 0) {
            $data['pagina'] .= '<tr><td colspan="7"> Afiliado a ' . $talleres[(int) $mas->LogiaAfiliada] . '</td></tr>';
        }
        if (strlen($mas->observaciones) > 1) {
            $data['pagina'] .= '<tr><td colspan="7">' . $mas->observaciones . '</td><tr>';
        }
        if ($mas->Estado == 0) {
            $data['pagina'] .= '<tr><td colspan="7">' . $mas->Estadotxt . ' (Libro Negro)</td><tr>';
        } else {
            $data['pagina'] .= '<tr><td colspan="7">' . $mas->Estadotxt . '</td><tr>';
        }
        $data['pagina'] .= '<tr bgcolor="#F4F4F4"><td colspan="7"><div align="center">Ultimo mes pagado : ' . strtoupper($mas->ultimoPago) . ' </div></td></tr>';
        $cargos = Kardex_model::getCargos($id);
        if (!empty($cargos)) {
            $data['pagina'] .= '<tr><td colspan="7"><div align="center"><strong>CARGOS REALIZADOS EN LOGIA</strong></div></td>
    </tr><tr>
    <td bgcolor="#ABABAB"><div align="center">Gestion</div></td>
      <td colspan="3" bgcolor="#ABABAB"><div align="center">Cargo</div></td>
      <td colspan="3" bgcolor="#ABABAB"><div align="center">Logia</div></td>
      </tr>';
            foreach ($cargos as $dta) {
                $data['pagina'] .= '<tr><td>' . $dta->gestion . '</td>
      <td colspan="3">' . $dta->oficial . '</td>
      <td colspan="3">R:.L:.S:. ' . $dta->logia . ' Nro. ' . $dta->idlogia . '</td></tr>';
            }
        }
        ///--------- cargos glb
        $cargosg = Kardex_model::getCargosGLB($id);
        if (!empty($cargosg)) {
            $data['pagina'] .= '<tr><td colspan="7"><div align="center"><strong>CARGOS REALIZADOS EN LA GLB</strong></div></td></tr><tr>
    <td bgcolor="#ABABAB"><div align="center">Gestion</div></td>
      <td colspan="3" bgcolor="#ABABAB"><div align="center">Cargo</div></td>
      <td colspan="3" bgcolor="#ABABAB"><div align="center">Comisión</div></td>
      </tr>';
            foreach ($cargosg as $dta) {
                $data['pagina'] .= '<tr><td>' . $dta->descripcion . '</td>
      <td colspan="3">' . $dta->oficial . '</td>
      <td colspan="3">' . $dta->lugar . ' <!--' . $dta->gestion . '*/--> </td></tr>';
            }
        }
        $data['pagina'] .= '</tbody></table></div>';
        $pdf = PDF::loadView('pdfs.pdf_blanco', $data);
        $pdf->set_paper('letter', 'portrait');
        return $pdf->download('kardex.pdf');
    }
    private function calculaedad($fechanacimiento)
    {
        if (strlen($fechanacimiento) > 2) {
            list($ano, $mes, $dia) = explode("-", $fechanacimiento);
            $ano_diferencia = date("Y") - $ano;
            $mes_diferencia = date("m") - $mes;
            $dia_diferencia = date("d") - $dia;
            if ($dia_diferencia < 0 || $mes_diferencia < 0) {
                $ano_diferencia--;
            }

            return $ano_diferencia;
        } else {
            return 0;
        }
    }
}
