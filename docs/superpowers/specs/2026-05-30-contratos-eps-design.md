# Contratos EPS — Diseño del Módulo

## Objetivo

Implementar la gestión genérica de contratos EPS en PrascCenter: un módulo de administración para registrar contratos, planes y copagos de cualquier EPS; asignación de plan a cada paciente dentro de su ficha; y registro del número de autorización y copago cobrado por cita.

## Contexto del negocio

- El consultorio atiende pacientes de múltiples EPS y también pacientes particulares.
- Cada EPS puede tener un contrato activo con el consultorio. No todas las EPS tienen contrato (las que no tienen se manejan como particular).
- Un contrato agrupa uno o varios **planes/pólizas** (ej. Póliza Clásica, Póliza Global).
- Cada plan define límites de consultas y, para cada tipo de servicio cubierto, un **copago** que el paciente paga por consulta.
- Al agendar una cita para un paciente con plan EPS, el sistema debe mostrar el copago correspondiente y permitir registrar el número de autorización emitido por la EPS.

---

## Módulo 1 — Gestión de Contratos EPS (Administración)

### Ubicación

`Administración → Gestionar Contratos EPS`

Ruta: `/administracion/contratos-eps`

### UI: 3 pestañas (Bootstrap nav-tabs, estilo InvestX existente)

**Tab 1 — Contratos**
- Tabla: `#`, EPS, Vigencia Inicio, Vigencia Fin, Planes (badge con conteo), Estado (Activo / Borrador), Acción
- Acciones por fila: Ver planes (abre Tab 2 con ese contrato), Editar, Eliminar
- Toolbar: buscador + botón `+ Nuevo Contrato`
- Modal para crear/editar: EPS (select de tabla `entidades`), Vigencia inicio, Vigencia fin, Estado

**Tab 2 — Planes** (contextual al contrato seleccionado)
- Info-bar: nombre EPS · vigencia del contrato
- Tabla: `#`, Plan/Póliza, Descripción, Límite consultas, Período, Estado, Acción
- Acciones: Ver copagos (abre Tab 3), Editar, Eliminar
- Toolbar: buscador + botón `+ Nuevo Plan`
- Modal crear/editar: Nombre plan, Descripción, Límite consultas (número o "Ilimitada"), Período (Anual / Sin período), Estado

**Tab 3 — Copagos** (contextual al plan seleccionado)
- Info-bar: EPS · Plan · límite de consultas
- Tabla: `#`, Tipo de Servicio, Copago, Máx. sesiones, Acción
- Acciones: Editar, Eliminar
- Toolbar: botón `+ Agregar Servicio`
- Modal crear/editar: Tipo de servicio (texto libre), Monto copago, Máx. sesiones (número o "Ilimitada")

### Permiso requerido

`gestionContratos` — nuevo permiso a registrar en la tabla de perfiles.

---

## Módulo 2 — Cobertura EPS en ficha del paciente

### Ubicación

`Gestionar Pacientes → modal Agregar/Editar paciente → pestaña "Cobertura EPS"`

Se añade como **tercera pestaña** a las nav-pills existentes (`Información acompañante` | `Anexos` | **`Cobertura EPS`**).

### Lógica de la pestaña

1. El campo `Entidad promotora` (select `#eps`) ya existe en el formulario principal del modal.
2. La pestaña Cobertura EPS lee esa EPS y lista solo los planes del contrato **activo** de esa EPS.
3. Si el paciente ya tiene plan asignado se muestra:
   - Panel resumen: EPS, nombre del plan, vigencia, copago por tipo de servicio, límite de consultas
   - Tabla historial de autorizaciones (citas vinculadas): fecha, tipo servicio, N° autorización, copago cobrado, estado
   - Botones: Cambiar plan | Quitar plan
4. Si no tiene plan asignado: estado vacío + botón `+ Asignar plan`
5. Formulario de asignación/cambio (inline, aparece/oculta con JS):
   - Plan/Póliza (select filtrado por EPS del campo superior) — obligatorio
   - N° Póliza/Afiliado — opcional
   - Fecha de vinculación — opcional

El guardado del plan se hace con una petición AJAX independiente al guardar el paciente (no bloquea el formulario principal).

---

## Módulo 3 — Autorización en cita

### Campos nuevos en tabla `citas`

| Campo | Tipo | Null | Descripción |
|---|---|---|---|
| `numero_autorizacion` | VARCHAR(60) | NULL | N° emitido por la EPS |
| `copago_cobrado` | DECIMAL(10,2) | NULL | Copago real cobrado al paciente |

### Dónde se registra

Desde la pestaña **Cobertura EPS** del paciente (`paciente-cobertura-tab`), en la tabla de historial de autorizaciones, cada fila con N° de autorización pendiente tiene un botón de edición inline para registrar el número y confirmar el copago cobrado.

