# Contratos EPS Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Implementar gestión de contratos EPS (módulo admin con 3 tabs), asignación de plan en ficha del paciente (nueva pestaña en modal), y registro de autorización/copago por cita.

**Architecture:** Tres módulos secuenciales — (1) ContratosEpsController con CRUD para contratos/planes/copagos vía raw DB queries, (2) nuevos métodos en PacientesController para asignación de plan y autorizaciones, (3) nueva pestaña nav-pills en el modal de gestionarPacientes. Todo sigue el patrón fetch+JSON+DB::connection('mysql') ya establecido en el proyecto.

**Tech Stack:** Laravel 8, PHP 8.2, MySQL via DB::connection('mysql'), Bootstrap 5 + InvestX theme, fetch() AJAX con X-CSRF-TOKEN, PHPUnit feature tests.

---

## Mapa de archivos

| Acción | Archivo |
|--------|---------|
| Crear | `database/migrations/2026_05_30_000001_create_contratos_eps_tables.php` |
| Crear | `database/migrations/2026_05_30_000002_add_autorizacion_to_citas.php` |
| Crear | `app/Http/Controllers/ContratosEpsController.php` |
| Crear | `resources/views/Adminitraccion/gestionarContratosEps.blade.php` |
| Crear | `tests/Feature/ContratosEpsTest.php` |
| Modificar | `routes/web.php` — import + 11 rutas nuevas |
| Modificar | `resources/views/Plantilla/Menu.blade.php` — enlace al nuevo módulo |
| Modificar | `app/Http/Controllers/PacientesController.php` — 5 métodos nuevos |
| Modificar | `resources/views/Pacientes/gestionarPacientes.blade.php` — 3ª pestaña + JS |

---

### Task 1: Migraciones de base de datos

**Files:**
- Create: `database/migrations/2026_05_30_000001_create_contratos_eps_tables.php`
- Create: `database/migrations/2026_05_30_000002_add_autorizacion_to_citas.php`
- Test: `tests/Feature/ContratosEpsTest.php`

- [ ] **Step 1: Crear migración de las 4 tablas EPS**

```php
<?php
// database/migrations/2026_05_30_000001_create_contratos_eps_tables.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContratosEpsTables extends Migration
{
    public function up()
    {
        Schema::create('contratos_eps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_eps');
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->enum('estado', ['activo', 'borrador'])->default('borrador');
            $table->timestamps();
            $table->foreign('id_eps')->references('id')->on('entidades')->onDelete('cascade');
        });

        Schema::create('planes_eps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_contrato');
            $table->string('nombre', 120);
            $table->string('descripcion', 255)->nullable();
            $table->unsignedInteger('limite_consultas')->nullable(); // NULL = ilimitado
            $table->enum('periodo', ['anual', 'sin_periodo'])->default('anual');
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->timestamps();
            $table->foreign('id_contrato')->references('id')->on('contratos_eps')->onDelete('cascade');
        });

        Schema::create('copagos_eps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_plan');
            $table->string('tipo_servicio', 120);
            $table->decimal('monto_copago', 10, 2);
            $table->unsignedInteger('max_sesiones')->nullable(); // NULL = ilimitado
            $table->timestamps();
            $table->foreign('id_plan')->references('id')->on('planes_eps')->onDelete('cascade');
        });

        Schema::create('paciente_planes_eps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_paciente');
            $table->unsignedBigInteger('id_plan');
            $table->string('numero_poliza', 60)->nullable();
            $table->date('fecha_vinculacion')->nullable();
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->timestamps();
            $table->foreign('id_paciente')->references('id')->on('pacientes')->onDelete('cascade');
            $table->foreign('id_plan')->references('id')->on('planes_eps')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('paciente_planes_eps');
        Schema::dropIfExists('copagos_eps');
        Schema::dropIfExists('planes_eps');
        Schema::dropIfExists('contratos_eps');
    }
}
```

- [ ] **Step 2: Crear migración para columnas en citas**

```php
<?php
// database/migrations/2026_05_30_000002_add_autorizacion_to_citas.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAutorizacionToCitas extends Migration
{
    public function up()
    {
        Schema::table('citas', function (Blueprint $table) {
            $table->string('numero_autorizacion', 60)->nullable()->after('comentario');
            $table->decimal('copago_cobrado', 10, 2)->nullable()->after('numero_autorizacion');
        });
    }

    public function down()
    {
        Schema::table('citas', function (Blueprint $table) {
            $table->dropColumn(['numero_autorizacion', 'copago_cobrado']);
        });
    }
}
```

- [ ] **Step 3: Escribir test que verifica las tablas**

```php
<?php
// tests/Feature/ContratosEpsTest.php
namespace Tests\Feature;

use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ContratosEpsTest extends TestCase
{
    public function test_contratos_eps_tables_exist()
    {
        $this->assertTrue(Schema::hasTable('contratos_eps'));
        $this->assertTrue(Schema::hasTable('planes_eps'));
        $this->assertTrue(Schema::hasTable('copagos_eps'));
        $this->assertTrue(Schema::hasTable('paciente_planes_eps'));
    }

    public function test_citas_has_autorizacion_columns()
    {
        $this->assertTrue(Schema::hasColumn('citas', 'numero_autorizacion'));
        $this->assertTrue(Schema::hasColumn('citas', 'copago_cobrado'));
    }

    public function test_contratos_eps_index_requires_auth()
    {
        $response = $this->get('/Administracion/ContratosEps');
        $response->assertRedirect('/');
    }

    public function test_listar_contratos_requires_auth()
    {
        $response = $this->postJson('/contratosEps/listarContratos');
        $response->assertStatus(401);
    }

    public function test_guardar_plan_paciente_requires_auth()
    {
        $response = $this->postJson('/pacientes/guardarPlanEps');
        $response->assertStatus(401);
    }
}
```

- [ ] **Step 4: Ejecutar migraciones y verificar que el test falla (tablas aún no existen)**

```bash
php artisan test tests/Feature/ContratosEpsTest.php --filter=test_contratos_eps_tables_exist
```
Esperado: FAIL — `contratos_eps` table not found.

