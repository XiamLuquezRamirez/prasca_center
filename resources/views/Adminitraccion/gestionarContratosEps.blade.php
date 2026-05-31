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

let activeContratoId = null;
let activePlanId     = null;

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
        document.getElementById('trContratos').innerHTML        = data.html;
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
    document.getElementById('accionPlan').value     = 'guardar';
    document.getElementById('idPlanEdit').value     = '';
    document.getElementById('idContratoPlan').value = activeContratoId;
    document.getElementById('nombrePlan').value     = '';
    document.getElementById('descripcionPlan').value = '';
    document.getElementById('limitePlan').value     = '';
    document.getElementById('periodoPlan').value    = 'anual';
    document.getElementById('estadoPlan').value     = 'activo';
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
