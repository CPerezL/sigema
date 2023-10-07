<!DOCTYPE html>
<html>
<meta charset="utf-8">

<head>
  <style>
    body {
      font-family: sans-serif;
    }

    table,
    th,
    td {
      font-size: 10px;
      border-collapse: collapse;
    }

    h2 {
      font-size: 16px;
      font-family: sans-serif;
      font-style: italic;
      text-align: center;
    }

    h6 {
      font-family: sans-serif;
      font-size: 8px;
      font-weight: normal;
      font-style: normal;
    }

    div.texto {
      font-size: 10px;
      font-family: sans-serif;
      text-align: justify;
      line-height: 1.6;
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
                left: 1.5cm;
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

    .pagenum:before {
      content: counter(page);
    }

    #pagenumber:before {
      content: "{{ $circular }} - Pag. "counter(page);
    }
  </style>
</head>

<body>
  <header>
    <table width="100%">
      <tr>
        <td><img src="media/glb-150.png" height="120"></td>
        <td><img src="media/cabecera.jpg" height="80" width="400"></td>
      </tr>
    </table>

  </header>
  <footer>
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td>
          <img src="media/pies.jpg" height="74">
          <center><small><b><span id="pagenumber" /></span> de {{$paginas}}</b></small></center>
        </td>
      </tr>
    </table>
</footer>
  <!-- Wrap the content of your PDF inside a main tag -->
  <main>
    <br>
    <div class="texto" style="text-align: right">{!! $fechagen !!}</div>

    <table width="100%" border="0" cellpadding="3">
      <tr>
        <td>
          <center>
            <h2><u>CIRCULAR<br>N° {{ $circular }}<u></h2>
          </center>
          <div class="texto"><b>A LOS HH:. DE LAS LOGIAS DE LA OBEDIENCIA:</b><br>
            Hermanos:<br>
            <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Por disposición del Muy Respetable Gran Maestro y conforme a lo dispuesto por el Artículo. 75°, Inc. 3 del REGLAMENTO DE LAS LOGIAS
              SIMBÓLICAS, os damos a conocer la nómina, antecedentes y fotografías de los profanos propuestos en las Logias de la Obediencia, la misma que debe ser leida en Tenida de
              PRIMER GRADO en su totalidad, ademas, DEBE SER EXPUESTA EN LOS VITRALES O EN CUALQUIER LUGAR DE VUESTRAS SECRETARIAS DONDE SEA VISTA POR LOS HH:. QUE LAS VISITAN,
              CON EL OBJETO DE QUE TODOS PODAMOS TENER ACCESO A LA INFORMACIÓN DE LA MISMA.</p>
          </div>
        </td>
      </tr>
    </table>
    <div>
      {!! $contenido !!}
    </div>
    <p>

    <table width="100%" border="0" cellpadding="0">
      <tr>
        <td>
          <p>Si tenéis alguna observación que hacer, os solicitamos darla a conocer a vuestro Venerable Maestro o a la Gran Secretaria General de la Gran Logia de Bolivia.</p>
          <p><b>TODA LA INFORMACIÓN RECIBIDA SERA TRATADA CON CARACTER CONFIDENCIAL Y ESTRICTAMENTE RESERVADA.</b></p>
          <p>Con este especial motivo, os hacemos llegar el saludo fraternal de la {{ $region }} del Valle de {{ $valle }}</p>
        </td>
      </tr>
      <tr>
        <td>
          <center>
            <p><span style="font-variant: small-caps"><b>PIENSA, SUEÑA, CREE Y ATRÉVETE...</b></span></p>
          </center><br>
        </td>
      </tr>
      <tr>
        <td><br><br><br><br>
          <center>{{ $auto }}<br>VALLE DE {{ strtoupper($valle) }}</center>
        </td>
      </tr>
    </table>
    </p>
  </main>

</body>

</html>
