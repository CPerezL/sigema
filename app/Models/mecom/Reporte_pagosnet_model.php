<?php

namespace App\Models\mecom;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reporte_pagosnet_model extends Model
{
    use HasFactory;
    public static function getBanco($desde, $hasta)
    {

        DB::select("SET lc_time_names = 'es_ES'");
        $dd = explode('/', $desde);
        $hh = explode('/', $hasta);
        $desded = "$dd[2]-$dd[1]-$dd[0]";
        $hastad = "$hh[2]-$hh[1]-$hh[0]";
        $qry = "SELECT 'Oriente de Bolivia' AS jurisdiccion,'G:.L:.B:. Obolos y Tramites' AS entidad,COUNT(DISTINCT B.idFormulario) AS numeroTrans,count(B.idRegistro)  AS numero,G.cuenta,G.banco,G.codigo,SUM(B.montoGLB) AS montototal,G.tipo,1 AS orden,G.cuenta
  FROM sgm_mecom_formularios A
  JOIN sgm_mecom_registros B ON A.idFormulario=B.idFormulario
  JOIN sgm_mecom_pagos_registros E ON A.idFormulario=E.idFormulario
  JOIN sgm_entidades G ON  G.identificador=1 AND G.tipo=1
  WHERE A.estado = 4  AND A.tipo IN (10,12,13,14,15,16,17) AND A.fechaAprobacion BETWEEN '$desded 00:00:01' AND '$hastad 23:59:59'
  GROUP BY G.cuenta,G.banco,G.codigo,G.tipo
  HAVING SUM(B.montoGLB)>0
  UNION ALL
  SELECT 'Oriente de Bolivia' AS jurisdiccion,'G:.L:.B:. Pagos Extra' AS entidad,COUNT(DISTINCT B.idFormulario) AS numeroTrans,count(B.idRegistro)  AS numero,G.cuenta,G.banco,G.codigo,SUM(B.montoGLB) AS montototal,G.tipo,1 AS orden,G.cuenta
  FROM sgm_mecom_formularios A
  JOIN sgm_mecom_registros B ON A.idFormulario=B.idFormulario
  JOIN sgm_mecom_pagos_registros E ON A.idFormulario=E.idFormulario
  JOIN sgm_entidades G ON  G.identificador=10 AND G.tipo=1
  WHERE A.estado = 4  AND A.tipo=11 AND A.fechaAprobacion BETWEEN '$desded 00:00:01' AND '$hastad 23:59:59' AND B.montoGLB>0
  GROUP BY G.cuenta,G.banco,G.codigo,G.tipo
  HAVING SUM(B.montoGLB)>0
  UNION ALL
  SELECT 'Oriente de Bolivia' AS jurisdiccion,'COMAP' AS entidad,COUNT(DISTINCT B.idFormulario) AS numeroTrans,count(B.idRegistro)  AS numero,G.cuenta,G.banco,G.codigo,SUM(B.montoCOMAP) AS montototal,G.tipo,2 AS orden,G.cuenta
  FROM sgm_mecom_formularios A
  JOIN sgm_mecom_registros B ON A.idFormulario=B.idFormulario
  JOIN sgm_mecom_pagos_registros E ON A.idFormulario=E.idFormulario
  JOIN sgm_entidades G ON  G.identificador=1 AND G.tipo=2
  WHERE A.estado = 4  AND A.tipo IN(10,11,12,13,14,15,16,17) AND A.fechaAprobacion BETWEEN '$desded 00:00:01' AND '$hastad 23:59:59' and B.montoCOMAP>0
  GROUP BY G.cuenta,G.banco,G.codigo
  HAVING SUM(E.montoCOMAP)>0
  UNION ALL
  SELECT case when B.tipo=2 then 'Gran Logia Distrital' ELSE 'Gran Delegacion Regional' END AS jurisdiccion, B.valle AS entidad, COUNT(DISTINCT E.idFormulario) AS numeroTrans,SUM(A.numeroMiembros) AS numero,G.cuenta,G.banco,G.codigo,SUM(E.montoGDR) AS montototal,
  G.tipo,G.identificador AS orden,G.cuenta
  FROM sgm_mecom_formularios A
  JOIN sgm_mecom_pagos_registros E ON A.idFormulario=E.idFormulario
  JOIN sgm_valles B ON  E.valle=B.idValle
  JOIN sgm_entidades G ON  A.idValle=G.identificador AND G.tipo=3
  WHERE A.estado = 4  AND A.tipo IN(10,11,12,13,14,15,16,17) AND A.fechaAprobacion BETWEEN '$desded 00:00:01' AND '$hastad 23:59:59' and E.montoGDR>0
  GROUP BY E.valle
  HAVING SUM(E.montoGDR)>0
  UNION ALL
  SELECT V.valle AS jurisdiccion, CONCAT(B.logia,' Nro. ',B.numero) AS entidad,COUNT(DISTINCT A.idFormulario) AS numeroTrans, SUM(E.cantidad) AS numero,G.cuenta,G.banco,G.codigo,SUM(E.montoTaller) AS montototal,G.tipo,G.identificador AS orden ,G.cuenta
  FROM sgm_mecom_formularios A
  JOIN sgm_mecom_pagos_registros E ON A.idFormulario=E.idFormulario AND A.taller=E.logia
  JOIN sgm_entidades G ON  G.identificador=E.logia
  JOIN sgm_logias B ON B.numero=E.logia
  JOIN sgm_valles V ON V.idValle=E.valle
  WHERE E.montoTaller>0 and G.tipo=4 AND A.estado = 4  AND A.tipo IN(10,11,12,13,14,15,16) AND A.fechaAprobacion BETWEEN '$desded 00:00:01' AND '$hastad 23:59:59'
  GROUP BY  E.valle,E.logia,G.cuenta
  HAVING SUM(E.montoTaller)>0
  ORDER BY tipo,orden";
//   echo $qry;
        $results = DB::select($qry);
        return $results;
    }
    public static function getReportePagos($desde, $hasta, $valle, $logia)
    {
        if (strlen($desde) > 0 || strlen($hasta) > 0) {
            DB::select("SET lc_time_names = 'es_ES'");
            $dd = explode('/', $desde);
            $hh = explode('/', $hasta);
            $desded = "$dd[2]-$dd[1]-$dd[0]";
            $hastad = "$hh[2]-$hh[1]-$hh[0]";

            if ($logia > 0) {
                $qry = "SELECT 'Oriente de Bolivia' AS jurisdiccion,'G:.L:.B:.' AS entidad,COUNT(DISTINCT B.idFormulario) AS numeroTrans,count(B.idRegistro)  AS numero,G.cuenta,G.banco,G.codigo,SUM(B.montoGLB) AS montototal,G.tipo,1 AS orden,G.cuenta
  FROM sgm_mecom_formularios A
  JOIN sgm_mecom_registros B ON A.idFormulario=B.idFormulario
  JOIN sgm_mecom_pagos_registros E ON A.idFormulario=E.idFormulario
  JOIN sgm_entidades G ON  G.identificador=1 AND G.tipo=1
  WHERE A.estado = 4  AND A.tipo IN (10,12,13,14,15,16,17) AND A.fechaAprobacion BETWEEN '$desded 00:00:01' AND '$hastad 23:59:59' AND A.taller=$logia
  UNION ALL
  SELECT 'Oriente de Bolivia' AS jurisdiccion,'COMAP' AS entidad,COUNT(DISTINCT B.idFormulario) AS numeroTrans,count(B.idRegistro)  AS numero,G.cuenta,G.banco,G.codigo,SUM(B.montoCOMAP) AS montototal,G.tipo,2 AS orden,G.cuenta
  FROM sgm_mecom_formularios A
  JOIN sgm_mecom_registros B ON A.idFormulario=B.idFormulario
  JOIN sgm_mecom_pagos_registros E ON A.idFormulario=E.idFormulario
  JOIN sgm_entidades G ON  G.identificador=1 AND G.tipo=2
  WHERE A.estado = 4  AND A.tipo IN (10,11,12,13,14,15,16,17) AND A.fechaAprobacion BETWEEN '$desded 00:00:01' AND '$hastad 23:59:59' AND A.taller=$logia
  UNION ALL
  SELECT case when B.tipo=2 then 'Gran Logia Distrital' ELSE 'Gran Delegacion Regional' END AS jurisdiccion, B.valle AS entidad, COUNT(DISTINCT E.idFormulario) AS numeroTrans,SUM(A.numeroMiembros) AS numero,G.cuenta,G.banco,G.codigo,SUM(E.montoGDR) AS montototal,
  G.tipo,G.identificador AS orden,G.cuenta
  FROM sgm_mecom_formularios A
  JOIN sgm_mecom_pagos_registros E ON A.idFormulario=E.idFormulario
  JOIN sgm_valles B ON  E.valle=B.idValle
  JOIN sgm_entidades G ON  A.idValle=G.identificador AND G.tipo=3
  WHERE A.estado = 4  AND A.tipo IN (10,11,12,13,14,15,16,17) AND A.fechaAprobacion BETWEEN '$desded 00:00:01' AND '$hastad 23:59:59'  AND A.taller=$logia
  GROUP BY E.valle
  UNION ALL
  SELECT V.valle AS jurisdiccion, CONCAT(B.logia,' Nro. ',B.numero) AS entidad,COUNT(A.idFormulario) AS numeroTrans, SUM(E.cantidad) AS numero,G.cuenta,G.banco,G.codigo,SUM(E.montoTaller) AS montototal,G.tipo,G.identificador AS orden ,G.cuenta
  FROM sgm_mecom_formularios A
  JOIN sgm_mecom_pagos_registros E ON A.idFormulario=E.idFormulario AND A.taller=E.logia
  JOIN sgm_entidades G ON  G.identificador=E.logia
  JOIN sgm_logias B ON B.numero=E.logia AND B.numero=$logia
  JOIN sgm_valles V ON V.idValle=E.valle
  WHERE E.montoTaller>0 and G.tipo=4 AND A.estado = 4 AND A.tipo IN (10,11,12,13,14,15,16,17) AND A.fechaAprobacion BETWEEN '$desded 00:00:01' AND '$hastad 23:59:59'  and E.tipo IN (1,11)
  GROUP BY  E.valle,E.logia
      ORDER BY tipo,orden";
            } elseif ($valle > 0) {
                $qry = "SELECT 'Oriente de Bolivia' AS jurisdiccion,'G:.L:.B:.' AS entidad,COUNT(DISTINCT B.idFormulario) AS numeroTrans,count(B.idRegistro)  AS numero,G.cuenta,G.banco,G.codigo,SUM(B.montoGLB) AS montototal,G.tipo,1 AS orden,G.cuenta
  FROM sgm_mecom_formularios A
  JOIN sgm_mecom_registros B ON A.idFormulario=B.idFormulario
  JOIN sgm_mecom_pagos_registros E ON A.idFormulario=E.idFormulario
  JOIN sgm_entidades G ON  G.identificador=1 AND G.tipo=1
  WHERE A.estado = 4  AND A.tipo IN (10,12,13,14,15,16,17) AND A.fechaAprobacion BETWEEN '$desded 00:00:01' AND '$hastad 23:59:59'  AND A.idValle=$valle
  UNION ALL
  SELECT 'Oriente de Bolivia' AS jurisdiccion,'COMAP' AS entidad,COUNT(DISTINCT B.idFormulario) AS numeroTrans,count(B.idRegistro)  AS numero,G.cuenta,G.banco,G.codigo,SUM(B.montoCOMAP) AS montototal,G.tipo,2 AS orden,G.cuenta
  FROM sgm_mecom_formularios A
  JOIN sgm_mecom_registros B ON A.idFormulario=B.idFormulario
  JOIN sgm_mecom_pagos_registros E ON A.idFormulario=E.idFormulario
  JOIN sgm_entidades G ON  G.identificador=1 AND G.tipo=2
  WHERE A.estado = 4  AND A.tipo IN (10,12,13,14,15,16,17) AND A.fechaAprobacion BETWEEN '$desded 00:00:01' AND '$hastad 23:59:59'  AND A.idValle=$valle
  UNION ALL
  SELECT case when B.tipo=2 then 'Gran Logia Distrital' ELSE 'Gran Delegacion Regional' END AS jurisdiccion, B.valle AS entidad, COUNT(DISTINCT E.idFormulario) AS numeroTrans,SUM(A.numeroMiembros) AS numero,G.cuenta,G.banco,G.codigo,SUM(E.montoGDR) AS montototal,
  G.tipo,G.identificador AS orden,G.cuenta
  FROM sgm_mecom_formularios A
  JOIN sgm_mecom_pagos_registros E ON A.idFormulario=E.idFormulario
  JOIN sgm_valles B ON  E.valle=B.idValle
  JOIN sgm_entidades G ON  A.idValle=G.identificador AND G.tipo=3
  WHERE A.estado = 4  AND A.tipo IN (10,11,12,13,14,15,16,17) AND A.fechaAprobacion BETWEEN '$desded 00:00:01' AND '$hastad 23:59:59' AND A.idValle=$valle
  GROUP BY E.valle
  UNION ALL
  SELECT V.valle AS jurisdiccion, CONCAT(B.logia,' Nro. ',B.numero) AS entidad,COUNT(A.idFormulario) AS numeroTrans, SUM(E.cantidad) AS numero,G.cuenta,G.banco,G.codigo,SUM(E.montoTaller) AS montototal,G.tipo,G.identificador AS orden ,G.cuenta
  FROM sgm_mecom_formularios A
  JOIN sgm_mecom_pagos_registros E ON A.idFormulario=E.idFormulario AND A.taller=E.logia
  JOIN sgm_entidades G ON  G.identificador=E.logia
  JOIN sgm_logias B ON B.numero=E.logia
  JOIN sgm_valles V ON V.idValle=E.valle
  WHERE E.montoTaller>0 and G.tipo IN (10,11,12,13,14,15,16,17) AND A.estado = 4  AND A.tipo IN (10,11) AND A.fechaAprobacion BETWEEN '$desded 00:00:01' AND '$hastad 23:59:59'  AND A.idValle=$valle  and E.tipo IN (1,11)
  GROUP BY  E.valle,E.logia
      ORDER BY tipo,orden";
            } else {
                $qry = "SELECT 'Oriente de Bolivia' AS jurisdiccion,'G:.L:.B:.' AS entidad,COUNT(DISTINCT B.idFormulario) AS numeroTrans,count(B.idRegistro)  AS numero,G.cuenta,G.banco,G.codigo,SUM(B.montoGLB) AS montototal,G.tipo,1 AS orden,G.cuenta
  FROM sgm_mecom_formularios A
  JOIN sgm_mecom_registros B ON A.idFormulario=B.idFormulario
  JOIN sgm_mecom_pagos_registros E ON A.idFormulario=E.idFormulario
  JOIN sgm_entidades G ON  G.identificador=1 AND G.tipo=1
  WHERE A.estado = 4  AND A.tipo IN (10,12,13,14,15,16,17) AND A.fechaAprobacion BETWEEN '$desded 00:00:01' AND '$hastad 23:59:59'
  UNION ALL
  SELECT 'Oriente de Bolivia' AS jurisdiccion,'COMAP' AS entidad,COUNT(DISTINCT B.idFormulario) AS numeroTrans,count(B.idRegistro)  AS numero,G.cuenta,G.banco,G.codigo,SUM(B.montoCOMAP) AS montototal,G.tipo,2 AS orden,G.cuenta
  FROM sgm_mecom_formularios A
  JOIN sgm_mecom_registros B ON A.idFormulario=B.idFormulario
  JOIN sgm_mecom_pagos_registros E ON A.idFormulario=E.idFormulario
  JOIN sgm_entidades G ON  G.identificador=1 AND G.tipo=2
  WHERE A.estado = 4  AND A.tipo IN (10,11,12,13,14,15,16,17) AND A.fechaAprobacion BETWEEN '$desded 00:00:01' AND '$hastad 23:59:59'
  UNION ALL
  SELECT case when B.tipo=2 then 'Gran Logia Distrital' ELSE 'Gran Delegacion Regional' END AS jurisdiccion, B.valle AS entidad, COUNT(DISTINCT E.idFormulario) AS numeroTrans,SUM(A.numeroMiembros) AS numero,G.cuenta,G.banco,G.codigo,SUM(E.montoGDR) AS montototal,
  G.tipo,G.identificador AS orden,G.cuenta
  FROM sgm_mecom_formularios A
  JOIN sgm_mecom_pagos_registros E ON A.idFormulario=E.idFormulario
  JOIN sgm_valles B ON  E.valle=B.idValle
  JOIN sgm_entidades G ON  A.idValle=G.identificador AND G.tipo=3
  WHERE A.estado = 4  AND A.tipo IN (10,11,12,13,14,15,16,17) AND A.fechaAprobacion BETWEEN '$desded 00:00:01' AND '$hastad 23:59:59'
  GROUP BY E.valle
  UNION ALL
  SELECT V.valle AS jurisdiccion, CONCAT(B.logia,' Nro. ',B.numero) AS entidad,COUNT(A.idFormulario) AS numeroTrans, SUM(E.cantidad) AS numero,G.cuenta,G.banco,G.codigo,SUM(E.montoTaller) AS montototal,G.tipo,G.identificador AS orden ,G.cuenta
  FROM sgm_mecom_formularios A
  JOIN sgm_mecom_pagos_registros E ON A.idFormulario=E.idFormulario AND A.taller=E.logia
  JOIN sgm_entidades G ON  G.identificador=E.logia
  JOIN sgm_logias B ON B.numero=E.logia
  JOIN sgm_valles V ON V.idValle=E.valle
  WHERE E.montoTaller>0  and G.tipo=4 AND A.estado = 4  AND A.tipo IN (10,11,12,13,14,15,16,17) AND A.fechaAprobacion BETWEEN '$desded 00:00:01' AND '$hastad 23:59:59' and E.tipo IN (1,11)
  GROUP BY  E.valle,E.logia
      ORDER BY tipo,orden";
            }
            $results = DB::select($qry);
            return $results;
        } else {
            return '';
        }

    }
    public static function getReportePagosConta($desde, $hasta, $valle)
    {
        if ($valle > 0) {
            DB::select("SET lc_time_names = 'es_ES'");
            $dd = explode('/', $desde);
            $hh = explode('/', $hasta);
            $desded = "$dd[2]-$dd[1]-$dd[0]";
            $hastad = "$hh[2]-$hh[1]-$hh[0]";
            $qry = "SELECT C.NombreCompleto,CASE C.Grado WHEN 2 THEN 'C' WHEN 3 THEN 'M' WHEN 4 THEN 'V' ELSE 'A' END AS Grado, SUBSTRING(C.Miembro, 1, 1) AS Cat,B.numeroCuotas as cantidad,
            CONCAT(DATE_FORMAT(B.ultimoPago,'%b-%Y'),'/',DATE_FORMAT(B.fechaPagoNuevo,'%b-%Y')) AS Periodo, CONCAT(D.logia, ' Nº ',B.taller) AS Logia,B.montoGDR, B.montoGLB,
            B.montoCOMAP,B.monto,B.taller,1 AS orden,A.fechaAprobacion ,case When B.montoTaller>0 then B.montoTaller ELSE E.montoTaller END AS montoTaller
      FROM sgm_mecom_formularios A
      JOIN sgm_mecom_registros B ON A.idFormulario=B.idFormulario
      JOIN sgm_miembros C ON B.idMiembro=C.id
      JOIN sgm_logias D ON B.taller=D.numero
      JOIN sgm_mecom_pagos_registros E ON A.idFormulario=E.idFormulario
      WHERE A.estado = '4' AND D.valle = $valle AND A.tipo IN (10,11,12,13,14,15,16,17) AND A.fechaAprobacion BETWEEN '$desded 00:00:01' AND '$hastad 23:59:59'
      UNION ALL
      SELECT '' AS NombreCompleto,'' AS Grado, '' AS Cat,'' AS cantidad,'SubTotal' AS Periodo,CONCAT(D.logia, ' Nº ',B.taller) AS Logia,
      SUM(B.montoGDR), SUM(B.montoGLB),SUM(B.montoCOMAP),SUM(B.monto),B.taller,2 AS orden,A.fechaAprobacion,SUM(case When B.montoTaller>0 then B.montoTaller ELSE E.montoTaller END) AS montoTaller
      FROM sgm_mecom_formularios A
      JOIN sgm_mecom_registros B ON A.idFormulario=B.idFormulario
      JOIN sgm_miembros C ON B.idMiembro=C.id
      JOIN sgm_logias D ON B.taller=D.numero
      JOIN sgm_mecom_pagos_registros E ON A.idFormulario=E.idFormulario
      WHERE A.estado = '4' AND D.valle = $valle AND A.tipo IN (10,11,12,13,14,15,16,17)AND A.fechaAprobacion BETWEEN '$desded 00:00:01' AND '$hastad 23:59:59' GROUP BY B.taller
      UNION ALL
      SELECT '' AS NombreCompleto,'' AS Grado, '' AS Cat,'' AS cantidad,'' AS Periodo,'TOTALES' AS Logia,SUM(B.montoGDR), SUM(B.montoGLB),SUM(B.montoCOMAP),
      SUM(B.monto),1000 AS taller,3 AS orden,A.fechaAprobacion,SUM(case When B.montoTaller>0 then B.montoTaller ELSE E.montoTaller END) AS montoTaller
      FROM sgm_mecom_formularios A
      JOIN sgm_mecom_registros B ON A.idFormulario=B.idFormulario
      JOIN sgm_miembros C ON B.idMiembro=C.id
      JOIN sgm_logias D ON B.taller=D.numero
      JOIN sgm_mecom_pagos_registros E ON A.idFormulario=E.idFormulario
      WHERE A.estado = '4' AND D.valle = $valle AND A.tipo IN (10,11,12,13,14,15,16,17) AND A.fechaAprobacion BETWEEN '$desded 00:00:01' AND '$hastad 23:59:59'
      ORDER BY taller,orden,fechaAprobacion";
//    echo $qry.'<hr>';
            $results = DB::select($qry);
            return $results;
        } else {
            return '';
        }

    }
    public static function getReporteResumen($desde, $hasta, $valle)
    {
        if ($valle > 0) {
            DB::select("SET lc_time_names = 'es_ES'");
            $dd = explode('/', $desde);
            $hh = explode('/', $hasta);
            $desded = "$dd[2]-$dd[1]-$dd[0]";
            $hastad = "$hh[2]-$hh[1]-$hh[0]";
            $qry = "SELECT 'Regulares' AS Miembrotxt,SUM(case When B.montoTaller>0 then B.montoTaller ELSE E.montoTaller END) AS log,SUM(B.montoGDR) AS gdr, SUM(B.montoGLB) AS glb,SUM(B.montoCOMAP) AS comap,SUM(B.monto) AS total,1 AS orden,COUNT(C.id) AS Cant
    FROM sgm_mecom_formularios A
    JOIN sgm_mecom_registros B ON A.idFormulario=B.idFormulario
    JOIN sgm_miembros C ON B.idMiembro=C.id
    JOIN sgm_logias D ON B.taller=D.numero
    JOIN sgm_mecom_pagos_registros E ON A.idFormulario=E.idFormulario
    WHERE A.estado = '4' AND D.valle = $valle AND A.tipo IN (10,12,13,14,15,16,17) AND A.fechaAprobacion BETWEEN '$desded 00:00:01' AND '$hastad 23:59:59' AND C.Miembro='Regular'
    UNION ALL
    SELECT 'Honorarios' AS Miembrotxt,SUM(case When B.montoTaller>0 then B.montoTaller ELSE E.montoTaller END) AS log,SUM(B.montoGDR) AS gdr, SUM(B.montoGLB) AS glb,SUM(B.montoCOMAP) AS comap,SUM(B.monto) AS total,2 AS orden,COUNT(C.id) AS Cant
    FROM sgm_mecom_formularios A
    JOIN sgm_mecom_registros B ON A.idFormulario=B.idFormulario
    JOIN sgm_miembros C ON B.idMiembro=C.id
    JOIN sgm_logias D ON B.taller=D.numero
    JOIN sgm_mecom_pagos_registros E ON A.idFormulario=E.idFormulario
    WHERE A.estado = '4' AND D.valle = $valle AND A.tipo IN (10,12,13,14,15,16,17) AND A.fechaAprobacion BETWEEN '$desded 00:00:01' AND '$hastad 23:59:59' AND C.Miembro='Honorario'
    UNION ALL
    SELECT 'Ausentes' AS Miembrotxt,SUM(case When B.montoTaller>0 then B.montoTaller ELSE E.montoTaller END) AS log,SUM(B.montoGDR) AS gdr, SUM(B.montoGLB) AS glb,SUM(B.montoCOMAP) AS comap,SUM(B.monto) AS total,3 AS orden,COUNT(C.id) AS Cant
    FROM sgm_mecom_formularios A
    JOIN sgm_mecom_registros B ON A.idFormulario=B.idFormulario
    JOIN sgm_miembros C ON B.idMiembro=C.id
    JOIN sgm_logias D ON B.taller=D.numero
    JOIN sgm_mecom_pagos_registros E ON A.idFormulario=E.idFormulario
    WHERE A.estado = '4' AND D.valle = $valle AND A.tipo IN (10,12,13,14,15,16,17)AND A.fechaAprobacion BETWEEN '$desded 00:00:01' AND '$hastad 23:59:59' AND C.Miembro='Ausente'
    UNION ALL
    SELECT 'TOTALES' AS Miembro,SUM(case When B.montoTaller>0 then B.montoTaller ELSE E.montoTaller END) AS log,SUM(B.montoGDR) AS gdr, SUM(B.montoGLB) AS glb,SUM(B.montoCOMAP) AS comap,SUM(B.monto) AS total,4 AS orden,COUNT(C.id) AS Cant
    FROM sgm_mecom_formularios A
    JOIN sgm_mecom_registros B ON A.idFormulario=B.idFormulario
    JOIN sgm_miembros C ON B.idMiembro=C.id
    JOIN sgm_logias D ON B.taller=D.numero
    JOIN sgm_mecom_pagos_registros E ON A.idFormulario=E.idFormulario
    WHERE A.estado = '4' AND D.valle = $valle AND A.tipo IN (10,12,13,14,15,16,17) AND A.fechaAprobacion BETWEEN '$desded 00:00:01' AND '$hastad 23:59:59'
    ORDER BY orden";
            //    echo $qry.'<hr>';
            $query = DB::select($qry);
            return $query;
        } else {
            return '';
        }
    }
    /*  REPORTES NUEVOS DE OBOLOS Y DESPOITOS QR PARA TALLERES, GDRS, GLB 2023 */
    public static function getReporteObolosTaller($desde, $hasta, $taller)
    {
        if ($taller > 0) {
            DB::select("SET lc_time_names = 'es_ES'");
            $dd = explode('/', $desde);
            $hh = explode('/', $hasta);
            $desded = "$dd[2]-$dd[1]-$dd[0]";
            $hastad = "$hh[2]-$hh[1]-$hh[0]";
            $qry = "SELECT  1 AS orden,A.idRegistro, B.fechaAprobacion,A.miembro,C.Miembro,D.NombreCompleto,A.aprobado,B.tipo,A.idFormulario,C.numeroCuotas, B.numeroMiembros,A.monto AS montoform,
            case when C.monto>0 then C.monto ELSE A.monto END AS monto,case when C.montoTaller>0 then C.montoTaller ELSE A.montoTaller END AS montoTaller,
            DATE_FORMAT(C.ultimoPago,'%Y-%b') AS mesanterior,DATE_FORMAT(C.fechaPagoNuevo,'%Y-%b') AS mesnuevo
            FROM sgm_mecom_pagos_registros A JOIN sgm_mecom_formularios B ON A.idFormulario=B.idFormulario
            JOIN sgm_mecom_registros C ON B.idFormulario=C.idFormulario
            left JOIN sgm_miembros D ON C.idMiembro=D.id
            WHERE A.logia=$taller AND  B.fechaAprobacion BETWEEN '$desded 00:00:01' AND '$hastad  23:59:59' AND ( C.montoTaller >0 OR A.montoTaller>0 )
            UNION ALL
            SELECT 2 AS orden, '' idRegistro, '' fechaAprobacion,'' miembro, '' Miembro, 'SUMA TOTAL' NombreCompleto,'' aprobado, '' ,'' idFormulario, '=' numeroCuotas, '' numeroMiembros, '' montoform,
            SUM(case when C.monto>0 then C.monto ELSE A.monto END) AS monto,
            SUM(case when C.montoTaller>0 then C.montoTaller ELSE A.montoTaller END) AS montoTaller,'' mesanterior,'' mesnuevo
            FROM sgm_mecom_pagos_registros A JOIN sgm_mecom_formularios B ON A.idFormulario=B.idFormulario
            JOIN sgm_mecom_registros C ON B.idFormulario=C.idFormulario
            left JOIN sgm_miembros D ON C.idMiembro=D.id
            WHERE A.logia=$taller AND B.fechaAprobacion BETWEEN '$desded 00:00:01' AND '$hastad  23:59:59' AND ( C.montoTaller >0 OR A.montoTaller>0 )
            ORDER BY orden asc,fechaAprobacion ASC,idFormulario ASC";
            $results = DB::select($qry);
            return $results;
        } else {
            return '';
        }

    }
}
