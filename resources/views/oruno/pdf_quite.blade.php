<!DOCTYPE html>
<html>
<meta charset="utf-8">

<head>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Aref+Ruqaa+Ink:wght@700&family=Felipa&display=swap" rel="stylesheet">
  <style>
    @page {
      margin-left: 0.5cm;
      margin-right: 0.5cm;
      margin-top: 2cm;
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
      font-size: 18px;
      line-height: 1;
      font-family: 'Aref Ruqaa Ink', serif;
      font-weight: 400;
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

    <blockquote>
        <center><img src="quite-cabecera.jpg" height="350"></center>
      <blockquote>
        <table width="100%" border="0" cellpadding="0">
          <tr>
            <td>
              <center>
                <div class="texto"><p>Os hacemos saber que la Aug:. y Resp:. Log:.</p></div>
                <div><span>{{ $logia }}</span></div>
                <div class="texto">
                  <p>de esta Obed:. Simb:., ha otorgado la Plancha de Quite y Placet al Q:.H:.</p>
                </div>
                <div>
                  <span>{{ $nombre }}</span>
                </div>
              </center>
              <div class="texto">
                <center>
                  <p>Quien se encuentra en pleno goce de sus Derechos Masónicos y en cumplimiento de todas las Leyes y costumbres, que nuestros antiguos usos y reglamentos marcan.</p>
                  <p>Por tanto, a la <b>Gran Logia Simbólica del Paraguay</b> le honra recomendar a toda la Fraternidad Masónica del Universo, al Muy Q:. H:.,
                    quien sostiene las CCol: del Temp:. de la Virtud y Sabiduría con entusiasmo, lealtad, cariño y con la firmeza que señalan nuestros Principios.</p>
                  <p>Para constancia de las RResp:. LLog: que integran la Gran Familia Universal, extendemos, firmamos y sellamos la presente, en nuestro Temp:. Masónico del Vall:. de Asunción,
                    Or: de la República del Paraguay, a los {{ $fechacert }} (E:.V:.)
                  </p>
                </center>
              </div>
            </td>
          </tr>
        </table>
      </blockquote>
    </blockquote>
    <br>
    <br>
    <br>
    <br>
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="15%" align="center" valign="bottom" class="firma"><br>Gr:.Sec:.</td>
        <td width="14%" align="center" valign="bottom" class="firma"><br>Ser:. Gr:. Maes:.</td>
      </tr>
    </table>
  </div>
</body>

</html>
