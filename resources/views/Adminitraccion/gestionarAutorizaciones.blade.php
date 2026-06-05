@extends('Plantilla.Principal')
@section('title', 'Autorizaciones EPS')
@section('Contenido')

<div class="content-header">
    <div class="d-flex align-items-center">
        <div class="me-auto">
            <h4 class="page-title">Autorizaciones EPS</h4>
            <div class="d-inline-block align-items-center">
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#"><i class="mdi mdi-home-outline"></i></a></li>
                        <li class="breadcrumb-item">Agenda</li>
                        <li class="breadcrumb-item active">Autorizaciones EPS</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="row">
        <div class="col-12">
            <div class="box">
                <div class="box-header with-border">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="text" id="busquedaAut" class="form-control"
                                    placeholder="Buscar por paciente, EPS o N° autorización...">
                                <button class="btn btn-primary" onclick="cargar(1)">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <button class="btn btn-success" onclick="nuevaAutorizacion()">
                                <i class="fa fa-plus me-1"></i> Nueva Autorización
                            </button>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Paciente</th>
                                    <th>EPS / Plan</th>
                                    <th>N° Autorización</th>
                                    <th>Tipo Servicio</th>
                                    <th class="text-center">Sesiones</th>
                                    <th class="text-end">Copago</th>
                                    <th>Vencimiento</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="trAutorizaciones">
                                <tr>
                                    <td colspan="10" class="text-center py-4">
                                        <i class="fa fa-spinner fa-spin"></i> Cargando...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div id="paginationAut" class="mt-2"></div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Modal Crear / Editar --}}
<div class="modal fade" id="modalAutorizacion" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tituloModalAut">Nueva Autorización</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="idAutorizacion">

                <div class="row g-3">
                    {{-- Paciente --}}
                    <div class="col-md-6">
                        <label class="form-label">Paciente <span class="text-danger">*</span></label>
                        <select id="selPacienteAut" class="form-control select2" style="width:100%;">
                            <option value="">Buscar paciente...</option>
                        </select>
                    </div>
                    {{-- Plan EPS --}}
                    <div class="col-md-6">
                        <label class="form-label">Plan EPS <span class="text-danger">*</span></label>
                        <select id="selPlanAut" class="form-control" onchange="cargarServicios(this.value)">
                            <option value="">— Seleccione plan —</option>
                        </select>
                        <small class="text-muted" id="infoEpsAut"></small>
                    </div>
                    {{-- N° Autorización --}}
                    <div class="col-md-4">
                        <label class="form-label">N° Autorización <span class="text-danger">*</span></label>
                        <input type="text" id="numAutorizacion" class="form-control"
                            placeholder="Ej: 110010001234">
                    </div>
                    {{-- Tipo de servicio --}}
                    <div class="col-md-8">
                        <label class="form-label">Tipo de servicio <span class="text-danger">*</span></label>
                        <select id="selServicioAut" class="form-control" onchange="autoCompletarCopago(this)">
                            <option value="">— Seleccione servicio —</option>
                        </select>
                    </div>
                    {{-- Fecha solicitud --}}
                    <div class="col-md-4">
                        <label class="form-label">Fecha solicitud <span class="text-danger">*</span></label>
                        <input type="date" id="fechaSolicitudAut" class="form-control">
                    </div>
                    {{-- Fecha vencimiento --}}
                    <div class="col-md-4">
                        <label class="form-label">Fecha vencimiento</label>
                        <input type="date" id="fechaVencimientoAut" class="form-control">
                    </div>
                    {{-- Sesiones autorizadas --}}
                    <div class="col-md-4">
                        <label class="form-label">Sesiones autorizadas</label>
                        <input type="number" id="sesionesAut" class="form-control" min="1"
                            placeholder="Vacío = ilimitado">
                    </div>
                    {{-- Valor copago --}}
                    <div class="col-md-4">
                        <label class="form-label">Valor copago ($)</label>
                        <input type="number" id="valorCopagoAut" class="form-control" min="0" value="0"
                            step="100">
                    </div>
                    {{-- Valor autorizado --}}
                    <div class="col-md-4">
                        <label class="form-label">Valor autorizado ($)</label>
                        <input type="number" id="valorAutorizadoAut" class="form-control" min="0" value="0"
                            step="100">
                    </div>
                    {{-- Estado --}}
                    <div class="col-md-4">
                        <label class="form-label">Estado</label>
                        <select id="estadoAut" class="form-control">
                            <option value="activa">Activa</option>
                            <option value="agotada">Agotada</option>
                            <option value="vencida">Vencida</option>
                            <option value="anulada">Anulada</option>
                        </select>
                    </div>
                    {{-- Observaciones --}}
                    <div class="col-12">
                        <label class="form-label">Observaciones</label>
                        <textarea id="obsAut" class="form-control" rows="2"
                            placeholder="Notas adicionales..."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardarAutorizacion()">
                    <i class="fa fa-save me-1"></i> Guardar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
