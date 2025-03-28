<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use \PDF;


function limpiarValores($valor)
{
    return !is_null($valor) && $valor !== '';
}



class HistoriaPsicologica extends Model
{
    private static function limpiarValores($valor)
    {
        return !is_null($valor) && $valor !== '';
    }

    public static function busquedaPlanIntervencion($idHistoria)
    {
        $plan = DB::table('historia_clinica')
            ->select('plan_intervencion', 'objetivo_general', 'objetivos_especificos', 'sugerencias_interconsultas', 'observaciones_recomendaciones')
            ->where('id', $idHistoria)
            ->first();

        return $plan;
    }
    public static function busquedaVentaConsulta($idHistoria)
    {
        $venta = DB::table('servicios')
            ->where('servicios.id_historia', $idHistoria)
            ->leftJoin('ventas', 'servicios.id', 'ventas.id_servicio')
            ->select(
                'servicios.id',
                'servicios.descripcion',
                'servicios.precio',
                'servicios.fecha',
                'ventas.estado_venta as estadoVentaConsulta'
            )
            ->where('servicios.estado', 'ACTIVO')
            ->where('servicios.tipo_historia', 'PSICOLOGIA')
            ->first();

        if ($venta) {
            if ($venta->estadoVentaConsulta == "PENDIENTE") {
                $venta->estadoVentaConsulta = "COMPRADA";
            }
        }
        return $venta;
    }

    public static function busquedaRecaudo($idRecaudo)
    {
        $recaudo = DB::table('pagos')
            ->leftJoin('servicios', 'pagos.id_servicio', 'servicios.id')
            ->leftJoin('ventas', 'servicios.id', 'ventas.id_servicio')
            ->select(
                'pagos.id',
                'servicios.id_paciente',
                'pagos.fecha_pago',
                'pagos.pago_realizado',
                'ventas.saldo',
                'servicios.precio',
                'servicios.id_tipo_servicio',
                'servicios.tipo'

            )
            ->where('pagos.id', $idRecaudo)
            ->first();
         

        if ($recaudo) {
            if ($recaudo->tipo == 'CONSULTA') {
                $recaudo->descripcion = DB::connection('mysql')->table('especialidades')
                    ->select('nombre as descripcion')
                    ->where('id', $recaudo->id_tipo_servicio)
                    ->first();
            }else if($recaudo->tipo == 'SESION'){
                $recaudo->descripcion = DB::connection('mysql')->table('sesiones')
                    ->select('descripcion')
                    ->where('id', $recaudo->id_tipo_servicio)
                    ->first();
            }else if($recaudo->tipo == 'PAQUETE'){
                $recaudo->descripcion = DB::connection('mysql')->table('paquetes')
                    ->select('descripcion')
                    ->where('id', $recaudo->id_tipo_servicio)
                    ->first();
            }else if($recaudo->tipo == 'PRUEBAS'){
                $recaudo->descripcion = DB::connection('mysql')->table('pruebas')
                    ->select('descripcion')
                    ->where('id', $recaudo->id_tipo_servicio)
                    ->first();
            }
        }
       
        return $recaudo;
    }

