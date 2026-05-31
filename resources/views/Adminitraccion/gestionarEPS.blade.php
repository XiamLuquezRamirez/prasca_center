@extends('Plantilla.Principal')
@section('title', 'Gestionar Entidades Promotoras')
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
            <h4 class="page-title">Gestionar Entidades Promotoras</h4>
            <div class="d-inline-block align-items-center">
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#"><i class="mdi mdi-home-outline"></i></a></li>
                        <li class="breadcrumb-item" aria-current="page">Inicio</li>
                        <li class="breadcrumb-item active" aria-current="page">Gestionar entidades Promotoras</li>
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
                    <h5 class="card-title">Entidades Promotoras de Salud</h5>
                </div>
                <div class="card-body">

                    {{-- TABS --}}
                    <ul class="nav nav-tabs" id="epsMainTab">
                        <li class="nav-item">
                            <a class="nav-link active" id="tab-eps-link" data-bs-toggle="tab" href="#tab-eps">
                                <i class="fa fa-hospital me-1"></i> Entidades Promotoras
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab-contratos-link" data-bs-toggle="tab" href="#tab-contratos">
                                <i class="fa fa-file-contract me-1"></i> Contratos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab-planes-link" data-bs-toggle="tab" href="#tab-planes">
                                <i class="fa fa-layer-group me-1"></i> Planes &middot; <span id="labelEpsPlanes">&mdash;</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab-copagos-link" data-bs-toggle="tab" href="#tab-copagos">
                                <i class="fa fa-dollar-sign me-1"></i> Copagos &middot; <span id="labelPlanCopagos">&mdash;</span>
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content" style="padding-top:16px;">

                        {{-- TAB 1: ENTIDADES PROMOTORAS --}}
                        <div class="tab-pane fade show active" id="tab-eps">
                            <div class="box-controls pull-right">
                                <div class="box-header-actions">
                                    <div class="input-group input-group-merge">
                                        <input type="text" id="busqueda" class="form-control">
                                        <div class="input-group-text" data-password="false">
                                            <span class="fa fa-search"></span>
                                        </div>
                                        <button type="button" onclick="nuevoRegistro(1);"
                                            class="btn btn-xs btn-primary font-bold">
                                            <i class="fa fa-plus"></i> Nueva Entidad promotora
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th style="width:10%;">Código</th>
                                        <th style="width:80%;">Entidad</th>
                                        <th style="width:10%;">Acción</th>
                                    </tr>
                                </thead>
                                <tbody id="trRegistros"></tbody>
                            </table>
                            <div id="pagination-links" class="text-center ml-1 mt-2"></div>
                        </div>

                        {{-- TAB 2: CONTRATOS --}}
                        <div class="tab-pane fade" id="tab-contratos">
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

                        {{-- TAB 3: PLANES --}}
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

                        {{-- TAB 4: COPAGOS --}}
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

<!-- MODAL ENTIDADES -->
<div class="modal fade" id="modalEPS" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 40%;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="tituloAccion">Agregar entidad</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body">
                <form id="formEntidades">
                    <input type="hidden" name="accRegistro" id="accRegistro" value="guardar" />
                    <input type="hidden" name="idRegistro" id="idRegistro" value="" />
                    <input type="hidden" name="codigoOriginal" id="codigoOriginal" value="" />
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="codigo" class="form-label">Código :</label>
                                <input type="text" class="form-control" id="codigo" name="codigo">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="nit" class="form-label">NIT :</label>
                                <input type="text" class="form-control" id="nit" name="nit">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nombre" class="form-label">Nombre :</label>
                                <input type="text" class="form-control" id="nombre" name="nombre">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email" class="form-label">Email :</label>
                                <input type="text" class="form-control" id="email" name="email">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="telefono" class="form-label">Teléfono :</label>
                                <input type="text" class="form-control" id="telefono" name="telefono">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="observaciones" class="form-label">Observaciones :</label>
                                <textarea class="form-control" id="observaciones" name="observaciones" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="box-footer text-end">
                            <button type="button" onclick="nuevoRegistro(2);" style="display: none;" id="newRegistro"
                                class="btn btn-primary-light me-1">
                                <i class="ti-plus "></i> Nuevo
                            </button>
                            <button type="button" id="cancelRegistro" onclick="cancelarRegistro();"
                                class="btn btn-primary-light me-1">
                                <i class="ti-close"></i> Cancelar
                            </button>
                            <button type="button" id="saveRegistro" onclick="guardarRegistro();"
                                class="btn btn-primary">
                                <i class="ti-save"></i> Guardar
                            </button>
                        </div>
                    </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
