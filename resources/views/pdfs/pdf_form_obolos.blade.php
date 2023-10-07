<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <style>
    .row {
      margin-right: -15px;
      margin-left: -15px;
    }

    .col-xs-1,
    .col-sm-1,
    .col-md-1,
    .col-lg-1,
    .col-xs-2,
    .col-sm-2,
    .col-md-2,
    .col-lg-2,
    .col-xs-3,
    .col-sm-3,
    .col-md-3,
    .col-lg-3,
    .col-xs-4,
    .col-sm-4,
    .col-md-4,
    .col-lg-4,
    .col-xs-5,
    .col-sm-5,
    .col-md-5,
    .col-lg-5,
    .col-xs-6,
    .col-sm-6,
    .col-md-6,
    .col-lg-6,
    .col-xs-7,
    .col-sm-7,
    .col-md-7,
    .col-lg-7,
    .col-xs-8,
    .col-sm-8,
    .col-md-8,
    .col-lg-8,
    .col-xs-9,
    .col-sm-9,
    .col-md-9,
    .col-lg-9,
    .col-xs-10,
    .col-sm-10,
    .col-md-10,
    .col-lg-10,
    .col-xs-11,
    .col-sm-11,
    .col-md-11,
    .col-lg-11,
    .col-xs-12,
    .col-sm-12,
    .col-md-12,
    .col-lg-12 {
      position: relative;
      min-height: 1px;
      padding-right: 15px;
      padding-left: 15px;
    }

    .col-lg-12 {
      width: 100%;
    }

    .text-center {
      text-align: center;
    }

    body {
      font-family: Helvetica, Arial, sans-serif;
      font-size: 12px;
      line-height: 1.42857143;
      color: #333;
      background-color: #fff;
    }

    .otable table {
      border-left: 0.01em solid #000;
      border-right: 0;
      border-top: 0.01em solid #000;
      border-bottom: 0;
      border-collapse: collapse;
    }

    .otable table td,
    .otable table th {
      border-left: 0;
      border-right: 0.01em solid #000;
      border-top: 0;
      border-bottom: 0.01em solid #000;
    }

    .mitable table {
      border-left: 0;
      border-right: 0;
      border-top: 0;
      border-bottom: 0;
      border-collapse: collapse;
    }

    .mitable table td,
    .mitable table th {
      border-left: 0;
      border-right: 0;
      border-top: 0;
      border-bottom: 0;
    }
  </style>
</head>

