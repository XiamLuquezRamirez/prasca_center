<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use DOMDocument;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Dian\SendBillSync;
use Stenfrank\UBL21dian\XAdES\SignInvoice;
use Stenfrank\UBL21dian\Templates\SOAP\SendBillAsync;
use Stenfrank\UBL21dian\Templates\SOAP\SendTestSetAsync;
use Stenfrank\UBL21dian\Templates\SOAP\GetStatusZip;
use Stenfrank\UBL21dian\Client;

class EInvoiceController extends Controller
{
    private function resolvePath(string $path): string
    {
        $path = trim($path);
        if ($path === '') return $path;

        if (preg_match('/^(?:[a-zA-Z]:\\\\|\\\\\\\\|\\/)/', $path)) {
            return $path;
        }

        if (is_file($path)) {
            return $path;
        }

        $baseResolved = base_path($path);
        if (is_file($baseResolved)) {
            return $baseResolved;
        }

        $normalized = str_replace('\\', '/', $path);
        if (str_starts_with($normalized, 'public/')) {
            $publicResolved = public_path(substr($normalized, strlen('public/')));
            return $publicResolved;
        }

        return $baseResolved;
    }

    public function generarXml(Request $request): JsonResponse
    {
        try {
            $payload = $this->getPayload($request);
            $xml = $this->buildInvoiceXml($payload);

            return response()->json([
                'ok'      => true,
                'mensaje' => 'XML generado.',
                'xml'     => $xml,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'ok'      => false,
                'mensaje' => 'Error generando XML.',
                'detalle' => $e->getMessage(),
            ], 422);
        }
    }

    public function generarFirmar(Request $request): JsonResponse
    {
        try {
            $payload = $this->getPayload($request);
            $xml     = $this->buildInvoiceXml($payload);
            $signed  = $this->signXml($xml);

            return response()->json([
                'ok'      => true,
                'mensaje' => 'XML generado y firmado.',
                'xml'     => $signed,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'ok'      => false,
                'mensaje' => 'Error generando/firma XML.',
                'detalle' => $e->getMessage(),
            ], 422);
        }
    }

    public function generarFirmarEnviar(Request $request): JsonResponse
    {
        try {
            $payload  = $this->getPayload($request);
            $xml      = $this->buildInvoiceXml($payload);
            $signed   = $this->signXml($xml);
            $response = $this->enviarDian($signed);

            $responseXml = is_string($response)
                ? $response
                : (is_array($response)
                    ? json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
                    : (string)$response);

            $zipKey = $this->extractXmlValueByLocalName($responseXml, 'ZipKey');

            return response()->json([
                'ok'      => true,
                'mensaje' => 'Enviado a DIAN (sync). Revisa la respuesta.',
                'zip_key' => $zipKey,
                'xml'     => $responseXml,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'ok'      => false,
                'mensaje' => 'Error generando/firma/enviando DIAN.',
                'detalle' => $e->getMessage(),
            ], 422);
        }
    }

