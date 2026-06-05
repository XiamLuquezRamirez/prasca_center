<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Monolog\Handler\IFTTTHandler;

function limpiarValores($valor)
{
    return !is_null($valor) && $valor !== '';
}

class HistoriaNeuroPsicologica extends Model
{


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
            ->where('servicios.tipo_historia', 'NEUROPSICOLOGIA')
            ->first();
        if ($venta) {
            if ($venta->estadoVentaConsulta == "PENDIENTE") {
                $venta->estadoVentaConsulta = "COMPRADA";
            }
        }

        return $venta;
    }

    public static function busquedaExamenMental($idHisto)
    {
        return DB::connection('mysql')->table('examen_mental_neuro')
            ->where("id_historia", $idHisto)
            ->first();
    }

    
    public static function busquedaConsultaImprimir($idConsulta)
    {
        $consulta = DB::connection('mysql')->table('consultas_psicologica_neuro')
            ->where("id", $idConsulta)
            ->first();       

        return $consulta;
            
    }

    public static function busquedaConsultaHistoria($idHistoria)
    {
        $consulta = DB::table('historia_clinica_neuro')
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

    public static function busquedaAnexosInformes($idInf)
    {
        $anexos = DB::connection('mysql')->table('anexos_informe_neuropsicologia')
            ->where("id_informe", $idInf)
            ->get();

        return $anexos;
    }

    public static function busquedaOrdenMedica($idInf)
    {
        $ordenMedica = DB::connection('mysql')->table('orden_medica_neuro')
            ->leftJoin('referencia_cups', 'orden_medica_neuro.codigo_cups', 'referencia_cups.id')
            ->select('referencia_cups.id', 'referencia_cups.codigo', 'orden_medica_neuro.cantidad', 'orden_medica_neuro.observacion', 'referencia_cups.nombre as textoCodigo')
            ->where("id_tipo", $idInf)
            ->get();

        return $ordenMedica;
    }

    public static function guardarPlanIntervencion($request)
    {
        try {
            DB::beginTransaction();
            try {
                $idHistoria = $request['idHistoriaPlan'];

                DB::table('historia_clinica_neuro')->where('id', $idHistoria)->update(array_filter([
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

    public static function busquedaPlanIntervencion($idHistoria)
    {
        $plan = DB::table('historia_clinica_neuro')
            ->select('objetivo_general', 'objetivos_especificos', 'sugerencias_interconsultas', 'observaciones_recomendaciones')
            ->where('id', $idHistoria)
            ->first();

        return $plan;
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
                        'remision' => 'remision',
                        'codConsulta' => 'codigo_consulta',
                        'motivoConsulta' => 'motivo_consulta',
                        'motivoConsultaTexto' => 'motivo_consulta_texto',
                        'enfermedadActual' => 'enfermedad_actual',
                        'codDiagnostico' => 'dx_principal',
                        'codImpresionDiagnostico' => 'codigo_diagnostico',
                        'diagnostico_primera_vez' => 'diagnostico_primera_vez',
                        'objetivo_general' => 'objetivo_general',
                        'objetivos_especificos' => 'objetivos_especificos',
                        'sugerencias_interconsultas' => 'sugerencias_interconsultas',
                        'observaciones_recomendaciones' => 'observaciones_recomendaciones',
                        'tipoPsicologia' => 'tipologia',
                        'plan_intervension' => 'plan_intervension',
                        'completa' => 'completa',
                        'porcentaje_completitud' => 'porcentaje_completitud',
                        'codDiagnosticoRelacionado1' => 'dx_principal1',
                        'codDiagnosticoRelacionado2' => 'dx_principal2',
                        'codImpresionDiagnosticoRelacionado1' => 'codigo_diagnostico1',
                        'codImpresionDiagnosticoRelacionado2' => 'codigo_diagnostico2',

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
                    if($request['completa'] == '0'){
                        $datosInsertar['estado_hitoria'] = 'abierta';
                    }else{
                        $datosInsertar['estado_hitoria'] = 'cerrada';
                    }
                    $datosInsertar['estado_registro'] = 'ACTIVO';
                    $datosInsertar['fecha_historia'] = now();

                    if (!empty($datosInsertar)) {
                        $idHistoria = DB::table('historia_clinica_neuro')->insertGetId($datosInsertar);
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
                            DB::table('antecedentes_medicos_neuro')->insert([
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
                            $detalle = $config['esArray'] && is_array($request[$tipo]) 
                                ? implode(',', $request[$tipo]) 
                                : $request[$tipo];
                                
                            DB::table('antecedentes_familiares_neuro')
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
                        }else{
                            DB::table('antecedentes_familiares_neuro')
                                ->updateOrInsert(
                                    [
                                        'id_historia' => $idHistoria,
                                        'tipo' => $tipo
                                    ],
                                    [
                                        'detalle' => '',
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
                        'historia_socio_afectiva' => 'Historia emocional/afectiva',
                        'historia_deportiva' => 'Historia deportiva'
                    ];

                    foreach ($areasAjusteDesempeno as $campo => $nombre) {
                        if (!empty($request[$campo])) {
                            DB::table('historia_ajuste_desempeno_neuro')
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

                    foreach ($tiposInterconsultas as $campo => $nombre) {
                        if (!empty($request[$campo])) {
                            DB::table('interconsultas_neuro')
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
                        }else{
                            DB::table('interconsultas_neuro')
                                ->updateOrInsert(
                                    [
                                        'id_historia' => $idHistoria,
                                        'tipo' => $campo
                                    ],
                                    [
                                        'detalle' => '',
                                        'nombre' => $nombre
                                    ]   
                                );
                        }
                    }

               
                    // Filtrar los valores no vacíos
                    $examenMental = array_filter([
                        'id_historia' => $idHistoria,
                        'examen_mental' => $request['examen_mental'] ?? null,
                        'ciclos_del_sueno' => $request['ciclos_sueno'] ?? null,
                        'apetito' => $request['apetito'] ?? null,
                        'actividades_autocuidado' => $request['autocuidado'] ?? null,
                    ]);

                    // Insertar solo si hay datos válidos
                    if (!empty($examenMental)) {
                        DB::table('examen_mental_neuro')->insert($examenMental);
                    }                 

                    /// En el caso de que sea pediatria
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
                                DB::table('antecedentes_prenatales_neuro')
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
                            }else{
                                DB::table('antecedentes_prenatales_neuro')
                                    ->updateOrInsert(
                                        [
                                            'id_historia' => $idHistoria,
                                            'tipo' => $campo
                                        ],
                                        [
                                            'detalle' => '',
                                            'nombre' => $nombre
                                        ]
                                    );
                            }


                        }

                        // Insertar antecedentes natales

                        $tiposAntecedentesNatales = [
                            'tipo_nacimiento' => 'Tipo de nacimiento',
                            'causa_cesarea' => 'Causa de la cesárea',
                            'reanimacion' => 'Uso de maniobras de reanimación',
                            'peso_nacer' => 'Peso al nacer',
                            'talla_nacer' => 'Talla al nacer',
                            'llanto_nacer' => 'Llanto al nacer'
                        ];

                        foreach ($tiposAntecedentesNatales as $campo => $nombre) {
                            if (!empty($request[$campo])) {
                                DB::table('antecedentes_natales_neuro')
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
                            }else{
                                DB::table('antecedentes_natales_neuro')
                                    ->updateOrInsert(
                                        [
                                            'id_historia' => $idHistoria,
                                            'tipo' => $campo
                                        ],
                                        [
                                            'detalle' => '',
                                            'nombre' => $nombre
                                        ]
                                    );
                            }
                        }

                        // Insertar antecedentes posnatales

                        $tiposAntecedentesPosnatales = [
                            'hospitalizaciones_postnatales' => 'Hospitalizaciones recién nacido',
                            'desarrollo_psicomotor' => 'Desarrollo psicomotor'
                        ];

                        foreach ($tiposAntecedentesPosnatales as $campo => $nombre) {
                            if (!empty($request[$campo])) {
                                DB::table('antecedentes_posnatales_neuro')
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
                            }else{
                                DB::table('antecedentes_posnatales_neuro')
                                    ->updateOrInsert(
                                        [
                                            'id_historia' => $idHistoria,
                                            'tipo' => $campo
                                        ],
                                        [
                                            'detalle' => '',
                                            'nombre' => $nombre
                                        ]
                                    );
                            }
                        }
                        

                        // Insertar desarrollo psicomotor

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

                        foreach ($tiposDesarrolloPsicomotor as $campo => $nombre) {
                            if (!empty($request[$campo])) {
                                DB::table('desarrollo_psicomotor_neuro')
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
                            }else{
                                DB::table('desarrollo_psicomotor_neuro')
                                    ->updateOrInsert(
                                        [
                                            'id_historia' => $idHistoria,
                                            'tipo' => $campo
                                        ],
                                        [
                                            'detalle' => '',
                                            'nombre' => $nombre
                                        ]
                                    );
                            }
                        }
                            
                    }

                    // Insertar ordenes medicas
                    if (isset($request['codigoOrdenMedica']) && is_array($request['codigoOrdenMedica'])) {
                        foreach ($request['codigoOrdenMedica'] as $key => $codigo) {
                            DB::connection('mysql')->table('orden_medica_neuro')->insert([
                                'tipo' => 'HISTORIA',
                                'id_tipo' => $idHistoria,
                                'codigo_cups' => $codigo,
                                'cantidad' => $request['cantidadOrdenMedica'][$key] ?? null,
                                'observacion' => $request['observacionOrdenMedica'][$key] ?? null,
                            ]);
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
                DB::beginTransaction();
                try {
                    $idHistoria = $request['idHistoria'];
                    // Insertar en `historia_clinica`

                    // Mapeo de campos del request a campos de la base de datos
                    $camposMapping = [
                        'idPaciente' => 'id_paciente',
                        'idProfesional' => 'id_profesional',
                        'remision' => 'remision',
                        'codConsulta' => 'codigo_consulta',
                        'motivoConsulta' => 'motivo_consulta',
                        'motivoConsultaTexto' => 'motivo_consulta_texto',
                        'enfermedadActual' => 'enfermedad_actual',
                        'codDiagnostico' => 'dx_principal',
                        'codImpresionDiagnostico' => 'codigo_diagnostico',
                        'diagnostico_primera_vez' => 'diagnostico_primera_vez',
                        'objetivo_general' => 'objetivo_general',
                        'objetivos_especificos' => 'objetivos_especificos',
                        'sugerencias_interconsultas' => 'sugerencias_interconsultas',
                        'observaciones_recomendaciones' => 'observaciones_recomendaciones',
                        'tipoPsicologia' => 'tipologia',
                        'plan_intervension' => 'plan_intervension',
                        'completa' => 'completa',
                        'porcentaje_completitud' => 'porcentaje_completitud',
                        'codDiagnosticoRelacionado1' => 'dx_principal1',
                        'codDiagnosticoRelacionado2' => 'dx_principal2',
                        'codImpresionDiagnosticoRelacionado1' => 'codigo_diagnostico1',
                        'codImpresionDiagnosticoRelacionado2' => 'codigo_diagnostico2'
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

                    if($request['completa'] == '0'){
                        $datosActualizar['estado_hitoria'] = 'abierta';
                    }else{
                        $datosActualizar['estado_hitoria'] = 'cerrada';
                    }

                    if (!empty($datosActualizar)) {
                        $historia = DB::table('historia_clinica_neuro')
                            ->where('id', $idHistoria)
                            ->update($datosActualizar);
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
                            DB::table('antecedentes_medicos_neuro')
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
                        }else{
                            DB::table('antecedentes_medicos_neuro')
                                ->updateOrInsert(
                                    [
                                        'id_historia' => $idHistoria,
                                        'tipo' => $tipo
                                    ],
                                    [
                                        'detalle' => '',
                                        'nombre' => $nombre     
                                    ]   
                                );
                        }   
                    }
                    
                    // Insertar antecedentes familiares
                    DB::table('antecedentes_familiares_neuro')->where('id_historia', $idHistoria)->delete();
                    // Definir los tipos de antecedentes familiares
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
                            $detalle = $config['esArray'] && is_array($request[$tipo]) 
                                ? implode(',', $request[$tipo]) 
                                : $request[$tipo];
                                
                            DB::table('antecedentes_familiares_neuro')
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
                        }else{
                            DB::table('antecedentes_familiares_neuro')
                                ->updateOrInsert(
                                    [
                                        'id_historia' => $idHistoria,
                                        'tipo' => $tipo
                                    ],
                                    [
                                        'detalle' => '',
                                        'nombre' => $config['nombre']
                                    ]
                                );
                        }
                    }

                    // Insertar áreas de ajuste y/o desempeño
                    
                    DB::table('historia_ajuste_desempeno_neuro')->where('id_historia', $idHistoria)->delete();


                    $tiposAjusteDesempeno = [
                        'historia_educativa' => 'Historia educativa',
                        'historia_laboral' => 'Historia laboral',
                        'historia_familiar' => 'Historia familiar',
                        'historia_social' => 'Historia social',
                        'historia_socio_afectiva' => 'Historia emocional/afectiva',
                        'historia_deportiva' => 'Historia deportiva'
                    ];

                    foreach ($tiposAjusteDesempeno as $tipo => $nombre) {
                        if (array_key_exists($tipo, $request)) {
                            DB::table('historia_ajuste_desempeno_neuro')
                                ->updateOrInsert(
                                    [
                                        'id_historia' => $idHistoria,
                                        'area' => $tipo
                                    ],
                                    [
                                        'detalle' => $request[$tipo],
                                        'nombre' => $nombre
                                    ]
                                );
                        }else{  
                            DB::table('historia_ajuste_desempeno_neuro')
                                ->updateOrInsert(
                                    [
                                        'id_historia' => $idHistoria,
                                        'area' => $tipo
                                    ],
                                    [   
                                        'detalle' => '',
                                        'nombre' => $nombre
                                    ]
                                );
                        }
                    }

                    // Insertar interconsultas

                    DB::table('interconsultas_neuro')->where('id_historia', $idHistoria)->delete();

                    $tiposInterconsultas = [
                        'intervencion_psiquiatria' => 'Intervención psiquiátrica',
                        'intervencion_neurologia' => 'Intervención neurológica',
                        'intervencion_neuropsicologia' => 'Intervención neuropsicológica'
                    ];
                    
                    foreach ($tiposInterconsultas as $tipo => $nombre) {
                        if (array_key_exists($tipo, $request)) {
                            DB::table('interconsultas_neuro')
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
                        }else{
                            DB::table('interconsultas_neuro')
                                ->updateOrInsert(
                                    [
                                        'id_historia' => $idHistoria,
                                        'tipo' => $tipo
                                    ],
                                    [
                                        'detalle' => '',
                                        'nombre' => $nombre
                                    ]
                                );
                        }
                    }
                   

                    // Insertar Funciones Somáticas
                    DB::table('examen_mental_neuro')->where('id_historia', $idHistoria)->delete();

                    $examenMental = array_filter([
                        'id_historia' => $idHistoria,
                        'examen_mental' => $request['examen_mental'],
                        'ciclos_del_sueno' => $request['ciclos_sueno'],
                        'apetito' => $request['apetito'],
                        'actividades_autocuidado' => $request['autocuidado'],
                    ]);

                    if (!empty($examenMental)) {
                        DB::table('examen_mental_neuro')->insert($examenMental);
                    }
                    

                    /// En el caso de que sea pediatria
                    if ($request['tipoPsicologia'] == "Pediatría") {
                        // Insertar antecedentes prenatales
                        DB::table('antecedentes_prenatales_neuro')->where('id_historia', $idHistoria)->delete();

                        $tiposAntecedentesPrenatales = [
                            'edad_madre' => 'Edad de la madre en el embarazo',
                            'enfermedades_madre' => 'Enfermedades de la madre',
                            'numero_embarazo' => 'Único embarazo',
                            'enbarazo_controlado' => 'El embarazo fue controlado por atención médica',
                            'planificacion' => 'Uso de planificación en el momento del embarazo',
                            'estado_madre' => 'Estado de la madre durante el embarazo'
                        ];

                        foreach ($tiposAntecedentesPrenatales as $tipo => $nombre) {
                            if (array_key_exists($tipo, $request)) {
                                DB::table('antecedentes_prenatales_neuro')
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
                            }else{
                                DB::table('antecedentes_prenatales_neuro')
                                    ->updateOrInsert(
                                        [
                                            'id_historia' => $idHistoria,
                                            'tipo' => $tipo
                                        ],
                                        [
                                            'detalle' => '',
                                            'nombre' => $nombre
                                        ]
                                    );
                            }
                        }

                        // Insertar antecedentes natales
                        DB::table('antecedentes_natales_neuro')->where('id_historia', $idHistoria)->delete();

                        $tiposAntecedentesNatales = [
                            'tipo_nacimiento' => 'Tipo de nacimiento',
                            'causa_cesarea' => 'Causa de la cesárea',
                            'reanimacion' => 'Uso de maniobras de reanimación',
                            'peso_nacer' => 'Peso al nacer',
                            'talla_nacer' => 'Talla al nacer',
                            'llanto_nacer' => 'Llanto al nacer'
                        ];

                        foreach ($tiposAntecedentesNatales as $tipo => $nombre) {
                            if (array_key_exists($tipo, $request)) {
                                DB::table('antecedentes_natales_neuro')
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
                            }else{
                                DB::table('antecedentes_natales_neuro')
                                    ->updateOrInsert(
                                        [
                                            'id_historia' => $idHistoria,
                                            'tipo' => $tipo
                                        ],
                                        [
                                            'detalle' => '',
                                            'nombre' => $nombre
                                        ]
                                    );
                            }
                        }
                         

                        // Insertar antecedentes posnatales
                        DB::table('antecedentes_posnatales_neuro')->where('id_historia', $idHistoria)->delete();

                        $tiposAntecedentesPosnatales = [
                            'hospitalizaciones_postnatales' => 'Hospitalizaciones recién nacido',
                            'desarrollo_psicomotor' => 'Desarrollo psicomotor'
                        ];

                        foreach ($tiposAntecedentesPosnatales as $tipo => $nombre) {
                            if (array_key_exists($tipo, $request)) {
                                DB::table('antecedentes_posnatales_neuro')
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
                            }else{
                                DB::table('antecedentes_posnatales_neuro')
                                    ->updateOrInsert(
                                        [
                                            'id_historia' => $idHistoria,
                                            'tipo' => $tipo
                                        ],
                                        [
                                            'detalle' => '',
                                            'nombre' => $nombre
                                        ]
                                    );
                            }
                        }

                        // Insertar desarrollo psicomotor
                        DB::table('desarrollo_psicomotor_neuro')->where('id_historia', $idHistoria)->delete();

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

                        foreach ($tiposDesarrolloPsicomotor as $tipo => $nombre) {
                            if (array_key_exists($tipo, $request)) {
                                DB::table('desarrollo_psicomotor_neuro')
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
                            }else{
                                DB::table('desarrollo_psicomotor_neuro')
                                    ->updateOrInsert(
                                        [
                                            'id_historia' => $idHistoria,
                                            'tipo' => $tipo
                                        ],
                                        [
                                            'detalle' => '',
                                            'nombre' => $nombre
                                        ]
                                    );
                            }
                        }
                        
                    }

                    //eliinar y actualizar ordenes medicas
                    DB::connection('mysql')->table('orden_medica_neuro')->where('id_tipo', $idHistoria)->delete();

                    if (isset($request['codigoOrdenMedica']) && is_array($request['codigoOrdenMedica'])) {
                        foreach ($request['codigoOrdenMedica'] as $key => $codigo) {                       
                            DB::connection('mysql')->table('orden_medica_neuro')->insert([
                                'tipo' => 'HISTORIA',
                                'id_tipo' => $idHistoria,
                                'codigo_cups' => $codigo,
                                'cantidad' => $request['cantidadOrdenMedica'][$key] ?? null,
                                'observacion' => $request['observacionOrdenMedica'][$key] ?? null,
                            ]);
                        }
                    }

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
                    $idConsulta = DB::table('consultas_psicologica_neuro')->insertGetId(array_filter([
                        'id_historia' => $request['idHist'] ?? null,
                        'id_profesional' => $request['profesionalConsulta'] ?? null,
                        'fecha_consulta' => $request['fechaEvolucion'] . ' ' . $request['horaSeleccionada'] ?? null,
                        'hora_fin' => $request['horaFin'] ?? null,
                        'evolucion_y_o_plantrabajo' => $request['evolucion_plan']  ?? null,
                        'estado' => 'ACTIVO',
                        'modalidad_grupo_servicio' => $request['modalidadGrupoServicio'] ?? null,
                        'grupo_servicios' => $request['grupoServicios'] ?? '01',
                        'cod_servicio' => $request['codConsultaConsulta'] ?? null,
                        'finalidad_tecnologia_salud' => $request['finalidadTecnologiaSalud'] ?? null,
                        'causa_motivo_atencion' => $request['causaMotivoAtencion'] ?? null,
                        'tipo_diagnostico_principal' => $request['tipoDiagnosticoPrincipal'] ?? null,
                        'impresion_diagnostica' => $request['codImpresionDiagnosticoConsulta'] ?? null,
                        'dx_relacionado1' => $request['dxRelacionado1'] ?? null,
                        'dx_relacionado2' => $request['dxRelacionado2'] ?? null,
                        'dx_relacionado3' => $request['dxRelacionado3'] ?? null,
                        'concepto_recaudo' => $request['conceptoRecaudo'] ?? null,
                    ]));

                 

                    $Paciente = DB::table('historia_clinica_neuro')
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
                        ->where('servicios.tipo_servicio', 'NEUROPSICOLOGÍA')
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
                            ->where('id_paciente', $Paciente->id_paciente)
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

                    // Registrar en servicios_prestados para consolidación RIPS
                    $cups = DB::table('referencia_cups')->where('id', $request['codConsultaConsulta'] ?? null)->select('codigo', 'nombre')->first();
                    $profesionalNeuro = DB::table('profesionales')->where('id', $request['profesionalConsulta'] ?? null)->select('identificacion', 'tipo_identificacion', 'nombre')->first();
                    $dxPrincipalNeuro = DB::table('referencia_cie10')->where('id', $request['codImpresionDiagnosticoConsulta'] ?? null)->value('codigo');
                    $dx1Neuro = DB::table('referencia_cie10')->where('id', $request['dxRelacionado1'] ?? null)->value('codigo');
                    $dx2Neuro = DB::table('referencia_cie10')->where('id', $request['dxRelacionado2'] ?? null)->value('codigo');
                    $dx3Neuro = DB::table('referencia_cie10')->where('id', $request['dxRelacionado3'] ?? null)->value('codigo');

                    DB::table('servicios_prestados')->insert([
                        'paciente_id'              => $Paciente->id_paciente,
                        'historia_clinica_id'      => $request['idHist'],
                        'consulta_id'              => $idConsulta,
                        'profesional_id'           => $request['profesionalConsulta'] ?? null,
                        'codigo_cups'              => $cups->codigo ?? null,
                        'codigo_servicio_habilitado' => $request['codServicio'] ?? null,
                        'nombre_servicio'          => $cups->nombre ?? null,
                        'fecha_servicio'           => $request['fechaEvolucion'],
                        'hora_inicio'              => $request['horaSeleccionada'] ?? null,
                        'hora_fin'                 => $request['horaFin'] ?? null,
                        'modalidad_atencion'       => $request['modalidadGrupoServicio'] ?? null,
                        'grupo_servicios'          => $request['grupoServicios'] ?? '01',
                        'finalidad'                => $request['finalidadTecnologiaSalud'] ?? null,
                        'causa_externa'            => $request['causaMotivoAtencion'] ?? null,
                        'diagnostico_principal'    => $dxPrincipalNeuro,
                        'diagnostico_relacionado1' => $dx1Neuro,
                        'diagnostico_relacionado2' => $dx2Neuro,
                        'diagnostico_relacionado3' => $dx3Neuro,
                        'tipo_diagnostico_principal' => $request['tipoDiagnosticoPrincipal'] ?? null,
                        'profesional_tipo_doc'     => $profesionalNeuro->tipo_identificacion ?? 'CC',
                        'profesional_num_doc'      => $profesionalNeuro->identificacion ?? null,
                        'profesional_nombres'      => $profesionalNeuro->nombre ?? null,
                        'valor_servicio'           => 0,
                        'valor_copago'             => 0,
                        'valor_pagado_paciente'    => 0,
                        'concepto_recaudo'         => $request['conceptoRecaudo'] ?? null,
                        'estado'                   => 'atendido',
                        'tipo'                     => 'NEUROPSICOLOGIA',
                    ]);

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
                    DB::table('consultas_psicologica_neuro')->where('id', $idConsulta)->update(array_filter([
                        'fecha_consulta' => $request['fechaEvolucion'] . ' ' . $request['horaSeleccionada'] ?? null,
                        'hora_fin' => $request['horaFin'] ?? null,
                        'evolucion_y_o_plantrabajo' => $request['evolucion_plan']  ?? null,
                        'id_profesional' => $request['profesionalConsulta'] ?? null,
                        'modalidad_grupo_servicio' => $request['modalidadGrupoServicio'] ?? null,
                        'grupo_servicios' => $request['grupoServicios'] ?? '01',
                        'cod_servicio' => $request['codConsultaConsulta'] ?? null,
                        'finalidad_tecnologia_salud' => $request['finalidadTecnologiaSalud'] ?? null,
                        'causa_motivo_atencion' => $request['causaMotivoAtencion'] ?? null,
                        'tipo_diagnostico_principal' => $request['tipoDiagnosticoPrincipal'] ?? null,
                        'impresion_diagnostica' => $request['codImpresionDiagnosticoConsulta'] ?? null,
                        'dx_relacionado1' => $request['dxRelacionado1'] ?? null,
                        'dx_relacionado2' => $request['dxRelacionado2'] ?? null,
                        'dx_relacionado3' => $request['dxRelacionado3'] ?? null,
                        'concepto_recaudo' => $request['conceptoRecaudo'] ?? null,
                    ]));

                    // Actualizar registro en servicios_prestados
                    $cupsU = DB::table('referencia_cups')->where('id', $request['codConsultaConsulta'] ?? null)->select('codigo', 'nombre')->first();
                    $profU = DB::table('profesionales')->where('id', $request['profesionalConsulta'] ?? null)->select('identificacion', 'tipo_identificacion', 'nombre')->first();
                    $dxPU = DB::table('referencia_cie10')->where('id', $request['codImpresionDiagnosticoConsulta'] ?? null)->value('codigo');
                    $dx1U = DB::table('referencia_cie10')->where('id', $request['dxRelacionado1'] ?? null)->value('codigo');
                    $dx2U = DB::table('referencia_cie10')->where('id', $request['dxRelacionado2'] ?? null)->value('codigo');
                    $dx3U = DB::table('referencia_cie10')->where('id', $request['dxRelacionado3'] ?? null)->value('codigo');

                    DB::table('servicios_prestados')->updateOrInsert(
                        ['consulta_id' => $idConsulta, 'tipo' => 'NEUROPSICOLOGIA'],
                        [
                            'profesional_id'           => $request['profesionalConsulta'] ?? null,
                            'codigo_cups'              => $cupsU->codigo ?? null,
                            'codigo_servicio_habilitado' => $request['codServicio'] ?? null,
                            'nombre_servicio'          => $cupsU->nombre ?? null,
                            'fecha_servicio'           => $request['fechaEvolucion'],
                            'hora_inicio'              => $request['horaSeleccionada'] ?? null,
                            'hora_fin'                 => $request['horaFin'] ?? null,
                            'modalidad_atencion'       => $request['modalidadGrupoServicio'] ?? null,
                            'grupo_servicios'          => $request['grupoServicios'] ?? '01',
                            'finalidad'                => $request['finalidadTecnologiaSalud'] ?? null,
                            'causa_externa'            => $request['causaMotivoAtencion'] ?? null,
                            'diagnostico_principal'    => $dxPU,
                            'diagnostico_relacionado1' => $dx1U,
                            'diagnostico_relacionado2' => $dx2U,
                            'diagnostico_relacionado3' => $dx3U,
                            'tipo_diagnostico_principal' => $request['tipoDiagnosticoPrincipal'] ?? null,
                            'profesional_tipo_doc'     => $profU->tipo_identificacion ?? 'CC',
                            'profesional_num_doc'      => $profU->identificacion ?? null,
                            'profesional_nombres'      => $profU->nombre ?? null,
                            'concepto_recaudo'         => $request['conceptoRecaudo'] ?? null,
                        ]
                    );

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

    public static function busquedaConsulta($idConsulta)
    {
        return DB::connection('mysql')->table('consultas_psicologica_neuro')
            ->where("id", $idConsulta)
            ->first();
    }

    public static function historialConsultas($idHisto)
    {
        return DB::connection('mysql')->table('consultas_psicologica_neuro')
            ->leftJoin("profesionales", "profesionales.id", "consultas_psicologica_neuro.id_profesional")
            ->where("consultas_psicologica_neuro.id_historia", $idHisto)
            ->orderBy('consultas_psicologica_neuro.fecha_consulta', 'desc')
            ->where("consultas_psicologica_neuro.estado", "ACTIVO")
            ->take(5)
            ->select(
                'consultas_psicologica_neuro.id',
                'consultas_psicologica_neuro.fecha_consulta',
                'profesionales.nombre AS profesional'
            )
            ->get();
    }

    public static function busquedaHistoriaNeuro($idHisto)
    {
        $historia = DB::connection('mysql')->table('historia_clinica_neuro')
            ->where("id", $idHisto)
            ->first();
           
       
            if ($historia->dx_principal != null) {
                $historia->dx_principal_detalle = DB::connection('mysql')
                    ->table('referencia_cie10')
                    ->where('id', $historia->dx_principal)
                    ->first() ?? (object) [];
            }
    
            if ($historia->dx_principal1 != null) {
                $historia->dx_principal1_detalle = DB::connection('mysql')
                    ->table('referencia_cie10')
                    ->where('id', $historia->dx_principal1)
                    ->first() ?? (object) [];
            }
    
            if ($historia->dx_principal2 != null) {
                $historia->dx_principal2_detalle = DB::connection('mysql')
                    ->table('referencia_cie10')
                    ->where('id', $historia->dx_principal2)
                    ->first() ?? (object) [];
            }
    
            if ($historia->codigo_consulta != null) {
                $historia->codigo_consulta_detalle = DB::connection('mysql')
                    ->table('referencia_cups')
                    ->where('id', $historia->codigo_consulta)
                    ->first();
            }
    
            if ($historia->otro_motivo_consulta != null) {
                $historia->motivo_consulta_detalle = DB::connection('mysql')
                    ->table('opciones_hc_psicologia')
                    ->where('id', $historia->otro_motivo_consulta)
                    ->first();
            }
    
            if ($historia->codigo_diagnostico != null) {
                $historia->impresion_diagnostica_detalle = DB::connection('mysql')
                    ->table('referencia_cie10')
                    ->where('id', $historia->codigo_diagnostico)
                    ->first() ?? (object) [];
            }
    
            if ($historia->codigo_diagnostico1 != null) {
                $historia->codigo_diagnostico1_detalle = DB::connection('mysql')
                    ->table('referencia_cie10')
                    ->where('id', $historia->codigo_diagnostico1)
                    ->first() ?? (object) [];
            }
    
            if ($historia->codigo_diagnostico2 != null) {
                $historia->codigo_diagnostico2_detalle = DB::connection('mysql')
                    ->table('referencia_cie10')
                    ->where('id', $historia->codigo_diagnostico2)
                    ->first() ?? (object) [];
            }
    
            if ($historia->plan_intervension != null) {
                $historia->plan_intervension_detalle = DB::connection('mysql')
                    ->table('opciones_hc_psicologia')
                    ->where('id', $historia->plan_intervencion)
                    ->first();
            }
    
            if ($historia->id_profesional != null) {
                $historia->profesional_detalle = DB::connection('mysql')->table('profesionales')
                    ->join("users", "users.id", "profesionales.usuario")
                    ->where("profesionales.id", $historia->id_profesional)
                    ->select("profesionales.*", "users.login_usuario", "users.estado_usuario", "users.id as idUsuario")
                    ->first();
            }
        

        return $historia;
    }

    public static function busquedaHistoriaNeuroPaciente($idPac)
    {
        return DB::connection('mysql')->table('historia_clinica_neuro')
            ->where("id_paciente", $idPac)
            ->where("estado_registro", "ACTIVO")
            ->first();
    }

    public static function busquedaHistoriaPaciente($idPac)
    {
        return DB::connection('mysql')->table('historia_clinica_neuro')
            ->where("id_paciente", $idPac)
            ->where("estado_registro", "ACTIVO")
            ->exists();
    }

    public static function busquedaAntecedentes($idHisto)
    {
        return DB::connection('mysql')->table('antecedentes_medicos_neuro')
            ->where("id_historia", $idHisto)
            ->get();
    }

    public static function busquedaAntFamiliares($idHisto)
    {
        return DB::connection('mysql')->table('antecedentes_familiares_neuro')
            ->where("id_historia", $idHisto)
            ->get();
    }

    public static function busquedaAreaAjuste($idHisto)
    {
        return DB::connection('mysql')->table('historia_ajuste_desempeno_neuro')
            ->where("id_historia", $idHisto)
            ->get();
    }

    public static function busquedaInterconsulta($idHisto)
    {
        return DB::connection('mysql')->table('interconsultas_neuro')
            ->where("id_historia", $idHisto)
            ->get();
    }

    public static function busquedaAparienciaPersonal($idHisto)
    {
        $apariencias = DB::connection('mysql')->table('apariencia_personal_neuro')
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
        $funciones = DB::connection('mysql')->table('funciones_cognitivas_neuro')
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
        return DB::connection('mysql')->table('funciones_somaticas_neuro')
            ->where("id_historia", $idHisto)
            ->first();
    }

    public static function busquedaAntPrenatales($idHisto)
    {
        return DB::connection('mysql')->table('antecedentes_prenatales_neuro')
            ->where("id_historia", $idHisto)
            ->get();
    }

    public static function busquedaAntNatales($idHisto)
    {
        return DB::connection('mysql')->table('antecedentes_natales_neuro')
            ->where("id_historia", $idHisto)
            ->get();
    }

    public static function busquedaAntPosnatales($idHisto)
    {
        return DB::connection('mysql')->table('antecedentes_posnatales_neuro')
            ->where("id_historia", $idHisto)
            ->get();
    }

    public static function desarrolloPsicomotor($idHisto)
    {
        return DB::connection('mysql')->table('desarrollo_psicomotor_neuro')
            ->where("id_historia", $idHisto)
            ->get();
    }

    public static function busquedaInforme($idInf)
    {
        $informe = DB::connection('mysql')->table('informe_evolucion_neuropsicologia')
            ->where("id", $idInf)
            ->first();

            if($informe->impresion_diagnostica_princippal != null){
                $informe->impresion_diagnostica_princippal_detalle = DB::connection('mysql')->table('referencia_cie10')
                    ->where('id', $informe->impresion_diagnostica_princippal)
                    ->first();
            }

            if($informe->impresion_diagnostica_relacionada_1 != null){
                $informe->impresion_diagnostica_relacionada_1_detalle = DB::connection('mysql')->table('referencia_cie10')
                    ->where('id', $informe->impresion_diagnostica_relacionada_1)
                    ->first();
            }

            if($informe->impresion_diagnostica_relacionada_2 != null){
                $informe->impresion_diagnostica_relacionada_2_detalle = DB::connection('mysql')->table('referencia_cie10')
                    ->where('id', $informe->impresion_diagnostica_relacionada_2)
                    ->first();
            }
            
            

        return $informe;
    }

    public static function guardarInforme($request)
    {
        try {
            $idInforme = $request['idInforme'];
            if ($request['accInforme'] == 'guardar') {
                DB::beginTransaction();
                try {

                    // Insertar en `informe_evolucion`
                    $idInforme = DB::table('informe_evolucion_neuropsicologia')->insertGetId(array_filter([
                        'id_paciente' => $request['idPaciente'] ?? null,
                        'id_profesional' => $request['profesionalInforme'] ?? null,
                        'fecha_creacion' => $request['fechaEvolucion'] . ' ' . $request['horaSeleccionada'],
                        'estado' => 'ACTIVO',
                        'motivo_consulta' => $request['motivoConsulta'] ?? null,
                        'estado_actual' => $request['estadoActual'] ?? null,
                        'historia_personal' => $request['historiaPersonal'] ?? null,
                        'desarrollo_psicomotor' => $request['desarrolloPsicomotor'] ?? null,
                        'desarrollo_lenguaje' => $request['desarrolloLenguaje'] ?? null,
                        'abc' => $request['abc'] ?? null,
                        'antecedentes_medicos_familiares' => $request['antecedentesMedicosFamiliares'] ?? null,
                        'antecedentes_personales' => $request['antecedentesPersonales'] ?? null,
                        'historia_desarrollo' => $request['historiaDesarrollo'] ?? null,
                        'historia_escolar' => $request['historiaEscolar'] ?? null,
                        'historia_socio_afectiva' => $request['historiaSocioAfectiva'] ?? null,
                        'condicion_paciente' => $request['condicionPaciente'] ?? null,
                        'resultados_evaluacion' => $request['resultadosEvaluacion'] ?? null,
                        'impresion_diagnostica' => $request['impresionDiagnostica'] ?? null,
                        'impresion_diagnostica_princippal' => $request['codImpresionDiagnostico'] ?? null,
                        'impresion_diagnostica_relacionada_1' => $request['codImpresionDiagnosticoRelacionado1'] ?? null,
                        'impresion_diagnostica_relacionada_2' => $request['codImpresionDiagnosticoRelacionado2'] ?? null
                    ]));

                    if (isset($request['archivo']) && is_array($request['archivo'])) {
                        foreach ($request['archivo'] as $key => $archivo) {
                            DB::connection('mysql')->table('anexos_informe_neuropsicologia')->insert([
                                'id_informe' => $idInforme,
                                'url' => $archivo,
                                'tipo_archivo' => $request['tipoArc'][$key] ?? null,
                                'nombre_archivo' => $request['nombre'][$key] ?? null,
                                'peso' => $request['peso'][$key] ?? null
                            ]);
                        }
                    }

                    //insertar ordenes medicas
                    if (isset($request['codigoOrdenMedica']) && is_array($request['codigoOrdenMedica'])) {
                        foreach ($request['codigoOrdenMedica'] as $key => $codigo) {
                            DB::connection('mysql')->table('orden_medica_neuro')->insert([
                                'tipo' => 'INFORME',
                                'id_tipo' => $idInforme,
                                'codigo_cups' => $codigo,
                                'cantidad' => $request['cantidadOrdenMedica'][$key] ?? null,
                                'observacion' => $request['observacionOrdenMedica'][$key] ?? null,
                            ]);
                        }
                    }

                    

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

                    $camposMapping = [
                        'profesionalInforme' => 'id_profesional',
                        'fechaEvolucion' => 'fecha_creacion',
                        'motivoConsulta' => 'motivo_consulta',
                        'estadoActual' => 'estado_actual',
                        'historiaPersonal' => 'historia_personal',
                        'desarrolloPsicomotor' => 'desarrollo_psicomotor',
                        'desarrolloLenguaje' => 'desarrollo_lenguaje',
                        'abc' => 'abc',
                        'antecedentesMedicosFamiliares' => 'antecedentes_medicos_familiares',
                        'antecedentesPersonales' => 'antecedentes_personales',
                        'historiaDesarrollo' => 'historia_desarrollo',
                        'historiaEscolar' => 'historia_escolar',
                        'historiaSocioAfectiva' => 'historia_socio_afectiva',
                        'condicionPaciente' => 'condicion_paciente',
                        'resultadosEvaluacion' => 'resultados_evaluacion',
                        'impresionDiagnostica' => 'impresion_diagnostica',
                        'codImpresionDiagnostico' => 'impresion_diagnostica_princippal',
                        'codImpresionDiagnosticoRelacionado1' => 'impresion_diagnostica_relacionada_1',
                        'codImpresionDiagnosticoRelacionado2' => 'impresion_diagnostica_relacionada_2'
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

                  

                    if (!empty($datosInsertar)) {
                        $Informe = DB::table('informe_evolucion_neuropsicologia')->where('id', $idInforme)->update($datosInsertar);
                    }

                    if (isset($request['archivo']) && is_array($request['archivo'])) {
                        foreach ($request['archivo'] as $key => $archivo) {
                            DB::connection('mysql')->table('anexos_informe_neuropsicologia')->insert([
                                'id_informe' => $idInforme,
                                'url' => $archivo,
                                'tipo_archivo' => $request['tipoArc'][$key] ?? null,
                                'nombre_archivo' => $request['nombre'][$key] ?? null,
                                'peso' => $request['peso'][$key] ?? null
                            ]);
                        }
                    }

                    //eliinar y actualizar ordenes medicas

                    DB::connection('mysql')->table('orden_medica_neuro')->where('id_tipo', $idInforme)->delete();
                   
                    if (isset($request['codigoOrdenMedica'])) {
                        foreach ($request['codigoOrdenMedica'] as $key => $codigo) {
                       
                            DB::connection('mysql')->table('orden_medica_neuro')->insert([
                                'tipo' => 'INFORME',
                                'id_tipo' => $idInforme,
                                'codigo_cups' => $codigo,
                                'cantidad' => $request['cantidadOrdenMedica'][$key] ?? null,
                                'observacion' => $request['observacionOrdenMedica'][$key] ?? null,
                            ]);
                        }
                    }

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

    public static function BuscarHistoria($idHistoria)
    {
        return DB::connection('mysql')->table('historia_clinica_neuro')
            ->leftJoin("pacientes", "pacientes.id", "historia_clinica_neuro.id_paciente")
            ->where("historia_clinica_neuro.id", $idHistoria)
            ->select("pacientes.primer_nombre", "pacientes.segundo_nombre", "pacientes.primer_apellido", "pacientes.segundo_apellido")
            ->first();
    }
}