<body>
  <div style="font-size:14px; line-height: 1.5;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="mitable">
      <tr>
        <td width="15%" align="center" valign="middle"><img width="90" src="media/{{ $logo }}"></td>
        <td width="85%">
          <div align="center" style="font-size:12px; line-height: 0.8;">
            <h2>Planilla de Aportes a la Gran Logia</h2>
            <h4>{{ $taller }}</h4>
          </div>
        </td>
      </tr>
    </table>
    <br>
    <p>Valle de {{ $nvalle }}, {{ $diaformu }}</p>
    <hr>
    <div><b>Planilla Nro. L{{ $tallern }}-{{ $numero }}-{{ $gestion }}</b></div>
    <div>
      Fecha de elaboraci&oacute;n y envio de la Planilla : {{ $diaelabor }}
    </div>
    <hr>
    <h3>RESUMEN</h3>
    <div style="align:center">
      <table width="80%" border="0" cellspacing="0" cellpadding="0" align="center">
        <tr>
          <td>Cuota Gran Logia</td>
          <td>Bs.</td>
          <td>{{ $montos->mglb }}</td>
        </tr>
        <tr>
          <td>Cuota Comite de Mutuo Apoyo</td>
          <td>Bs.</td>
          <td>{{ $montos->mcomap }}</td>
        </tr>
        <tr>
          <td>Cuota GLD / GDR</td>
          <td>Bs.</td>
          <td>{{ $montos->mgdr }}</td>
        </tr>
        <tr>
          <td></td>
          <td>TOTALES</td>
          <td><u>{{ $montos->msuma }}</u></td>
        </tr>
      </table>
    </div>
    <br>
    <hr>
    <br>
    <div align="left">Resumen de importes para realizar las transferencias electronicas al Banco BISA S.A.</div>
    <br>
    <div style="text-align:center;font-size: 12px;">
      <table width="90%" cellspacing="0" cellpadding="3" border="1" align="center">
        <thead>
          <tr style="text-align: center;">
            <th>DETALLE</th>
            <th>No DE CUENTA</th>
            <th>CODIGO DE PLANILLA</th>
            <th>IMPORTES</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Cuota Gran Logia de Bolivia</td>
            <td style="text-align: center;">{{ $dglb->cuenta }}</td>
            <td style="text-align: center;">{{ $dglb->planilla }}-{{ $tallern }}-{{ $numero }}-{{ $gestion }}</td>
            <td style="text-align: center;">{{ $montos->mglb }}</td>
          </tr>
          <tr>
            <td>Cuota Comite de Mutuo Apoyo</td>
            <td style="text-align: center;">{{ $dcomap->cuenta }}</td>
            <td style="text-align: center;">{{ $dcomap->planilla }}-{{ $tallern }}-{{ $numero }}-{{ $gestion }}</td>
            <td style="text-align: center;">{{ $montos->mcomap }}</td>
          </tr>
          <tr>
            <td>Cuota Gran Delegacion Regional</td>
            <td style="text-align: center;">{{ $dgdr->cuenta }}</td>
            <td style="text-align: center;">{{ $dgdr->planilla }}-{{ $tallern }}-{{ $numero }}-{{ $gestion }}</td>
            <td style="text-align: center;">{{ $montos->mgdr }}</td>
          </tr>
          <tr>
            <td colspan="2"></td>
            <td style="text-align: center;">TOTALES</td>
            <td style="text-align: center;">{{ $montos->msuma }}</td>
          </tr>
        </tbody>
      </table>
    </div>
    <br><br>
    <br><br>
    <br><br>
    <br><br>
    <br><br>
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="0%">
          <center>Venerable Maestro</center>
        </td>
        <td>
          <center>Tesorero</center>
        </td>
      </tr>
    </table>
  </div>
  <footer></footer>
  <div style="page-break-after: always;"></div>
  <div style="font-size:10px; line-height: 1;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="mitable">
      <tr>
        <td width="15%" align="center" valign="middle"><img width="90" src="media/{{ $logo }}"></td>
        <td width="85%">
          <div align="center" style="font-size:12px; line-height: 0.8;">
            <h2>Planilla de Aportes a la Gran Logia de Bolivia</h2>
            <h4>{{ $taller }}</h4>
            <center>
              <div><small>Planilla Nro. L{{ $tallern }}-{{ $numero }}-{{ $gestion }}</small></div>
            </center>
          </div>
        </td>
      </tr>
    </table>
    <table width="100%" cellspacing="0" cellpadding="2" border="1" align="center" style="font-size: 9px;">
      <thead>
        <tr style="text-align:center;">
          <th colspan="6" style="border:none;"></th>
          <th colspan="4" style="background-color:#cccccc;">CUOTAS</th>
          <th style="border:0"></th>
        </tr>
        <tr style="text-align:center;background-color:#cccccc;">
          <th>Nombre Completo</th>
          <th>Gr</th>
          <th>Cat</th>
          <th>Cuotas</th>
          <th>Exen</th>
          <th>Periodo</th>
          <th>Ext</th>
          <th>GDR</th>
          <th>GLB</th>
          <th>COMAP</th>
          <th>Total Pago</th>
        </tr>
      </thead>
      <tbody>
        <?php
        foreach ($lista as $ver) {
            echo "<tr style=\"text-align:center;\">
              <td style=\"text-align:left;\" width=\"200\">$ver->NombreCompleto</td>
              <td>$ver->grado</td>
              <td>$ver->miembro</td>
              <td>$ver->numeroCuotas</td>
              <td>0</td><td width=\"80\">$ver->fechaUno/$ver->fechaDos</td><td></td><td>$ver->montoGDR</td><td>$ver->montoGLB</td><td>$ver->montoCOMAP</td><td>$ver->monto</td></tr>";
        }
        ?>
      </tbody>
      <tfoot>
        <tr style="text-align:center;font-size: 10px;font-weight: bold;">
          <td colspan="5"></td>
          <td>TOTALES</td>
          <td></td>
          <td>{{ $montos->mgdr }}</td>
          <td>{{ $montos->mglb }}</td>
          <td>{{ $montos->mcomap }}</td>
          <td>{{ $montos->msuma }}</td>
        </tr>
      </tfoot>
    </table>
  </div>
  <footer></footer>
</body>

</html>
