@extends('Plantilla.Principal')
@section('title', 'Factura Electrónica DIAN')
@section('Contenido')
<div class="content-header">
    <div class="d-flex align-items-center">
        <div class="me-auto">
            <h4 class="page-title">Factura Electrónica DIAN</h4>
            <div class="d-inline-block align-items-center">
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#"><i class="mdi mdi-home-outline"></i></a></li>
                        <li class="breadcrumb-item">Administración</li>
                        <li class="breadcrumb-item active">Factura Electrónica</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<section class="content">
<div class="row">

{{-- ===== FILTROS ===== --}}
<div class="col-12">
<div class="card">
    <div class="card-header"><h5 class="card-title">Parámetros de Facturación</h5></div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label">EPS / Entidad</label>
                <select id="sel_eps" class="form-select">
                    <option value="">-- Seleccione --</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Contrato</label>
                <select id="sel_contrato" class="form-select">
                    <option value="">-- Seleccione EPS --</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Período Inicio</label>
                <input type="date" id="inp_periodo_inicio" class="form-control">
            </div>
            <div class="col-md-2">
                <label class="form-label">Período Fin</label>
                <input type="date" id="inp_periodo_fin" class="form-control">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-primary w-100" onclick="previewServicios()">
                    <i class="fa fa-search"></i> Buscar Servicios
                </button>
            </div>
        </div>

        <div class="row g-3 mt-2">
            <div class="col-md-2">
                <label class="form-label">Prefijo</label>
                <input type="text" id="inp_prefix" class="form-control" value="FE" maxlength="4">
            </div>
            <div class="col-md-2">
                <label class="form-label">N° Factura</label>
                <input type="number" id="inp_numero_factura" class="form-control" value="" min="1">
            </div>
        </div>
    </div>
</div>
</div>

{{-- ===== SERVICIOS PREVIEW ===== --}}
<div class="col-12" id="div_servicios" style="display:none;">
<div class="card">
    <div class="card-header">
        <h5 class="card-title">Servicios a Facturar</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-striped mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Paciente</th>
                        <th>Documento</th>
                        <th>Fecha</th>
                        <th>CUPS</th>
                        <th>Descripción</th>
                        <th class="text-end">V. Servicio</th>
                        <th class="text-end">Copago</th>
                        <th class="text-end">V. EPS</th>
                        <th class="text-end">IVA %</th>
                    </tr>
                </thead>
                <tbody id="tbody_servicios"></tbody>
                <tfoot>
                    <tr class="table-secondary fw-bold">
                        <td colspan="6" class="text-end">TOTAL</td>
                        <td class="text-end" id="tfoot_valor_servicio">0</td>
                        <td class="text-end" id="tfoot_copago">0</td>
                        <td class="text-end" id="tfoot_valor_eps">0</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <div class="card-footer d-flex gap-2 flex-wrap">
        <button type="button" class="btn btn-outline-secondary" onclick="generarAccion('xml')">
            <i class="fa fa-code"></i> Generar XML
        </button>
        <button type="button" class="btn btn-outline-warning" onclick="generarAccion('firmar')">
            <i class="fa fa-pen-nib"></i> Generar + Firmar
        </button>
        <button type="button" class="btn btn-success" onclick="generarAccion('enviar')">
            <i class="fa fa-paper-plane"></i> Generar + Firmar + Enviar
        </button>
        <div class="ms-auto d-flex gap-2 align-items-center">
            <input type="text" id="inp_zip_key" class="form-control" placeholder="ZipKey DIAN" style="width:280px;">
            <button type="button" class="btn btn-info text-white" onclick="consultarEstado()">
                <i class="fa fa-search"></i> Consultar Estado
            </button>
        </div>
    </div>
</div>
</div>