- [ ] **Step 5: Correr migraciones**

```bash
php artisan migrate
```
Esperado: 2 migraciones ejecutadas sin errores.

- [ ] **Step 6: Ejecutar tests de esquema — deben pasar**

```bash
php artisan test tests/Feature/ContratosEpsTest.php --filter=test_contratos_eps_tables_exist
php artisan test tests/Feature/ContratosEpsTest.php --filter=test_citas_has_autorizacion_columns
```
Esperado: PASS ambos.

- [ ] **Step 7: Commit**

```bash
git add database/migrations/2026_05_30_000001_create_contratos_eps_tables.php
git add database/migrations/2026_05_30_000002_add_autorizacion_to_citas.php
git add tests/Feature/ContratosEpsTest.php
git commit -m "feat: add contratos EPS migrations and schema tests"
```

---

### Task 2: ContratosEpsController

**Files:**
- Create: `app/Http/Controllers/ContratosEpsController.php`

- [ ] **Step 1: Crear el controlador completo**

```php
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
            ->leftJoin('entidades', 'entidades.id', '=', 'contratos_eps.id_eps')
            ->select('contratos_eps.*', 'entidades.nombre as eps_nombre')
            ->orderBy('contratos_eps.id', 'desc');

        if ($search) {
            $query->where('entidades.nombre', 'LIKE', "%{$search}%");
        }

        $registros = $query->paginate($perPage, ['*'], 'page', $page);
        $html = '';
        $x = ($page - 1) * $perPage + 1;

        foreach ($registros as $r) {
            $planesCount = DB::connection('mysql')->table('planes_eps')
                ->where('id_contrato', $r->id)->count();
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
        } else {
            DB::connection('mysql')->table('contratos_eps')
                ->where('id', $d['idContrato'])
                ->update([
                    'id_eps'       => $d['id_eps'],
                    'fecha_inicio' => $d['fecha_inicio'] ?: null,
                    'fecha_fin'    => $d['fecha_fin']    ?: null,
                    'estado'       => $d['estado'],
                    'updated_at'   => now(),
                ]);
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
        } else {
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
        } else {
            DB::connection('mysql')->table('copagos_eps')
                ->where('id', $d['idCopago'])
                ->update([
                    'tipo_servicio' => $d['tipo_servicio'],
                    'monto_copago'  => $d['monto_copago'],
                    'max_sesiones'  => $max,
                    'updated_at'    => now(),
                ]);
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
        $eps = DB::connection('mysql')->table('entidades')
            ->orderBy('nombre')
            ->select('id', 'nombre')
            ->get();
        return response()->json($eps);
    }
}
```

- [ ] **Step 2: Verificar sintaxis PHP**

```bash
php -l app/Http/Controllers/ContratosEpsController.php
```
Esperado: `No syntax errors detected`

- [ ] **Step 3: Commit**

```bash
git add app/Http/Controllers/ContratosEpsController.php
git commit -m "feat: add ContratosEpsController with full CRUD"
```

---

### Task 3: Rutas y menú

**Files:**
- Modify: `routes/web.php`
- Modify: `resources/views/Plantilla/Menu.blade.php`

- [ ] **Step 1: Agregar import y rutas en web.php**

Agrega al bloque de `use` al inicio del archivo (después de la última línea `use`):

```php
use App\Http\Controllers\ContratosEpsController;
```

Dentro del grupo `Route::middleware(['auth'])->group(function () {`, agrega este bloque (sugerido: después del bloque de rutas de `SistemaController`):

```php
    /// CONTRATOS EPS
    Route::middleware(['permission:AdminContratos'])->group(function () {
        Route::get('/Administracion/ContratosEps', [ContratosEpsController::class, 'index'])->name('contratosEps.index');
    });
    Route::post('/contratosEps/listarContratos', [ContratosEpsController::class, 'listarContratos'])->name('contratosEps.listarContratos');
    Route::post('/contratosEps/guardarContrato', [ContratosEpsController::class, 'guardarContrato'])->name('contratosEps.guardarContrato');
    Route::post('/contratosEps/eliminarContrato', [ContratosEpsController::class, 'eliminarContrato'])->name('contratosEps.eliminarContrato');
    Route::post('/contratosEps/listarPlanes', [ContratosEpsController::class, 'listarPlanes'])->name('contratosEps.listarPlanes');
    Route::post('/contratosEps/guardarPlan', [ContratosEpsController::class, 'guardarPlan'])->name('contratosEps.guardarPlan');
    Route::post('/contratosEps/eliminarPlan', [ContratosEpsController::class, 'eliminarPlan'])->name('contratosEps.eliminarPlan');
    Route::post('/contratosEps/listarCopagos', [ContratosEpsController::class, 'listarCopagos'])->name('contratosEps.listarCopagos');
    Route::post('/contratosEps/guardarCopago', [ContratosEpsController::class, 'guardarCopago'])->name('contratosEps.guardarCopago');
    Route::post('/contratosEps/eliminarCopago', [ContratosEpsController::class, 'eliminarCopago'])->name('contratosEps.eliminarCopago');
    Route::post('/contratosEps/planesPorEps',          [ContratosEpsController::class, 'planesPorEps'])->name('contratosEps.planesPorEps');
    Route::post('/contratosEps/listarEntidadesEps',    [ContratosEpsController::class, 'listarEntidadesEps'])->name('contratosEps.listarEntidadesEps');
```

- [ ] **Step 2: Verificar que las rutas se registran**

```bash
php artisan route:list --name=contratosEps
```
Esperado: 12 rutas listadas.

- [ ] **Step 3: Correr tests de autenticación — deben pasar ahora**

```bash
php artisan test tests/Feature/ContratosEpsTest.php --filter=test_contratos_eps_index_requires_auth
php artisan test tests/Feature/ContratosEpsTest.php --filter=test_listar_contratos_requires_auth
```
Esperado: PASS ambos.

- [ ] **Step 4: Agregar entrada en el menú**

En `resources/views/Plantilla/Menu.blade.php`, **después** del bloque `@if (in_array('Admineps', ...))` (alrededor de la línea 164), agregar:

