# Refactor AdminitraccionController Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Dividir `AdminitraccionController` (3742 líneas, 101 conexiones, 13 comunidades) en 6 controladores con responsabilidad única, sin cambiar ninguna lógica ni URL existente.

**Architecture:** Extracción pura de métodos — cada método se mueve a su nuevo controlador, se actualiza `routes/web.php` para apuntar al nuevo controlador, y se elimina del original. Sin cambios de comportamiento. Sin renombrar rutas. La aplicación debe seguir funcionando idéntica después de cada tarea.

**Tech Stack:** Laravel 8/PHP 8.2, `Illuminate\Http\Request`, `Illuminate\Support\Facades\Auth`, `Illuminate\Support\Facades\DB`, DomPDF (`\PDF`), Eloquent Models existentes.

---

## Mapa de archivos

| Acción | Archivo | Responsabilidad |
|---|---|---|
| Crear | `app/Http/Controllers/ProfesionalController.php` | Gestión de profesionales |
| Crear | `app/Http/Controllers/CatalogoController.php` | CUPS, CIE10, Entidades, Especialidades, Componentes |
| Crear | `app/Http/Controllers/ServicioController.php` | Pruebas, Sesiones, Paquetes, Asesorías |
| Crear | `app/Http/Controllers/CajaController.php` | Cajas y Gastos |
| Crear | `app/Http/Controllers/RecaudosController.php` | Ventas, Pagos, Recaudos, Impresión |
| Crear | `app/Http/Controllers/SistemaController.php` | Backup, Usuarios, Perfiles, Logs |
| Modificar | `app/Http/Controllers/AdminitraccionController.php` | Eliminar métodos migrados en cada tarea |
| Modificar | `routes/web.php` | Actualizar `use` y referencias de controlador |

**Regla de oro:** nunca modificar la lógica interna de ningún método — solo moverlo.

---

## Tarea 1: ProfesionalController

Métodos a mover desde `AdminitraccionController`:
- `Profesionales()` — L958
- `listaProfesionales(Request $request)` — L2746
- `guardarProfesional(Request $request)` — L2638 *(usa `self::sanear_string()` — copiar también)*
- `busquedaProfesional(Request $request)` — L2955
- `eliminarProfesional()` — L3462
- `verificarIdentProfesional(Request $request)` — L2820
- `cargarListaProf()` — L2802
- `sanear_string($string)` — L3523 *(helper privado, solo lo usa `guardarProfesional`)*

**Files:**
- Create: `app/Http/Controllers/ProfesionalController.php`
- Modify: `app/Http/Controllers/AdminitraccionController.php` (eliminar los 8 métodos)
- Modify: `routes/web.php` (actualizar 7 rutas)

- [ ] **Paso 1: Crear el nuevo controlador**

Crear `app/Http/Controllers/ProfesionalController.php` con el siguiente esqueleto, luego **cortar** los 8 métodos de `AdminitraccionController` y **pegarlos** dentro de la clase:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Profesional;
use App\Models\Especialidades;
use \PDF;

class ProfesionalController extends Controller
{
    // Pegar aquí los métodos:
    // Profesionales(), listaProfesionales(), guardarProfesional(),
    // busquedaProfesional(), eliminarProfesional(),
    // verificarIdentProfesional(), cargarListaProf()
    // sanear_string() — cambiar visibilidad a private
}
```

En `sanear_string`, cambiar `public function` → `private function`.
En `guardarProfesional`, la llamada `self::sanear_string(...)` no cambia.

- [ ] **Paso 2: Actualizar rutas**

En `routes/web.php`, agregar al bloque de `use` al inicio del archivo:
```php
use App\Http\Controllers\ProfesionalController;
```

Luego reemplazar `AdminitraccionController::class` → `ProfesionalController::class` en estas 7 rutas:
```
/Administracion/Profesionales           (GET)
/profesionales/listaProfesionales       (POST)
/verificar-identificacion-profesional   (POST)
/profesional/guardar                    (POST)
/profesional/buscaProfesional           (POST)
/profesional/eliminarProf               (POST)
profesionales/cargarListaProf           (GET)
```

- [ ] **Paso 3: Eliminar métodos de AdminitraccionController**

Borrar de `AdminitraccionController.php` los 8 métodos movidos (L958–L971, L2638–L2686, L2746–L2801, L2802–L2819, L2820–L2829, L2955–L2961, L3462–L3522, L3523–L3608).

También eliminar del bloque `use` de `AdminitraccionController` las líneas que ya no se necesiten en ese archivo (verificar que `Profesional` y `Especialidades` sigan usándose en otros métodos antes de eliminarlos).

- [ ] **Paso 4: Verificar que las rutas responden**

```bash
php artisan route:list --name=profesionales
```

Resultado esperado: todas las rutas de profesionales listadas con `ProfesionalController`.

Abrir en el navegador: `http://localhost/Administracion/Profesionales` — debe cargar la vista sin error 500.

