<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\HistoriaNeuroPsicologica;
use App\Models\CategoriaHCP;
use App\Models\Pacientes;
use \PDF;


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
            'idConsulta' => $respuesta['idConsulta'],
            'message' =>  $message,
            'title' =>  $title
        ]);
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
                ->where("consultas_psicologica_neuro.id_historia",$idHist)
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
                'historialConsultas' =>$consutlasLateral
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
                    "historia_clinica_neuro.estado_hitoria"
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

                    if ($item->estado_hitoria == "abierta") {
                        $estado = "<i class='fa fa-unlock'></i> Abierta";
                        $class = "text-success";
                        $disabled = "";
                    } else {
                        $estado = "<i class='fa fa-unlock-alt'></i> Cerrada";
                        $class = "text-danger";
                        $disabled = "disabled";
                    }

                    $tdTable .= ' <div class="box pull-up">
                                <div class="box-body">
                                    <div class="d-md-flex justify-content-between align-items-center">
                                        <div>
                                            <p><span class="text-primary">Historia Clínica</span> | <span
                                                    class="text-fade">Tipo: Psicológica - ' . $item->tipologia . '</span></p>
                                            <h3 class="mb-0 fw-500">Paciente: ' . $item->identificacion_completa . ' - ' . $item->nombre_completo . '</h3>
                                        </div>
                                        <div class="mt-10 mt-md-0">
                                            <a onclick="verHistoria(' . $item->id . ')"
                                                class="waves-effect waves-light btn btn-outline btn-primary">Ver
                                                Detalles</a>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="d-md-flex justify-content-between align-items-center">
                                        <div class="d-flex justify-content-start align-items-center">
                                            <div class="min-w-100">
                                                <p class="mb-0 text-fade">Fecha de Creación</p>
                                                <h6 class="mb-0">' . date('d/m/Y g:i:s A', strtotime($item->fecha_historia)) . '</h6>
                                            </div>
                                            <div style="cursor:pointer;" data-id="' . $item->id . '" data-estado="' . $item->estado_hitoria . '" onclick="cerrarHistoria(this)" class="mx-lg-50 mx-20 min-w-70">
                                                <p class="mb-0 text-fade">Estado</p>
                                                <h6 class="mb-0 ' . $class . '">' . $estado . '</h6>
                                            </div>
                                            <div>
                                                <p class="mb-0 text-fade">Notas</p>
                                                <h6 class="mb-0">[Resumen o notas importantes]</h6>
                                            </div>
                                        </div>
                                        <div class="mt-10 mt-md-0">
                                            <button type="button" ' . $disabled . ' data-id="' . $item->id . '" data-tipo="' . $item->tipologia . '" onclick="editarHistoria(this);"
                                                class="waves-effect waves-light btn btn-primary btn-flat"><i
                                                    class="fa fa-edit me-10"></i>Editar</button>
                                            <button type="button" data-id="' . $item->id . '" data-estado="' . $item->estado_hitoria . '" onclick="evolucionHistoria(this);"
                                                class="waves-effect waves-light btn btn-secondary btn-flat"><i
                                                    class="fa fa-arrow-right me-10"></i>Evolución</button>
                                            <button type="button" onclick="imprimirHistoria(' . $item->id . ');"
                                                class="waves-effect waves-light btn btn-danger btn-flat"><i
                                                    class="fa fa-print me-10"></i>Imprimir</button>
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

    public function consultasLateral($idHistoria)   {
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

        $fileName = 'Historia_neuro_'.$idHist. '.pdf';
        $filePath = 'historias_neuro/' . $fileName;
        $pdf->save(public_path($filePath));
        $url = asset($filePath);

        return response()->json(['url' => $url]);
    }

    public function cerrarHistoriaNeuro()
    {
        try {
            $idHist = request()->input('idHist');
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
                    'estado_hitoria' => 'cerrada',
                ]);


            if ($consulta) {
                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Historia cerrada correctamente'
                    ]
                );
            } else {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'No se encontró la historia o no se pudo cerrar'
                    ],
                    404
                );
            }
        } catch (\Exception $e) {
            // Manejar cualquier error o excepción
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Ocurrió un error al intentar cerrar la historia',
                    'error' => $e->getMessage()
                ],
                500
            );
        }
    }
}