```blade
@if (in_array('AdminContratos', session('permisos', [])))
    <li id="principalParametrosContratos">
        <a href="{{ url('/Administracion/ContratosEps') }}">
            <i class="icon-Commit"><span class="path1"></span><span
                class="path2"></span></i> Contratos EPS
        </a>
    </li>
@endif
```

- [ ] **Step 5: Commit**

```bash
git add routes/web.php resources/views/Plantilla/Menu.blade.php
git commit -m "feat: register ContratosEps routes and menu entry"
```

---

### Task 4: Vista gestionarContratosEps.blade.php

**Files:**
- Create: `resources/views/Adminitraccion/gestionarContratosEps.blade.php`

- [ ] **Step 1: Crear la vista completa**

```blade
{{-- resources/views/Adminitraccion/gestionarContratosEps.blade.php --}}
@extends('Plantilla.Principal')
@section('title', 'Gestionar Contratos EPS')
@section('Contenido')

<style>
    .badge-activo   { background:#e6f9ec;color:#1a8a3a;border-radius:12px;padding:2px 10px;font-size:11px;font-weight:600; }
    .badge-borrador { background:#fff8e1;color:#b07c00;border-radius:12px;padding:2px 10px;font-size:11px;font-weight:600; }
    .badge-planes   { background:#e8e8f7;color:#2b2b6c;border-radius:12px;padding:2px 10px;font-size:11px;font-weight:600; }
    .action-btn     { color:#2b2b6c;cursor:pointer;font-size:12px;margin-right:8px; }
    .action-btn:hover { text-decoration:underline; }
    .action-delete  { color:#dc3545;cursor:pointer;font-size:12px; }
    .copago-amount  { color:#1a8a3a;font-weight:600; }
    .info-bar       { background:#f0f0fa;border-radius:6px;padding:8px 14px;font-size:12px;color:#2b2b6c;margin-bottom:14px; }
</style>

<div class="content-header">
    <div class="d-flex align-items-center">
        <div class="me-auto">
            <h4 class="page-title">Gestionar Contratos EPS</h4>
            <div class="d-inline-block align-items-center">
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#"><i class="mdi mdi-home-outline"></i></a></li>
                        <li class="breadcrumb-item">Administración</li>
                        <li class="breadcrumb-item active">Contratos EPS</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Contratos EPS</h5>
                </div>
                <div class="card-body">

                    {{-- TABS --}}
                    <ul class="nav nav-tabs" id="contratosTab">
                        <li class="nav-item">
                            <a class="nav-link active" id="tab-contratos-link" data-bs-toggle="tab" href="#tab-contratos">
                                <i class="fa fa-file-contract me-1"></i> Contratos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab-planes-link" data-bs-toggle="tab" href="#tab-planes">
                                <i class="fa fa-layer-group me-1"></i> Planes · <span id="labelEpsPlanes">—</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab-copagos-link" data-bs-toggle="tab" href="#tab-copagos">
                                <i class="fa fa-dollar-sign me-1"></i> Copagos · <span id="labelPlanCopagos">—</span>
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content" style="padding-top:16px;">

                        {{-- TAB 1: CONTRATOS --}}
                        <div class="tab-pane fade show active" id="tab-contratos">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="input-group input-group-merge" style="max-width:280px;">
                                    <input type="text" id="busquedaContratos" class="form-control form-control-sm" placeholder="Buscar EPS...">
                                    <span class="input-group-text"><i class="fa fa-search"></i></span>
                                </div>
                                <button class="btn btn-xs btn-primary" onclick="nuevoContrato()">
                                    <i class="fa fa-plus me-1"></i> Nuevo Contrato
                                </button>
                            </div>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th><th>EPS</th><th>Vigencia Inicio</th><th>Vigencia Fin</th>
                                        <th>Planes</th><th>Estado</th><th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody id="trContratos"></tbody>
                            </table>
                            <div id="paginationContratos" class="text-center mt-2"></div>
                        </div>

                        {{-- TAB 2: PLANES --}}
                        <div class="tab-pane fade" id="tab-planes">
                            <div class="info-bar" id="infoBarPlanes">
                                <i class="fa fa-info-circle me-1"></i> Selecciona un contrato para ver sus planes.
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="input-group input-group-merge" style="max-width:280px;">
                                    <input type="text" id="busquedaPlanes" class="form-control form-control-sm" placeholder="Buscar plan...">
                                    <span class="input-group-text"><i class="fa fa-search"></i></span>
                                </div>
                                <button class="btn btn-xs btn-primary" onclick="nuevoPlan()">
                                    <i class="fa fa-plus me-1"></i> Nuevo Plan
                                </button>
                            </div>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th><th>Plan / Póliza</th><th>Descripción</th>
                                        <th>Límite consultas</th><th>Período</th><th>Estado</th><th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody id="trPlanes"></tbody>
                            </table>
                        </div>

                        {{-- TAB 3: COPAGOS --}}
                        <div class="tab-pane fade" id="tab-copagos">
                            <div class="info-bar" id="infoBarCopagos">
                                <i class="fa fa-info-circle me-1"></i> Selecciona un plan para ver sus copagos.
                            </div>
                            <div class="d-flex justify-content-end mb-3">
                                <button class="btn btn-xs btn-primary" onclick="nuevoCopago()">
                                    <i class="fa fa-plus me-1"></i> Agregar Servicio
                                </button>
                            </div>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th><th>Tipo de Servicio</th><th>Copago</th>
                                        <th>Máx. sesiones</th><th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody id="trCopagos"></tbody>
                            </table>
                        </div>

                    </div>{{-- /tab-content --}}
                </div>{{-- /card-body --}}
            </div>{{-- /card --}}
        </div>
    </div>
</section>

{{-- MODAL CONTRATO --}}
<div class="modal fade" id="modalContrato" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:500px;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="tituloModalContrato">Agregar contrato</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formContrato">
                    <input type="hidden" id="accionContrato" name="accion" value="guardar">
                    <input type="hidden" id="idContratoEdit" name="idContrato" value="">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label">EPS <span class="text-danger">*</span></label>
                                <select class="form-control" id="epsContrato" name="id_eps"></select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Vigencia inicio</label>
                                <input type="date" class="form-control" id="fechaInicioContrato" name="fecha_inicio">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Vigencia fin</label>
                                <input type="date" class="form-control" id="fechaFinContrato" name="fecha_fin">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Estado</label>
                                <select class="form-control" id="estadoContrato" name="estado">
                                    <option value="borrador">Borrador</option>
                                    <option value="activo">Activo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer text-end mt-2">
                        <button type="button" class="btn btn-primary-light me-1" data-bs-dismiss="modal">
                            <i class="ti-close"></i> Cancelar
                        </button>
                        <button type="button" class="btn btn-primary" onclick="guardarContrato()">
                            <i class="ti-save"></i> Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- MODAL PLAN --}}
<div class="modal fade" id="modalPlan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:500px;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="tituloModalPlan">Agregar plan</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formPlan">
                    <input type="hidden" id="accionPlan" name="accion" value="guardar">
                    <input type="hidden" id="idPlanEdit" name="idPlan" value="">
                    <input type="hidden" id="idContratoPlan" name="id_contrato" value="">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label">Nombre del plan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nombrePlan" name="nombre">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label">Descripción</label>
                                <input type="text" class="form-control" id="descripcionPlan" name="descripcion">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Límite consultas <small class="text-muted">(vacío = ilimitado)</small></label>
                                <input type="text" class="form-control" id="limitePlan" name="limite_consultas" placeholder="Ej: 24">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Período</label>
                                <select class="form-control" id="periodoPlan" name="periodo">
                                    <option value="anual">Anual</option>
                                    <option value="sin_periodo">Sin período</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Estado</label>
                                <select class="form-control" id="estadoPlan" name="estado">
                                    <option value="activo">Activo</option>
                                    <option value="inactivo">Inactivo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer text-end mt-2">
                        <button type="button" class="btn btn-primary-light me-1" data-bs-dismiss="modal">
                            <i class="ti-close"></i> Cancelar
                        </button>
                        <button type="button" class="btn btn-primary" onclick="guardarPlan()">
                            <i class="ti-save"></i> Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- MODAL COPAGO --}}
<div class="modal fade" id="modalCopago" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:460px;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="tituloModalCopago">Agregar servicio</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formCopago">
                    <input type="hidden" id="accionCopago" name="accion" value="guardar">
                    <input type="hidden" id="idCopagoEdit" name="idCopago" value="">
                    <input type="hidden" id="idPlanCopago" name="id_plan" value="">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label">Tipo de servicio <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="tipoServicioCopago" name="tipo_servicio"
                                    placeholder="Ej: Consulta Psicología">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Copago (COP) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="montoCopago" name="monto_copago"
                                    placeholder="Ej: 47400" min="0" step="100">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Máx. sesiones <small class="text-muted">(vacío = ilimitado)</small></label>
                                <input type="text" class="form-control" id="maxSesionesCopago" name="max_sesiones"
                                    placeholder="Ej: 50">
                            </div>
                        </div>
                    </div>
                    <div class="box-footer text-end mt-2">
                        <button type="button" class="btn btn-primary-light me-1" data-bs-dismiss="modal">
                            <i class="ti-close"></i> Cancelar
                        </button>
                        <button type="button" class="btn btn-primary" onclick="guardarCopago()">
                            <i class="ti-save"></i> Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const headers = { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF };

// Estado activo en el módulo
let activeContratoId   = null;
let activePlanId       = null;

document.addEventListener('DOMContentLoaded', function () {
    let menuP = document.getElementById('principalParametros');
    let menuS = document.getElementById('principalParametrosContratos');
    if (menuP) menuP.classList.add('active', 'menu-open');
    if (menuS) menuS.classList.add('active');

    cargarEpsSelect();
    cargarContratos(1);

    document.getElementById('busquedaContratos').addEventListener('input', function () {
        cargarContratos(1, this.value);
    });
    document.getElementById('busquedaPlanes').addEventListener('input', function () {
        if (activeContratoId) cargarPlanes(activeContratoId, this.value);
    });
    document.addEventListener('click', function (e) {
        if (e.target.matches('.pagination a')) {
            e.preventDefault();
            const page = e.target.getAttribute('href').split('page=')[1];
            if (!isNaN(page)) cargarContratos(page, document.getElementById('busquedaContratos').value);
        }
    });
});

// ── EPS select ───────────────────────────────────────────
function cargarEpsSelect() {
    fetch("{{ route('contratosEps.listarEntidadesEps') }}", {
        method: 'POST', headers
    })
    .then(r => r.json())
    .then(data => {
        const sel = document.getElementById('epsContrato');
        sel.innerHTML = '<option value="">Seleccionar EPS…</option>';
        data.forEach(e => {
            sel.innerHTML += `<option value="${e.id}">${e.nombre}</option>`;
        });
    });
}

// ── CONTRATOS ────────────────────────────────────────────
function cargarContratos(page = 1, search = '') {
    fetch("{{ route('contratosEps.listarContratos') }}", {
        method: 'POST', headers,
        body: JSON.stringify({ page, search })
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('trContratos').innerHTML       = data.html;
        document.getElementById('paginationContratos').innerHTML = data.pagination;
    });
}

function nuevoContrato() {
    document.getElementById('tituloModalContrato').textContent = 'Agregar contrato';
    document.getElementById('accionContrato').value   = 'guardar';
    document.getElementById('idContratoEdit').value   = '';
    document.getElementById('epsContrato').value      = '';
    document.getElementById('fechaInicioContrato').value = '';
    document.getElementById('fechaFinContrato').value    = '';
    document.getElementById('estadoContrato').value   = 'borrador';
    new bootstrap.Modal(document.getElementById('modalContrato')).show();
}

function editarContrato(id, idEps, fechaInicio, fechaFin, estado) {
    document.getElementById('tituloModalContrato').textContent = 'Editar contrato';
    document.getElementById('accionContrato').value      = 'editar';
    document.getElementById('idContratoEdit').value      = id;
    document.getElementById('epsContrato').value         = idEps;
    document.getElementById('fechaInicioContrato').value = fechaInicio;
    document.getElementById('fechaFinContrato').value    = fechaFin;
    document.getElementById('estadoContrato').value      = estado;
    new bootstrap.Modal(document.getElementById('modalContrato')).show();
}

function guardarContrato() {
    const body = {
        accion:       document.getElementById('accionContrato').value,
        idContrato:   document.getElementById('idContratoEdit').value,
        id_eps:       document.getElementById('epsContrato').value,
        fecha_inicio: document.getElementById('fechaInicioContrato').value,
        fecha_fin:    document.getElementById('fechaFinContrato').value,
        estado:       document.getElementById('estadoContrato').value,
    };
    if (!body.id_eps) { Swal.fire('Error', 'Selecciona una EPS.', 'warning'); return; }

    fetch("{{ route('contratosEps.guardarContrato') }}", {
        method: 'POST', headers, body: JSON.stringify(body)
    })
    .then(r => r.json())
    .then(data => {
        bootstrap.Modal.getInstance(document.getElementById('modalContrato')).hide();
        Swal.fire('¡Listo!', data.message, 'success');
        cargarContratos(1);
    });
}

function eliminarContrato(id) {
    Swal.fire({
        title: '¿Eliminar contrato?', text: 'Se eliminarán también sus planes y copagos.',
        icon: 'warning', showCancelButton: true,
        confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar'
    }).then(result => {
        if (result.isConfirmed) {
            fetch("{{ route('contratosEps.eliminarContrato') }}", {
                method: 'POST', headers, body: JSON.stringify({ idContrato: id })
            })
            .then(r => r.json())
            .then(() => { Swal.fire('Eliminado', '', 'success'); cargarContratos(1); });
        }
    });
}

// ── PLANES ────────────────────────────────────────────────
function verPlanes(idContrato, epsNombre) {
    activeContratoId = idContrato;
    document.getElementById('labelEpsPlanes').textContent = epsNombre;
    document.getElementById('infoBarPlanes').innerHTML =
        `<i class="fa fa-info-circle me-1"></i> Contrato <strong>${epsNombre}</strong>`;
    document.getElementById('idContratoPlan').value = idContrato;
    bootstrap.Tab.getOrCreateInstance(document.getElementById('tab-planes-link')).show();
    cargarPlanes(idContrato);
}

function cargarPlanes(idContrato, search = '') {
    fetch("{{ route('contratosEps.listarPlanes') }}", {
        method: 'POST', headers,
        body: JSON.stringify({ idContrato, search })
    })
    .then(r => r.json())
    .then(data => { document.getElementById('trPlanes').innerHTML = data.html; });
}

function nuevoPlan() {
    if (!activeContratoId) { Swal.fire('Info', 'Primero selecciona un contrato.', 'info'); return; }
    document.getElementById('tituloModalPlan').textContent = 'Agregar plan';
    document.getElementById('accionPlan').value    = 'guardar';
    document.getElementById('idPlanEdit').value    = '';
    document.getElementById('idContratoPlan').value = activeContratoId;
    document.getElementById('nombrePlan').value    = '';
    document.getElementById('descripcionPlan').value = '';
    document.getElementById('limitePlan').value    = '';
    document.getElementById('periodoPlan').value   = 'anual';
    document.getElementById('estadoPlan').value    = 'activo';
    new bootstrap.Modal(document.getElementById('modalPlan')).show();
}

function editarPlan(id, nombre, descripcion, limite, periodo, estado) {
    document.getElementById('tituloModalPlan').textContent = 'Editar plan';
    document.getElementById('accionPlan').value      = 'editar';
    document.getElementById('idPlanEdit').value      = id;
    document.getElementById('nombrePlan').value      = nombre;
    document.getElementById('descripcionPlan').value = descripcion;
    document.getElementById('limitePlan').value      = limite;
    document.getElementById('periodoPlan').value     = periodo;
    document.getElementById('estadoPlan').value      = estado;
    new bootstrap.Modal(document.getElementById('modalPlan')).show();
}

function guardarPlan() {
    const body = {
        accion:           document.getElementById('accionPlan').value,
        idPlan:           document.getElementById('idPlanEdit').value,
        id_contrato:      document.getElementById('idContratoPlan').value,
        nombre:           document.getElementById('nombrePlan').value,
        descripcion:      document.getElementById('descripcionPlan').value,
        limite_consultas: document.getElementById('limitePlan').value,
        periodo:          document.getElementById('periodoPlan').value,
        estado:           document.getElementById('estadoPlan').value,
    };
    if (!body.nombre) { Swal.fire('Error', 'El nombre es obligatorio.', 'warning'); return; }

    fetch("{{ route('contratosEps.guardarPlan') }}", {
        method: 'POST', headers, body: JSON.stringify(body)
    })
    .then(r => r.json())
    .then(data => {
        bootstrap.Modal.getInstance(document.getElementById('modalPlan')).hide();
        Swal.fire('¡Listo!', data.message, 'success');
        cargarPlanes(activeContratoId);
    });
}

function eliminarPlan(id) {
    Swal.fire({
        title: '¿Eliminar plan?', icon: 'warning',
        showCancelButton: true, confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar'
    }).then(result => {
        if (result.isConfirmed) {
            fetch("{{ route('contratosEps.eliminarPlan') }}", {
                method: 'POST', headers, body: JSON.stringify({ idPlan: id })
            })
            .then(r => r.json())
            .then(() => { Swal.fire('Eliminado', '', 'success'); cargarPlanes(activeContratoId); });
        }
    });
}

// ── COPAGOS ───────────────────────────────────────────────
function verCopagos(idPlan, planNombre) {
    activePlanId = idPlan;
    document.getElementById('labelPlanCopagos').textContent = planNombre;
    document.getElementById('infoBarCopagos').innerHTML =
        `<i class="fa fa-info-circle me-1"></i> Plan <strong>${planNombre}</strong> · Copagos por tipo de servicio`;
    document.getElementById('idPlanCopago').value = idPlan;
    bootstrap.Tab.getOrCreateInstance(document.getElementById('tab-copagos-link')).show();
    cargarCopagos(idPlan);
}

function cargarCopagos(idPlan) {
    fetch("{{ route('contratosEps.listarCopagos') }}", {
        method: 'POST', headers, body: JSON.stringify({ idPlan })
    })
    .then(r => r.json())
    .then(data => { document.getElementById('trCopagos').innerHTML = data.html; });
}

function nuevoCopago() {
    if (!activePlanId) { Swal.fire('Info', 'Primero selecciona un plan.', 'info'); return; }
    document.getElementById('tituloModalCopago').textContent = 'Agregar servicio';
    document.getElementById('accionCopago').value  = 'guardar';
    document.getElementById('idCopagoEdit').value  = '';
    document.getElementById('idPlanCopago').value  = activePlanId;
    document.getElementById('tipoServicioCopago').value = '';
    document.getElementById('montoCopago').value   = '';
    document.getElementById('maxSesionesCopago').value  = '';
    new bootstrap.Modal(document.getElementById('modalCopago')).show();
}

function editarCopago(id, tipoServicio, monto, max) {
    document.getElementById('tituloModalCopago').textContent  = 'Editar servicio';
    document.getElementById('accionCopago').value             = 'editar';
    document.getElementById('idCopagoEdit').value             = id;
    document.getElementById('tipoServicioCopago').value       = tipoServicio;
    document.getElementById('montoCopago').value              = monto;
    document.getElementById('maxSesionesCopago').value        = max;
    new bootstrap.Modal(document.getElementById('modalCopago')).show();
}

function guardarCopago() {
    const body = {
        accion:        document.getElementById('accionCopago').value,
        idCopago:      document.getElementById('idCopagoEdit').value,
        id_plan:       document.getElementById('idPlanCopago').value,
        tipo_servicio: document.getElementById('tipoServicioCopago').value,
        monto_copago:  document.getElementById('montoCopago').value,
        max_sesiones:  document.getElementById('maxSesionesCopago').value,
    };
    if (!body.tipo_servicio || !body.monto_copago) {
        Swal.fire('Error', 'Tipo de servicio y copago son obligatorios.', 'warning'); return;
    }
    fetch("{{ route('contratosEps.guardarCopago') }}", {
        method: 'POST', headers, body: JSON.stringify(body)
    })
    .then(r => r.json())
    .then(data => {
        bootstrap.Modal.getInstance(document.getElementById('modalCopago')).hide();
        Swal.fire('¡Listo!', data.message, 'success');
        cargarCopagos(activePlanId);
    });
}

function eliminarCopago(id) {
    Swal.fire({
        title: '¿Eliminar servicio?', icon: 'warning',
        showCancelButton: true, confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar'
    }).then(result => {
        if (result.isConfirmed) {
            fetch("{{ route('contratosEps.eliminarCopago') }}", {
                method: 'POST', headers, body: JSON.stringify({ idCopago: id })
            })
            .then(r => r.json())
            .then(() => { Swal.fire('Eliminado', '', 'success'); cargarCopagos(activePlanId); });
        }
    });
}
</script>
@endsection
```