const CSRF     = '{{ csrf_token() }}';
const BASE_URL = '{{ asset("/") }}';

document.addEventListener('DOMContentLoaded', function () {
    loader = document.getElementById('loader');
    loadNow(1);

    cargar(1);
    cargarPacientesSelect();

    // Buscar al presionar Enter
    document.getElementById('busquedaAut').addEventListener('keydown', function (e) {
        if (e.key === 'Enter') cargar(1);
    });

    // Delegación de paginación
    document.addEventListener('click', function (e) {
        const link = e.target.closest('#paginationAut a.page-link');
        if (!link) return;
        e.preventDefault();
        const url  = new URL(link.href);
        const page = url.searchParams.get('page') || 1;
        cargar(parseInt(page));
    });
});

function cargar(page) {
    const busqueda = document.getElementById('busquedaAut').value;
    document.getElementById('trAutorizaciones').innerHTML =
        '<tr><td colspan="10" class="text-center py-3"><i class="fa fa-spinner fa-spin"></i></td></tr>';

    fetch(`${BASE_URL}autorizaciones/listar`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF,
        },
        body: JSON.stringify({ page, busqueda }),
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('trAutorizaciones').innerHTML =
            data.html || '<tr><td colspan="10" class="text-center text-muted">Sin resultados</td></tr>';
        document.getElementById('paginationAut').innerHTML = data.pagination || '';
        loadNow(0);
    })
    .catch(() => {
        document.getElementById('trAutorizaciones').innerHTML =
            '<tr><td colspan="10" class="text-center text-danger">Error al cargar</td></tr>';
        loadNow(0);
    });
}

function cargarPacientesSelect() {
    fetch(`${BASE_URL}pacientes/cargarListaPacientes`)
        .then(r => r.json())
        .then(data => {
            const sel = document.getElementById('selPacienteAut');
            data.forEach(p => {
                const opt = new Option(
                    `${p.nombre} — ${p.identificacion}`,
                    p.id
                );
                sel.appendChild(opt);
            });
            if (window.jQuery && $.fn.select2) {
                $('#selPacienteAut').select2({ dropdownParent: $('#modalAutorizacion') });
                $('#selPacienteAut').on('change', function () {
                    cargarPlanesDelPaciente(this.value);
                });
            } else {
                sel.addEventListener('change', function () {
                    cargarPlanesDelPaciente(this.value);
                });
            }
        });
}

function cargarPlanesDelPaciente(idPaciente) {
    document.getElementById('selPlanAut').innerHTML = '<option value="">Cargando...</option>';
    document.getElementById('selServicioAut').innerHTML = '<option value="">— Seleccione servicio —</option>';
    document.getElementById('infoEpsAut').textContent = '';

    if (!idPaciente) {
        document.getElementById('selPlanAut').innerHTML = '<option value="">— Seleccione plan —</option>';
        return;
    }

    fetch(`${BASE_URL}autorizaciones/planesPorPaciente?id_paciente=${idPaciente}`)
        .then(r => r.json())
        .then(planes => {
            const sel = document.getElementById('selPlanAut');
            sel.innerHTML = '<option value="">— Seleccione plan —</option>';
            planes.forEach(p => {
                sel.innerHTML += `<option value="${p.id}" data-eps="${p.nombre_eps}">${p.nombre} (${p.nombre_eps})</option>`;
            });
            if (planes.length === 0) {
                sel.innerHTML = '<option value="">Sin planes activos</option>';
                document.getElementById('infoEpsAut').textContent = 'El paciente no tiene plan EPS activo.';
            }
        });
}

