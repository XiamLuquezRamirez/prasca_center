<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AutorizacionesController extends Controller
{
    public function index()
    {
        if (!Auth::check()) return redirect("/")->with("error", "Su Sesión ha Terminado");
        return view('Adminitraccion.gestionarAutorizaciones');
    }

    public function listar(Request $request)
    {
        if (!Auth::check()) return response()->json(['success' => false], 401);

        $perPage  = 10;
        $page     = max(1, (int) $request->get('page', 1));
        $busqueda = $request->get('busqueda', '');

        $query = DB::connection('mysql')->table('autorizaciones')
            ->join('pacientes', 'pacientes.id', '=', 'autorizaciones.id_paciente')
            ->join('planes_eps', 'planes_eps.id', '=', 'autorizaciones.id_plan')
            ->join('contratos_eps', 'contratos_eps.id', '=', 'planes_eps.id_contrato')
            ->join('eps', 'eps.id', '=', 'contratos_eps.id_eps')
            ->selectRaw("
                autorizaciones.*,
                CONCAT(pacientes.primer_nombre,' ',pacientes.primer_apellido) AS nombre_paciente,
                pacientes.identificacion AS doc_paciente,
                planes_eps.nombre AS nombre_plan,
                eps.entidad AS nombre_eps,
                (SELECT COUNT(*) FROM citas WHERE citas.id_autorizacion = autorizaciones.id) AS sesiones_usadas
            ")
            ->orderBy('autorizaciones.id', 'desc');

        if ($busqueda) {
            $query->where(function ($q) use ($busqueda) {
                $q->where('autorizaciones.numero_autorizacion', 'LIKE', "%{$busqueda}%")
                  ->orWhere('pacientes.primer_nombre', 'LIKE', "%{$busqueda}%")
                  ->orWhere('pacientes.primer_apellido', 'LIKE', "%{$busqueda}%")
                  ->orWhere('eps.entidad', 'LIKE', "%{$busqueda}%");
            });
        }

        $registros = $query->paginate($perPage, ['*'], 'page', $page);
        $html = '';
        $x = ($page - 1) * $perPage + 1;

        $badges = [
            'activa'  => '<span class="badge bg-success">Activa</span>',
            'agotada' => '<span class="badge bg-warning text-dark">Agotada</span>',
            'vencida' => '<span class="badge bg-secondary">Vencida</span>',
            'anulada' => '<span class="badge bg-danger">Anulada</span>',
        ];

        foreach ($registros as $r) {
            $sesiones  = $r->sesiones_autorizadas
                ? "{$r->sesiones_usadas} / {$r->sesiones_autorizadas}"
                : ($r->sesiones_usadas . ' / ∞');
            $copago    = '$' . number_format($r->valor_copago, 0, ',', '.');
            $vence     = $r->fecha_vencimiento
                ? date('d/m/Y', strtotime($r->fecha_vencimiento))
                : '—';
            $badge     = $badges[$r->estado] ?? $r->estado;
            $numJs     = addslashes($r->numero_autorizacion);
            $tipoJs    = addslashes($r->tipo_servicio);
            $obsJs     = addslashes($r->observaciones ?? '');

            $html .= "
            <tr>
                <td>{$x}</td>
                <td><strong>{$r->nombre_paciente}</strong><br><small class='text-muted'>{$r->doc_paciente}</small></td>
                <td>{$r->nombre_eps}<br><small>{$r->nombre_plan}</small></td>
                <td><code>{$numJs}</code></td>
                <td>{$r->tipo_servicio}</td>
                <td class='text-center'>{$sesiones}</td>
                <td class='text-end'>{$copago}</td>
                <td>{$vence}</td>
                <td>{$badge}</td>
                <td>
                    <button type='button' class='btn btn-xs btn-primary me-1'
                        onclick='editarAutorizacion({$r->id},{$r->id_paciente},{$r->id_plan},\"{$numJs}\",\"{$tipoJs}\",\"{$r->fecha_solicitud}\",\"{$r->fecha_vencimiento}\",{$r->sesiones_autorizadas},{$r->valor_copago},{$r->valor_autorizado},\"{$r->estado}\",\"{$obsJs}\")'>
                        <i class='fa fa-edit'></i></button>
                    <button type='button' class='btn btn-xs btn-danger'
                        onclick='eliminarAutorizacion({$r->id})'>
                        <i class='fa fa-trash'></i></button>
                </td>
            </tr>";
            $x++;
        }

        return response()->json([
            'html'       => $html,
            'pagination' => (string) $registros->appends(['busqueda' => $busqueda])
                                ->links('Adminitraccion.PaginacionAutorizaciones'),
            'total'      => $registros->total(),
        ]);
    }

    public function guardar(Request $request)
    {
        if (!Auth::check()) return response()->json(['success' => false], 401);

        $id   = $request->input('id');
        $data = [
            'id_paciente'         => $request->input('id_paciente'),
            'id_plan'             => $request->input('id_plan'),
            'numero_autorizacion' => $request->input('numero_autorizacion'),
            'tipo_servicio'       => $request->input('tipo_servicio'),
            'fecha_solicitud'     => $request->input('fecha_solicitud'),
            'fecha_vencimiento'   => $request->input('fecha_vencimiento') ?: null,
            'sesiones_autorizadas'=> $request->input('sesiones_autorizadas') ?: null,
            'valor_copago'        => $request->input('valor_copago', 0),
            'valor_autorizado'    => $request->input('valor_autorizado', 0),
            'estado'              => $request->input('estado', 'activa'),
            'observaciones'       => $request->input('observaciones') ?: null,
            'updated_at'          => now(),
        ];

        if ($id) {
            DB::connection('mysql')->table('autorizaciones')->where('id', $id)->update($data);
        } else {
            $data['created_at'] = now();
            DB::connection('mysql')->table('autorizaciones')->insert($data);
        }

        return response()->json(['success' => true]);
    }

    public function eliminar(Request $request)
    {
        if (!Auth::check()) return response()->json(['success' => false], 401);

        $id    = $request->input('id');
        $citas = DB::connection('mysql')->table('citas')
            ->where('id_autorizacion', $id)->count();

        if ($citas > 0) {
            return response()->json([
                'success' => false,
                'message' => "No se puede eliminar: hay {$citas} cita(s) vinculada(s).",
            ], 422);
        }

        DB::connection('mysql')->table('autorizaciones')->where('id', $id)->delete();
        return response()->json(['success' => true]);
    }

    // Endpoint para el modal de citas: autorizaciones activas de un paciente
    public function porPaciente(Request $request)
    {
        if (!Auth::check()) return response()->json([], 401);

        $idPaciente = $request->input('id_paciente');

        $autorizaciones = DB::connection('mysql')->table('autorizaciones')
            ->join('planes_eps',    'planes_eps.id',    '=', 'autorizaciones.id_plan')
            ->join('contratos_eps', 'contratos_eps.id', '=', 'planes_eps.id_contrato')
            ->join('eps',           'eps.id',           '=', 'contratos_eps.id_eps')
            ->selectRaw("
                autorizaciones.id,
                autorizaciones.numero_autorizacion,
                autorizaciones.tipo_servicio,
                autorizaciones.sesiones_autorizadas,
                autorizaciones.valor_copago,
                autorizaciones.fecha_vencimiento,
                planes_eps.nombre AS nombre_plan,
                eps.entidad AS nombre_eps,
                (SELECT COUNT(*) FROM citas WHERE citas.id_autorizacion = autorizaciones.id) AS sesiones_usadas
            ")
            ->where('autorizaciones.id_paciente', $idPaciente)
            ->where('autorizaciones.estado', 'activa')
            ->orderBy('autorizaciones.id', 'desc')
            ->get();

        return response()->json($autorizaciones);
    }

    // Planes activos del paciente (vía paciente_planes_eps)
    public function planesPorPaciente(Request $request)
    {
        if (!Auth::check()) return response()->json([], 401);

        $idPaciente = $request->input('id_paciente');

        $planes = DB::connection('mysql')->table('paciente_planes_eps')
            ->join('planes_eps',    'planes_eps.id',    '=', 'paciente_planes_eps.id_plan')
            ->join('contratos_eps', 'contratos_eps.id', '=', 'planes_eps.id_contrato')
            ->join('eps',           'eps.id',           '=', 'contratos_eps.id_eps')
            ->where('paciente_planes_eps.id_paciente', $idPaciente)
            ->where('paciente_planes_eps.estado', 'activo')
            ->select(
                'planes_eps.id',
                'planes_eps.nombre',
                'eps.entidad AS nombre_eps'
            )
            ->get();

        return response()->json($planes);
    }

    // Tipos de servicio (copagos) del plan
    public function serviciosPorPlan(Request $request)
    {
        if (!Auth::check()) return response()->json([], 401);

        $idPlan = $request->input('id_plan');

        $copagos = DB::connection('mysql')->table('copagos_eps')
            ->where('id_plan', $idPlan)
            ->select('id', 'tipo_servicio', 'monto_copago', 'max_sesiones')
            ->get();

        return response()->json($copagos);
    }
}
