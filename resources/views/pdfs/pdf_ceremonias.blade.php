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
        <td width="25%" align="center" valign="middle"><img src="media/{{ $logo }}" width="150"></td>
        <td width="40%">
          <div align="center">
            <h1>GRAN LOGIA DE BOLIVIA</h1>
            <h2>A:. L:. G:. D:. G:. A:. D:. U:.</h2>
          </div>
        </td>
        <td width="20%" valign="top" align="right">Gestión {{ $gestion }} <br>FORMULARIO GL-9<br></td>
      </tr>
    </table>
    <p></p>
    <p>
    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="font-size:14px;">
      <tr>
        <td width="25%" align="center" valign="middle">
            {{-- Solicitud Nro {{ $solicitud }} --}}
        </td>
        <td width="40%">
          <div align="center">
            Valle de {{ strtoupper($nvalle) }}
          </div>
        </td>
        <td width="20%">Fecha: {{ $fecha }}</td>
      </tr>
    </table>
    </p>
    <blockquote>
      <div style="font-size:14px;">
        <p>En nombre y bajo los auspicios de la <b>GRAN LOGIA DE BOLIVIA</b></p>
        <p>La R:.L:.S:. {{ $taller }}</p>
        <p>Regularmente Constituida en el <b>Valle de {{ ucfirst($nvalle) }}</b>, a la</p>
        <center>
          <h1>GRAN LOGIA DE BOLIVIA<br><small>S:. F:. U:.</small></h1>
        </center>
        <p>Queridos Hermanos:</p>
        <p>De conformidad con el Reglamento de la Orden tenemos la satisfaccion de pediros tengais a bien conceder {{ $textounocert }}</p>
        <p>
        <ol>
          {!! $asistencia !!}
        </ol>
        </p>
      </div>
      <div style="font-size:14px;">
        <p>Para {{ $textodoscert }} y datos masónicos se encuentran indicados en el cuadro que adjuntamos.</p>
        <p>Los derechos correspondientes que ascienden a la suma de Bs. <b>{{ $monto }}</b> estan amparados por la transacción electrónica a la orden de la G:.L:.B:.</p>
      </div>
    </blockquote>
  </div>
</body>

</html>
