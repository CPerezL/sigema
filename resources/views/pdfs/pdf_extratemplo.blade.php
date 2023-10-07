<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <style>
    plan.table {
      border-left: 0.01em solid #000;
      border-right: 0;
      border-top: 0.01em solid #000;
      border-bottom: 0;
      border-collapse: collapse;
    }

    plan.table td,
    plan.table th {
      border-left: 0;
      border-right: 0.01em solid #000;
      border-top: 0;
      border-bottom: 0.01em solid #000;
    }
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
  <div style="font-size:11px; line-height: 1.6;">
    <div align="center">
      <h3>ACTA DE TRABAJOS EXTRA TEMPLO<br>INSTRUCCIÓN MASÓNICA<br>GESTIÓN {!! $gestionet !!}<br>{!! $gradoet !!} GRADO</h3>
    </div>
    <div style="font-size:12px;">
      <div align="left">LOGIA: {!! $taller !!}</div>
      <div align="left">VALLE: {!! $nvalle !!}</div>
      <div align="left">TEMA: {!! $etema !!}</div>
      <div align="left">FECHA: {!! $ftenida !!}</div>
    </div>
    <table width="100%" cellspacing="0" cellpadding="0" border="1">
      <tr style="text-align: center">
        <td width="10%">GRADO</td>
        <td width="60%">NOMBRE COMPLETO</td>
      </tr>
      {!! $asistencia !!}
    </table>
    <p>&nbsp;</p>
    <h4>INSTRUCTOR(ES)</h4>
    <table width="100%" cellspacing="0" cellpadding="0" border="1">
      <tr style="text-align: center">
        <td width="65%">NOMBRE COMPLETO</td>
      </tr>
      {!! $instructores !!}
    </table>
    <p>&nbsp;</p>
    <div style="font-size:12px;">Fecha de remisión a la Gran Secretaria .............................................</div>
  </div>
</body>

</html>
