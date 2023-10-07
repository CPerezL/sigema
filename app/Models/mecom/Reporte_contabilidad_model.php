<?php

namespace App\Models\mecom;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reporte_contabilidad_model extends Model
{
    use HasFactory;
    public static function getReporteTramites($desde, $hasta, $tipo, $valle, $logia = 0)
    {
        if (strlen($desde) > 0 || strlen($hasta) > 0) {
            //DB::select("SET lc_time_names = 'es_ES'");
            if ($tipo == 11) {//pagos extra
                $cond = "A.aprobado=1 AND B.tipo=11 ";
                $dd = explode('/', $desde);
                $hh = explode('/', $hasta);
                $desded = "$dd[2]-$dd[1]-$dd[0]";
                $hastad = "$hh[2]-$hh[1]-$hh[0]";
                if ($logia > 0) {
                    $cond .= "AND A.logia = $logia";
                } else {
                    if ($valle > 0) {
                        $cond .= "AND A.valle = $valle";
                    }
                }
                $qry = "SELECT  E.cobro AS tipoTram,A.idFormulario,A.idRegistro, A.fechaModificacion,D.NombreCompleto as nombreCompleto,B.numeroMiembros,A.monto AS montoform,
                case when C.monto>0 then C.monto ELSE A.monto END AS monto,case when C.montoGLB>0 then C.montoGLB ELSE A.montoGLB END as montoGLB,case when C.montoGDR>0 then C.montoGDR ELSE A.montoGDR END AS montoGDR,
                case when C.montoCOMAP>0 then C.montoCOMAP ELSE A.montoCOMAP END AS montoCOMAP,case when C.montoTaller>0 then C.montoTaller ELSE A.montoTaller END AS montoTaller
                , CONCAT(F.logia,' Nº ',B.taller) AS logiaName,G.valle,
                CASE D.Grado WHEN 0 THEN 'Ninguno' WHEN 1 THEN 'Aprendiz' WHEN 2 THEN 'Compañero' WHEN 3 THEN 'Maestro' WHEN 4 THEN 'V.M./Ex V.M.' END AS GradoActual
                FROM sgm_mecom_pagos_registros A JOIN sgm_mecom_formularios B ON A.idFormulario=B.idFormulario
                JOIN sgm_mecom_registros C ON B.idFormulario=C.idFormulario
                JOIN sgm_pagos_montos E ON E.idMonto=A.proceso
                LEFT JOIN sgm_miembros D ON C.idMiembro=D.id
                JOIN sgm_logias F ON  F.numero=A.logia
                JOIN sgm_valles G ON G.idValle=A.valle
                WHERE $cond  AND B.fechaAprobacion BETWEEN '$desded 00:00:01' AND '$hastad 23:59:59' ORDER BY fechaModificacion ASC,idFormulario ASC";
                $results = DB::select($qry);
                $mon = 0;
                $monglb = 0;
                $mongdr = 0;
                $montall = 0;
                $moncomap = 0;
                foreach ($results as $val) {
                    $mon += $val->monto;
                    $monglb += $val->montoGLB;
                    $mongdr += $val->montoGDR;
                    $montall += $val->montoTaller;
                    $moncomap += $val->montoCOMAP;
                }
                $a = array("tipoTram" => "", "idFormulario" => 0, "idRegistro" => "", "fechaModificacion" => "", "numeroMiembros" => 0, "montoform" => 0, "monto" => $mon, "montoGLB" => $monglb, "montoGDR" => $mongdr, "montoCOMAP" => $moncomap, "montoTaller" => $montall, "nombreCompleto" => "SUMAS TOTALES", "logiaName" => "", "valle" => "");
                array_push($results, $a);
            } elseif ($tipo == 12) {//iniciaciones
                $cond = "A.aprobado=1 AND B.tipo=12 ";
                $dd = explode('/', $desde);
                $hh = explode('/', $hasta);
                $desded = "$dd[2]-$dd[1]-$dd[0]";
                $hastad = "$hh[2]-$hh[1]-$hh[0]";
                if ($logia > 0) {
                    $cond .= "AND B.taller = $logia";
                } else {
                    if ($valle > 0) {
                        $cond .= "AND D.valle = $valle";
                    }
                }
                $qry = "SELECT 1 as orden,'Iniciación' tipoTram, A.idFormulario,A.idRegistro, A.fechaModificacion,B.numeroMiembros,A.monto AS montoform,
            case when C.monto>0 then C.monto ELSE A.monto END AS monto,case when C.montoGLB>0 then C.montoGLB ELSE A.montoGLB END as montoGLB,case when C.montoGDR>0 then C.montoGDR ELSE A.montoGDR END AS montoGDR,
            case when C.montoCOMAP>0 then C.montoCOMAP ELSE A.montoCOMAP END AS montoCOMAP,case when C.montoTaller>0 then C.montoTaller ELSE A.montoTaller END AS montoTaller,
            B.taller,D.logiaName,concat(D.apPaterno,' ',D.apMaterno,' ',D.nombres) as nombreCompleto,E.valle,'Profano' as GradoActual
            FROM sgm_mecom_pagos_registros A JOIN sgm_mecom_formularios B ON A.idFormulario=B.idFormulario
            JOIN sgm_mecom_registros C ON B.idFormulario=C.idFormulario JOIN sgm_tramites_iniciacion D ON A.idMiembro=D.idTramite
            JOIN sgm_valles E ON D.valle=E.idValle
            WHERE $cond AND B.fechaAprobacion BETWEEN '$desded 00:00:01' AND '$hastad 23:59:59'
            UNION ALL
            SELECT 2 AS orden,'' tipoTram,'' idFormulario,'' idRegistro, '' fechaModificacion,'' numeroMiembros, SUM(A.monto) AS montoform,
SUM(case when C.monto>0 then C.monto ELSE A.monto END) AS monto,SUM(case when C.montoGLB>0 then C.montoGLB ELSE A.montoGLB END) as montoGLB,SUM(case when C.montoGDR>0 then C.montoGDR ELSE A.montoGDR END) AS montoGDR,
SUM(case when C.montoCOMAP>0 then C.montoCOMAP ELSE A.montoCOMAP END) AS montoCOMAP,SUM(case when C.montoTaller>0 then C.montoTaller ELSE A.montoTaller END) AS montoTaller,
'' taller, '' logiaName, 'SUMAS TOTALES' nombreCompleto, '' valle,'' GradoActual
            FROM sgm_mecom_pagos_registros A JOIN sgm_mecom_formularios B ON A.idFormulario=B.idFormulario
            JOIN sgm_mecom_registros C ON B.idFormulario=C.idFormulario JOIN sgm_tramites_iniciacion D ON A.idMiembro=D.idTramite
            WHERE $cond AND B.fechaAprobacion BETWEEN '$desded 00:00:01' AND '$hastad 23:59:59'
            ORDER BY orden ASC,fechaModificacion ASC,idFormulario ASC";
                $results = DB::select($qry);
            } elseif ($tipo == 13) {//aumento
                $cond = "A.aprobado=1 AND B.tipo=13 ";
                $dd = explode('/', $desde);
                $hh = explode('/', $hasta);
                $desded = "$dd[2]-$dd[1]-$dd[0]";
                $hastad = "$hh[2]-$hh[1]-$hh[0]";
                if ($logia > 0) {
                    $cond .= "AND B.taller = $logia";
                } else {
                    if ($valle > 0) {
                        $cond .= "AND B.idValle = $valle";
                    }
                }
                $qry = "SELECT DISTINCT 'Aumento de Salario' as tipoTram,A.idFormulario,A.idRegistro, A.fechaModificacion, B.numeroMiembros,A.monto AS montoform,
                (case when C.monto>0 then C.monto ELSE A.monto END)/B.numeroMiembros AS monto,(case when C.montoGLB>0 then C.montoGLB ELSE A.montoGLB END)/B.numeroMiembros as montoGLB,
                (case when C.montoGDR>0 then C.montoGDR ELSE A.montoGDR END)/B.numeroMiembros AS montoGDR,(case when C.montoCOMAP>0 then C.montoCOMAP ELSE A.montoCOMAP END)/B.numeroMiembros AS montoCOMAP,
                (case when C.montoTaller>0 then C.montoTaller ELSE A.montoTaller END)/B.numeroMiembros AS montoTaller,E.NombreCompleto AS nombreCompleto, CONCAT(F.logia,' Nº ',B.taller) AS logiaName,G.valle,
                'Aprendiz' GradoActual
                FROM sgm_mecom_pagos_registros A JOIN sgm_mecom_formularios B ON A.idFormulario=B.idFormulario JOIN sgm_mecom_registros C ON B.idFormulario=C.idFormulario
                JOIN sgm_tramites_aumento D ON D.depositoGLB=A.idRegistro JOIN sgm_miembros E ON D.idMiembro=E.id JOIN sgm_logias F ON  B.taller=F.numero JOIN sgm_valles G ON B.idValle=G.idValle
                WHERE $cond  AND B.fechaAprobacion BETWEEN '$desded 00:00:01' AND '$hastad 23:59:59' ORDER BY fechaModificacion ASC,idFormulario ASC";
                $results = DB::select($qry);
                $mon = 0;
                $monglb = 0;
                $mongdr = 0;
                $montall = 0;
                $moncomap = 0;
                foreach ($results as $val) {
                    $mon += $val->monto;
                    $monglb += $val->montoGLB;
                    $mongdr += $val->montoGDR;
                    $montall += $val->montoTaller;
                    $moncomap += $val->montoCOMAP;
                }
                $a = array("tipoTram" => "", "idFormulario" => 0, "idRegistro" => "", "fechaModificacion" => "", "numeroMiembros" => 0, "montoform" => 0, "monto" => $mon, "montoGLB" => $monglb, "montoGDR" => $mongdr, "montoCOMAP" => $moncomap, "montoTaller" => $montall, "nombreCompleto" => "SUMAS TOTALES", "logiaName" => "", "valle" => "");
                array_push($results, $a);
            } elseif ($tipo == 14) {//Exaltacion
                $cond = "A.aprobado=1 AND B.tipo=14 ";
                $dd = explode('/', $desde);
                $hh = explode('/', $hasta);
                $desded = "$dd[2]-$dd[1]-$dd[0]";
                $hastad = "$hh[2]-$hh[1]-$hh[0]";
                if ($logia > 0) {
                    $cond .= "AND B.taller = $logia";
                } else {
                    if ($valle > 0) {
                        $cond .= "AND B.idValle = $valle";
                    }
                }
                $qry = "SELECT 'Exaltacion' as tipoTram,A.idFormulario,A.idRegistro, A.fechaModificacion, B.numeroMiembros,A.monto AS montoform,
                (case when C.monto>0 then C.monto ELSE A.monto END)/B.numeroMiembros AS monto,
                (case when C.montoGLB>0 then C.montoGLB ELSE A.montoGLB END)/B.numeroMiembros as montoGLB,
                (case when C.montoGDR>0 then C.montoGDR ELSE A.montoGDR END)/B.numeroMiembros AS montoGDR,
                (case when C.montoCOMAP>0 then C.montoCOMAP ELSE A.montoCOMAP END)/B.numeroMiembros AS montoCOMAP,
                (case when C.montoTaller>0 then C.montoTaller ELSE A.montoTaller END)/B.numeroMiembros AS montoTaller,
                E.NombreCompleto AS nombreCompleto, CONCAT(F.logia,' Nº ',B.taller) AS logiaName,G.valle,'Compañero' GradoActual
                FROM sgm_mecom_pagos_registros A
                JOIN sgm_mecom_formularios B ON A.idFormulario=B.idFormulario
                JOIN sgm_mecom_registros C ON B.idFormulario=C.idFormulario
                JOIN sgm_tramites_exaltacion D ON D.depositoGLB=A.idRegistro
                JOIN sgm_miembros E ON D.idMiembro=E.id
                JOIN sgm_logias F ON  B.taller=F.numero
                JOIN sgm_valles G ON B.idValle=G.idValle
                WHERE $cond  AND B.fechaAprobacion BETWEEN '$desded 00:00:01' AND '$hastad 23:59:59'
                ORDER BY A.fechaModificacion,A.idFormulario,D.idMiembro";
                $results = DB::select($qry);
                $mon = 0;
                $monglb = 0;
                $mongdr = 0;
                $montall = 0;
                $moncomap = 0;
                foreach ($results as $val) {
                    $mon += $val->monto;
                    $monglb += $val->montoGLB;
                    $mongdr += $val->montoGDR;
                    $montall += $val->montoTaller;
                    $moncomap += $val->montoCOMAP;
                }
                $a = array("tipoTram" => "", "idFormulario" => 0, "idRegistro" => "", "fechaModificacion" => "", "numeroMiembros" => 0, "montoform" => 0, "monto" => $mon, "montoGLB" => $monglb, "montoGDR" => $mongdr, "montoCOMAP" => $moncomap, "montoTaller" => $montall, "nombreCompleto" => "SUMAS TOTALES", "logiaName" => "", "valle" => "");
                array_push($results, $a);
            } elseif ($tipo == 15) {//afiliaciones
                $cond = "A.aprobado=1 AND B.tipo=15 ";
                $dd = explode('/', $desde);
                $hh = explode('/', $hasta);
                $desded = "$dd[2]-$dd[1]-$dd[0]";
                $hastad = "$hh[2]-$hh[1]-$hh[0]";
                if ($logia > 0) {
                    $cond .= "AND E.idLogiaNueva = $logia";
                } else {
                    if ($valle > 0) {
                        $cond .= "AND B.idValle = $valle";
                    }
                }
                $qry = "SELECT  DISTINCT A.miembro as tipoTram,A.idFormulario , A.idRegistro, A.fechaModificacion,B.numeroMiembros,A.monto AS montoform,
                case when C.monto>0 then C.monto ELSE A.monto END AS monto,case when C.montoGLB>0 then C.montoGLB ELSE A.montoGLB END as montoGLB,case when C.montoGDR>0 then C.montoGDR ELSE A.montoGDR END AS montoGDR,
                case when C.montoCOMAP>0 then C.montoCOMAP ELSE A.montoCOMAP END AS montoCOMAP,case when C.montoTaller>0 then C.montoTaller ELSE A.montoTaller END AS montoTaller,D.NombreCompleto as nombreCompleto,
                CONCAT(F.logia,' Nº ',B.taller) AS logiaName,G.valle,
                CASE D.Grado WHEN 0 THEN 'Ninguno' WHEN 1 THEN 'Aprendiz' WHEN 2 THEN 'Compañero' WHEN 3 THEN 'Maestro' WHEN 4 THEN 'V.M./Ex V.M.' END AS GradoActual
                FROM sgm_mecom_pagos_registros A JOIN sgm_mecom_formularios B ON A.idFormulario=B.idFormulario JOIN sgm_mecom_registros C ON B.idFormulario=C.idFormulario
                JOIN sgm_afiliaciones E ON E.id=A.idMiembro
                JOIN sgm_miembros D ON E.idMiembro=D.id
                JOIN sgm_logias F ON  F.numero=E.idLogiaNueva
                JOIN sgm_valles G ON G.idValle=F.valle
                WHERE $cond  AND B.fechaAprobacion BETWEEN '$desded 00:00:01' AND '$hastad 23:59:59'
                ORDER BY A.fechaModificacion,A.idFormulario";
                $results = DB::select($qry);
                $mon = 0;
                $monglb = 0;
                $mongdr = 0;
                $montall = 0;
                $moncomap = 0;
                foreach ($results as $val) {
                    $mon += $val->monto;
                    $monglb += $val->montoGLB;
                    $mongdr += $val->montoGDR;
                    $montall += $val->montoTaller;
                    $moncomap += $val->montoCOMAP;
                }
                $a = array("tipoTram" => "", "idFormulario" => 0, "idRegistro" => "", "fechaModificacion" => "", "numeroMiembros" => 0, "montoform" => 0, "monto" => $mon, "montoGLB" => $monglb, "montoGDR" => $mongdr, "montoCOMAP" => $moncomap, "montoTaller" => $montall, "nombreCompleto" => "SUMAS TOTALES", "logiaName" => "", "valle" => "");
                array_push($results, $a);
            } elseif ($tipo == 16) {
                $cond = "A.aprobado=1 AND B.tipo=16 ";
                $dd = explode('/', $desde);
                $hh = explode('/', $hasta);
                $desded = "$dd[2]-$dd[1]-$dd[0]";
                $hastad = "$hh[2]-$hh[1]-$hh[0]";
                if ($logia > 0) {
                    $cond .= "AND A.logia = $logia";
                } else {
                    if ($valle > 0) {
                        $cond .= "AND B.idValle = $valle";
                    }
                }
                $qry = "SELECT  A.miembro as tipoTram,A.idFormulario,A.idRegistro, A.fechaModificacion,D.NombreCompleto as nombreCompleto,C.numeroCuotas, B.numeroMiembros,A.monto AS montoform,
                case when C.monto>0 then C.monto ELSE A.monto END AS monto,case when C.montoGLB>0 then C.montoGLB ELSE A.montoGLB END as montoGLB,case when C.montoGDR>0 then C.montoGDR ELSE A.montoGDR END AS montoGDR,
                case when C.montoCOMAP>0 then C.montoCOMAP ELSE A.montoCOMAP END AS montoCOMAP,case when C.montoTaller>0 then C.montoTaller ELSE A.montoTaller END AS montoTaller,
                CONCAT(F.logia,' Nº ',B.taller) AS logiaName,G.valle,
                CASE D.Grado WHEN 0 THEN 'Ninguno' WHEN 1 THEN 'Aprendiz' WHEN 2 THEN 'Compañero' WHEN 3 THEN 'Maestro' WHEN 4 THEN 'V.M./Ex V.M.' END AS GradoActual
                FROM sgm_mecom_pagos_registros A JOIN sgm_mecom_formularios B ON A.idFormulario=B.idFormulario
                JOIN sgm_mecom_registros C ON B.idFormulario=C.idFormulario
                JOIN sgm_reincorporaciones E ON E.id=A.idMiembro
                JOIN sgm_miembros D ON E.idMiembro=D.id
                JOIN sgm_logias F ON F.numero=A.logia
                JOIN sgm_valles G ON G.idValle=F.valle
                WHERE $cond  AND B.fechaAprobacion BETWEEN '$desded 00:00:01' AND '$hastad 23:59:59'
                ORDER BY A.fechaModificacion,A.idFormulario";
                $results = DB::select($qry);
                $mon = 0;
                $monglb = 0;
                $mongdr = 0;
                $montall = 0;
                $moncomap = 0;
                foreach ($results as $val) {
                    $mon += $val->monto;
                    $monglb += $val->montoGLB;
                    $mongdr += $val->montoGDR;
                    $montall += $val->montoTaller;
                    $moncomap += $val->montoCOMAP;
                }
                $a = array("tipoTram" => "", "idFormulario" => 0, "idRegistro" => "", "fechaModificacion" => "", "numeroMiembros" => 0, "montoform" => 0, "monto" => $mon, "montoGLB" => $monglb, "montoGDR" => $mongdr, "montoCOMAP" => $moncomap, "montoTaller" => $montall, "nombreCompleto" => "SUMAS TOTALES", "logiaName" => "", "valle" => "");
                array_push($results, $a);
            }
            elseif ($tipo == 10) {//obolos
                $cond = "A.aprobado=1 AND B.tipo=10 ";
                $dd = explode('/', $desde);
                $hh = explode('/', $hasta);
                $desded = "$dd[2]-$dd[1]-$dd[0]";
                $hastad = "$hh[2]-$hh[1]-$hh[0]";
                if ($logia > 0) {
                    $cond .= "AND A.logia = $logia";
                } else {
                    if ($valle > 0) {
                        $cond .= "AND A.valle = $valle";
                    }
                }
                $qry = "SELECT  'Pago Obolo QR' AS tipoTram,A.idFormulario, A.idRegistro, B.fechaAprobacion as fechaModificacion,D.NombreCompleto AS nombreCompleto, B.numeroMiembros,A.monto AS montoform,
                case when C.monto>0 then C.monto ELSE A.monto END AS monto,
                case when C.montoGLB>0 then C.montoGLB ELSE A.montoGLB END as montoGLB,
                case when C.montoGDR>0 then C.montoGDR ELSE A.montoGDR END AS montoGDR,
                case when C.montoCOMAP>0 then C.montoCOMAP ELSE A.montoCOMAP END AS montoCOMAP,
                case when C.montoTaller>0 then C.montoTaller ELSE A.montoTaller END AS montoTaller,
                 CONCAT(F.logia,' Nº ',B.taller) AS logiaName,G.valle, concat(DATE_FORMAT(C.ultimoPago,'%Y-%b'),'-',DATE_FORMAT(C.fechaPagoNuevo,'%Y-%b')) AS periodo,
                 CASE D.Grado WHEN 0 THEN 'Ninguno' WHEN 1 THEN 'Aprendiz' WHEN 2 THEN 'Compañero' WHEN 3 THEN 'Maestro' WHEN 4 THEN 'V.M./Ex V.M.' END AS GradoActual
                            FROM sgm_mecom_pagos_registros A JOIN sgm_mecom_formularios B ON A.idFormulario=B.idFormulario
                            JOIN sgm_mecom_registros C ON B.idFormulario=C.idFormulario
                            left JOIN sgm_miembros D ON C.idMiembro=D.id
                            JOIN sgm_logias F ON  F.numero=A.logia
                JOIN sgm_valles G ON G.idValle=F.valle
                            WHERE $cond  AND B.fechaAprobacion BETWEEN '$desded 00:00:01' AND '$hastad 23:59:59'";
                $results = DB::select($qry);
                $mon = 0;
                $monglb = 0;
                $mongdr = 0;
                $montall = 0;
                $moncomap = 0;
                foreach ($results as $val) {
                    $mon += $val->monto;
                    $monglb += $val->montoGLB;
                    $mongdr += $val->montoGDR;
                    $montall += $val->montoTaller;
                    $moncomap += $val->montoCOMAP;
                }
                $a = array("tipoTram" => "", "idFormulario" => 0, "idRegistro" => "", "fechaModificacion" => "", "numeroMiembros" => 0, "montoform" => 0, "monto" => $mon, "montoGLB" => $monglb, "montoGDR" => $mongdr, "montoCOMAP" => $moncomap, "montoTaller" => $montall, "nombreCompleto" => "SUMAS TOTALES", "logiaName" => "", "valle" => "");
                array_push($results, $a);
            }
            return $results;
        } else {
            return '';
        }

    }
}