No se modifica el flujo de creación de citas (`inicio.blade.php`). El número de autorización se registra después, cuando la EPS lo emite.

---

## Modelo de datos

### Tablas nuevas

```sql
-- 1. Contratos EPS
CREATE TABLE contratos_eps (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_eps        INT UNSIGNED NOT NULL,            -- FK → entidades.id
    fecha_inicio  DATE NULL,
    fecha_fin     DATE NULL,
    estado        ENUM('activo','borrador') DEFAULT 'borrador',
    created_at    TIMESTAMP NULL,
    updated_at    TIMESTAMP NULL
);

-- 2. Planes del contrato
CREATE TABLE planes_eps (
    id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_contrato       INT UNSIGNED NOT NULL,        -- FK → contratos_eps.id
    nombre            VARCHAR(120) NOT NULL,
    descripcion       VARCHAR(255) NULL,
    limite_consultas  INT UNSIGNED NULL,            -- NULL = ilimitado
    periodo           ENUM('anual','sin_periodo') DEFAULT 'anual',
    estado            ENUM('activo','inactivo') DEFAULT 'activo',
    created_at        TIMESTAMP NULL,
    updated_at        TIMESTAMP NULL
);

-- 3. Copagos por tipo de servicio dentro del plan
CREATE TABLE copagos_eps (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_plan          INT UNSIGNED NOT NULL,         -- FK → planes_eps.id
    tipo_servicio    VARCHAR(120) NOT NULL,
    monto_copago     DECIMAL(10,2) NOT NULL,
    max_sesiones     INT UNSIGNED NULL,             -- NULL = ilimitado
    created_at       TIMESTAMP NULL,
    updated_at       TIMESTAMP NULL
);

-- 4. Plan asignado al paciente
CREATE TABLE paciente_planes_eps (
    id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_paciente       INT UNSIGNED NOT NULL,        -- FK → pacientes.id
    id_plan           INT UNSIGNED NOT NULL,        -- FK → planes_eps.id
    numero_poliza     VARCHAR(60) NULL,
    fecha_vinculacion DATE NULL,
    estado            ENUM('activo','inactivo') DEFAULT 'activo',
    created_at        TIMESTAMP NULL,
    updated_at        TIMESTAMP NULL
);
```

### Columnas nuevas en tabla existente

```sql
ALTER TABLE citas
    ADD COLUMN numero_autorizacion VARCHAR(60)   NULL AFTER comentario,
    ADD COLUMN copago_cobrado      DECIMAL(10,2) NULL AFTER numero_autorizacion;
```

---

## Nuevos archivos (controladores / vistas / migraciones)

| Archivo | Responsabilidad |
|---|---|
| `app/Http/Controllers/ContratosEpsController.php` | CRUD contratos, planes, copagos; endpoint para cargar planes por EPS (usado en ficha paciente) |
| `resources/views/Adminitraccion/gestionarContratosEps.blade.php` | Vista 3-tabs del módulo de administración |
| `database/migrations/2026_05_30_000001_create_contratos_eps_tables.php` | Crea las 4 tablas nuevas |
| `database/migrations/2026_05_30_000002_add_autorizacion_to_citas.php` | Agrega columnas a `citas` |

### Archivos modificados

| Archivo | Cambio |
|---|---|
| `routes/web.php` | Rutas CRUD del módulo + endpoint planes por EPS |
| `resources/views/Plantilla/Menu.blade.php` | Enlace al nuevo módulo bajo Administración |
| `resources/views/Pacientes/gestionarPacientes.blade.php` | Nueva pestaña Cobertura EPS + JS AJAX |
| `app/Models/Citas.php` | Incluir `numero_autorizacion` y `copago_cobrado` en `GuardarCitas` / `EditarCitas` |

---

## Flujo de datos (resumen)

```
Administración
  └─ ContratosEpsController
       ├─ contratos_eps  (EPS + vigencia)
       ├─ planes_eps     (plan + límite)
       └─ copagos_eps    (servicio + copago)

Gestionar Pacientes
  └─ PacientesController (modificado)
       └─ paciente_planes_eps  (asignación plan ↔ paciente)
            └─ citas.numero_autorizacion / copago_cobrado  (por cita)
```

---

## Permisos

Nuevo permiso `gestionContratos` a registrar en la tabla de perfiles del sistema. El módulo de administración solo es visible/accesible para usuarios con ese permiso. La pestaña Cobertura EPS en pacientes es accesible para cualquier usuario con permiso `gestionPacientes`.

---

## Lo que NO cubre este spec

- Radicación de facturas RIPS (fase futura).
- Integración directa con APIs de EPS.
- Reportes de consumo por EPS/plan.
- Facturación electrónica.
