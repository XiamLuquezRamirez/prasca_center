<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\HistoriaNeuroPsicologica;
use App\Models\CategoriaHCP;
use App\Models\Pacientes;
use \PDF;
use App\Models\Paquetes;


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
                ->table('consultas_psicologica_neuro')
                ->leftJoin('historia_clinica_neuro', 'historia_clinica_neuro.id', '=', 'consultas_psicologica_neuro.id_historia')
                ->leftJoin('profesionales', 'profesionales.usuario', 'consultas_psicologica_neuro.id_profesional')
                ->leftJoin('pacientes', 'pacientes.id', '=', 'historia_clinica_neuro.id_paciente')
                ->where('consultas_psicologica_neuro.estado', 'ACTIVO')
                ->select(
                    'pacientes.id',
                    'profesionales.nombre as profesional',
                    DB::raw("CONCAT(pacientes.tipo_identificacion, ' ', pacientes.identificacion) as identificacion"),
                    DB::raw("CONCAT(primer_nombre, ' ', segundo_nombre, ' ', primer_apellido, ' ', segundo_apellido) as nombre"),
                    DB::raw('MAX(consultas_psicologica_neuro.fecha_consulta) as ultima_fecha_consulta')
                )
                ->groupBy('pacientes.id', 'tipo_identificacion', 'identificacion', 'primer_nombre', 'segundo_nombre', 'primer_apellido', 'segundo_apellido', 'profesionales.nombre');



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
                                    <td>' . $item->identificacion . ' - ' . $item->nombre . '</td>
                                    <td>' . date('d/m/Y g:i:s A', strtotime($item->ultima_fecha_consulta)) . '</td>
                                    <td>' . $item->profesional . '</td>
                                    <td class="table-action min-w-100">
                                        <a onclick="imprimirInforme(' . $item->id . ');" style="cursor: pointer;" title="Imprimir informe" class="text-fade hover-warning"><i class="align-middle"
                                                data-feather="file-text"></i></a>
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

        return response()->json([
            'informe' => $informe,
            'anexos'  => $anexos
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

        $data = $request->all();
        $respuesta = HistoriaNeuroPsicologica::guardar($data);

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
            'id' => $respuesta['idHistoria'],
            'message' =>  $message,
            'title' =>  $title
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
                ->leftJoin("profesionales", "profesionales.usuario", "consultas_psicologica_neuro.id_profesional")
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
            $pagination = $ListConsultas->links('Adminitraccion.Paginacion')->render();

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
                ->where('estado_registro', 'ACTIVO')
                ->select(
                    "historia_clinica_neuro.id",
                    DB::raw("CONCAT(tipo_identificacion, ' ', identificacion) as identificacion_completa"),
                    DB::raw("CONCAT(primer_nombre,' ',segundo_nombre,' ',primer_apellido,' ', segundo_apellido) as nombre_completo"),
                    "historia_clinica_neuro.fecha_historia",
                    "historia_clinica_neuro.tipologia",
                    "historia_clinica_neuro.estado_hitoria",
                    "pacientes.fecha_nacimiento",
                    'historia_clinica_neuro.codigo_consulta',
                    'pacientes.id as id_paciente'
                );

            if ($search) {
                $historias->where(function ($query) use ($search) {
                    $query->where('pacientes.identificacion', 'LIKE', '%' . $search . '%')
                        ->orWhere('pacientes.primer_nombre', 'LIKE', '%' . $search . '%')
                        ->orWhere('pacientes.segundo_nombre', 'LIKE', '%' . $search . '%')
                        ->orWhere('pacientes.primer_apellido', 'LIKE', '%' . $search . '%')
                        ->orWhere('pacientes.segundo_apellido', 'LIKE', '%' . $search . '%');
                });
            }

            $ListHistoria = $historias->paginate($perPage, ['*'], 'page', $page);

            $tdTable = '';
            $x = ($page - 1) * $perPage + 1;

            foreach ($ListHistoria as $i => $item) {
                if (!is_null($item)) {


                    $paqueteActivo = Paquetes::paqueteActivoNeuro($item->id);

                    $event = "initial";
                    if ($paqueteActivo) {
                        $event = "none";
                    }

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

                    $tdTable .= ' <div class="box pull-up">
                    <div class="box-body">
                        <div class="d-md-flex justify-content-between align-items-center">
                            <div>
                                <p><span class="text-primary">Historia Clínica</span> | <span
                                        class="text-fade">Tipo: Psicológica - ' . $item->tipologia . '</span></p>
                                <h3 class="mb-0 fw-500">Paciente: ' . $item->identificacion_completa . ' - ' . $item->nombre_completo . '</h3>
                            </div>
                            <div class="mt-10 mt-md-0">
                        <div class="btn-group mb-5">
                        <button type="button" class="waves-effect waves-light btn btn-danger dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-dollar"></i> Venta de servicios</button>
                        <div class="dropdown-menu" style="">
                        <a class="dropdown-item" data-paciente="'.$item->id_paciente.'" data-id="' . $item->id . '" data-consulta="' . $item->codigo_consulta . '" style="pointer-events: ' . $event . '; cursor: pointer;"  onclick="ventaConsulta(this)">Venta consulta</a>
                        <a class="dropdown-item" data-paciente="'.$item->id_paciente.'" data-id="' . $item->id . '" style="pointer-events: ' . $event . '; cursor: pointer;"  onclick="ventaSesion(this)">Venta sesión</a>
                        <a class="dropdown-item" data-paciente="'.$item->id_paciente.'" data-id="' . $item->id . '" style="cursor: pointer;"  onclick="ComprarPaquete(this)">Venta paquete</a>
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
                                <button type="button" data-id="' . $item->id . '" data-estado="' . $item->estado_hitoria . '" onclick="evolucionHistoria(this);"
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
        $aparienciaPersonal = HistoriaNeuroPsicologica::busquedaAparienciaPersonal($historia->id);
        $funcionesCognitiva = HistoriaNeuroPsicologica::busquedaFuncionesCognitivas($historia->id);
        $funcionesSomaticas = HistoriaNeuroPsicologica::busquedaFuncionesSomaticas($historia->id);
        $antecedentesPrenatales = HistoriaNeuroPsicologica::busquedaAntPrenatales($historia->id);
        $antecedentesNatales = HistoriaNeuroPsicologica::busquedaAntNatales($historia->id);
        $antecedentesPosnatales = HistoriaNeuroPsicologica::busquedaAntPosnatales($historia->id);
        $desarrolloPsicomotor = HistoriaNeuroPsicologica::desarrolloPsicomotor($historia->id);

        $historiaCon = self::consultasLateral($historia->id);
        return response()->json([
            'historia' => $historia,
            'paciente' => $pacientes,
            'antecedentesPersonales' => $antecedentesPersonales,
            'antecedentesFamiliares' => $antecedentesFamiliares,
            'areaAjuste' => $areaAjuste,
            'interconuslta' => $interconuslta,
            'aparienciaPersonal' => $aparienciaPersonal,
            'funcionesCognitiva' => $funcionesCognitiva,
            'funcionesSomaticas' => $funcionesSomaticas,
            'antecedentesPrenatales' => $antecedentesPrenatales,
            'antecedentesNatales' => $antecedentesNatales,
            'antecedentesPosnatales' => $antecedentesPosnatales,
            'desarrolloPsicomotor' => $desarrolloPsicomotor,
            'historialConsultas' => $historiaCon
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
        $aparienciaPersonal = HistoriaNeuroPsicologica::busquedaAparienciaPersonal($historia->id);
        $funcionesCognitiva = HistoriaNeuroPsicologica::busquedaFuncionesCognitivas($historia->id);
        $funcionesSomaticas = HistoriaNeuroPsicologica::busquedaFuncionesSomaticas($historia->id);
        $antecedentesPrenatales = HistoriaNeuroPsicologica::busquedaAntPrenatales($historia->id);
        $antecedentesNatales = HistoriaNeuroPsicologica::busquedaAntNatales($historia->id);
        $antecedentesPosnatales = HistoriaNeuroPsicologica::busquedaAntPosnatales($historia->id);
        $desarrolloPsicomotor = HistoriaNeuroPsicologica::desarrolloPsicomotor($historia->id);
        $data = [
            'historia' => $historia,
            'paciente' => $pacientes,
            'antecedentesPersonales' => $antecedentesPersonales,
            'antecedentesFamiliares' => $antecedentesFamiliares,
            'areaAjuste' => $areaAjuste,
            'interconuslta' => $interconuslta,
            'aparienciaPersonal' => $aparienciaPersonal,
            'funcionesCognitiva' => $funcionesCognitiva,
            'funcionesSomaticas' => $funcionesSomaticas,
            'antecedentesPrenatales' => $antecedentesPrenatales,
            'antecedentesNatales' => $antecedentesNatales,
            'antecedentesPosnatales' => $antecedentesPosnatales,
            'desarrolloPsicomotor' => $desarrolloPsicomotor
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