{{-- ===== RESULTADO ===== --}}
<div class="col-12" id="div_resultado" style="display:none;">
<div class="card" id="card_resultado">
    <div class="card-header d-flex align-items-center gap-3">
        <h5 class="card-title mb-0">Resultado DIAN</h5>
        <span id="badge_status" class="badge fs-6"></span>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <table class="table table-sm">
                    <tbody>
                        <tr><th style="width:160px;">Factura</th><td id="res_factura">-</td></tr>
                        <tr><th>CUFE</th><td id="res_cufe" style="word-break:break-all;font-size:0.75rem;">-</td></tr>
                        <tr><th>ZipKey</th><td id="res_zip_key">-</td></tr>
                        <tr><th>Código</th><td id="res_codigo">-</td></tr>
                        <tr><th>Estado</th><td id="res_descripcion">-</td></tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-4 text-end" id="div_acciones_descarga" style="display:none;">
                <div class="d-flex flex-column gap-2 align-items-end">
                    <button type="button" class="btn btn-danger" id="fe_btn_pdf">
                        <i class="fa fa-file-pdf"></i> Descargar PDF
                    </button>
                    <button type="button" class="btn btn-secondary" id="fe_btn_json">
                        <i class="fa fa-file-code"></i> Descargar JSON
                    </button>
                </div>
            </div>
        </div>

        <div id="div_xml_preview" style="display:none;">
            <label class="form-label fw-bold">XML Generado</label>
            <textarea id="txt_xml" class="form-control font-monospace" rows="12" readonly></textarea>
        </div>
    </div>
</div>
</div>

{{-- ===== ALERTAS ===== --}}
<div class="col-12">
    <div id="div_alert" class="alert" role="alert" style="display:none;"></div>
</div>

</div>{{-- /row --}}
</section>

@endsection

@section('scripts')
<script>
const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
let currentPayload = null;

// ===== EPS / CONTRATOS =====
async function cargarEps() {
    try {
        const res = await fetch("{{ url('/facturaelectronica/eps-lista') }}", {
            credentials: 'same-origin',
            headers: {'X-CSRF-TOKEN': csrf, 'Accept': 'application/json'}
        });
        const lista = await res.json();
        const sel = document.getElementById('sel_eps');
        lista.forEach(e => {
            const opt = document.createElement('option');
            opt.value = e.id;
            opt.textContent = e.nombre ?? e.razon_social ?? e.id;
            sel.appendChild(opt);
        });
    } catch(err) {
        console.error('Error cargando EPS', err);
    }
}

document.getElementById('sel_eps').addEventListener('change', async function () {
    const epsId = this.value;
    const sel = document.getElementById('sel_contrato');
    sel.innerHTML = '<option value="">-- Seleccione contrato --</option>';
    if (!epsId) return;
    try {
        const res = await fetch(`{{ url('/facturaelectronica/eps') }}/${epsId}/contratos`, {
            credentials: 'same-origin',
            headers: {'X-CSRF-TOKEN': csrf, 'Accept': 'application/json'}
        });
        const lista = await res.json();
        lista.forEach(c => {
            const opt = document.createElement('option');
            opt.value = c.id;
            opt.textContent = (c.numero_contrato ?? c.id) + (c.descripcion ? ' - ' + c.descripcion : '');
            sel.appendChild(opt);
        });
    } catch(err) {
        console.error('Error cargando contratos', err);
    }
});

// ===== PREVIEW SERVICIOS =====
async function previewServicios() {
    const epsId      = document.getElementById('sel_eps').value;
    const contratoId = document.getElementById('sel_contrato').value;
    const inicio     = document.getElementById('inp_periodo_inicio').value;
    const fin        = document.getElementById('inp_periodo_fin').value;

    if (!epsId || !inicio || !fin) {
        showAlert('Seleccione EPS y período.', 'warning'); return;
    }

    try {
        const res = await fetch("{{ url('/facturaelectronica/eps/preview-servicios') }}", {
            method: 'POST',
            credentials: 'same-origin',
            headers: {'Content-Type':'application/json','X-CSRF-TOKEN':csrf,'Accept':'application/json'},
            body: JSON.stringify({eps_id: epsId, contrato_id: contratoId, periodo_inicio: inicio, periodo_fin: fin})
        });
        const data = await res.json();
        if (!data.ok) { showAlert(data.mensaje ?? 'Error al cargar servicios.', 'danger'); return; }

        renderServicios(data.servicios ?? []);
        document.getElementById('inp_numero_factura').value = data.numero_factura ?? '';
        document.getElementById('div_servicios').style.display = '';
        document.getElementById('div_resultado').style.display = 'none';
        hideAlert();
    } catch(err) {
        showAlert('Error de red: ' + err.message, 'danger');
    }
}

