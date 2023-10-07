<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <style>
    body {
      font: normal 14px Verdana, Arial, sans-serif;
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
  <div style="font-size:11px; line-height: 1.6;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="25%" align="center" valign="middle"><img src="media/glsp-150.png" width="150"></td>
        <td width="40%">
          <div align="center">
            <h2>GRAN LOGIA SIMBÓLICA DEL PARAGUAY<br>CERTIFICACIÓN DE DEPOSITO<BR>Departamento de Contabilidad</h2>
          </div>
        </td>
        <td width="20%" valign="top" align="right"></td>
      </tr>
    </table>
    <p></p>
    <p></p>
    <hr>
    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="font-size:14px;">
      <tr>
        <td width="25%" align="center" valign="middle"></td>
        <td width="20%">
        </td>
        <td width="40%" align="right">Trámite Nro {{ $tramite }}-{{ $gestionet }}</td>
      </tr>
    </table>
    <blockquote>
      <div style="font-size:14px; text-align: justify;">
        <p>El presente documento CERTIFICA la recepcion del pago para la reincoporación a la Orden, para el Hermano: {{ $profano }}, Deposito/Transferencia en fecha {{ strtoupper($fechadep) }}
          realizado por la R:.L:.S:. {{ $taller }} en el valle de {{ strtoupper($nvalle) }}.
        </p>
        <p></p>
        <p>Asunción, {{ $fecha }}</p>
        <p></p>
        <p></p>
        <center>
          <p><B>DEPARTAMENTO DE CONTABILIDAD<B></p>
        </center>
      </div>
    </blockquote>
  </div>
</body>

</html>
