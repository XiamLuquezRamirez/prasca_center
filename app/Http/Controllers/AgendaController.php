<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Citas;
use Illuminate\Support\Facades\DB;
use App\Models\Pacientes;

class AgendaController extends Controller
{
    public function agenda()
    {
        if (Auth::check()) {
            $citas = Citas::AllCitas();
            
            // Verificar si hay resultados
            if (request()->ajax()) {
                return response()->json([
                    'disponibilidad' => $citas
                ], 200);
            }

            // Verificamos si es una solicitud AJAX
            if (request()->ajax()) {
                return response()->json([
                    'disponibilidad' => $citas
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
           // $envioCorreo = self::envioCambioEstadoCita($idCita,$tipo);

            if (request()->ajax()) {
                return response()->json([
                    'estado' => $CitasPaciente
                ]);
            }
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

    public function disponibilidad(){
        if (!Auth::check()) {
            return response()->json(['error' => 'Su Sesión ha Terminado'], 401); // 401 para no autorizado
        }
        
        $idProf = request()->get('idProf');
        $idCita = request()->get('idCita');
        $disponibilidad = Citas::CitasProfesional($idProf, $idCita);
        
        if ($disponibilidad->isEmpty()) {
            return response()->json(['disponibilidad' => []], 200);
        }
        
        return response()->json(['disponibilidad' => $disponibilidad], 200);
    }

    public function cargarListaPacientes(){
        $especialidades = DB::connection('mysql')
        ->table('pacientes')
        ->select('id', DB::raw("CONCAT(tipo_identificacion, ' ', identificacion) as identificacion"),DB::raw("CONCAT(primer_nombre,' ',segundo_nombre,' ',primer_apellido,' ', segundo_apellido) as nombre"), )
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
           // self::envioCambioEstadoCita($cita, 'recordatorio');
        } else {
            $cita = Citas::EditarCitas($data);
         //   self::envioCambioEstadoCita($data['idCitaPac'], 'recordatorio');
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