</div><!-- /.modal -->

<!-- MODAL VENTA ASESORIA -->
<div class="modal fade" id="modalVentaAsesoria" tabindex="-1" aria-labelledby="modalVentaAsesoriaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 60%;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myLargeModalLabelCotizacion">Venta de asesoria</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="input-group input-group-merge d-flex justify-content-end p-3">
                <button type="button" onclick="cancelarVentaAsesoria();" style="display: none;"
                    id="btnRegresarVentaAsesoria" class="btn btn-secondary">
                    <i class="ti-arrow-left"></i> Regresar
                </button>
                <button type="button" onclick="nuevaVentaAsesoria();" id="newVentaAsesoria"
                    class="btn btn-primary">
                    <i class="ti-plus"></i> Nueva venta
                </button>
            </div>
            <div class="modal-body">
                <div id="listadoVentaAsesoria">
                    <table id="tablaVentaAsesoria" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Asesoria</th>
                                <th>Valor</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="trRegistrosVentaAsesoria"></tbody>
                    </table>
                    <div id="pagination-links-ventaAsesoria"></div>
                </div>
                <div id="ventaAsesoriaForm" style="display: none;">
                    <form id="formVentaAsesoria">
                        <input type="hidden" id="idVentaAsesoria" name="idVentaAsesoria" />
                        <input type="hidden" id="accVentaAsesoria" name="accVentaAsesoria" />
                        <input type="hidden" id="idEPS" name="idEPS" />
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="tipoAsesoria" class="form-label">tipo de asesoria :</label>
                                    <select class="form-control select2" id="tipoAsesoria" onchange="cargarAsesoria(this)" name="tipoAsesoria">
                                        <option value="">Seleccione el tipo de asesoria</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="fechaAsesoria" class="form-label">Fecha :</label>
                                    <input type="date" class="form-control" min="1" id="fechaAsesoria" name="fechaAsesoria" value="1">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="valorAsesoriaVis" class="form-label">Valor :</label>
                                    <input type="text" class="form-control" placeholder="$ 0,00"
                                        onchange="cambioFormato(this.id);" onkeypress="return validartxtnum(event)"
                                        onclick="this.select();" id="valorAsesoriaVis" name="valorAsesoriaVis" value="$0,00">
                                    <input type="hidden" class="form-control" id="valorAsesoria" name="valorAsesoria" value="0">
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12 text-end">
                                <button class="btn btn-secondary" type="button" onclick="cancelarVentaAsesoria();"><i class="ti-close"></i> Cancelar</button>
                                <button class="btn btn-primary" type="button" onclick="guardarVentaAsesoria()"><i class="ti-save"></i> Guardar cotización</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

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

document.addEventListener("DOMContentLoaded", function() {
    let menuP = document.getElementById("principalParametros");
    let menuS = document.getElementById("principalParametrosEPS");
    menuP.classList.add("active", "menu-open");
    menuS.classList.add("active");

    loader = document.getElementById('loader');
    loadNow(1);

    $("#formEntidades").validate({
        rules: {
            nombre: { required: true },
            email:  { required: true, email: true }
        },
        messages: {
            nombre: { required: "Por favor, ingrese el nombre de la entidad." },
            email:  { required: "Por favor, ingrese el email de la entidad.", email: "Por favor, ingrese un email válido." }
        },
        submitHandler: function(form) { guardarRegistro(); }
    });

    cargar(1);
    cargarAsesorias();
    cargarEpsSelect();
    cargarContratos(1);

    document.addEventListener('click', function(event) {
        if (event.target.matches('.pagination a')) {
            event.preventDefault();
            var href = event.target.getAttribute('href');
            var page = href.split('page=')[1];
            if (!isNaN(page)) {
                if (event.target.closest('#paginationContratos')) {
                    cargarContratos(page, document.getElementById('busquedaContratos').value);
                } else if (event.target.closest('#pagination-links')) {
                    cargar(page, document.getElementById('busqueda').value);
                }
            }
        }
    });

    document.getElementById('busqueda').addEventListener('input', function() {
        cargar(1, this.value);
    });
    document.getElementById('busquedaContratos').addEventListener('input', function() {
        cargarContratos(1, this.value);
    });
    document.getElementById('busquedaPlanes').addEventListener('input', function() {
        if (activeContratoId) cargarPlanes(activeContratoId, this.value);
    });
});