- [ ] **Paso 5: Commit**

```bash
git add app/Http/Controllers/ProfesionalController.php \
        app/Http/Controllers/AdminitraccionController.php \
        routes/web.php
git commit -m "refactor: extraer ProfesionalController de AdminitraccionController"
```

---

## Tarea 2: CatalogoController

Métodos a mover (catálogos médicos estáticos):

**Especialidades:** `Especialidades()` L28, `listaEspecialidades()` L2687, `guardarEspecialidad()` L2293, `busquedaEspecialidad()` L2847, `eliminarEspecialidad()` L2962, `cargarListaEsp()` L2810

**CUPS:** `CUPS()` L53, `listaCUPS()` L230, `guardarCUPS()` L445, `buscaCUPS()` L207, `eliminarCUPS()` L507, `verificarCodigoCUPS()` L166

**CIE10:** `CIE10()` L115, `listaCIE10()` L378, `guardarCIE10()` L476, `buscaCIE10()` L218, `eliminarCIE10()` L521, `verificarCodigoCIE10()` L194

**Entidades:** `Entidades()` L589, `listaEntidades()` L1057, `guardarEntidades()` L2350, `buscaEntidad()` L2853, `eliminarEntidad()` L3011, `verificarCodigoEntidad()` L2830

**Componentes:** `Componentes()` L579, `listaComponentes()` L1232, `guardarComponente()` L2406, `buscarComponente()` L101, `eliminarComponente()` L3112, `listaCategoriasSelect()` L86

**Files:**
- Create: `app/Http/Controllers/CatalogoController.php`
- Modify: `app/Http/Controllers/AdminitraccionController.php`
- Modify: `routes/web.php`

- [ ] **Paso 1: Crear el controlador**

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Especialidades;
use App\Models\Entidades;
use App\Models\CUPS;
use App\Models\CIE10;
use App\Models\Componentes;

