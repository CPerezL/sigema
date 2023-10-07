<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Session;
use PDF;

trait ReporteTrait
{
    /**
     * reportes
     * trait para crar reportes en idea
     *
     * @param Request $request
     */

    public function crearReportePDF($titulo,$subtitulo, $cabecera, $cuerpo, $campos, $nombre = 'Reporte',$forma='portrait')
    {
        if (count($campos) > 0) {
            $data['titulo'] = $titulo;
            $data['subtitulo'] = $subtitulo;
            $data['cabecera'] = $cabecera;
            $data['items'] = $cuerpo;
            $data['campos'] = $campos;
            $nomefile = 'GLSP-' . $nombre;
            // Load all views as normal
            $data['logo'] = 'glsp-150.png';
            $pdf = PDF::loadView('oruno.pdf_reporte', $data);
            $pdf->set_paper('Letter', $forma);
            return $pdf->download($nomefile . '.pdf');
        } else {
            abort(419);
        }
    }
}