// ── ENTIDADES PROMOTORAS ─────────────────────────────────

function editarRegistroVenta(idRegistro) {
    document.getElementById('idVentaAsesoria').value = idRegistro;
    document.getElementById('accVentaAsesoria').value = 'editar';

    fetch("{{ route('asesorias.buscaVentaAsesoria') }}", {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ idRegistro: idRegistro })
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('listadoVentaAsesoria').style.display = 'none';
        document.getElementById('ventaAsesoriaForm').style.display = 'block';
        document.getElementById('btnRegresarVentaAsesoria').style.display = 'block';
        document.getElementById('newVentaAsesoria').style.display = 'none';
        document.getElementById('tipoAsesoria').value = data.id_tipo_servicio;
        document.getElementById('fechaAsesoria').value = data.fecha.split(' ')[0];
        document.getElementById('valorAsesoria').value = data.precio;
        document.getElementById('valorAsesoriaVis').value = formatCurrency(data.precio, 'es-CO', 'COP');
    })
    .catch(error => console.error('Error:', error));
}

function verServiciosVenta(idRegistro) {
    var modal = new bootstrap.Modal(document.getElementById("modalVentaAsesoria"), {
        backdrop: 'static', keyboard: false
    });
    modal.show();
    document.getElementById('idEPS').value = idRegistro;
    cagarServiciosVenta(idRegistro);
}

function cagarServiciosVenta(idRegistro) {
    fetch("{{ route('asesorias.listaServiciosVenta') }}", {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ idRegistro: idRegistro })
    })
    .then(response => response.json())
    .then(data => { document.getElementById('trRegistrosVentaAsesoria').innerHTML = data; })
    .catch(error => console.error('Error:', error));
}

function cargarAsesorias() {
    fetch("{{ route('asesorias.listaAsesoriasSelect') }}", {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF }
    })
    .then(response => response.json())
    .then(data => {
        let html = '<option value="">Seleccione la asesoria</option>';
        data.forEach(item => {
            html += `<option data-valor="${item.valor}" data-tiempo="${item.tiempo}" value="${item.id}">${item.descripcion} - ${item.tiempo}</option>`;
        });
        document.getElementById('tipoAsesoria').innerHTML = html;
    })
    .catch(error => console.error('Error:', error));
}

function cancelarVentaAsesoria() {
    document.getElementById('ventaAsesoriaForm').style.display = 'none';
    document.getElementById('listadoVentaAsesoria').style.display = 'block';
    document.getElementById('btnRegresarVentaAsesoria').style.display = 'none';
    document.getElementById('newVentaAsesoria').style.display = 'block';
    document.getElementById('saveRegistro').style.display = 'block';
}

function nuevaVentaAsesoria() {
    document.getElementById('ventaAsesoriaForm').style.display = 'block';
    document.getElementById('listadoVentaAsesoria').style.display = 'none';
    document.getElementById('btnRegresarVentaAsesoria').style.display = 'block';
    document.getElementById('newVentaAsesoria').style.display = 'none';
    document.getElementById('saveRegistro').style.display = 'none';
    document.getElementById('cancelRegistro').style.display = 'none';
    document.getElementById('formVentaAsesoria').reset();
    document.getElementById('idVentaAsesoria').value = '';
    document.getElementById('accVentaAsesoria').value = 'guardar';
}

function cargarAsesoria(select) {
    let valor = select.options[select.selectedIndex].getAttribute('data-valor');
    let tiempo = select.options[select.selectedIndex].getAttribute('data-tiempo');
    document.getElementById('valorAsesoria').value = valor;
    document.getElementById('valorAsesoriaVis').value = formatCurrency(valor, 'es-CO', 'COP');
    document.getElementById('fechaAsesoria').value = tiempo;
}

function cambioFormato(id) {
    let numero = document.getElementById(id);
    document.getElementById("valorAsesoria").value = numero.value;
    numero.value = formatCurrency(numero.value, 'es-CO', 'COP');
}

function guardarVentaAsesoria() {
    let formData = new FormData(document.getElementById('formVentaAsesoria'));
    fetch("{{ route('asesorias.guardarVentaAsesoria') }}", {
        method: 'POST',
        body: formData,
        headers: { 'X-CSRF-TOKEN': CSRF }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            swal("¡Buen trabajo!", "La operación fue realizada exitosamente.", "success");
            cancelarVentaAsesoria();
            cagarServiciosVenta(document.getElementById('idEPS').value);
        } else {
            swal("¡Alerta!", "No se realizo ningun cambio", "warning");
        }
    })
    .catch(error => console.error('Error:', error));
}