- [ ] **Step 2: Probar en navegador — ir a `/Administracion/ContratosEps` (con sesión activa y permiso `AdminContratos`)**

Verificar:
- Los 3 tabs se renderizan.
- Tab Contratos carga (puede estar vacía si no hay datos).
- Botón `+ Nuevo Contrato` abre el modal con el select de EPS poblado.
- Guardar un contrato lo muestra en la tabla.
- `Ver planes` activa Tab 2 con el nombre de la EPS en el título.
- `Ver copagos` desde Tab 2 activa Tab 3.

- [ ] **Step 3: Commit**

```bash
git add resources/views/Adminitraccion/gestionarContratosEps.blade.php
git commit -m "feat: add gestionarContratosEps view with 3-tab UI"
```

---

### Task 5: PacientesController — endpoints de cobertura EPS

**Files:**
- Modify: `app/Http/Controllers/PacientesController.php`

- [ ] **Step 1: Agregar los 5 métodos al final del controlador (antes del cierre `}`)**

```php
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
            ->join('entidades',     'entidades.id',     '=', 'contratos_eps.id_eps')
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
                'entidades.nombre as eps_nombre'
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
            ->orderBy('inicio', 'desc')
            ->select('id', 'inicio', 'motivo', 'numero_autorizacion', 'copago_cobrado', 'estado')
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
```

