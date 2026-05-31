<?php
// app/Http/Controllers/ContratosEpsController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ContratosEpsController extends Controller
{
    // ── Vista principal ─────────────────────────────
    public function index()
    {
        if (!Auth::check()) {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
        return view('Adminitraccion.gestionarContratosEps');
    }

    // ── CONTRATOS ────────────────────────────────────
    public function listarContratos(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false], 401);
        }
        $perPage = 10;
        $page    = max(1, (int)$request->get('page', 1));
        $search  = $request->get('search', '');

        $query = DB::connection('mysql')->table('contratos_eps')
            ->leftJoin('eps', 'eps.id', '=', 'contratos_eps.id_eps')
            ->selectRaw('contratos_eps.*, eps.nombre AS eps_nombre, (SELECT COUNT(*) FROM planes_eps WHERE planes_eps.id_contrato = contratos_eps.id) AS planes_count')
            ->orderBy('contratos_eps.id', 'desc');

        if ($search) {
            $query->where('eps.nombre', 'LIKE', "%{$search}%");
        }

        $registros = $query->paginate($perPage, ['*'], 'page', $page);
        $html = '';
        $x = ($page - 1) * $perPage + 1;

        foreach ($registros as $r) {
            $planesCount = $r->planes_count;
            $badgeEstado = $r->estado === 'activo'
                ? '<span class="badge-activo">Activo</span>'
                : '<span class="badge-borrador">Borrador</span>';
            $inicio = $r->fecha_inicio ? date('M Y', strtotime($r->fecha_inicio)) : '—';
            $fin    = $r->fecha_fin    ? date('M Y', strtotime($r->fecha_fin))    : '—';
            $epsNombre    = addslashes($r->eps_nombre);
            $fechaInicioV = $r->fecha_inicio ?? '';
            $fechaFinV    = $r->fecha_fin    ?? '';
            $estadoV      = $r->estado;
            $html .= "
            <tr>
                <td>{$x}</td>
                <td><strong>{$r->eps_nombre}</strong></td>
                <td>{$inicio}</td>
                <td>{$fin}</td>
                <td><span class=\"badge-planes\">{$planesCount} planes</span></td>
                <td>{$badgeEstado}</td>
                <td>
                    <span class=\"action-btn\" onclick=\"verPlanes({$r->id},'{$epsNombre}')\">
                        <i class=\"fa fa-list me-1\"></i>Ver planes</span>
                    <span class=\"action-btn\" onclick=\"editarContrato({$r->id},{$r->id_eps},'{$fechaInicioV}','{$fechaFinV}','{$estadoV}')\">
                        <i class=\"fa fa-edit\"></i></span>
                    <span class=\"action-delete\" onclick=\"eliminarContrato({$r->id})\">
                        <i class=\"fa fa-trash\"></i></span>
                </td>
            </tr>";
            $x++;
        }

        return response()->json([
            'html'       => $html,
            'pagination' => (string)$registros->appends(['search' => $search])->links('pagination::bootstrap-5'),
            'total'      => $registros->total(),
        ]);
    }

    public function guardarContrato(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false], 401);
        }
        $d = $request->all();

        if ($d['accion'] === 'guardar') {
            DB::connection('mysql')->table('contratos_eps')->insert([
                'id_eps'       => $d['id_eps'],
                'fecha_inicio' => $d['fecha_inicio'] ?: null,
                'fecha_fin'    => $d['fecha_fin']    ?: null,
                'estado'       => $d['estado'],
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        } elseif ($d['accion'] === 'editar') {
            DB::connection('mysql')->table('contratos_eps')
                ->where('id', $d['idContrato'])
                ->update([
                    'id_eps'       => $d['id_eps'],
                    'fecha_inicio' => $d['fecha_inicio'] ?: null,
                    'fecha_fin'    => $d['fecha_fin']    ?: null,
                    'estado'       => $d['estado'],
                    'updated_at'   => now(),
                ]);
        } else {
            return response()->json(['success' => false, 'message' => 'Acción no válida.'], 400);
        }
        return response()->json(['success' => true, 'message' => 'Operación realizada exitosamente.']);
    }

    public function eliminarContrato(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false], 401);
        }
        DB::connection('mysql')->table('contratos_eps')->where('id', $request->idContrato)->delete();
        return response()->json(['success' => true]);
    }

    // ── PLANES ───────────────────────────────────────
    public function listarPlanes(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false], 401);
        }
        $search = $request->get('search', '');
        $query  = DB::connection('mysql')->table('planes_eps')
            ->where('id_contrato', $request->idContrato)
            ->orderBy('id', 'desc');
        if ($search) {
            $query->where('nombre', 'LIKE', "%{$search}%");
        }
        $planes = $query->get();
        $html   = '';
        $x      = 1;

        foreach ($planes as $p) {
            $limite    = $p->limite_consultas ?? 'Ilimitada';
            $periodo   = $p->periodo === 'anual' ? 'Anual' : '—';
            $badge     = $p->estado === 'activo'
                ? '<span class="badge-activo">Activo</span>'
                : '<span class="badge-inactivo" style="background:#f0f0f0;color:#888;border-radius:12px;padding:2px 10px;font-size:11px;font-weight:600;">Inactivo</span>';
            $nombre    = addslashes($p->nombre);
            $descripS  = addslashes($p->descripcion ?? '');
            $limiteV   = $p->limite_consultas ?? '';
            $html   .= "
            <tr>
                <td>{$x}</td>
                <td><strong>{$p->nombre}</strong></td>
                <td>{$p->descripcion}</td>
                <td>{$limite}</td>
                <td>{$periodo}</td>
                <td>{$badge}</td>
                <td>
                    <span class=\"action-btn\" onclick=\"verCopagos({$p->id},'{$nombre}')\">
                        <i class=\"fa fa-dollar-sign me-1\"></i>Copagos</span>
                    <span class=\"action-btn\" onclick=\"editarPlan({$p->id},'{$nombre}','{$descripS}','{$limiteV}','{$p->periodo}','{$p->estado}')\">
                        <i class=\"fa fa-edit\"></i></span>
                    <span class=\"action-delete\" onclick=\"eliminarPlan({$p->id})\">
                        <i class=\"fa fa-trash\"></i></span>
                </td>
            </tr>";
            $x++;
        }
        return response()->json(['html' => $html]);
    }

    public function guardarPlan(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false], 401);
        }
        $d      = $request->all();
        $limite = ($d['limite_consultas'] === '' || strtolower($d['limite_consultas']) === 'ilimitada')
            ? null : (int)$d['limite_consultas'];

        if ($d['accion'] === 'guardar') {
            DB::connection('mysql')->table('planes_eps')->insert([
                'id_contrato'      => $d['id_contrato'],
                'nombre'           => $d['nombre'],
                'descripcion'      => $d['descripcion'] ?? null,
                'limite_consultas' => $limite,
                'periodo'          => $d['periodo'],
                'estado'           => $d['estado'],
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);
        } elseif ($d['accion'] === 'editar') {
            DB::connection('mysql')->table('planes_eps')
                ->where('id', $d['idPlan'])
                ->update([
                    'nombre'           => $d['nombre'],
                    'descripcion'      => $d['descripcion'] ?? null,
                    'limite_consultas' => $limite,
                    'periodo'          => $d['periodo'],
                    'estado'           => $d['estado'],
                    'updated_at'       => now(),
                ]);
        } else {
            return response()->json(['success' => false, 'message' => 'Acción no válida.'], 400);
        }
        return response()->json(['success' => true, 'message' => 'Plan guardado.']);
    }

    public function eliminarPlan(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false], 401);
        }
        DB::connection('mysql')->table('planes_eps')->where('id', $request->idPlan)->delete();
        return response()->json(['success' => true]);
    }

    // ── COPAGOS ──────────────────────────────────────
    public function listarCopagos(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false], 401);
        }
        $copagos = DB::connection('mysql')->table('copagos_eps')
            ->where('id_plan', $request->idPlan)
            ->orderBy('id')
            ->get();
        $html = '';
        $x    = 1;

        foreach ($copagos as $c) {
            $monto   = '$' . number_format($c->monto_copago, 0, ',', '.');
            $max     = $c->max_sesiones ?? 'Ilimitada';
            $tipoS   = addslashes($c->tipo_servicio);
            $maxV    = $c->max_sesiones ?? '';
            $html .= "
            <tr>
                <td>{$x}</td>
                <td>{$c->tipo_servicio}</td>
                <td><span class=\"copago-amount\">{$monto}</span></td>
                <td>{$max}</td>
                <td>
                    <span class=\"action-btn\" onclick=\"editarCopago({$c->id},'{$tipoS}',{$c->monto_copago},'{$maxV}')\">
                        <i class=\"fa fa-edit\"></i></span>
                    <span class=\"action-delete\" onclick=\"eliminarCopago({$c->id})\">
                        <i class=\"fa fa-trash\"></i></span>
                </td>
            </tr>";
            $x++;
        }
        return response()->json(['html' => $html]);
    }

    public function guardarCopago(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false], 401);
        }
        $d   = $request->all();
        $max = ($d['max_sesiones'] === '' || strtolower($d['max_sesiones']) === 'ilimitada')
            ? null : (int)$d['max_sesiones'];

        if ($d['accion'] === 'guardar') {
            DB::connection('mysql')->table('copagos_eps')->insert([
                'id_plan'       => $d['id_plan'],
                'tipo_servicio' => $d['tipo_servicio'],
                'monto_copago'  => $d['monto_copago'],
                'max_sesiones'  => $max,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        } elseif ($d['accion'] === 'editar') {
            DB::connection('mysql')->table('copagos_eps')
                ->where('id', $d['idCopago'])
                ->update([
                    'tipo_servicio' => $d['tipo_servicio'],
                    'monto_copago'  => $d['monto_copago'],
                    'max_sesiones'  => $max,
                    'updated_at'    => now(),
                ]);
        } else {
            return response()->json(['success' => false, 'message' => 'Acción no válida.'], 400);
        }
        return response()->json(['success' => true, 'message' => 'Servicio guardado.']);
    }

    public function eliminarCopago(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false], 401);
        }
        DB::connection('mysql')->table('copagos_eps')->where('id', $request->idCopago)->delete();
        return response()->json(['success' => true]);
    }

    // ── Endpoint para ficha paciente ─────────────────
    public function planesPorEps(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false], 401);
        }
        $contrato = DB::connection('mysql')->table('contratos_eps')
            ->where('id_eps', $request->idEps)
            ->where('estado', 'activo')
            ->orderBy('id', 'desc')
            ->first();

        if (!$contrato) {
            return response()->json(['planes' => []]);
        }

        $planes = DB::connection('mysql')->table('planes_eps')
            ->where('id_contrato', $contrato->id)
            ->where('estado', 'activo')
            ->select('id', 'nombre', 'limite_consultas', 'periodo')
            ->get();

        return response()->json(['planes' => $planes]);
    }

    // ── Lista de entidades EPS para select ──────────
    public function listarEntidadesEps(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false], 401);
        }
        $eps = DB::connection('mysql')->table('eps')
            ->orderBy('nombre')
            ->select('id', 'nombre')
            ->get();
        return response()->json($eps);
    }
}