function eliminarRegistroVenta(idRegistro) {
    swal({
        title: "Esta seguro?",
        text: "No podrás recuperar este registro!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#fec801",
        confirmButtonText: "Si, eliminar!",
        cancelButtonText: "No, cancelar!",
        closeOnConfirm: false,
        closeOnCancel: false
    }, function(isConfirm) {
        if (isConfirm) {
            fetch("{{ route('asesorias.eliminarVentaAsesoria') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify({ idRegistro: idRegistro })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    swal("¡Buen trabajo!", "La operación fue realizada exitosamente.", "success");
                    cagarServiciosVenta(document.getElementById('idEPS').value);
                } else {
                    swal("¡Alerta!", "No se realizo ningun cambio", "warning");
                }
            })
            .catch(error => console.error('Error:', error));
        } else {
            swal("Cancelado", "Tu registro esta salvo :)", "error");
        }
    });
}

function formatCurrency(number, locale, currencySymbol) {
    return new Intl.NumberFormat(locale, {
        style: 'currency', currency: currencySymbol, minimumFractionDigits: 2
    }).format(number);
}

function validartxtnum(e) {
    tecla = e.which || e.keyCode;
    patron = /[0-9]+$/;
    te = String.fromCharCode(tecla);
    return (patron.test(te) || tecla == 9 || tecla == 8 || tecla == 37 || tecla == 39 || tecla == 44);
}

function guardarRegistro() {
    if ($("#formEntidades").valid()) {
        const formData = new FormData(document.getElementById('formEntidades'));
        fetch("{{ route('form.guardarEntidades') }}", {
            method: 'POST',
            body: formData,
            headers: { 'X-CSRF-TOKEN': CSRF }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                swal("¡Buen trabajo!", "La operación fue realizada exitosamente.", "success");
                document.getElementById('saveRegistro').setAttribute('disabled', 'disabled');
                document.getElementById('newRegistro').style.display = 'initial';
                document.getElementById('cancelRegistro').style.display = 'none';
                cargar(1);
                document.getElementById("accRegistro").value = "guardar";
            } else {
                console.error('Error en el procesamiento:', data.message);
            }
        })
        .catch(error => console.error("Error al enviar los datos:", error));
    }
}

function editarRegistro(idRegistro) {
    var modal = new bootstrap.Modal(document.getElementById("modalEPS"), {
        backdrop: 'static', keyboard: false
    });
    document.getElementById("accRegistro").value = 'editar';
    document.getElementById("idRegistro").value = idRegistro;
    document.getElementById('saveRegistro').removeAttribute('disabled');
    document.getElementById("tituloAccion").innerHTML = "Editar entidad promotora";
    modal.show();

    fetch("{{ route('entidades.buscaEntidad') }}", {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ idRegistro: idRegistro })
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById("nombre").value       = data.entidad;
        document.getElementById("codigo").value       = data.codigo;
        document.getElementById("nit").value          = data.nit;
        document.getElementById("email").value        = data.email;
        document.getElementById("telefono").value     = data.telefono;
        document.getElementById("observaciones").value = data.observaciones;
    })
    .catch(error => console.error('Error:', error));
}

function cancelarRegistro() {
    document.getElementById('formEntidades').reset();
}

function nuevoRegistro(opc) {
    if (opc == 1) {
        var modal = new bootstrap.Modal(document.getElementById("modalEPS"), {
            backdrop: 'static', keyboard: false
        });
        modal.show();
    }
    cancelarRegistro();
    document.getElementById('saveRegistro').removeAttribute('disabled');
    document.getElementById('newRegistro').style.display = 'none';
    document.getElementById('cancelRegistro').style.display = 'initial';
    document.getElementById("accRegistro").value = "guardar";
    document.getElementById("tituloAccion").innerHTML = "Agregar entidad promotora";
}

function eliminarRegistro(idReg) {
    swal({
        title: "Esta seguro?",
        text: "No podrás recuperar este registrto!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#fec801",
        confirmButtonText: "Si, eliminar!",
        cancelButtonText: "No, cancelar!",
        closeOnConfirm: false,
        closeOnCancel: false
    }, function(isConfirm) {
        if (isConfirm) {
            fetch("{{ route('entidades.eliminarEntidad') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify({ idReg: idReg })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    swal("¡Buen trabajo!", data.message, "success");
                    cargar(1);
                } else {
                    swal("¡Alerta!", data.message, "warning");
                }
            });
        } else {
            swal("Cancelado", "Tu registro esta salvo :)", "error");
        }
    });
}