class CatalogoController extends Controller
{
    // Pegar aquí los 29 métodos listados arriba
}
```

- [ ] **Paso 2: Actualizar rutas**

Agregar en `routes/web.php`:
```php
use App\Http\Controllers\CatalogoController;
```

Reemplazar `AdminitraccionController::class` → `CatalogoController::class` en estas rutas:
```
/Administracion/Especialidades, /especialidad/*       (6 rutas)
/Administracion/CodigosConsultas, /cups/*             (6 rutas)
/Administracion/CodigosDiagnosticos, /cie10/*         (6 rutas)
/Administracion/Entidades, /entidades/*               (6 rutas)
/Administracion/Componentes, /componentes/*           (6 rutas)
/especialidad/cargarListaEsp                          (1 ruta)
```
Total: 31 rutas.

- [ ] **Paso 3: Eliminar métodos de AdminitraccionController**

Borrar los 29 métodos del original. Verificar que los `use` de `Especialidades`, `Entidades`, `CUPS`, `CIE10`, `Componentes` ya no sean necesarios en `AdminitraccionController` antes de eliminarlos (algunos modelos pueden seguir usándose en otros métodos).

- [ ] **Paso 4: Verificar rutas**

```bash
php artisan route:list | grep CatalogoController
```

Abrir en el navegador:
- `http://localhost/Administracion/Especialidades`
- `http://localhost/Administracion/CodigosConsultas`
- `http://localhost/Administracion/CodigosDiagnosticos`
- `http://localhost/Administracion/Entidades`
- `http://localhost/Administracion/Componentes`

Cada una debe cargar sin error 500.

- [ ] **Paso 5: Commit**

```bash
git add app/Http/Controllers/CatalogoController.php \
        app/Http/Controllers/AdminitraccionController.php \
        routes/web.php
git commit -m "refactor: extraer CatalogoController (CUPS, CIE10, Entidades, Especialidades, Componentes)"
```

---

## Tarea 3: ServicioController

Métodos a mover (catálogo de servicios clínicos):

**Pruebas:** `Pruebas()` L543, `listaPruebas()` L599, `guardarPrueba()` L2463, `buscarPrueba()` L2934, `eliminarPrueba()` L3211

**Sesiones:** `Sesiones()` L535, `listaSesiones()` L658, `guardarSesion()` L2491, `buscarSesion()` L2941, `eliminarSesion()` L3262

**Paquetes:** `Paquetes()` L949, `listaPaquetes()` L1116, `guardarPaquete()` L2378, `buscarPaquete()` L2922, `eliminarPaquete()` L3062

**Asesorías:** `Asesorias()` L158, `AsesoriasList()` L95, `listaAsesorias()` L1173, `guardarAsesoria()` L2434, `buscarAsesoria()` L2928, `eliminarAsesoria()` L3160, `guardarVentaAsesoria()` L179, `eliminarVentaAsesoria()` L38, `buscaVentaAsesoria()` L108, `listaServiciosVenta()` L125

**Files:**
- Create: `app/Http/Controllers/ServicioController.php`
- Modify: `app/Http/Controllers/AdminitraccionController.php`
- Modify: `routes/web.php`

- [ ] **Paso 1: Crear el controlador**

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Pruebas;
use App\Models\Sesiones;
use App\Models\Paquetes;
use App\Models\Asesorias;
use App\Models\Servicios;

class ServicioController extends Controller
{
    // Pegar aquí los 25 métodos listados arriba
}
```

- [ ] **Paso 2: Actualizar rutas**

Agregar en `routes/web.php`:
```php
use App\Http\Controllers\ServicioController;
```

Reemplazar `AdminitraccionController::class` → `ServicioController::class` en estas rutas:
```
/Administracion/Pruebas, /pruebas/*         (5 rutas)
/Administracion/Sesiones, /sesiones/*       (5 rutas)
/Administracion/Paquetes, /paquetes/*       (5 rutas)
/Administracion/Asesorias, /asesorias/*     (10 rutas)
```
Total: 25 rutas.

- [ ] **Paso 3: Eliminar métodos de AdminitraccionController**

Borrar los 25 métodos del original.

- [ ] **Paso 4: Verificar rutas**

```bash
php artisan route:list | grep ServicioController
```

Abrir en el navegador:
- `http://localhost/Administracion/Pruebas`
- `http://localhost/Administracion/Sesiones`
- `http://localhost/Administracion/Paquetes`
- `http://localhost/Administracion/Asesorias`

- [ ] **Paso 5: Commit**

```bash
git add app/Http/Controllers/ServicioController.php \
        app/Http/Controllers/AdminitraccionController.php \
        routes/web.php
git commit -m "refactor: extraer ServicioController (Pruebas, Sesiones, Paquetes, Asesorias)"
```

---

## Tarea 4: CajaController

Métodos a mover:

**Cajas:** `Cajas()` L569, `listaCajas()` L1405, `guardarCaja()` L2549, `detalleCaja()` L2578, `cerrarCaja()` L718 *(sin modificador `public` — agregar `public` al moverlo)*, `eliminarCaja()` L3412, `consultarMontoCierre()` L61

**Gastos:** `Gastos()` L560, `listaGastos()` L1310, `guardarGastos()` L2520, `buscarGasto()` L2948, `eliminarGasto()` L3363, `guardarCategoria()` L2609, `eliminarCategoria()` L3313, `listaCategorias()` L1291 *(sin modificador `public` — agregar `public` al moverlo)*

**Files:**
- Create: `app/Http/Controllers/CajaController.php`
- Modify: `app/Http/Controllers/AdminitraccionController.php`
- Modify: `routes/web.php`

- [ ] **Paso 1: Crear el controlador**

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Gastos;

class CajaController extends Controller
{
    // Pegar aquí los 15 métodos
    // IMPORTANTE: añadir "public" a cerrarCaja() y listaCategorias()
}
```

Al mover `cerrarCaja()` y `listaCategorias()`, cambiar:
```php
function cerrarCaja()          →   public function cerrarCaja()
function listaCategorias()     →   public function listaCategorias()
```

- [ ] **Paso 2: Actualizar rutas**

Agregar en `routes/web.php`:
```php
use App\Http\Controllers\CajaController;
```

Reemplazar `AdminitraccionController::class` → `CajaController::class` en estas rutas:
```
/Administracion/Cajas, /cajas/*          (7 rutas)
/Administracion/Gastos, /gastos/*        (8 rutas)
```
Total: 15 rutas.

- [ ] **Paso 3: Eliminar métodos de AdminitraccionController**

Borrar los 15 métodos del original.

- [ ] **Paso 4: Verificar rutas**

```bash
php artisan route:list | grep CajaController
```

Abrir:
- `http://localhost/Administracion/Cajas`
- `http://localhost/Administracion/Gastos`

- [ ] **Paso 5: Commit**

```bash
git add app/Http/Controllers/CajaController.php \
        app/Http/Controllers/AdminitraccionController.php \
        routes/web.php
git commit -m "refactor: extraer CajaController (Cajas y Gastos)"
```

---

## Tarea 5: RecaudosController

Métodos a mover:

`Recaudos()` L551, `listaVentasPacientes()` L1522, `listaVentasEps()` L1663, `listaVentasPacientesPagos()` L2036, `listaVentasPacientesPagosEps()` L2154, `listaPagos()` L1781, `otraInformacionRecaudos()` L1913, `detalleVentaServicioPaciente()` L2860, `detalleVentaPagosPaciente()` L2897, `guardarPagoVenta()` L2321, `eliminarPagoRecaudo()` L968, `imprimirRecaudo()` L768 *(usa `self::addCeros()` — copiar también)*, `obtenerDatosPago()` L3609, `actualizarPagoRecaudo()` L3683, `addCeros()` L943 *(helper privado)*

**Files:**
- Create: `app/Http/Controllers/RecaudosController.php`
- Modify: `app/Http/Controllers/AdminitraccionController.php`
- Modify: `routes/web.php`

- [ ] **Paso 1: Crear el controlador**

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Pacientes;
use App\Models\Paquetes;
use App\Models\Asesorias;
use App\Models\Servicios;
use Dompdf\Dompdf;
use \PDF;

class RecaudosController extends Controller
{
    // Pegar aquí los 14 métodos + addCeros()
    // addCeros() → cambiar a private function addCeros()
}
```

En `addCeros()`, cambiar `public function` → `private function`.
La llamada `self::addCeros(...)` dentro de `imprimirRecaudo()` no cambia.

- [ ] **Paso 2: Actualizar rutas**

Agregar en `routes/web.php`:
```php
use App\Http\Controllers\RecaudosController;
```

Reemplazar `AdminitraccionController::class` → `RecaudosController::class` en estas rutas:
```
/Administracion/Recaudos                       (GET)
/Administracion/listaVentasPacientes           (POST)
/Administracion/otraInformacionRecaudos        (POST)
/Administracion/listaVentasPacientesPagos      (POST)
/Administracion/detalleVentaServicioPaciente   (POST)
/Administracion/detalleVentaPagosPaciente      (POST)
/Administracion/guardar                        (POST)
/Administracion/eliminarPagoRecaudo            (POST)
/Administracion/imprimirRecaudo                (POST)
/Administracion/listaPagos                     (POST)
/Administracion/obtenerDatosPago               (POST)
/Administracion/actualizarPagoRecaudo          (POST)
/Administracion/listaVentasEps                 (POST)
/Administracion/listaVentasEpsPagos            (POST)
/Administracion/listaVentasPacientesPagosEps   (POST)
```
Total: 15 rutas.

- [ ] **Paso 3: Eliminar métodos de AdminitraccionController**

Borrar los 15 métodos del original (incluido `addCeros`).

- [ ] **Paso 4: Verificar rutas**

```bash
php artisan route:list | grep RecaudosController
```

Abrir `http://localhost/Administracion/Recaudos` — debe cargar sin error.

- [ ] **Paso 5: Commit**

```bash
git add app/Http/Controllers/RecaudosController.php \
        app/Http/Controllers/AdminitraccionController.php \
        routes/web.php
git commit -m "refactor: extraer RecaudosController (ventas, pagos, recaudos, impresion)"
```

---

## Tarea 6: SistemaController

Métodos a mover (últimos métodos restantes en AdminitraccionController):

`Usuarios()` L2264, `Perfiles()` L2273, `Logs()` L2282, `Backup()` L148, `listaBackup()` L295, `verDetalleBackup()` L360

**Files:**
- Create: `app/Http/Controllers/SistemaController.php`
- Modify: `app/Http/Controllers/AdminitraccionController.php` (quedará vacío o con solo el esqueleto de clase)
- Modify: `routes/web.php`

- [ ] **Paso 1: Crear el controlador**

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SistemaController extends Controller
{
    // Pegar aquí: Usuarios(), Perfiles(), Logs(),
    //             Backup(), listaBackup(), verDetalleBackup()
}
```

- [ ] **Paso 2: Actualizar rutas**

Agregar en `routes/web.php`:
```php
use App\Http\Controllers\SistemaController;
```

Reemplazar `AdminitraccionController::class` → `SistemaController::class` en estas rutas:
```
/Administracion/Usuarios    (GET)
/Administracion/Perfiles    (GET)
/Administracion/Logs        (GET)
/Administracion/Backup      (GET)
/backup/listaBackup         (POST)
/backup/verDetalleBackup    (POST)
```

- [ ] **Paso 3: Vaciar AdminitraccionController**

Después de mover los 6 métodos restantes, `AdminitraccionController` solo tiene el esqueleto de clase. Eliminar todos los `use` de modelos que ya no se usen. El archivo puede eliminarse si no queda ninguna ruta apuntando a él:

```bash
php artisan route:list | grep AdminitraccionController
```

Si el resultado está vacío, eliminar el archivo:
```bash
rm app/Http/Controllers/AdminitraccionController.php
```

Y eliminar el `use` de `AdminitraccionController` de `routes/web.php`.

- [ ] **Paso 4: Verificar rutas**

```bash
php artisan route:list | grep SistemaController
php artisan route:list | grep AdminitraccionController  # debe estar vacío
```

Abrir:
- `http://localhost/Administracion/Usuarios`
- `http://localhost/Administracion/Backup`
- `http://localhost/Administracion/Logs`

- [ ] **Paso 5: Commit final**

```bash
git add app/Http/Controllers/SistemaController.php \
        app/Http/Controllers/AdminitraccionController.php \
        routes/web.php
git commit -m "refactor: extraer SistemaController y eliminar AdminitraccionController"
```

---

## Verificación final

- [ ] **Smoke test completo de navegación**

Abrir cada sección de administración en el navegador y confirmar que carga:
```
/Administracion/Profesionales
/Administracion/Especialidades
/Administracion/CodigosConsultas
/Administracion/CodigosDiagnosticos
/Administracion/Entidades
/Administracion/Componentes
/Administracion/Pruebas
/Administracion/Sesiones
/Administracion/Paquetes
/Administracion/Asesorias
/Administracion/Cajas
/Administracion/Gastos
/Administracion/Recaudos
/Administracion/Usuarios
/Administracion/Backup
/Administracion/Logs
```

- [ ] **Confirmar que no quedan rutas rotas**

```bash
php artisan route:list 2>&1 | grep -i "not found\|error"
```

Resultado esperado: sin salida.

- [ ] **Commit de limpieza de imports en web.php**

Verificar que el bloque `use` de `routes/web.php` no tenga imports no usados:
```bash
git diff HEAD routes/web.php
```

```bash
git add routes/web.php
git commit -m "refactor: limpiar imports no usados en routes/web.php"
```

---

## Notas de riesgo

- **`cerrarCaja()` y `listaCategorias()`** no tienen modificador `public` en el original (PHP las trata como public por defecto, pero es un code smell). Al moverlas, declarar explícitamente `public function`.
- **`listaVentasEpsPagos`** aparece en `routes/web.php` L365 pero no hay un método con ese nombre en el controlador — es un bug preexistente. No crear el método; dejar la ruta como estaba (apuntará a `RecaudosController` después de la Tarea 5).
- **`sanear_string()`** se usa solo en `guardarProfesional` → va a `ProfesionalController` como método privado.
- **`addCeros()`** se usa solo en `imprimirRecaudo` → va a `RecaudosController` como método privado.