- [ ] **Step 2: Verificar sintaxis**

```bash
php -l app/Http/Controllers/PacientesController.php
```
Esperado: `No syntax errors detected`

- [ ] **Step 3: Agregar las 5 rutas en web.php** (dentro del grupo `auth`, junto a las demás rutas de pacientes):

```php
    Route::post('/pacientes/guardarPlanEps',        [PacientesController::class, 'guardarPlanPaciente'])->name('pacientes.guardarPlanEps');
    Route::post('/pacientes/quitarPlanEps',         [PacientesController::class, 'quitarPlanPaciente'])->name('pacientes.quitarPlanEps');
    Route::post('/pacientes/obtenerCobertura',      [PacientesController::class, 'obtenerCoberturaPaciente'])->name('pacientes.obtenerCobertura');
    Route::post('/pacientes/listarAutorizaciones',  [PacientesController::class, 'listarAutorizaciones'])->name('pacientes.listarAutorizaciones');
    Route::post('/pacientes/registrarAutorizacion', [PacientesController::class, 'registrarAutorizacion'])->name('pacientes.registrarAutorizacion');
```

- [ ] **Step 4: Verificar rutas**

```bash
php artisan route:list --name=pacientes.guardarPlanEps
```
Esperado: la ruta aparece en la lista.

