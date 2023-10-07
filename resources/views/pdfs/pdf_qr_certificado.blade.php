<!DOCTYPE html>
<html>

<head>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Aref+Ruqaa+Ink:wght@700&family=Felipa&display=swap" rel="stylesheet">
  <style>
    @page {
      margin-left: 0.5cm;
      margin-right: 0.6cm;
      margin-top: 0.5cm;
      margin-bottom: 0;
    }

    body {
      font-family: 'Felipa', cursive;
      font-size: 16px;
      line-height: 1;
      text-align: justify;
      font-weight: 400;
    }

    span {
      font-family: 'Aref Ruqaa Ink', serif;
    }

    .firma {
      font-family: 'Felipa', cursive;
      font-size: 14px;
      line-height: 0.7;
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
    <br>
    <blockquote>
        <blockquote>
        <div style="text-align: justify">
          <p><center><h3>A todos los Masones Regulares <br> Salud - Fuerza - Unión </h3></center></p>
          <p>En virtud a la petición hecha por la
            <span>
              {{ $taller }}
            </span>.</b>
          </p>
          <p>Nos, Dignatarios de la <span>GRAN LOGIA DE PARAGUAY</span>, le concedemos el presente <span>
              {{ $tipocert }}
            </span> para que pueda gozar de todos los derechos y prerrogativas inherentes a su grado, al hermano:</p>
          <center>
            <span style="text-transform:uppercase;">
              {{ $suscrito }}
            </span>
          </center>
          <p>Constatándonos que recibió el
            {{ $ritocert }}.
          </p>
          <p>Es dado en el Valle de
            {{ strtoupper($nvalle) }}, Bolivia a los
            {{ $fechacert }} e:.v:.
          </p>
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              {{-- <td width="33%" align="center" valign="middle"><img src="media/qr-jjc.png" width="82"><img src="media/gsg-jjc.jpg" width="110"></td> --}}
              <td width="33%" align="center" valign="middle"><img src="{{ $firma2qr }}" width="70"><img src="media/gsg-jjc.jpg" width="110"></td>
              {{-- <td width="34%" align="center" valign="middle"><img src="media/qr-rao.png" width="82"><img src="media/gm-rao.jpg" width="110"><br> --}}
                <td width="34%" align="center" valign="middle"><img src="{{ $firma1qr }}" width="70"><img src="media/gm-rao.jpg" width="110"><br>
                <div class="firma">{!! $firma1 !!}</div>
              </td>
              {{-- <td width="33%" align="center" valign="middle"><img src="media/qr-ecb.png" width="82"><img src="media/gt-ecb.jpg" width="110"></td> --}}
              <td width="33%" align="center" valign="middle"><img src="{{ $firma3qr }}" width="70"><img src="media/gt-ecb.jpg" width="110"></td>
            </tr>
            <tr>
              <td width="33%" align="center" valign="middle">
                <div class="firma">{!! $firma2 !!}</div>
              </td>
              <td width="34%" align="center" valign="middle"></td>
              <td width="33%" align="center" valign="middle">
                <div class="firma">{!! $firma3 !!}</div>
              </td>
            </tr>
          </table>
          <p>Nos, Dignatarios de esta <span>
              {{ $tipogdr }}
            </span> hacemos constar que el presente Certificado ha sido entregado.
          </p>
          <p>&nbsp;</p>
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td width="36%" align="center" valign="middle"></td>
              <td width="32%" align="center" valign="middle" class="firma"></td>
              <td width="32%" align="center" valign="middle" class="firma">
                {{ $gdr1 }}
              </td>
              <td width="36%" align="center" valign="middle"></td>
            </tr>
            <td width="36%" align="center" valign="middle"></td>
              <td width="32%" align="center" valign="middle" class="firma">
                {{ $gdr2 }}
              </td>
              <td width="32%" align="center" valign="middle" >
                <td width="36%" align="center" valign="middle" class="firma">
                {{ $gdr3 }}
                </td>
            <tr>

            </tr>
          </table>
          <br>
          <<!--{!! $salto !!}-->
        </div>
      </blockquote>
    </blockquote>
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="30%" align="center" valign="middle">&nbsp;</td>
        <td width="18%" align="center" valign="bottom" class="firma"><br>Venerable Maestro</td>
        <td width="15%" align="center" valign="bottom" class="firma"><br>Ne Varietur</td>
        <td width="14%" align="center" valign="bottom" class="firma"><br>Secretario</td>
        <td width="23%" valign="top" align="center"><img src="{{ $imaqr }}" width="100"></td>
      </tr>
    </table>
  </div>
</body>

</html>