    public static function listarMediosPagos($idPago)
    {
        $mediosPagos = DB::table('medio_pagos')
            ->where('id_pago', $idPago)
            ->select(
                'id',
                'valor',
                DB::raw("CASE
            WHEN medio_pago = 'e' THEN 'Efectivo'
            WHEN medio_pago = 't' THEN 'Transferencia'
            WHEN medio_pago = 'tc' THEN 'Tarjeta de débito'
            ELSE 'Tarjeta de crédito' END as nombreMedioPago"),
                'referencia'
            )
            ->get();
        return $mediosPagos;
    }

    public static function busquedaVentaSesiom($idServi)
    {
        $venta = DB::table('servicios')
            ->where('servicios.id', $idServi)
            ->leftJoin('ventas', 'servicios.id', 'ventas.id_servicio')
            ->select(
                'servicios.id',
                'servicios.descripcion',
                'servicios.precio',
                'servicios.fecha',
                'ventas.estado_venta'
            )
            ->where('servicios.estado', 'ACTIVO')
            ->first();
        return $venta;
    }
    public static function busquedaServicios($fec1, $fec2)
    {
        $servicios = DB::table('servicios')
            ->leftJoin('ventas', 'servicios.id', 'ventas.id_servicio')
            ->select(
                'servicios.id',
                'servicios.descripcion',
                'servicios.precio',
                'servicios.fecha',
                'ventas.estado_venta',
                'servicios.tipo'
            )
            ->where('servicios.estado', 'ACTIVO')
            ->whereBetween('servicios.fecha', [$fec1, $fec2])
            ->get();
        return $servicios;
    }

   

   

    public static function busquedaConsultaHistoria($idHistoria)
    {
        $consulta = DB::table('historia_clinica')
            ->where('id', $idHistoria)
            ->where('estado_registro', 'ACTIVO')
            ->select('codigo_consulta')
            ->first();

        if ($consulta) {
            // Obtener la descripción de la tabla referencia_cups
            $consulta->descripcion = DB::connection('mysql')->table('referencia_cups')
                ->where("id", $consulta->codigo_consulta)
                ->first();

            // Agregar el estado de la venta
            $consulta->estadoVentaConsulta = "PENDIENTE";
        }

        return $consulta;
    }

    public static function guardarPlanIntervencion($request)
    {
        try {
            DB::beginTransaction();
            try {
                $idHistoria = $request['idHistoriaPlan'];

                DB::table('historia_clinica')->where('id', $idHistoria)->update(array_filter([
                    // 'plan_intervencion' => $request['plan_intervencion'] ?? null,
                    'objetivo_general' => $request['objetivoGeneralModal'] ?? null,
                    'objetivos_especificos' => $request['objetivoEspecificoModal'] ?? null,
                    'sugerencias_interconsultas' => $request['sugerenciasModal'] ?? null,
                    'observaciones_recomendaciones' => $request['observacionesModal'] ?? null
                ]));
                DB::commit();
                return ['idHistoria' => $idHistoria];
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return ['error' => 'Error al guardar el plan de intervención'];
        }
    }

    public static function Guardar($request)
    {

        try {
            if ($request['accHistoria'] == 'guardar') {

                DB::beginTransaction();
                try {
                    // Insertar en `historia_clinica`
                    $idHistoria = DB::table('historia_clinica')->insertGetId(array_filter([
                        'id_paciente' => $request['idPaciente'] ?? null,
                        'id_profesional' => $request['idProfesional'] ?? null,
                        'primera_vez' => $request['primeraVez'] ?? null,
                        'remision' => $request['remision'] ?? null,
                        'codigo_consulta' => $request['codConsulta'] ?? null,
                        'motivo_consulta' => $request['motivoConsulta'] ?? null,
                        'otro_motivo_consulta' => !empty($request['motivoConsultaOtro']) ? implode(',', $request['motivoConsultaOtro']) : null,
                        'enfermedad_actual' => $request['enfermedadActual'] ?? null,
                        'dx_principal' => $request['codDiagnostico'] ?? null,
                        'codigo_diagnostico' => $request['codImpresionDiagnostico'] ?? null,
                        'diagnostico_primera_vez' => $request['establecidoPrimeraVez'] ?? null,
                        'plan_intervencion' => $request['plan_intervencion'] ?? null,
                        'objetivo_general' => $request['objetivo_general'] ?? null,
                        'objetivos_especificos' => $request['objetivos_especificos'] ?? null,
                        'sugerencias_interconsultas' => $request['sugerencia_interconsultas'] ?? null,
                        'observaciones_recomendaciones' => $request['observaciones_recomendaciones'] ?? null,
                        'tipologia' => $request['tipoPsicologia'] ?? null,
                        'fecha_historia' => now(),
                        'estado_hitoria' => 'cerrada',
                        'estado_registro' => 'ACTIVO',
                        'eval_inicial' => $request['resumen_evaluacion_inicial'] ?? null,
                        'otro_dx_principal' => $request['otro_CodDiagnostico'] ?? null,
                        'otro_cod_diagnostico' => $request['otra_ImpresionDiagnostica'] ?? null,


                    ]));

                    // Insertar antecedentes médicos
                    $antecedentesMedicos = array_filter([
                        ['id_historia' => $idHistoria, 'tipo' => 'quirurgicos', 'detalle' => $request['quirurgicos'], 'nombre' => 'Quirúrgico'],
                        ['id_historia' => $idHistoria, 'tipo' => 'toxicos', 'detalle' => $request['toxicos'], 'nombre' => 'Tóxicos'],
                        ['id_historia' => $idHistoria, 'tipo' => 'traumaticos', 'detalle' => $request['traumaticos'], 'nombre' => 'Traumáticos'],
                        ['id_historia' => $idHistoria, 'tipo' => 'medicacion', 'detalle' => $request['medicacion'], 'nombre' => 'Medicación'],
                        ['id_historia' => $idHistoria, 'tipo' => 'paraclinicos', 'detalle' => $request['paraclinicos'], 'nombre' => 'Paraclínicos'],
                        ['id_historia' => $idHistoria, 'tipo' => 'hospitalizaciones', 'detalle' => $request['hospitalizaciones'], 'nombre' => 'Hospitalizaciones'],
                        ['id_historia' => $idHistoria, 'tipo' => 'patologia', 'detalle' => $request['patologia'], 'nombre' => 'Patología']
                    ], function ($item) {
                        return !empty($item['detalle']);
                    });
                    DB::table('antecedentes_medicos')->insert($antecedentesMedicos);

                    // Insertar antecedentes familiares
                    $antecedentesFamiliares = array_filter([
                        ['id_historia' => $idHistoria, 'tipo' => 'depresion', 'detalle' => implode(',', $request['depresion']), 'nombre' => 'Depresión'],
                        ['id_historia' => $idHistoria, 'tipo' => 'ansiedad', 'detalle' => implode(',', $request['ansiedad']), 'nombre' => 'Ansiedad'],
                        ['id_historia' => $idHistoria, 'tipo' => 'demencia', 'detalle' => implode(',', $request['demencia']), 'nombre' => 'Demencia'],
                        ['id_historia' => $idHistoria, 'tipo' => 'alcoholismo', 'detalle' => implode(',', $request['alcoholismo']), 'nombre' => 'Alcoholismo'],
                        ['id_historia' => $idHistoria, 'tipo' => 'drogadiccion', 'detalle' => implode(',', $request['drogadiccion']), 'nombre' => 'Drogadicción'],
                        ['id_historia' => $idHistoria, 'tipo' => 'discapacidad_intelectual', 'detalle' => implode(',', $request['discapacidad_intelectual']), 'nombre' => 'Discapacidad intelectual'],
                        ['id_historia' => $idHistoria, 'tipo' => 'patologicos', 'detalle' => implode(',', $request['patologicos']), 'nombre' => 'Patológicos'],
                        ['id_historia' => $idHistoria, 'tipo' => 'otros', 'detalle' => implode(',', $request['otros']), 'nombre' => 'Otros'],
                    ], function ($item) {
                        return !empty($item['detalle']);
                    });
                    DB::table('antecedentes_familiares')->insert($antecedentesFamiliares);

                    // Insertar áreas de ajuste y/o desempeño
                    $ajusteDesempeno = array_filter([
                        ['id_historia' => $idHistoria, 'area' => 'historia_educativa', 'detalle' => $request['historia_educativa'], 'nombre' => 'Historia educativa'],
                        ['id_historia' => $idHistoria, 'area' => 'historia_laboral', 'detalle' => $request['historia_laboral'], 'nombre' => 'Historia laboral'],
                        ['id_historia' => $idHistoria, 'area' => 'historia_familiar', 'detalle' => $request['historia_familiar'], 'nombre' => 'Historia familiar'],
                        ['id_historia' => $idHistoria, 'area' => 'historia_social', 'detalle' => $request['historia_social'], 'nombre' => 'Historia social'],
                        ['id_historia' => $idHistoria, 'area' => 'historia_socio_afectiva', 'detalle' => $request['historia_socio_afectiva'], 'nombre' => 'Historia socio-afectiva']
                    ], function ($item) {
                        return !empty($item['detalle']);
                    });
                    DB::table('historia_ajuste_desempeno')->insert($ajusteDesempeno);

                    // Insertar interconsultas
                    $interconsultas = array_filter([
                        ['id_historia' => $idHistoria, 'tipo' => 'intervencion_psiquiatria', 'detalle' => $request['intervencion_psiquiatria'], 'nombre' => 'Intervención psiquiátrica'],
                        ['id_historia' => $idHistoria, 'tipo' => 'intervencion_neurologia', 'detalle' => $request['intervencion_neurologia'], 'nombre' => 'Intervención neurológica'],
                        ['id_historia' => $idHistoria, 'tipo' => 'intervencion_neuropsicologia', 'detalle' => $request['intervencion_neuropsicologia'], 'nombre' => 'Intervención neuropsicológica']
                    ], function ($item) {
                        return !empty($item['detalle']);
                    });
                    DB::table('interconsultas')->insert($interconsultas);

                    // Insertar apariencia personal
                    $aparienciaPersonal = array_filter([
                        ['id_historia' => $idHistoria, 'caracteristica' => 'edad', 'detalle' => implode(',', $request['edad']) ?? null, 'nombre' => 'Edad'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'edad_otro', 'detalle' => $request['edad_otro'] ?? null, 'nombre' => 'Edad (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'desarrollo', 'detalle' => implode(',', $request['desarrollo']) ?? null, 'nombre' => 'Desarrollo pondoestatural'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'desarrollo_otro', 'detalle' => $request['desarrollo_otro'] ?? null, 'nombre' => 'Desarrollo pondoestatural (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'aseo', 'detalle' => implode(',', $request['aseo']) ?? null, 'nombre' => 'Aseo y Arreglo'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'aseo_otro', 'detalle' => $request['aseo_otro'] ?? null, 'nombre' => 'Aseo y arreglo (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'salud', 'detalle' => implode(',', $request['salud']) ?? null, 'nombre' => 'Salud somática'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'salud_otro', 'detalle' => $request['salud_otro'] ?? null, 'nombre' => 'Salud somática (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'facies', 'detalle' => implode(',', $request['facies']) ?? null, 'nombre' => 'Facies'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'facies_otro', 'detalle' => $request['facies_otro'] ?? null, 'nombre' => 'Facies (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'biotipo', 'detalle' => implode(',', $request['biotipo']) ?? null, 'nombre' => 'Biotipo'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'biotipo_otro', 'detalle' => $request['biotipo_otro'] ?? null, 'nombre' => 'Biotipo (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'actitud', 'detalle' => implode(',', $request['actitud']) ?? null, 'nombre' => 'Actitud'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'actitud_otro', 'detalle' => $request['actitud_otro'] ?? null, 'nombre' => 'Actitud (otro)']
                    ], function ($item) {
                        return !empty($item['detalle']);
                    });

                    // Inserta los datos filtrados
                    DB::table('apariencia_personal')->insert($aparienciaPersonal);

                    // Insertar funciones cognitivas
                    $funcionesSomaticas = array_filter([
                        ['id_historia' => $idHistoria, 'caracteristica' => 'consciencia', 'detalle' => implode(',', $request['consciencia']) ?? null, 'nombre' => 'Consciencia'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'consciencia_otro', 'detalle' => $request['consciencia_otro'] ?? null, 'nombre' => 'Consciencia (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'orientacion', 'detalle' => implode(',', $request['orientacion']) ?? null, 'nombre' => 'Orientación'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'orientacion_otro', 'detalle' => $request['orientacion_otro'] ?? null, 'nombre' => 'Orientación (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'memoria', 'detalle' => implode(',', $request['memoria']) ?? null, 'nombre' => 'Memoria'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'memoria_otro', 'detalle' => $request['memoria_otro'] ?? null, 'nombre' => 'Memoria (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'atencion', 'detalle' => implode(',', $request['atencion']) ?? null, 'nombre' => 'Atención'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'atencion_otro', 'detalle' => $request['atencion_otro'] ?? null, 'nombre' => 'Atención (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'concentracion', 'detalle' => implode(',', $request['concentracion']) ?? null, 'nombre' => 'Concentración'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'concentracion_otro', 'detalle' => $request['concentracion_otro'] ?? null, 'nombre' => 'Concentración (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'lenguaje', 'detalle' => implode(',', $request['lenguaje']) ?? null, 'nombre' => 'Lenguaje'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'lenguaje_otro', 'detalle' => $request['lenguaje_otro'] ?? null, 'nombre' => 'Lenguaje (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'pensamiento', 'detalle' => implode(',', $request['pensamiento']) ?? null, 'nombre' => 'Pensamiento'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'pensamiento_otro', 'detalle' => $request['pensamiento_otro'] ?? null, 'nombre' => 'Pensamiento (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'afecto', 'detalle' => implode(',', $request['afecto']) ?? null, 'nombre' => 'Afecto'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'afecto_otro', 'detalle' => $request['afecto_otro'] ?? null, 'nombre' => 'Afecto (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'sensopercepcion', 'detalle' => implode(',', $request['sensopercepcion']) ?? null, 'nombre' => 'Sensopercepción'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'sensopercepcion_otro', 'detalle' => $request['sensopercepcion_otro'] ?? null, 'nombre' => 'Sensopercepción (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'psicomotricidad', 'detalle' => implode(',', $request['psicomotricidad']) ?? null, 'nombre' => 'Psicomotricidad'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'psicomotricidad_otro', 'detalle' => $request['psicomotricidad_otro'] ?? null, 'nombre' => 'Psicomotricidad (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'juicio', 'detalle' => implode(',', $request['juicio']) ?? null, 'nombre' => 'Juicio'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'juicio_otro', 'detalle' => $request['juicio_otro'] ?? null, 'nombre' => 'Juicio (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'inteligencia', 'detalle' => implode(',', $request['inteligencia']) ?? null, 'nombre' => 'Inteligencia'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'inteligencia_otro', 'detalle' => $request['inteligencia_otro'] ?? null, 'nombre' => 'Inteligencia (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'conciencia_enfermedad', 'detalle' => implode(',', $request['conciencia_enfermedad']) ?? null, 'nombre' => 'Conciencia de enfermedad'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'conciencia_enfermedad_otro', 'detalle' => $request['conciencia_enfermedad_otro'] ?? null, 'nombre' => 'Conciencia de enfermedad (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'sufrimiento_psicologico', 'detalle' => implode(',', $request['sufrimiento_psicologico']) ?? null, 'nombre' => 'Sufrimiento psicológico'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'sufrimiento_psicologico_otro', 'detalle' => $request['sufrimiento_psicologico_otro'] ?? null, 'nombre' => 'Sufrimiento psicológico (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'motivacion_tratamiento', 'detalle' => implode(',', $request['motivacion_tratamiento']) ?? null, 'nombre' => 'Motivación al tratamiento'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'motivacion_tratamiento_otro', 'detalle' => $request['motivacion_tratamiento_otro'] ?? null, 'nombre' => 'Motivación al tratamiento (otro)']
                    ], function ($item) {
                        return !empty($item['detalle']);
                    });

                    // Inserta los datos filtrados
                    DB::table('funciones_cognitivas')->insert($funcionesSomaticas);


                    // Insertar Funciones Somáticas
                    $examenMental = array_filter([
                        'id_historia' => $idHistoria,
                        'ciclos_del_sueno' => $request['ciclos_sueno'],
                        'apetito' => $request['apetito'],
                        'actividades_autocuidado' => $request['autocuidado'],
                    ]);
                    DB::table('funciones_somaticas')->insert($examenMental);


                    /// En el caso de que sea pediatria
                    if ($request['tipoPsicologia'] == "Pediatría") {
                        // Insertar antecedentes prenatales
                        $antecedentesFamiliares = array_filter([
                            ['id_historia' => $idHistoria, 'tipo' => 'edad_madre', 'detalle' => $request['edad_madre'], 'nombre' => 'Edad de la madre en el embarazo'],
                            ['id_historia' => $idHistoria, 'tipo' => 'enfermedades_madre', 'detalle' => $request['enfermedades_madre'], 'nombre' => 'Enfermedades de la madre'],
                            ['id_historia' => $idHistoria, 'tipo' => 'numero_embarazo', 'detalle' => $request['numero_embarazo'], 'nombre' => 'Único embarazo'],
                            ['id_historia' => $idHistoria, 'tipo' => 'enbarazo_controlado', 'detalle' => $request['enbarazo_controlado'], 'nombre' => 'El embarazo fue controlado por atención médica'],
                            ['id_historia' => $idHistoria, 'tipo' => 'planificacion', 'detalle' => $request['planificacion'], 'nombre' => 'Uso de planificación en el momento del embarazo'],
                            ['id_historia' => $idHistoria, 'tipo' => 'estado_madre', 'detalle' => $request['estado_madre'], 'nombre' => 'Estado de la madre durante el embarazo']
                        ], function ($item) {
                            return !empty($item['detalle']);
                        });
                        DB::table('antecedentes_prenatales')->insert($antecedentesFamiliares);

                        // Insertar antecedentes natales
                        $antecedentesFamiliares = array_filter([
                            ['id_historia' => $idHistoria, 'tipo' => 'tipo_nacimiento', 'detalle' => $request['tipo_nacimiento'], 'nombre' => 'Tipo de nacimiento'],
                            ['id_historia' => $idHistoria, 'tipo' => 'causa_cesarea', 'detalle' => $request['causa_cesarea'], 'nombre' => 'Causa de la cesárea'],
                            ['id_historia' => $idHistoria, 'tipo' => 'reanimacion', 'detalle' => $request['reanimacion'], 'nombre' => 'Uso de maniobras de reanimación'],
                            ['id_historia' => $idHistoria, 'tipo' => 'peso_nacer', 'detalle' => $request['peso_nacer'], 'nombre' => 'Peso al nacer'],
                            ['id_historia' => $idHistoria, 'tipo' => 'talla_nacer', 'detalle' => $request['talla_nacer'], 'nombre' => 'Talla al nacer'],
                            ['id_historia' => $idHistoria, 'tipo' => 'llanto_nacer', 'detalle' => $request['llanto_nacer'], 'nombre' => 'Llanto al nacer']
                        ], function ($item) {
                            return !empty($item['detalle']);
                        });
                        DB::table('antecedentes_natales')->insert($antecedentesFamiliares);

                        // Insertar antecedentes posnatales
                        $antecedentesFamiliares = array_filter([
                            ['id_historia' => $idHistoria, 'tipo' => 'hospitalizaciones_postnatales', 'detalle' => $request['hospitalizaciones_postnatales'], 'nombre' => 'Hospitalizaciones recién nacido'],
                            ['id_historia' => $idHistoria, 'tipo' => 'desarrollo_psicomotor', 'detalle' => $request['desarrollo_psicomotor'], 'nombre' => 'Desarrollo psicomotor']
                        ], function ($item) {
                            return !empty($item['detalle']);
                        });
                        DB::table('antecedentes_posnatales')->insert($antecedentesFamiliares);

                        // Insertar desarrollo psicomotor
                        $antecedentesFamiliares = array_filter([
                            ['id_historia' => $idHistoria, 'tipo' => 'control_cefalico', 'detalle' => $request['control_cefalico'], 'nombre' => 'Control cefálico'],
                            ['id_historia' => $idHistoria, 'tipo' => 'rolado', 'detalle' => $request['rolado'], 'nombre' => 'Rolado'],
                            ['id_historia' => $idHistoria, 'tipo' => 'sedente_solo', 'detalle' => $request['sedente_solo'], 'nombre' => 'Sedente solo'],
                            ['id_historia' => $idHistoria, 'tipo' => 'gateo', 'detalle' => $request['gateo'], 'nombre' => 'Gateo'],
                            ['id_historia' => $idHistoria, 'tipo' => 'bipedo', 'detalle' => $request['bipedo'], 'nombre' => 'Bípedo sin ayuda'],
                            ['id_historia' => $idHistoria, 'tipo' => 'marcha', 'detalle' => $request['marcha'], 'nombre' => 'Marcha'],
                            ['id_historia' => $idHistoria, 'tipo' => 'lenguaje_verbal', 'detalle' => $request['lenguaje_verbal'], 'nombre' => 'Lenguaje verbal'],
                            ['id_historia' => $idHistoria, 'tipo' => 'lenguaje_verbal_fluido', 'detalle' => $request['lenguaje_verbal_fluido'], 'nombre' => 'Lenguaje verbal fluido']
                        ], function ($item) {
                            return !empty($item['detalle']);
                        });
                        DB::table('desarrollo_psicomotor')->insert($antecedentesFamiliares);
                    }



                    // Confirmar transacción
                    DB::commit();

                    return ['idHistoria' => $idHistoria];
                } catch (\Exception $e) {
                    // Revertir transacción en caso de error
                    DB::rollBack();
                    throw $e;
                }
            } else {
                DB::beginTransaction();

                try {
                    // Insertar en `historia_clinica`

                    $idHistoria = $request['idHistoria'];
                    DB::table('historia_clinica')->where('id', $idHistoria)->update(array_filter([
                        'id_paciente' => $request['idPaciente'] ?? null,
                        'id_profesional' => $request['idProfesional'] ?? null,
                        'primera_vez' => $request['primeraVez'] ?? null,
                        'remision' => $request['remision'] ?? null,
                        'codigo_consulta' => $request['codConsulta'] ?? null,
                        'motivo_consulta' => $request['motivoConsulta'] ?? null,
                        'otro_motivo_consulta' => !empty($request['motivoConsultaOtro']) ? implode(',', $request['motivoConsultaOtro']) : null,
                        'enfermedad_actual' => $request['enfermedadActual'] ?? null,
                        'dx_principal' => $request['codDiagnostico'] ?? null,
                        'codigo_diagnostico' => $request['codImpresionDiagnostico'] ?? null,
                        'diagnostico_primera_vez' => $request['establecidoPrimeraVez'] ?? null,
                        'plan_intervencion' => $request['plan_intervencion'] ?? null,
                        'objetivo_general' => $request['objetivo_general'] ?? null,
                        'objetivos_especificos' => $request['objetivos_especificos'] ?? null,
                        'sugerencias_interconsultas' => $request['sugerencia_interconsultas'] ?? null,
                        'observaciones_recomendaciones' => $request['observaciones_recomendaciones'] ?? null,
                        'tipologia' => $request['tipoPsicologia'] ?? null,
                        'eval_inicial' => $request['resumen_evaluacion_inicial'] ?? null,
                        'otro_dx_principal' => $request['otro_CodDiagnostico'] ?? null,
                        'otro_cod_diagnostico' => $request['otra_ImpresionDiagnostica'] ?? null,

                    ]));


                    // Insertar antecedentes médicos
                    DB::table('antecedentes_medicos')->where('id_historia', $idHistoria)->delete();
                    $antecedentesMedicos = array_filter([
                        ['id_historia' => $idHistoria, 'tipo' => 'quirurgicos', 'detalle' => $request['quirurgicos'], 'nombre' => 'Quirúrgicos'],
                        ['id_historia' => $idHistoria, 'tipo' => 'toxicos', 'detalle' => $request['toxicos'], 'nombre' => 'Tóxicos'],
                        ['id_historia' => $idHistoria, 'tipo' => 'traumaticos', 'detalle' => $request['traumaticos'], 'nombre' => 'Traumáticos'],
                        ['id_historia' => $idHistoria, 'tipo' => 'medicacion', 'detalle' => $request['medicacion'], 'nombre' => 'Medicación'],
                        ['id_historia' => $idHistoria, 'tipo' => 'paraclinicos', 'detalle' => $request['paraclinicos'], 'nombre' => 'Paraclínicos'],
                        ['id_historia' => $idHistoria, 'tipo' => 'hospitalizaciones', 'detalle' => $request['hospitalizaciones'], 'nombre' => 'Hospitalizaciones'],
                        ['id_historia' => $idHistoria, 'tipo' => 'patologia', 'detalle' => $request['patologia'], 'nombre' => 'Patología']
                    ], function ($item) {
                        return !empty($item['detalle']);
                    });
                    DB::table('antecedentes_medicos')->insert($antecedentesMedicos);

                    // Insertar antecedentes familiares
                    DB::table('antecedentes_familiares')->where('id_historia', $idHistoria)->delete();
                    $antecedentesFamiliares = array_filter([
                        ['id_historia' => $idHistoria, 'tipo' => 'depresion', 'detalle' => implode(',', $request['depresion']), 'nombre' => 'Depresión'],
                        ['id_historia' => $idHistoria, 'tipo' => 'ansiedad', 'detalle' => implode(',', $request['ansiedad']), 'nombre' => 'Ansiedad'],
                        ['id_historia' => $idHistoria, 'tipo' => 'demencia', 'detalle' => implode(',', $request['demencia']), 'nombre' => 'Demencia'],
                        ['id_historia' => $idHistoria, 'tipo' => 'alcoholismo', 'detalle' => implode(',', $request['alcoholismo']), 'nombre' => 'Alcoholismo'],
                        ['id_historia' => $idHistoria, 'tipo' => 'drogadiccion', 'detalle' => implode(',', $request['drogadiccion']), 'nombre' => 'Drogadicción'],
                        ['id_historia' => $idHistoria, 'tipo' => 'discapacidad_intelectual', 'detalle' => implode(',', $request['discapacidad_intelectual']), 'nombre' => 'Discapacidad intelectual'],
                        ['id_historia' => $idHistoria, 'tipo' => 'patologicos', 'detalle' => implode(',', $request['patologicos']), 'nombre' => 'Patológicos'],
                        ['id_historia' => $idHistoria, 'tipo' => 'otros', 'detalle' => implode(',', $request['otros']), 'nombre' => 'Otros'],
                    ], function ($item) {
                        return !empty($item['detalle']);
                    });
                    DB::table('antecedentes_familiares')->insert($antecedentesFamiliares);

                    // Insertar áreas de ajuste y/o desempeño
                    DB::table('historia_ajuste_desempeno')->where('id_historia', $idHistoria)->delete();
                    $ajusteDesempeno = array_filter([
                        ['id_historia' => $idHistoria, 'area' => 'historia_educativa', 'detalle' => $request['historia_educativa'], 'nombre' => 'Historia educativa'],
                        ['id_historia' => $idHistoria, 'area' => 'historia_laboral', 'detalle' => $request['historia_laboral'], 'nombre' => 'Historia laboral'],
                        ['id_historia' => $idHistoria, 'area' => 'historia_familiar', 'detalle' => $request['historia_familiar'], 'nombre' => 'Historia familiar'],
                        ['id_historia' => $idHistoria, 'area' => 'historia_social', 'detalle' => $request['historia_social'], 'nombre' => 'Historia social'],
                        ['id_historia' => $idHistoria, 'area' => 'historia_socio_afectiva', 'detalle' => $request['historia_socio_afectiva'], 'nombre' => 'Historia socio-afectiva']
                    ], function ($item) {
                        return !empty($item['detalle']);
                    });
                    DB::table('historia_ajuste_desempeno')->insert($ajusteDesempeno);

                    // Insertar interconsultas
                    DB::table('interconsultas')->where('id_historia', $idHistoria)->delete();
                    $interconsultas = array_filter([
                        ['id_historia' => $idHistoria, 'tipo' => 'intervencion_psiquiatria', 'detalle' => $request['intervencion_psiquiatria'], 'nombre' => 'Intervención psiquiátrica'],
                        ['id_historia' => $idHistoria, 'tipo' => 'intervencion_neurologia', 'detalle' => $request['intervencion_neurologia'], 'nombre' => 'Intervención neurológica'],
                        ['id_historia' => $idHistoria, 'tipo' => 'intervencion_neuropsicologia', 'detalle' => $request['intervencion_neuropsicologia'], 'nombre' => 'Intervención neuropsicológica']
                    ], function ($item) {
                        return !empty($item['detalle']);
                    });
                    DB::table('interconsultas')->insert($interconsultas);

                    // Insertar apariencia personal
                    DB::table('apariencia_personal')->where('id_historia', $idHistoria)->delete();
                    $aparienciaPersonal = array_filter([
                        ['id_historia' => $idHistoria, 'caracteristica' => 'edad', 'detalle' => implode(',', $request['edad']) ?? null, 'nombre' => 'Edad'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'edad_otro', 'detalle' => $request['edad_otro'] ?? null, 'nombre' => 'Edad (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'desarrollo', 'detalle' => implode(',', $request['desarrollo']) ?? null, 'nombre' => 'Desarrollo pondoestatural'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'desarrollo_otro', 'detalle' => $request['desarrollo_otro'] ?? null, 'nombre' => 'Desarrollo pondoestatural (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'aseo', 'detalle' => implode(',', $request['aseo']) ?? null, 'nombre' => 'Aseo y Arreglo'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'aseo_otro', 'detalle' => $request['aseo_otro'] ?? null, 'nombre' => 'Aseo y arreglo (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'salud', 'detalle' => implode(',', $request['salud']) ?? null, 'nombre' => 'Salud somática'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'salud_otro', 'detalle' => $request['salud_otro'] ?? null, 'nombre' => 'Salud somática (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'facies', 'detalle' => implode(',', $request['facies']) ?? null, 'nombre' => 'Facies'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'facies_otro', 'detalle' => $request['facies_otro'] ?? null, 'nombre' => 'Facies (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'biotipo', 'detalle' => implode(',', $request['biotipo']) ?? null, 'nombre' => 'Biotipo'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'biotipo_otro', 'detalle' => $request['biotipo_otro'] ?? null, 'nombre' => 'Biotipo (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'actitud', 'detalle' => implode(',', $request['actitud']) ?? null, 'nombre' => 'Actitud'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'actitud_otro', 'detalle' => $request['actitud_otro'] ?? null, 'nombre' => 'Actitud (otro)']
                    ], function ($item) {
                        return !empty($item['detalle']);
                    });

                    // Inserta los datos filtrados
                    DB::table('apariencia_personal')->insert($aparienciaPersonal);

                    // Insertar funciones cognitivas
                    DB::table('funciones_cognitivas')->where('id_historia', $idHistoria)->delete();

                    $funcionesSomaticas = array_filter([
                        ['id_historia' => $idHistoria, 'caracteristica' => 'consciencia', 'detalle' => implode(',', $request['consciencia']) ?? null, 'nombre' => 'Consciencia'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'consciencia_otro', 'detalle' => $request['consciencia_otro'] ?? null, 'nombre' => 'Consciencia (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'orientacion', 'detalle' => implode(',', $request['orientacion']) ?? null, 'nombre' => 'Orientación'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'orientacion_otro', 'detalle' => $request['orientacion_otro'] ?? null, 'nombre' => 'Orientación (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'memoria', 'detalle' => implode(',', $request['memoria']) ?? null, 'nombre' => 'Memoria'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'memoria_otro', 'detalle' => $request['memoria_otro'] ?? null, 'nombre' => 'Memoria (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'atencion', 'detalle' => implode(',', $request['atencion']) ?? null, 'nombre' => 'Atención'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'atencion_otro', 'detalle' => $request['atencion_otro'] ?? null, 'nombre' => 'Atención (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'concentracion', 'detalle' => implode(',', $request['concentracion']) ?? null, 'nombre' => 'Concentración'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'concentracion_otro', 'detalle' => $request['concentracion_otro'] ?? null, 'nombre' => 'Concentración (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'lenguaje', 'detalle' => implode(',', $request['lenguaje']) ?? null, 'nombre' => 'Lenguaje'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'lenguaje_otro', 'detalle' => $request['lenguaje_otro'] ?? null, 'nombre' => 'Lenguaje (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'pensamiento', 'detalle' => implode(',', $request['pensamiento']) ?? null, 'nombre' => 'Pensamiento'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'pensamiento_otro', 'detalle' => $request['pensamiento_otro'] ?? null, 'nombre' => 'Pensamiento (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'afecto', 'detalle' => implode(',', $request['afecto']) ?? null, 'nombre' => 'Afecto'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'afecto_otro', 'detalle' => $request['afecto_otro'] ?? null, 'nombre' => 'Afecto (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'sensopercepcion', 'detalle' => implode(',', $request['sensopercepcion']) ?? null, 'nombre' => 'Sensopercepción'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'sensopercepcion_otro', 'detalle' => $request['sensopercepcion_otro'] ?? null, 'nombre' => 'Sensopercepción (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'psicomotricidad', 'detalle' => implode(',', $request['psicomotricidad']) ?? null, 'nombre' => 'Psicomotricidad'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'psicomotricidad_otro', 'detalle' => $request['psicomotricidad_otro'] ?? null, 'nombre' => 'Psicomotricidad (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'juicio', 'detalle' => implode(',', $request['juicio']) ?? null, 'nombre' => 'Juicio'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'juicio_otro', 'detalle' => $request['juicio_otro'] ?? null, 'nombre' => 'Juicio (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'inteligencia', 'detalle' => implode(',', $request['inteligencia']) ?? null, 'nombre' => 'Inteligencia'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'inteligencia_otro', 'detalle' => $request['inteligencia_otro'] ?? null, 'nombre' => 'Inteligencia (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'conciencia_enfermedad', 'detalle' => implode(',', $request['conciencia_enfermedad']) ?? null, 'nombre' => 'Conciencia de enfermedad'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'conciencia_enfermedad_otro', 'detalle' => $request['conciencia_enfermedad_otro'] ?? null, 'nombre' => 'Conciencia de enfermedad (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'sufrimiento_psicologico', 'detalle' => implode(',', $request['sufrimiento_psicologico']) ?? null, 'nombre' => 'Sufrimiento psicológico'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'sufrimiento_psicologico_otro', 'detalle' => $request['sufrimiento_psicologico_otro'] ?? null, 'nombre' => 'Sufrimiento psicológico (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'motivacion_tratamiento', 'detalle' => implode(',', $request['motivacion_tratamiento']) ?? null, 'nombre' => 'Motivación al tratamiento'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'motivacion_tratamiento_otro', 'detalle' => $request['motivacion_tratamiento_otro'] ?? null, 'nombre' => 'Motivación al tratamiento (otro)']
                    ], function ($item) {
                        return !empty($item['detalle']);
                    });

                    // Inserta los datos filtrados
                    DB::table('funciones_cognitivas')->insert($funcionesSomaticas);

                    // Insertar Funciones Somáticas
                    DB::table('funciones_somaticas')->where('id_historia', $idHistoria)->delete();

                    $examenMental = array_filter([
                        'id_historia' => $idHistoria,
                        'ciclos_del_sueno' => $request['ciclos_sueno'],
                        'apetito' => $request['apetito'],
                        'actividades_autocuidado' => $request['autocuidado'],
                    ]);
                    DB::table('funciones_somaticas')->insert($examenMental);


                    //si es pediatria 

                    /// En el caso de que sea pediatria
                    if ($request['tipoPsicologia'] == "Pediatría") {
                        // Insertar antecedentes prenatales
                        DB::table('antecedentes_prenatales')->where('id_historia', $idHistoria)->delete();
                        $antecedentesFamiliares = array_filter([
                            ['id_historia' => $idHistoria, 'tipo' => 'edad_madre', 'detalle' => $request['edad_madre'], 'nombre' => 'Edad de la madre en el embarazo'],
                            ['id_historia' => $idHistoria, 'tipo' => 'enfermedades_madre', 'detalle' => $request['enfermedades_madre'], 'nombre' => 'Enfermedades de la madre'],
                            ['id_historia' => $idHistoria, 'tipo' => 'numero_embarazo', 'detalle' => $request['numero_embarazo'], 'nombre' => 'Único embarazo'],
                            ['id_historia' => $idHistoria, 'tipo' => 'enbarazo_controlado', 'detalle' => $request['enbarazo_controlado'], 'nombre' => 'El embarazo fue controlado por atención médica'],
                            ['id_historia' => $idHistoria, 'tipo' => 'planificacion', 'detalle' => $request['planificacion'], 'nombre' => 'Uso de planificación en el momento del embarazo'],
                            ['id_historia' => $idHistoria, 'tipo' => 'estado_madre', 'detalle' => $request['estado_madre'], 'nombre' => 'Estado de la madre durante el embarazo']
                        ], function ($item) {
                            return !empty($item['detalle']);
                        });
                        DB::table('antecedentes_prenatales')->insert($antecedentesFamiliares);

                        // Insertar antecedentes natales
                        DB::table('antecedentes_natales')->where('id_historia', $idHistoria)->delete();
                        $antecedentesFamiliares = array_filter([
                            ['id_historia' => $idHistoria, 'tipo' => 'tipo_nacimiento', 'detalle' => $request['tipo_nacimiento'], 'nombre' => 'Tipo de nacimiento'],
                            ['id_historia' => $idHistoria, 'tipo' => 'causa_cesarea', 'detalle' => $request['causa_cesarea'], 'nombre' => 'Causa de la cesárea'],
                            ['id_historia' => $idHistoria, 'tipo' => 'reanimacion', 'detalle' => $request['reanimacion'], 'nombre' => 'Uso de maniobras de reanimación'],
                            ['id_historia' => $idHistoria, 'tipo' => 'peso_nacer', 'detalle' => $request['peso_nacer'], 'nombre' => 'Peso al nacer'],
                            ['id_historia' => $idHistoria, 'tipo' => 'talla_nacer', 'detalle' => $request['talla_nacer'], 'nombre' => 'Talla al nacer'],
                            ['id_historia' => $idHistoria, 'tipo' => 'llanto_nacer', 'detalle' => $request['llanto_nacer'], 'nombre' => 'Llanto al nacer']
                        ], function ($item) {
                            return !empty($item['detalle']);
                        });
                        DB::table('antecedentes_natales')->insert($antecedentesFamiliares);

                        // Insertar antecedentes posnatales
                        DB::table('antecedentes_posnatales')->where('id_historia', $idHistoria)->delete();
                        $antecedentesFamiliares = array_filter([
                            ['id_historia' => $idHistoria, 'tipo' => 'hospitalizaciones_postnatales', 'detalle' => $request['hospitalizaciones_postnatales'], 'nombre' => 'Hospitalizaciones recién nacido'],
                            ['id_historia' => $idHistoria, 'tipo' => 'desarrollo_psicomotor', 'detalle' => $request['desarrollo_psicomotor'], 'nombre' => 'Desarrollo psicomotor']
                        ], function ($item) {
                            return !empty($item['detalle']);
                        });
                        DB::table('antecedentes_posnatales')->insert($antecedentesFamiliares);

                        // Insertar desarrollo psicomotor
                        DB::table('desarrollo_psicomotor')->where('id_historia', $idHistoria)->delete();
                        $antecedentesFamiliares = array_filter([
                            ['id_historia' => $idHistoria, 'tipo' => 'control_cefalico', 'detalle' => $request['control_cefalico'], 'nombre' => 'Control cefálico'],
                            ['id_historia' => $idHistoria, 'tipo' => 'rolado', 'detalle' => $request['rolado'], 'nombre' => 'Rolado'],
                            ['id_historia' => $idHistoria, 'tipo' => 'sedente_solo', 'detalle' => $request['sedente_solo'], 'nombre' => 'Sedente solo'],
                            ['id_historia' => $idHistoria, 'tipo' => 'gateo', 'detalle' => $request['gateo'], 'nombre' => 'Gateo'],
                            ['id_historia' => $idHistoria, 'tipo' => 'bipedo', 'detalle' => $request['bipedo'], 'nombre' => 'Bípedo sin ayuda'],
                            ['id_historia' => $idHistoria, 'tipo' => 'marcha', 'detalle' => $request['marcha'], 'nombre' => 'Marcha'],
                            ['id_historia' => $idHistoria, 'tipo' => 'lenguaje_verbal', 'detalle' => $request['lenguaje_verbal'], 'nombre' => 'Lenguaje verbal'],
                            ['id_historia' => $idHistoria, 'tipo' => 'lenguaje_verbal_fluido', 'detalle' => $request['lenguaje_verbal_fluido'], 'nombre' => 'Lenguaje verbal fluido']
                        ], function ($item) {
                            return !empty($item['detalle']);
                        });
                        DB::table('desarrollo_psicomotor')->insert($antecedentesFamiliares);
                    }

                    // Confirmar transacción
                    DB::commit();
                    return ['idHistoria' => $idHistoria];
                } catch (\Exception $e) {
                    // Revertir transacción en caso de error
                    DB::rollBack();
                    throw $e;
                }
            }
        } catch (Exception $e) {
            // Manejo del error
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al procesar el formulario: ' . $e->getMessage(),
            ], 500);
        }
    }

    public static function guardarConsulta($request)
    {
        try {
            if ($request['accHistoriaConsulta'] == 'guardar') {

                DB::beginTransaction();

                try {
                    // Insertar en `historia_clinica`
                    $idConsulta = DB::table('consultas_psicologica')->insertGetId(array_filter([
                        'id_historia' => $request['idHist'] ?? null,
                        'id_profesional' => Auth::user()->id,
                        'fecha_consulta' => $request['fechaEvolucion'] . ' ' . $request['horaSeleccionada'],
                        'codigo_consulta' => $request['codConsultaConsulta'] ?? null,
                        'impresion_diagnostica' => $request['codImpresionDiagnosticoConsulta']  ?? null,
                        'motivo' => $request['motivoConsultaModal'] ?? null,
                        'objetivo_sesion' => $request['objetivo_sesion'] ?? null,
                        'tecnicas_utilizadas' => $request['tecnicas_utilizadas'] ?? null,
                        'actividades_especificas' => $request['actividades_especificas'] ?? null,
                        'evaluacion_indicadores' => $request['evaluacion_indicadores'] ?? null,
                        'evolucion_sesion' => $request['evolucion_sesion'] ?? null,
                        'estado' => 'ACTIVO'
                    ]));

                    $Paciente = DB::table('historia_clinica')
                    ->where('id', $request['idHist'])
                    ->select('id_paciente')
                    ->first();

                 
                    //ACTUALIZAR NUMERO DE PAQUETES DISPONIBLES
                    $paquete = DB::connection('mysql')
                        ->table('servicios')
                        ->leftJoin("ventas", "servicios.id", "ventas.id_servicio")
                        ->leftJoin('sesiones_paquete_uso', 'ventas.id',  'sesiones_paquete_uso.venta_id')
                        ->where('servicios.id_paciente', $Paciente->id_paciente)
                        ->where('servicios.tipo', 'PAQUETE')
                        ->where('ventas.estado_venta', 'PENDIENTE')
                        ->where('servicios.estado', 'ACTIVO')
                        ->select(
                            'ventas.id',
                            'ventas.cantidad',
                            DB::raw('ventas.cantidad - COUNT(sesiones_paquete_uso.id) as sesiones_disponibles')
                        )
                        ->groupBy('ventas.id', 'ventas.cantidad', 'ventas.cantidad') // Agregar GROUP BY
                        ->first();

                       
               
                    //VALIAR SI EL sesiones_disponibles ES 1 PARA CAMBIAR EL ESTADO A TERMIANDO
                    if ($paquete->sesiones_disponibles == 1) {
                        $paqueteUpdate = DB::table('ventas')
                            ->where('id_pacientes', $Paciente->id_paciente)
                            ->where('estado_venta', 'PENDIENTE')
                            ->update(array_filter([
                                'estado_venta' => 'TERMINADO'
                            ]));

                        $sesiones = DB::table('sesiones_paquete_uso')->insert(array_filter([
                            'venta_id' => $paquete->id,
                            'id_paciente' => $Paciente->id_paciente,
                            'fecha_usada' => $request['fechaEvolucion']
                        ]));
                    } else {
                        $sesiones = DB::table('sesiones_paquete_uso')->insert(array_filter([
                            'venta_id' => $paquete->id,
                            'id_paciente' => $Paciente->id_paciente,
                            'fecha_usada' => $request['fechaEvolucion']
                        ]));
                    }

                    // Confirmar transacción
                    DB::commit();
                    return  $idConsulta;
                } catch (\Exception $e) {
                    // Revertir transacción en caso de error
                    DB::rollBack();
                    throw $e;
                }
            } else {
                DB::beginTransaction();

                try {
                    // Insertar en `historia_clinica`

                    $idConsulta = $request['idHistoriaConsulta'];
                    DB::table('consultas_psicologica')->where('id', $idConsulta)->update(array_filter([
                        'fecha_consulta' => $request['fechaEvolucion'] . ' ' . $request['horaSeleccionada'],
                        'codigo_consulta' => $request['codConsultaConsulta'] ?? null,
                        'impresion_diagnostica' => $request['codImpresionDiagnosticoConsulta']  ?? null,
                        'motivo' => $request['motivoConsultaModal'] ?? null,
                        'objetivo_sesion' => $request['objetivo_sesion'] ?? null,
                        'tecnicas_utilizadas' => $request['tecnicas_utilizadas'] ?? null,
                        'actividades_especificas' => $request['actividades_especificas'] ?? null,
                        'evaluacion_indicadores' => $request['evaluacion_indicadores'] ?? null,
                        'evolucion_sesion' => $request['evolucion_sesion'] ?? null,
                    ]));

                    // Confirmar transacción
                    DB::commit();
                    return  $idConsulta;
                } catch (\Exception $e) {
                    // Revertir transacción en caso de error
                    DB::rollBack();
                    throw $e;
                }
            }
        } catch (Exception $e) {
            // Manejo del error
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al procesar el formulario: ' . $e->getMessage(),
            ], 500);
        }
    }

    public static function guardarInforme($request)
    {
        try {
            $idInforme = $request['idInforme'];
            if ($request['accInforme'] == 'guardar') {
                DB::beginTransaction();
                try {
                    // Insertar en `informe_evolucion`
                    $idInforme = DB::table('informe_evolucion')->insertGetId(array_filter([
                        'id_paciente' => $request['idPaciente'] ?? null,
                        'id_profesional' => $request['profesionalInforme'] ?? null,
                        'fecha_creacion' => $request['fechaEvolucion'] . ' ' . $request['horaSeleccionada'],
                        'impresion_diagnostica' => $request['codImpresionDiagnosticoConsulta'] ?? null,
                        'establecido_primera' => $request['establecidoPrimeraVez']  ?? null,
                        'remision' => $request['remision'] ?? null,
                        'resumen_evaluacion_psicologica' => $request['resumen_evaluacion_inicial'] ?? null,
                        'objetivos_terapeuticos_iniciales' => $request['objetivo_terapeutico'] ?? null,
                        'evaluacion_actual' => $request['evolucion_tratamiento'] ?? null,
                        'plan_tratamiento_continuidad' => $request['plan_continuidad'] ?? null,
                        'intervencion_psiquiatria' => $request['intervencion_psiquiatria'] ?? null,
                        'intervencion_neurologia' => $request['intervencion_neurologia'] ?? null,
                        'intervencion_neuropsicologia' => $request['intervencion_neuropsicologia'] ?? null,
                        'sugerencias_interconsultas' => $request['sugerencia_consulta'] ?? null,
                        'observaciones_recomendaciones' => $request['observaciones_consulta'] ?? null,
                        'numero_sesiones' => $request['numeroSesiones'] ?? null,
                        'estado' => 'ACTIVO'
                    ]));

                    // Confirmar transacción
                    DB::commit();
                    return  $idInforme;
                } catch (\Exception $e) {
                    // Revertir transacción en caso de error
                    DB::rollBack();
                    throw $e;
                }
            } else {
                DB::beginTransaction();

                try {
                    // Insertar en informe_evolucion`
                    DB::table('informe_evolucion')->where('id', $idInforme)->update(array_filter([
                        'id_paciente' => $request['idPaciente'] ?? null,
                        'id_profesional' => $request['profesionalInforme'] ?? null,
                        'fecha_creacion' => $request['fechaEvolucion'] . ' ' . $request['horaSeleccionada'],
                        'impresion_diagnostica' => $request['codImpresionDiagnosticoConsulta'] ?? null,
                        'establecido_primera' => $request['establecidoPrimeraVez']  ?? null,
                        'remision' => $request['remision'] ?? null,
                        'resumen_evaluacion_psicologica' => $request['resumen_evaluacion_inicial'] ?? null,
                        'objetivos_terapeuticos_iniciales' => $request['objetivo_terapeutico'] ?? null,
                        'evaluacion_actual' => $request['evolucion_tratamiento'] ?? null,
                        'plan_tratamiento_continuidad' => $request['plan_continuidad'] ?? null,
                        'intervencion_psiquiatria' => $request['intervencion_psiquiatria'] ?? null,
                        'intervencion_neurologia' => $request['intervencion_neurologia'] ?? null,
                        'intervencion_neuropsicologia' => $request['intervencion_neuropsicologia'] ?? null,
                        'sugerencias_interconsultas' => $request['sugerencia_consulta'] ?? null,
                        'observaciones_recomendaciones' => $request['observaciones_consulta'] ?? null,
                        'numero_sesiones' => $request['numeroSesiones'] ?? null,
                    ]));

                    // Confirmar transacción
                    DB::commit();
                    return  $idInforme;
                } catch (\Exception $e) {
                    Log::error('Error al actualizar el informe: ' . $e->getMessage(), [
                        'idInforme' => $idInforme,
                        'data' => $request->all()
                    ]);
                    DB::rollBack();
                    throw $e;
                }
            }
        } catch (Exception $e) {
            // Manejo del error
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al procesar el formulario: ' . $e->getMessage(),
            ], 500);
        }
    }

    public static function busquedaHistoria($idHisto)
    {
        $historia = DB::connection('mysql')->table('historia_clinica')
            ->where("id", $idHisto)
            ->first();


        $historia->dx_principal_detalle = DB::connection('mysql')
            ->table('referencia_cie10')
            ->where('id', $historia->dx_principal)
            ->first();

        $historia->codigo_consulta_detalle = DB::connection('mysql')
            ->table('referencia_cups')
            ->where('id', $historia->codigo_consulta)
            ->first();

        $historia->motivo_consulta_detalle = DB::connection('mysql')
            ->table('opciones_hc_psicologia')
            ->where('id', $historia->otro_motivo_consulta)
            ->first();

        $historia->impresion_diagnostica_detalle = DB::connection('mysql')
            ->table('referencia_cie10')
            ->where('id', $historia->codigo_diagnostico)
            ->first() ?? (object) [];

        $historia->plan_intervension_detalle = DB::connection('mysql')
            ->table('opciones_hc_psicologia')
            ->where('id', $historia->plan_intervencion)
            ->first();

        $historia->profesional_detalle = DB::connection('mysql')->table('profesionales')
            ->join("users", "users.id", "profesionales.usuario")
            ->where("profesionales.usuario", $historia->id_profesional)
            ->select("profesionales.*", "users.login_usuario", "users.estado_usuario", "users.id as idUsuario")
            ->first();
            
        return $historia;
    }

    public static function busquedaConsulta($idConsulta)
    {
        return DB::connection('mysql')->table('consultas_psicologica')
            ->where("id", $idConsulta)
            ->first();
    }

    public static function busquedaInforme($idInf)
    {
        $informe = DB::connection('mysql')->table('informe_evolucion')
            ->where("id", $idInf)
            ->first();

        if ($informe) {
            $informe->impresion_diagnostica = DB::connection('mysql')->table('referencia_cie10')
                ->where("id", $informe->impresion_diagnostica)
                ->select(DB::raw('CONCAT(codigo, " - ", nombre) AS diagnostico'))
                ->first();
        }

        return $informe;
    }

    public static function busquedaEvolucionesPaciente($idHistoria)
    {
        return DB::connection('mysql')->table('consultas_psicologica')
            ->where("id_historia", $idHistoria)
            ->where("estado", "ACTIVO")
            ->get();
    }

    public static function busquedaHistoriaPaciente($idPac)
    {
        $historia = DB::connection('mysql')->table('historia_clinica')
            ->where("id_paciente", $idPac)
            ->where("estado_registro", "ACTIVO")
            ->first();
        if ($historia) {
            $historia->impresion_diagnostica = DB::connection('mysql')->table('referencia_cie10')
                ->where("id", $historia->codigo_diagnostico)
                ->select(DB::raw('CONCAT(codigo, " - ", nombre) AS diagnostico'))
                ->first();
        }


        return $historia;
    }

    public static function busquedaAntecedentes($idHisto)
    {
        return DB::connection('mysql')->table('antecedentes_medicos')
            ->where("id_historia", $idHisto)
            ->get();
    }

    public static function busquedaAntFamiliares($idHisto)
    {
        return DB::connection('mysql')->table('antecedentes_familiares')
            ->where("id_historia", $idHisto)
            ->get();
    }

    public static function busquedaAntPrenatales($idHisto)
    {
        return DB::connection('mysql')->table('antecedentes_prenatales')
            ->where("id_historia", $idHisto)
            ->get();
    }

    public static function busquedaAntNatales($idHisto)
    {
        return DB::connection('mysql')->table('antecedentes_natales')
            ->where("id_historia", $idHisto)
            ->get();
    }

    public static function busquedaAntPosnatales($idHisto)
    {
        return DB::connection('mysql')->table('antecedentes_posnatales')
            ->where("id_historia", $idHisto)
            ->get();
    }

    public static function desarrolloPsicomotor($idHisto)
    {
        return DB::connection('mysql')->table('desarrollo_psicomotor')
            ->where("id_historia", $idHisto)
            ->get();
    }
    public static function busquedaNotas($idHisto)
    {
        return DB::connection('mysql')->table('notas')
            ->where("id_historia", $idHisto)
            ->first();
    }

    public static function busquedaAreaAjuste($idHisto)
    {
        return DB::connection('mysql')->table('historia_ajuste_desempeno')
            ->where("id_historia", $idHisto)
            ->get();
    }

    public static function historialConsultas($idHisto)
    {
        return DB::connection('mysql')->table('consultas_psicologica')
            ->leftJoin("referencia_cups", "referencia_cups.id", "consultas_psicologica.codigo_consulta")
            ->leftJoin("referencia_cie10", "referencia_cie10.id", "consultas_psicologica.impresion_diagnostica")
            ->leftJoin("profesionales", "profesionales.usuario", "consultas_psicologica.id_profesional")
            ->where("consultas_psicologica.id_historia", $idHisto)
            ->orderBy('consultas_psicologica.fecha_consulta', 'desc')
            ->where("consultas_psicologica.estado", "ACTIVO")
            ->take(5)
            ->select(
                'consultas_psicologica.id',
                'consultas_psicologica.fecha_consulta',
                'referencia_cups.nombre AS consulta',
                'referencia_cie10.nombre AS diagnostico',
                'profesionales.nombre AS profesional'
            )
            ->get();
    }

    public static function busquedaConsultaDetalle($idEvolucion)
    {
        return DB::connection('mysql')->table('consultas_psicologica')
            ->leftJoin("referencia_cups", "referencia_cups.id", "consultas_psicologica.codigo_consulta")
            ->leftJoin("referencia_cie10", "referencia_cie10.id", "consultas_psicologica.impresion_diagnostica")
            ->leftJoin("profesionales", "profesionales.usuario", "consultas_psicologica.id_profesional")
            ->where("consultas_psicologica.id", $idEvolucion)
            ->select(
                'consultas_psicologica.id',
                'consultas_psicologica.fecha_consulta',
                'consultas_psicologica.impresion_diagnostica',
                'consultas_psicologica.motivo',
                'consultas_psicologica.objetivo_sesion',
                'consultas_psicologica.tecnicas_utilizadas',
                'consultas_psicologica.actividades_especificas',
                'consultas_psicologica.evaluacion_indicadores',
                'consultas_psicologica.evolucion_sesion',
                'referencia_cups.nombre AS consulta',
                'referencia_cie10.nombre AS diagnostico',
                'profesionales.nombre AS profesional'
            )
            ->first();
    }


    public static function busquedaInterconsulta($idHisto)
    {
        return DB::connection('mysql')->table('interconsultas')
            ->where("id_historia", $idHisto)
            ->get();
    }
    public static function busquedaAparienciaPersonal($idHisto)
    {
        $apariencias = DB::connection('mysql')->table('apariencia_personal')
            ->where("id_historia", $idHisto)
            ->get();

        foreach ($apariencias as $item) {
            if (!empty($item->detalle)) {
                $detalleIds = explode(',', $item->detalle);

                // Obtener las descripciones separadas por comas
                $opciones = DB::connection('mysql')->table('opciones_hc_psicologia')
                    ->whereIn("id", $detalleIds)
                    ->pluck("opcion") // Devuelve una colección de strings
                    ->toArray(); // Convertir a array

                $item->apariencia_detalle = !empty($opciones) ? implode(', ', $opciones) : "No registrado";
            } else {
                $item->apariencia_detalle = "No registrado";
            }
        }

        return $apariencias;
    }


    public static function busquedaFuncionesCognitivas($idHisto)
    {
        $funciones = DB::connection('mysql')->table('funciones_cognitivas')
            ->where("id_historia", $idHisto)
            ->get();

        foreach ($funciones as $item) {
            $detalleIds = explode(',', $item->detalle);
            $opciones = DB::connection('mysql')->table('opciones_hc_psicologia')
                ->whereIn("id", $detalleIds)
                ->pluck("opcion") // Devuelve una colección de strings
                ->toArray(); // Convertir a array
            $item->funciones_detalle = !empty($opciones) ? implode(', ', $opciones) : "No registrado";
        }

        return $funciones;
    }
    public static function busquedaFuncionesSomaticas($idHisto)
    {
        return DB::connection('mysql')->table('funciones_somaticas')
            ->where("id_historia", $idHisto)
            ->first();
    }
}