function cargar(page, searchTerm = '') {
    var data = { page: page, search: searchTerm };
    fetch("{{ route('entidades.listaEntidades') }}", {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(responseData => {
        document.getElementById('trRegistros').innerHTML = responseData.entidades;
        feather.replace();
        document.getElementById('pagination-links').innerHTML = responseData.links;
        loadNow(0);
    })
    .catch(error => console.error('Error:', error));
}

// ── CONTRATOS ────────────────────────────────────────────

function cargarEpsSelect() {
    fetch("{{ route('contratosEps.listarEntidadesEps') }}", { method: 'POST', headers })
    .then(r => r.json())
    .then(data => {
        const sel = document.getElementById('epsContrato');
        sel.innerHTML = '<option value="">Seleccionar EPS…</option>';
        data.forEach(e => {
            sel.innerHTML += `<option value="${e.id}">${e.nombre}</option>`;
        });
    });
}

function cargarContratos(page = 1, search = '') {
    fetch("{{ route('contratosEps.listarContratos') }}", {
        method: 'POST', headers, body: JSON.stringify({ page, search })
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('trContratos').innerHTML         = data.html;
        document.getElementById('paginationContratos').innerHTML = data.pagination;
    });
}

function nuevoContrato() {
    document.getElementById('tituloModalContrato').textContent   = 'Agregar contrato';
    document.getElementById('accionContrato').value             = 'guardar';
    document.getElementById('idContratoEdit').value             = '';
    document.getElementById('epsContrato').value                = '';
    document.getElementById('fechaInicioContrato').value        = '';
    document.getElementById('fechaFinContrato').value           = '';
    document.getElementById('estadoContrato').value             = 'borrador';
    new bootstrap.Modal(document.getElementById('modalContrato')).show();
}

function editarContrato(id, idEps, fechaInicio, fechaFin, estado) {
    document.getElementById('tituloModalContrato').textContent  = 'Editar contrato';
    document.getElementById('accionContrato').value             = 'editar';
    document.getElementById('idContratoEdit').value             = id;
    document.getElementById('epsContrato').value                = idEps;
    document.getElementById('fechaInicioContrato').value        = fechaInicio;
    document.getElementById('fechaFinContrato').value           = fechaFin;
    document.getElementById('estadoContrato').value             = estado;
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
    if (!body.id_eps) { swal("Error", "Selecciona una EPS.", "warning"); return; }

    fetch("{{ route('contratosEps.guardarContrato') }}", {
        method: 'POST', headers, body: JSON.stringify(body)
    })
    .then(r => r.json())
    .then(data => {
        bootstrap.Modal.getInstance(document.getElementById('modalContrato')).hide();
        swal("¡Listo!", data.message, "success");
        cargarContratos(1);
    });
}

function eliminarContrato(id) {
    swal({
        title: "¿Eliminar contrato?",
        text: "Se eliminarán también sus planes y copagos.",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#fec801",
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar",
        closeOnConfirm: false,
        closeOnCancel: false
    }, function(isConfirm) {
        if (isConfirm) {
            fetch("{{ route('contratosEps.eliminarContrato') }}", {
                method: 'POST', headers, body: JSON.stringify({ idContrato: id })
            })
            .then(r => r.json())
            .then(() => { swal("Eliminado", "El contrato fue eliminado.", "success"); cargarContratos(1); });
        } else {
            swal("Cancelado", "El contrato no fue eliminado.", "error");
        }
    });
}

// ── PLANES ───────────────────────────────────────────────

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
        method: 'POST', headers, body: JSON.stringify({ idContrato, search })
    })
    .then(r => r.json())
    .then(data => { document.getElementById('trPlanes').innerHTML = data.html; });
}

function nuevoPlan() {
    if (!activeContratoId) { swal("Info", "Primero selecciona un contrato.", "info"); return; }
    document.getElementById('tituloModalPlan').textContent  = 'Agregar plan';
    document.getElementById('accionPlan').value             = 'guardar';
    document.getElementById('idPlanEdit').value             = '';
    document.getElementById('idContratoPlan').value         = activeContratoId;
    document.getElementById('nombrePlan').value             = '';
    document.getElementById('descripcionPlan').value        = '';
    document.getElementById('limitePlan').value             = '';
    document.getElementById('periodoPlan').value            = 'anual';
    document.getElementById('estadoPlan').value             = 'activo';
    new bootstrap.Modal(document.getElementById('modalPlan')).show();
}

