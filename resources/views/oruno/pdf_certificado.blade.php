<!DOCTYPE html>
<html>

<head>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100&family=UnifrakturCook:wght@700&display=swap" rel="stylesheet">
  <style>
    @page {
      margin-left: 0cm;
      margin-right: 0cm;
      margin-top: 1.5cm;
      margin-bottom: 0cm;
    }

    body {
      font-size: 16px;
      line-height: 1;
      text-align: justify;
      font-weight: 400;
      font-family: 'Roboto', sans-serif;
      background-image: url('certificado.jpg');
      background-position: top left;
      background-repeat: no-repeat;
      background-size: 100%;
      padding: 0;
      margin: 0;
      /* padding: 300px 100px 10px 100px; */
      width: 100%;
      height: 100%;
    }

    .texto-titulo {
      font-family: 'Roboto', sans-serif;
      font-size: 16px;
      font-weight: 700;
    }

    .texto-dato {
      font-size: 20px;
      font-family: 'UnifrakturCook', cursive;
    }

    .texto-dato2 {
      font-size: 20px;
      font-family: 'UnifrakturCook', cursive;
    }

    .firma {
      font-family: 'Roboto', sans-serif;
      font-size: 14px;
      color: lightgray;
      font-style: italic;
    }

    .texto-titulo2 {
      font-family: 'Roboto', sans-serif;
      font-size: 16px;
      font-weight: 700;
      line-height: 1.3;
    }
  </style>
</head>

<body>
  <div>
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="70%" align="center" valign="middle"></td>
        <td width="30%" valign="top" align="right"><span>
            <!--{{ $numecert }}-->
          </span></td>
      </tr>
    </table>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <div>
      <p>
        <center>
          <spam class="texto-titulo">El Q.·.H.·.</span><br><span class="texto-dato">{{ $suscrito }}</spam>
          <br><br>
          <spam class="texto-titulo">Posee el Grado de</span><br><span class="texto-dato">{{ $tipocert }}</spam>
          <br><br>
          <spam class="texto-titulo">{{ $casotxt }}</span><br><span class="texto-dato2">"{{ $taller }}"</spam>
          <br><br>
          <spam class="texto-titulo2">En Ten.·. Mag.·. del dia<br>{{ $fechacert }}</span><br>
            <span class="texto-dato2">Vall.·. de {{ $nvalle }}, Or.·. del Paraguay
          </spam>
        </center>
      </p>
    </div>
    <br><br><br>
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="20%" align="center" valign="middle">&nbsp;</td>
        <td width="20%" align="center" valign="bottom" class="firma">&nbsp;</td>
        <td width="20%" align="center" valign="bottom" class="firma"><br>Ven.·. Maes.·.</td>
        <td width="20%" align="center" valign="bottom" class="firma"><br>&nbsp;</td>
        <td width="20%" valign="top" align="center">&nbsp;</td>
      </tr>
      <tr>
        <td width="20%" align="center" valign="middle">&nbsp;</td>
        <td width="20%" align="center" valign="bottom" class="firma"><br>Sec.·.</td>
        <td width="20%" align="center" valign="bottom" class="firma"><br>&nbsp;</td>
        <td width="20%" align="center" valign="bottom" class="firma"><br>Orad.·.</td>
        <td width="20%" valign="top" align="center">&nbsp;</td>
      </tr>
    </table>
    <br><br>
    <br><br><br>
    <br><br><br>
    <br><br><br>
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="28%" align="center" valign="middle">&nbsp;</td>
        <td width="30%" align="center" valign="bottom" class="firma">Gr.·. Sec.·.</td>
        <td width="30%" align="center" valign="bottom" class="firma">Gr.·. Maes.·.</td>
        <td>&nbsp;</td>
      </tr>
    </table>
  </div>
</body>

</html>