    public function generarPdf(Request $request)
    {
        try {
            $payload = $this->getPayload($request);
            $data    = $this->buildInvoiceData($payload);

            $data['qr_data_uri']  = $this->generarQrDataUri($data['qr_string']);
            $data['total_letras'] = $this->numeroALetras((float)$data['payable_amount']);

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
                'facturaelectronica.pdf_factura',
                ['data' => $data]
            )->setPaper('A4', 'portrait');

            $filename = 'factura_' . $data['id'] . '_' . date('Ymd') . '.pdf';
            return $pdf->download($filename);
        } catch (\Throwable $e) {
            return response()->json([
                'ok'      => false,
                'mensaje' => 'Error generando PDF.',
                'detalle' => $e->getMessage(),
            ], 422);
        }
    }

    public function descargarJson(Request $request)
    {
        try {
            $payload = $this->getPayload($request);
            $data    = $this->buildInvoiceData($payload);

            $json = [
                'id'               => $data['id'],
                'cufe'             => $data['cufe'],
                'fecha_generacion' => $data['issue_date'] . ' ' . substr($data['issue_time'], 0, 8),
                'emisor'           => [
                    'nombre'       => $data['company_name'],
                    'nit'          => $data['company_nit'],
                    'dv'           => $data['dv_company'],
                    'direccion'    => $data['company_address'],
                    'ciudad'       => $data['company_city'],
                    'departamento' => $data['company_department'],
                    'telefono'     => $data['company_phone'],
                    'email'        => $data['company_email'],
                ],
                'adquiriente' => [
                    'nombre'    => $data['customer_name'],
                    'nit'       => $data['customer_nit'],
                    'dv'        => $data['dv_customer'],
                    'direccion' => $data['customer_address'],
                    'ciudad'    => $data['customer_city'],
                ],
                'items' => array_map(fn($it) => [
                    'codigo'         => $it['seller_item_id'],
                    'descripcion'    => $it['lineItemName'],
                    'cantidad'       => (float)$it['lineQuantity'],
                    'valor_unitario' => (float)$it['itemPrice'],
                    'total'          => (float)$it['lineTotal'],
                ], $data['items']),
                'totales' => [
                    'subtotal' => (float)$data['line_extension_amount'],
                    'iva'      => (float)$data['total_tax_amount'],
                    'total'    => (float)$data['payable_amount'],
                ],
                'autorizacion' => $data['invoice_authorization'],
                'prefijo'      => $data['prefix'],
                'numero'       => $data['numero_factura'],
                'software_id'  => $data['software_id'],
            ];

            $filename = 'factura_' . $data['id'] . '_' . date('Ymd') . '.json';
            return response(
                json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
                200,
                [
                    'Content-Type'        => 'application/json; charset=utf-8',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                ]
            );
        } catch (\Throwable $e) {
            return response()->json([
                'ok'      => false,
                'mensaje' => 'Error generando JSON.',
                'detalle' => $e->getMessage(),
            ], 422);
        }
    }

    public function consultarEstadoZip(Request $request): JsonResponse
    {
        try {
            $trackId = trim((string)($request->input('track_id') ?? $request->input('zip_key') ?? ''));
            if ($trackId === '') {
                throw new \InvalidArgumentException('track_id (ZipKey) es obligatorio.');
            }

            $certPath = $this->resolvePath((string)config('fe.cert.p12_path', public_path('key/certificado.p12')));
            $password = (string)config('fe.cert.p12_password', '');

            if ($password === '') {
                throw new \RuntimeException('Configura FE_CERT_P12_PASSWORD en el .env.');
            }
            if (!is_file($certPath)) {
                throw new \RuntimeException("No se encontró el certificado .p12 en: {$certPath}");
            }

            $getStatusZip = new GetStatusZip($certPath, $password);
            $getStatusZip->trackId = $trackId;
            $getStatusZip->sign();

            $client = new Client($getStatusZip);
            $xml    = (string)$client->getResponse();
            $parsed = $this->parseDianStatusZipResponse($xml);

            return response()->json([
                'ok'                 => true,
                'mensaje'            => 'Estado consultado.',
                'track_id'           => $trackId,
                'resultado'          => $parsed['resultado'],
                'is_valid'           => $parsed['is_valid'],
                'status_code'        => $parsed['status_code'],
                'status_description' => $parsed['status_description'],
                'status_message'     => $parsed['status_message'],
                'errors'             => $parsed['errors'],
                'xml'                => $xml,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'ok'      => false,
                'mensaje' => 'Error consultando estado en DIAN.',
                'detalle' => $e->getMessage(),
            ], 422);
        }
    }

    public function epsLista(): JsonResponse
    {
        $eps = DB::table('eps')
            ->where('estado', 'ACTIVO')
            ->orderBy('entidad')
            ->get(['id', 'entidad', 'nit', 'regimen', 'codigo']);

        return response()->json(['ok' => true, 'eps' => $eps]);
    }

    public function epsContratos(int $id): JsonResponse
    {
        $contratos = DB::table('contratos_eps')
            ->where('eps_id', $id)
            ->where('estado', 'vigente')
            ->get();

        $planes = DB::table('eps_planes')
            ->where('eps_id', $id)
            ->where('estado', 'activo')
            ->get();

        $eps = DB::table('eps')->find($id);

        return response()->json([
            'ok'        => true,
            'contratos' => $contratos,
            'planes'    => $planes,
            'eps'       => $eps,
        ]);
    }

    public function previewServicios(Request $request): JsonResponse
    {
        $epsId      = (int)$request->input('eps_id', 0);
        $contratoId = (int)$request->input('contrato_id', 0);
        $desde      = $request->input('fecha_desde', '');
        $hasta      = $request->input('fecha_hasta', '');

        if ($epsId <= 0) {
            return response()->json(['ok' => false, 'mensaje' => 'eps_id es obligatorio.'], 422);
        }

        $eps      = DB::table('eps')->find($epsId);
        $contrato = $contratoId > 0 ? DB::table('contratos_eps')->find($contratoId) : null;
        $plan     = DB::table('eps_planes')->where('eps_id', $epsId)->where('estado', 'activo')->first();

        $query = DB::table('servicios_prestados as sp')
            ->join('pacientes as p', 'p.id', '=', 'sp.paciente_id')
            ->where('p.eps', $epsId)
            ->where('sp.estado', 'atendido')
            ->where('sp.facturado', 0);

        if ($desde !== '') $query->where('sp.fecha_servicio', '>=', $desde);
        if ($hasta !== '') $query->where('sp.fecha_servicio', '<=', $hasta);
        if ($contratoId > 0) {
            $query->where(function ($q) use ($contratoId) {
                $q->where('sp.contrato_id', $contratoId)->orWhereNull('sp.contrato_id');
            });
        }

        $servicios = $query->select(
            'sp.id', 'sp.paciente_id', 'sp.codigo_cups', 'sp.nombre_servicio',
            'sp.fecha_servicio', 'sp.valor_servicio', 'sp.valor_copago', 'sp.valor_pagado_paciente',
            DB::raw("CONCAT(p.primer_nombre, ' ', p.primer_apellido) as paciente_nombre"),
            'p.identificacion as paciente_identificacion'
        )->orderBy('sp.fecha_servicio')->get();

        $pacientesIds = $servicios->pluck('paciente_id')->unique()->values();
        $polizas = DB::table('paciente_plan_eps')
            ->whereIn('paciente_id', $pacientesIds)
            ->where('estado', 'activo')
            ->orderBy('fecha_inicio', 'desc')
            ->get()
            ->groupBy('paciente_id')
            ->map(fn($rows) => $rows->first()->numero_poliza ?? '');

        // El valor a facturar a la EPS = valor_servicio - valor_copago (saldo tras copago del paciente)
        $itemsMap = [];
        foreach ($servicios as $s) {
            $key      = $s->codigo_cups ?: 'SIN_CUPS';
            $valorEps = max(0.0, (float)$s->valor_servicio - (float)$s->valor_copago);
            if (!isset($itemsMap[$key])) {
                $itemsMap[$key] = [
                    'codigo_cups'    => $s->codigo_cups,
                    'nombre'         => $s->nombre_servicio ?: 'Servicio de Salud',
                    'cantidad'       => 0,
                    'valor_unitario' => $valorEps,
                    'total'          => 0.0,
                    'unspsc'         => '85122200',
                    'servicios_ids'  => [],
                ];
            }
            $itemsMap[$key]['cantidad']++;
            $itemsMap[$key]['total'] += $valorEps;
            $itemsMap[$key]['servicios_ids'][] = $s->id;
        }

        $prestador = DB::table('prestadores_reps')->where('activo', 1)->orderBy('id')->first();

        $saludAuto = [];
        if ($contrato) {
            $modalidad = $this->mapModalidadPago((string)($contrato->tipo_contrato ?? ''));
            $saludAuto['modalidad_pago_id']    = $modalidad['id'];
            $saludAuto['modalidad_pago_value'] = $modalidad['value'];
            $saludAuto['numero_contrato']      = (string)($contrato->numero_contrato ?? '');
        }
        if ($eps) {
            $cobertura = $this->mapCoberturaPlan((string)($eps->regimen ?? ''));
            $saludAuto['cobertura_id']    = $cobertura['id'];
            $saludAuto['cobertura_value'] = $cobertura['value'];
        }
        if ($prestador) {
            $saludAuto['codigo_prestador'] = (string)($prestador->codigo_reps ?? '');
        }

        return response()->json([
            'ok'              => true,
            'eps'             => $eps,
            'contrato'        => $contrato,
            'plan'            => $plan,
            'prestador'       => $prestador,
            'items'           => array_values($itemsMap),
            'servicios'       => $servicios,
            'servicios_count' => $servicios->count(),
            'total'           => $servicios->sum(fn($s) => max(0.0, (float)$s->valor_servicio - (float)$s->valor_copago)),
            'polizas'         => $polizas,
            'salud_auto'      => $saludAuto,
        ]);
    }

    private function mapModalidadPago(string $tipo): array
    {
        return match (strtolower(trim($tipo))) {
            'capitado'                => ['id' => '02', 'value' => 'Capitado'],
            'global', 'pago_global'   => ['id' => '03', 'value' => 'Pago global prospectivo'],
            default                   => ['id' => '01', 'value' => 'Evento'],
        };
    }

    private function mapCoberturaPlan(string $regimen): array
    {
        $r = strtolower(trim(str_replace(["\r", "\n"], '', $regimen)));
        if (str_contains($r, 'prepag')) {
            return ['id' => '13', 'value' => 'Medicina Prepagada'];
        }
        return ['id' => '10', 'value' => 'Plan de Beneficios en Salud - PBS'];
    }

    private function parseDianStatusZipResponse(string $xml): array
    {
        $statusCode        = $this->extractXmlValueByLocalName($xml, 'StatusCode');
        $statusDescription = $this->extractXmlValueByLocalName($xml, 'StatusDescription');
        $statusMessage     = $this->extractXmlValueByLocalName($xml, 'StatusMessage');
        $isValidRaw        = $this->extractXmlValueByLocalName($xml, 'IsValid');

        $isValid = null;
        if ($isValidRaw !== null && $isValidRaw !== '') {
            $v       = strtolower(trim($isValidRaw));
            $isValid = in_array($v, ['true', '1', 'yes', 'si', 'sí'], true);
        }

        $errors = $this->extractXmlValuesByLocalName($xml, 'ErrorMessage');
        if (empty($errors)) {
            $errors = $this->extractXmlValuesByLocalName($xml, 'string');
        }

        $hayErrores = !empty($errors);
        $texto      = strtolower(trim(($statusDescription ?? '') . ' ' . ($statusMessage ?? '')));

        // El texto del StatusMessage tiene prioridad sobre IsValid.
        // DIAN habilitación devuelve IsValid=false (código 2) pero el mensaje dice "Aceptado"
        // cuando el set de prueba fue aprobado — son dos cosas distintas.
        $resultado = 'desconocido';
        if (str_contains($texto, 'acept') || str_contains($texto, 'aprob')) {
            $resultado = 'aceptado';
        } elseif ($isValid === true) {
            $resultado = 'aceptado';
        } elseif (str_contains($texto, 'rechaz') || str_contains($texto, 'no valid') || ($hayErrores && $isValid === false)) {
            $resultado = 'rechazado';
        } elseif ($isValid === false) {
            $resultado = 'rechazado';
        } elseif (str_contains($texto, 'proces') || str_contains($texto, 'en cola') || str_contains($texto, 'recib')) {
            $resultado = 'en_proceso';
        }

        return [
            'resultado'          => $resultado,
            'is_valid'           => $isValid,
            'status_code'        => $statusCode,
            'status_description' => $statusDescription,
            'status_message'     => $statusMessage,
            'errors'             => array_values(array_filter(array_map('trim', $errors), fn($s) => $s !== '')),
        ];
    }

    private function extractXmlValueByLocalName(string $xml, string $localName): ?string
    {
        if (trim($xml) === '') return null;
        if (!str_contains($xml, "<{$localName}") && !str_contains($xml, ":{$localName}")) {
            return null;
        }

        libxml_use_internal_errors(true);
        $doc = new \DOMDocument();
        if (!$doc->loadXML($xml)) {
            return null;
        }
        $xp    = new \DOMXPath($doc);
        $nodes = $xp->query("//*[local-name()='{$localName}']");
        if (!$nodes || $nodes->length === 0) return null;
        return trim((string)$nodes->item(0)->textContent);
    }

    private function extractXmlValuesByLocalName(string $xml, string $localName): array
    {
        if (trim($xml) === '') return [];
        if (!str_contains($xml, "<{$localName}") && !str_contains($xml, ":{$localName}")) {
            return [];
        }

        libxml_use_internal_errors(true);
        $doc = new \DOMDocument();
        if (!$doc->loadXML($xml)) {
            return [];
        }

        $xp    = new \DOMXPath($doc);
        $nodes = $xp->query("//*[local-name()='{$localName}']");
        if (!$nodes || $nodes->length === 0) return [];

        $out = [];
        foreach ($nodes as $n) {
            $out[] = (string)$n->textContent;
        }
        return $out;
    }

    private function getPayload(Request $request): array
    {
        $data = $request->all();
        $d    = (array)config('fe.defaults', []);

        $epsId        = (int)($data['eps_id'] ?? 0);
        $contratoId   = (int)($data['contrato_id'] ?? 0);
        $epsData      = null;
        $contratoData = null;
        $prestadorData = null;

        if ($epsId > 0) {
            $epsData       = DB::table('eps')->find($epsId);
            if ($contratoId > 0) {
                $contratoData = DB::table('contratos_eps')->find($contratoId);
            }
            $prestadorData = DB::table('prestadores_reps')->where('activo', 1)->orderBy('id')->first();
        }

        $prefix             = trim((string)($data['prefix'] ?? ($d['prefix'] ?? '')));
        $numeroFactura      = trim((string)($data['numero_factura'] ?? ($d['numero_factura'] ?? '')));
        $customizationId    = trim((string)($data['customization_id'] ?? ($d['customization_id'] ?? '10')));
        $profileExecutionId = trim((string)($data['profile_execution_id'] ?? ($d['profile_execution_id'] ?? '2')));
        $companyNit         = preg_replace('/\D+/', '', (string)($data['company_nit'] ?? ($d['company_nit'] ?? '')));
        $softwareId         = trim((string)($data['software_id'] ?? ($d['software_id'] ?? '')));
        $pin                = (string)($data['pin'] ?? ($d['pin'] ?? ''));
        $claveTecnica       = trim((string)($data['clave_tecnica'] ?? ($d['clave_tecnica'] ?? '')));
        $esSalud            = (int)($data['es_salud'] ?? 0) === 1;

        $customerNitRaw = $data['customer_nit'] ?? ($epsData ? ($epsData->nit ?? '') : ($d['customer_nit'] ?? ''));
        $customerNit    = preg_replace('/\D+/', '', (string)$customerNitRaw);

        if ($prefix === '' || $numeroFactura === '') {
            throw new \InvalidArgumentException('Prefijo y número de factura son obligatorios.');
        }
        if ($companyNit === '' || $customerNit === '') {
            throw new \InvalidArgumentException('NIT emisor y adquiriente son obligatorios (solo números).');
        }
        if ($softwareId === '' || $pin === '') {
            throw new \InvalidArgumentException('SoftwareID y PIN son obligatorios para calcular el SoftwareSecurityCode.');
        }
        if ($claveTecnica === '') {
            throw new \InvalidArgumentException('La clave técnica es obligatoria para calcular el CUFE.');
        }

        $saludManual   = (array)($data['salud'] ?? []);
        $saludDefaults = (array)($d['salud'] ?? []);

        $saludAuto = [];
        if ($contratoData) {
            $modalidad = $this->mapModalidadPago((string)($contratoData->tipo_contrato ?? ''));
            $saludAuto['modalidad_pago_id']    = $modalidad['id'];
            $saludAuto['modalidad_pago_value'] = $modalidad['value'];
            $saludAuto['numero_contrato']      = (string)($contratoData->numero_contrato ?? '');
        }
        if ($epsData) {
            $cobertura = $this->mapCoberturaPlan((string)($epsData->regimen ?? ''));
            $saludAuto['cobertura_id']    = $cobertura['id'];
            $saludAuto['cobertura_value'] = $cobertura['value'];
        }
        if ($prestadorData) {
            $saludAuto['codigo_prestador'] = (string)($prestadorData->codigo_reps ?? '');
        }

        $saludMerged = array_merge($saludAuto, $saludDefaults, array_filter($saludManual, fn($v) => $v !== '' && $v !== null));

        return [
            'prefix'               => $prefix,
            'numero_factura'       => $numeroFactura,
            'customization_id'     => $customizationId,
            'profile_execution_id' => $profileExecutionId,
            'company_nit'          => $companyNit,
            'customer_nit'         => $customerNit,
            'software_id'          => $softwareId,
            'pin'                  => $pin,
            'clave_tecnica'        => $claveTecnica,
            'es_salud'             => $esSalud,
            'eps_id'               => $epsId,
            'eps_data'             => $epsData,
            'contrato_data'        => $contratoData,
            'items'                => is_array($data['items'] ?? null) ? $data['items'] : null,
            'salud'                => [
                'codigo_prestador'     => trim((string)($saludMerged['codigo_prestador'] ?? '')),
                'numero_contrato'      => trim((string)($saludMerged['numero_contrato'] ?? '')),
                'modalidad_pago_id'    => trim((string)($saludMerged['modalidad_pago_id'] ?? '')),
                'modalidad_pago_value' => trim((string)($saludMerged['modalidad_pago_value'] ?? '')),
                'cobertura_id'         => trim((string)($saludMerged['cobertura_id'] ?? '')),
                'cobertura_value'      => trim((string)($saludMerged['cobertura_value'] ?? '')),
                'numero_poliza'        => trim((string)($saludMerged['numero_poliza'] ?? '')),
            ],
        ];
    }

    private function buildInvoiceData(array $p): array
    {
        $d         = (array)config('fe.defaults', []);
        $companyD  = (array)($d['company'] ?? []);
        $customerD = (array)($d['customer'] ?? []);
        $paymentD  = (array)($d['payment'] ?? []);

        $prefix        = $p['prefix'];
        $numeroFactura = $p['numero_factura'];
        $companyNit    = $p['company_nit'];
        $customerNit   = $p['customer_nit'];
        $softwareId    = $p['software_id'];
        $pin           = $p['pin'];

        $invoiceAuthorization    = (string)($d['invoice_authorization'] ?? '18760000001');
        $startDate               = (string)($d['auth_start'] ?? '2019-01-19');
        $endDate                 = (string)($d['auth_end'] ?? '2030-01-19');
        $from                    = (string)($d['auth_from'] ?? '1');
        $to                      = (string)($d['auth_to'] ?? '5000000');
        $authorizationProviderId = (string)($d['auth_provider_nit'] ?? '800197268');

        $companyName           = (string)($companyD['name'] ?? 'PUMAREJO PRASCA MARIA ISABEL');
        $supplierAccountId     = (string)($companyD['additional_account_id'] ?? '2');
        $companyCity           = (string)($companyD['city'] ?? 'VALLEDUPAR');
        $companyDepartment     = (string)($companyD['dept'] ?? 'Cesar');
        $companyDepartmentCode = (string)($companyD['dept_code'] ?? '20');
        $companyAddress        = (string)($companyD['address'] ?? 'CR 11 11 07 BRR SAN JOAQUIN LC 102');
        $taxLevelCode          = (string)($companyD['tax_level_code'] ?? 'R-99-PN');
        $cityCode              = (string)($companyD['city_code'] ?? '20001');
        $companyPhone          = (string)($companyD['phone'] ?? '');
        $companyEmail          = (string)($companyD['email'] ?? '');

        $additionalAccountID    = (string)($customerD['additional_account_id'] ?? '1');
        $customerName           = (string)($customerD['name'] ?? '');
        $customerTaxLevelCode   = (string)($customerD['tax_level_code'] ?? 'O-13');
        $customerCityCode       = (string)($customerD['city_code'] ?? '11001');
        $customerCity           = (string)($customerD['city'] ?? 'Bogotá, D.c.');
        $customerDepartment     = (string)($customerD['dept'] ?? 'Bogotá');
        $customerDepartmentCode = (string)($customerD['dept_code'] ?? '11');
        $customerAddress        = (string)($customerD['address'] ?? 'Av. #97 - 13');
        $customerIdCode         = (string)($customerD['id_code'] ?? '31');

        if (!empty($p['eps_data'])) {
            $epsDb                  = $p['eps_data'];
            $customerName           = strtoupper(trim((string)($epsDb->entidad ?? $customerName)));
            $additionalAccountID    = '1';
            $customerIdCode         = '31';
            $customerTaxLevelCode   = 'R-99-PN';
            $customerCityCode       = '11001';
            $customerCity           = 'Bogotá, D.C.';
            $customerDepartment     = 'Bogotá';
            $customerDepartmentCode = '11';
            $customerAddress        = 'Colombia';
        }

        $paymentMeansID   = (string)($paymentD['means_id'] ?? '1');
        $paymentMeansCode = (string)($paymentD['means_code'] ?? '42');
        $bankTransfer     = (string)($d['bank_transfer'] ?? '');
        $softwareName     = (string)($d['software_name'] ?? '');
        $softwareMaker    = (string)($d['software_maker'] ?? '');

        $customizationId    = $p['customization_id'];
        $profileExecutionId = $p['profile_execution_id'];
        $invoiceTypeCode    = '01';
        $id                 = $prefix . $numeroFactura;
        $softwareSecurityCode = hash('sha384', $softwareId . $pin . $id);

        $issueDate              = Carbon::now()->format('Y-m-d');
        $issueTime              = Carbon::now()->format('H:i:s') . '-05:00';
        $invoicePeriodStartDate = Carbon::now()->startOfMonth()->toDateString();
        $invoicePeriodEndDate   = Carbon::now()->endOfMonth()->toDateString();

        $rawItems = is_array($p['items'] ?? null) && !empty($p['items']) ? $p['items'] : null;
        if ($rawItems !== null) {
            $items = [];
            foreach ($rawItems as $i => $raw) {
                $qty       = max(1, (float)($raw['cantidad'] ?? 1));
                $unitPrice = (float)($raw['valor_unitario'] ?? 0);
                $lineTotal = (float)($raw['total'] ?? ($qty * $unitPrice));
                $items[]   = [
                    'lineID'                 => (string)($i + 1),
                    'lineQuantity'           => number_format($qty, 6, '.', ''),
                    'lineTax'                => '0.00',
                    'lineTaxPercent'         => '0.00',
                    'lineTaxTotal'           => '0.00',
                    'lineItemName'           => (string)($raw['nombre'] ?? 'Servicio de Salud'),
                    'lineTotal'              => number_format($lineTotal, 2, '.', ''),
                    'lineAllowanceAmount'    => '0.00',
                    'itemPrice'              => number_format($unitPrice, 2, '.', ''),
                    'linePercentageDiscount' => '0.00',
                    'unitCode'               => '94',
                    'unspsc'                 => (string)($raw['unspsc'] ?? '85122200'),
                    'seller_item_id'         => (string)($raw['codigo_cups'] ?? 'SERV-001'),
                ];
            }
        } else {
            $items = [[
                'lineID'                 => '1',
                'lineQuantity'           => '1.000000',
                'lineTax'                => '0.00',
                'lineTaxPercent'         => '0.00',
                'lineTaxTotal'           => '0.00',
                'lineItemName'           => $p['es_salud'] ? '01 SALUD-PSICOLOGÍA CLÍNICA' : 'SERVICIO DE PRUEBA',
                'lineTotal'              => '1000.00',
                'lineAllowanceAmount'    => '0.00',
                'itemPrice'              => '1000.00',
                'linePercentageDiscount' => '0.00',
                'unitCode'               => $p['es_salud'] ? '94' : 'EA',
                'unspsc'                 => $p['es_salud'] ? '85122200' : '85122000',
                'seller_item_id'         => 'SERV-001',
            ]];
        }

        $totalBase           = array_sum(array_map(fn($it) => (float)$it['lineTotal'], $items));
        $lineExtensionAmount = number_format($totalBase, 2, '.', '');
        $taxableAmount       = $lineExtensionAmount;
        $taxAmount           = '0.00';
        $percent             = '0.00';
        $totalTaxAmount      = '0.00';
        $taxExclusiveAmount  = $lineExtensionAmount;
        $taxInclusiveAmount  = $lineExtensionAmount;
        $payableAmount       = $lineExtensionAmount;

        $dvCompanyNit  = $this->calcularDV($companyNit);
        $dvCustomerNit = $this->calcularDV($customerNit);
        $dvDian        = $this->calcularDV($authorizationProviderId);

        $claveTecnica = $p['clave_tecnica'];
        $cufeString   =
            $id . $issueDate . $issueTime .
            number_format((float)$lineExtensionAmount, 2, '.', '') .
            '01' . number_format((float)$taxAmount, 2, '.', '') .
            '04' . '0.00' .
            '03' . '0.00' .
            number_format((float)$payableAmount, 2, '.', '') .
            $companyNit . $customerNit . $claveTecnica . $profileExecutionId;
        $uuid = hash('sha384', $cufeString);

        $qrBase = (string)config('fe.dian.catalogo_qr_base',
            'https://catalogo-vpfe-hab.dian.gov.co/Document/searchqr?documentKey=');
        $qrCode = "NroFactura=$id\nNitFacturador=$companyNit\nNitAdquiriente=$customerNit\n"
            . "FechaFactura=$issueDate\nValorTotalFactura=$payableAmount\nCUFE=$uuid\n"
            . "URL={$qrBase}{$uuid}";

        return [
            'id'                     => $id,
            'prefix'                 => $prefix,
            'numero_factura'         => $numeroFactura,
            'cufe'                   => $uuid,
            'issue_date'             => $issueDate,
            'issue_time'             => $issueTime,
            'period_start'           => $invoicePeriodStartDate,
            'period_end'             => $invoicePeriodEndDate,
            'customization_id'       => $customizationId,
            'profile_execution_id'   => $profileExecutionId,
            'invoice_type_code'      => $invoiceTypeCode,
            'invoice_authorization'  => $invoiceAuthorization,
            'auth_start'             => $startDate,
            'auth_end'               => $endDate,
            'auth_from'              => $from,
            'auth_to'                => $to,
            'auth_provider_nit'      => $authorizationProviderId,
            'dv_dian'                => $dvDian,
            'company_name'           => $companyName,
            'company_nit'            => $companyNit,
            'dv_company'             => $dvCompanyNit,
            'company_city'           => $companyCity,
            'company_department'     => $companyDepartment,
            'company_dept_code'      => $companyDepartmentCode,
            'company_address'        => $companyAddress,
            'company_city_code'      => $cityCode,
            'company_phone'          => $companyPhone,
            'company_email'          => $companyEmail,
            'supplier_account_id'    => $supplierAccountId,
            'tax_level_code'         => $taxLevelCode,
            'customer_name'          => $customerName,
            'customer_nit'           => $customerNit,
            'dv_customer'            => $dvCustomerNit,
            'customer_city'          => $customerCity,
            'customer_dept'          => $customerDepartment,
            'customer_dept_code'     => $customerDepartmentCode,
            'customer_address'       => $customerAddress,
            'customer_city_code'     => $customerCityCode,
            'customer_id_code'       => $customerIdCode,
            'customer_tax_level'     => $customerTaxLevelCode,
            'additional_account_id'  => $additionalAccountID,
            'payment_means_id'       => $paymentMeansID,
            'payment_means_code'     => $paymentMeansCode,
            'bank_transfer'          => $bankTransfer,
            'software_id'            => $softwareId,
            'software_security_code' => $softwareSecurityCode,
            'software_name'          => $softwareName,
            'software_maker'         => $softwareMaker,
            'clave_tecnica'          => $claveTecnica,
            'qr_string'              => $qrCode,
            'qr_base_url'            => $qrBase,
            'items'                  => $items,
            'line_count'             => count($items),
            'line_extension_amount'  => $lineExtensionAmount,
            'taxable_amount'         => $taxableAmount,
            'tax_amount'             => $taxAmount,
            'percent'                => $percent,
            'total_tax_amount'       => $totalTaxAmount,
            'tax_exclusive_amount'   => $taxExclusiveAmount,
            'tax_inclusive_amount'   => $taxInclusiveAmount,
            'payable_amount'         => $payableAmount,
            'es_salud'               => $p['es_salud'] ?? false,
            'salud'                  => $p['salud'] ?? [],
        ];
    }

    private function buildInvoiceXml(array $p): string
    {
        $inv = $this->buildInvoiceData($p);

        $xml  = $this->generateHeaderXML();
        $xml .= $this->generateExtensionsXML(
            $inv['invoice_authorization'], $inv['auth_start'], $inv['auth_end'],
            $inv['prefix'], $inv['auth_from'], $inv['auth_to'],
            $inv['company_nit'], $inv['dv_company'], $inv['software_id'],
            $inv['software_security_code'], $inv['auth_provider_nit'], $inv['dv_dian'],
            $inv['qr_string'], $p
        );
        $xml .= $this->generateVersionXML(
            $inv['customization_id'], $inv['profile_execution_id'],
            $inv['id'], $inv['cufe'], $inv['issue_date'], $inv['issue_time'],
            $inv['invoice_type_code'], (string)$inv['line_count'],
            $inv['period_start'], $inv['period_end']
        );
        $xml .= $this->generateSupplierXML(
            $inv['company_name'], $inv['supplier_account_id'],
            $inv['company_city_code'], $inv['company_city'],
            $inv['company_department'], $inv['company_dept_code'],
            $inv['company_address'], $inv['company_nit'], $inv['dv_company'],
            $inv['tax_level_code'], $inv['company_city_code'], '01', 'IVA', $inv['prefix']
        );
        $xml .= $this->generateCustomerXML(
            $inv['additional_account_id'], $inv['customer_name'],
            $inv['customer_tax_level'], $inv['customer_city_code'],
            $inv['customer_city'], $inv['customer_dept'], $inv['customer_dept_code'],
            $inv['customer_address'], $inv['customer_nit'], $inv['dv_customer'],
            $inv['customer_id_code']
        );
        $xml .= $this->generateTotalsXML(
            $inv['payment_means_id'], $inv['payment_means_code'],
            $inv['taxable_amount'], $inv['tax_amount'], $inv['percent'],
            $inv['total_tax_amount'], $inv['line_extension_amount'],
            $inv['tax_exclusive_amount'], $inv['tax_inclusive_amount'], $inv['payable_amount']
        );
        $xml .= $this->generateInvoiceLinesXML($inv['items']);

        return $xml;
    }

    private function generateHeaderXML(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2"
 xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"
 xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2"
 xmlns:ds="http://www.w3.org/2000/09/xmldsig#"
 xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2"
 xmlns:sts="http://www.dian.gov.co/contratos/facturaelectronica/v1/Structures"
 xmlns:xades="http://uri.etsi.org/01903/v1.3.2#"
 xmlns:xades141="http://uri.etsi.org/01903/v1.4.1#"
 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 xsi:schemaLocation="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2 http://docs.oasis-open.org/ubl/os-UBL-2.1/xsd/maindoc/UBL-Invoice-2.1.xsd">
';
    }

    private function generateExtensionsXML(
        string $invoiceAuthorization,
        string $startDate,
        string $endDate,
        string $prefix,
        string $from,
        string $to,
        string $companyNit,
        string $dvCompanyNit,
        string $softwareId,
        string $softwareSecurityCode,
        string $authorizationProviderId,
        string $dvDian,
        string $qrCode,
        array $payload
    ): string {
        $saludXml = '';
        if (!empty($payload['es_salud'])) {
            $saludXml = $this->generateSectorSaludXmlBlock($payload['salud'] ?? []);
        }

        return "<ext:UBLExtensions>
  <ext:UBLExtension>
    <ext:ExtensionContent>
      <sts:DianExtensions>
        <sts:InvoiceControl>
          <sts:InvoiceAuthorization>{$invoiceAuthorization}</sts:InvoiceAuthorization>
          <sts:AuthorizationPeriod>
            <cbc:StartDate>{$startDate}</cbc:StartDate>
            <cbc:EndDate>{$endDate}</cbc:EndDate>
          </sts:AuthorizationPeriod>
          <sts:AuthorizedInvoices>
            <sts:Prefix>{$prefix}</sts:Prefix>
            <sts:From>{$from}</sts:From>
            <sts:To>{$to}</sts:To>
          </sts:AuthorizedInvoices>
        </sts:InvoiceControl>
        <sts:InvoiceSource>
          <cbc:IdentificationCode listAgencyID='6' listAgencyName='United Nations Economic Commission for Europe' listSchemeURI='urn:oasis:names:specification:ubl:codelist:gc:CountryIdentificationCode-2.1'>CO</cbc:IdentificationCode>
        </sts:InvoiceSource>
        <sts:SoftwareProvider>
          <sts:ProviderID schemeAgencyID='195' schemeAgencyName='CO, DIAN (Dirección de Impuestos y Aduanas Nacionales)' schemeID='{$dvCompanyNit}' schemeName='31'>{$companyNit}</sts:ProviderID>
          <sts:SoftwareID schemeAgencyID='195' schemeAgencyName='CO, DIAN (Dirección de Impuestos y Aduanas Nacionales)'>{$softwareId}</sts:SoftwareID>
        </sts:SoftwareProvider>
        <sts:SoftwareSecurityCode schemeAgencyID='195' schemeAgencyName='CO, DIAN (Dirección de Impuestos y Aduanas Nacionales)'>{$softwareSecurityCode}</sts:SoftwareSecurityCode>
        <sts:AuthorizationProvider>
          <sts:AuthorizationProviderID schemeAgencyID='195' schemeAgencyName='CO, DIAN (Dirección de Impuestos y Aduanas Nacionales)' schemeID='{$dvDian}' schemeName='31'>{$authorizationProviderId}</sts:AuthorizationProviderID>
        </sts:AuthorizationProvider>
        <sts:QRCode>{$qrCode}</sts:QRCode>
      </sts:DianExtensions>
{$saludXml}
    </ext:ExtensionContent>
  </ext:UBLExtension>
  <ext:UBLExtension>
    <ext:ExtensionContent></ext:ExtensionContent>
  </ext:UBLExtension>
</ext:UBLExtensions>";
    }

    private function generateSectorSaludXmlBlock(array $salud): string
    {
        $codigoPrestador = htmlspecialchars((string)($salud['codigo_prestador'] ?? ''), ENT_XML1);
        $numeroContrato  = htmlspecialchars((string)($salud['numero_contrato'] ?? ''), ENT_XML1);
        $numeroPoliza    = htmlspecialchars((string)($salud['numero_poliza'] ?? ''), ENT_XML1);
        $modalidadId     = htmlspecialchars((string)($salud['modalidad_pago_id'] ?? ''), ENT_XML1);
        $modalidadValue  = htmlspecialchars((string)($salud['modalidad_pago_value'] ?? ''), ENT_XML1);
        $coberturaId     = htmlspecialchars((string)($salud['cobertura_id'] ?? ''), ENT_XML1);
        $coberturaValue  = htmlspecialchars((string)($salud['cobertura_value'] ?? ''), ENT_XML1);

        return "<CustomTagGeneral>
  <Name>Responsable</Name>
  <Value>https://www.sispro.gov.co/central-financiamiento/Pages/facturacion-electronica.aspx</Value>
  <Name>Tipo, identificador:año del acto administrativo</Name>
  <Value>Resolución 2275:2023</Value>
  <Interoperabilidad>
    <Group schemeName=\"Sector Salud\">
      <Collection schemeName=\"Usuario\">
        <AdditionalInformation>
          <Name>CODIGO_PRESTADOR</Name>
          <Value>{$codigoPrestador}</Value>
        </AdditionalInformation>
        <AdditionalInformation>
          <Name>MODALIDAD_PAGO</Name>
          <Value schemeID=\"{$modalidadId}\" schemeName=\"salud_modalidad_pago.gc\">{$modalidadValue}</Value>
        </AdditionalInformation>
        <AdditionalInformation>
          <Name>COBERTURA_PLAN_BENEFICIOS</Name>
          <Value schemeID=\"{$coberturaId}\" schemeName=\"salud_cobertura.gc\">{$coberturaValue}</Value>
        </AdditionalInformation>
        <AdditionalInformation>
          <Name>NUMERO_CONTRATO</Name>
          <Value>{$numeroContrato}</Value>
        </AdditionalInformation>
        <AdditionalInformation>
          <Name>NUMERO_POLIZA</Name>
          <Value>{$numeroPoliza}</Value>
        </AdditionalInformation>
      </Collection>
    </Group>
  </Interoperabilidad>
</CustomTagGeneral>";
    }

    private function generateVersionXML(
        string $customizationId,
        string $profileExecutionId,
        string $id,
        string $uuid,
        string $issueDate,
        string $issueTime,
        string $invoiceTypeCode,
        string $lineCountNumeric,
        string $periodStart,
        string $periodEnd
    ): string {
        return "<cbc:UBLVersionID>UBL 2.1</cbc:UBLVersionID>
<cbc:CustomizationID>{$customizationId}</cbc:CustomizationID>
<cbc:ProfileID>DIAN 2.1: Factura Electrónica de Venta</cbc:ProfileID>
<cbc:ProfileExecutionID>{$profileExecutionId}</cbc:ProfileExecutionID>
<cbc:ID>{$id}</cbc:ID>
<cbc:UUID schemeID='{$profileExecutionId}' schemeName='CUFE-SHA384'>{$uuid}</cbc:UUID>
<cbc:IssueDate>{$issueDate}</cbc:IssueDate>
<cbc:IssueTime>{$issueTime}</cbc:IssueTime>
<cbc:InvoiceTypeCode>{$invoiceTypeCode}</cbc:InvoiceTypeCode>
<cbc:DocumentCurrencyCode listAgencyID='6' listAgencyName='United Nations Economic Commission for Europe' listID='ISO 4217 Alpha'>COP</cbc:DocumentCurrencyCode>
<cbc:LineCountNumeric>{$lineCountNumeric}</cbc:LineCountNumeric>
<cac:InvoicePeriod>
  <cbc:StartDate>{$periodStart}</cbc:StartDate>
  <cbc:EndDate>{$periodEnd}</cbc:EndDate>
</cac:InvoicePeriod>";
    }

    private function generateSupplierXML(
        string $companyName,
        string $additionalAccountId,
        string $physCityCode,
        string $companyCity,
        string $companyDepartment,
        string $companyDepartmentCode,
        string $companyAddress,
        string $companyNit,
        string $dvCompanyNit,
        string $taxLevelCode,
        string $registrationCityCode,
        string $taxSchemeId,
        string $taxSchemeName,
        string $billingPrefix
    ): string {
        return "<cac:AccountingSupplierParty>
  <cbc:AdditionalAccountID schemeAgencyID='195'>{$additionalAccountId}</cbc:AdditionalAccountID>
  <cac:Party>
    <cac:PartyName>
      <cbc:Name>{$companyName}</cbc:Name>
    </cac:PartyName>
    <cac:PhysicalLocation>
      <cac:Address>
        <cbc:ID>{$physCityCode}</cbc:ID>
        <cbc:CityName>{$companyCity}</cbc:CityName>
        <cbc:CountrySubentity>{$companyDepartment}</cbc:CountrySubentity>
        <cbc:CountrySubentityCode>{$companyDepartmentCode}</cbc:CountrySubentityCode>
        <cac:AddressLine>
          <cbc:Line>{$companyAddress}</cbc:Line>
        </cac:AddressLine>
        <cac:Country>
          <cbc:IdentificationCode>CO</cbc:IdentificationCode>
          <cbc:Name languageID='es'>Colombia</cbc:Name>
        </cac:Country>
      </cac:Address>
    </cac:PhysicalLocation>
    <cac:PartyTaxScheme>
      <cbc:RegistrationName>{$companyName}</cbc:RegistrationName>
      <cbc:CompanyID schemeAgencyID='195' schemeAgencyName='CO, DIAN (Dirección de Impuestos y Aduanas Nacionales)' schemeID='{$dvCompanyNit}' schemeName='31'>{$companyNit}</cbc:CompanyID>
      <cbc:TaxLevelCode listName='49'>{$taxLevelCode}</cbc:TaxLevelCode>
      <cac:RegistrationAddress>
        <cbc:ID>{$registrationCityCode}</cbc:ID>
        <cbc:CityName>{$companyCity}</cbc:CityName>
        <cbc:CountrySubentity>{$companyDepartment}</cbc:CountrySubentity>
        <cbc:CountrySubentityCode>{$companyDepartmentCode}</cbc:CountrySubentityCode>
        <cac:AddressLine>
          <cbc:Line>{$companyAddress}</cbc:Line>
        </cac:AddressLine>
        <cac:Country>
          <cbc:IdentificationCode>CO</cbc:IdentificationCode>
          <cbc:Name languageID='es'>Colombia</cbc:Name>
        </cac:Country>
      </cac:RegistrationAddress>
      <cac:TaxScheme>
        <cbc:ID>{$taxSchemeId}</cbc:ID>
        <cbc:Name>{$taxSchemeName}</cbc:Name>
      </cac:TaxScheme>
    </cac:PartyTaxScheme>
    <cac:PartyLegalEntity>
      <cbc:RegistrationName>{$companyName}</cbc:RegistrationName>
      <cbc:CompanyID schemeAgencyID='195' schemeAgencyName='CO, DIAN (Dirección de Impuestos y Aduanas Nacionales)' schemeID='{$dvCompanyNit}' schemeName='31'>{$companyNit}</cbc:CompanyID>
      <cac:CorporateRegistrationScheme>
        <cbc:ID>{$billingPrefix}</cbc:ID>
      </cac:CorporateRegistrationScheme>
    </cac:PartyLegalEntity>
  </cac:Party>
</cac:AccountingSupplierParty>";
    }

    private function generateCustomerXML(
        string $additionalAccountID,
        string $customerName,
        string $taxLevelCode,
        string $customerCityCode,
        string $customerCity,
        string $customerDepartment,
        string $customerDepartmentCode,
        string $customerAddress,
        string $customerNit,
        string $dvCustomerNit,
        string $customerIdCode
    ): string {
        return "<cac:AccountingCustomerParty>
  <cbc:AdditionalAccountID schemeAgencyID='195'>{$additionalAccountID}</cbc:AdditionalAccountID>
  <cac:Party>
    <cac:PartyName>
      <cbc:Name>{$customerName}</cbc:Name>
    </cac:PartyName>
    <cac:PhysicalLocation>
      <cac:Address>
        <cbc:ID>{$customerCityCode}</cbc:ID>
        <cbc:CityName>{$customerCity}</cbc:CityName>
        <cbc:CountrySubentity>{$customerDepartment}</cbc:CountrySubentity>
        <cbc:CountrySubentityCode>{$customerDepartmentCode}</cbc:CountrySubentityCode>
        <cac:AddressLine>
          <cbc:Line>{$customerAddress}</cbc:Line>
        </cac:AddressLine>
        <cac:Country>
          <cbc:IdentificationCode>CO</cbc:IdentificationCode>
          <cbc:Name languageID='es'>Colombia</cbc:Name>
        </cac:Country>
      </cac:Address>
    </cac:PhysicalLocation>
    <cac:PartyTaxScheme>
      <cbc:RegistrationName>{$customerName}</cbc:RegistrationName>
      <cbc:CompanyID schemeAgencyID='195' schemeAgencyName='CO, DIAN (Dirección de Impuestos y Aduanas Nacionales)' schemeName='{$customerIdCode}' schemeID='{$dvCustomerNit}'>{$customerNit}</cbc:CompanyID>
      <cbc:TaxLevelCode listName='49'>{$taxLevelCode}</cbc:TaxLevelCode>
      <cac:TaxScheme>
        <cbc:ID>01</cbc:ID>
        <cbc:Name>IVA</cbc:Name>
      </cac:TaxScheme>
    </cac:PartyTaxScheme>
    <cac:PartyLegalEntity>
      <cbc:RegistrationName>{$customerName}</cbc:RegistrationName>
      <cbc:CompanyID schemeAgencyID='195' schemeAgencyName='CO, DIAN (Dirección de Impuestos y Aduanas Nacionales)' schemeID='{$dvCustomerNit}' schemeName='{$customerIdCode}'>{$customerNit}</cbc:CompanyID>
    </cac:PartyLegalEntity>
  </cac:Party>
</cac:AccountingCustomerParty>";
    }

    private function generateTotalsXML(
        string $paymentMeansID,
        string $paymentMeansCode,
        string $taxableAmount,
        string $taxAmount,
        string $percent,
        string $totalTaxAmount,
        string $lineExtensionAmount,
        string $taxExclusiveAmount,
        string $taxInclusiveAmount,
        string $payableAmount
    ): string {
        return "<cac:PaymentMeans>
  <cbc:ID>{$paymentMeansID}</cbc:ID>
  <cbc:PaymentMeansCode>{$paymentMeansCode}</cbc:PaymentMeansCode>
</cac:PaymentMeans>
<cac:TaxTotal>
  <cbc:TaxAmount currencyID='COP'>{$totalTaxAmount}</cbc:TaxAmount>
  <cac:TaxSubtotal>
    <cbc:TaxableAmount currencyID='COP'>{$taxableAmount}</cbc:TaxableAmount>
    <cbc:TaxAmount currencyID='COP'>{$taxAmount}</cbc:TaxAmount>
    <cac:TaxCategory>
      <cbc:Percent>{$percent}</cbc:Percent>
      <cac:TaxScheme>
        <cbc:ID>01</cbc:ID>
        <cbc:Name>IVA</cbc:Name>
      </cac:TaxScheme>
    </cac:TaxCategory>
  </cac:TaxSubtotal>
</cac:TaxTotal>
<cac:LegalMonetaryTotal>
  <cbc:LineExtensionAmount currencyID='COP'>{$lineExtensionAmount}</cbc:LineExtensionAmount>
  <cbc:TaxExclusiveAmount currencyID='COP'>{$taxExclusiveAmount}</cbc:TaxExclusiveAmount>
  <cbc:TaxInclusiveAmount currencyID='COP'>{$taxInclusiveAmount}</cbc:TaxInclusiveAmount>
  <cbc:PayableAmount currencyID='COP'>{$payableAmount}</cbc:PayableAmount>
</cac:LegalMonetaryTotal>";
    }

    private function generateInvoiceLinesXML(array $items): string
    {
        $xml = '';
        foreach ($items as $it) {
            $lineID    = $it['lineID'];
            $qty       = $it['lineQuantity'];
            $unitCode  = $it['unitCode'] ?? 'EA';
            $baseAmount = $it['itemPrice'];
            $taxTotal  = $it['lineTaxTotal'];
            $taxable   = $baseAmount;
            $taxAmount = $it['lineTax'];
            $taxPercent = $it['lineTaxPercent'];
            $desc      = htmlspecialchars((string)$it['lineItemName'], ENT_XML1);
            $discPct   = $it['linePercentageDiscount'];
            $allowAmt  = $it['lineAllowanceAmount'];
            $unspsc    = (string)($it['unspsc'] ?? '85122000');
            $sellerItemId = htmlspecialchars((string)($it['seller_item_id'] ?? 'SERV-001'), ENT_XML1);

            $xml .= "<cac:InvoiceLine>
  <cbc:ID>{$lineID}</cbc:ID>
  <cbc:InvoicedQuantity unitCode='{$unitCode}'>{$qty}</cbc:InvoicedQuantity>
  <cbc:LineExtensionAmount currencyID='COP'>{$baseAmount}</cbc:LineExtensionAmount>
  <cbc:FreeOfChargeIndicator>false</cbc:FreeOfChargeIndicator>
  <cac:AllowanceCharge>
    <cbc:ID>1</cbc:ID>
    <cbc:ChargeIndicator>false</cbc:ChargeIndicator>
    <cbc:MultiplierFactorNumeric>{$discPct}</cbc:MultiplierFactorNumeric>
    <cbc:Amount currencyID='COP'>{$allowAmt}</cbc:Amount>
    <cbc:BaseAmount currencyID='COP'>{$baseAmount}</cbc:BaseAmount>
  </cac:AllowanceCharge>
  <cac:TaxTotal>
    <cbc:TaxAmount currencyID='COP'>{$taxTotal}</cbc:TaxAmount>
    <cac:TaxSubtotal>
      <cbc:TaxableAmount currencyID='COP'>{$taxable}</cbc:TaxableAmount>
      <cbc:TaxAmount currencyID='COP'>{$taxAmount}</cbc:TaxAmount>
      <cac:TaxCategory>
        <cbc:Percent>{$taxPercent}</cbc:Percent>
        <cac:TaxScheme>
          <cbc:ID>01</cbc:ID>
          <cbc:Name>IVA</cbc:Name>
        </cac:TaxScheme>
      </cac:TaxCategory>
    </cac:TaxSubtotal>
  </cac:TaxTotal>
  <cac:Item>
    <cbc:Description>{$desc}</cbc:Description>
    <cac:SellersItemIdentification>
      <cbc:ID>{$sellerItemId}</cbc:ID>
    </cac:SellersItemIdentification>
    <cac:StandardItemIdentification>
      <cbc:ID schemeID='001' schemeAgencyID='10' schemeAgencyName='GS1 Colombia' schemeName='UNSPSC'>{$unspsc}</cbc:ID>
    </cac:StandardItemIdentification>
  </cac:Item>
  <cac:Price>
    <cbc:PriceAmount currencyID='COP'>{$baseAmount}</cbc:PriceAmount>
    <cbc:BaseQuantity unitCode='{$unitCode}'>{$qty}</cbc:BaseQuantity>
  </cac:Price>
</cac:InvoiceLine>";
        }

        $xml .= "</Invoice>";
        return $xml;
    }

    private function signXml(string $xmlString): string
    {
        $certPath = $this->resolvePath((string)config('fe.cert.p12_path', public_path('key/certificado.p12')));
        $password = (string)config('fe.cert.p12_password', '');

        if ($password === '') {
            throw new \RuntimeException('Configura FE_CERT_P12_PASSWORD en el .env.');
        }
        if (!is_file($certPath)) {
            throw new \RuntimeException("No se encontró el certificado .p12 en: {$certPath}");
        }

        $signInvoice = new SignInvoice($certPath, $password, $xmlString);
        return (string)$signInvoice->xml;
    }

    private function enviarDian(string $xmlString)
    {
        $certPath = $this->resolvePath((string)config('fe.cert.p12_path', public_path('key/certificado.p12')));
        $password = (string)config('fe.cert.p12_password', '');

        if ($password === '') {
            throw new \RuntimeException('Configura FE_CERT_P12_PASSWORD en el .env.');
        }
        if (!is_file($certPath)) {
            throw new \RuntimeException("No se encontró el certificado .p12 en: {$certPath}");
        }

        preg_match('/<cbc:ID>([^<]+)<\/cbc:ID>/i', $xmlString, $m);
        $docId   = preg_replace('/[^A-Za-z0-9]/', '', $m[1] ?? 'FACTURA');
        $zipName = $docId . '.zip';
        $xmlName = $docId . '.xml';

        $zipPath = storage_path($zipName);
        $zip     = new \ZipArchive();
        $zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        $zip->addFromString($xmlName, $xmlString);
        $zip->close();

        $to   = (string)config('fe.dian.url', 'https://vpfe-hab.dian.gov.co/WcfDianCustomerServices.svc');
        $mode = strtolower(trim((string)config('fe.dian.send_mode', 'sync')));

        if ($mode === 'test_set') {
            $testSetId = (string)config('fe.dian.test_set_id', '');
            if ($testSetId === '') {
                throw new \RuntimeException('Configura FE_DIAN_TEST_SET_ID en el .env para el modo test_set.');
            }
            $tpl = new SendTestSetAsync($certPath, $password);
            $tpl->testSetId = $testSetId;
        } elseif ($mode === 'async') {
            $tpl = new SendBillAsync($certPath, $password);
        } else {
            $tpl = new SendBillSync($certPath, $password);
        }

        $tpl->To          = $to;
        $tpl->fileName    = $zipName;
        $tpl->contentFile = base64_encode((string)file_get_contents($zipPath));
        $tpl->signToSend();

        $client = new Client($tpl);
        return $client->getResponse();
    }

    private function generarQrDataUri(string $text): string
    {
        // Try endroid/qr-code if installed
        if (class_exists(\Endroid\QrCode\Builder\Builder::class)) {
            try {
                $result = \Endroid\QrCode\Builder\Builder::create()
                    ->writer(new \Endroid\QrCode\Writer\PngWriter())
                    ->data($text)
                    ->size(150)
                    ->margin(4)
                    ->build();
                return 'data:image/png;base64,' . base64_encode($result->getString());
            } catch (\Throwable $e) {
                // fall through to GD fallback
            }
        }

        // GD fallback: white image with the URL text
        if (!function_exists('imagecreatetruecolor')) {
            return '';
        }
        $w  = 200; $h = 200;
        $im = imagecreatetruecolor($w, $h);
        $white  = imagecolorallocate($im, 255, 255, 255);
        $black  = imagecolorallocate($im, 0, 0, 0);
        $gray   = imagecolorallocate($im, 100, 100, 100);
        imagefill($im, 0, 0, $white);
        imagerectangle($im, 0, 0, $w - 1, $h - 1, $black);

        imagestring($im, 3, 10, 10, 'Consultar en DIAN:', $black);
        // Word-wrap the URL into chunks of ~30 chars
        $chunks = str_split($text, 28);
        $y = 32;
        foreach ($chunks as $chunk) {
            imagestring($im, 1, 6, $y, $chunk, $gray);
            $y += 12;
            if ($y > $h - 14) break;
        }

        ob_start();
        imagepng($im);
        $png = ob_get_clean();
        imagedestroy($im);

        return 'data:image/png;base64,' . base64_encode($png);
    }

    private function numeroALetras(float $amount): string
    {
        $n = (int)round($amount);
        if ($n === 0) return 'CERO PESOS';

        $u = ['', 'UN', 'DOS', 'TRES', 'CUATRO', 'CINCO', 'SEIS', 'SIETE', 'OCHO', 'NUEVE',
              'DIEZ', 'ONCE', 'DOCE', 'TRECE', 'CATORCE', 'QUINCE', 'DIECISEIS',
              'DIECISIETE', 'DIECIOCHO', 'DIECINUEVE'];
        $d = ['', '', 'VEINTE', 'TREINTA', 'CUARENTA', 'CINCUENTA',
              'SESENTA', 'SETENTA', 'OCHENTA', 'NOVENTA'];
        $c = ['', 'CIENTO', 'DOSCIENTOS', 'TRESCIENTOS', 'CUATROCIENTOS', 'QUINIENTOS',
              'SEISCIENTOS', 'SETECIENTOS', 'OCHOCIENTOS', 'NOVECIENTOS'];

        $g = function(int $num) use ($u, $d, $c, &$g): string {
            if ($num === 0) return '';
            if ($num === 100) return 'CIEN';
            if ($num < 20) return $u[$num];
            if ($num < 30) return $num === 20 ? 'VEINTE' : 'VEINTI' . mb_strtolower($u[$num - 20]);
            $hund = intdiv($num, 100);
            $rem  = $num % 100;
            $tens = intdiv($rem, 10);
            $unit = $rem % 10;
            $out  = $hund > 0 ? $c[$hund] : '';
            if ($rem > 0) {
                if ($out !== '') $out .= ' ';
                if ($rem < 20) {
                    $out .= $u[$rem];
                } else {
                    $out .= $d[$tens];
                    if ($unit > 0) $out .= ' Y ' . $u[$unit];
                }
            }
            return $out;
        };

        $mil   = intdiv($n, 1_000_000);
        $miles = intdiv($n % 1_000_000, 1_000);
        $resto = $n % 1_000;

        $parts = [];
        if ($mil   > 0) $parts[] = $mil   === 1 ? 'UN MILLON'  : $g($mil)   . ' MILLONES';
        if ($miles > 0) $parts[] = $miles === 1 ? 'MIL'        : $g($miles) . ' MIL';
        if ($resto > 0) $parts[] = $g($resto);

        return implode(' ', $parts) . ' PESOS';
    }

    private function calcularDV(string $nit): string
    {
        $nit = str_replace([' ', '.', '-'], '', $nit);
        if (!is_numeric($nit)) {
            throw new \InvalidArgumentException('El NIT debe ser numérico.');
        }

        $factores     = [3, 7, 13, 17, 19, 23, 29, 37, 41, 43, 47, 53, 59, 67, 71];
        $nitInvertido = strrev($nit);
        $suma         = 0;

        for ($i = 0; $i < strlen($nitInvertido); $i++) {
            $suma += ((int)$nitInvertido[$i]) * $factores[$i];
        }

        $residuo = $suma % 11;
        return (string)(($residuo > 1) ? (11 - $residuo) : $residuo);
    }
}