- [ ] **Step 5: Correr test de autenticación**

```bash
php artisan test tests/Feature/ContratosEpsTest.php --filter=test_guardar_plan_paciente_requires_auth
```
Esperado: PASS.

- [ ] **Step 6: Commit**

```bash
git add app/Http/Controllers/PacientesController.php routes/web.php
git commit -m "feat: add PacientesController EPS coverage endpoints"
```

---

### Task 6: Tab Cobertura EPS en gestionarPacientes

**Files:**
- Modify: `resources/views/Pacientes/gestionarPacientes.blade.php`

- [ ] **Step 1: Agregar la tercera pestaña en las nav-pills**

Localizar en el archivo (alrededor de la línea 406-413) el bloque:
```html
<li class="nav-item">
    <a href="#anexos" data-bs-toggle="tab" aria-expanded="false"
        class="nav-link rounded-0 ">
        <span class="d-none d-md-block"><i class="mdi mdi-file-multiple me-1"></i>
            Anexos</span>
    </a>
</li>
```

Agregar **después** de ese `</li>`:
```html
<li class="nav-item">
    <a href="#cobertura-eps" data-bs-toggle="tab" aria-expanded="false"
        class="nav-link rounded-0" id="tabCoberturaEps"
        onclick="cargarCoberturaEps()">
        <span class="d-none d-md-block">
            <i class="fa fa-shield-halved me-1"></i> Cobertura EPS
        </span>
    </a>
</li>
```

