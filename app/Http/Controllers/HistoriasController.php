<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\HistoriaPsicologica;
use App\Models\CategoriaHCP;
use App\Models\Pacientes;
use App\Models\Profesional;

class HistoriasController extends Controller
{
    public function historiaPsicologia()
    {
        if (Auth::check()) {
            return view('HistoriasClinica.psicologia');
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function buscaHistoriaPsicologica(Request $request)
    {
        $idHist = $request->input('idHist');
        $historia = HistoriaPsicologica::busquedaHistoria($idHist);

        $pacientes = Pacientes::busquedaPaciente($historia->id_paciente);

        $antecedentesPersonales = HistoriaPsicologica::busquedaAntecedentes($historia->id);
        $antecedentesFamiliares = HistoriaPsicologica::busquedaAntFamiliares($historia->id);
        $areaAjuste = HistoriaPsicologica::busquedaAreaAjuste($historia->id);
        $interconuslta = HistoriaPsicologica::busquedaInterconsulta($historia->id);
        $aparienciaPersonal = HistoriaPsicologica::busquedaAparienciaPersonal($historia->id);
        $funcionesCognitiva = HistoriaPsicologica::busquedaFuncionesCognitivas($historia->id);
        $funcionesSomaticas = HistoriaPsicologica::busquedaFuncionesSomaticas($historia->id);
        
        return response()->json([
            'historia' => $historia,
            'paciente' => $pacientes,
            'antecedentesPersonales' => $antecedentesPersonales,
            'antecedentesFamiliares' => $antecedentesFamiliares,
            'areaAjuste' => $areaAjuste,
            'interconuslta' => $interconuslta,
            'aparienciaPersonal' => $aparienciaPersonal,
            'funcionesCognitiva' => $funcionesCognitiva,
            'funcionesSomaticas' => $funcionesSomaticas          
        ]);
    }

    public function buscaProfesionalHistoria(Request $request){
        $idProf = $request->input('idProf');
        $profesional = Profesional::busquedaProfesionalHitoria($idProf);
       
        return response()->json($profesional);
    }

    public function buscaCUPS(Request $request)
    {
        $id = $request->get('id');
        $query = $request->get('q');
        $page = $request->get('page', 1); // Página actual
        $perPage = 30; // Número de resultados por página

        // Si se proporciona un ID, devolver solo ese registro
        if ($id) {
            $resultado = DB::connection('mysql')
                ->table('referencia_cups')
                ->where('id', $id)
                ->select('id', 'nombre as text')
                ->first();

            if ($resultado) {
                return response()->json($resultado); // Devuelve el registro único
            }

            return response()->json(null, 404); // Registro no encontrado
        }

        // Búsqueda por término
        $resultados = DB::connection('mysql')
            ->table('referencia_cups')
            ->where('nombre', 'like', '%' . $query . '%')
            ->orWhere('codigo', 'like', '%' . $query . '%')
            ->select('id', 'nombre as text')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        // Total de resultados para paginación
        $total = DB::connection('mysql')
            ->table('referencia_cups')
            ->where('nombre', 'like', '%' . $query . '%')
            ->orWhere('codigo', 'like', '%' . $query . '%')
            ->count();

        // Formato de respuesta compatible con Select2
        return response()->json([
            'data' => $resultados,
            'total_count' => $total
        ]);
    }

    public function obtenerOpcionesHCP()
    {
       
        $categorias = CategoriaHCP::with('opciones')->get();
        return response()->json($categorias);
    }

    public function buscaCIE(Request $request)
    {
        $id = $request->get('id');
        $query = $request->get('q');
        $page = $request->get('page', 1);
        $perPage = 30; 

        if ($id) {
            $resultado = DB::connection('mysql')
                ->table('referencia_cie10')
                ->where('id', $id)
                ->select('id', 'nombre as text')
                ->first();

            if ($resultado) {
                return response()->json($resultado); // Devuelve el registro único
            }

            return response()->json(null, 404); // Registro no encontrado
        }

        $resultados = DB::connection('mysql')
            ->table('referencia_cie10')
            ->where('nombre', 'like', '%' . $query . '%')
            ->orWhere('codigo', 'like', '%' . $query . '%')
            ->select('id', 'nombre as text')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        // Total de resultados para paginación
        $total = DB::connection('mysql')
            ->table('referencia_cie10')
            ->where('nombre', 'like', '%' . $query . '%')
            ->orWhere('codigo', 'like', '%' . $query . '%')
            ->count();

        // Formato de respuesta compatible con Select2
        return response()->json([
            'data' => $resultados,
            'total_count' => $total
        ]);
    }

    public function  guardarHistoriaPsicologica(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'estado' => 'error',
                'mensaje' => 'Su sesión ha terminado.',
            ], 401);
        }

        $data = $request->all();

        $respuesta = HistoriaPsicologica::guardar($data);

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

    public function listaHistoriasPsicologica(Request $request)
    {

        if (Auth::check()) {
            $perPage = 5; // Número de posts por página
            $page = request()->get('page', 1);
            $search = request()->get('search');
            if (!is_numeric($page)) {
                $page = 1; // Establecer un valor predeterminado si no es numérico
            }

            $historias = DB::connection('mysql')
                ->table('historia_clinica')
                ->leftJoin('pacientes', 'historia_clinica.id_paciente', '=', 'pacientes.id')
                ->where('estado_registro', 'ACTIVO')
                ->orderBy('historia_clinica.fecha_historia', 'desc')
                ->select(
                    "historia_clinica.id",
                    DB::raw("CONCAT(tipo_identificacion, ' ', identificacion) as identificacion_completa"),
                    DB::raw("CONCAT(primer_nombre,' ',segundo_nombre,' ',primer_apellido,' ', segundo_apellido) as nombre_completo"),
                    "historia_clinica.fecha_historia",
                    "historia_clinica.tipologia",
                    "historia_clinica.estado_hitoria"
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
                                            <div class="mx-lg-50 mx-20 min-w-70">
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
                                            <button type="button" onclick="evolucionHistoria(' . $item->id . ');"
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
}