function editarPlan(id, nombre, descripcion, limite, periodo, estado) {
    document.getElementById('tituloModalPlan').textContent  = 'Editar plan';
    document.getElementById('accionPlan').value             = 'editar';
    document.getElementById('idPlanEdit').value             = id;
    document.getElementById('nombrePlan').value             = nombre;
    document.getElementById('descripcionPlan').value        = descripcion;
    document.getElementById('limitePlan').value             = limite;
    document.getElementById('periodoPlan').value            = periodo;
    document.getElementById('estadoPlan').value             = estado;
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
    if (!body.nombre) { swal("Error", "El nombre es obligatorio.", "warning"); return; }

    fetch("{{ route('contratosEps.guardarPlan') }}", {
        method: 'POST', headers, body: JSON.stringify(body)
    })
    .then(r => r.json())
    .then(data => {
        bootstrap.Modal.getInstance(document.getElementById('modalPlan')).hide();
        swal("¡Listo!", data.message, "success");
        cargarPlanes(activeContratoId);
    });
}

function eliminarPlan(id) {
    swal({
        title: "¿Eliminar plan?",
        text: "Se eliminarán también sus copagos.",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#fec801",
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar",
        closeOnConfirm: false,
        closeOnCancel: false
    }, function(isConfirm) {
        if (isConfirm) {
            fetch("{{ route('contratosEps.eliminarPlan') }}", {
                method: 'POST', headers, body: JSON.stringify({ idPlan: id })
            })
            .then(r => r.json())
            .then(() => { swal("Eliminado", "El plan fue eliminado.", "success"); cargarPlanes(activeContratoId); });
        } else {
            swal("Cancelado", "El plan no fue eliminado.", "error");
        }
    });
}

// ── COPAGOS ──────────────────────────────────────────────

function verCopagos(idPlan, planNombre) {
    activePlanId = idPlan;
    document.getElementById('labelPlanCopagos').textContent = planNombre;
    document.getElementById('infoBarCopagos').innerHTML =
        `<i class="fa fa-info-circle me-1"></i> Plan <strong>${planNombre}</strong> &middot; Copagos por tipo de servicio`;
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
    if (!activePlanId) { swal("Info", "Primero selecciona un plan.", "info"); return; }
    document.getElementById('tituloModalCopago').textContent    = 'Agregar servicio';
    document.getElementById('accionCopago').value               = 'guardar';
    document.getElementById('idCopagoEdit').value               = '';
    document.getElementById('idPlanCopago').value               = activePlanId;
    document.getElementById('tipoServicioCopago').value         = '';
    document.getElementById('montoCopago').value                = '';
    document.getElementById('maxSesionesCopago').value          = '';
    new bootstrap.Modal(document.getElementById('modalCopago')).show();
}

function editarCopago(id, tipoServicio, monto, max) {
    document.getElementById('tituloModalCopago').textContent    = 'Editar servicio';
    document.getElementById('accionCopago').value               = 'editar';
    document.getElementById('idCopagoEdit').value               = id;
    document.getElementById('tipoServicioCopago').value         = tipoServicio;
    document.getElementById('montoCopago').value                = monto;
    document.getElementById('maxSesionesCopago').value          = max;
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
        swal("Error", "Tipo de servicio y copago son obligatorios.", "warning"); return;
    }
    fetch("{{ route('contratosEps.guardarCopago') }}", {
        method: 'POST', headers, body: JSON.stringify(body)
    })
    .then(r => r.json())
    .then(data => {
        bootstrap.Modal.getInstance(document.getElementById('modalCopago')).hide();
        swal("¡Listo!", data.message, "success");
        cargarCopagos(activePlanId);
    });
}

function eliminarCopago(id) {
    swal({
        title: "¿Eliminar servicio?",
        text: "Se eliminará el copago de este tipo de servicio.",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#fec801",
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar",
        closeOnConfirm: false,
        closeOnCancel: false
    }, function(isConfirm) {
        if (isConfirm) {
            fetch("{{ route('contratosEps.eliminarCopago') }}", {
                method: 'POST', headers, body: JSON.stringify({ idCopago: id })
            })
            .then(r => r.json())
            .then(() => { swal("Eliminado", "El servicio fue eliminado.", "success"); cargarCopagos(activePlanId); });
        } else {
            swal("Cancelado", "El servicio no fue eliminado.", "error");
        }
    });
}
</script>

@endsection