function renderServicios(servicios) {
    const tbody = document.getElementById('tbody_servicios');
    tbody.innerHTML = '';
    let totalServicio = 0, totalCopago = 0, totalEps = 0;

    servicios.forEach((s, i) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${i+1}</td>
            <td>${esc(s.paciente_nombre)}</td>
            <td>${esc(s.documento)}</td>
            <td>${esc(s.fecha_servicio)}</td>
            <td>${esc(s.cups_codigo)}</td>
            <td>${esc(s.cups_descripcion)}</td>
            <td class="text-end">${fmt(s.valor_servicio)}</td>
            <td class="text-end">${fmt(s.valor_copago)}</td>
            <td class="text-end">${fmt(s.valor_eps)}</td>
            <td class="text-end">${esc(s.iva_porcentaje ?? '0')}%</td>
        `;
        tbody.appendChild(tr);
        totalServicio += parseFloat(s.valor_servicio ?? 0);
        totalCopago   += parseFloat(s.valor_copago   ?? 0);
        totalEps      += parseFloat(s.valor_eps      ?? 0);
    });

    document.getElementById('tfoot_valor_servicio').textContent = fmt(totalServicio);
    document.getElementById('tfoot_copago').textContent         = fmt(totalCopago);
    document.getElementById('tfoot_valor_eps').textContent      = fmt(totalEps);
}

// ===== PAYLOAD =====
function getPayload() {
    const servicios = [];
    document.querySelectorAll('#tbody_servicios tr').forEach((tr, i) => {
        const tds = tr.querySelectorAll('td');
        servicios.push({
            linea:             i + 1,
            paciente_nombre:   tds[1]?.textContent?.trim() ?? '',
            documento:         tds[2]?.textContent?.trim() ?? '',
            fecha_servicio:    tds[3]?.textContent?.trim() ?? '',
            cups_codigo:       tds[4]?.textContent?.trim() ?? '',
            cups_descripcion:  tds[5]?.textContent?.trim() ?? '',
            valor_servicio:    parseFmt(tds[6]?.textContent),
            valor_copago:      parseFmt(tds[7]?.textContent),
            valor_eps:         parseFmt(tds[8]?.textContent),
            iva_porcentaje:    parseFloat(tds[9]?.textContent ?? '0'),
        });
    });
    return {
        eps_id:          document.getElementById('sel_eps').value,
        contrato_id:     document.getElementById('sel_contrato').value,
        periodo_inicio:  document.getElementById('inp_periodo_inicio').value,
        periodo_fin:     document.getElementById('inp_periodo_fin').value,
        prefix:          document.getElementById('inp_prefix').value,
        numero_factura:  document.getElementById('inp_numero_factura').value,
        servicios,
    };
}

// ===== ACCIONES DIAN =====
const urlMap = {
    xml:    "{{ url('/facturaelectronica/generar-xml') }}",
    firmar: "{{ url('/facturaelectronica/generar-firmar') }}",
    enviar: "{{ url('/facturaelectronica/generar-firmar-enviar') }}",
};

async function generarAccion(tipo) {
    const payload = getPayload();
    if (!payload.eps_id) { showAlert('Seleccione EPS y preview primero.', 'warning'); return; }

    currentPayload = payload;
    hideAlert();

    try {
        const res = await fetch(urlMap[tipo], {
            method: 'POST',
            credentials: 'same-origin',
            headers: {'Content-Type':'application/json','X-CSRF-TOKEN':csrf,'Accept':'application/json'},
            body: JSON.stringify(payload)
        });
        const data = await res.json();
        showStatusBox(data, tipo);
    } catch(err) {
        showAlert('Error de red: ' + err.message, 'danger');
    }
}

async function consultarEstado() {
    const zipKey = document.getElementById('inp_zip_key').value.trim();
    if (!zipKey) { showAlert('Ingrese un ZipKey.', 'warning'); return; }

    try {
        const res = await fetch("{{ url('/facturaelectronica/estado-zip') }}", {
            method: 'POST',
            credentials: 'same-origin',
            headers: {'Content-Type':'application/json','X-CSRF-TOKEN':csrf,'Accept':'application/json'},
            body: JSON.stringify({zip_key: zipKey})
        });
        const data = await res.json();
        showStatusBox(data, 'estado');
    } catch(err) {
        showAlert('Error de red: ' + err.message, 'danger');
    }
}

// ===== STATUS BOX =====
function showStatusBox(data, tipo) {
    const div = document.getElementById('div_resultado');
    div.style.display = '';

    // Badge de estado
    const badge  = document.getElementById('badge_status');
    const estado = String(data?.status ?? '').toLowerCase();
    badge.className = 'badge fs-6 ' + (
        estado === 'aceptado'  ? 'bg-success' :
        estado === 'rechazado' ? 'bg-danger'  :
        'bg-secondary'
    );
    badge.textContent = (data?.status ?? 'SIN RESPUESTA').toUpperCase();

    document.getElementById('res_factura').textContent  = data?.factura  ?? '-';
    document.getElementById('res_cufe').textContent     = data?.cufe     ?? '-';
    document.getElementById('res_zip_key').textContent  = data?.zip_key  ?? '-';
    document.getElementById('res_codigo').textContent   = data?.status_code ?? '-';

    // Descripción: mostrar ambos si son distintos
    const desc1 = data?.status_description ? String(data.status_description) : '';
    const desc2 = data?.status_message     ? String(data.status_message)     : '';
    const descCell = document.getElementById('res_descripcion');
    if (desc1 && desc2 && desc1 !== desc2) {
        descCell.innerHTML =
            `<div><strong>Descripción:</strong> ${esc(desc1)}</div>` +
            `<div class="mt-1"><strong>Mensaje DIAN:</strong> ${esc(desc2)}</div>`;
    } else {
        descCell.textContent = desc1 || desc2 || '-';
    }

    // XML preview solo si tipo === 'xml' y hay xml
    const xmlDiv  = document.getElementById('div_xml_preview');
    const txtXml  = document.getElementById('txt_xml');
    if (tipo === 'xml' && data?.xml) {
        txtXml.value = data.xml;
        xmlDiv.style.display = '';
    } else {
        xmlDiv.style.display = 'none';
        txtXml.value = '';
    }

    // Si hay zip_key, rellenarlo en el campo de consulta
    if (data?.zip_key) {
        document.getElementById('inp_zip_key').value = data.zip_key;
    }

    // Botones PDF/JSON: mostrar si el estado no es solo XML
    const descarga = document.getElementById('div_acciones_descarga');
    descarga.style.display = (tipo !== 'xml') ? '' : 'none';

    div.scrollIntoView({behavior: 'smooth', block: 'start'});
}

// ===== DESCARGAR PDF / JSON =====
async function downloadFile(url, payload, defaultFilename) {
    try {
        const res = await fetch(url, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {'Content-Type':'application/json','X-CSRF-TOKEN':csrf,'Accept':'*/*'},
            body: JSON.stringify(payload)
        });
        if (!res.ok) {
            const err = await res.json().catch(() => ({mensaje: `Error HTTP ${res.status}`}));
            showAlert(err.mensaje ?? 'Error al descargar.', 'danger');
            return;
        }
        const disposition = res.headers.get('content-disposition') ?? '';
        const match       = disposition.match(/filename="?([^";]+)"?/i);
        const filename    = match ? match[1] : defaultFilename;
        const blob        = await res.blob();
        const a           = document.createElement('a');
        a.href            = URL.createObjectURL(blob);
        a.download        = filename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(a.href);
        showAlert('Descargado: ' + filename, 'success');
    } catch(err) {
        showAlert('Error al descargar: ' + err.message, 'danger');
    }
}

document.getElementById('fe_btn_pdf').addEventListener('click', () => {
    const payload = currentPayload ?? getPayload();
    downloadFile("{{ url('/facturaelectronica/generar-pdf') }}", payload, 'factura.pdf');
});

document.getElementById('fe_btn_json').addEventListener('click', () => {
    const payload = currentPayload ?? getPayload();
    downloadFile("{{ url('/facturaelectronica/descargar-json') }}", payload, 'factura.json');
});

// ===== HELPERS =====
function esc(str) {
    return String(str ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}
function fmt(val) {
    const n = parseFloat(val ?? 0);
    return isNaN(n) ? '0' : n.toLocaleString('es-CO', {minimumFractionDigits: 0, maximumFractionDigits: 0});
}
function parseFmt(str) {
    return parseFloat(String(str ?? '0').replace(/\./g,'').replace(/,/g,'.')) || 0;
}
function showAlert(msg, type = 'danger') {
    const el = document.getElementById('div_alert');
    el.className = 'alert alert-' + type;
    el.textContent = msg;
    el.style.display = '';
}
function hideAlert() {
    document.getElementById('div_alert').style.display = 'none';
}

// Inicializar
cargarEps();
</script>
@endsection