- [ ] **Step 2: Agregar el contenido del tab después del bloque `#anexos`**

Localizar el cierre `</div>` del tab `#anexos` (alrededor de la línea 491) y agregar **después**:

```html
{{-- TAB COBERTURA EPS --}}
<div class="tab-pane" id="cobertura-eps">

    {{-- Panel: tiene plan activo --}}
    <div id="epsPlanActivo" style="display:none;">
        <div id="epsPlanInfo" class="mb-3"
            style="background:#f0f0fa;border-radius:6px;padding:12px 16px;font-size:13px;color:#2b2b6c;">
        </div>
        <div class="d-flex justify-content-between align-items-center mb-2">
            <strong style="font-size:12px;">Historial de autorizaciones</strong>
            <div>
                <button class="btn btn-xs btn-primary-light me-1" onclick="mostrarFormPlan()">
                    <i class="fa fa-exchange-alt me-1"></i> Cambiar plan
                </button>
                <button class="btn btn-xs" style="background:#fde8e8;color:#c0392b;border:none;"
                    onclick="quitarPlanEps()">
                    <i class="fa fa-times me-1"></i> Quitar
                </button>
            </div>
        </div>
        <table class="table table-striped table-sm" style="font-size:12px;">
            <thead>
                <tr>
                    <th>Fecha cita</th><th>Tipo servicio</th>
                    <th>N° Autorización</th><th>Copago cobrado</th><th>Acción</th>
                </tr>
            </thead>
            <tbody id="trAutorizaciones"></tbody>
        </table>
    </div>

    {{-- Panel: sin plan --}}
    <div id="epsSinPlan" style="display:none;text-align:center;padding:28px 20px;color:#aaa;">
        <i class="fa fa-shield-halved" style="font-size:32px;opacity:.3;display:block;margin-bottom:8px;"></i>
        <p style="font-size:13px;margin:0 0 12px;">
            Este paciente no tiene un plan EPS asignado. Se atenderá como <strong>particular</strong>.
        </p>
        <button class="btn btn-xs btn-primary" onclick="mostrarFormPlan()">
            <i class="fa fa-plus me-1"></i> Asignar plan
        </button>
    </div>

    {{-- Formulario asignar/cambiar plan --}}
    <div id="epsFormPlan" style="display:none;">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">Plan / Póliza <span class="text-danger">*</span></label>
                    <select class="form-control" id="selectPlanEps">
                        <option value="">Seleccionar plan…</option>
                    </select>
                    <small class="text-muted" id="hintPlanesEps"></small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label class="form-label">N° Póliza / Afiliado</label>
                    <input type="text" class="form-control" id="numeroPolizaEps" placeholder="Opcional">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label class="form-label">Fecha vinculación</label>
                    <input type="date" class="form-control" id="fechaVinculacionEps">
                </div>
            </div>
            <div class="col-12">
                <button class="btn btn-xs btn-primary me-1" onclick="guardarPlanEps()">
                    <i class="fa fa-save me-1"></i> Guardar plan
                </button>
                <button class="btn btn-xs btn-primary-light" onclick="cancelarFormPlan()">
                    Cancelar
                </button>
            </div>
        </div>
    </div>

    {{-- Modal editar autorización --}}
    <div class="modal fade" id="modalAutorizacion" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar autorización</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="idCitaAutorizacion">
                    <div class="form-group">
                        <label class="form-label">N° Autorización EPS</label>
                        <input type="text" class="form-control" id="numAutorizacionInput"
                            placeholder="Ej: AUT-2025-00341">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Copago cobrado (COP)</label>
                        <input type="number" class="form-control" id="copagoCobradoInput"
                            placeholder="Ej: 47400" min="0" step="100">
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary-light btn-sm" data-bs-dismiss="modal">Cancelar</button>
                    <button class="btn btn-primary btn-sm" onclick="guardarAutorizacion()">
                        <i class="fa fa-save me-1"></i> Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>{{-- /cobertura-eps --}}
```

- [ ] **Step 3: Agregar el bloque JavaScript al final del archivo (antes de `@endsection`)**