function cargarServicios(idPlan) {
    document.getElementById('selServicioAut').innerHTML = '<option value="">Cargando...</option>';
    document.getElementById('infoEpsAut').textContent = '';

    if (!idPlan) {
        document.getElementById('selServicioAut').innerHTML = '<option value="">— Seleccione servicio —</option>';
        return;
    }

    const opt = document.getElementById('selPlanAut').options[document.getElementById('selPlanAut').selectedIndex];
    if (opt && opt.dataset.eps) {
        document.getElementById('infoEpsAut').textContent = opt.dataset.eps;
    }

    fetch(`${BASE_URL}autorizaciones/serviciosPorPlan?id_plan=${idPlan}`)
        .then(r => r.json())
        .then(copagos => {
            const sel = document.getElementById('selServicioAut');
            sel.innerHTML = '<option value="">— Seleccione servicio —</option>';
            copagos.forEach(c => {
                const max   = c.max_sesiones || 'Ilimitada';
                const monto = '$' + Number(c.monto_copago).toLocaleString('es-CO');
                sel.innerHTML += `<option value="${c.tipo_servicio}" data-copago="${c.monto_copago}" data-max="${c.max_sesiones || ''}">${c.tipo_servicio} — ${monto} copago — ${max} ses.</option>`;
            });
        });
}

function autoCompletarCopago(sel) {
    const opt = sel.options[sel.selectedIndex];
    if (opt && opt.dataset.copago !== undefined) {
        document.getElementById('valorCopagoAut').value = opt.dataset.copago;
        if (opt.dataset.max) {
            document.getElementById('sesionesAut').value = opt.dataset.max;
        }
    }
}

function nuevaAutorizacion() {
    document.getElementById('tituloModalAut').textContent = 'Nueva Autorización';
    document.getElementById('idAutorizacion').value = '';
    document.getElementById('numAutorizacion').value = '';
    document.getElementById('fechaSolicitudAut').value = new Date().toISOString().split('T')[0];
    document.getElementById('fechaVencimientoAut').value = '';
    document.getElementById('sesionesAut').value = '';
    document.getElementById('valorCopagoAut').value = '0';
    document.getElementById('valorAutorizadoAut').value = '0';
    document.getElementById('estadoAut').value = 'activa';
    document.getElementById('obsAut').value = '';
    if (window.jQuery && $.fn.select2) {
        $('#selPacienteAut').val('').trigger('change.select2');
    } else {
        document.getElementById('selPacienteAut').value = '';
    }
    document.getElementById('selPlanAut').innerHTML = '<option value="">— Seleccione plan —</option>';
    document.getElementById('selServicioAut').innerHTML = '<option value="">— Seleccione servicio —</option>';
    document.getElementById('infoEpsAut').textContent = '';
    new bootstrap.Modal(document.getElementById('modalAutorizacion')).show();
}

