<?php

namespace App\Http\Controllers;

use App\Models\HistoriaPsicologica;
use App\Models\HistoriaNeuroPsicologica;
use App\Models\Cotizaciones;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Pacientes;
use App\Models\Servicios;
use Dompdf\Dompdf;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


class PacientesController extends Controller
{
    public function Pacientes()
    {
        if (Auth::check()) {
            $bandera = "";
            return view('Pacientes.gestionarPacientes', compact('bandera'));
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function consultas()
    {
        $consultas = Pacientes::listConsultas();
        return response()->json($consultas);
    }

    public function imprimirCotizacion(Request $request)
    {
        if (Auth::check()) {
            $pdf = new Dompdf();

            $idCotizacion = $request->input('idCotizacion');
            $cotizacion = Cotizaciones::buscaCotizacion($idCotizacion);
            $detalles = Cotizaciones::buscaDetallesCotizacion($idCotizacion);


            $logoPath = public_path('app-assets/images/logo/logo_prasca.png');

            // Convertir la imagen a base64
            $logoData = base64_encode(file_get_contents($logoPath));
            $logo = 'data:image/png;base64,' . $logoData;

            $fechaElaboracion = \Carbon\Carbon::parse($cotizacion->fecha)->format('d/m/Y h:i A');

            $paciente = Pacientes::busquedaPaciente($cotizacion->paciente);

            $fechaNacimiento = \Carbon\Carbon::parse($paciente->fecha_nacimiento)->format('d/m/Y h:i A');


            $html = '<head>
                <style>
                  @page {
                        margin: 20mm;
                    }
                    
                    body {
                        position: relative;
                    }
                    
                    /* Marca de agua con el logo */
                    body::before {
                        content: "";
                        position: fixed;
                        top: 50%;
                        left: 50%;
                        transform: translate(-50%, -50%);
                        width: 500px;
                        height: 500px;
                        background-image: url(' . $logo . ');
                        background-size: contain;
                        background-repeat: no-repeat;
                        background-position: center;
                        opacity: 0.2;
                        z-index: -1;
                        pointer-events: none;
                    }
                    
                    /* Contenido principal */
                    .content {
                        position: relative;
                        z-index: 1;
                    }
                    
                    table {
                        width: 100%;
                        border-collapse: collapse;
                        border-width: 0.1px;
                    }
                    th, td {
                        border: 0.1px solid black;
                        padding: 4px;
                    }
                    th {
                        background-color: #EAEBF4;
                       
                        font-size: 13px;
                        line-height: 19px;
                        text-align: left;
                    }
                    tr:nth-child(even) {
                        background-color: #f2f2f2;
                        opacity: 0.5;
                         font-size: 13px;
                    }
                    .no-border {
                        border: none;
                        text-align: center;
                    }
                    hr {
                        border-width: 0.1px;
                        border-color: #333;
                        border-style: solid;
                    }

                     td {
                        font-size: 13px;
                        line-height: 19px;
                        text-align: left;
                    }
                        
                    section {
                        margin-bottom: 10px;
                    }

                    .section p {
                        margin: 0;
                        padding: 0;
                    }
                </style>
            </head>';

            $html .= '<div class="content">';
            $html .= '<table style="width:100%; border-collapse: collapse; background-color: transparent;">';
            $html .= '<tr>';
            $html .= '<td class="no-border" style="padding: 0;"><img src="' . $logo . '" style="width: 200px; height: auto;"></td>';
            $html .= '<td class="no-border" style="padding: 0; vertical-align: top;">';
            $html .= '<p style="margin: 0;">DRA. MARIA ISABEL PUMAREJO</p>';
            $html .= '<p style="margin: 0;">PSICÒLOGA CLINICA - T.P. No. 259542</p>';
            $html .= '<p style="margin: 0;">Calle 11 # 11 - 07 San Joaquin</p>';
            $html .= '<p style="margin: 0;">Teléfono: 312 5678078</p>';
            $html .= '</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td class="no-border" colspan="2" style="text-align: center; padding: 1px;background-color: transparent;"> <h3>COTIZACIÓN DE SERVICIOS #' . str_pad($cotizacion->id, 4, '0', STR_PAD_LEFT) . '</h3></td>';
            $html .= '</tr>';
            $html .= '</table>';

            $html .= '<table>
                    <tr>
                        <td ><b>FECHA DE ELABORACIÓN:</b> ' . $fechaElaboracion . '</td>
                    </tr>
                </table>';

            $html .= '<div class="section" >
                <h4 style="background-color: #EAEBF4; padding: 6px; margin-bottom: 10px; opacity: 0.8;">1. DATOS DE IDENTIFICACIÓN DEL PACIENTE</h4>
                <table style="width: 100%; border-collapse: collapse; border: none;">
                    <tr>
                        <td colspan="3" style="border: none; padding: 8px 4px 4px 4px;">
                            <span style="font-weight: bold; width: 120px; display: inline-block; text-align: right; margin-right: 10px;">NOMBRE:</span>
                            <span style="border-bottom: 1px solid #ccc; min-width: 200px; display: inline-block;">' .
                $paciente->primer_nombre . ' ' .
                $paciente->segundo_nombre . ' ' .
                $paciente->primer_apellido . ' ' .
                $paciente->segundo_apellido .
                '</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" style="border: none; padding: 8px 4px 4px 4px;">
                            <span style="font-weight: bold; width: 120px; display: inline-block; text-align: right; margin-right: 10px;">NACIMIENTO:</span>
                            <span style="border-bottom: 1px solid #ccc; min-width: 200px; display: inline-block;">' .
                $fechaNacimiento . ' - ' . $paciente->lugar_nacimiento .
                '</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="border: none; padding: 8px 4px 4px 4px; width: 40%;">
                            <span style="font-weight: bold; width: 120px; display: inline-block; text-align: right; margin-right: 10px;">IDENTIFICACIÓN:</span>
                            <span style="border-bottom: 1px solid #ccc; min-width: 150px; display: inline-block;">' .
                $paciente->tipo_identificacion . ' ' . $paciente->identificacion .
                '</span>
                        </td>
                        <td style="border: none; padding: 8px 4px 4px 4px; width: 30%;">
                            <span style="font-weight: bold; width: 60px; display: inline-block; text-align: right; margin-right: 10px;">EDAD:</span>
                            <span style="border-bottom: 1px solid #ccc; min-width: 80px; display: inline-block;">' .
                $paciente->edad .
                '</span>
                        </td>
                        <td style="border: none; padding: 8px 4px 4px 4px; width: 30%;">
                            <span style="font-weight: bold; width: 60px; display: inline-block; text-align: right; margin-right: 10px;">SEXO:</span>
                            <span style="border-bottom: 1px solid #ccc; min-width: 80px; display: inline-block;">' .
                (($paciente->sexo === "H") ? "Masculino" : "Femenino") .
                '</span>
                        </td>
                    </tr>
                </table>
            </div>';

            $html .= '<div class="section">
                    <h4 style="background-color: #EAEBF4; padding: 6px; margin-bottom: 10px; opacity: 0.8;">2. DETALLE DE LA COTIZACIÓN</h4>
                </div>';

            $html .= '<div class="section">
                <table style="width:100%; border-collapse: collapse; border: none;">';
            $html .= '
                <thead>
                    <tr>
                        <th style="width: 40%;">Servicio</th>
                        <th style="width: 10%;">Cantidad</th>
                        <th style="width: 10%;">Valor Original</th>
                        <th style="width: 10%;">Valor Final</th>
                        <th style="width: 10%;">Desc/Unidad</th>
                        <th style="width: 10%;">Desc Total</th>
                        <th style="width: 10%;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>';

            $totalGeneral = 0;

            foreach ($detalles as $detalle) {
                $cantidad = $detalle->cantidad;
                $valorFinal = $detalle->valor_final;
                $valorOriginal = $detalle->valor_original ?? $valorFinal; // fallback
                $descUnidad = $valorOriginal - $valorFinal;
                $descTotal = $descUnidad * $cantidad;
                $subtotal = $valorFinal * $cantidad;

                $totalGeneral += $subtotal;

                $html .= '<tr>';
                $html .= '<td>' . htmlspecialchars($detalle->servicio->descripcion) . '</td>';
                $html .= '<td style="text-align: center;">' . $cantidad . '</td>';
                $html .= '<td style="text-align: right;">$ ' . number_format($valorOriginal, 0, ',', '.') . '</td>';
                $html .= '<td style="text-align: right;">$ ' . number_format($valorFinal, 0, ',', '.') . '</td>';
                $html .= '<td style="text-align: right;">$ ' . number_format($descUnidad, 0, ',', '.') . '</td>';
                $html .= '<td style="text-align: right;">$ ' . number_format($descTotal, 0, ',', '.') . '</td>';
                $html .= '<td style="text-align: right;">$ ' . number_format($subtotal, 0, ',', '.') . '</td>';
                $html .= '</tr>';
            }

            $html .= '<tr>
                <td colspan="6" style="border: none; padding: 8px 4px 4px 4px; text-align: right; font-weight: bold;">DESCUENTO:</td>
                <td style="border: none; padding: 8px 4px 4px 4px; text-align: right; font-weight: bold;">$ ' . number_format($cotizacion->descuento, 2, ',', '.') . '</td>
            </tr>';
            $html .= '<tr>
                <td colspan="6" style="border: none; padding: 8px 4px 4px 4px; text-align: right; font-weight: bold;">TOTAL:</td>
                <td style="border: none; padding: 8px 4px 4px 4px; text-align: right; font-weight: bold;">$ ' . number_format($totalGeneral, 2, ',', '.') . '</td>
            </tr>';

            $html .= '</tbody></table>
            </div>';

            $pdf->loadHtml($html);
            $pdf->setPaper('A3', 'portrait');
            $pdf->render();

            $pdfContent = $pdf->output();

            // Encabezados de respuesta para el archivo PDF
            $headers = [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="cotizacion_servicios.pdf"',
            ];


            $pdfContent = $pdf->output();

            return response($pdfContent, 200, $headers);
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function enviarCotizacion(Request $request)
    {
        if (Auth::check()) {
            $pdf = new Dompdf();

            $idCotizacion = $request->input('idCotizacion');
            $cotizacion = Cotizaciones::buscaCotizacion($idCotizacion);
            $detalles = Cotizaciones::buscaDetallesCotizacion($idCotizacion);


            $logoPath = public_path('app-assets/images/logo/logo_prasca.png');

            // Convertir la imagen a base64
            $logoData = base64_encode(file_get_contents($logoPath));
            $logo = 'data:image/png;base64,' . $logoData;

            $fechaElaboracion = \Carbon\Carbon::parse($cotizacion->fecha)->format('d/m/Y h:i A');

            $paciente = Pacientes::busquedaPaciente($cotizacion->paciente);

            $fechaNacimiento = \Carbon\Carbon::parse($paciente->fecha_nacimiento)->format('d/m/Y h:i A');


            $html = '<head>
                <style>
                       @page {
                        margin: 20mm;
                    }
                    
                    body {
                        position: relative;
                    }
                    
                    /* Marca de agua con el logo */
                    body::before {
                        content: "";
                        position: fixed;
                        top: 50%;
                        left: 50%;
                        transform: translate(-50%, -50%);
                        width: 400px;
                        height: 400px;
                        background-image: url(' . $logo . ');
                        background-size: contain;
                        background-repeat: no-repeat;
                        background-position: center;
                        opacity: 0.2;
                        z-index: -1;
                        pointer-events: none;
                    }
                    
                    /* Contenido principal */
                    .content {
                        position: relative;
                        z-index: 1;
                    }
                    table {
                        width: 100%;
                        border-collapse: collapse;
                        border-width: 0.1px;
                    }
                    th, td {
                        border: 0.1px solid black;
                        padding: 4px;
                    }
                    th {
                        background-color: #EAEBF4;
                    }
                    tr:nth-child(even) {
                        background-color: #f2f2f2;
                    }
                    .no-border {
                        border: none;
                        text-align: center;
                    }
                    hr {
                        border-width: 0.1px;
                        border-color: #333;
                        border-style: solid;
                    }
                        
                    section {
                        margin-bottom: 10px;
                    }

                    .section p {
                        margin: 0;
                        padding: 0;
                    }
                </style>
            </head>';

            $html .= '<div class="content">';
            $html .= '<table style="width:100%; border-collapse: collapse; background-color: transparent;">';
            $html .= '<tr>';
            $html .= '<td class="no-border" style="padding: 0;"><img src="' . $logo . '" style="width: 200px; height: auto;"></td>';
            $html .= '<td class="no-border" style="padding: 0; vertical-align: top;">';
            $html .= '<p style="margin: 0;">DRA. MARIA ISABEL PUMAREJO</p>';
            $html .= '<p style="margin: 0;">PSICÒLOGA CLINICA - T.P. No. 259542</p>';
            $html .= '<p style="margin: 0;">Calle 11 # 11 - 07 San Joaquin</p>';
            $html .= '<p style="margin: 0;">Teléfono: 312 5678078</p>';
            $html .= '</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td class="no-border" colspan="2" style="text-align: center; padding: 1px;background-color: transparent;"> <h3>COTIZACIÓN DE SERVICIOS #' . str_pad($cotizacion->id, 4, '0', STR_PAD_LEFT) . '</h3></td>';
            $html .= '</tr>';
            $html .= '</table>';

            $html .= '<table>
                    <tr>
                        <td ><b>FECHA DE ELABORACIÓN:</b> ' . $fechaElaboracion . '</td>
                    </tr>
                </table>';

            $html .= '<div class="section" >
                <h4 style="background-color: #EAEBF4; padding: 6px; margin-bottom: 10px; opacity: 0.8;">1. DATOS DE IDENTIFICACIÓN DEL PACIENTE</h4>
                <table style="width: 100%; border-collapse: collapse; border: none;">
                    <tr>
                        <td colspan="3" style="border: none; padding: 8px 4px 4px 4px;">
                            <span style="font-weight: bold; width: 120px; display: inline-block; text-align: right; margin-right: 10px;">NOMBRE:</span>
                            <span style="border-bottom: 1px solid #ccc; min-width: 200px; display: inline-block;">' .
                $paciente->primer_nombre . ' ' .
                $paciente->segundo_nombre . ' ' .
                $paciente->primer_apellido . ' ' .
                $paciente->segundo_apellido .
                '</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" style="border: none; padding: 8px 4px 4px 4px;">
                            <span style="font-weight: bold; width: 120px; display: inline-block; text-align: right; margin-right: 10px;">NACIMIENTO:</span>
                            <span style="border-bottom: 1px solid #ccc; min-width: 200px; display: inline-block;">' .
                $fechaNacimiento . ' - ' . $paciente->lugar_nacimiento .
                '</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="border: none; padding: 8px 4px 4px 4px; width: 40%;">
                            <span style="font-weight: bold; width: 120px; display: inline-block; text-align: right; margin-right: 10px;">IDENTIFICACIÓN:</span>
                            <span style="border-bottom: 1px solid #ccc; min-width: 150px; display: inline-block;">' .
                $paciente->tipo_identificacion . ' ' . $paciente->identificacion .
                '</span>
                        </td>
                        <td style="border: none; padding: 8px 4px 4px 4px; width: 30%;">
                            <span style="font-weight: bold; width: 60px; display: inline-block; text-align: right; margin-right: 10px;">EDAD:</span>
                            <span style="border-bottom: 1px solid #ccc; min-width: 80px; display: inline-block;">' .
                $paciente->edad .
                '</span>
                        </td>
                        <td style="border: none; padding: 8px 4px 4px 4px; width: 30%;">
                            <span style="font-weight: bold; width: 60px; display: inline-block; text-align: right; margin-right: 10px;">SEXO:</span>
                            <span style="border-bottom: 1px solid #ccc; min-width: 80px; display: inline-block;">' .
                (($paciente->sexo === "H") ? "Masculino" : "Femenino") .
                '</span>
                        </td>
                    </tr>
                </table>
            </div>';

            $html .= '<div class="section">
                    <h4 style="background-color: #EAEBF4; padding: 6px; margin-bottom: 10px;">2. DETALLE DE LA COTIZACIÓN</h4>
                </div>';

            $html .= '<div class="section">
                <table style="width:100%; border-collapse: collapse; border: none;">';
            $html .= '
                <thead>
                    <tr>
                        <th style="width: 40%;">Servicio</th>
                        <th style="width: 10%;">Cantidad</th>
                        <th style="width: 10%;">Valor Original</th>
                        <th style="width: 10%;">Valor Final</th>
                        <th style="width: 10%;">Desc/Unidad</th>
                        <th style="width: 10%;">Desc Total</th>
                        <th style="width: 10%;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>';

            $totalGeneral = 0;

            foreach ($detalles as $detalle) {
                $cantidad = $detalle->cantidad;
                $valorFinal = $detalle->valor_final;
                $valorOriginal = $detalle->valor_original ?? $valorFinal; // fallback
                $descUnidad = $valorOriginal - $valorFinal;
                $descTotal = $descUnidad * $cantidad;
                $subtotal = $valorFinal * $cantidad;

                $totalGeneral += $subtotal;

                $html .= '<tr>';
                $html .= '<td>' . htmlspecialchars($detalle->servicio->descripcion) . '</td>';
                $html .= '<td style="text-align: center;">' . $cantidad . '</td>';
                $html .= '<td style="text-align: right;">$ ' . number_format($valorOriginal, 0, ',', '.') . '</td>';
                $html .= '<td style="text-align: right;">$ ' . number_format($valorFinal, 0, ',', '.') . '</td>';
                $html .= '<td style="text-align: right;">$ ' . number_format($descUnidad, 0, ',', '.') . '</td>';
                $html .= '<td style="text-align: right;">$ ' . number_format($descTotal, 0, ',', '.') . '</td>';
                $html .= '<td style="text-align: right;">$ ' . number_format($subtotal, 0, ',', '.') . '</td>';
                $html .= '</tr>';
            }

            $html .= '<tr>
                <td colspan="6" style="border: none; padding: 8px 4px 4px 4px; text-align: right; font-weight: bold;">DESCUENTO:</td>
                <td style="border: none; padding: 8px 4px 4px 4px; text-align: right; font-weight: bold;">$ ' . number_format($cotizacion->descuento, 2, ',', '.') . '</td>
            </tr>';
            $html .= '<tr>
                <td colspan="6" style="border: none; padding: 8px 4px 4px 4px; text-align: right; font-weight: bold;">TOTAL:</td>
                <td style="border: none; padding: 8px 4px 4px 4px; text-align: right; font-weight: bold;">$ ' . number_format($totalGeneral, 2, ',', '.') . '</td>
            </tr>';

            $html .= '</tbody></table>
            </div>';


            $pdf->loadHtml($html);
            $pdf->setPaper('A3', 'portrait');
            $pdf->render();

            $pdfContent = $pdf->output();

            if ($paciente->email == "" || $paciente->email == null) {
                return response()->json(['resultado' => "noCorreo"]);
            } else {

                //enviar al correo del paciente el pdf
                $mail = new PHPMailer(true);
                $mensaje = '<p>Esperamos que este mensaje le encuentre bien.</p>
                
                <p>Adjunto encontrará la cotización detallada de los servicios solicitados en <strong>Prasca Center</strong>. Esta cotización incluye todos los servicios, procedimientos y costos asociados que fueron discutidos durante su consulta.</p>
                
                <p><strong>Información importante:</strong></p>
                <ul>
                    <li>Esta cotización tiene una validez de <strong>30 días</strong> a partir de la fecha de emisión</li>
                    <li>Los precios están sujetos a cambios sin previo aviso después del período de validez</li>
                    <li>Para confirmar su cita o realizar alguna modificación, puede contactarnos al teléfono o WhatsApp que aparece en la cotización</li>
                </ul>
                
                <p>Si tiene alguna pregunta sobre los servicios incluidos o desea realizar modificaciones, no dude en contactarnos. Estamos aquí para ayudarle.</p>
                
                <p>Gracias por confiar en <strong>Prasca Center</strong> para su atención psicológica.</p>
                
                <p>Saludos cordiales,<br>
                <strong>Equipo Prasca Center</strong></p>';

                $asunto = 'Cotización de Servicios - Prasca Center - Válida por 30 días';

                $contenido = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
                    <html xmlns='http://www.w3.org/1999/xhtml'>
                    <head>
                    <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
                    <meta name='viewport' content='width=device-width, initial-scale=1' />
                    <title>Narrative Invitation Email</title>
                    <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css'>
                    <script src='https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js'></script>
                    <script src='https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js'></script>
                    <script src='https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js'></script>
                    <style type='text/css'>
            
                    /* Take care of image borders and formatting */
            
                    img {
                        max-width: 600px;
                        outline: none;
                        text-decoration: none;
                        -ms-interpolation-mode: bicubic;
                    }
            
                    a {
                        border: 0;
                        outline: none;
                    }
            
                    a img {
                        border: none;
                    }
            
                    /* General styling */
            
                    td, h1, h2, h3  {
                        font-family: Helvetica, Arial, sans-serif;
                        font-weight: 400;
                    }
            
                    td {
                        font-size: 13px;
                        line-height: 19px;
                        text-align: left;
                    }
            
                    body {
                        -webkit-font-smoothing:antialiased;
                        -webkit-text-size-adjust:none;
                        width: 100%;
                        height: 100%;
                        color: #37302d;
                        background: #ffffff;
                    }
            
                    table {
                        border-collapse: collapse !important;
                    }
            
            
                    h1, h2, h3, h4 {
                        padding: 0;
                        margin: 0;
                        color: #444444;
                        font-weight: 400;
                        line-height: 110%;
                    }
            
                    h1 {
                        font-size: 35px;
                    }
            
                    h2 {
                        font-size: 30px;
                    }
            
                    h3 {
                        font-size: 24px;
                    }
            
                    h4 {
                        font-size: 18px;
                        font-weight: normal;
                    }
            
                    .important-font {
                        color: #21BEB4;
                        font-weight: bold;
                    }
            
                    .hide {
                        display: none !important;
                    }
            
                    .force-full-width {
                        width: 100% !important;
                    }
            
                    .rps_16ec table#x_main-wrapper {
                        border-collapse: collapse;
                        border-spacing: 0;
                        border: none;
                        margin: 0 auto;
                        width: 100%;
                      }
            
                      .rps_16ec #x_greeting {
                        text-align: center;
                      }
            
                      .rps_16ec table.x_appt-data {
                        width: auto;
                        margin: 0 auto;
                      }
            
                      .rps_16ec .x_data-row {
                        margin: 0 auto;
                        width: auto;
                      }
            
                      .rps_16ec .x_appt-data tr:first-child td {
                        padding-top: 12px;
                      }
            
                      .rps_16ec .x_data-row .x_label {
                        width: 25%;
                        font-weight: bold;
                        color: #0097cc;
                        text-align: right;
                      }
            
                      .rps_16ec .x_header td {
                        background: #0097cc;
                        padding: 3px;
                        color: #fafafa;
                        text-align: center;
                      }
            
                      .rps_16ec #x_initial-text {
                        padding: 18px 0;
                        line-height: 1.4em;
                      }
            
                      .rps_16ec .x_appt-data tr:first-child td {
                        padding-top: 12px;
                      }
                      .rps_16ec .x_data-row .x_label, .rps_16ec .x_data-row .x_data {
                        padding: 4px;
                          padding-top: 4px;
                      }
            
                    </style>
            
                    <style type='text/css' media='screen'>
                        @media screen {
                            @import url(http://fonts.googleapis.com/css?family=Open+Sans:400);
            
                            /* Thanks Outlook 2013! */
                            td, h1, h2, h3 {
                            font-family: 'Open Sans', 'Helvetica Neue', Arial, sans-serif !important;
                            }
                        }
                    </style>
            
                    <style type='text/css' media='only screen and (max-width: 600px)'>
                        /* Mobile styles */
                        @media only screen and (max-width: 600px) {
            
                        table[class='w320'] {
                            width: 320px !important;
                        }
            
                        table[class='w300'] {
                            width: 300px !important;
                        }
            
                        table[class='w290'] {
                            width: 290px !important;
                        }
            
                        td[class='w320'] {
                            width: 320px !important;
                        }
            
                        td[class~='mobile-padding'] {
                            padding-left: 14px !important;
                            padding-right: 14px !important;
                        }
            
                        td[class*='mobile-padding-left'] {
                            padding-left: 14px !important;
                        }
            
                        td[class*='mobile-padding-right'] {
                            padding-right: 14px !important;
                        }
            
                        td[class*='mobile-padding-left-only'] {
                            padding-left: 14px !important;
                            padding-right: 0 !important;
                        }
            
                        td[class*='mobile-padding-right-only'] {
                            padding-right: 14px !important;
                            padding-left: 0 !important;
                        }
            
                        td[class*='mobile-block'] {
                            display: block !important;
                            width: 100% !important;
                            text-align: left !important;
                            padding-left: 0 !important;
                            padding-right: 0 !important;
                            padding-bottom: 15px !important;
                        }
            
                        td[class*='mobile-no-padding-bottom'] {
                            padding-bottom: 0 !important;
                        }
            
                        td[class~='mobile-center'] {
                            text-align: center !important;
                        }
            
                        table[class*='mobile-center-block'] {
                            float: none !important;
                            margin: 0 auto !important;
                        }
            
                        *[class*='mobile-hide'] {
                            display: none !important;
                            width: 0 !important;
                            height: 0 !important;
                            line-height: 0 !important;
                            font-size: 0 !important;
                        }
            
                        td[class*='mobile-border'] {
                            border: 0 !important;
                        }
                        }
                    </style>
                    </head>
                    <body class='body' style='padding:0; margin:0; display:block; background:#ffffff; -webkit-text-size-adjust:none' bgcolor='#ffffff'>
                    <div class='rps_16ec'>
                    <div>
                    <table id='x_main-wrapper'>
                    <thead id='x_logo'>
                    <tr>
                    <th>
                    <img data-imagetype='External' src='" . $logo . "' width = '200px'  alt='Prasca Center' class='x_responsive'> 
                    </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                    <td id='x_greeting'>
                    Estimad@  <strong style='text-transform: capitalize;'> " . $paciente->primer_nombre . " " . $paciente->segundo_nombre . " " . $paciente->primer_apellido . " " . $paciente->segundo_apellido . ",</strong>
                    </td>
                    </tr>
                    <tr>
                    <td  id='x_initial-text'>
                    " . $mensaje . "
                    </td>
                    </tr>
                    <div>
                    </body>
                    </html>";

                try {
                    require base_path("vendor/autoload.php");
                    $mail->isSMTP();
                    $mail->Host = 'mail.prascacenter.com';  // Servidor SMTP de tu hosting
                    $mail->SMTPAuth = true;
                    $mail->Username = 'notificaciones@prascacenter.com'; // Tu correo completo
                    $mail->Password = 'isabel_2025*'; // Tu contraseña
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // O ENCRYPTION_SMTPS si usas SSL
                    $mail->Port = 587; // 465 si usas SSL, 587 para TLS

                    $mail->CharSet = 'UTF-8';
                    $mail->Encoding = 'base64';
                    // Opcional: Si hay problemas con el certificado SSL
                    $mail->SMTPOptions = [
                        'ssl' => [
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        ]
                    ];

                    // Configuración del correo
                    $mail->setFrom('notificaciones@prascacenter.com', 'Prasca Center');
                    $mail->addAddress($paciente->email, $paciente->primer_nombre . ' ' . $paciente->segundo_nombre . ' ' . $paciente->primer_apellido . ' ' . $paciente->segundo_apellido); // Correo y nombre del destinatario
                    $mail->isHTML(true);
                    $mail->Subject = $asunto;
                    $mail->Body = $contenido;
                    $mail->addStringAttachment($pdfContent, 'cotizacion_servicios.pdf');
                    // Enviar el correo
                    $mail->send();
                    return response()->json(['resultado' => "enviado"]);
                } catch (Exception $e) {
                    return response()->json(['resultado' => "error"]);
                }
            }
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function sesiones()
    {
        $sesiones = Pacientes::listSesiones();
        return response()->json($sesiones);
    }

    public function paquetes()
    {
        $paquetes = Pacientes::listPaquetes();
        return response()->json($paquetes);
    }

    public function pruebas()
    {
        $pruebas = Pacientes::listPruebas();
        return response()->json($pruebas);
    }

    public function editarCotizacion(Request $request)
    {
        $idCotizacion = $request->input('idCotizacion');
        $cotizacion = Cotizaciones::buscaCotizacion($idCotizacion);
        $detalles = Cotizaciones::buscaDetallesCotizacion($idCotizacion);
        $cotizacion = [
            'cotizacion' => $cotizacion,
            'detalles' => $detalles
        ];
        return response()->json($cotizacion);
    }

    public function eliminarAnexo(Request $request)
    {
        $idAnexo = $request->input('idAnexo');
        $anexo = DB::connection('mysql')
            ->table('anexos_pacientes')
            ->where('id', $idAnexo)
            ->delete();

        return response()->json(['success' => true, 'message' => 'Anexo eliminado correctamente']);
    }

    public function guardarCotizacion(Request $request)
    {
        $cotizacion = Cotizaciones::guardarCotizacion($request);

        return response()->json([
            'success' => true,
            'message' => 'Cotización guardada correctamente',
            'idCotizacion' => $cotizacion['idCotizacion']
        ]);
    }

    public function getPacientes(Request $request)
    {
        $search = $request->input('search');

        $pacientes = DB::table('pacientes')
            ->where(function ($query) use ($search) {
                $query->where('identificacion', 'like', '%' . $search . '%')
                    ->orWhere('primer_nombre', 'like', '%' . $search . '%')
                    ->orWhere('primer_apellido', 'like', '%' . $search . '%');
            })
            ->where('estado', 'ACTIVO')
            ->select('id', 'identificacion', 'primer_nombre', 'primer_apellido', 'estado')
            ->get();


        return response()->json($pacientes);
    }

    public function historiaPsicologica()
    {
        $bandera = "";
        if (Auth::check()) {
            return view('HistoriasClinica.psicologia', compact('bandera'));
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }
    public function historiaNeuropsicologica()
    {
        $bandera = "";
        if (Auth::check()) {
            return view('HistoriasClinica.Neuropsicologia', compact('bandera'));
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function buscaServicioVenta(Request $request)
    {
        $idServicio = $request->input('idServicio');
        $servicio = Servicios::buscaServicioVenta($idServicio);
        return response()->json($servicio);
    }

    public function listaCotizaciones(Request $request)
    {
        if (Auth::check()) {
            $perPage = 10; // Número de posts por página
            $page = request()->get('page', 1);
            $idPaciente = request()->get('idPaciente');
            if (!is_numeric($page)) {
                $page = 1; // Establecer un valor predeterminado si no es numérico
            }

            $cotizaciones = DB::connection('mysql')
                ->table('cotizaciones')
                ->where('paciente', $idPaciente)
                ->where('estado', 'ACTIVO')
                ->select(
                    'cotizaciones.id',
                    'cotizaciones.fecha',
                    'cotizaciones.valor',
                    'cotizaciones.sub_total',
                    'cotizaciones.descuento',
                    'cotizaciones.estado'
                );



            $ListCotizaciones = $cotizaciones->paginate($perPage, ['*'], 'page', $page);

            $tdTable = '';
            $x = ($page - 1) * $perPage + 1;
            $const = 1;
            foreach ($ListCotizaciones as $i => $item) {
                if (!is_null($item)) {

                    $tdTable .= '<tr>
                                    <td>' . $x . '</td>
                                    <td>' . date('d/m/Y', strtotime($item->fecha)) . '</td>
                                    <td>$ ' . number_format($item->descuento, 2, ',', '.') . '</td>
                                    <td>$ ' . number_format($item->valor, 2, ',', '.') . '</td>
                                    <td class="table-action min-w-100">
                                        <a onclick="imprimirCotizacion(' . $item->id . ');" style="cursor: pointer;" title="Imprimir" class="text-fade hover-primary"><i class="align-middle"
                                                data-feather="file-text"></i></a>
                                        <a onclick="editarRegistroCotizacion(' . $item->id . ');" style="cursor: pointer;" title="Editar" class="text-fade hover-primary"><i class="align-middle"
                                                data-feather="edit-2"></i></a>
                                        <a onclick="eliminarRegistroCotizacion(' . $item->id . ');" style="cursor: pointer;" title="Eliminar" class="text-fade hover-warning"><i class="align-middle"
                                                data-feather="trash"></i></a>
                                    </td>
                                </tr>';
                    $x++;
                    $const++;
                }
            }
            $pagination = $ListCotizaciones->links('Pacientes.PaginacionCotizaciones')->render();

            return response()->json([
                'cotizaciones' => $tdTable,
                'links' => $pagination
            ]);
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function listaVentaServiciosPacientes(Request $request)
    {

        if (Auth::check()) {
            $perPage = 10; // Número de posts por página
            $page = request()->get('page', 1);
            $search = request()->get('search');
            $idPaciente = request()->get('idPaciente');
            if (!is_numeric($page)) {
                $page = 1; // Establecer un valor predeterminado si no es numérico
            }


            $paquetes = DB::connection('mysql')
                ->table('servicios')
                ->leftJoin("ventas", "servicios.id", "ventas.id_servicio")
                ->leftJoin('sesiones_paquete_uso', 'ventas.id',  'sesiones_paquete_uso.venta_id')
                ->where('servicios.estado', 'ACTIVO')
                ->where('servicios.id_paciente', $idPaciente)
                ->orderBy('servicios.fecha', 'desc')
                ->groupBy([
                    'servicios.id',
                    'servicios.id_tipo_servicio',
                    'servicios.tipo',
                    'servicios.id_paciente',
                    'servicios.fecha',
                    'servicios.precio',
                    'ventas.cantidad',
                    'ventas.estado_venta',
                    'servicios.tipo_servicio'
                ])
                ->select(
                    'servicios.id_tipo_servicio',
                    'servicios.tipo',
                    'servicios.id_paciente',
                    'servicios.fecha',
                    'servicios.precio',
                    'servicios.id',
                    'ventas.cantidad',
                    'ventas.estado_venta',
                    'servicios.tipo_servicio',
                    DB::raw('ventas.cantidad - COUNT(DISTINCT sesiones_paquete_uso.id) as sesiones_disponibles'),
                    DB::raw("(SELECT nombre FROM especialidades WHERE especialidades.id = servicios.id_tipo_servicio AND servicios.tipo = 'CONSULTA' LIMIT 1) AS descripcion_consulta"),
                    DB::raw("(SELECT descripcion FROM sesiones WHERE sesiones.id = servicios.id_tipo_servicio AND servicios.tipo = 'SESION' LIMIT 1) AS descripcion_sesion"),
                    DB::raw("(SELECT descripcion FROM paquetes WHERE paquetes.id = servicios.id_tipo_servicio AND servicios.tipo = 'PAQUETE' LIMIT 1) AS descripcion_paquete"),
                    DB::raw("(SELECT descripcion FROM pruebas WHERE pruebas.id = servicios.id_tipo_servicio AND servicios.tipo = 'PRUEBAS' LIMIT 1) AS descripcion_pruebas"),
                    DB::raw("
                    COALESCE(
                        (SELECT nombre FROM especialidades WHERE especialidades.id = servicios.id_tipo_servicio AND servicios.tipo = 'CONSULTA' LIMIT 1),
                        (SELECT descripcion FROM sesiones WHERE sesiones.id = servicios.id_tipo_servicio AND servicios.tipo = 'SESION' LIMIT 1),
                        (SELECT descripcion FROM paquetes WHERE paquetes.id = servicios.id_tipo_servicio AND servicios.tipo = 'PAQUETE' LIMIT 1),
                        (SELECT descripcion FROM pruebas WHERE pruebas.id = servicios.id_tipo_servicio AND servicios.tipo = 'PRUEBAS' LIMIT 1),
                        'Sin descripción'
                    ) AS descripcion
                ")
                );

            if ($search) {
                $paquetes->whereRaw("
                COALESCE(
                    (SELECT nombre FROM especialidades WHERE especialidades.id = servicios.id_tipo_servicio AND servicios.tipo = 'CONSULTA' LIMIT 1),
                    (SELECT descripcion FROM sesiones WHERE sesiones.id = servicios.id_tipo_servicio AND servicios.tipo = 'SESION' LIMIT 1),
                    (SELECT descripcion FROM paquetes WHERE paquetes.id = servicios.id_tipo_servicio AND servicios.tipo = 'PAQUETE' LIMIT 1),
                    (SELECT descripcion FROM pruebas WHERE pruebas.id = servicios.id_tipo_servicio AND servicios.tipo = 'PRUEBAS' LIMIT 1)
                ) LIKE ?", ["%$search%"]);
            }

            $ListPaquetes = $paquetes->paginate($perPage, ['*'], 'page', $page);

            $tdTable = '';
            $x = ($page - 1) * $perPage + 1;
            $const = 1;
            foreach ($ListPaquetes as $i => $item) {
                if (!is_null($item)) {
                    $valor = number_format($item->precio, 2, ',', '.');
                    $fecha = date('d/m/Y H:i:s', strtotime($item->fecha));
                    $sesiones = "1 / 1";
                    if ($item->tipo == 'PAQUETE') {
                        $color = 'badge-warning';
                        $sesiones = $item->sesiones_disponibles . ' Disponibles de ' . $item->cantidad;
                    } else if ($item->tipo == 'SESION') {
                        $color = 'badge-success';
                    } else if ($item->tipo == 'CONSULTA') {
                        $color = 'badge-primary';
                    } else if ($item->tipo == 'PRUEBAS') {
                        $color = 'badge-info';
                    }

                    $tdTable .= '<tr>
                                    <td>' . $item->descripcion . '</td>
                                    <td><span class="badge ' . $color . '">' . $item->tipo . ' - ' . $item->tipo_servicio . '</span></td>
                                    <td>' . $sesiones . '</td>
                                    <td>' . \Carbon\Carbon::parse($item->fecha)->format('d/m/Y') . '</td>
                                    <td>$ ' . $valor . '</td>
                                    <td>' . $item->estado_venta . '</td>
                                    <td class="table-action min-w-100">
                                        <a onclick="editarRegistro(' . $item->id . ');" style="cursor: pointer;" title="Editar" class="text-fade hover-primary"><i class="align-middle"
                                                data-feather="edit-2"></i></a>
                                        <a onclick="eliminarRegistro(' . $item->id . ');" style="cursor: pointer;" title="Eliminar" class="text-fade hover-warning"><i class="align-middle"
                                                data-feather="trash"></i></a>
                                    </td>
                                </tr>';
                    $x++;
                    $const++;
                }
            }
            $pagination = $ListPaquetes->links('Pacientes.PaginacionVentaServicios')->render();

            return response()->json([
                'servicios' => $tdTable,
                'links' => $pagination
            ]);
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function eliminarPaciente()
    {
        try {
            $idPaciente = request()->input('idPaciente');

            if (!$idPaciente) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'ID del paciente no proporcionado'
                    ],
                    400
                );
            }

            $paciente = DB::connection('mysql')
                ->table('pacientes')
                ->where('id', $idPaciente)
                ->update([
                    'estado' => 'ELIMINADO',
                ]);


            if ($paciente) {
                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Paciente eliminado correctamente'
                    ]
                );
            } else {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'No se encontró el paciente o no se pudo eliminar'
                    ],
                    404
                );
            }
        } catch (\Exception $e) {
            // Manejar cualquier error o excepción
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Ocurrió un error al intentar eliminar el paciente',
                    'error' => $e->getMessage()
                ],
                500
            );
        }
    }

    public function eliminarServicioVenta()
    {
        try {
            $idServicio = request()->input('idServicio');

            if (!$idServicio) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'ID del servicio no proporcionado'
                    ],
                    400
                );
            }

            $paciente = DB::connection('mysql')
                ->table('servicios')
                ->where('id', $idServicio)
                ->update([
                    'estado' => 'ELIMINADO',
                ]);


            if ($paciente) {
                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Servicio eliminado correctamente'
                    ]
                );
            } else {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'No se encontró el servicio o no se pudo eliminar'
                    ],
                    404
                );
            }
        } catch (\Exception $e) {
            // Manejar cualquier error o excepción
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Ocurrió un error al intentar eliminar el servicio',
                    'error' => $e->getMessage()
                ],
                500
            );
        }
    }
    public function eliminarCotizacion()
    {
        try {
            $idCotizacion = request()->input('idCotizacion');

            if (!$idCotizacion) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'ID de la cotización no proporcionado'
                    ],
                    400
                );
            }

            $paciente = DB::connection('mysql')
                ->table('cotizaciones')
                ->where('id', $idCotizacion)
                ->update([
                    'estado' => 'ELIMINADO',
                ]);


            if ($paciente) {
                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Cotización eliminada correctamente'
                    ]
                );
            } else {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'No se encontró la cotización o no se pudo eliminar'
                    ],
                    404
                );
            }
        } catch (\Exception $e) {
            // Manejar cualquier error o excepción
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Ocurrió un error al intentar eliminar la cotización',
                    'error' => $e->getMessage()
                ],
                500
            );
        }
    }

    public function ocupaciones()
    {
        $ocupaciones = Pacientes::listOcupaciones();
        return response()->json($ocupaciones);
    }
    public function municipios(Request $request)
    {
        $idMuni = $request->input('idMuni');

        $municipios = Pacientes::listMunicipios($idMuni);
        return response()->json($municipios);
    }

    public function departamentos()
    {

        $departamentos = Pacientes::listDepartamentos();
        return response()->json($departamentos);
    }

    public function tipoUSuario()
    {

        $tipoUSuario = Pacientes::listTipoUsuario();
        return response()->json($tipoUSuario);
    }

    public function eps()
    {
        $eps = Pacientes::listEps();
        return response()->json($eps);
    }


    public function verificarIdentPaciente(Request $request)
    {
        $identificacion = $request->input('identificacion');
        $idPaciente = $request->input('id'); // Puede ser null si es nuevo


        $paciente = DB::table('pacientes')
            ->where('identificacion', $identificacion)
            ->where('estado', 'ACTIVO')
            ->first();


        if (!$paciente) {
            // No existe ningún paciente con esa cédula: OK
            return response()->json(true);
        }

        if ($idPaciente && $paciente->id == $idPaciente) {
            // La identificación pertenece al paciente actual: OK
            return response()->json(true);
        }

        // La identificación pertenece a otro paciente: ERROR
        return response()->json(false);
    }


    public function busquedaPaciente(Request $request)
    {
        $idPaciente = $request->input('idPaciente');
        $paciente = Pacientes::busquedaPaciente($idPaciente);
        $anexos = Pacientes::busquedaPacienteAnexos($idPaciente);
        return response()->json([
            'paciente' => $paciente,
            'anexos' => $anexos
        ]);
    }
    public function buscaPacienteHistoria(Request $request)
    {
        $idPaciente = $request->input('idPaciente');
        $paciente = Pacientes::busquedaPaciente($idPaciente);
        $historia = HistoriaPsicologica::busquedaHistoriaPaciente($idPaciente);
        $profesional = Pacientes::busquedaProfesional();
        return response()->json([
            'paciente' => $paciente,
            'historia' => $historia,
            'profesional' => $profesional
        ]);
    }

    public function buscaPacienteHistoriaNeuro(Request $request)
    {
        $idPaciente = $request->input('idPaciente');
        $paciente = Pacientes::busquedaPaciente($idPaciente);
        $historia = HistoriaNeuroPsicologica::busquedaHistoriaNeuroPaciente($idPaciente);

        return response()->json([
            'paciente' => $paciente,
            'historia' => $historia
        ]);
    }

    public function guardarPaciente(Request $request)
    {
        // Verificar si el usuario está autenticado
        if (!Auth::check()) {
            return response()->json([
                'estado' => 'error',
                'mensaje' => 'Su sesión ha terminado.',
            ], 401); // Código de error 401: No autorizado
        }

        // Capturar los datos del request
        $data = $request->all();

        // Manejar el archivo de foto del paciente
        if (isset($data['fotoPaciente'])) {
            $archivo = $data['fotoPaciente'];
            $nombreOriginal = $archivo->getClientOriginalName();

            // Generar un nombre único para el archivo
            $prefijo = substr(md5(uniqid(rand())), 0, 6);
            $nombreArchivo = self::sanear_string($prefijo . '_' . $nombreOriginal);

            // Guardar el archivo en la ruta especificada
            $archivo->move(public_path() . '/app-assets/images/FotosPacientes/', $nombreArchivo);
            $data['img'] = $nombreArchivo;
        } else {
            if ($data['accPacientes'] == 'guardar') {
                $data['img'] = "default.jpg";
            } else {
                $data['img'] = $data['fotoCargada'];
            }
        }

        // manejar anexos de pacientes 
        if ($request->hasFile('archivos')) {
            $archivos = $request->file('archivos'); // Obtiene todos los archivos con el name "archivos[]"

            $arc = [];
            $tip = [];
            $nom = [];
            $siz = [];

            foreach ($archivos as $archivo) {
                $nombreOriginal = $archivo->getClientOriginalName();
                $tipoMime = $archivo->getClientMimeType();
                $peso = $archivo->getSize();
                // Generar un nombre único para el archivo
                $prefijo = substr(md5(uniqid(rand())), 0, 6);
                $nombreArchivo = self::sanear_string($prefijo . '_' . $nombreOriginal);

                // Mover el archivo a la carpeta deseada
                $archivo->move(public_path('anexosPacientes'), $nombreArchivo);

                // Almacenar la información del archivo en arrays
                $arc[] = $nombreArchivo;
                $tip[] = $tipoMime;
                $nom[] = $nombreOriginal;
                $siz[] = $peso;
            }

            // Preparar los datos para trabajar con ellos o almacenarlos

            $data['archivo'] = $arc;
            $data['tipoArc'] = $tip;
            $data['nombre'] = $nom;
            $data['peso'] = $siz;

            // Aquí puedes guardar la información en la base de datos si lo necesitas
            // Ejemplo: Archivo::createMany($data);
        }

        // Guardar la información del paciente
        $respuesta = Pacientes::guardar($data);



        // Verificar el resultado y preparar la respuesta
        if ($respuesta) {
            $estado = 'success';
            $message = 'La operación fue realizada exitosamente.';
            $title = '¡Buen trabajo!';
        } else {
            $message = 'No se pudo realizada la operación.';
            $estado = 'warning';
            $title = '¡Opps salio algo mal!';
        }

        // Retornar la respuesta en formato JSON
        return response()->json([
            'success' => $estado,
            'id' => $respuesta,
            'message' =>  $message,
            'title' =>  $title
        ]);
    }




    public function listaPacientes(Request $request)
    {

        if (Auth::check()) {
            $perPage = 10; // Número de posts por página
            $page = request()->get('page', 1);
            $search = request()->get('search');
            if (!is_numeric($page)) {
                $page = 1; // Establecer un valor predeterminado si no es numérico
            }

            $pacientes = DB::connection('mysql')
                ->table('pacientes')
                ->leftJoin("tipo_usuario", "tipo_usuario.id", "pacientes.tipo_usuario")
                ->where('estado', 'ACTIVO')
                ->select(
                    DB::raw("CONCAT(tipo_identificacion, ' ', identificacion) as identificacion_completa"),
                    DB::raw("CONCAT(primer_nombre,' ',segundo_nombre,' ',primer_apellido,' ', segundo_apellido) as nombre_completo"),
                    'telefono',
                    'pacientes.id',
                    'tipo_usuario.descripcion AS regimen',
                    DB::raw("
                CASE 
                    WHEN sexo = 'H' THEN 'Hombre'
                    WHEN sexo = 'M' THEN 'Mujer'
                    WHEN sexo = 'I' THEN 'Indeterminado o Intersexual'
                    ELSE 'Sin Especificar'
                END as sexo
            "),

                    DB::raw("STR_TO_DATE(fecha_nacimiento, '%Y-%m-%d') as fecha_nacimiento_formateada"),
                    DB::raw("CONCAT(TIMESTAMPDIFF(YEAR, STR_TO_DATE(fecha_nacimiento, '%Y-%m-%d'), CURDATE())) as edad"),
                    DB::raw("
                CASE 
                    WHEN completo = 1 THEN 'COMPLETO'
                    ELSE 'IMCOMPLETO'
                END as estadoCompleto
            "),
                );


          
            if ($search) {
                $pacientes->where(function ($query) use ($search) {
                    // Búsqueda por identificación
                    $query->where('identificacion', 'LIKE', '%' . $search . '%');
                    
                    // Búsqueda individual por campos
                    $query->orWhere('primer_nombre', 'LIKE', '%' . $search . '%')
                        ->orWhere('segundo_nombre', 'LIKE', '%' . $search . '%')
                        ->orWhere('primer_apellido', 'LIKE', '%' . $search . '%')
                        ->orWhere('segundo_apellido', 'LIKE', '%' . $search . '%');
                    
                    // Búsqueda en nombre completo (exacta)
                    $query->orWhere(DB::raw("CONCAT(primer_nombre,' ',segundo_nombre,' ',primer_apellido,' ', segundo_apellido)"), 'LIKE', '%' . $search . '%');
                    
                    // Búsqueda por palabras en desorden: divide el término de búsqueda en palabras
                    // y verifica que todas estén presentes en el nombre completo
                    $palabras = array_filter(explode(' ', trim($search)));
                    if (count($palabras) > 1) {
                        $query->orWhere(function ($subQuery) use ($palabras) {
                            $nombreCompleto = DB::raw("CONCAT(primer_nombre,' ',segundo_nombre,' ',primer_apellido,' ', segundo_apellido)");
                            foreach ($palabras as $palabra) {
                                $subQuery->where($nombreCompleto, 'LIKE', '%' . trim($palabra) . '%');
                            }
                        });
                    }
                });
            }

            $ListPacientes = $pacientes->paginate($perPage, ['*'], 'page', $page)->appends(request()->except('page'));

            $tdTable = '';
            $x = ($page - 1) * $perPage + 1;

            foreach ($ListPacientes as $i => $item) {
                if (!is_null($item)) {
                    $clases = ($item->estadoCompleto == "IMCOMPLETO") ? "badge-danger" : "badge-success";

                    $tdTable .= '<tr>
                                    <td>' . $item->identificacion_completa . '</td>
                                    <td>' . $item->nombre_completo . '</td>
                                    <td>' . $item->regimen . '</td>
                                    <td>' . $item->sexo . '</td>
                                    <td>' . $item->edad . ' Años</td>
                                    <td>' . $item->telefono . '</td>
                                    <td><span class="badge ' . $clases . '">' . $item->estadoCompleto . '</span></td>
                                    <td class="table-action min-w-100">
                                    <a href="javascript:void(0)" onclick="verServiciosVenta(' . $item->id . ');" style="cursor: pointer;" title="Venta de servicios" class="text-fade hover-info"><i class="align-middle"
                                    data-feather="shopping-cart"></i></a>
                                    <a  style="cursor: pointer;" data-bs-toggle="dropdown" title="Historia clinica" class="text-fade hover-info"><i class="align-middle"
                                    data-feather="file-text"></i></a>
                                        <div class="dropdown-menu">
									        <a class="dropdown-item" id="hsitPsi' . $item->id . '" data-id="' . $item->id . '" data-edad="' . $item->edad . '" style="cursor:pointer;" onclick="goHistoriaPsicologia(this)" >Historia clinica psicológica</a>
									        <a class="dropdown-item" id="hsitNeu' . $item->id . '" data-id="' . $item->id . '" data-edad="' . $item->edad . '" style="cursor:pointer;" onclick="goHistoriaNeuropsicologia(this)">Historia clinica neuropsicológica</a>
								        </div>
                                    <a  onclick="editarPaciente(' . $item->id . ');" style="cursor: pointer;" title="Editar" class="text-fade hover-primary"><i class="align-middle"
                                                data-feather="edit-2"></i></a>
                                    <a  onclick="eliminarPaciente(' . $item->id . ');" style="cursor: pointer;" title="Eliminar" class="text-fade hover-warning"><i class="align-middle"
                                                data-feather="trash"></i></a>
                                    </td>
                                </tr>';
                    $x++;
                }
            }

            $pagination = $ListPacientes->links('Pacientes.Paginacion')->render();

            return response()->json([
                'pacientes' => $tdTable,
                'links' => $pagination
            ]);
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function listaPacientesModal(Request $request)
    {

        if (Auth::check()) {
            $perPage = 10; // Número de posts por página
            $page = request()->get('page', 1);
            $search = request()->get('search');
            if (!is_numeric($page)) {
                $page = 1; // Establecer un valor predeterminado si no es numérico
            }

            $pacientes = DB::connection('mysql')
                ->table('pacientes')
                ->where('estado', 'ACTIVO')
                ->select(
                    DB::raw("CONCAT(tipo_identificacion, ' ', identificacion) as identificacion_completa"),
                    DB::raw("CONCAT(primer_nombre,' ',segundo_nombre,' ',primer_apellido,' ', segundo_apellido) as nombre_completo"),
                    'telefono',
                    'id',
                    DB::raw("
                CASE 
                    WHEN sexo = 'H' THEN 'Hombre'
                    WHEN sexo = 'M' THEN 'Mujer'
                    WHEN sexo = 'I' THEN 'Indeterminado o Intersexual'
                    ELSE 'Sin Especificar'
                END as sexo
            "),
                    DB::raw("
                CASE
                    WHEN tipo_usuario = '01' THEN 'Contributivo cotizante'
                    WHEN tipo_usuario = '02' THEN 'Contributivo beneficiario'
                    WHEN tipo_usuario = '03' THEN 'Contributivo adicional'
                    WHEN tipo_usuario = '04' THEN 'Subsidiado'
                    WHEN tipo_usuario = '05' THEN 'No afiliado'
                    WHEN tipo_usuario = '06' THEN 'Especial o Excepcion cotizante'
                    WHEN tipo_usuario = '07' THEN 'Especial o Excepcion beneficiario'
                    WHEN tipo_usuario = '08' THEN 'Personas privadas de la libertad a cargo del Fondo Nacional de Salud'
                    WHEN tipo_usuario = '09' THEN 'Tomador / Amparado ARL'
                    WHEN tipo_usuario = '10' THEN 'Tomador / Amparado SOAT'
                    ELSE 'Sin Especificar'
                END as regimen
            "),
                    DB::raw("STR_TO_DATE(fecha_nacimiento, '%Y-%m-%d') as fecha_nacimiento_formateada"),
                    DB::raw("CONCAT(TIMESTAMPDIFF(YEAR, STR_TO_DATE(fecha_nacimiento, '%Y-%m-%d'), CURDATE())) as edad"),
                    DB::raw("
                CASE
                    WHEN completo = 1 THEN 'COMPLETO'
                    ELSE 'IMCOMPLETO'
                END as estado
            "),
                );

            if ($search) {
                $pacientes->where(function ($query) use ($search) {
                    $query->where('identificacion', 'LIKE', '%' . $search . '%')
                        ->orWhere('primer_nombre', 'LIKE', '%' . $search . '%')
                        ->orWhere('segundo_nombre', 'LIKE', '%' . $search . '%')
                        ->orWhere('primer_apellido', 'LIKE', '%' . $search . '%')
                        ->orWhere('segundo_apellido', 'LIKE', '%' . $search . '%');
                });
            }

            $ListPacientes = $pacientes->paginate($perPage, ['*'], 'page', $page);

            $tdTable = '';
            $x = ($page - 1) * $perPage + 1;

            foreach ($ListPacientes as $i => $item) {
                if (!is_null($item)) {
                    $clases = ($item->estado == "IMCOMPLETO") ? "badge-danger" : "badge-success";

                    $tdTable .= '<tr data-edad="' . $item->edad . '" data-id="' . $item->id . '" onclick="seleccionarPaciente(this)" style="cursor: pointer;">
                                    <td>' . $item->identificacion_completa . ' - ' . $item->nombre_completo . '</td>
                                    <td>' . $item->regimen . '</td>
                                    <td>' . $item->sexo . '</td>
                                    <td>' . $item->edad . ' Años</td>
                                </tr>';
                    $x++;
                }
            }

            $pagination = $ListPacientes->links('HistoriasClinica.PaginacionPac')->render();

            return response()->json([
                'pacientes' => $tdTable,
                'links' => $pagination
            ]);
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function sanear_string($string)
    {

        $string = trim($string);

        $string = str_replace(
            array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
            array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
            $string
        );

        $string = str_replace(
            array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
            array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
            $string
        );

        $string = str_replace(
            array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
            array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
            $string
        );

        $string = str_replace(
            array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
            array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
            $string
        );

        $string = str_replace(
            array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
            array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
            $string
        );

        $string = str_replace(
            array('ñ', 'Ñ', 'ç', 'Ç'),
            array('n', 'N', 'c', 'C'),
            $string
        );

        //Esta parte se encarga de eliminar cualquier caracter extraño
        $string = str_replace(
            array(
                "¨",
                "º",
                "-",
                "~",
                "",
                "@",
                "|",
                "!",
                "·",
                "$",
                "%",
                "&",
                "/",
                "(",
                ")",
                "?",
                "'",
                " h¡",
                "¿",
                "[",
                "^",
                "<code>",
                "]",
                "+",
                "}",
                "{",
                "¨",
                "´",
                ">",
                "< ",
                ";",
                ",",
                ":",
                " ",
            ),
            '',
            $string
        );

        return $string;
    }

    // ── COBERTURA EPS ────────────────────────────────────
    public function guardarPlanPaciente(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false], 401);
        }
        $d = $request->all();

        // Desactivar plan anterior si existe
        DB::connection('mysql')->table('paciente_planes_eps')
            ->where('id_paciente', $d['idPaciente'])
            ->where('estado', 'activo')
            ->update(['estado' => 'inactivo', 'updated_at' => now()]);

        // Insertar nueva asignación
        DB::connection('mysql')->table('paciente_planes_eps')->insert([
            'id_paciente'       => $d['idPaciente'],
            'id_plan'           => $d['idPlan'],
            'numero_poliza'     => $d['numeroPoliza']     ?? null,
            'fecha_vinculacion' => $d['fechaVinculacion'] ?: null,
            'estado'            => 'activo',
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Plan asignado correctamente.']);
    }

    public function quitarPlanPaciente(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false], 401);
        }
        DB::connection('mysql')->table('paciente_planes_eps')
            ->where('id_paciente', $request->idPaciente)
            ->where('estado', 'activo')
            ->update(['estado' => 'inactivo', 'updated_at' => now()]);

        return response()->json(['success' => true]);
    }

    public function obtenerCoberturaPaciente(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false], 401);
        }
        $asignacion = DB::connection('mysql')->table('paciente_planes_eps')
            ->join('planes_eps',    'planes_eps.id',    '=', 'paciente_planes_eps.id_plan')
            ->join('contratos_eps', 'contratos_eps.id', '=', 'planes_eps.id_contrato')
            ->join('eps',           'eps.id',           '=', 'contratos_eps.id_eps')
            ->where('paciente_planes_eps.id_paciente', $request->idPaciente)
            ->where('paciente_planes_eps.estado', 'activo')
            ->select(
                'paciente_planes_eps.id as id_asignacion',
                'paciente_planes_eps.numero_poliza',
                'planes_eps.id as id_plan',
                'planes_eps.nombre as plan_nombre',
                'planes_eps.descripcion as plan_descripcion',
                'planes_eps.limite_consultas',
                'contratos_eps.fecha_inicio',
                'contratos_eps.fecha_fin',
                'eps.entidad as eps_nombre'
            )
            ->first();

        if (!$asignacion) {
            return response()->json(['tiene_plan' => false]);
        }

        $copagos = DB::connection('mysql')->table('copagos_eps')
            ->where('id_plan', $asignacion->id_plan)
            ->get();

        return response()->json([
            'tiene_plan' => true,
            'asignacion' => $asignacion,
            'copagos'    => $copagos,
        ]);
    }

    public function listarAutorizaciones(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false], 401);
        }
        $citas = DB::connection('mysql')->table('citas')
            ->where('paciente', $request->idPaciente)
            ->leftJoin('especialidades', 'especialidades.id', '=', 'citas.motivo')
            ->orderBy('inicio', 'desc')
            ->select('citas.id', 'inicio', 'especialidades.nombre as motivo', 'citas.numero_autorizacion', 'citas.copago_cobrado', 'citas.estado')
            ->get();

        $html = '';
        foreach ($citas as $c) {
            $fecha  = date('d M Y', strtotime($c->inicio));
            $numAut = $c->numero_autorizacion ?? '—';
            $copago = $c->copago_cobrado
                ? '$' . number_format($c->copago_cobrado, 0, ',', '.')
                : '—';
            $numAutJs = addslashes($c->numero_autorizacion ?? '');
            $html .= "
            <tr>
                <td>{$fecha}</td>
                <td>{$c->motivo}</td>
                <td>{$numAut}</td>
                <td class=\"copago-amount\">{$copago}</td>
                <td>
                    <span class=\"action-btn\"
                        onclick=\"editarAutorizacion({$c->id},'{$numAutJs}','{$c->copago_cobrado}')\">
                        <i class=\"fa fa-edit\"></i> Registrar
                    </span>
                </td>
            </tr>";
        }
        return response()->json(['html' => $html]);
    }

    public function registrarAutorizacion(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false], 401);
        }
        DB::connection('mysql')->table('citas')
            ->where('id', $request->idCita)
            ->update([
                'numero_autorizacion' => $request->numeroAutorizacion ?: null,
                'copago_cobrado'      => $request->copagoCobrado      ?: null,
            ]);

        return response()->json(['success' => true, 'message' => 'Autorización registrada.']);
    }
}
