<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\HistoriaNeuroPsicologica;
use App\Models\CategoriaHCP;
use App\Models\Pacientes;
use App\Models\Pruebas;
use \PDF;
use Dompdf\Dompdf;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use App\Models\Profesional;
use Illuminate\Support\Facades\Log;
use App\Models\RespaldoFormulario;


class HistoriaNeuroPsicologicaController extends Controller
{
    public function historiaNeuroPsicologia()
    {
        if (Auth::check()) {
            return view('HistoriasClinica.Neuropsicologia');
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function listaPaquetesSel()
    {
        $pruebas = Pruebas::listarPruebas();
        return response()->json($pruebas);
    }

    public function eliminarPrueba()
    {
        try {
            $idPrueba = request()->input('idPrueba');
            if (!$idPrueba) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'ID de la prueba no proporcionado'
                    ],
                    400
                );
            }

            $consulta = DB::connection('mysql')
                ->table('servicios')
                ->where('id', $idPrueba)
                ->update([
                    'estado' => 'ELIMINADO',
                ]);


            if ($consulta) {
                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Prueba eliminada correctamente'
                    ]
                );
            } else {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'No se encontró la prueba o no se pudo eliminar'
                    ],
                    404
                );
            }
        } catch (\Exception $e) {
            // Manejar cualquier error o excepción
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Ocurrió un error al intentar eliminar la prueba',
                    'error' => $e->getMessage()
                ],
                500
            );
        }
    }

    public function enviarHistoriaCorreoNeuro(Request $request)
    {

        $idHist = $request->input('idHist');
        $urlPDF = $request->input('urlPDF');

        if (Auth::check()) {
            $historia = HistoriaNeuroPsicologica::busquedaHistoriaNeuro($idHist);
            $paciente = Pacientes::busquedaPaciente($historia->id_paciente);

            // Verificar si el paciente tiene correo electrónico
            if (empty($paciente->email)) {
                return response()->json([
                    'success' => false,
                    'message' => 'El paciente no tiene un correo electrónico registrado.'
                ]);
            }

            try {
                // Configurar PHPMailer
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = 'mail.prascacenter.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'notificaciones@prascacenter.com';
                $mail->Password = 'isabel_2025*';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
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
                $mail->addAddress($paciente->email, $paciente->primer_nombre . ' ' . $paciente->primer_apellido);
                $mail->isHTML(true);
                $mail->Subject = 'Historia Clínica Neuropsicológica - Prasca Center';

                // Contenido del correo
                $contenido = "
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset='UTF-8'>
                    <title>Historia Clínica Neuropsicológica</title>
                    <style>
                        body { font-family: Times New Roman, Arial, sans-serif; line-height: 1.6; color: #333; }
                        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                        .header { background-color: #f8f9fa; padding: 20px; text-align: center; border-radius: 5px; }
                        .content { padding: 20px; }
                        .footer { background-color: #f8f9fa; padding: 15px; text-align: center; font-size: 12px; color: #666; }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <div class='header'>
                            <h2>Historia Clínica Neuropsicológica</h2>
                            <p>Prasca Center</p>
                        </div>
                        <div class='content'>
                            <p>Estimado/a <strong>" . $paciente->primer_nombre . " " . $paciente->primer_apellido . "</strong>,</p>
                            <p>Adjunto encontrará su historia clínica neuropsicológica en formato PDF.</p>
                            <p>Este documento contiene información confidencial sobre su evaluación neuropsicológica y debe ser tratado con la debida confidencialidad.</p>
                            <p>Si tiene alguna pregunta o necesita aclaración sobre algún aspecto de su historia clínica, no dude en contactarnos.</p>
                            <p>Atentamente,<br>
                            <strong>Equipo de Prasca Center</strong></p>
                        </div>
                        <div class='footer'>
                            <p>Este es un correo automático, por favor no responda a este mensaje.</p>
                            <p>Prasca Center - Calle 11 # 11 - 07 San Joaquin - Teléfono: 312 5678078</p>
                        </div>
                    </div>
                </body>
                </html>";

                $mail->Body = $contenido;

                // Adjuntar el PDF
                $pdfPath = public_path(str_replace(asset(''), '', $urlPDF));
                if (file_exists($pdfPath)) {
                    $mail->addAttachment($pdfPath, 'Historia_Clinica_Psicologica.pdf');
                }

                // Enviar el correo
                $mail->send();

                return response()->json([
                    'success' => true,
                    'message' => 'La historia clínica se ha enviado correctamente por correo electrónico.'
                ]);
            } catch (Exception $e) {
                Log::error('Error al enviar historia por correo: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Error al enviar el correo electrónico. Por favor, intente nuevamente.'
                ]);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Su sesión ha terminado.'
            ]);
        }
    }

    public function imprimirInformeNeuropsicologia(Request $request)
    {

        if (Auth::check()) {

            $pdf = new Dompdf();
            $idInforme = $request->input('idInforme');
            $informe = HistoriaNeuroPsicologica::busquedaInforme($idInforme);

            // Ruta absoluta al logo
            $logoPath = public_path('app-assets/images/logo/logo_prasca.png');

            // Convertir la imagen a base64
            $logoData = base64_encode(file_get_contents($logoPath));
            $logo = 'data:image/png;base64,' . $logoData;

            $fechaElaboracion = now()->format('d-m-Y');
            $horaElaboracion = now()->format('h:i:s A');

            $paciente = Pacientes::busquedaPaciente($informe->id_paciente);

            $profesional = Profesional::busquedaProfesional($informe->id_profesional);
            $firmaPath = public_path('app-assets/images/firmasProfesionales/' . $profesional->firma);
            $firmaData = base64_encode(file_get_contents($firmaPath));
            $firma = 'data:image/png;base64,' . $firmaData;

            $fechaNacimiento = \Carbon\Carbon::parse($paciente->fecha_nacimiento)->format('d/m/Y');

            $formatRichText = function (?string $html) {
                if (empty($html)) {
                    return '';
                }

                $decoded = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $decoded = preg_replace('/<p[^>]*>\s*(?:&nbsp;|\s)*<\/p>/i', '', $decoded);

                $decoded = preg_replace_callback('/<p\b([^>]*)>/i', function ($matches) {
                    $attrs = $matches[1];
                    if (stripos($attrs, 'style=') !== false) {
                        $attrs = preg_replace_callback('/style="([^"]*)"/i', function ($styleMatches) {
                            $style = trim($styleMatches[1]);
                            $style .= (strlen($style) && substr($style, -1) === ';') ? '' : ';';
                            $style .= 'margin:0 0 6px !important; line-height:1.45 !important;';
                            return 'style="' . $style . '"';
                        }, $attrs, 1);
                    } else {
                        $attrs .= ' style="margin:0 0 6px !important; line-height:1.45 !important;"';
                    }
                    return '<p' . $attrs . '>';
                }, $decoded);

                return $decoded;
            };

            $html = '<head>
                <style>
                 @page {
                        margin: 12mm 18mm;
                    }
                    
                    body {
                        position: relative;
                        font-family: "Times New Roman", Times, serif;
                        color: #333;
                        line-height: 1.4;
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
                        opacity: 0.12;
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
                        background-color: rgba(234, 235, 244, 0.6);
                        font-size: 12px;
                        line-height: 1.3;
                        text-align: left;
                    }
                    
                    td {
                        font-size: 11px;
                        line-height: 1.4;
                        text-align: left;
                    }
                    
                    .no-border {
                        border: none;
                        text-align: center;
                    }
                    
                    .section {
                        margin-bottom: 8px;
                        page-break-inside: avoid;
                    }
                    
                    .section h4, .section h5 {
                        margin: 0;
                        padding: 0;
                    }
                    
                    .section-title {
                        background: linear-gradient(135deg, rgba(105, 106, 109, 0.85) 0%,rgba(238, 211, 255, 0.18)) 100%);
                        color:rgb(0, 1, 5);
                        padding: 6px 10px;
                        margin: 10px 0 6px 0;
                        font-size: 13px;
                        font-weight: bold;
                        border-radius: 2px;
                    }
                    
                    .info-box {
                        background-color: rgba(248, 249, 250, 0.5);
                        border-left: 3px solid rgb(191, 191, 194);
                        padding: 8px 10px;
                        margin-bottom: 6px;
                        border-radius: 2px;
                    }
                    
                    .info-box p {
                        margin: 0;
                        padding: 0;
                    }
                    
                    .field-label {
                        font-weight: bold;
                        color: #495057;
                        font-size: 11px;
                        display: block;
                        margin-bottom: 3px;
                    }
                    
                    .field-value {
                        color: #212529;
                        font-size: 11px;
                        line-height: 1.4;
                        text-align: justify;
                    }
                    
                    .subsection {
                        margin-top: 6px;
                        padding: 6px 8px;
                        background-color: rgba(255, 255, 255, 0.4);
                        border-left: 2px solidrgb(77, 77, 77);
                        border-radius: 2px;
                    }
                    
                    .subsection p {
                        margin: 0;
                        padding: 0;
                    }
                    
                    .header-info {
                        font-size: 10px;
                        line-height: 1.3;
                    }
                    
                    h3 {
                        color:rgb(3, 3, 3);
                        margin: 8px 0;
                        font-size: 15px;
                    }
                    
                    h4 {
                        margin: 8px 0;
                        font-size: 13px;
                    }
                    
                    h5 {
                        margin: 6px 0;
                        font-size: 12px;
                    }
                    
                    hr {
                        border: none;
                        border-top: 1px solid #e0e0e0;
                        margin: 10px 0;
                    }
                    
                    .text-procedimiento {
                        font-size: 11px;
                        display: flex;
                        flex-direction: column;
                        gap: 3px;
                    }
                    
                    .text-procedimiento p {
                        margin: 0;
                        padding: 0;
                    }
                    
                    .text-procedimiento p:first-child {
                        font-weight: bold;
                    }

                    .rich-text {
                        margin: 0 !important;
                    }
                    
                    .rich-text p {
                        margin: 0 0 6px !important;
                        line-height: 1.45 !important;
                    }
                    
                    .rich-text p + p {
                        margin-top: 6px !important;
                    }
                    
                    .rich-text ul,
                    .rich-text ol {
                        margin: 6px 0 6px 18px !important;
                        padding: 0 !important;
                    }
                    
                    .rich-text li {
                        margin-bottom: 4px !important;
                    }
                        
                </style>
            </head>';

            $html .= '<div class="content">';
            $html .= '<table style="width:100%; border-collapse: collapse; background-color: transparent; margin-bottom: 8px;">';
            $html .= '<tr>';
            $html .= '<td class="no-border" style="padding: 0; width: 35%;"><img src="' . $logo . '" style="width: 160px; height: auto;"></td>';
            $html .= '<td class="no-border" style="padding: 0; vertical-align: top; text-align: right;">';
            $html .= '<p class="header-info" style="margin: 0; font-weight: bold;">DRA. MARIA ISABEL PUMAREJO</p>';
            $html .= '<p class="header-info" style="margin: 0;">PSICÓLOGA CLÍNICA - T.P. No. 259542</p>';
            $html .= '<p class="header-info" style="margin: 0;">Calle 11 # 11 - 07 San Joaquin</p>';
            $html .= '<p class="header-info" style="margin: 0;">Teléfono: 312 5678078</p>';
            $html .= '</td>';
            $html .= '</tr>';
            $html .= '</table>';

            $html .= '<div style="text-align: center; margin: 12px 0 8px 0;">';
            $html .= '<h3 style="margin: 3px 0; text-transform: uppercase;">INFORME DE NEUROPSICOLOGÍA</h3>';
            $html .= '<div style="font-size: 10px; color: #666; margin: 3px 0;">';
            $html .= '<span style="margin-right: 15px;"><b>FECHA DE EVALUACIÓN:</b> ' . $fechaElaboracion . '</span>';
            $html .= '<span><b>HORA:</b> ' . $horaElaboracion . '</span>';
            $html .= '</div>';
            $html .= '</div>';

            $html .= '<hr>';

            $html .= '<div class="section-title">1. DATOS DE IDENTIFICACIÓN DEL PACIENTE</div>';
            $html .= '<div class="info-box">';
            $html .= '<table style="width: 100%; border-collapse: collapse; border: none;">';
            $html .= '<tr>';
            $html .= '<td colspan="3" style="border: none; padding: 3px 0;">';
            $html .= '<span class="field-label">NOMBRE COMPLETO:</span>';
            $html .= '<span class="field-value">' .
                trim($paciente->primer_nombre . ' ' .
                    $paciente->segundo_nombre . ' ' .
                    $paciente->primer_apellido . ' ' .
                    $paciente->segundo_apellido) .
                '</span>';
            $html .= '</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td style="border: none; padding: 3px 0; width: 50%;">';
            $html .= '<span class="field-label">IDENTIFICACIÓN:</span>';
            $html .= '<span class="field-value">' . $paciente->tipo_identificacion . ' ' . $paciente->identificacion . '</span>';
            $html .= '</td>';
            $html .= '<td style="border: none; padding: 3px 0; width: 25%;">';
            $html .= '<span class="field-label">EDAD:</span>';
            $html .= '<span class="field-value">' . $paciente->edad . ' </span>';
            $html .= '</td>';
            $html .= '<td style="border: none; padding: 3px 0; width: 25%;">';
            $html .= '<span class="field-label">SEXO:</span>';
            $html .= '<span class="field-value">' . (($paciente->sexo === "M") ? "Femenino" : "Masculino") . '</span>';
            $html .= '</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td colspan="3" style="border: none; padding: 3px 0;">';
            $html .= '<span class="field-label">FECHA Y LUGAR DE NACIMIENTO:</span>';
            $fechaNacimientoFormateada = \Carbon\Carbon::parse($paciente->fecha_nacimiento)->format('d/m/Y');
            $html .= '<span class="field-value">' . $fechaNacimientoFormateada . ' - ' . $paciente->lugar_nacimiento . '</span>';
            $html .= '</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td colspan="2" style="border: none; padding: 3px 0;">';
            $html .= '<span class="field-label">ACOMPAÑANTE:</span>';
            $html .= '<span class="field-value">' . ($paciente->acompanante ?? 'N/A') . '</span>';
            $html .= '</td>';
            $html .= '<td style="border: none; padding: 3px 0;">';
            $html .= '<span class="field-label">TELÉFONO:</span>';
            $html .= '<span class="field-value">' . ($paciente->telefono_acompanate ?? 'N/A') . '</span>';
            $html .= '</td>';
            $html .= '</tr>';
            $html .= '</table>';
            $html .= '</div>';

            // Motivo de consulta

            if ($fechaElaboracion > '2026-01-30') {
                $html .= '<div class="subsection">';
                $html .= '<p class="field-value">' . $formatRichText($informe->motivo_consulta) . '</p>';
                $html .= '</div>';
            } else {
                if (!empty($informe->motivo_consulta)) {
                    $html .= '<div class="section-title">1. MOTIVO DE CONSULTA</div>';
                    $html .= '<div class="subsection">';
                    $html .= '<p class="field-value">' . $formatRichText($informe->motivo_consulta) . '</p>';
                    $html .= '</div>';
                }

                if (!empty($informe->motivo_consulta)) {
                    $html .= '<div class="section-title">2. MOTIVO DE CONSULTA</div>';
                    $html .= '<div class="subsection">';
                    $html .= '<p class="field-value">' . $formatRichText($informe->motivo_consulta) . '</p>';
                    $html .= '</div>';
                }

                // Estado actual
                if (!empty($informe->estado_actual)) {
                    $html .= '<div class="section-title">3. ESTADO ACTUAL</div>';
                    $html .= '<div class="subsection">';
                    $html .= '<p class="field-value">' . $formatRichText($informe->estado_actual) . '</p>';
                    $html .= '</div>';
                }

                // Historia personal
                if (!empty($informe->historia_personal)) {
                    $html .= '<div class="section-title">4. HISTORIA PERSONAL</div>';
                    $html .= '<div class="subsection">';
                    $html .= '<p class="field-value">' . $formatRichText($informe->historia_personal) . '</p>';
                    $html .= '</div>';
                }

                // Desarrollo psicomotor
                if (!empty($informe->desarrollo_psicomotor)) {
                    $html .= '<div class="section-title">5. DESARROLLO PSICOMOTOR</div>';
                    $html .= '<div class="subsection">';
                    $html .= '<p class="field-value">' . $formatRichText($informe->desarrollo_psicomotor) . '</p>';
                    $html .= '</div>';
                }

                // Desarrollo lenguaje
                if (!empty($informe->desarrollo_lenguaje)) {
                    $html .= '<div class="section-title">6. DESARROLLO LENGUAJE</div>';
                    $html .= '<div class="subsection">';
                    $html .= '<p class="field-value">' . $formatRichText($informe->desarrollo_lenguaje) . '</p>';
                    $html .= '</div>';
                }

                // Evaluación actual (ABC)
                if (!empty($informe->abc)) {
                    $html .= '<div class="section-title">7. EVALUACIÓN ACTUAL</div>';
                    $html .= '<div class="subsection">';
                    $html .= '<p class="field-value">' . $formatRichText($informe->abc) . '</p>';
                    $html .= '</div>';
                }

                // Antecedentes médicos y familiares
                if (!empty($informe->antecedentes_medicos_familiares)) {
                    $html .= '<div class="section-title">8. ANTECEDENTES MÉDICOS Y FAMILIARES</div>';
                    $html .= '<div class="subsection">';
                    $html .= '<p class="field-value">' . $formatRichText($informe->antecedentes_medicos_familiares) . '</p>';
                    $html .= '</div>';
                }

                // Antecedentes personales
                if (!empty($informe->antecedentes_personales)) {
                    $html .= '<div class="section-title">9. ANTECEDENTES PERSONALES</div>';
                    $html .= '<div class="subsection">';
                    $html .= '<p class="field-value">' . $formatRichText($informe->antecedentes_personales) . '</p>';
                    $html .= '</div>';
                }

                // Historia de desarrollo
                if (!empty($informe->historia_desarrollo)) {
                    $html .= '<div class="section-title">10. HISTORIA DE DESARROLLO</div>';
                    $html .= '<div class="subsection">';
                    $html .= '<p class="field-value">' . $formatRichText($informe->historia_desarrollo) . '</p>';
                    $html .= '</div>';
                }

                // Historia escolar
                if (!empty($informe->historia_escolar)) {
                    $html .= '<div class="section-title">11. HISTORIA ESCOLAR</div>';
                    $html .= '<div class="subsection">';
                    $html .= '<p class="field-value">' . $formatRichText($informe->historia_escolar) . '</p>';
                    $html .= '</div>';
                }

                // Historia socioafectiva
                if (!empty($informe->historia_socio_afectiva)) {
                    $html .= '<div class="section-title">12. HISTORIA SOCIOAFECTIVA</div>';
                    $html .= '<div class="subsection">';
                    $html .= '<p class="field-value">' . $formatRichText($informe->historia_socio_afectiva) . '</p>';
                    $html .= '</div>';
                }

                // Condición del paciente
                if (!empty($informe->condicion_paciente)) {
                    $html .= '<div class="section-title">13. CONDICIÓN DEL PACIENTE EN LA CONSULTA</div>';
                    $html .= '<div class="subsection">';
                    $html .= '<p class="field-value">' . $formatRichText($informe->condicion_paciente) . '</p>';
                    $html .= '</div>';
                }
            }




            // Resultado de la evaluación
            if (!empty($informe->resultados_evaluacion)) {
                $html .= '<div class="section-title">14. RESULTADO DE LA EVALUACIÓN</div>';
                $html .= '<div class="subsection">';
                $html .= '<p class="field-value">' . $formatRichText($informe->resultados_evaluacion) . '</p>';
                $html .= '</div>';
            }

            // Impresión diagnóstica
            if (!empty($informe->impresion_diagnostica)) {
                $html .= '<div class="section-title">15. IMPRESIÓN DIAGNÓSTICA</div>';
                $html .= '<div class="subsection">';
                $html .= '<p class="field-value">' . $formatRichText($informe->impresion_diagnostica) . '</p>';
                $html .= '</div>';
            }

            // Separador antes de la firma
            $html .= '<div style="margin-top: 20px; margin-bottom: 10px;"></div>';

            // Sección de firma del profesional
            $html .= '<div style="margin-top: 15px; padding-top: 10px; border-top: 1px solid rgba(224, 224, 224, 0.6);">';
            $html .= '<table style="width:100%; border-collapse: collapse; background-color: transparent;">';
            $html .= '<tr><td class="no-border" style="text-align: left; padding: 3px 0;">';
            $html .= '<p style="margin: 0; font-size: 11px; font-weight: bold; color: #495057;">' . $profesional->nombre . '</p>';
            $html .= '</td></tr>';
            $html .= '<tr><td class="no-border" style="text-align: left; padding: 6px 0;">';
            $html .= '<img width="130" src="' . $firma . '" style="display: block;" />';
            $html .= '</td></tr>';
            $html .= '<tr><td class="no-border" style="text-align: left; padding: 3px 0;">';
            $html .= '<p style="margin: 0; font-size: 10px; color: #666;"><b>TARJETA PROFESIONAL:</b> ' . $profesional->registro . '</p>';
            $html .= '</td></tr>';
            $html .= '</table>';
            $html .= '</div>';

            $html .= '</div></body></html>';

            $ordenesMedicas = HistoriaNeuroPsicologica::busquedaOrdenMedica($idInforme);

            if ($ordenesMedicas != null && $ordenesMedicas->count() > 0) {
                //agregar otra hoja con las ordenes medicas
                $html .= '<div class="content" style="page-break-before: always;">';
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
                $html .= '<td class="no-border" colspan="2" style="text-align: center; padding: 1px;background-color: transparent;"> <h3>INFORME DE PSICOLOGÍA - ORDENES MÉDICAS</h3></td>';
                $html .= '</tr>';
                $html .= '</table>';

                $html .= '<table>
                     <tr>
                         <td ><b>FECHA DE EVALUACIÓN:</b> ' . $fechaElaboracion . '</td>
                         <td ><b>HORA:</b> ' . $horaElaboracion . '</td>
                     </tr>
                 </table>';

                $html .= '<div class="section" >
                 <h4 style="background-color:rgba(234, 235, 244, 0.47); padding: 6px;">DATOS DE IDENTIFICACIÓN DEL PACIENTE</h4>
                 <table style="width: 100%; border-collapse: collapse; border: none;">
                     <tr>
                         <td colspan="3" style="border: none; padding: 8px 4px 4px 4px;">
                             <span style="font-weight: bold; width: 60px; display: inline-block;">NOMBRE:</span>
                             <span style="border-bottom: 1px solid #ccc; display: inline-block;">' .
                    $paciente->primer_nombre . ' ' .
                    $paciente->segundo_nombre . ' ' .
                    $paciente->primer_apellido . ' ' .
                    $paciente->segundo_apellido .
                    '</span>
                         </td>
                     </tr>
                     <tr>
                         <td style="border: none; padding: 8px 4px 4px 4px; width: 40%;">
                             <span style="font-weight: bold; width: 110px; display: inline-block;">IDENTIFICACIÓN:</span>
                             <span style="border-bottom: 1px solid #ccc; display: inline-block;">' .
                    $paciente->tipo_identificacion . ' ' . $paciente->identificacion .
                    '</span>
                                     </td>
                         <td style="border: none; padding: 8px 4px 4px 4px; width: 40%;">
                             <span style="font-weight: bold; width: 40px; display: inline-block;">EDAD:</span>
                             <span style="border-bottom: 1px solid #ccc; display: inline-block;">' .
                    $paciente->edad .
                    '</span>
                                     </td>
                         <td style="border: none; padding: 8px 4px 4px 4px; width: 20%;">
                             <span style="font-weight: bold; width: 40px; display: inline-block;">SEXO:</span>
                             <span style="border-bottom: 1px solid #ccc; display: inline-block;">' .
                    (($paciente->sexo === "M") ? "Femenino" : "Masculino") .
                    '</span>
                         </td>
                     </tr>
                     <tr>
                         <td colspan="3" style="border: none; padding: 8px 4px 4px 4px;">
                             <span style="font-weight: bold; width: 150px; display: inline-block;">FECHA DE NACIMIENTO:</span>
                             <span style="border-bottom: 1px solid #ccc; display: inline-block;">' .
                    date('d/m/Y', strtotime($fechaNacimiento)) . ' - ' . $paciente->lugar_nacimiento .
                    '</span>
                                     </td>
                                 </tr>
                     
                     <tr>
                         <td colspan="2" style="border: none; padding: 8px 4px 4px 4px;  width: 70%;">
                             <span style="font-weight: bold; width: 110px; display: inline-block;">ACOMPAÑANTE:</span>
                             <span style="border-bottom: 1px solid #ccc; display: inline-block;">' .
                    $paciente->acompanante .
                    '</span>
                         </td>
                         <td style="border: none; padding: 8px 4px 4px 4px; width: 30%;">
                             <span style="font-weight: bold; width: 70px; display: inline-block;">TELÉFONO:</span>
                             <span style="border-bottom: 1px solid #ccc; display: inline-block;">' .
                    $paciente->telefono_acompanate .
                    '</span>
                         </td>
                     </tr>  
                 </table>
             </div>';

                $html .= '<div class="section">
                     <h4> DIAGNÓSTICOS</h4>';
                if ($informe->impresion_diagnostica_princippal != null) {
                    $html .= '<p style="margin-bottom: 10px;"><strong>PRINCIPAL:</strong> ' . $informe->impresion_diagnostica_princippal_detalle->codigo . ' - ' . $informe->impresion_diagnostica_princippal_detalle->nombre . '</p>';
                }
                if ($informe->impresion_diagnostica_relacionada_1 != null) {
                    $html .= '<p style="margin-bottom: 10px;"><strong>RELACIONADO 1:</strong> ' . $informe->impresion_diagnostica_relacionada_1_detalle->codigo . ' - ' . $informe->impresion_diagnostica_relacionada_1_detalle->nombre . '</p>';
                }
                if ($informe->impresion_diagnostica_relacionada_2 != null) {
                    $html .= '<p style="margin-bottom: 10px;"><strong>RELACIONADO 2:</strong> ' . $informe->impresion_diagnostica_relacionada_2_detalle->codigo . ' - ' . $informe->impresion_diagnostica_relacionada_2_detalle->nombre . '</p>';
                }
                $html .= '</div>';


                $html .= '<div class="section">
                     <h4>ORDENES MÉDICAS</h4>';

                // Obtener las órdenes médicas del informe


                if (!empty($ordenesMedicas)) {
                    $html .= '<table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                     <thead>
                         <tr style="background-color:rgba(234, 235, 244, 0.47);">
                             <th style="border: 1px solid #000; padding: 8px; text-align: center; width: 10%;">No.</th>
                             <th style="border: 1px solid #000; padding: 8px; text-align: center; width: 75%;">PROCEDIMIENTO</th>
                             <th style="border: 1px solid #000; padding: 8px; text-align: center; width: 15%;">CANTIDAD</th>
                         </tr>
                     </thead>
                     <tbody>';

                    $contador = 1;
                    foreach ($ordenesMedicas as $orden) {
                        $html .= '<tr>
                         <td style="border: 1px solid #000; padding: 8px; text-align: center;">' . $contador . '</td>
                         <td style="border: 1px solid #000; padding: 8px;" class="text-procedimiento"><p>' . $orden->codigo . ' - ' . $orden->textoCodigo . '</p> <p><strong>OBSERVACIÓN:</strong> ' . $orden->observacion . '</p></td>
                         <td style="border: 1px solid #000; padding: 8px; text-align: center;">' . $orden->cantidad . '</td>
                     </tr>';
                        $contador++;
                    }

                    $html .= '</tbody></table>';
                } else {
                    $html .= '<p style="margin-top: 10px;">No se registraron órdenes médicas para este informe.</p>';
                }

                $html .= '</div>';

                $html .= '<div class="section">
                <table style="width:100%; border-collapse: collapse; background-color: transparent; margin-top: 20px;">
                         <tr><td class="no-border" style="text-align: left;"><img width="150" src="' . $firma . '" /><br><b>' . $profesional->nombre . '</b></td></tr>
                         <tr><td class="no-border" style="text-align: left; font-size: 10px;"><b>TARJETA PROFESIONAL: ' . $profesional->registro . '</td></tr>
                         </tr>
                     </table>
                 </div></div>';
            }

            $pdf->loadHtml($html);
            $pdf->setPaper('A4', 'portrait');
            $pdf->render();

            $pdfContent = $pdf->output();

            // Encabezados de respuesta para el archivo PDF
            $headers = [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="InformeNeuropsicologico.pdf"',
            ];

            return response($pdfContent, 200, $headers);
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function buscaPruebaVenta(Request $request)
    {
        $idPrueba = $request->input('idPrueba');
        $prueba = Pruebas::busquedaPaquetesVentas($idPrueba);

        return response()->json([
            'prueba' => $prueba
        ]);
    }

    public function  guardarPruebaVenta(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'estado' => 'error',
                'mensaje' => 'Su sesión ha terminado.',
            ], 401);
        }

        $data = $request->all();

        $respuesta = Pruebas::guardarPruebaVenta($data);

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

    public function listaPruebasModal(Request $request)
    {

        if (Auth::check()) {
            $perPage = 10; // Número de posts por página
            $page = request()->get('page', 1);
            $search = request()->get('search');
            if (!is_numeric($page)) {
                $page = 1; // Establecer un valor predeterminado si no es numérico
            }

            $idHist = $request->input('idHist');
            $tipoHist = $request->input('tipoHist');

            $pruebas = DB::connection('mysql')
                ->table('servicios')
                ->leftJoin("ventas", "servicios.id", "ventas.id_servicio")
                ->leftJoin("pruebas", "pruebas.id", "servicios.id_paquete")
                ->where('servicios.estado', 'ACTIVO')
                ->where('servicios.tipo', 'PRUEBAS')
                ->where('servicios.id_historia', $idHist)
                ->where('servicios.tipo_historia', $tipoHist)
                ->select(
                    'servicios.id',
                    'servicios.fecha AS fecha_compra',
                    'ventas.total AS monto_total',
                    'ventas.estado_venta AS estado_control',
                    'pruebas.descripcion',
                    'servicios.descripcion as descripcion_prueba'
                )
                ->groupBy(
                    'servicios.id',
                    'servicios.fecha',
                    'ventas.total',
                    'ventas.estado_venta',
                    'pruebas.descripcion',
                    'servicios.descripcion'
                );



            if ($search) {
                $pruebas->where(function ($query) use ($search) {
                    $query->where('pruebas.descripcion', 'LIKE', '%' . $search . '%');
                });
            }

            $ListPruebas = $pruebas->paginate($perPage, ['*'], 'page', $page);

            $tdTable = '';
            $x = ($page - 1) * $perPage + 1;

            foreach ($ListPruebas as $i => $item) {
                if (!is_null($item)) {
                    $valorTotal = number_format($item->monto_total, 2, ',', '.');

                    $tdTable .= '<tr style="cursor: pointer;">
                                    <td>' . $item->descripcion_prueba . '</td>
                                    <td>' . $item->fecha_compra . '</td>
                                    <td>$ ' . $valorTotal . '</td>
                                    <td>' . $item->estado_control . '</td>
                                    <td class="table-action min-w-100">
                                        <a onclick="editarPrueba(' . $item->id . ');" style="cursor: pointer;" title="Editar" class="text-fade hover-primary"><i class="align-middle"
                                                data-feather="edit-2"></i></a>
                                        <a onclick="eliminarPrueba(' . $item->id . ');" style="cursor: pointer;" title="Eliminar" class="text-fade hover-warning"><i class="align-middle"
                                                data-feather="trash"></i></a>
                                    </td>
                                </tr>';
                    $x++;
                }
            }

            $pagination = $ListPruebas->links('HistoriasClinica.PaginacionPruebas')->render();

            return response()->json([
                'pruebas' => $tdTable,
                'links' => $pagination
            ]);
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function buscaVentaConsultaNeuro(request $request)
    {
        $idHist = $request->input('idHist');

        $servicioConsulta = HistoriaNeuroPsicologica::busquedaVentaConsulta($idHist);

        if (!$servicioConsulta) {
            $servicioConsulta = HistoriaNeuroPsicologica::busquedaConsultaHistoria($idHist);
        }
        return response()->json([
            'servicioConsulta' => $servicioConsulta
        ]);
    }

    public function guardarPlanIntervencionNeuro()
    {
        try {
            $data = request()->all();
            $respuesta = HistoriaNeuroPsicologica::guardarPlanIntervencion($data);

            if ($respuesta) {
                return response()->json([
                    'success' => true,
                    'title' => '¡Buen trabajo!',
                    'message' => 'Plan de intervención guardado correctamente',
                    'id' => $respuesta
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'title' => '¡Opps salio algo mal!',
                    'message' => 'No se pudo guardar el plan de intervención'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'title' => '¡Opps salio algo mal!',
                'message' => 'Ocurrió un error al intentar guardar el plan de intervención',
                'error' => $e->getMessage()
            ]);
        }
    }


    public function informePsicologia()
    {
        if (Auth::check()) {
            return view('HistoriasClinica.informeNeuropsicologia');
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function buscaPlanIntervencionNeuro(request $request)
    {
        $idHist = $request->input('idHist');
        $planIntervencion = HistoriaNeuroPsicologica::busquedaPlanIntervencion($idHist);

        return response()->json([
            'planIntervencion' => $planIntervencion
        ]);
    }

    public function  guardarInformeNeuropsicologica(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'estado' => 'error',
                'mensaje' => 'Su sesión ha terminado.',
            ], 401);
        }

        $data = $request->all();
        self::guardarRespaldoFormulario($request, 'Informe neuropsicológico');


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

        $respuesta = HistoriaNeuroPsicologica::guardarInforme($data);

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


    public function eliminarInformeNeuro()
    {
        try {
            $idReg = request()->input('idReg');
            if (!$idReg) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'ID de la Informe no proporcionado'
                    ],
                    400
                );
            }

            $consulta = DB::connection('mysql')
                ->table('informe_evolucion_neuropsicologia')
                ->where('id', $idReg)
                ->update([
                    'estado' => 'ELIMINADO',
                ]);

            if ($consulta) {
                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Informe eliminado correctamente'
                    ]
                );
            } else {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'No se encontró la consulta o no se pudo eliminar'
                    ],
                    404
                );
            }
        } catch (\Exception $e) {
            // Manejar cualquier error o excepción
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Ocurrió un error al intentar eliminar la consulta',
                    'error' => $e->getMessage()
                ],
                500
            );
        }
    }

    public function eliminarAnexoInforme()
    {
        try {
            $idAnexo = request()->input('idAnexo');
            if (!$idAnexo) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'ID del anexo no proporcionado'
                    ],
                    400
                );
            }

            $anexo = DB::connection('mysql')
                ->table('anexos_informe_neuropsicologia')
                ->where('id', $idAnexo)
                ->first();
            if ($anexo) {
                unlink(public_path('anexosPacientes/' . $anexo->url));
            }

            $consulta = DB::connection('mysql')
                ->table('anexos_informe_neuropsicologia')
                ->where('id', $idAnexo)
                ->delete();

            if ($consulta) {
                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Anexo eliminado correctamente'
                    ]
                );
            } else {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'No se encontró el anexo o no se pudo eliminar'
                    ],
                    404
                );
            }
        } catch (\Exception $e) {
            // Manejar cualquier error o excepción
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Ocurrió un error al intentar eliminar el anexo',
                    'error' => $e->getMessage()
                ],
                500
            );
        }
    }

    public function listaPacientesInformeNeuropsicologia(Request $request)
    {
        if (Auth::check()) {
            $perPage = 10; // Número de posts por página
            $page = request()->get('page', 1);
            $search = request()->get('search');
            if (!is_numeric($page)) {
                $page = 1; // Establecer un valor predeterminado si no es numérico
            }

            $pacientesEvol = DB::connection('mysql')
                ->table('informe_evolucion_neuropsicologia')
                ->leftJoin('profesionales', 'profesionales.id', 'informe_evolucion_neuropsicologia.id_profesional')
                ->leftJoin('pacientes', 'pacientes.id', '=', 'informe_evolucion_neuropsicologia.id_paciente')
                ->where('informe_evolucion_neuropsicologia.estado', 'ACTIVO')
                ->orderBy('informe_evolucion_neuropsicologia.fecha_creacion', 'desc')
                ->select(
                    'informe_evolucion_neuropsicologia.id',
                    'profesionales.nombre as profesional',
                    'informe_evolucion_neuropsicologia.fecha_creacion',
                    DB::raw("CONCAT(pacientes.tipo_identificacion, ' ', pacientes.identificacion) as identificacion"),
                    DB::raw("CONCAT(primer_nombre, ' ', segundo_nombre, ' ', primer_apellido, ' ', segundo_apellido) as nombre"),
                )
                ->groupBy('informe_evolucion_neuropsicologia.id', 'tipo_identificacion', 'identificacion', 'primer_nombre', 'segundo_nombre', 'primer_apellido', 'segundo_apellido', 'profesionales.nombre', 'informe_evolucion_neuropsicologia.fecha_creacion');



            if ($search) {
                $pacientesEvol->where(function ($query) use ($search) {
                    $query->where('pacientes.identificacion', 'LIKE', '%' . $search . '%')
                        ->orWhere('pacientes.primer_nombre', 'LIKE', '%' . $search . '%')
                        ->orWhere('pacientes.segundo_nombre', 'LIKE', '%' . $search . '%')
                        ->orWhere('pacientes.primer_apellido', 'LIKE', '%' . $search . '%')
                        ->orWhere('pacientes.segundo_apellido', 'LIKE', '%' . $search . '%');
                });
            }


            $ListPacientesEvol = $pacientesEvol->paginate($perPage, ['*'], 'page', $page);

            $tdTable = '';
            $x = ($page - 1) * $perPage + 1;

            foreach ($ListPacientesEvol as $i => $item) {
                if (!is_null($item)) {
                    $tdTable .= '<tr>
                                    <td style="text-transform: capitalize;">' . $item->identificacion . ' - ' . $item->nombre . '</td>
                                    <td>' . date('d/m/Y g:i:s A', strtotime($item->fecha_creacion)) . '</td>
                                    <td style="text-transform: capitalize;">' . $item->profesional . '</td>
                                    <td>
                                        <a onclick="imprimirInforme(' . $item->id . ');" style="cursor: pointer;" title="Imprimir informe" class="text-fade hover-warning"><i class="align-middle"
                                                data-feather="file-text"></i></a>
                                        <a onclick="editarInforme(' . $item->id . ');" style="cursor: pointer;" title="Editar" class="text-fade hover-primary"><i class="align-middle"
                                                data-feather="edit-2"></i></a>
                                        <a onclick="eliminarInforme(' . $item->id . ');" style="cursor: pointer;" title="Eliminar" class="text-fade hover-warning"><i class="align-middle"
                                                data-feather="trash"></i></a>
                                    </td>                               
                                </tr>';
                    $x++;
                }
            }

            $pagination = $ListPacientesEvol->links('HistoriasClinica.Paginacion')->render();

            return response()->json([
                'pacientesEvol' => $tdTable,
                'links' => $pagination
            ]);
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function informeNeuropsicologiaList(Request $request)
    {
        if (Auth::check()) {
            $perPage = 5; // Número de posts por página
            $page = request()->get('page', 1);
            $idPaciente = request()->get('idPac');
            if (!is_numeric($page)) {
                $page = 1; // Establecer un valor predeterminado si no es numérico
            }

            $informes = DB::connection('mysql')
                ->table('informe_evolucion_neuropsicologia')
                ->leftJoin("profesionales", "profesionales.id", "informe_evolucion_neuropsicologia.id_profesional")
                ->where("informe_evolucion_neuropsicologia.estado", "ACTIVO")
                ->where("informe_evolucion_neuropsicologia.id_paciente", $idPaciente)
                ->orderBy('informe_evolucion_neuropsicologia.fecha_creacion', 'desc')
                ->select(
                    'informe_evolucion_neuropsicologia.id',
                    'informe_evolucion_neuropsicologia.fecha_creacion',
                    'profesionales.nombre AS profesional'
                );


            $ListInformes = $informes->paginate($perPage, ['*'], 'page', $page);

            $tdTable = '';
            $x = ($page - 1) * $perPage + 1;
            $const = 1;
            foreach ($ListInformes as $i => $item) {
                if (!is_null($item)) {
                    $tdTable .= '<tr>
                                    <td>' . $const . '</td>
                                    <td>' . $item->profesional . '</td>
                                    <td>' . date('d/m/Y g:i:s A', strtotime($item->fecha_creacion)) . '</td>
                                    <td class="table-action min-w-100">
                                        <a onclick="descargarArchivos(' . $item->id . ');" style="cursor: pointer;" title="Descargar informes" class="text-fade hover-primary"><i class="align-middle"
                                                data-feather="file-text"></i></a>
                                        <a onclick="editarInforme(' . $item->id . ');" style="cursor: pointer;" title="Editar" class="text-fade hover-primary"><i class="align-middle"
                                                data-feather="edit-2"></i></a>
                                        <a onclick="eliminarInforme(' . $item->id . ');" style="cursor: pointer;" title="Eliminar" class="text-fade hover-warning"><i class="align-middle"
                                                data-feather="trash"></i></a>
                                    </td>
                                </tr>';
                    $x++;
                    $const++;
                }
            }
            $pagination = $ListInformes->links('HistoriasClinica.PaginacionConsultas')->render();

            return response()->json([
                'informes' => $tdTable,
                'links' => $pagination
            ]);
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function buscaInformeNeuropsicologica(Request $request)
    {
        $idInforme = $request->input('idInforme');
        $informe = HistoriaNeuroPsicologica::busquedaInforme($idInforme);
        $anexos = HistoriaNeuroPsicologica::busquedaAnexosInformes($idInforme);
        $ordenMedica = HistoriaNeuroPsicologica::busquedaOrdenMedica($idInforme);
        return response()->json([
            'informe' => $informe,
            'anexos' => $anexos,
            'ordenMedica' => $ordenMedica
        ]);
    }

    public function buscarAnexosInforme(Request $request)
    {
        $idInforme = $request->input('idInforme');
        $anexos = HistoriaNeuroPsicologica::busquedaAnexosInformes($idInforme);

        return response()->json([
            'anexos'  => $anexos
        ]);
    }



    public function  guardarHistoriaNeuroPsicologica(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'estado' => 'error',
                'mensaje' => 'Su sesión ha terminado.',
            ], 401);
        }

        try {
            $data = $request->all();
            self::guardarRespaldoFormulario($request, 'Historia neuropsicológica');
            $respuesta = HistoriaNeuroPsicologica::Guardar($data);

            return response()->json($respuesta);
        } catch (\Exception $e) {
            Log::error('Error en guardarHistoriaNeuroPsicologica: ' . $e->getMessage(), [
                'data' => $request->all(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la solicitud',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function guardarRespaldoFormulario($data, $formulario)
    {
        if ($formulario == 'Historia neuropsicológica' || $formulario == 'Informe neuropsicológico') {
            $p = Pacientes::BuscarPaciente($data->idPaciente);
        } else {
            $p = HistoriaNeuroPsicologica::BuscarHistoria($data->idHist);
        }
        $respaldo = RespaldoFormulario::create([
            'datos' => $data->except(['_token']), // Guardamos todo menos el token CSRF
            'user_id' => Auth::check() ? Auth::id() : null,
            'formulario' => $formulario,
            'paciente' => $p->primer_nombre . ' ' . $p->segundo_nombre . ' ' . $p->primer_apellido . ' ' . $p->segundo_apellido
        ]);
    }

    public function eliminarConsultaNeuro()
    {
        try {
            $idConsulta = request()->input('idConsulta');
            if (!$idConsulta) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'ID de la consulta no proporcionada'
                    ],
                    400
                );
            }

            $consulta = DB::connection('mysql')
                ->table('consultas_psicologica_neuro')
                ->where('id', $idConsulta)
                ->update([
                    'estado' => 'ELIMINADO',
                ]);



            if ($consulta) {
                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Consulta eliminada correctamente'
                    ]
                );
            } else {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'No se encontró el Informe o no se pudo eliminar'
                    ],
                    404
                );
            }
        } catch (\Exception $e) {
            // Manejar cualquier error o excepción
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Ocurrió un error al intentar eliminar el Informe',
                    'error' => $e->getMessage()
                ],
                500
            );
        }
    }

    public function  guardarConsultaNeuroPsicologica(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'estado' => 'error',
                'mensaje' => 'Su sesión ha terminado.',
            ], 401);
        }

        $data = $request->all();
        self::guardarRespaldoFormulario($request, 'Consulta neuropsicológica');
        $respuesta = HistoriaNeuroPsicologica::guardarConsulta($data);

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

    public function buscaConsultaNeuroPsicologica(Request $request)
    {
        $idConsulta = $request->input('idConsulta');
        $consulta = HistoriaNeuroPsicologica::busquedaConsulta($idConsulta);

        return response()->json([
            'consulta' => $consulta
        ]);
    }

    public function listaConsultasModalNeuro(Request $request)
    {
        if (Auth::check()) {
            $perPage = 5; // Número de posts por página
            $page = request()->get('page', 1);
            $search = request()->get('search');
            $idHist = request()->get('idHist');
            if (!is_numeric($page)) {
                $page = 1; // Establecer un valor predeterminado si no es numérico
            }

            $consultas = DB::connection('mysql')
                ->table('consultas_psicologica_neuro')
                ->leftJoin("profesionales", "profesionales.id", "consultas_psicologica_neuro.id_profesional")
                ->where("consultas_psicologica_neuro.estado", "ACTIVO")
                ->where("consultas_psicologica_neuro.id_historia", $idHist)
                ->orderBy('consultas_psicologica_neuro.fecha_consulta', 'desc')
                ->select(
                    'consultas_psicologica_neuro.id',
                    'consultas_psicologica_neuro.fecha_consulta',
                    'profesionales.nombre AS profesional'
                );

            if ($search) {
                $consultas->where(function ($query) use ($search) {
                    $query->where('profesionales.nombre', 'LIKE', '%' . $search . '%');
                });
            }

            $ListConsultas = $consultas->paginate($perPage, ['*'], 'page', $page);

            $tdTable = '';
            $x = ($page - 1) * $perPage + 1;
            $const = 1;
            foreach ($ListConsultas as $i => $item) {
                if (!is_null($item)) {
                    $tdTable .= '<tr>
                                    <td>' . $item->fecha_consulta . '</td>
                                    <td>' . $item->profesional . '</td>
                                    <td class="table-action min-w-100">
                                     <a onclick="imprimirConsulta(' . $item->id . ');" style="cursor: pointer;" title="Imprimir" class="text-fade hover-primary"><i class="align-middle"
                                                data-feather="file-text"></i></a>
                                        <a onclick="editarConsulta(' . $item->id . ');" style="cursor: pointer;" title="Editar" class="text-fade hover-primary"><i class="align-middle"
                                                data-feather="edit-2"></i></a>
                                        <a onclick="eliminarConsulta(' . $item->id . ');" style="cursor: pointer;" title="Eliminar" class="text-fade hover-warning"><i class="align-middle"
                                                data-feather="trash"></i></a>
                                    </td>
                                </tr>';
                    $x++;
                    $const++;
                }
            }
            $pagination = $ListConsultas->links('HistoriasClinica.PaginacionConsultas')->render();

            $consutlasLateral = self::consultasLateral($idHist);

            return response()->json([
                'consultas' => $tdTable,
                'links' => $pagination,
                'historialConsultas' => $consutlasLateral
            ]);
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function listaHistoriasNeuroPsicologica(Request $request)
    {

        if (Auth::check()) {
            $perPage = 5;
            $page = request()->get('page', 1);
            $search = request()->get('search');
            if (!is_numeric($page)) {
                $page = 1;
            }

            $historias = DB::connection('mysql')
                ->table('historia_clinica_neuro')
                ->leftJoin('pacientes', 'historia_clinica_neuro.id_paciente', 'pacientes.id')
                ->leftJoin('profesionales', 'historia_clinica_neuro.id_profesional', 'profesionales.id')
                ->where('estado_registro', 'ACTIVO')
                ->orderBy('historia_clinica_neuro.fecha_historia', 'desc')
                ->select(
                    "historia_clinica_neuro.id",
                    DB::raw("CONCAT(pacientes.tipo_identificacion, ' ', pacientes.identificacion) as identificacion_completa"),
                    DB::raw("CONCAT(primer_nombre,' ',segundo_nombre,' ',primer_apellido,' ', segundo_apellido) as nombre_completo"),
                    "historia_clinica_neuro.fecha_historia",
                    "historia_clinica_neuro.tipologia",
                    "historia_clinica_neuro.estado_hitoria",
                    "pacientes.fecha_nacimiento",
                    'historia_clinica_neuro.codigo_consulta',
                    'pacientes.id as id_paciente',
                    'profesionales.nombre as profesional',
                    'historia_clinica_neuro.porcentaje_completitud'

                );

                if ($search) {
                    $historias->where(function ($query) use ($search) {
                        // Búsqueda por identificación
                        $query->where('pacientes.identificacion', 'LIKE', '%' . $search . '%');
                        
                        // Búsqueda individual por campos
                        $query->orWhere('pacientes.primer_nombre', 'LIKE', '%' . $search . '%')
                            ->orWhere('pacientes.segundo_nombre', 'LIKE', '%' . $search . '%')
                            ->orWhere('pacientes.primer_apellido', 'LIKE', '%' . $search . '%')
                            ->orWhere('pacientes.segundo_apellido', 'LIKE', '%' . $search . '%');
                        
                        // Búsqueda en nombre completo (exacta)
                        $query->orWhere(DB::raw("CONCAT(pacientes.primer_nombre,' ',pacientes.segundo_nombre,' ',pacientes.primer_apellido,' ', pacientes.segundo_apellido)"), 'LIKE', '%' . $search . '%');
                        
                        // Búsqueda por palabras en desorden: divide el término de búsqueda en palabras
                        // y verifica que todas estén presentes en el nombre completo
                        $palabras = array_filter(explode(' ', trim($search)));
                        if (count($palabras) > 1) {
                            $query->orWhere(function ($subQuery) use ($palabras) {
                                $nombreCompleto = DB::raw("CONCAT(pacientes.primer_nombre,' ',pacientes.segundo_nombre,' ',pacientes.primer_apellido,' ', pacientes.segundo_apellido)");
                                foreach ($palabras as $palabra) {
                                    $subQuery->where($nombreCompleto, 'LIKE', '%' . trim($palabra) . '%');
                                }
                            });
                        }
                    });
                }

            $ListHistoria = $historias->paginate($perPage, ['*'], 'page', $page);

            $tdTable = '';
            $x = ($page - 1) * $perPage + 1;

            foreach ($ListHistoria as $i => $item) {
                if (!is_null($item)) {


                    if ($item->estado_hitoria == "abierta") {
                        $estado = "<i class='fa fa-unlock'></i> Abierta";
                        $class = "text-success";
                        $disabled = "";
                    } else {
                        $estado = "<i class='fa fa-unlock-alt'></i> Cerrada";
                        $class = "text-danger";
                        $disabled = "disabled";
                    }

                    $fechaNacimiento = $item->fecha_nacimiento;
                    $fechaNacimiento = \Carbon\Carbon::parse($fechaNacimiento);
                    $fechaActual = \Carbon\Carbon::now();
                    $diferencia = $fechaActual->diff($fechaNacimiento);
                    $edadTexto = "{$diferencia->y} años, {$diferencia->m} meses, y {$diferencia->d} días";


                    $porcentajeCompletitud = $item->porcentaje_completitud;
                    if ($porcentajeCompletitud >= 90) {
                        $bgColor = 'bg-success'; // Verde intenso
                    } elseif ($porcentajeCompletitud >= 70) {
                        $bgColor = 'bg-primary'; // Azul
                    } elseif ($porcentajeCompletitud >= 50) {
                        $bgColor = 'bg-info'; // Celeste
                    } elseif ($porcentajeCompletitud >= 20) {
                        $bgColor = 'bg-warning'; // Amarillo/naranja
                    } else {
                        $bgColor = 'bg-danger'; // Rojo
                    }

                    $tdTable .= ' <div class="box pull-up">
                    <div class="box-body">
                        <div class="d-md-flex justify-content-between align-items-center">
                            <div>
                                <p><span class="text-primary">Historia Clínica</span> | 
                                <span class="text-fade">Tipo: Neuropsicológica - ' . $item->tipologia . '</span> 
                                | <span class="text-primary">Profesional:</span> 
                                <span class="text-fade" style="text-transform: capitalize;">' . $item->profesional . '</span>
                                </p>
                                <h3 class="mb-0 fw-500">Paciente: ' . $item->identificacion_completa . ' - ' . $item->nombre_completo . '</h3>
                            </div>
                            <div class="mt-10 mt-md-0">
                        <div class="btn-group mb-5">
                        <div style="margin-right: 20px;">
                                        <div>
                                            <p class="text-fade m-0">Completada</p>
                                                <div> <label
                                                    id="tasaPorcentaje">' . $porcentajeCompletitud . '%</label>
                                                <div
                                                    class="progress progress-lg">
                                                        <div id="tasaBarraPorcentaje"
                                                        class="progress-bar ' . $bgColor . '"
                                                        role="progressbar"
                                                            style="width: ' . $porcentajeCompletitud . '%"
                                                        aria-valuenow="75"
                                                        aria-valuemin="0"
                                                        aria-valuemax="100">
                                                </div>
                                                </div>
                                                </div>
                                            </div>
								    </div>
                    </div> 
                    <button type="button" data-id="' . $item->id . '" dapta-tipo="' . $item->tipologia . '" onclick="verHistoria(this)" class="waves-effect waves-light btn btn-info mb-5"><i class="fa fa-search"></i> Ver detalle</button>
                    </div>
                        </div>
                        <hr>
                        <div class="d-md-flex justify-content-between align-items-center">
                            <div class="d-flex justify-content-start align-items-center">
                            <div class=" mx-20 min-w-70">
                                    <p class="mb-0 text-fade">Edad</p>
                                    <h6 class="mb-0">' . $edadTexto . '</h6>
                                </div>    
                            <div >
                                    <p class="mb-0 text-fade">Fecha de Creación</p>
                                    <h6 class="mb-0">' . date('d/m/Y g:i:s A', strtotime($item->fecha_historia)) . '</h6>
                                </div>
                                <div style="cursor:pointer;" data-id="' . $item->id . '" data-estado="' . $item->estado_hitoria . '" onclick="cerrarHistoria(this)" class="mx-lg-50 mx-20 min-w-70">
                                    <p class="mb-0 text-fade">Estado</p>
                                    <h6 class="mb-0 ' . $class . '">' . $estado . '</h6>
                                </div>
                            </div>
                            <div class="mt-10 mt-md-0">
                                <button type="button" ' . $disabled . ' data-id="' . $item->id . '" data-tipo="' . $item->tipologia . '" onclick="editarHistoria(this);"
                                    class="waves-effect waves-light btn btn-primary btn-flat"><i
                                        class="fa fa-edit me-10"></i>Editar</button>
                                <button type="button" data-id="' . $item->id . '" data-estado="' . $item->estado_hitoria . '" data-id-paciente="' . $item->id_paciente . '" onclick="evolucionHistoria(this);"
                                    class="waves-effect waves-light btn btn-secondary btn-flat"><i
                                        class="fa fa-arrow-right me-10"></i>Evolución</button>
                                <button type="button" data-id="' . $item->id . '"  onclick="PlanIntervencionHistoria(this);"
                                    class="waves-effect waves-light btn btn-warning btn-flat"><i
                                        class="fa fa-list me-10"></i>Plan de intervención</button>
                                <button type="button" onclick="imprimirHistoria(' . $item->id . ');"
                                    class="waves-effect waves-light btn btn-info btn-flat"><i
                                        class="fa fa-print me-10"></i>Imprimir</button>
                                <button type="button" onclick="eliminarHistoria(' . $item->id . ');"
                                    class="waves-effect waves-light btn btn-danger btn-flat"><i
                                        class="fa fa-trash-o me-10"></i>Eliminar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>';
                    $x++;
                }
            }

            $pagination = $ListHistoria->links('HistoriasClinica.Paginacion')->render();

            return response()->json([
                'historias' => $tdTable,
                'links' => $pagination
            ]);
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function buscaHistoriaNeuroPsicologica(Request $request)
    {
        $idHist = $request->input('idHist');


        $historia = HistoriaNeuroPsicologica::busquedaHistoriaNeuro($idHist);
        $pacientes = Pacientes::busquedaPaciente($historia->id_paciente);
        $antecedentesPersonales = HistoriaNeuroPsicologica::busquedaAntecedentes($historia->id);
        $antecedentesFamiliares = HistoriaNeuroPsicologica::busquedaAntFamiliares($historia->id);
        $areaAjuste = HistoriaNeuroPsicologica::busquedaAreaAjuste($historia->id);
        $interconuslta = HistoriaNeuroPsicologica::busquedaInterconsulta($historia->id);
        $examenMental = HistoriaNeuroPsicologica::busquedaExamenMental($historia->id);
        $antecedentesPrenatales = HistoriaNeuroPsicologica::busquedaAntPrenatales($historia->id);
        $antecedentesNatales = HistoriaNeuroPsicologica::busquedaAntNatales($historia->id);
        $antecedentesPosnatales = HistoriaNeuroPsicologica::busquedaAntPosnatales($historia->id);
        $desarrolloPsicomotor = HistoriaNeuroPsicologica::desarrolloPsicomotor($historia->id);

        $ordenMedica = HistoriaNeuroPsicologica::busquedaOrdenMedica($historia->id, 'HISTORIA');

        $historiaCon = self::consultasLateral($historia->id);
        return response()->json([
            'historia' => $historia,
            'paciente' => $pacientes,
            'antecedentesPersonales' => $antecedentesPersonales,
            'antecedentesFamiliares' => $antecedentesFamiliares,
            'areaAjuste' => $areaAjuste,
            'interconuslta' => $interconuslta,
            'examenMental' => $examenMental,
            'antecedentesPrenatales' => $antecedentesPrenatales,
            'antecedentesNatales' => $antecedentesNatales,
            'antecedentesPosnatales' => $antecedentesPosnatales,
            'desarrolloPsicomotor' => $desarrolloPsicomotor,
            'historialConsultas' => $historiaCon,
            'ordenMedica' => $ordenMedica
        ]);
    }

    public function consultasLateral($idHistoria)
    {
        $historialConsultas = HistoriaNeuroPsicologica::historialConsultas($idHistoria);

        $historiaCon = "";
        $mt = "mt-4";
        foreach ($historialConsultas as $i => $item) {

            if ($i > 0) {
                $mt = "mb-0";
            }

            $historiaCon .= '<div class="' . $mt . '">
            <div class="mb-20" style="border: 1px solid #cfcfcf; border-radius: 10px; padding: 10px;">
                <div class="dropdown float-end">
                    <a href="#" class="dropdown-toggle no-caret"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="mdi mdi-dots-vertical"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                    <a href="javascript:verConsulta(' . $item->id . ');"
                            class="dropdown-item"><i class="fa fa-eye"></i> Ver</a>    
                    <a href="javascript:imprimirConsulta(' . $item->id . ');"
                            class="dropdown-item"><i class="fa fa-print"></i> Imprimir</a>
                    </div> <!-- item-->
                </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div
                                class="bg-transparent h-50 w-50 border border-light product_icon text-center">
                                <p class="mb-0 fs-20 w-50 fw-600 l-h-40"><i
                                    class="fa fa-stethoscope"
                                    aria-hidden="true"></i>
                                </p>
                            </div>
                        </div>
                        <p style="margin: 0px" class="fs-16">' . $item->fecha_consulta . '</p>
                        <div>
                            <div class="d-flex flex-column font-weight-500">
                                <span class="text-fade text-end"><i
                                        class="fa fa-user-md"></i> ' . $item->profesional . '</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>';
        }
        return $historiaCon;
    }

    public function imprimirHistoria(Request $request)
    {
        $idHist = $request->input('idHist');

        $historia = HistoriaNeuroPsicologica::busquedaHistoriaNeuro($idHist);
        $pacientes = Pacientes::busquedaPaciente($historia->id_paciente);
        $antecedentesPersonales = HistoriaNeuroPsicologica::busquedaAntecedentes($historia->id);
        $antecedentesFamiliares = HistoriaNeuroPsicologica::busquedaAntFamiliares($historia->id);
        $areaAjuste = HistoriaNeuroPsicologica::busquedaAreaAjuste($historia->id);
        $interconuslta = HistoriaNeuroPsicologica::busquedaInterconsulta($historia->id);
        $examenMental = HistoriaNeuroPsicologica::busquedaExamenMental($historia->id);

        $antecedentesPrenatales = HistoriaNeuroPsicologica::busquedaAntPrenatales($historia->id);
        $antecedentesNatales = HistoriaNeuroPsicologica::busquedaAntNatales($historia->id);
        $antecedentesPosnatales = HistoriaNeuroPsicologica::busquedaAntPosnatales($historia->id);
        $desarrolloPsicomotor = HistoriaNeuroPsicologica::desarrolloPsicomotor($historia->id);
        $ordenMedica = HistoriaNeuroPsicologica::busquedaOrdenMedica($historia->id, 'HISTORIA');
        $data = [
            'historia' => $historia,
            'paciente' => $pacientes,
            'antecedentesPersonales' => $antecedentesPersonales,
            'antecedentesFamiliares' => $antecedentesFamiliares,
            'areaAjuste' => $areaAjuste,
            'interconuslta' => $interconuslta,
            'examenMental' => $examenMental,
            'antecedentesPrenatales' => $antecedentesPrenatales,
            'antecedentesNatales' => $antecedentesNatales,
            'antecedentesPosnatales' => $antecedentesPosnatales,
            'desarrolloPsicomotor' => $desarrolloPsicomotor,
            'ordenMedica' => $ordenMedica
        ];

        $pdf = PDF::loadView('imprimir.imprimirHistoriaNeuro', $data)->setPaper('a4');

        $fileName = 'Historia_neuro_' . $idHist . '.pdf';
        $filePath = 'historias_neuro/' . $fileName;
        $pdf->save(public_path($filePath));
        $url = asset($filePath);

        return response()->json(['url' => $url]);
    }

    public function cerrarHistoriaNeuro()
    {
        try {
            $idHist = request()->input('idHist');
            $estado = request()->input('estado');
            if (!$idHist) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'ID de la historia no proporcionada'
                    ],
                    400
                );
            }

            $consulta = DB::connection('mysql')
                ->table('historia_clinica_neuro')
                ->where('id', $idHist)
                ->update([
                    'estado_hitoria' => ($estado == 'abierta' ? 'cerrada' : 'abierta'),
                ]);


            if ($consulta) {
                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Estado de la historia cambiado correctamente'
                    ]
                );
            } else {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'No se encontró la historia'
                    ],
                    404
                );
            }
        } catch (\Exception $e) {
            // Manejar cualquier error o excepción
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Ocurrió un error al intentar cambiar el estado de la historia',
                    'error' => $e->getMessage()
                ],
                500
            );
        }
    }

    public function imprimirConsultaNeuro(Request $request)
    {
        $idConsulta = $request->input('idConsulta');

        if (Auth::check()) {
            $pdf = new Dompdf();

            $consulta = HistoriaNeuroPsicologica::busquedaConsultaImprimir($idConsulta);

            $idPaciente = DB::connection('mysql')->table('historia_clinica_neuro')
                ->where('id', $consulta->id_historia)
                ->value('id_paciente');

            // Ruta absoluta al logo
            $logoPath = public_path('app-assets/images/logo/logo_prasca.png');

            // Convertir la imagen a base64
            $logoData = base64_encode(file_get_contents($logoPath));
            $logo = 'data:image/png;base64,' . $logoData;

            $fechaElaboracion = \Carbon\Carbon::parse($consulta->fecha_consulta)->format('d/m/Y h:i A');

            $paciente = Pacientes::busquedaPaciente($idPaciente);

            $profesional = Profesional::busquedaProfesional($consulta->id_profesional);

            $firmaPath = public_path('app-assets/images/firmasProfesionales/' . $profesional->firma);
            $firmaData = base64_encode(file_get_contents($firmaPath));
            $firma = 'data:image/png;base64,' . $firmaData;

            $fechaNacimiento = \Carbon\Carbon::parse($paciente->fecha_nacimiento)->format('d/m/Y h:i A');


            $formatRichText = function (?string $html) {
                if (empty($html)) {
                    return '';
                }

                $decoded = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $decoded = preg_replace('/<p[^>]*>\s*(?:&nbsp;|\s)*<\/p>/i', '', $decoded);

                $decoded = preg_replace_callback('/<p\b([^>]*)>/i', function ($matches) {
                    $attrs = $matches[1];
                    if (stripos($attrs, 'style=') !== false) {
                        $attrs = preg_replace_callback('/style="([^"]*)"/i', function ($styleMatches) {
                            $style = trim($styleMatches[1]);
                            $style .= (strlen($style) && substr($style, -1) === ';') ? '' : ';';
                            $style .= 'margin:0 0 6px !important; line-height:1.45 !important;';
                            return 'style="' . $style . '"';
                        }, $attrs, 1);
                    } else {
                        $attrs .= ' style="margin:0 0 6px !important; line-height:1.45 !important;"';
                    }
                    return '<p' . $attrs . '>';
                }, $decoded);

                return $decoded;
            };

            $html = '<head>
                <style>
                    @page {
                        margin: 12mm 18mm;
                    }
                    
                    body {
                        position: relative;
                        font-family: Times New Roman, Arial, sans-serif;
                        color: #333;
                        line-height: 1.4;
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
                        opacity: 0.12;
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
                        background-color: rgba(234, 235, 244, 0.6);
                        font-size: 12px;
                        line-height: 1.3;
                        text-align: left;
                    }
                    
                    td {
                        font-size: 11px;
                        line-height: 1.4;
                        text-align: left;
                    }
                    
                    .no-border {
                        border: none;
                        text-align: center;
                    }
                    
                    .section {
                        margin-bottom: 8px;
                        page-break-inside: avoid;
                    }
                    
                    .section-title {
                                  background: linear-gradient(135deg, rgba(102, 126, 234, 0.85) 0%,rgb(230, 208, 245)) 100%);
                        color:rgb(0, 1, 5);
                        padding: 6px 10px;
                        margin: 10px 0 6px 0;
                        font-size: 13px;
                        font-weight: bold;
                        border-radius: 2px;
                    }
                    
                    .info-box {
                        background-color: rgba(248, 249, 250, 0.5);
                        border-left: 3px solid rgb(138, 157, 238);
                        padding: 8px 10px;
                        margin-bottom: 6px;
                        border-radius: 2px;
                    }
                    
                    .info-box p {
                        margin: 0;
                        padding: 0;
                    }
                    
                    .field-label {
                        font-weight: bold;
                        color: #495057;
                        font-size: 11px;
                        display: block;
                        margin-bottom: 3px;
                    }
                    
                    .field-value {
                        color: #212529;
                        font-size: 11px;
                        line-height: 1.4;
                        text-align: justify;
                    }
                    
                    .subsection {
                        margin-top: 6px;
                        padding: 6px 8px;
                        background-color: rgba(255, 255, 255, 0.4);
                        border-left: 2px solid #667eea;
                        border-radius: 2px;
                    }
                    
                    .subsection p {
                        margin: 0;
                        padding: 0;
                    }
                    
                    .header-info {
                        font-size: 10px;
                        line-height: 1.3;
                    }
                    
                    h3 {
                        color: #667eea;
                        margin: 8px 0;
                        font-size: 15px;
                    }
                    
                    hr {
                        border: none;
                        border-top: 1px solid #e0e0e0;
                        margin: 10px 0;
                    }
                </style>
            </head>';

            $html .= '<div class="content">';
            $html .= '<table style="width:100%; border-collapse: collapse; background-color: transparent; margin-bottom: 8px;">';
            $html .= '<tr>';
            $html .= '<td class="no-border" style="padding: 0; width: 35%;"><img src="' . $logo . '" style="width: 160px; height: auto;"></td>';
            $html .= '<td class="no-border" style="padding: 0; vertical-align: top; text-align: right;">';
            $html .= '<p class="header-info" style="margin: 0; font-weight: bold;">DRA. MARIA ISABEL PUMAREJO</p>';
            $html .= '<p class="header-info" style="margin: 0;">PSICÓLOGA CLÍNICA - T.P. No. 259542</p>';
            $html .= '<p class="header-info" style="margin: 0;">Calle 11 # 11 - 07 San Joaquin</p>';
            $html .= '<p class="header-info" style="margin: 0;">Teléfono: 312 5678078</p>';
            $html .= '</td>';
            $html .= '</tr>';
            $html .= '</table>';

            $html .= '<div style="text-align: center; margin: 12px 0 8px 0;">';
            $html .= '<h3 style="margin: 3px 0; text-transform: uppercase;">RESULTADO DE CONSULTA NEUROPSICOLÓGICA</h3>';
            $html .= '<p style="font-size: 10px; color: #666; margin: 3px 0;"><b>FECHA DE ELABORACIÓN:</b> ' . $fechaElaboracion . '</p>';
            $html .= '</div>';

            $html .= '<hr>';

            $html .= '<div class="section-title">1. DATOS DE IDENTIFICACIÓN DEL PACIENTE</div>';
            $html .= '<div class="info-box">';
            $html .= '<table style="width: 100%; border-collapse: collapse; border: none;">';
            $html .= '<tr>';
            $html .= '<td colspan="3" style="border: none; padding: 3px 0;">';
            $html .= '<span class="field-label">NOMBRE COMPLETO:</span>';
            $html .= '<span class="field-value">' .
                trim($paciente->primer_nombre . ' ' .
                    $paciente->segundo_nombre . ' ' .
                    $paciente->primer_apellido . ' ' .
                    $paciente->segundo_apellido) .
                '</span>';
            $html .= '</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td style="border: none; padding: 3px 0; width: 50%;">';
            $html .= '<span class="field-label">IDENTIFICACIÓN:</span>';
            $html .= '<span class="field-value">' . $paciente->tipo_identificacion . ' ' . $paciente->identificacion . '</span>';
            $html .= '</td>';
            $html .= '<td style="border: none; padding: 3px 0; width: 25%;">';
            $html .= '<span class="field-label">EDAD:</span>';
            $html .= '<span class="field-value">' . $paciente->edad . ' años</span>';
            $html .= '</td>';
            $html .= '<td style="border: none; padding: 3px 0; width: 25%;">';
            $html .= '<span class="field-label">SEXO:</span>';
            $html .= '<span class="field-value">' . (($paciente->sexo === "M") ? "Femenino" : "Masculino") . '</span>';
            $html .= '</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td colspan="3" style="border: none; padding: 3px 0;">';
            $html .= '<span class="field-label">FECHA Y LUGAR DE NACIMIENTO:</span>';
            $fechaNacimientoFormateada = \Carbon\Carbon::parse($paciente->fecha_nacimiento)->format('d/m/Y');
            $html .= '<span class="field-value">' . $fechaNacimientoFormateada . ' - ' . $paciente->lugar_nacimiento . '</span>';
            $html .= '</td>';
            $html .= '</tr>';
            $html .= '</table>';
            $html .= '</div>';

            $html .= '<div class="section-title">2. DETALLE DE LA CONSULTA</div>';

            // Evolución y plan de trabajo
            if (!empty($consulta->evolucion_y_o_plantrabajo)) {
                $html .= '<div class="subsection">';
                $html .= '<span class="field-label">EVOLUCIÓN Y/O PLAN DE TRABAJO:</span>';
                $html .= '<p class="field-value">' . $formatRichText($consulta->evolucion_y_o_plantrabajo) . '</p>';
                $html .= '</div>';
            }

            // Separador antes de la firma
            $html .= '<div style="margin-top: 20px; margin-bottom: 10px;"></div>';

            // Sección de firma del profesional
            $html .= '<div style="margin-top: 15px; padding-top: 10px; border-top: 1px solid rgba(224, 224, 224, 0.6);">';
            $html .= '<table style="width:100%; border-collapse: collapse; background-color: transparent;">';
            $html .= '<tr><td class="no-border" style="text-align: left; padding: 3px 0;">';
            $html .= '<p style="margin: 0; font-size: 11px; font-weight: bold; color: #495057;">' . $profesional->nombre . '</p>';
            $html .= '</td></tr>';
            $html .= '<tr><td class="no-border" style="text-align: left; padding: 6px 0;">';
            $html .= '<img width="130" src="' . $firma . '" style="display: block;" />';
            $html .= '</td></tr>';
            $html .= '<tr><td class="no-border" style="text-align: left; padding: 3px 0;">';
            $html .= '<p style="margin: 0; font-size: 10px; color: #666;"><b>TARJETA PROFESIONAL:</b> ' . $profesional->registro . '</p>';
            $html .= '</td></tr>';
            $html .= '</table>';
            $html .= '</div>';

            $html .= '</div></body></html>';

            $pdf->loadHtml($html);
            $pdf->setPaper('A4', 'portrait');
            $pdf->render();

            // Obtener el contenido del PDF
            $pdfContent = $pdf->output();

            // Encabezados de respuesta para el archivo PDF
            $headers = [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="resultado_consulta_psicologica.pdf"',
            ];

            $pdfContent = $pdf->output();
            return response($pdfContent, 200, $headers);
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }


    public function enviarConsultaNeuro(Request $request)
    {
        $idConsulta = $request->input('idConsulta');

        if (Auth::check()) {
            $pdf = new Dompdf();

            $consulta = HistoriaNeuroPsicologica::busquedaConsultaImprimir($idConsulta);

            $idPaciente = DB::connection('mysql')->table('historia_clinica_neuro')
                ->where('id', $consulta->id_historia)
                ->value('id_paciente');

            // Ruta absoluta al logo
            $logoPath = public_path('app-assets/images/logo/logo_prasca.png');

            // Convertir la imagen a base64
            $logoData = base64_encode(file_get_contents($logoPath));
            $logo = 'data:image/png;base64,' . $logoData;

            $fechaElaboracion = \Carbon\Carbon::parse($consulta->fecha_consulta)->format('d/m/Y h:i A');

            $paciente = Pacientes::busquedaPaciente($idPaciente);

            $profesional = Profesional::busquedaProfesional($consulta->id_profesional);

            $firmaPath = public_path('app-assets/images/firmasProfesionales/' . $profesional->firma);
            $firmaData = base64_encode(file_get_contents($firmaPath));
            $firma = 'data:image/png;base64,' . $firmaData;

            $fechaNacimiento = \Carbon\Carbon::parse($paciente->fecha_nacimiento)->format('d/m/Y h:i A');

            $html = '<head>
                <style>
                    @page {
                        margin: 12mm 18mm;
                    }
                    
                    body {
                        position: relative;
                        font-family: Times New Roman, Arial, sans-serif;
                        color: #333;
                        line-height: 1.4;
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
                        opacity: 0.12;
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
                        background-color: rgba(234, 235, 244, 0.6);
                        font-size: 12px;
                        line-height: 1.3;
                        text-align: left;
                    }
                    
                    td {
                        font-size: 11px;
                        line-height: 1.4;
                        text-align: left;
                    }
                    
                    .no-border {
                        border: none;
                        text-align: center;
                    }
                    
                    .section {
                        margin-bottom: 8px;
                        page-break-inside: avoid;
                    }
                    
                    .section-title {
                                 background: linear-gradient(135deg, rgba(102, 126, 234, 0.85) 0%,rgb(230, 208, 245)) 100%);
                        color:rgb(0, 1, 5);
                        padding: 6px 10px;
                        margin: 10px 0 6px 0;
                        font-size: 13px;
                        font-weight: bold;
                        border-radius: 2px;
                    }
                    
                    .info-box {
                        background-color: rgba(248, 249, 250, 0.5);
                        border-left: 3px solid rgb(138, 157, 238);
                        padding: 8px 10px;
                        margin-bottom: 6px;
                        border-radius: 2px;
                    }
                    
                    .info-box p {
                        margin: 0;
                        padding: 0;
                    }
                    
                    .field-label {
                        font-weight: bold;
                        color: #495057;
                        font-size: 11px;
                        display: block;
                        margin-bottom: 3px;
                    }
                    
                    .field-value {
                        color: #212529;
                        font-size: 11px;
                        line-height: 1.4;
                        text-align: justify;
                    }
                    
                    .subsection {
                        margin-top: 6px;
                        padding: 6px 8px;
                        background-color: rgba(255, 255, 255, 0.4);
                        border-left: 2px solid #667eea;
                        border-radius: 2px;
                    }
                    
                    .subsection p {
                        margin: 0;
                        padding: 0;
                    }
                    
                    .header-info {
                        font-size: 10px;
                        line-height: 1.3;
                    }
                    
                    h3 {
                        color: #667eea;
                        margin: 8px 0;
                        font-size: 15px;
                    }
                    
                    hr {
                        border: none;
                        border-top: 1px solid #e0e0e0;
                        margin: 10px 0;
                    }
                </style>
            </head>';

            $html .= '<div class="content">';
            $html .= '<table style="width:100%; border-collapse: collapse; background-color: transparent; margin-bottom: 8px;">';
            $html .= '<tr>';
            $html .= '<td class="no-border" style="padding: 0; width: 35%;"><img src="' . $logo . '" style="width: 160px; height: auto;"></td>';
            $html .= '<td class="no-border" style="padding: 0; vertical-align: top; text-align: right;">';
            $html .= '<p class="header-info" style="margin: 0; font-weight: bold;">DRA. MARIA ISABEL PUMAREJO</p>';
            $html .= '<p class="header-info" style="margin: 0;">PSICÓLOGA CLÍNICA - T.P. No. 259542</p>';
            $html .= '<p class="header-info" style="margin: 0;">Calle 11 # 11 - 07 San Joaquin</p>';
            $html .= '<p class="header-info" style="margin: 0;">Teléfono: 312 5678078</p>';
            $html .= '</td>';
            $html .= '</tr>';
            $html .= '</table>';

            $html .= '<div style="text-align: center; margin: 12px 0 8px 0;">';
            $html .= '<h3 style="margin: 3px 0; text-transform: uppercase;">RESULTADO DE CONSULTA NEUROPSICOLÓGICA</h3>';
            $html .= '<p style="font-size: 10px; color: #666; margin: 3px 0;"><b>FECHA DE ELABORACIÓN:</b> ' . $fechaElaboracion . '</p>';
            $html .= '</div>';

            $html .= '<hr>';

            $html .= '<div class="section-title">1. DATOS DE IDENTIFICACIÓN DEL PACIENTE</div>';
            $html .= '<div class="info-box">';
            $html .= '<table style="width: 100%; border-collapse: collapse; border: none;">';
            $html .= '<tr>';
            $html .= '<td colspan="3" style="border: none; padding: 3px 0;">';
            $html .= '<span class="field-label">NOMBRE COMPLETO:</span>';
            $html .= '<span class="field-value">' .
                trim($paciente->primer_nombre . ' ' .
                    $paciente->segundo_nombre . ' ' .
                    $paciente->primer_apellido . ' ' .
                    $paciente->segundo_apellido) .
                '</span>';
            $html .= '</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td style="border: none; padding: 3px 0; width: 50%;">';
            $html .= '<span class="field-label">IDENTIFICACIÓN:</span>';
            $html .= '<span class="field-value">' . $paciente->tipo_identificacion . ' ' . $paciente->identificacion . '</span>';
            $html .= '</td>';
            $html .= '<td style="border: none; padding: 3px 0; width: 25%;">';
            $html .= '<span class="field-label">EDAD:</span>';
            $html .= '<span class="field-value">' . $paciente->edad . ' años</span>';
            $html .= '</td>';
            $html .= '<td style="border: none; padding: 3px 0; width: 25%;">';
            $html .= '<span class="field-label">SEXO:</span>';
            $html .= '<span class="field-value">' . (($paciente->sexo === "M") ? "Femenino" : "Masculino") . '</span>';
            $html .= '</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td colspan="3" style="border: none; padding: 3px 0;">';
            $html .= '<span class="field-label">FECHA Y LUGAR DE NACIMIENTO:</span>';
            $fechaNacimientoFormateada = \Carbon\Carbon::parse($paciente->fecha_nacimiento)->format('d/m/Y');
            $html .= '<span class="field-value">' . $fechaNacimientoFormateada . ' - ' . $paciente->lugar_nacimiento . '</span>';
            $html .= '</td>';
            $html .= '</tr>';
            $html .= '</table>';
            $html .= '</div>';

            $html .= '<div class="section-title">2. DETALLE DE LA CONSULTA</div>';

            // Evolución y plan de trabajo
            if (!empty($consulta->evolucion_y_o_plantrabajo)) {
                $html .= '<div class="subsection">';
                $html .= '<span class="field-label">EVOLUCIÓN Y/O PLAN DE TRABAJO:</span>';
                $html .= '<p class="field-value">' . $formatRichText($consulta->evolucion_y_o_plantrabajo) . '</p>';
                $html .= '</div>';
            }

            // Separador antes de la firma
            $html .= '<div style="margin-top: 20px; margin-bottom: 10px;"></div>';

            // Sección de firma del profesional
            $html .= '<div style="margin-top: 15px; padding-top: 10px; border-top: 1px solid rgba(224, 224, 224, 0.6);">';
            $html .= '<table style="width:100%; border-collapse: collapse; background-color: transparent;">';
            $html .= '<tr><td class="no-border" style="text-align: left; padding: 3px 0;">';
            $html .= '<p style="margin: 0; font-size: 11px; font-weight: bold; color: #495057;">' . $profesional->nombre . '</p>';
            $html .= '</td></tr>';
            $html .= '<tr><td class="no-border" style="text-align: left; padding: 6px 0;">';
            $html .= '<img width="130" src="' . $firma . '" style="display: block;" />';
            $html .= '</td></tr>';
            $html .= '<tr><td class="no-border" style="text-align: left; padding: 3px 0;">';
            $html .= '<p style="margin: 0; font-size: 10px; color: #666;"><b>TARJETA PROFESIONAL:</b> ' . $profesional->registro . '</p>';
            $html .= '</td></tr>';
            $html .= '</table>';
            $html .= '</div>';

            $html .= '</div></body></html>';

            $pdf->loadHtml($html);
            $pdf->setPaper('A4', 'portrait');
            $pdf->render();

            // Obtener el contenido del PDF
            $pdfContent = $pdf->output();

            // Encabezados de respuesta para el archivo PDF
            $headers = [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="resultado_consulta_psicologica.pdf"',
            ];

            $pdfContent = $pdf->output();


            if ($paciente->email == "" || $paciente->email == null) {
                return response()->json(['resultado' => "noCorreo"]);
            } else {
                //enviar al correo del paciente el pdf
                $mail = new PHPMailer(true);
                $mensaje = 'Se ha enviado un archivo adjunto con el resultado de la consulta neuropsicológica';
                $asunto = 'Resultado de Consulta Neuropsicológica - Prasca Center';

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
                    font-family: Times New Roman, Arial, sans-serif;
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
                        font-family: 'Times New Roman', 'Arial', Arial, sans-serif !important;
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
                <img data-imagetype='External' src='" . $logo . "' width = '200px'  alt='PERFECTA' class='x_responsive'> 
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
                    $mail->addStringAttachment($pdfContent, 'resultado_consulta_neuroPsicologica.pdf');
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

    public function eliminarHistoriaNeuro()
    {
        try {
            $idHistoria = request()->input('idHistoria');
            if (!$idHistoria) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'ID de la historia no proporcionada'
                    ],
                    400
                );
            }

            // eliminar historia con delete
            $consulta = DB::connection('mysql')
                ->table('historia_clinica_neuro')
                ->where('id', $idHistoria)
                ->update([
                    'estado_registro' => 'ELIMINADO',
                ]);

            if ($consulta) {
                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Historia eliminada correctamente'
                    ]
                );
            } else {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'No se encontró la historia o no se pudo eliminar'
                    ],
                    404
                );
            }
        } catch (\Exception $e) {
            // Manejar cualquier error o excepción
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Ocurrió un error al intentar eliminar la hisotria',
                    'error' => $e->getMessage()
                ],
                500
            );
        }
    }

    public function enviarInformeNeuropsicologia(Request $request)
    {


        if (Auth::check()) {

            $pdf = new Dompdf();
            $idInforme = $request->input('idInforme');
            $informe = HistoriaNeuroPsicologica::busquedaInforme($idInforme);

            // Ruta absoluta al logo
            $logoPath = public_path('app-assets/images/logo/logo_prasca.png');

            // Convertir la imagen a base64
            $logoData = base64_encode(file_get_contents($logoPath));
            $logo = 'data:image/png;base64,' . $logoData;

            $fechaElaboracion = now()->format('d-m-Y');
            $horaElaboracion = now()->format('H:i:s A');

            $paciente = Pacientes::busquedaPaciente($informe->id_paciente);

            $profesional = Profesional::busquedaProfesional($informe->id_profesional);
            $firmaPath = public_path('app-assets/images/firmasProfesionales/' . $profesional->firma);
            $firmaData = base64_encode(file_get_contents($firmaPath));
            $firma = 'data:image/png;base64,' . $firmaData;


            $fechaNacimiento = \Carbon\Carbon::parse($paciente->fecha_nacimiento)->format('d/m/Y h:i A');

            $formatRichText = function (?string $html) {
                if (empty($html)) {
                    return '';
                }

                $decoded = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $decoded = preg_replace('/<p[^>]*>\s*(?:&nbsp;|\s)*<\/p>/i', '', $decoded);

                $decoded = preg_replace_callback('/<p\b([^>]*)>/i', function ($matches) {
                    $attrs = $matches[1];
                    if (stripos($attrs, 'style=') !== false) {
                        $attrs = preg_replace_callback('/style="([^"]*)"/i', function ($styleMatches) {
                            $style = trim($styleMatches[1]);
                            $style .= (strlen($style) && substr($style, -1) === ';') ? '' : ';';
                            $style .= 'margin:0 0 6px !important; line-height:1.45 !important;';
                            return 'style="' . $style . '"';
                        }, $attrs, 1);
                    } else {
                        $attrs .= ' style="margin:0 0 6px !important; line-height:1.45 !important;"';
                    }
                    return '<p' . $attrs . '>';
                }, $decoded);

                return $decoded;
            };

            $html = '<head>
                <style>
                    @page {
                        margin: 12mm 18mm;
                    }
                    
                    body {
                        position: relative;
                        font-family: Times New Roman, Arial, sans-serif;
                        color: #333;
                        line-height: 1.4;
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
                        opacity: 0.12;
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
                        background-color: rgba(234, 235, 244, 0.6);
                        font-size: 12px;
                        line-height: 1.3;
                        text-align: left;
                    }
                    
                    td {
                        font-size: 11px;
                        line-height: 1.4;
                        text-align: left;
                    }
                    
                    .no-border {
                        border: none;
                        text-align: center;
                    }
                    
                    .section {
                        margin-bottom: 8px;
                        page-break-inside: avoid;
                    }
                    
                    .section h4, .section h5 {
                        margin: 0;
                        padding: 0;
                    }
                    
                    .section-title {
                                  background: linear-gradient(135deg, rgba(102, 126, 234, 0.85) 0%,rgb(230, 208, 245)) 100%);
                        color:rgb(0, 1, 5);
                        padding: 6px 10px;
                        margin: 10px 0 6px 0;
                        font-size: 13px;
                        font-weight: bold;
                        border-radius: 2px;
                    }
                    
                    .info-box {
                        background-color: rgba(248, 249, 250, 0.5);
                        border-left: 3px solid rgb(138, 157, 238);
                        padding: 8px 10px;
                        margin-bottom: 6px;
                        border-radius: 2px;
                    }
                    
                    .info-box p {
                        margin: 0;
                        padding: 0;
                    }
                    
                    .field-label {
                        font-weight: bold;
                        color: #495057;
                        font-size: 11px;
                        display: block;
                        margin-bottom: 3px;
                    }
                    
                    .field-value {
                        color: #212529;
                        font-size: 11px;
                        line-height: 1.4;
                        text-align: justify;
                    }
                    
                    .subsection {
                        margin-top: 6px;
                        padding: 6px 8px;
                        background-color: rgba(255, 255, 255, 0.4);
                        border-left: 2px solid #667eea;
                        border-radius: 2px;
                    }
                    
                    .subsection p {
                        margin: 0;
                        padding: 0;
                    }
                    
                    .header-info {
                        font-size: 10px;
                        line-height: 1.3;
                    }
                    
                    h3 {
                        color: #667eea;
                        margin: 8px 0;
                        font-size: 15px;
                    }
                    
                    h4 {
                        margin: 8px 0;
                        font-size: 13px;
                    }
                    
                    h5 {
                        margin: 6px 0;
                        font-size: 12px;
                    }
                    
                    hr {
                        border: none;
                        border-top: 1px solid #e0e0e0;
                        margin: 10px 0;
                    }
                    
                    .text-procedimiento {
                        font-size: 11px;
                        display: flex;
                        flex-direction: column;
                        gap: 3px;
                    }
                    
                    .text-procedimiento p {
                        margin: 0;
                        padding: 0;
                    }
                    
                    .text-procedimiento p:first-child {
                        font-weight: bold;
                    }
                </style>
            </head>';

            $html .= '<div class="content">';
            $html .= '<table style="width:100%; border-collapse: collapse; background-color: transparent; margin-bottom: 8px;">';
            $html .= '<tr>';
            $html .= '<td class="no-border" style="padding: 0; width: 35%;"><img src="' . $logo . '" style="width: 160px; height: auto;"></td>';
            $html .= '<td class="no-border" style="padding: 0; vertical-align: top; text-align: right;">';
            $html .= '<p class="header-info" style="margin: 0; font-weight: bold;">DRA. MARIA ISABEL PUMAREJO</p>';
            $html .= '<p class="header-info" style="margin: 0;">PSICÓLOGA CLÍNICA - T.P. No. 259542</p>';
            $html .= '<p class="header-info" style="margin: 0;">Calle 11 # 11 - 07 San Joaquin</p>';
            $html .= '<p class="header-info" style="margin: 0;">Teléfono: 312 5678078</p>';
            $html .= '</td>';
            $html .= '</tr>';
            $html .= '</table>';

            $html .= '<div style="text-align: center; margin: 12px 0 8px 0;">';
            $html .= '<h3 style="margin: 3px 0; text-transform: uppercase;">INFORME DE NEUROPSICOLOGÍA</h3>';
            $html .= '<div style="font-size: 10px; color: #666; margin: 3px 0;">';
            $html .= '<span style="margin-right: 15px;"><b>FECHA DE EVALUACIÓN:</b> ' . $fechaElaboracion . '</span>';
            $html .= '<span><b>HORA:</b> ' . $horaElaboracion . '</span>';
            $html .= '</div>';
            $html .= '</div>';

            $html .= '<hr>';

            $html .= '<div class="section-title">1. DATOS DE IDENTIFICACIÓN DEL PACIENTE</div>';
            $html .= '<div class="info-box">';
            $html .= '<table style="width: 100%; border-collapse: collapse; border: none;">';
            $html .= '<tr>';
            $html .= '<td colspan="3" style="border: none; padding: 3px 0;">';
            $html .= '<span class="field-label">NOMBRE COMPLETO:</span>';
            $html .= '<span class="field-value">' .
                trim($paciente->primer_nombre . ' ' .
                    $paciente->segundo_nombre . ' ' .
                    $paciente->primer_apellido . ' ' .
                    $paciente->segundo_apellido) .
                '</span>';
            $html .= '</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td style="border: none; padding: 3px 0; width: 50%;">';
            $html .= '<span class="field-label">IDENTIFICACIÓN:</span>';
            $html .= '<span class="field-value">' . $paciente->tipo_identificacion . ' ' . $paciente->identificacion . '</span>';
            $html .= '</td>';
            $html .= '<td style="border: none; padding: 3px 0; width: 25%;">';
            $html .= '<span class="field-label">EDAD:</span>';
            $html .= '<span class="field-value">' . $paciente->edad . ' años</span>';
            $html .= '</td>';
            $html .= '<td style="border: none; padding: 3px 0; width: 25%;">';
            $html .= '<span class="field-label">SEXO:</span>';
            $html .= '<span class="field-value">' . (($paciente->sexo === "M") ? "Femenino" : "Masculino") . '</span>';
            $html .= '</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td colspan="3" style="border: none; padding: 3px 0;">';
            $html .= '<span class="field-label">FECHA Y LUGAR DE NACIMIENTO:</span>';
            $fechaNacimientoFormateada = \Carbon\Carbon::parse($paciente->fecha_nacimiento)->format('d/m/Y');
            $html .= '<span class="field-value">' . $fechaNacimientoFormateada . ' - ' . $paciente->lugar_nacimiento . '</span>';
            $html .= '</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td colspan="2" style="border: none; padding: 3px 0;">';
            $html .= '<span class="field-label">ACOMPAÑANTE:</span>';
            $html .= '<span class="field-value">' . ($paciente->acompanante ?? 'N/A') . '</span>';
            $html .= '</td>';
            $html .= '<td style="border: none; padding: 3px 0;">';
            $html .= '<span class="field-label">TELÉFONO:</span>';
            $html .= '<span class="field-value">' . ($paciente->telefono_acompanate ?? 'N/A') . '</span>';
            $html .= '</td>';
            $html .= '</tr>';
            $html .= '</table>';
            $html .= '</div>';

            // Motivo de consulta
            if (!empty($informe->motivo_consulta)) {
                $html .= '<div class="section-title">2. MOTIVO DE CONSULTA</div>';
                $html .= '<div class="subsection">';
                $html .= '<p class="field-value">' . $formatRichText($informe->motivo_consulta) . '</p>';
                $html .= '</div>';
            }

            // Estado actual
            if (!empty($informe->estado_actual)) {
                $html .= '<div class="section-title">3. ESTADO ACTUAL</div>';
                $html .= '<div class="subsection">';
                $html .= '<p class="field-value">' . $formatRichText($informe->estado_actual) . '</p>';
                $html .= '</div>';
            }

            // Historia personal
            if (!empty($informe->historia_personal)) {
                $html .= '<div class="section-title">4. HISTORIA PERSONAL</div>';
                $html .= '<div class="subsection">';
                $html .= '<p class="field-value">' . $formatRichText($informe->historia_personal) . '</p>';
                $html .= '</div>';
            }

            // Desarrollo psicomotor
            if (!empty($informe->desarrollo_psicomotor)) {
                $html .= '<div class="section-title">5. DESARROLLO PSICOMOTOR</div>';
                $html .= '<div class="subsection">';
                $html .= '<p class="field-value">' . $formatRichText($informe->desarrollo_psicomotor) . '</p>';
                $html .= '</div>';
            }

            // Desarrollo lenguaje
            if (!empty($informe->desarrollo_lenguaje)) {
                $html .= '<div class="section-title">6. DESARROLLO LENGUAJE</div>';
                $html .= '<div class="subsection">';
                $html .= '<p class="field-value">' . $formatRichText($informe->desarrollo_lenguaje) . '</p>';
                $html .= '</div>';
            }

            // Evaluación actual (ABC)
            if (!empty($informe->abc)) {
                $html .= '<div class="section-title">7. EVALUACIÓN ACTUAL</div>';
                $html .= '<div class="subsection">';
                $html .= '<p class="field-value">' . $formatRichText($informe->abc) . '</p>';
                $html .= '</div>';
            }

            // Antecedentes médicos y familiares
            if (!empty($informe->antecedentes_medicos_familiares)) {
                $html .= '<div class="section-title">8. ANTECEDENTES MÉDICOS Y FAMILIARES</div>';
                $html .= '<div class="subsection">';
                $html .= '<p class="field-value">' . $formatRichText($informe->antecedentes_medicos_familiares) . '</p>';
                $html .= '</div>';
            }

            // Antecedentes personales
            if (!empty($informe->antecedentes_personales)) {
                $html .= '<div class="section-title">9. ANTECEDENTES PERSONALES</div>';
                $html .= '<div class="subsection">';
                $html .= '<p class="field-value">' . $formatRichText($informe->antecedentes_personales) . '</p>';
                $html .= '</div>';
            }

            // Historia de desarrollo
            if (!empty($informe->historia_desarrollo)) {
                $html .= '<div class="section-title">10. HISTORIA DE DESARROLLO</div>';
                $html .= '<div class="subsection">';
                $html .= '<p class="field-value">' . $formatRichText($informe->historia_desarrollo) . '</p>';
                $html .= '</div>';
            }

            // Historia escolar
            if (!empty($informe->historia_escolar)) {
                $html .= '<div class="section-title">11. HISTORIA ESCOLAR</div>';
                $html .= '<div class="subsection">';
                $html .= '<p class="field-value">' . $formatRichText($informe->historia_escolar) . '</p>';
                $html .= '</div>';
            }

            // Historia socioafectiva
            if (!empty($informe->historia_socio_afectiva)) {
                $html .= '<div class="section-title">12. HISTORIA SOCIOAFECTIVA</div>';
                $html .= '<div class="subsection">';
                $html .= '<p class="field-value">' . $formatRichText($informe->historia_socio_afectiva) . '</p>';
                $html .= '</div>';
            }

            // Condición del paciente
            if (!empty($informe->condicion_paciente)) {
                $html .= '<div class="section-title">13. CONDICIÓN DEL PACIENTE EN LA CONSULTA</div>';
                $html .= '<div class="subsection">';
                $html .= '<p class="field-value">' . $formatRichText($informe->condicion_paciente) . '</p>';
                $html .= '</div>';
            }

            // Resultado de la evaluación
            if (!empty($informe->resultados_evaluacion)) {
                $html .= '<div class="section-title">14. RESULTADO DE LA EVALUACIÓN</div>';
                $html .= '<div class="subsection">';
                $html .= '<p class="field-value">' . $formatRichText($informe->resultados_evaluacion) . '</p>';
                $html .= '</div>';
            }

            // Impresión diagnóstica
            if (!empty($informe->impresion_diagnostica)) {
                $html .= '<div class="section-title">15. IMPRESIÓN DIAGNÓSTICA</div>';
                $html .= '<div class="subsection">';
                $html .= '<p class="field-value">' . $formatRichText($informe->impresion_diagnostica) . '</p>';
                $html .= '</div>';
            }

            // Separador antes de la firma
            $html .= '<div style="margin-top: 20px; margin-bottom: 10px;"></div>';

            // Sección de firma del profesional
            $html .= '<div style="margin-top: 15px; padding-top: 10px; border-top: 1px solid rgba(224, 224, 224, 0.6);">';
            $html .= '<table style="width:100%; border-collapse: collapse; background-color: transparent;">';
            $html .= '<tr><td class="no-border" style="text-align: left; padding: 3px 0;">';
            $html .= '<p style="margin: 0; font-size: 11px; font-weight: bold; color: #495057;">' . $profesional->nombre . '</p>';
            $html .= '</td></tr>';
            $html .= '<tr><td class="no-border" style="text-align: left; padding: 6px 0;">';
            $html .= '<img width="130" src="' . $firma . '" style="display: block;" />';
            $html .= '</td></tr>';
            $html .= '<tr><td class="no-border" style="text-align: left; padding: 3px 0;">';
            $html .= '<p style="margin: 0; font-size: 10px; color: #666;"><b>TARJETA PROFESIONAL:</b> ' . $profesional->registro . '</p>';
            $html .= '</td></tr>';
            $html .= '</table>';
            $html .= '</div>';

            $html .= '</div></body></html>';

            $ordenesMedicas = HistoriaNeuroPsicologica::busquedaOrdenMedica($idInforme);

            if ($ordenesMedicas != null && $ordenesMedicas->count() > 0) {
                //agregar otra hoja con las ordenes medicas
                $html .= '<div class="content" style="page-break-before: always;">';
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
                $html .= '<td class="no-border" colspan="2" style="text-align: center; padding: 1px;background-color: transparent;"> <h3>INFORME DE PSICOLOGÍA - ORDENES MÉDICAS</h3></td>';
                $html .= '</tr>';
                $html .= '</table>';

                $html .= '<table>
                     <tr>
                         <td ><b>FECHA DE EVALUACIÓN:</b> ' . $fechaElaboracion . '</td>
                         <td ><b>HORA:</b> ' . $horaElaboracion . '</td>
                     </tr>
                 </table>';

                $html .= '<div class="section" >
                 <h4 style="background-color:rgba(234, 235, 244, 0.47); padding: 6px;">DATOS DE IDENTIFICACIÓN DEL PACIENTE</h4>
                 <table style="width: 100%; border-collapse: collapse; border: none;">
                     <tr>
                         <td colspan="3" style="border: none; padding: 8px 4px 4px 4px;">
                             <span style="font-weight: bold; width: 60px; display: inline-block;">NOMBRE:</span>
                             <span style="border-bottom: 1px solid #ccc; display: inline-block;">' .
                    $paciente->primer_nombre . ' ' .
                    $paciente->segundo_nombre . ' ' .
                    $paciente->primer_apellido . ' ' .
                    $paciente->segundo_apellido .
                    '</span>
                         </td>
                     </tr>
                     <tr>
                         <td style="border: none; padding: 8px 4px 4px 4px; width: 40%;">
                             <span style="font-weight: bold; width: 110px; display: inline-block;">IDENTIFICACIÓN:</span>
                             <span style="border-bottom: 1px solid #ccc; display: inline-block;">' .
                    $paciente->tipo_identificacion . ' ' . $paciente->identificacion .
                    '</span>
                                     </td>
                         <td style="border: none; padding: 8px 4px 4px 4px; width: 40%;">
                             <span style="font-weight: bold; width: 40px; display: inline-block;">EDAD:</span>
                             <span style="border-bottom: 1px solid #ccc; display: inline-block;">' .
                    $paciente->edad .
                    '</span>
                                     </td>
                         <td style="border: none; padding: 8px 4px 4px 4px; width: 20%;">
                             <span style="font-weight: bold; width: 40px; display: inline-block;">SEXO:</span>
                             <span style="border-bottom: 1px solid #ccc; display: inline-block;">' .
                    (($paciente->sexo === "M") ? "Femenino" : "Masculino") .
                    '</span>
                         </td>
                     </tr>
                     <tr>
                         <td colspan="3" style="border: none; padding: 8px 4px 4px 4px;">
                             <span style="font-weight: bold; width: 150px; display: inline-block;">FECHA DE NACIMIENTO:</span>
                             <span style="border-bottom: 1px solid #ccc; display: inline-block;">' .
                    date('d/m/Y', strtotime($fechaNacimiento)) . ' - ' . $paciente->lugar_nacimiento .
                    '</span>
                                     </td>
                                 </tr>
                     
                     <tr>
                         <td colspan="2" style="border: none; padding: 8px 4px 4px 4px;  width: 70%;">
                             <span style="font-weight: bold; width: 110px; display: inline-block;">ACOMPAÑANTE:</span>
                             <span style="border-bottom: 1px solid #ccc; display: inline-block;">' .
                    $paciente->acompanante .
                    '</span>
                         </td>
                         <td style="border: none; padding: 8px 4px 4px 4px; width: 30%;">
                             <span style="font-weight: bold; width: 70px; display: inline-block;">TELÉFONO:</span>
                             <span style="border-bottom: 1px solid #ccc; display: inline-block;">' .
                    $paciente->telefono_acompanate .
                    '</span>
                         </td>
                     </tr>  
                 </table>
             </div>';

                $html .= '<div class="section">
                     <h4> DIAGNÓSTICOS</h4>';
                if ($informe->impresion_diagnostica_princippal != null) {
                    $html .= '<p style="margin-bottom: 10px;"><strong>PRINCIPAL:</strong> ' . $informe->impresion_diagnostica_princippal_detalle->codigo . ' - ' . $informe->impresion_diagnostica_princippal_detalle->nombre . '</p>';
                }
                if ($informe->impresion_diagnostica_relacionada_1 != null) {
                    $html .= '<p style="margin-bottom: 10px;"><strong>RELACIONADO 1:</strong> ' . $informe->impresion_diagnostica_relacionada_1_detalle->codigo . ' - ' . $informe->impresion_diagnostica_relacionada_1_detalle->nombre . '</p>';
                }
                if ($informe->impresion_diagnostica_relacionada_2 != null) {
                    $html .= '<p style="margin-bottom: 10px;"><strong>RELACIONADO 2:</strong> ' . $informe->impresion_diagnostica_relacionada_2_detalle->codigo . ' - ' . $informe->impresion_diagnostica_relacionada_2_detalle->nombre . '</p>';
                }
                $html .= '</div>';


                $html .= '<div class="section">
                     <h4>ORDENES MÉDICAS</h4>';

                // Obtener las órdenes médicas del informe


                if (!empty($ordenesMedicas)) {
                    $html .= '<table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                     <thead>
                         <tr style="background-color:rgba(234, 235, 244, 0.47);">
                             <th style="border: 1px solid #000; padding: 8px; text-align: center; width: 10%;">No.</th>
                             <th style="border: 1px solid #000; padding: 8px; text-align: center; width: 75%;">PROCEDIMIENTO</th>
                             <th style="border: 1px solid #000; padding: 8px; text-align: center; width: 15%;">CANTIDAD</th>
                         </tr>
                     </thead>
                     <tbody>';

                    $contador = 1;
                    foreach ($ordenesMedicas as $orden) {
                        $html .= '<tr>
                         <td style="border: 1px solid #000; padding: 8px; text-align: center;">' . $contador . '</td>
                         <td style="border: 1px solid #000; padding: 8px;" class="text-procedimiento"><p>' . $orden->codigo . ' - ' . $orden->textoCodigo . '</p> <p><strong>OBSERVACIÓN:</strong> ' . $orden->observacion . '</p></td>
                         <td style="border: 1px solid #000; padding: 8px; text-align: center;">' . $orden->cantidad . '</td>
                     </tr>';
                        $contador++;
                    }

                    $html .= '</tbody></table>';
                } else {
                    $html .= '<p style="margin-top: 10px;">No se registraron órdenes médicas para este informe.</p>';
                }

                $html .= '</div>';

                $html .= '<div class="section">
                <table style="width:100%; border-collapse: collapse; background-color: transparent; margin-top: 20px;">
                         <tr><td class="no-border" style="text-align: left;"><img width="150" src="' . $firma . '" /><br><b>' . $profesional->nombre . '</b></td></tr>
                         <tr><td class="no-border" style="text-align: left; font-size: 10px;"><b>TARJETA PROFESIONAL: ' . $profesional->registro . '</td></tr>
                         </tr>
                     </table>
                 </div></div>';
            }

            $pdf->loadHtml($html);
            $pdf->setPaper('A4', 'portrait');
            $pdf->render();

            $pdfContent = $pdf->output();


            if ($paciente->email == "" || $paciente->email == null) {
                return response()->json(['resultado' => "noCorreo"]);
            } else {
                //enviar al correo del paciente el pdf
                $mail = new PHPMailer(true);
                $mensaje = 'Se ha enviado un archivo adjunto con el informe neuropsicologico';
                $asunto = 'Resultado de Informe Neuropsicologico - Prasca Center';

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
                        font-family: Times New Roman, Arial, sans-serif;
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
                            font-family: 'Times New Roman', 'Arial', Arial, sans-serif !important;
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
                    <img data-imagetype='External' src='" . $logo . "' width = '200px'  alt='PERFECTA' class='x_responsive'> 
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
                    $mail->addStringAttachment($pdfContent, 'resultado_informe_neuropsicologico.pdf');
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
}
