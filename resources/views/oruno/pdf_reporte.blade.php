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

    @page {
      margin: 0cm 0cm;
    }

    body {
      font-family: Helvetica, Arial, sans-serif;
      font-size: 12px;
      line-height: 1.42857143;
      color: #333;
      background-color: #fff;
      margin-top: 3.1cm;
      margin-left: 1cm;
      margin-right: 1cm;
      margin-bottom: 1cm;
    }

    header {
      position: fixed;
      top: 0.5cm;
      left: 1.0cm;
      right: 0cm;
      height: 3cm;
    }

    footer {
      position: fixed;
      bottom: 0cm;
      left: 1.4cm;
      right: 0cm;
      height: 1cm;
    }

    .pagenum:before {
      content: counter(page);
    }

    #pagenumber:before {
      content: "Pag. " counter(page);
    }
  </style>
</head>

<body>
  <header>
    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="mitable">
      <tr>
        <td width="10%" align="center" valign="middle"><img width="90" src="media/{{ $logo }}"></td>
        <td width="90%">
          <div align="center" style="font-size:12px; line-height: 0.8;">
            <h2>GRAN LOGIA SIMBÓLICA DEL PARAGUAY</h2>
            <h4>{{ $titulo }} </h4>
            <h4> {{ $subtitulo }} - <span id="pagenumber" /></span></h4>
          </div>
        </td>
      </tr>
    </table>
  </header>
  <main>
    <div style="font-size:10px; line-height: 1;">
      <table width="100%" cellspacing="0" cellpadding="2" border="1" align="center" style="font-size: 9px;">
        <thead>
          <tr style="text-align:center;background-color:#cccccc;">
            <?php
            foreach ($cabecera as $ttt) {
                echo "<th style=\"text-align:center;\">$ttt</th>";
            }
            ?>
          </tr>
        </thead>
        <tbody>
          <?php
          foreach ($items as $cols) {
              echo '<tr style=\"text-align:center;\">';
              foreach ($campos as $fils) {
                  echo '<td style=\"text-align:center;\" width=\"200\">' . $cols[$fils] . '</td>';
              }
              echo '</tr>';
          }
          ?>
        </tbody>
      </table>
    </div>
  </main>
  <footer>
  </footer>
</body>
</html>
