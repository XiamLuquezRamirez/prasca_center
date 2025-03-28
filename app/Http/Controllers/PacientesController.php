<?php

namespace App\Http\Controllers;

use App\Models\HistoriaPsicologica;
use App\Models\HistoriaNeuroPsicologica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Pacientes;
use App\Models\Servicios;

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

    public function consultas(){
        $consultas = Pacientes::listConsultas();
        return response()->json($consultas);
    }

    public function sesiones(){
        $sesiones = Pacientes::listSesiones();
        return response()->json($sesiones);
    }

    public function paquetes(){
        $paquetes = Pacientes::listPaquetes();
        return response()->json($paquetes);
    }

    public function pruebas(){
        $pruebas = Pacientes::listPruebas();
        return response()->json($pruebas);
    }

    public function historiaPsicologica(){
        $bandera = "";
        if (Auth::check()) {
            return view('HistoriasClinica.psicologia', compact('bandera'));
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }
    public function historiaNeuropsicologica(){
        $bandera = "";
        if (Auth::check()) {
            return view('HistoriasClinica.Neuropsicologia', compact('bandera'));
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function buscaServicioVenta(Request $request){
        $idServicio = $request->input('idServicio');
        $servicio = Servicios::buscaServicioVenta($idServicio);
        return response()->json($servicio);
    }

    public function listaVentaServiciosPacientes(Request $request){
        
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
            ->groupBy([
                'servicios.id',
                'servicios.id_tipo_servicio',
                'servicios.tipo',
                'servicios.id_paciente',
                'servicios.fecha',
                'servicios.precio',
                'ventas.cantidad',
                'ventas.estado_venta'
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
                    $fecha = date('d/m/Y H:i:s' , strtotime($item->fecha));
                    $sesiones = "1 / 1";
                    if ($item->tipo == 'PAQUETE') {
                        $color = 'badge-warning';
                        $sesiones = $item->sesiones_disponibles. ' Disponibles de ' . $item->cantidad;
                    } else if ($item->tipo == 'SESION') {
                        $color = 'badge-primary';
                    } else if ($item->tipo == 'CONSULTA') {
                        $color = 'badge-warning';
                    } else if ($item->tipo == 'PRUEBAS') {
                        $color = 'badge-info';
                    }

                    $tdTable .= '<tr>
                                    <td>' . $item->descripcion . '</td>
                                    <td><span class="badge ' . $color . '">' . $item->tipo . '</span></td>
                                    <td>' . $sesiones . '</td>
                                    <td>' . $fecha. '</td>
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
        $idPaciente = $request->input('id'); // Capturar el id del paciente si es una edición

        // Verificar si la identificación existe y pertenece a otro paciente
        $pacienteExistente = DB::table('pacientes')
            ->where('identificacion', $identificacion)
            ->when($idPaciente, function ($query) use ($idPaciente) {
                // Ignorar el registro actual si es una edición
                $query->where('id', '!=', $idPaciente);
            })
            ->exists();

        return response()->json(!$pacienteExistente); // Devuelve true si NO existe duplicado, false si ya está registrado
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

            $ListPacientes = $pacientes->paginate($perPage, ['*'], 'page', $page)->appends(request()->except('page'));

            $tdTable = '';
            $x = ($page - 1) * $perPage + 1;

            foreach ($ListPacientes as $i => $item) {
                if (!is_null($item)) {
                    $clases = ($item->estado == "IMCOMPLETO") ? "badge-danger" : "badge-success";

                    $tdTable .= '<tr>
                                    <td>' . $item->identificacion_completa . '</td>
                                    <td>' . $item->nombre_completo . '</td>
                                    <td>' . $item->regimen . '</td>
                                    <td>' . $item->sexo . '</td>
                                    <td>' . $item->edad . ' Años</td>
                                    <td>' . $item->telefono . '</td>
                                    <td><span class="badge ' . $clases . '">' . $item->estado . '</span></td>
                                    <td class="table-action min-w-100">
                                    <a href="javascript:void(0)" onclick="verServiciosVenta(' . $item->id . ');" style="cursor: pointer;" title="Venta de servicios" class="text-fade hover-info"><i class="align-middle"
                                    data-feather="shopping-cart"></i></a>
                                    <a  style="cursor: pointer;" data-bs-toggle="dropdown" title="Historia clinica" class="text-fade hover-info"><i class="align-middle"
                                    data-feather="file-text"></i></a>
                                        <div class="dropdown-menu">
									        <a class="dropdown-item" id="hsitPsi'.$item->id .'" data-id="' . $item->id . '" data-edad="' . $item->edad . '" style="cursor:pointer;" onclick="goHistoriaPsicologia(this)" >Historia clinica psicológica</a>
									        <a class="dropdown-item" id="hsitNeu'.$item->id .'" data-id="' . $item->id . '" data-edad="' . $item->edad . '" style="cursor:pointer;" onclick="goHistoriaNeuropsicologia(this)">Historia clinica neuropsicológica</a>
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
}
