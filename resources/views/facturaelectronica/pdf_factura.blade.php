<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #111; }
    table { width: 100%; border-collapse: collapse; }
    .page { padding: 14px 16px; }

    /* Header */
    .header-table td { vertical-align: top; padding: 2px 4px; }
    .header-title { font-size: 13px; font-weight: bold; color: #1a1a6e; }
    .header-sub { font-size: 8px; color: #444; }
    .header-fe { text-align: center; border: 2px solid #1a1a6e; padding: 6px 10px; }
    .header-fe .fe-label { font-size: 8px; font-weight: bold; color: #1a1a6e; }
    .header-fe .fe-num { font-size: 16px; font-weight: bold; color: #1a1a6e; }

    /* Section titles */
    .section-title { background: #1a1a6e; color: #fff; font-weight: bold; font-size: 8px;
        padding: 2px 5px; margin: 5px 0 2px 0; }

    /* Info tables */
    .info-table td { padding: 2px 4px; border: 1px solid #ccc; vertical-align: top; }
    .info-table .lbl { font-weight: bold; background: #f0f0f0; width: 120px; }

    /* Items table */
    .items-table th { background: #1a1a6e; color: #fff; padding: 3px 4px;
        text-align: center; font-size: 8px; border: 1px solid #888; }
    .items-table td { padding: 2px 4px; border: 1px solid #ccc; font-size: 8px; }
    .items-table tr:nth-child(even) td { background: #f7f7f7; }
    .items-table .num { text-align: right; }

    /* Totals */
    .totals-table td { padding: 2px 5px; }
    .totals-table .lbl { font-weight: bold; text-align: right; }
    .totals-table .val { text-align: right; border-bottom: 1px solid #ccc; width: 120px; }
    .totals-table .grand-lbl { font-weight: bold; text-align: right; font-size: 11px; }
    .totals-table .grand-val { font-weight: bold; text-align: right; font-size: 11px;
        border: 2px solid #1a1a6e; padding: 3px 5px; }

    /* Footer */
    .cufe-box { border: 1px solid #aaa; padding: 4px; margin-top: 6px; font-size: 7px; }
    .footer-text { font-size: 7px; color: #555; margin-top: 4px; }
    .representacion { text-align: center; font-size: 7px; color: #555;
        border: 1px solid #aaa; padding: 2px; margin: 4px 0; }
    .auth-text { font-size: 7px; color: #333; margin-top: 3px; }
    .letras-text { font-style: italic; font-size: 8px; color: #333; margin: 3px 0; }
</style>
</head>
<body>
<div class="page">

{{-- ===== CABECERA ===== --}}
<table class="header-table" style="margin-bottom:4px;">
    <tr>
        <td style="width:50%;">
            <div class="header-title">{{ $data['company_name'] }}</div>
            <div class="header-sub">NIT: {{ $data['company_nit'] }}-{{ $data['dv_company'] }}</div>
            <div class="header-sub">{{ $data['company_address'] }}, {{ $data['company_city'] }}</div>
            <div class="header-sub">Tel: {{ $data['company_phone'] }} &nbsp;|&nbsp; {{ $data['company_email'] }}</div>
            <div class="header-sub">Régimen: {{ $data['tax_level_code'] }}</div>
        </td>
        <td style="width:30%; text-align:center;">
            @if(!empty($data['qr_data_uri']))
            <img src="{{ $data['qr_data_uri'] }}" style="width:80px;height:80px;" alt="QR">
            @endif
        </td>
        <td style="width:20%;">
            <div class="header-fe">
                <div class="fe-label">FACTURA DE VENTA</div>
                <div class="fe-label">ELECTRÓNICA</div>
                <div class="fe-num">{{ $data['prefix'] }}-{{ $data['numero_factura'] }}</div>
                <div class="fe-label">Fecha: {{ $data['issue_date'] }}</div>
            </div>
        </td>
    </tr>
</table>

<div class="representacion">REPRESENTACIÓN GRÁFICA DE FACTURA ELECTRÓNICA DE VENTA — No tiene validez fiscal</div>

{{-- ===== DATOS CLIENTE ===== --}}
<div class="section-title">DATOS DEL ADQUIRIENTE</div>
<table class="info-table" style="margin-bottom:4px;">
    <tr>
        <td class="lbl">Razón Social / Nombre</td>
        <td colspan="3">{{ $data['customer_name'] }}</td>
    </tr>
    <tr>
        <td class="lbl">NIT / Documento</td>
        <td>{{ $data['customer_nit'] }}-{{ $data['dv_customer'] }}</td>
        <td class="lbl">Tipo Doc.</td>
        <td>{{ $data['customer_id_code'] }}</td>
    </tr>
    <tr>
        <td class="lbl">Dirección</td>
        <td>{{ $data['customer_address'] }}</td>
        <td class="lbl">Ciudad</td>
        <td>{{ $data['customer_city'] }}, {{ $data['customer_dept'] }}</td>
    </tr>
    <tr>
        <td class="lbl">Período Facturado</td>
        <td>{{ $data['period_start'] }} al {{ $data['period_end'] }}</td>
        <td class="lbl">Régimen</td>
        <td>{{ $data['customer_tax_level'] }}</td>
    </tr>
</table>

{{-- ===== ITEMS ===== --}}
<div class="section-title">DETALLE DE SERVICIOS</div>
<table class="items-table" style="margin-bottom:4px;">
    <thead>
        <tr>
            <th style="width:3%;">#</th>
            <th style="width:8%;">CUPS</th>
            <th style="width:30%;">DESCRIPCIÓN</th>
            <th style="width:12%;">PACIENTE</th>
            <th style="width:8%;">DOCUMENTO</th>
            <th style="width:8%;">FECHA</th>
            <th style="width:5%;">CANT.</th>
            <th style="width:5%;">UNID.</th>
            <th style="width:10%;">VR. UNIT.</th>
            <th style="width:5%;">IVA%</th>
            <th style="width:10%;">VR. TOTAL</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data['items'] as $item)
        <tr>
            <td class="num">{{ $item['linea'] }}</td>
            <td>{{ $item['cups_codigo'] ?? '' }}</td>
            <td>{{ $item['cups_descripcion'] ?? $item['descripcion'] ?? '' }}</td>
            <td>{{ $item['paciente_nombre'] ?? '' }}</td>
            <td>{{ $item['documento'] ?? '' }}</td>
            <td>{{ $item['fecha_servicio'] ?? '' }}</td>
            <td class="num">{{ $item['cantidad'] ?? 1 }}</td>
            <td>{{ $item['unidad'] ?? 'SVC' }}</td>
            <td class="num">$ {{ number_format($item['precio_unitario'] ?? $item['valor_eps'] ?? 0, 0, ',', '.') }}</td>
            <td class="num">{{ $item['iva_porcentaje'] ?? 0 }}%</td>
            <td class="num">$ {{ number_format($item['valor_total'] ?? $item['valor_eps'] ?? 0, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

{{-- ===== TOTALES ===== --}}
<table style="margin-bottom:6px;">
    <tr>
        <td style="width:55%; vertical-align:top;">
            <div class="letras-text">SON: {{ strtoupper($data['total_letras'] ?? '') }}</div>
            @if(!empty($data['bank_transfer']))
            <div style="font-size:7px; margin-top:3px;">
                <strong>Transferencia bancaria:</strong> {{ $data['bank_transfer'] }}
            </div>
            @endif
            @if(!empty($data['software_name']))
            <div class="footer-text" style="margin-top:5px;">
                <strong>Software:</strong> {{ $data['software_name'] }}<br>
                {{ $data['software_maker'] ?? '' }}
            </div>
            @endif
        </td>
        <td style="width:45%; vertical-align:top;">
            <table class="totals-table" style="margin-left:auto;">
                <tr>
                    <td class="lbl">SUBTOTAL:</td>
                    <td class="val">$ {{ number_format($data['line_extension_amount'] ?? 0, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="lbl">BASE IMPONIBLE:</td>
                    <td class="val">$ {{ number_format($data['taxable_amount'] ?? 0, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="lbl">IVA ({{ $data['percent'] ?? 0 }}%):</td>
                    <td class="val">$ {{ number_format($data['tax_amount'] ?? 0, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="lbl">TRANSFERENCIAS:</td>
                    <td class="val">$ 0</td>
                </tr>
                <tr style="height:4px;"><td colspan="2"></td></tr>
                <tr>
                    <td class="grand-lbl">TOTAL A PAGAR:</td>
                    <td class="grand-val">$ {{ number_format($data['payable_amount'] ?? 0, 0, ',', '.') }}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

{{-- ===== CUFE Y AUTORIZACIÓN ===== --}}
<div class="cufe-box">
    <strong>CUFE:</strong> {{ $data['cufe'] ?? '' }}
</div>

<div class="auth-text">
    Autorización de numeración de facturación No. <strong>{{ $data['invoice_authorization'] }}</strong>
    del <strong>{{ $data['auth_start'] }}</strong> al <strong>{{ $data['auth_end'] }}</strong>.
    Rango autorizado del <strong>{{ $data['prefix'] }}{{ $data['auth_from'] }}</strong>
    al <strong>{{ $data['prefix'] }}{{ $data['auth_to'] }}</strong>.
    Proveedor: <strong>{{ $data['auth_provider_nit'] }}</strong>.
</div>

@if(!empty($data['es_salud']) && $data['es_salud'])
<div class="auth-text" style="margin-top:3px;">
    <strong>Prestador:</strong> {{ $data['salud']['codigo_prestador'] ?? '' }} &nbsp;|&nbsp;
    <strong>Contrato:</strong> {{ $data['salud']['numero_contrato'] ?? '' }} &nbsp;|&nbsp;
    <strong>Modalidad:</strong> {{ $data['salud']['modalidad_pago_value'] ?? '' }} &nbsp;|&nbsp;
    <strong>Cobertura:</strong> {{ $data['salud']['cobertura_value'] ?? '' }}
</div>
@endif

</div>{{-- /page --}}
</body>
</html>
