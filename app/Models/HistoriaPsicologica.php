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
            } else if ($recaudo->tipo == 'SESION') {
                $recaudo->descripcion = DB::connection('mysql')->table('sesiones')
                    ->select('descripcion')
                    ->where('id', $recaudo->id_tipo_servicio)
                    ->first();
            } else if ($recaudo->tipo == 'PAQUETE') {
                $recaudo->descripcion = DB::connection('mysql')->table('paquetes')
                    ->select('descripcion')
                    ->where('id', $recaudo->id_tipo_servicio)
                    ->first();
            } else if ($recaudo->tipo == 'PRUEBAS') {
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
                    $camposMapping = [
                        'idPaciente' => 'id_paciente',
                        'idProfesional' => 'id_profesional',
                        'primeraVez' => 'primera_vez',
                        'remision' => 'remision',
                        'codConsulta' => 'codigo_consulta',
                        'motivoConsulta' => 'motivo_consulta',
                        'motivoConsultaOtro' => 'otro_motivo_consulta',
                        'enfermedadActual' => 'enfermedad_actual',
                        'codDiagnostico' => 'dx_principal',
                        'codImpresionDiagnostico' => 'codigo_diagnostico',
                        'establecidoPrimeraVez' => 'diagnostico_primera_vez',
                        'plan_intervencion' => 'plan_intervencion',
                        'objetivo_general' => 'objetivo_general',
                        'objetivos_especificos' => 'objetivos_especificos',
                        'sugerencia_interconsultas' => 'sugerencias_interconsultas',
                        'observaciones_recomendaciones' => 'observaciones_recomendaciones',
                        'tipoPsicologia' => 'tipologia',
                        'resumen_evaluacion_inicial' => 'eval_inicial',
                        'otro_CodDiagnostico' => 'otro_dx_principal',
                        'otra_ImpresionDiagnostica' => 'otro_cod_diagnostico'

                    ];

                    $datosInsertar = [];

                    foreach ($camposMapping as $campoRequest => $campoDB) {
                        if (array_key_exists($campoRequest, $request)) {
                            if (is_array($request[$campoRequest])) {
                                // Si es un array, se hace implode para convertirlo en una cadena separada por comas
                                $datosInsertar[$campoDB] = implode(',', $request[$campoRequest]);
                            } else {
                                // Si es un valor único, se asigna directamente
                                $datosInsertar[$campoDB] = $request[$campoRequest];
                            }
                        } else {
                            // Si no existe el campo en la solicitud, asignamos un valor vacío (o null si prefieres)
                            $datosInsertar[$campoDB] = null;
                        }
                    }

                    $datosInsertar['fecha_historia'] = now();
                    $datosInsertar['estado_hitoria'] = 'cerrada';
                    $datosInsertar['estado_registro'] = 'ACTIVO';

                    if (!empty($datosInsertar)) {
                        $idHistoria = DB::table('historia_clinica')->insertGetId($datosInsertar);
                    }

                    // Insertar antecedentes médicos
                    $tiposAntecedentes = [
                        'quirurgicos' => 'Quirúrgico',
                        'toxicos' => 'Tóxicos',
                        'traumaticos' => 'Traumáticos',
                        'medicacion' => 'Medicación',
                        'paraclinicos' => 'Paraclínicos',
                        'hospitalizaciones' => 'Hospitalizaciones',
                        'patologia' => 'Patología'
                    ];

                    foreach ($tiposAntecedentes as $tipo => $nombre) {
                        if (array_key_exists($tipo, $request)) {
                            DB::table('antecedentes_medicos')->insert([
                                'id_historia' => $idHistoria,
                                'tipo' => $tipo,
                                'detalle' => $request[$tipo],
                                'nombre' => $nombre
                            ]);
                        }
                    }

                    // Insertar antecedentes familiares
                    $tiposAntecedentesFamiliares = [
                        'depresion' => ['nombre' => 'Depresión', 'esArray' => true],
                        'ansiedad' => ['nombre' => 'Ansiedad', 'esArray' => true],
                        'demencia' => ['nombre' => 'Demencia', 'esArray' => true],
                        'alcoholismo' => ['nombre' => 'Alcoholismo', 'esArray' => true],
                        'drogadiccion' => ['nombre' => 'Drogadicción', 'esArray' => true],
                        'discapacidad_intelectual' => ['nombre' => 'Discapacidad intelectual', 'esArray' => true],
                        'patologicos' => ['nombre' => 'Patológicos', 'esArray' => false],
                        'otros' => ['nombre' => 'Otros', 'esArray' => false]
                    ];

                    foreach ($tiposAntecedentesFamiliares as $tipo => $config) {
                        if (array_key_exists($tipo, $request)) {
                            // Verificar si es un array y procesar el detalle correctamente
                            $detalle = $config['esArray'] ?
                                (is_array($request[$tipo]) ? implode(',', $request[$tipo]) : $request[$tipo]) :
                                $request[$tipo];

                            // Insertar el antecedente familiar
                            DB::table('antecedentes_familiares')->insert([
                                'id_historia' => $idHistoria,
                                'tipo' => $tipo,
                                'detalle' => $detalle,
                                'nombre' => $config['nombre']
                            ]);
                        }
                    }


                    // Definir las áreas de ajuste y/o desempeño
                    $areasAjusteDesempeno = [
                        'historia_educativa' => 'Historia educativa',
                        'historia_laboral' => 'Historia laboral',
                        'historia_familiar' => 'Historia familiar',
                        'historia_social' => 'Historia social',
                        'historia_socio_afectiva' => 'Historia socio-afectiva'
                    ];

                    // Recorrer y actualizar/insertar cada área
                    foreach ($areasAjusteDesempeno as $campo => $nombre) {
                        if (!empty($request[$campo])) {
                            DB::table('historia_ajuste_desempeno')
                                ->updateOrInsert(
                                    [
                                        'id_historia' => $idHistoria,
                                        'area' => $campo
                                    ],
                                    [
                                        'detalle' => $request[$campo],
                                        'nombre' => $nombre
                                    ]
                                );
                        }
                    }

                    // Definir los tipos de interconsultas
                    $tiposInterconsultas = [
                        'intervencion_psiquiatria' => 'Intervención psiquiátrica',
                        'intervencion_neurologia' => 'Intervención neurológica',
                        'intervencion_neuropsicologia' => 'Intervención neuropsicológica'
                    ];

                    // Recorrer y actualizar/insertar cada interconsulta
                    foreach ($tiposInterconsultas as $campo => $nombre) {
                        if (!empty($request[$campo])) {
                            DB::table('interconsultas')
                                ->updateOrInsert(
                                    [
                                        'id_historia' => $idHistoria,
                                        'tipo' => $campo
                                    ],
                                    [
                                        'detalle' => $request[$campo],
                                        'nombre' => $nombre
                                    ]
                                );
                        }
                    }

                    // Definir las características de apariencia personal
                    $tiposAparienciaPersonal = [
                        'edad' => 'Edad',
                        'edad_otro' => 'Edad (otro)',
                        'desarrollo' => 'Desarrollo pondoestatural',
                        'desarrollo_otro' => 'Desarrollo pondoestatural (otro)',
                        'aseo' => 'Aseo y Arreglo',
                        'aseo_otro' => 'Aseo y arreglo (otro)',
                        'salud' => 'Salud somática',
                        'salud_otro' => 'Salud somática (otro)',
                        'facies' => 'Facies',
                        'facies_otro' => 'Facies (otro)',
                        'biotipo' => 'Biotipo',
                        'biotipo_otro' => 'Biotipo (otro)',
                        'actitud' => 'Actitud',
                        'actitud_otro' => 'Actitud (otro)'
                    ];



                    // Recorrer y actualizar/insertar cada característica
                    foreach ($tiposAparienciaPersonal as $campo => $nombre) {
                        if (!empty($request[$campo])) {
                            // Si el campo es un array, convertirlo a cadena separada por comas
                            $detalle = is_array($request[$campo]) ? implode(',', $request[$campo]) : $request[$campo];

                            DB::table('apariencia_personal')
                                ->updateOrInsert(
                                    [
                                        'id_historia' => $idHistoria,
                                        'caracteristica' => $campo
                                    ],
                                    [
                                        'detalle' => $detalle,
                                        'nombre' => $nombre
                                    ]
                                );
                        }
                    }


                    // Definir funciones cognitivas
                    $tiposFuncionesCognitivas = [
                        'consciencia' => 'Consciencia',
                        'consciencia_otro' => 'Consciencia (otro)',
                        'orientacion' => 'Orientación',
                        'orientacion_otro' => 'Orientación (otro)',
                        'memoria' => 'Memoria',
                        'memoria_otro' => 'Memoria (otro)',
                        'atencion' => 'Atención',
                        'atencion_otro' => 'Atención (otro)',
                        'concentracion' => 'Concentración',
                        'concentracion_otro' => 'Concentración (otro)',
                        'lenguaje' => 'Lenguaje',
                        'lenguaje_otro' => 'Lenguaje (otro)',
                        'pensamiento' => 'Pensamiento',
                        'pensamiento_otro' => 'Pensamiento (otro)',
                        'afecto' => 'Afecto',
                        'afecto_otro' => 'Afecto (otro)',
                        'sensopercepcion' => 'Sensopercepción',
                        'sensopercepcion_otro' => 'Sensopercepción (otro)',
                        'psicomotricidad' => 'Psicomotricidad',
                        'psicomotricidad_otro' => 'Psicomotricidad (otro)',
                        'juicio' => 'Juicio',
                        'juicio_otro' => 'Juicio (otro)',
                        'inteligencia' => 'Inteligencia',
                        'inteligencia_otro' => 'Inteligencia (otro)',
                        'conciencia_enfermedad' => 'Conciencia de enfermedad',
                        'conciencia_enfermedad_otro' => 'Conciencia de enfermedad (otro)',
                        'sufrimiento_psicologico' => 'Sufrimiento psicológico',
                        'sufrimiento_psicologico_otro' => 'Sufrimiento psicológico (otro)',
                        'motivacion_tratamiento' => 'Motivación al tratamiento',
                        'motivacion_tratamiento_otro' => 'Motivación al tratamiento (otro)'
                    ];


                    // Recorrer y actualizar/insertar cada característica
                    foreach ($tiposFuncionesCognitivas as $campo => $nombre) {
                        if (!empty($request[$campo])) {
                            // Si el campo es un array, convertirlo a cadena separada por comas
                            $detalle = is_array($request[$campo]) ? implode(',', $request[$campo]) : $request[$campo];

                            DB::table('funciones_cognitivas')
                                ->updateOrInsert(
                                    [
                                        'id_historia' => $idHistoria,
                                        'caracteristica' => $campo
                                    ],
                                    [
                                        'detalle' => $detalle,
                                        'nombre' => $nombre
                                    ]
                                );
                        }
                    }


                    // Insertar Funciones Somáticas

                    // Filtrar los valores no vacíos
                    $examenMental = array_filter([
                        'id_historia' => $idHistoria,
                        'ciclos_del_sueno' => $request['ciclos_sueno'] ?? null,
                        'apetito' => $request['apetito'] ?? null,
                        'actividades_autocuidado' => $request['autocuidado'] ?? null,
                    ]);

                    // Insertar solo si hay datos válidos
                    if (!empty($examenMental)) {
                        DB::table('funciones_somaticas')->insert($examenMental);
                    }


                    //si es pediatria 
                    if ($request['tipoPsicologia'] == "Pediatría") {
                        // Definir los tipos de antecedentes prenatales
                        $tiposAntecedentesPrenatales = [
                            'edad_madre' => 'Edad de la madre en el embarazo',
                            'enfermedades_madre' => 'Enfermedades de la madre',
                            'numero_embarazo' => 'Único embarazo',
                            'enbarazo_controlado' => 'El embarazo fue controlado por atención médica',
                            'planificacion' => 'Uso de planificación en el momento del embarazo',
                            'estado_madre' => 'Estado de la madre durante el embarazo'
                        ];



                        // Recorrer y actualizar/insertar cada antecedente
                        foreach ($tiposAntecedentesPrenatales as $campo => $nombre) {
                            if (!empty($request[$campo])) {
                                DB::table('antecedentes_prenatales')->updateOrInsert(
                                    [
                                        'id_historia' => $idHistoria,
                                        'tipo' => $campo
                                    ],
                                    [
                                        'detalle' => $request[$campo],
                                        'nombre' => $nombre
                                    ]
                                );
                            }
                        }

                        // Definir los tipos de antecedentes natales
                        $tiposAntecedentesNatales = [
                            'tipo_nacimiento' => 'Tipo de nacimiento',
                            'causa_cesarea' => 'Causa de la cesárea',
                            'reanimacion' => 'Uso de maniobras de reanimación',
                            'peso_nacer' => 'Peso al nacer',
                            'talla_nacer' => 'Talla al nacer',
                            'llanto_nacer' => 'Llanto al nacer'
                        ];



                        // Recorrer y actualizar/insertar cada antecedente
                        foreach ($tiposAntecedentesNatales as $campo => $nombre) {
                            if (!empty($request[$campo])) {
                                DB::table('antecedentes_natales')->updateOrInsert(
                                    [
                                        'id_historia' => $idHistoria,
                                        'tipo' => $campo
                                    ],
                                    [
                                        'detalle' => $request[$campo],
                                        'nombre' => $nombre
                                    ]
                                );
                            }
                        }


                        // Definir los tipos de antecedentes posnatales
                        $tiposAntecedentesPosnatales = [
                            'hospitalizaciones_postnatales' => 'Hospitalizaciones recién nacido',
                            'desarrollo_psicomotor' => 'Desarrollo psicomotor'
                        ];



                        // Recorrer y actualizar/insertar cada antecedente
                        foreach ($tiposAntecedentesPosnatales as $campo => $nombre) {
                            if (!empty($request[$campo])) {
                                DB::table('antecedentes_posnatales')->updateOrInsert(
                                    [
                                        'id_historia' => $idHistoria,
                                        'tipo' => $campo
                                    ],
                                    [
                                        'detalle' => $request[$campo],
                                        'nombre' => $nombre
                                    ]
                                );
                            }
                        }


                        // Definir los tipos de desarrollo psicomotor
                        $tiposDesarrolloPsicomotor = [
                            'control_cefalico' => 'Control cefálico',
                            'rolado' => 'Rolado',
                            'sedente_solo' => 'Sedente solo',
                            'gateo' => 'Gateo',
                            'bipedo' => 'Bípedo sin ayuda',
                            'marcha' => 'Marcha',
                            'lenguaje_verbal' => 'Lenguaje verbal',
                            'lenguaje_verbal_fluido' => 'Lenguaje verbal fluido'
                        ];


                        // Recorrer y actualizar/insertar cada antecedente
                        foreach ($tiposDesarrolloPsicomotor as $campo => $nombre) {
                            if (!empty($request[$campo])) {
                                DB::table('desarrollo_psicomotor')->updateOrInsert(
                                    [
                                        'id_historia' => $idHistoria,
                                        'tipo' => $campo
                                    ],
                                    [
                                        'detalle' => $request[$campo],
                                        'nombre' => $nombre
                                    ]
                                );
                            }
                        }
                    }


                    // Confirmar transacción
                    DB::commit();

                    return [
                        'success' => true,
                        'idHistoria' => $idHistoria,
                        'message' => 'Historia clínica guardada exitosamente'
                    ];
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('Error al insertar historia clínica: ' . $e->getMessage(), [
                        'data' => $request,
                        'file' => $e->getFile(),
                        'line' => $e->getLine()
                    ]);

                    return [
                        'success' => false,
                        'message' => 'Error al guardar la historia clínica: ' . $e->getMessage()
                    ];
                }
            } else {
                // Edición de historia existente
                DB::beginTransaction();
                try {
                    $idHistoria = $request['idHistoria'];


                    // Mapeo de campos del request a campos de la base de datos
                    $camposMapping = [
                        'idPaciente' => 'id_paciente',
                        'idProfesional' => 'id_profesional',
                        'primeraVez' => 'primera_vez',
                        'remision' => 'remision',
                        'codConsulta' => 'codigo_consulta',
                        'motivoConsulta' => 'motivo_consulta',
                        'motivoConsultaOtro' => 'otro_motivo_consulta',
                        'enfermedadActual' => 'enfermedad_actual',
                        'codDiagnostico' => 'dx_principal',
                        'codImpresionDiagnostico' => 'codigo_diagnostico',
                        'establecidoPrimeraVez' => 'diagnostico_primera_vez',
                        'plan_intervencion' => 'plan_intervencion',
                        'objetivo_general' => 'objetivo_general',
                        'objetivos_especificos' => 'objetivos_especificos',
                        'sugerencia_interconsultas' => 'sugerencias_interconsultas',
                        'observaciones_recomendaciones' => 'observaciones_recomendaciones',
                        'tipoPsicologia' => 'tipologia',
                        'resumen_evaluacion_inicial' => 'eval_inicial',
                        'otro_CodDiagnostico' => 'otro_dx_principal',
                        'otra_ImpresionDiagnostica' => 'otro_cod_diagnostico'
                    ];

                    $datosActualizar = [];

                    // Recorrer el mapeo y construir el array de actualización
                    foreach ($camposMapping as $campoRequest => $campoDB) {
                        if (array_key_exists($campoRequest, $request)) {
                            if (is_array($request[$campoRequest])) {
                                $datosActualizar[$campoDB] = implode(',', $request[$campoRequest]);
                            } else {
                                $datosActualizar[$campoDB] = $request[$campoRequest];
                            }
                        } else {
                            // Si no existe el campo en la solicitud, asignamos un valor vacío (o null si prefieres)
                            $datosActualizar[$campoDB] = null;
                        }
                    }

                    if (!empty($datosActualizar)) {
                        $historia = DB::table('historia_clinica')
                            ->where('id', $idHistoria)
                            ->update($datosActualizar);
                    }

                    // Actualizar antecedentes médicos
                    $tiposAntecedentes = [
                        'quirurgicos' => 'Quirúrgico',
                        'toxicos' => 'Tóxicos',
                        'traumaticos' => 'Traumáticos',
                        'medicacion' => 'Medicación',
                        'paraclinicos' => 'Paraclínicos',
                        'hospitalizaciones' => 'Hospitalizaciones',
                        'patologia' => 'Patología'
                    ];

                    foreach ($tiposAntecedentes as $tipo => $nombre) {
                        if (array_key_exists($tipo, $request)) {
                            DB::table('antecedentes_medicos')
                                ->updateOrInsert(
                                    [
                                        'id_historia' => $idHistoria,
                                        'tipo' => $tipo
                                    ],
                                    [
                                        'detalle' => $request[$tipo],
                                        'nombre' => $nombre
                                    ]
                                );
                        }
                    }

                    // Insertar antecedentes familiares
                    $tiposAntecedentesFamiliares = [
                        'depresion' => ['nombre' => 'Depresión', 'esArray' => true],
                        'ansiedad' => ['nombre' => 'Ansiedad', 'esArray' => true],
                        'demencia' => ['nombre' => 'Demencia', 'esArray' => true],
                        'alcoholismo' => ['nombre' => 'Alcoholismo', 'esArray' => true],
                        'drogadiccion' => ['nombre' => 'Drogadicción', 'esArray' => true],
                        'discapacidad_intelectual' => ['nombre' => 'Discapacidad intelectual', 'esArray' => true],
                        'patologicos' => ['nombre' => 'Patológicos', 'esArray' => false],
                        'otros' => ['nombre' => 'Otros', 'esArray' => false]
                    ];

                    $antecedentesFamiliares = [];
                    // Para edición:
                    foreach ($tiposAntecedentesFamiliares as $tipo => $config) {
                        if (array_key_exists($tipo, $request)) {
                            $detalle = $config['esArray'] ?
                                (is_array($request[$tipo]) ? implode(',', $request[$tipo]) : $request[$tipo]) :
                                $request[$tipo];

                            DB::table('antecedentes_familiares')
                                ->updateOrInsert(
                                    [
                                        'id_historia' => $idHistoria,
                                        'tipo' => $tipo
                                    ],
                                    [
                                        'detalle' => $detalle,
                                        'nombre' => $config['nombre']
                                    ]
                                );
                        }
                    }

                    // Definir las áreas de ajuste y/o desempeño
                    $areasAjusteDesempeno = [
                        'historia_educativa' => 'Historia educativa',
                        'historia_laboral' => 'Historia laboral',
                        'historia_familiar' => 'Historia familiar',
                        'historia_social' => 'Historia social',
                        'historia_socio_afectiva' => 'Historia socio-afectiva'
                    ];

                    // Eliminar registros anteriores
                    DB::table('historia_ajuste_desempeno')->where('id_historia', $idHistoria)->delete();

                    // Recorrer y actualizar/insertar cada área
                    foreach ($areasAjusteDesempeno as $campo => $nombre) {
                        if (!empty($request[$campo])) {
                            DB::table('historia_ajuste_desempeno')
                                ->updateOrInsert(
                                    [
                                        'id_historia' => $idHistoria,
                                        'area' => $campo
                                    ],
                                    [
                                        'detalle' => $request[$campo],
                                        'nombre' => $nombre
                                    ]
                                );
                        }
                    }

                    // Definir los tipos de interconsultas
                    $tiposInterconsultas = [
                        'intervencion_psiquiatria' => 'Intervención psiquiátrica',
                        'intervencion_neurologia' => 'Intervención neurológica',
                        'intervencion_neuropsicologia' => 'Intervención neuropsicológica'
                    ];

                    // Eliminar registros anteriores
                    DB::table('interconsultas')->where('id_historia', $idHistoria)->delete();

                    // Recorrer y actualizar/insertar cada interconsulta
                    foreach ($tiposInterconsultas as $campo => $nombre) {
                        if (!empty($request[$campo])) {
                            DB::table('interconsultas')
                                ->updateOrInsert(
                                    [
                                        'id_historia' => $idHistoria,
                                        'tipo' => $campo
                                    ],
                                    [
                                        'detalle' => $request[$campo],
                                        'nombre' => $nombre
                                    ]
                                );
                        }
                    }


                    // Definir las características de apariencia personal
                    $tiposAparienciaPersonal = [
                        'edad' => 'Edad',
                        'edad_otro' => 'Edad (otro)',
                        'desarrollo' => 'Desarrollo pondoestatural',
                        'desarrollo_otro' => 'Desarrollo pondoestatural (otro)',
                        'aseo' => 'Aseo y Arreglo',
                        'aseo_otro' => 'Aseo y arreglo (otro)',
                        'salud' => 'Salud somática',
                        'salud_otro' => 'Salud somática (otro)',
                        'facies' => 'Facies',
                        'facies_otro' => 'Facies (otro)',
                        'biotipo' => 'Biotipo',
                        'biotipo_otro' => 'Biotipo (otro)',
                        'actitud' => 'Actitud',
                        'actitud_otro' => 'Actitud (otro)'
                    ];

                    // Eliminar registros anteriores
                    DB::table('apariencia_personal')->where('id_historia', $idHistoria)->delete();

                    // Recorrer y actualizar/insertar cada característica
                    foreach ($tiposAparienciaPersonal as $campo => $nombre) {
                        if (!empty($request[$campo])) {
                            // Si el campo es un array, convertirlo a cadena separada por comas
                            $detalle = is_array($request[$campo]) ? implode(',', $request[$campo]) : $request[$campo];

                            DB::table('apariencia_personal')
                                ->updateOrInsert(
                                    [
                                        'id_historia' => $idHistoria,
                                        'caracteristica' => $campo
                                    ],
                                    [
                                        'detalle' => $detalle,
                                        'nombre' => $nombre
                                    ]
                                );
                        }
                    }


                    // Definir funciones cognitivas
                    $tiposFuncionesCognitivas = [
                        'consciencia' => 'Consciencia',
                        'consciencia_otro' => 'Consciencia (otro)',
                        'orientacion' => 'Orientación',
                        'orientacion_otro' => 'Orientación (otro)',
                        'memoria' => 'Memoria',
                        'memoria_otro' => 'Memoria (otro)',
                        'atencion' => 'Atención',
                        'atencion_otro' => 'Atención (otro)',
                        'concentracion' => 'Concentración',
                        'concentracion_otro' => 'Concentración (otro)',
                        'lenguaje' => 'Lenguaje',
                        'lenguaje_otro' => 'Lenguaje (otro)',
                        'pensamiento' => 'Pensamiento',
                        'pensamiento_otro' => 'Pensamiento (otro)',
                        'afecto' => 'Afecto',
                        'afecto_otro' => 'Afecto (otro)',
                        'sensopercepcion' => 'Sensopercepción',
                        'sensopercepcion_otro' => 'Sensopercepción (otro)',
                        'psicomotricidad' => 'Psicomotricidad',
                        'psicomotricidad_otro' => 'Psicomotricidad (otro)',
                        'juicio' => 'Juicio',
                        'juicio_otro' => 'Juicio (otro)',
                        'inteligencia' => 'Inteligencia',
                        'inteligencia_otro' => 'Inteligencia (otro)',
                        'conciencia_enfermedad' => 'Conciencia de enfermedad',
                        'conciencia_enfermedad_otro' => 'Conciencia de enfermedad (otro)',
                        'sufrimiento_psicologico' => 'Sufrimiento psicológico',
                        'sufrimiento_psicologico_otro' => 'Sufrimiento psicológico (otro)',
                        'motivacion_tratamiento' => 'Motivación al tratamiento',
                        'motivacion_tratamiento_otro' => 'Motivación al tratamiento (otro)'
                    ];

                    // Eliminar registros anteriores
                    DB::table('funciones_cognitivas')->where('id_historia', $idHistoria)->delete();

                    // Recorrer y actualizar/insertar cada característica
                    foreach ($tiposFuncionesCognitivas as $campo => $nombre) {
                        if (!empty($request[$campo])) {
                            // Si el campo es un array, convertirlo a cadena separada por comas
                            $detalle = is_array($request[$campo]) ? implode(',', $request[$campo]) : $request[$campo];

                            DB::table('funciones_cognitivas')
                                ->updateOrInsert(
                                    [
                                        'id_historia' => $idHistoria,
                                        'caracteristica' => $campo
                                    ],
                                    [
                                        'detalle' => $detalle,
                                        'nombre' => $nombre
                                    ]
                                );
                        }
                    }

                    // Insertar Funciones Somáticas
                    // Eliminar registros anteriores
                    DB::table('funciones_somaticas')->where('id_historia', $idHistoria)->delete();

                    // Filtrar los valores no vacíos
                    $examenMental = array_filter([
                        'id_historia' => $idHistoria,
                        'ciclos_del_sueno' => $request['ciclos_sueno'] ?? null,
                        'apetito' => $request['apetito'] ?? null,
                        'actividades_autocuidado' => $request['autocuidado'] ?? null,
                    ]);

                    // Insertar solo si hay datos válidos
                    if (!empty($examenMental)) {
                        DB::table('funciones_somaticas')->insert($examenMental);
                    }


                    //si es pediatria 
                    if ($request['tipoPsicologia'] == "Pediatría") {
                        // Definir los tipos de antecedentes prenatales
                        $tiposAntecedentesPrenatales = [
                            'edad_madre' => 'Edad de la madre en el embarazo',
                            'enfermedades_madre' => 'Enfermedades de la madre',
                            'numero_embarazo' => 'Único embarazo',
                            'enbarazo_controlado' => 'El embarazo fue controlado por atención médica',
                            'planificacion' => 'Uso de planificación en el momento del embarazo',
                            'estado_madre' => 'Estado de la madre durante el embarazo'
                        ];

                        // Eliminar registros anteriores
                        DB::table('antecedentes_prenatales')->where('id_historia', $idHistoria)->delete();

                        // Recorrer y actualizar/insertar cada antecedente
                        foreach ($tiposAntecedentesPrenatales as $campo => $nombre) {
                            if (!empty($request[$campo])) {
                                DB::table('antecedentes_prenatales')->updateOrInsert(
                                    [
                                        'id_historia' => $idHistoria,
                                        'tipo' => $campo
                                    ],
                                    [
                                        'detalle' => $request[$campo],
                                        'nombre' => $nombre
                                    ]
                                );
                            }
                        }

                        // Definir los tipos de antecedentes natales
                        $tiposAntecedentesNatales = [
                            'tipo_nacimiento' => 'Tipo de nacimiento',
                            'causa_cesarea' => 'Causa de la cesárea',
                            'reanimacion' => 'Uso de maniobras de reanimación',
                            'peso_nacer' => 'Peso al nacer',
                            'talla_nacer' => 'Talla al nacer',
                            'llanto_nacer' => 'Llanto al nacer'
                        ];

                        // Eliminar registros anteriores
                        DB::table('antecedentes_natales')->where('id_historia', $idHistoria)->delete();

                        // Recorrer y actualizar/insertar cada antecedente
                        foreach ($tiposAntecedentesNatales as $campo => $nombre) {
                            if (!empty($request[$campo])) {
                                DB::table('antecedentes_natales')->updateOrInsert(
                                    [
                                        'id_historia' => $idHistoria,
                                        'tipo' => $campo
                                    ],
                                    [
                                        'detalle' => $request[$campo],
                                        'nombre' => $nombre
                                    ]
                                );
                            }
                        }


                        // Definir los tipos de antecedentes posnatales
                        $tiposAntecedentesPosnatales = [
                            'hospitalizaciones_postnatales' => 'Hospitalizaciones recién nacido',
                            'desarrollo_psicomotor' => 'Desarrollo psicomotor'
                        ];

                        // Eliminar registros anteriores
                        DB::table('antecedentes_posnatales')->where('id_historia', $idHistoria)->delete();

                        // Recorrer y actualizar/insertar cada antecedente
                        foreach ($tiposAntecedentesPosnatales as $campo => $nombre) {
                            if (!empty($request[$campo])) {
                                DB::table('antecedentes_posnatales')->updateOrInsert(
                                    [
                                        'id_historia' => $idHistoria,
                                        'tipo' => $campo
                                    ],
                                    [
                                        'detalle' => $request[$campo],
                                        'nombre' => $nombre
                                    ]
                                );
                            }
                        }


                        // Definir los tipos de desarrollo psicomotor
                        $tiposDesarrolloPsicomotor = [
                            'control_cefalico' => 'Control cefálico',
                            'rolado' => 'Rolado',
                            'sedente_solo' => 'Sedente solo',
                            'gateo' => 'Gateo',
                            'bipedo' => 'Bípedo sin ayuda',
                            'marcha' => 'Marcha',
                            'lenguaje_verbal' => 'Lenguaje verbal',
                            'lenguaje_verbal_fluido' => 'Lenguaje verbal fluido'
                        ];

                        // Eliminar registros anteriores
                        DB::table('desarrollo_psicomotor')->where('id_historia', $idHistoria)->delete();

                        // Recorrer y actualizar/insertar cada antecedente
                        foreach ($tiposDesarrolloPsicomotor as $campo => $nombre) {
                            if (!empty($request[$campo])) {
                                DB::table('desarrollo_psicomotor')->updateOrInsert(
                                    [
                                        'id_historia' => $idHistoria,
                                        'tipo' => $campo
                                    ],
                                    [
                                        'detalle' => $request[$campo],
                                        'nombre' => $nombre
                                    ]
                                );
                            }
                        }
                    }


                    // Confirmar transacción
                    DB::commit();
                    return [
                        'success' => true,
                        'idHistoria' => $idHistoria,
                        'message' => 'Historia clínica actualizada exitosamente'
                    ];
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('Error al actualizar historia clínica: ' . $e->getMessage(), [
                        'idHistoria' => $idHistoria,
                        'data' => $request,
                        'file' => $e->getFile(),
                        'line' => $e->getLine()
                    ]);

                    return [
                        'success' => false,
                        'message' => 'Error al actualizar la historia clínica',
                        'error' => $e->getMessage()
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::error('Error general en Guardar: ' . $e->getMessage(), [
                'data' => $request,
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return [
                'success' => false,
                'message' => 'Error general al procesar la historia clínica',
                'error' => $e->getMessage()
            ];
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
                        'id_profesional' => $request['profesionalConsulta'] ?? null,
                        'fecha_consulta' => $request['fechaEvolucion'] . ' ' . $request['horaSeleccionada'],
                        'codigo_consulta' => $request['codConsultaConsulta'] ?? null,
                        'impresion_diagnostica' => $request['codImpresionDiagnosticoConsulta']  ?? null,
                        'motivo' => $request['motivoConsultaModal'] ?? null,
                        'objetivo_sesion' => $request['objetivo_sesion'] ?? null,
                        'tecnicas_utilizadas' => $request['tecnicas_utilizadas'] ?? null,
                        'actividades_especificas' => $request['actividades_especificas'] ?? null,
                        'evaluacion_indicadores' => $request['evaluacion_indicadores'] ?? null,
                        'evolucion_sesion' => $request['evolucion_sesion'] ?? null,
                        'estado' => 'ACTIVO',
                        'otra_impresion_diagnostica' => $request['otra_ImpresionDiagnosticaConsulta'] ?? null
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
                        ->where('servicios.tipo_servicio', 'PSICOLOGÍA')
                        ->where('servicios.estado', 'ACTIVO')
                        ->select(
                            'ventas.id',
                            'ventas.cantidad',
                            DB::raw('ventas.cantidad - COUNT(sesiones_paquete_uso.id) as sesiones_disponibles')
                        )
                        ->groupBy('ventas.id', 'ventas.cantidad', 'ventas.cantidad') // Agregar GROUP BY
                        ->first();



                    //VALIAR SI EL sesiones_disponibles ES 1 PARA CAMBIAR EL ESTADO A TERMIANDO
                    if ($paquete) {
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
                        'id_profesional' => $request['profesionalConsulta'] ?? null,
                        'codigo_consulta' => $request['codConsultaConsulta'] ?? null,
                        'impresion_diagnostica' => $request['codImpresionDiagnosticoConsulta']  ?? null,
                        'motivo' => $request['motivoConsultaModal'] ?? null,
                        'objetivo_sesion' => $request['objetivo_sesion'] ?? null,
                        'tecnicas_utilizadas' => $request['tecnicas_utilizadas'] ?? null,
                        'actividades_especificas' => $request['actividades_especificas'] ?? null,
                        'evaluacion_indicadores' => $request['evaluacion_indicadores'] ?? null,
                        'evolucion_sesion' => $request['evolucion_sesion'] ?? null,
                        'otra_impresion_diagnostica' => $request['otra_ImpresionDiagnosticaConsulta'] ?? null
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
            ->where("profesionales.id", $historia->id_profesional)
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

    public static function busquedaConsultaImprimir($idConsulta)
    {
        $consulta = DB::connection('mysql')->table('consultas_psicologica')
            ->where("id", $idConsulta)
            ->first();

        $consulta->impresion_diagnostica = DB::connection('mysql')->table('referencia_cie10')
            ->where("id", $consulta->impresion_diagnostica)
            ->select(DB::raw('CONCAT(codigo, " - ", nombre) AS diagnostico'))
            ->first();

        $consulta->codigo_consulta = DB::connection('mysql')->table('referencia_cups')
            ->where("id", $consulta->codigo_consulta)
            ->select(DB::raw('CONCAT(codigo, " - ", nombre) AS consulta'))
            ->first();

        return $consulta;
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