```html
<script>
// ── COBERTURA EPS ─────────────────────────────────────────────
const CSRF_EPS = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const hdrsEPS  = { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_EPS };

function cargarCoberturaEps() {
    const idPaciente = document.getElementById('idPaciente').value;
    if (!idPaciente) return;

    fetch("{{ route('pacientes.obtenerCobertura') }}", {
        method: 'POST', headers: hdrsEPS,
        body: JSON.stringify({ idPaciente })
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('epsPlanActivo').style.display = 'none';
        document.getElementById('epsSinPlan').style.display    = 'none';
        document.getElementById('epsFormPlan').style.display   = 'none';

        if (data.tiene_plan) {
            const a = data.asignacion;
            const copagosHtml = data.copagos.map(c =>
                `<span style="margin-right:12px;"><strong style="color:#1a8a3a;">$${Number(c.monto_copago).toLocaleString('es-CO')}</strong> ${c.tipo_servicio}</span>`
            ).join('');
            document.getElementById('epsPlanInfo').innerHTML = `
                <strong>${a.eps_nombre}</strong> · ${a.plan_nombre}<br>
                <span style="font-size:11px;">
                    ${a.limite_consultas ? a.limite_consultas + ' consultas' : 'Ilimitadas'}
                    &nbsp;|&nbsp; ${copagosHtml}
                </span>`;
            document.getElementById('epsPlanActivo').style.display = 'block';
            cargarAutorizaciones(idPaciente);
        } else {
            // Cargar planes disponibles para la EPS del paciente
            const idEps = document.getElementById('eps').value;
            if (idEps) cargarPlanesEps(idEps);
            document.getElementById('epsSinPlan').style.display = 'block';
        }
    });
}

function cargarAutorizaciones(idPaciente) {
    fetch("{{ route('pacientes.listarAutorizaciones') }}", {
        method: 'POST', headers: hdrsEPS,
        body: JSON.stringify({ idPaciente })
    })
    .then(r => r.json())
    .then(data => { document.getElementById('trAutorizaciones').innerHTML = data.html; });
}

function cargarPlanesEps(idEps) {
    fetch("{{ route('contratosEps.planesPorEps') }}", {
        method: 'POST', headers: hdrsEPS,
        body: JSON.stringify({ idEps })
    })
    .then(r => r.json())
    .then(data => {
        const sel = document.getElementById('selectPlanEps');
        sel.innerHTML = '<option value="">Seleccionar plan…</option>';
        if (data.planes.length === 0) {
            document.getElementById('hintPlanesEps').textContent =
                'No hay contratos activos para esta EPS.';
        } else {
            data.planes.forEach(p => {
                const limite = p.limite_consultas ? p.limite_consultas + ' consultas' : 'Ilimitada';
                sel.innerHTML += `<option value="${p.id}">${p.nombre} — ${limite}</option>`;
            });
            document.getElementById('hintPlanesEps').textContent = '';
        }
    });
}

function mostrarFormPlan() {
    const idEps = document.getElementById('eps').value;
    if (!idEps) { Swal.fire('Info', 'Primero selecciona la EPS del paciente.', 'info'); return; }
    cargarPlanesEps(idEps);
    document.getElementById('epsPlanActivo').style.display = 'none';
    document.getElementById('epsSinPlan').style.display    = 'none';
    document.getElementById('epsFormPlan').style.display   = 'block';
}

function cancelarFormPlan() {
    document.getElementById('epsFormPlan').style.display = 'none';
    cargarCoberturaEps();
}

function guardarPlanEps() {
    const idPaciente = document.getElementById('idPaciente').value;
    const idPlan     = document.getElementById('selectPlanEps').value;
    if (!idPlan) { Swal.fire('Error', 'Selecciona un plan.', 'warning'); return; }

    fetch("{{ route('pacientes.guardarPlanEps') }}", {
        method: 'POST', headers: hdrsEPS,
        body: JSON.stringify({
            idPaciente,
            idPlan,
            numeroPoliza:     document.getElementById('numeroPolizaEps').value,
            fechaVinculacion: document.getElementById('fechaVinculacionEps').value,
        })
    })
    .then(r => r.json())
    .then(data => {
        Swal.fire('¡Listo!', data.message, 'success');
        cargarCoberturaEps();
    });
}

function quitarPlanEps() {
    const idPaciente = document.getElementById('idPaciente').value;
    Swal.fire({
        title: '¿Quitar plan EPS?', icon: 'warning',
        showCancelButton: true, confirmButtonText: 'Sí, quitar', cancelButtonText: 'Cancelar'
    }).then(result => {
        if (result.isConfirmed) {
            fetch("{{ route('pacientes.quitarPlanEps') }}", {
                method: 'POST', headers: hdrsEPS,
                body: JSON.stringify({ idPaciente })
            })
            .then(r => r.json())
            .then(() => { Swal.fire('Quitado', '', 'success'); cargarCoberturaEps(); });
        }
    });
}

function editarAutorizacion(idCita, numAut, copago) {
    document.getElementById('idCitaAutorizacion').value    = idCita;
    document.getElementById('numAutorizacionInput').value  = numAut !== 'null' ? numAut : '';
    document.getElementById('copagoCobradoInput').value    = copago !== 'null' ? copago : '';
    new bootstrap.Modal(document.getElementById('modalAutorizacion')).show();
}

function guardarAutorizacion() {
    fetch("{{ route('pacientes.registrarAutorizacion') }}", {
        method: 'POST', headers: hdrsEPS,
        body: JSON.stringify({
            idCita:             document.getElementById('idCitaAutorizacion').value,
            numeroAutorizacion: document.getElementById('numAutorizacionInput').value,
            copagoCobrado:      document.getElementById('copagoCobradoInput').value,
        })
    })
    .then(r => r.json())
    .then(data => {
        bootstrap.Modal.getInstance(document.getElementById('modalAutorizacion')).hide();
        Swal.fire('¡Listo!', data.message, 'success');
        cargarCoberturaEps();
    });
}
</script>
```

- [ ] **Step 4: Verificar sintaxis PHP/Blade**

```bash
php artisan view:clear
php artisan route:cache
```
Esperado: sin errores.

- [ ] **Step 5: Probar en navegador — abrir modal de un paciente y hacer clic en pestaña "Cobertura EPS"**

Verificar:
- La pestaña aparece correctamente junto a `Información acompañante` y `Anexos`.
- Si el paciente tiene EPS asignada en el campo `Entidad promotora`, al entrar al tab muestra el estado vacío con botón "Asignar plan".
- "Asignar plan" → carga el select de planes filtrado por la EPS del paciente.
- Guardar plan → muestra el panel de info del plan + tabla de autorizaciones.
- Clic en "Registrar" en una cita → abre modal, guarda número de autorización y copago.

- [ ] **Step 6: Correr todos los tests**

```bash
php artisan test tests/Feature/ContratosEpsTest.php
```
Esperado: todos los tests PASS.

- [ ] **Step 7: Commit final**

```bash
git add resources/views/Pacientes/gestionarPacientes.blade.php
git commit -m "feat: add Cobertura EPS tab to gestionarPacientes modal"
```
