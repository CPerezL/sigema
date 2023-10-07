<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <style>
        @page {
                margin: 0cm 0cm;
            }

            /** Define now the real margins of every page in the PDF **/
            body {
                margin-top: 3cm;
                margin-left: 2cm;
                margin-right: 2cm;
                margin-bottom: 2cm;
            }

            /** Define the header rules **/
            header {
                position: fixed;
                top: 0.5cm;
                left: 1.0cm;
                right: 0cm;
                height: 3cm;
            }

            /** Define the footer rules **/
            footer {
                position: fixed;
                bottom: 0cm;
                left: 1.4cm;
                right: 0cm;
                height: 3cm;
            }
    cabeza.table {
      border-left: 0.01em solid #000;
      border-right: 0;
      border-top: 0.01em solid #000;
      border-bottom: 0;
      border-collapse: collapse;
    }

    cabeza.table td,
    cabeza.table th {
      border-left: 0;
      border-right: 0.01em solid #000;
      border-top: 0;
      border-bottom: 0.01em solid #000;
    }
  </style>
</head>

<body>
    <header>
        <table width="100%">
          <tr>
            <td colspan="2"><img src="cabecera-glsp.jpg" width="96%"></td>
          </tr>
        </table>

      </header>
  <div style="font-size:13px; line-height: 1.3;">
    {{-- <table width="100%" border="0" cellspacing="20" cellpadding="0">
      <tr style="font-size:11px; line-height: 1;">
        <td width="10%"></td>
        <td width="30%">Para uso de la G:.L:.D:./G:.D:.R:.</td>
        <td>Fecha _______________</td>
        <td>Fecha _______________</td>
        <td>Fecha _______________</td>
      </tr>
    </table> --}}<br>
    <br>
    <br>
    <br>
    <div align="right">{!! $dtenida !!}</div>
    <div style="text-align: justify;">
      <br>En nombre y bajo los auspicios de la Gran Logia Simbólica del Paraguay en el Valle de {!! $nvalle !!}, se abrieron los trabajos de la <b> {!! $taller !!}</b>,{!! $ritotexto !!} con la asistencia de los siguientes hermanos:
    </div>
    <center>
      <h3>OFICIALIDAD</h3>
    </center>
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      {!! $oficiales !!}
    </table>
    <center>
      <h3>ASISTENCIA</h3>
    </center>
    <table width="100%" cellspacing="0" cellpadding="3" border="1">
      <tr style="text-align:center;font-size:12px;">
        <td width="24%">{!! $exvm !!}</td>
        <td width="26%">MAESTROS</td>
        <td width="26%">COMPAÑEROS</td>
        <td width="24%">APRENDICES</td>
      </tr>
      {!! $asistencia !!}
    </table>
    <table width="100%" cellspacing="0" cellpadding="3" border="1">
      <tr style="font-size:12px;">
        <td>
          Visitantes: {!! $visitadores !!}
        </td>
      </tr>
    </table>
    <div style="font-size:10px; line-height: 1;">Nota: Esta Acta debe remitirse directamente a la Gran Logia Simbólica del Paraguay, firmada por el Venerable Maestro y Secretario al d&iacute;a siguiente de haber sido aprobada.
    </div>
  </div>
</body>
</html>
