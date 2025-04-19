<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Citas;
use Illuminate\Support\Facades\DB;
use App\Models\Pacientes;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class AgendaController extends Controller
{
    public function agenda()
    {
        if (Auth::check()) {
            $citas = Citas::AllCitas();
              //Hirarios bloqueados
              $bloqueados = Citas::AllBloqueos();
              $disponibilidad = $citas->concat($bloqueados);
            // Verificar si hay resultados
            if (request()->ajax()) {
                return response()->json([
                    'disponibilidad' => $disponibilidad
                ], 200);
            }

            // Verificamos si es una solicitud AJAX
            if (request()->ajax()) {
                return response()->json([
                    'disponibilidad' => $disponibilidad
                ], 200);
            }

            // Si no es AJAX, devolvemos un error 400
            return response()->json([
                'error' => 'Solicitud no válida'
            ], 400);
        }

        // Si no está autenticado, devolvemos JSON para evitar errores en fetch
        return response()->json([
            'error' => 'Su sesión ha terminado. Por favor, inicie sesión nuevamente.'
        ], 401);
    }

    public function obtenerFechaInicioFinBloqueo()
    {
        $idBloqueo = request()->get('idBloqueo');
        $bloqueo = Citas::obtenerFechaInicioFinBloqueo($idBloqueo);

        return response()->json([
            'fechaInicio' => \Carbon\Carbon::parse($bloqueo->inicio)->format('d/m/Y H:i:s'),
            'fechaFin' => \Carbon\Carbon::parse($bloqueo->final)->format('d/m/Y H:i:s'),
            'inicio' => $bloqueo->inicio,
            'final' => $bloqueo->final,
            'observacion' => $bloqueo->comentario,
            'duracion' => $bloqueo->duracion
        ]);
    }

    public function notificaccionCita()
    {
        if (Auth::check()) {
            $idCita = request()->get('idCita');
            $tipo = 'recordatorio';
            //enviar correo de cambio de estado
            $envioCorreo = self::envioCambioEstadoCita($idCita, $tipo);
            if ($envioCorreo == 'enviado') {
                $estado = 'success';
                $title = '¡Buen trabajo!';
            } else {
                $estado = 'warning';
                $title = '¡Opps salio algo mal!';
            }
            if (request()->ajax()) {
                return response()->json([
                    'estado' => $estado,
                    'title' =>  $title
                ]);
            }
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function guardarBloquear()
    {
        if (Auth::check()) {
            $data = request()->all();
            
            $cita = Citas::GuardarBloquear($data);
            return response()->json($cita);
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function eliminarBloqueo()
    {
        if (Auth::check()) {
            $idBloqueo = request()->get('idBloqueo');
            $cita = Citas::EliminarBloqueo($idBloqueo);

            return response()->json([
                'status' => 200,
                'message' => 'Bloqueo eliminado correctamente'
            ]);
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function envioCambioEstadoCita($idCita, $tipo)
    {

        $mail = new PHPMailer(true);

        $datosCita = Citas::infcitasEmail($idCita);

        if ($datosCita->email == "" || $datosCita->email == null) {
            return 'noCorreo';
        }


        setlocale(LC_TIME, 'es_ES.utf8');
        $dateTime = new \DateTime($datosCita->inicio);

        // Formatea la fecha y hora según el nuevo formato
        $fechaHoraFormateada = $dateTime->format('d/m/Y h:i A');
        $mensaje = "";
        $asunto = "";
        if ($tipo == 'recordatorio') {
            $mensaje = "Le recordamos que tiene una cita pendiente";
            $asunto = "Recordatorio de cita";
        } else {
            $mensaje = "Su cita a cambiado a estado: " . $datosCita->estado;
            $asunto = "Cambio de estado de cita";
        }

        // Ruta absoluta al logo
        $logoPath = public_path('app-assets/images/logo/logo_prasca.png');

        // Convertir la imagen a base64
        $logoData = base64_encode(file_get_contents($logoPath));
        $logo = 'data:image/png;base64,' . $logoData;

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
            text-align: center;
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
        <img data-imagetype='External' src='" . $logo . "' width = '200px'  alt='PRASCA CENTER' class='x_responsive'> 
        </th>
        </tr>
        </thead>
        <tbody>
        <tr>
        <td id='x_greeting'>
        Estimad@ <strong style='text-transform: capitalize;'>" . $datosCita->apaciente . ", " . $datosCita->npaciente . ",</strong>
        </td>
        </tr>
        <tr>
        <td style='text-transform: capitalize;' id='x_initial-text'>
        " . $mensaje . "
        </td>
        </tr>
        <tr class='x_header'>
        <td>
        <table>
        <tbody>
        <tr>
        <td colspan='2'>
        Datos de la cita
        </td>
        </tr>
        </tbody>
        </table>
        </td>
        </tr>
        <tr>
        <td>
        <table class='x_appt-data'>
        <tbody>
        <tr class='x_data-row'>
        <td class='x_label'>
        Sede
        </td>
        <td class='x_data'>
        Prasca Center - Centro de rehabilitación psicologica
        </td>
        </tr>
        <tr class='x_data-row'>
        <td class='x_label'>
        Dirección
        </td>
        <td class='x_data'>
        Calle 11 # 11 - 07 San Joaquin
        </td>
        </tr>
        <tr class='x_data-row'>
        <td class='x_label'>
        Fecha
        </td>
        <td class='x_data'>" . $fechaHoraFormateada . "
        </td>
        </tr>
        </tbody>
        </table>
        </td>
        </tr>
        <tr>
        <td>
        <table class='x_appt-data'>
        <tbody>
        <tr class='x_data-row x_short'>
        <td class='x_label'>
        Profesional
        </td>
        <td class='x_data'>
        " . $datosCita->nomprof . "
        </td>
        </tr>
        <tr class='x_data-row x_short'>
        <td class='x_label'>
        Especialidad
        </td>
        <td class='x_data'>
        " . $datosCita->nombre . "
        </td>
        </tr>
        </tbody>
        </table>
        </td>
        </tr>
        <div>
        </body>
        </html>";


        try {
            // Configuración del servidor SMTP
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
            $mail->addAddress($datosCita->email, $datosCita->npaciente . ' ' . $datosCita->apaciente); // Correo y nombre del destinatario
            $mail->isHTML(true);
            $mail->Subject = $asunto;
            $mail->Body = $contenido;

            // Enviar el correo
            $mail->send();
            return 'enviado';
        } catch (Exception $e) {
            return 'error';
        }
    }

    public function GuardarComentario()
    {
        if (Auth::check()) {
            $data = request()->all();
            $cita = Citas::GuardarComentario($data);

            $comentario = Citas::buscaDetCitas($data['idCita']);


            if (request()->ajax()) {
                return response()->json([
                    'estado' => "ok",
                    'comentario' => $comentario->comentario,
                ]);
            }
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function cargarComentario()
    {
        if (Auth::check()) {
            $idCita = request()->get('idCita');
            $comentario = Citas::buscaDetCitas($idCita);

            if (request()->ajax()) {
                return response()->json([
                    'estado' => "ok",
                    'comentario' => $comentario->comentario,
                ]);
            }
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }


    public function CambioEstadocita()
    {
        if (Auth::check()) {
            $idCita = request()->get('idCita');
            $estadoCita = request()->get('estadoCita');
            $CitasPaciente = Citas::CambioEstadocita($idCita, $estadoCita);
            $tipo = 'cambioEstado';
            //enviar correo de cambio de estado
            $envioCorreo = self::envioCambioEstadoCita($idCita, $tipo);
            if ($envioCorreo == 'enviado') {
                $estado = 'success';
                $title = '¡Buen trabajo!';
            } else {
                $estado = 'warning';
                $title = '¡Opps salio algo mal!';
            }
            if (request()->ajax()) {
                return response()->json([
                    'estado' => $estado,
                    'title' =>  $title
                ]);
            }
            if (request()->ajax()) {
                return response()->json([
                    'estado' => $CitasPaciente
                ]);
            }
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }
    public function eliminarcita()
    {
        if (Auth::check()) {
            $idCita = request()->get('idCita');

            try {

                if (!$idCita) {
                    return response()->json(
                        [
                            'success' => false,
                            'message' => 'ID de la cita no proporcionado'
                        ],
                        400
                    );
                }

                $consulta = DB::connection('mysql')
                    ->table('citas')
                    ->where('id', $idCita)
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
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function listaCitasEstado()
    {
        if (Auth::check()) {
            $fecha1 = request()->get('fecha1');
            $fecha2 = request()->get('fecha2');
            $tipo = request()->get('tipo');
            $fechaInicio = \Carbon\Carbon::createFromFormat('d/m/Y', $fecha1)->format('Y-m-d') . 'T00:00:00';
            $fechaFin = \Carbon\Carbon::createFromFormat('d/m/Y', $fecha2)->format('Y-m-d') . 'T23:59:59';
            $citas = Citas::buscarCitas($fechaInicio, $fechaFin);

            if ($tipo == "no-confir") {
                $citas = $citas->whereIn('estado', ['Por atender', 'no-confirmada', 'por-atender', 'Confirmada']);
            } else if ($tipo == "atendidas") {
                $citas = $citas->where('estado', 'Atendida');
            } else if ($tipo == "canceladas") {
                $citas = $citas->where('estado', 'Anulada');
            }

            $listCitas = '';
            $x = 1;
            foreach ($citas as $i => $item) {
                if (!is_null($item)) {
                    $fecha = \Carbon\Carbon::parse($item->inicio)->format('d/m/Y h:i A');
                    $listCitas .= '<tr>
                      <td>' . ($x) . '</td>
                                <td>' . $item->primer_nombre . ' ' . $item->primer_apellido . '</td>
                                <td>' . $item->nomprof . '</td>
                                <td>' . $fecha . '</td>
                            </tr>';
                    $x++;
                }
            }


            return response()->json([
                'citas' => $listCitas
            ]);
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function listaCitasProfesional()
    {
        if (Auth::check()) {
            $fecha1 = request()->get('fecha1');
            $fecha2 = request()->get('fecha2');
            $idProf = request()->get('idProf');

            $fechaInicio = \Carbon\Carbon::createFromFormat('d/m/Y', $fecha1)->format('Y-m-d') . 'T00:00:00';
            $fechaFin = \Carbon\Carbon::createFromFormat('d/m/Y', $fecha2)->format('Y-m-d') . 'T23:59:59';
            $citas = Citas::buscarCitas($fechaInicio, $fechaFin);

            $citas = $citas->where('idprof', $idProf);

            $listCitas = '';
            $x = 1;
            foreach ($citas as $i => $item) {
                if (!is_null($item)) {
                    $fecha = \Carbon\Carbon::parse($item->inicio)->format('d/m/Y h:i A');
                    $listCitas .= '<tr>
                                <td>' . ($x) . '</td>
                                <td>' . $item->primer_nombre . ' ' . $item->primer_apellido . '</td>
                                <td>' . $item->nomprof . '</td>
                                <td>' . $fecha . '</td>
                            </tr>';
                }
                $x++;
            }


            return response()->json([
                'citas' => $listCitas
            ]);
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function informacionCita()
    {
        if (Auth::check()) {
            $idCita = request()->get('idCita');
            $detaCita = Citas::buscaDetCitas($idCita);

            $paciente = Pacientes::BuscarPaciente($detaCita->paciente);

            if (request()->ajax()) {
                return response()->json([
                    'detaCita' => $detaCita,
                    'paciente' => $paciente
                ]);
            }
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function disponibilidad()
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Su Sesión ha Terminado'], 401); // 401 para no autorizado
        }

        $idProf = request()->get('idProf');
        $idCita = request()->get('idCita');
        $idBloqueo = request()->get('idBloqueo');
        $disponibilidad = Citas::CitasProfesional($idProf, $idCita, $idBloqueo);
        $bloqueos = Citas::AllBloqueosDisponibles($idBloqueo);

        $disponibilidad = $disponibilidad->concat($bloqueos);

        if ($disponibilidad->isEmpty()) {
            return response()->json(['disponibilidad' => []], 200);
        }

        return response()->json(['disponibilidad' => $disponibilidad], 200);
    }

    public function cargarListaPacientes()
    {
        $especialidades = DB::connection('mysql')
            ->table('pacientes')
            ->select('id', DB::raw("CONCAT(tipo_identificacion, ' ', identificacion) as identificacion"), DB::raw("CONCAT(primer_nombre,' ',segundo_nombre,' ',primer_apellido,' ', segundo_apellido) as nombre"),)
            ->where('estado', 'ACTIVO')
            ->get();
        return response()->json($especialidades);
    }

    public function guardarCitas(Request $request)
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

        //Guardar la información del paciente
        if ($data["opc"] == "1") {
            $respuesta = Pacientes::guardarPend($data);
            $data['idPaciente'] = $respuesta;
        }

        if ($data['accionCita'] == "agregar") {
            $cita = Citas::GuardarCitas($data);
            if ($data['notCliente'] == "si") {
                self::envioCambioEstadoCita($cita, 'recordatorio');
            }
        } else {
            $cita = Citas::EditarCitas($data);
            if ($data['notCliente'] == "si") {
            self::envioCambioEstadoCita($data['idCita'], 'recordatorio');
        }
        }

        if (isset($data['idBloqueo'])) {
            if($data['idBloqueo'] !== null || $data['idBloqueo'] !== ''){
                $bloq = Citas::EditarBlo($data['idBloqueo']);
            }
        }

        // Verificar el resultado y preparar la respuesta
        if ($cita) {
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
            'id' => $cita,
            'message' =>  $message,
            'title' =>  $title
        ]);
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