function editarAutorizacion(id, idPaciente, idPlan, numAut, tipoServicio, fechaSol, fechaVen, sesiones, copago, valorAut, estado, obs) {
    document.getElementById('tituloModalAut').textContent = 'Editar Autorización';
    document.getElementById('idAutorizacion').value      = id;
    document.getElementById('numAutorizacion').value     = numAut;
    document.getElementById('fechaSolicitudAut').value   = fechaSol;
    document.getElementById('fechaVencimientoAut').value = fechaVen || '';
    document.getElementById('sesionesAut').value         = sesiones || '';
    document.getElementById('valorCopagoAut').value      = copago;
    document.getElementById('valorAutorizadoAut').value  = valorAut;
    document.getElementById('estadoAut').value           = estado;
    document.getElementById('obsAut').value              = obs;

    // Set patient select
    if (window.jQuery && $.fn.select2) {
        $('#selPacienteAut').val(idPaciente).trigger('change.select2');
    } else {
        document.getElementById('selPacienteAut').value = idPaciente;
    }

    // Load plans, then set plan, then load services, then set service
    fetch(`${BASE_URL}autorizaciones/planesPorPaciente?id_paciente=${idPaciente}`)
        .then(r => r.json())
        .then(planes => {
            const sel = document.getElementById('selPlanAut');
            sel.innerHTML = '<option value="">— Seleccione plan —</option>';
            planes.forEach(p => {
                sel.innerHTML += `<option value="${p.id}" data-eps="${p.nombre_eps}">${p.nombre} (${p.nombre_eps})</option>`;
            });
            sel.value = idPlan;

            return fetch(`${BASE_URL}autorizaciones/serviciosPorPlan?id_plan=${idPlan}`);
        })
        .then(r => r.json())
        .then(copagos => {
            const sel2 = document.getElementById('selServicioAut');
            sel2.innerHTML = '<option value="">— Seleccione servicio —</option>';
            copagos.forEach(c => {
                const max   = c.max_sesiones || 'Ilimitada';
                const monto = '$' + Number(c.monto_copago).toLocaleString('es-CO');
                sel2.innerHTML += `<option value="${c.tipo_servicio}" data-copago="${c.monto_copago}" data-max="${c.max_sesiones || ''}">${c.tipo_servicio} — ${monto} copago — ${max} ses.</option>`;
            });
            sel2.value = tipoServicio;
        });

    new bootstrap.Modal(document.getElementById('modalAutorizacion')).show();
}

function guardarAutorizacion() {
    const id         = document.getElementById('idAutorizacion').value;
    const idPaciente = window.jQuery
        ? $('#selPacienteAut').val()
        : document.getElementById('selPacienteAut').value;
    const idPlan     = document.getElementById('selPlanAut').value;
    const numAut     = document.getElementById('numAutorizacion').value.trim();
    const tipoServ   = document.getElementById('selServicioAut').value;
    const fechaSol   = document.getElementById('fechaSolicitudAut').value;

    if (!idPaciente) { return swal('Atención', 'Seleccione un paciente.', 'warning'); }
    if (!idPlan)     { return swal('Atención', 'Seleccione el plan EPS.', 'warning'); }
    if (!numAut)     { return swal('Atención', 'Ingrese el número de autorización.', 'warning'); }
    if (!tipoServ)   { return swal('Atención', 'Seleccione el tipo de servicio.', 'warning'); }
    if (!fechaSol)   { return swal('Atención', 'Ingrese la fecha de solicitud.', 'warning'); }

    fetch(`${BASE_URL}autorizaciones/guardar`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({
            id,
            id_paciente:          idPaciente,
            id_plan:              idPlan,
            numero_autorizacion:  numAut,
            tipo_servicio:        tipoServ,
            fecha_solicitud:      fechaSol,
            fecha_vencimiento:    document.getElementById('fechaVencimientoAut').value,
            sesiones_autorizadas: document.getElementById('sesionesAut').value,
            valor_copago:         document.getElementById('valorCopagoAut').value,
            valor_autorizado:     document.getElementById('valorAutorizadoAut').value,
            estado:               document.getElementById('estadoAut').value,
            observaciones:        document.getElementById('obsAut').value,
        }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('modalAutorizacion'))?.hide();
            swal({ title: 'Guardado', text: 'Autorización guardada exitosamente.', type: 'success', timer: 1500, showConfirmButton: false });
            cargar(1);
        } else {
            swal('Error', data.message || 'No se pudo guardar.', 'error');
        }
    });
}

function eliminarAutorizacion(id) {
    swal({
        title: '¿Eliminar autorización?',
        text: 'Esta acción no se puede deshacer.',
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonClass: 'btn btn-danger',
        cancelButtonClass: 'btn btn-light',
        buttonsStyling: false,
    }, function (confirm) {
        if (!confirm) return;
        fetch(`${BASE_URL}autorizaciones/eliminar`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ id }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                swal({ title: 'Eliminado', type: 'success', timer: 1200, showConfirmButton: false });
                cargar(1);
            } else {
                swal('No se puede eliminar', data.message, 'warning');
            }
        });
    });
}
</script>

@endsection
