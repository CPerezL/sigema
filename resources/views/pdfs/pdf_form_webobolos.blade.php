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
            <h2>Planilla de Aportes a la Gran Logia de Bolivia</h2>
          </div>
        </td>
      </tr>
    </table>
    <br>
    <p>Valle de {{ $nvalle }}, {{ $diaformu }}</p>
    <hr>
    <div><b>Planilla de fechas {{ $desdefechas }}</b></div>
    <hr>
    <br>
    <div align="left">Resumen de importes para realizar las transferencias electronicas</div>
    <br>
    <div style="text-align:center;font-size: 12px;">
      <table width="90%" cellspacing="0" cellpadding="3" border="1" align="center">
        <thead>
          <tr style="text-align: center;">
            <th>DETALLE</th>
            <th>Cant.</th>
            <th>Log.</th>
            <th>GLB</th>
            <th>COMAP</th>
            <th>GDR</th>
            <th>TOTAL</th>
          </tr>
        </thead>
        <tbody>
          <?php
          foreach ($resu as $vres) {
              echo '<tr><td>' . $vres->Miembrotxt . '</td><td style="text-align: center;">' . $vres->Cant . '</td><td style="text-align: center;">' . number_format($vres->log) . '</td><td style="text-align: center;">' . number_format($vres->glb) . '</td><td style="text-align: center;">' . number_format($vres->comap) . '</td><td style="text-align: center;">' . number_format($vres->gdr) . '</td><td style="text-align: center;">' . number_format($vres->total) . '</td></tr>';
          }
          ?>
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
        <td width="0%"></td>
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
            <center>
              <div><b>Planilla de fechas {{ $desdefechas }}</b></div>
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
          <th>Periodo</th>
          <th>Logia</th>
          <th>LOG.</th>
          <th>GDR</th>
          <th>GLB</th>
          <th>COMAP</th>
          <th>Total Pago</th>
        </tr>
      </thead>
      <tbody>
        <?php
        foreach ($lista as $ver) {
            echo "<tr style=\"text-align:center;\"><td style=\"text-align:left;\" width=\"150\">$ver->NombreCompleto</td><td>$ver->Grado</td><td>$ver->Cat</td><td>$ver->cantidad</td><td>$ver->Periodo</td><td width=\"80\">$ver->Logia</td><td>$ver->montoTaller</td><td>$ver->montoGDR</td><td>$ver->montoGLB</td><td>$ver->montoCOMAP</td><td>$ver->monto</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
  <footer></footer>
</body>

</html>
