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

    private static function limpiarValores($valor)
    {
        return !is_null($valor) && $valor !== '';
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
            ->where('servicios.tipo_historia', 'NEUROPSICOLOGIA')
            ->first();
        if ($venta) {
            if ($venta->estadoVentaConsulta == "PENDIENTE") {
                $venta->estadoVentaConsulta = "COMPRADA";
            }
        }

        return $venta;
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
                    $idHistoria = DB::table('historia_clinica_neuro')->insertGetId(array_filter([
                        'id_paciente' => $request['idPaciente'] ?? null,
                        'id_profesional' => $request['idProfesional'] ?? null,
                        'remision' => $request['remision'] ?? null,
                        'codigo_consulta' => $request['codConsulta'] ?? null,
                        'motivo_consulta' => $request['motivoConsulta'] ?? null,
                        'motivo_consulta_texto' => $request['motivoConsultaTexto'] ?? null,
                        'otro_motivo_consulta' => $request['otroMotivo'] ?? null,
                        'enfermedad_actual' => $request['enfermedadActual'] ?? null,
                        'dx_principal' => $request['codDiagnostico'] ?? null,
                        'codigo_diagnostico' => $request['codImpresionDiagnostico'] ?? null,
                        'diagnostico_primera_vez' => $request['establecidoPrimeraVez'] ?? null,
                        'objetivo_general' => $request['objetivo_general'] ?? null,
                        'objetivos_especificos' => $request['objetivos_especificos'] ?? null,
                        'sugerencias_interconsultas' => $request['sugerencia_interconsultas'] ?? null,
                        'observaciones_recomendaciones' => $request['observaciones_recomendaciones'] ?? null,
                        'tipologia' => $request['tipoPsicologia'] ?? null,
                        'plan_intervension' => $request['planIntervencion'] ?? null,
                        'fecha_historia' => now(),
                        'estado_hitoria' => 'cerrada',
                        'estado_registro' => 'ACTIVO',
                    ]));

                    // insertar datos de consulta 

                 

                    // Insertar antecedentes médicos
                    $antecedentesMedicos = array_filter([
                        ['id_historia' => $idHistoria, 'tipo' => 'quirurgicos', 'detalle' => $request['quirurgicos'], 'nombre' => 'Quirúrgico'],
                        ['id_historia' => $idHistoria, 'tipo' => 'toxicos', 'detalle' => $request['toxico'], 'nombre' => 'Tóxicos'],
                        ['id_historia' => $idHistoria, 'tipo' => 'traumaticos', 'detalle' => $request['traumaticos'], 'nombre' => 'Traumáticos'],
                        ['id_historia' => $idHistoria, 'tipo' => 'medicacion', 'detalle' => $request['medicacion'], 'nombre' => 'Medicación'],
                        ['id_historia' => $idHistoria, 'tipo' => 'paraclinicos', 'detalle' => $request['paraclinicos'], 'nombre' => 'Paraclínicos'],
                        ['id_historia' => $idHistoria, 'tipo' => 'hospitalizaciones', 'detalle' => $request['hospitalizaciones'], 'nombre' => 'Hospitalizaciones'],
                        ['id_historia' => $idHistoria, 'tipo' => 'patologia', 'detalle' => $request['patologia'], 'nombre' => 'Patología']
                    ], function ($item) {
                        return !empty($item['detalle']);
                    });
                    DB::table('antecedentes_medicos_neuro')->insert($antecedentesMedicos);


                    // Insertar antecedentes familiares
                    $antecedentesFamiliares = array_filter([
                        ['id_historia' => $idHistoria, 'tipo' => 'depresion', 'detalle' => implode(',', $request['depresion']) ?? null, 'nombre' => 'Depresión'],
                        ['id_historia' => $idHistoria, 'tipo' => 'ansiedad', 'detalle' => implode(',', $request['ansiedad']) ?? null, 'nombre' => 'Ansiedad'],
                        ['id_historia' => $idHistoria, 'tipo' => 'demencia', 'detalle' => implode(',', $request['demencia']) ?? null, 'nombre' => 'Demencia'],
                        ['id_historia' => $idHistoria, 'tipo' => 'alcoholismo', 'detalle' => implode(',', $request['alcoholismo']) ?? null, 'nombre' => 'Alcoholismo'],
                        ['id_historia' => $idHistoria, 'tipo' => 'drogadiccion', 'detalle' => implode(',', $request['drogadiccion']) ?? null, 'nombre' => 'Drogadicción'],
                        ['id_historia' => $idHistoria, 'tipo' => 'discapacidad_intelectual', 'detalle' => implode(',', $request['discapacidad_intelectual']) ?? null, 'nombre' => 'Discapacidad intelectual'],
                        ['id_historia' => $idHistoria, 'tipo' => 'patologicos', 'detalle' => $request['patologicos'] ?? null, 'nombre' => 'Patológicos'],
                        ['id_historia' => $idHistoria, 'tipo' => 'otros', 'detalle' => $request['otros'] ?? null, 'nombre' => 'Otros'],
                    ], function ($item) {
                        return !empty($item['detalle']);
                    });
                    DB::table('antecedentes_familiares_neuro')->insert($antecedentesFamiliares);


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
                    DB::table('historia_ajuste_desempeno_neuro')->insert($ajusteDesempeno);


                    // Insertar interconsultas
                    $interconsultas = array_filter([
                        ['id_historia' => $idHistoria, 'tipo' => 'intervencion_psiquiatria', 'detalle' => $request['intervencion_psiquiatria'], 'nombre' => 'Intervención psiquiátrica'],
                        ['id_historia' => $idHistoria, 'tipo' => 'intervencion_neurologia', 'detalle' => $request['intervencion_neurologia'], 'nombre' => 'Intervención neurológica'],
                        ['id_historia' => $idHistoria, 'tipo' => 'intervencion_neuropsicologia', 'detalle' => $request['intervencion_neuropsicologia'], 'nombre' => 'Intervención neuropsicológica']
                    ], function ($item) {
                        return !empty($item['detalle']);
                    });
                    DB::table('interconsultas_neuro')->insert($interconsultas);

                    // Insertar apariencia personal
                    $aparienciaPersonal = array_filter([
                        ['id_historia' => $idHistoria, 'caracteristica' => 'edad', 'detalle' => implode(',' , $request['edad']) ?? null, 'nombre' => 'Edad'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'edad_otro', 'detalle' => $request['edad_otro'] ?? null, 'nombre' => 'Edad (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'desarrollo', 'detalle' => implode(',' , $request['desarrollo']) ?? null, 'nombre' => 'Desarrollo pondoestatural'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'desarrollo_otro', 'detalle' => $request['desarrollo_otro'] ?? null, 'nombre' => 'Desarrollo pondoestatural (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'aseo', 'detalle' => implode(',' , $request['aseo']) ?? null, 'nombre' => 'Aseo y Arreglo'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'aseo_otro', 'detalle' => $request['aseo_otro'] ?? null, 'nombre' => 'Aseo y arreglo (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'salud', 'detalle' => implode(',' , $request['salud']) ?? null, 'nombre' => 'Salud somática'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'salud_otro', 'detalle' => $request['salud_otro'] ?? null, 'nombre' => 'Salud somática (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'facies', 'detalle' => implode(',' , $request['facies']) ?? null, 'nombre' => 'Facies'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'facies_otro', 'detalle' => $request['facies_otro'] ?? null, 'nombre' => 'Facies (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'biotipo', 'detalle' => implode(',' , $request['biotipo']) ?? null, 'nombre' => 'Biotipo'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'biotipo_otro', 'detalle' => $request['biotipo_otro'] ?? null, 'nombre' => 'Biotipo (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'actitud', 'detalle' => implode(',' , $request['actitud']) ?? null, 'nombre' => 'Actitud'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'actitud_otro', 'detalle' => $request['actitud_otro'] ?? null, 'nombre' => 'Actitud (otro)']
                    ], function ($item) {
                        return !empty($item['detalle']);
                    });

                    // Inserta los datos filtrados
                    DB::table('apariencia_personal_neuro')->insert($aparienciaPersonal);

                    // Insertar funciones cognitivas
                    $funcionesSomaticas = array_filter([
                        ['id_historia' => $idHistoria, 'caracteristica' => 'consciencia', 'detalle' => implode(',' , $request['consciencia']) ?? null, 'nombre' => 'Consciencia'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'consciencia_otro', 'detalle' => $request['consciencia_otro'] ?? null, 'nombre' => 'Consciencia (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'orientacion', 'detalle' => implode(',' , $request['orientacion']) ?? null, 'nombre' => 'Orientación'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'orientacion_otro', 'detalle' => $request['orientacion_otro'] ?? null, 'nombre' => 'Orientación (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'memoria', 'detalle' => implode(',' , $request['memoria']) ?? null, 'nombre' => 'Memoria'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'memoria_otro', 'detalle' => $request['memoria_otro'] ?? null, 'nombre' => 'Memoria (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'atencion', 'detalle' => implode(',' , $request['atencion']) ?? null, 'nombre' => 'Atención'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'atencion_otro', 'detalle' => $request['atencion_otro'] ?? null, 'nombre' => 'Atención (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'concentracion', 'detalle' => implode(',' , $request['concentracion']) ?? null, 'nombre' => 'Concentración'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'concentracion_otro', 'detalle' => $request['concentracion_otro'] ?? null, 'nombre' => 'Concentración (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'lenguaje', 'detalle' => implode(',' , $request['lenguaje']) ?? null, 'nombre' => 'Lenguaje'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'lenguaje_otro', 'detalle' => $request['lenguaje_otro'] ?? null, 'nombre' => 'Lenguaje (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'pensamiento', 'detalle' => implode(',' , $request['pensamiento']) ?? null, 'nombre' => 'Pensamiento'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'pensamiento_otro', 'detalle' => $request['pensamiento_otro'] ?? null, 'nombre' => 'Pensamiento (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'afecto', 'detalle' => implode(',' , $request['afecto']) ?? null, 'nombre' => 'Afecto'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'afecto_otro', 'detalle' => $request['afecto_otro'] ?? null, 'nombre' => 'Afecto (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'sensopercepcion', 'detalle' => implode(',' , $request['sensopercepcion']) ?? null, 'nombre' => 'Sensopercepción'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'sensopercepcion_otro', 'detalle' => $request['sensopercepcion_otro'] ?? null, 'nombre' => 'Sensopercepción (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'psicomotricidad', 'detalle' => implode(',' , $request['psicomotricidad']) ?? null, 'nombre' => 'Psicomotricidad'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'psicomotricidad_otro', 'detalle' => $request['psicomotricidad_otro'] ?? null, 'nombre' => 'Psicomotricidad (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'juicio', 'detalle' => implode(',' , $request['juicio']) ?? null, 'nombre' => 'Juicio'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'juicio_otro', 'detalle' => $request['juicio_otro'] ?? null, 'nombre' => 'Juicio (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'inteligencia', 'detalle' => implode(',' , $request['inteligencia']) ?? null, 'nombre' => 'Inteligencia'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'inteligencia_otro', 'detalle' => $request['inteligencia_otro'] ?? null, 'nombre' => 'Inteligencia (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'conciencia_enfermedad', 'detalle' => implode(',' , $request['conciencia_enfermedad']) ?? null, 'nombre' => 'Conciencia de enfermedad'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'conciencia_enfermedad_otro', 'detalle' => $request['conciencia_enfermedad_otro'] ?? null, 'nombre' => 'Conciencia de enfermedad (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'sufrimiento_psicologico', 'detalle' => implode(',' , $request['sufrimiento_psicologico']) ?? null, 'nombre' => 'Sufrimiento psicológico'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'sufrimiento_psicologico_otro', 'detalle' => $request['sufrimiento_psicologico_otro'] ?? null, 'nombre' => 'Sufrimiento psicológico (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'motivacion_tratamiento', 'detalle' => implode(',' , $request['motivacion_tratamiento']) ?? null, 'nombre' => 'Motivación al tratamiento'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'motivacion_tratamiento_otro', 'detalle' => $request['motivacion_tratamiento_otro'] ?? null, 'nombre' => 'Motivación al tratamiento (otro)']
                    ], function ($item) {
                        return !empty($item['detalle']);
                    });

                    // Inserta los datos filtrados
                    DB::table('funciones_cognitivas_neuro')->insert($funcionesSomaticas);

                    // Insertar Funciones Somáticas
                    $examenMental = array_filter([
                        'id_historia' => $idHistoria,
                        'ciclos_del_sueno' => $request['ciclos_sueno'],
                        'apetito' => $request['apetito'],
                        'actividades_autocuidado' => $request['autocuidado'],
                    ]);
                    DB::table('funciones_somaticas_neuro')->insert($examenMental);


                    /// En el caso de que sea pediatria
                    if ($request['tipoPsicologia'] == "Pediatría") {
                        // Insertar antecedentes prenatales
                        $antecedentesPrenatales = array_filter([
                            ['id_historia' => $idHistoria, 'tipo' => 'edad_madre', 'detalle' => $request['edad_madre'], 'nombre' => 'Edad de la madre en el embarazo'],
                            ['id_historia' => $idHistoria, 'tipo' => 'enfermedades_madre', 'detalle' => $request['enfermedades_madre'], 'nombre' => 'Enfermedades de la madre'],
                            ['id_historia' => $idHistoria, 'tipo' => 'numero_embarazo', 'detalle' => $request['numero_embarazo'], 'nombre' => 'Único embarazo'],
                            ['id_historia' => $idHistoria, 'tipo' => 'enbarazo_controlado', 'detalle' => $request['enbarazo_controlado'], 'nombre' => 'El embarazo fue controlado por atención médica'],
                            ['id_historia' => $idHistoria, 'tipo' => 'planificacion', 'detalle' => $request['planificacion'], 'nombre' => 'Uso de planificación en el momento del embarazo'],
                            ['id_historia' => $idHistoria, 'tipo' => 'estado_madre', 'detalle' => $request['estado_madre'], 'nombre' => 'Estado de la madre durante el embarazo']
                        ], function ($item) {
                            return !empty($item['detalle']);
                        });
                        DB::table('antecedentes_prenatales_neuro')->insert($antecedentesPrenatales);

                        // Insertar antecedentes natales
                        $antecedentesNatales = array_filter([
                            ['id_historia' => $idHistoria, 'tipo' => 'tipo_nacimiento', 'detalle' => $request['tipo_nacimiento'], 'nombre' => 'Tipo de nacimiento'],
                            ['id_historia' => $idHistoria, 'tipo' => 'causa_cesarea', 'detalle' => $request['causa_cesarea'], 'nombre' => 'Causa de la cesárea'],
                            ['id_historia' => $idHistoria, 'tipo' => 'reanimacion', 'detalle' => $request['reanimacion'], 'nombre' => 'Uso de maniobras de reanimación'],
                            ['id_historia' => $idHistoria, 'tipo' => 'peso_nacer', 'detalle' => $request['peso_nacer'], 'nombre' => 'Peso al nacer'],
                            ['id_historia' => $idHistoria, 'tipo' => 'talla_nacer', 'detalle' => $request['talla_nacer'], 'nombre' => 'Talla al nacer'],
                            ['id_historia' => $idHistoria, 'tipo' => 'llanto_nacer', 'detalle' => $request['llanto_nacer'], 'nombre' => 'Llanto al nacer']
                        ], function ($item) {
                            return !empty($item['detalle']);
                        });
                        DB::table('antecedentes_natales_neuro')->insert($antecedentesNatales);

                        // Insertar antecedentes posnatales
                        $antecedentesPosnatales = array_filter([
                            ['id_historia' => $idHistoria, 'tipo' => 'hospitalizaciones_postnatales', 'detalle' => $request['hospitalizaciones'], 'nombre' => 'Hospitalizaciones recién nacido'],
                            ['id_historia' => $idHistoria, 'tipo' => 'desarrollo_psicomotor', 'detalle' => $request['desarrollo_psicomotor'], 'nombre' => 'Desarrollo psicomotor']
                        ], function ($item) {
                            return !empty($item['detalle']);
                        });
                        DB::table('antecedentes_posnatales_neuro')->insert($antecedentesPosnatales);

                        // Insertar desarrollo psicomotor
                        $desarrolloPsicomotor = array_filter([
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
                        DB::table('desarrollo_psicomotor_neuro')->insert($desarrolloPsicomotor);
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
                    $idHistoria = $request['idHistoria'];
                    // Insertar en `historia_clinica`
                    DB::table('historia_clinica_neuro')->where('id', $idHistoria)->update(array_filter([
                        'id_paciente' => $request['idPaciente'] ?? null,
                        'id_profesional' => $request['idProfesional'] ?? null,
                        'remision' => $request['remision'] ?? null,
                        'codigo_consulta' => $request['codConsulta'] ?? null,
                        'motivo_consulta' => $request['motivoConsulta'] ?? null,
                        'motivo_consulta_texto' => $request['motivoConsultaTexto'] ?? null,
                        'otro_motivo_consulta' => $request['otroMotivo'] ?? null,
                        'enfermedad_actual' => $request['enfermedadActual'] ?? null,
                        'dx_principal' => $request['codDiagnostico'] ?? null,
                        'codigo_diagnostico' => $request['codImpresionDiagnostico'] ?? null,
                        'diagnostico_primera_vez' => $request['establecidoPrimeraVez'] ?? null,
                        'objetivo_general' => $request['objetivo_general'] ?? null,
                        'objetivos_especificos' => $request['objetivos_especificos'] ?? null,
                        'sugerencias_interconsultas' => $request['sugerencia_interconsultas'] ?? null,
                        'observaciones_recomendaciones' => $request['observaciones_recomendaciones'] ?? null,
                        'tipologia' => $request['tipoPsicologia'] ?? null,
                        'plan_intervension' => $request['planIntervencion'] ?? null,
                        'fecha_historia' => now(),
                        'estado_registro' => 'ACTIVO',
                    ]));

              
                    // Insertar antecedentes médicos
                    DB::table('antecedentes_medicos_neuro')->where('id_historia', $idHistoria)->delete();
                    $antecedentesMedicos = array_filter([
                        ['id_historia' => $idHistoria, 'tipo' => 'quirurgicos', 'detalle' => $request['quirurgicos'], 'nombre' => 'Quirúrgicos'],
                        ['id_historia' => $idHistoria, 'tipo' => 'toxicos', 'detalle' => $request['toxico'], 'nombre' => 'Tóxicos'],
                        ['id_historia' => $idHistoria, 'tipo' => 'traumaticos', 'detalle' => $request['traumaticos'], 'nombre' => 'Traumáticos'],
                        ['id_historia' => $idHistoria, 'tipo' => 'medicacion', 'detalle' => $request['medicacion'], 'nombre' => 'Medicación'],
                        ['id_historia' => $idHistoria, 'tipo' => 'paraclinicos', 'detalle' => $request['paraclinicos'], 'nombre' => 'Paraclínicos'],
                        ['id_historia' => $idHistoria, 'tipo' => 'hospitalizaciones', 'detalle' => $request['hospitalizaciones'], 'nombre' => 'Hospitalizaciones'],
                        ['id_historia' => $idHistoria, 'tipo' => 'patologia', 'detalle' => $request['patologia'], 'nombre' => 'Patología']
                    ], function ($item) {
                        return !empty($item['detalle']);
                    });
                    DB::table('antecedentes_medicos_neuro')->insert($antecedentesMedicos);


                    // Insertar antecedentes familiares
                    DB::table('antecedentes_familiares_neuro')->where('id_historia', $idHistoria)->delete();
                    $antecedentesFamiliares = array_filter([
                        ['id_historia' => $idHistoria, 'tipo' => 'depresion', 'detalle' => implode(',', $request['depresion']) ?? null, 'nombre' => 'Depresión'],
                        ['id_historia' => $idHistoria, 'tipo' => 'ansiedad', 'detalle' => implode(',', $request['ansiedad']) ?? null, 'nombre' => 'Ansiedad'],
                        ['id_historia' => $idHistoria, 'tipo' => 'demencia', 'detalle' => implode(',', $request['demencia']) ?? null, 'nombre' => 'Demencia'],
                        ['id_historia' => $idHistoria, 'tipo' => 'alcoholismo', 'detalle' => implode(',', $request['alcoholismo']) ?? null, 'nombre' => 'Alcoholismo'],
                        ['id_historia' => $idHistoria, 'tipo' => 'drogadiccion', 'detalle' => implode(',', $request['drogadiccion']) ?? null, 'nombre' => 'Drogadicción'],
                        ['id_historia' => $idHistoria, 'tipo' => 'discapacidad_intelectual', 'detalle' => implode(',', $request['discapacidad_intelectual']) ?? null, 'nombre' => 'Discapacidad intelectual'],
                        ['id_historia' => $idHistoria, 'tipo' => 'patologicos', 'detalle' => $request['patologicos'] ?? null, 'nombre' => 'Patológicos'],
                        ['id_historia' => $idHistoria, 'tipo' => 'otros', 'detalle' =>   $request['otros'] ?? null, 'nombre' => 'Otros'],
                    ], function ($item) {
                        return !empty($item['detalle']);
                    });
                    DB::table('antecedentes_familiares_neuro')->insert($antecedentesFamiliares);


                    // Insertar áreas de ajuste y/o desempeño
                    DB::table('historia_ajuste_desempeno_neuro')->where('id_historia', $idHistoria)->delete();
                    $ajusteDesempeno = array_filter([
                        ['id_historia' => $idHistoria, 'area' => 'historia_educativa', 'detalle' => $request['historia_educativa'], 'nombre' => 'Historia educativa'],
                        ['id_historia' => $idHistoria, 'area' => 'historia_laboral', 'detalle' => $request['historia_laboral'], 'nombre' => 'Historia laboral'],
                        ['id_historia' => $idHistoria, 'area' => 'historia_familiar', 'detalle' => $request['historia_familiar'], 'nombre' => 'Historia familiar'],
                        ['id_historia' => $idHistoria, 'area' => 'historia_social', 'detalle' => $request['historia_social'], 'nombre' => 'Historia social'],
                        ['id_historia' => $idHistoria, 'area' => 'historia_socio_afectiva', 'detalle' => $request['historia_socio_afectiva'], 'nombre' => 'Historia socio-afectiva']
                    ], function ($item) {
                        return !empty($item['detalle']);
                    });
                    DB::table('historia_ajuste_desempeno_neuro')->insert($ajusteDesempeno);


                    // Insertar interconsultas
                    DB::table('interconsultas_neuro')->where('id_historia', $idHistoria)->delete();
                    $interconsultas = array_filter([
                        ['id_historia' => $idHistoria, 'tipo' => 'intervencion_psiquiatria', 'detalle' => $request['intervencion_psiquiatria'], 'nombre' => 'Intervención psiquiátrica'],
                        ['id_historia' => $idHistoria, 'tipo' => 'intervencion_neurologia', 'detalle' => $request['intervencion_neurologia'], 'nombre' => 'Intervención neurológica'],
                        ['id_historia' => $idHistoria, 'tipo' => 'intervencion_neuropsicologia', 'detalle' => $request['intervencion_neuropsicologia'], 'nombre' => 'Intervención neuropsicológica']
                    ], function ($item) {
                        return !empty($item['detalle']);
                    });
                    DB::table('interconsultas_neuro')->insert($interconsultas);

                    // Insertar apariencia personal
                    DB::table('apariencia_personal_neuro')->where('id_historia', $idHistoria)->delete();
                    $aparienciaPersonal = array_filter([
                        ['id_historia' => $idHistoria, 'caracteristica' => 'edad', 'detalle' => implode(',' , $request['edad']) ?? null, 'nombre' => 'Edad'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'edad_otro', 'detalle' => $request['edad_otro'] ?? null, 'nombre' => 'Edad (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'desarrollo', 'detalle' => implode(',' , $request['desarrollo']) ?? null, 'nombre' => 'Desarrollo pondoestatural'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'desarrollo_otro', 'detalle' => $request['desarrollo_otro'] ?? null, 'nombre' => 'Desarrollo pondoestatural (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'aseo', 'detalle' => implode(',' , $request['aseo']) ?? null, 'nombre' => 'Aseo y Arreglo'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'aseo_otro', 'detalle' => $request['aseo_otro'] ?? null, 'nombre' => 'Aseo y arreglo (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'salud', 'detalle' => implode(',' , $request['salud']) ?? null, 'nombre' => 'Salud somática'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'salud_otro', 'detalle' => $request['salud_otro'] ?? null, 'nombre' => 'Salud somática (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'facies', 'detalle' => implode(',' , $request['facies']) ?? null, 'nombre' => 'Facies'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'facies_otro', 'detalle' => $request['facies_otro'] ?? null, 'nombre' => 'Facies (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'biotipo', 'detalle' => implode(',' , $request['biotipo']) ?? null, 'nombre' => 'Biotipo'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'biotipo_otro', 'detalle' => $request['biotipo_otro'] ?? null, 'nombre' => 'Biotipo (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'actitud', 'detalle' => implode(',' , $request['actitud']) ?? null, 'nombre' => 'Actitud'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'actitud_otro', 'detalle' => $request['actitud_otro'] ?? null, 'nombre' => 'Actitud (otro)']
                    ], function ($item) {
                        return !empty($item['detalle']);
                    });
                    DB::table('apariencia_personal_neuro')->insert($aparienciaPersonal);

                    // Insertar funciones cognitivas
                    DB::table('funciones_cognitivas_neuro')->where('id_historia', $idHistoria)->delete();
                    $funcionesSomaticas = array_filter([
                        ['id_historia' => $idHistoria, 'caracteristica' => 'consciencia', 'detalle' => implode(',' , $request['consciencia']) ?? null, 'nombre' => 'Consciencia'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'consciencia_otro', 'detalle' => $request['consciencia_otro'] ?? null, 'nombre' => 'Consciencia (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'orientacion', 'detalle' => implode(',' , $request['orientacion']) ?? null, 'nombre' => 'Orientación'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'orientacion_otro', 'detalle' => $request['orientacion_otro'] ?? null, 'nombre' => 'Orientación (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'memoria', 'detalle' => implode(',' , $request['memoria']) ?? null, 'nombre' => 'Memoria'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'memoria_otro', 'detalle' => $request['memoria_otro'] ?? null, 'nombre' => 'Memoria (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'atencion', 'detalle' => implode(',' , $request['atencion']) ?? null, 'nombre' => 'Atención'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'atencion_otro', 'detalle' => $request['atencion_otro'] ?? null, 'nombre' => 'Atención (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'concentracion', 'detalle' => implode(',' , $request['concentracion']) ?? null, 'nombre' => 'Concentración'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'concentracion_otro', 'detalle' => $request['concentracion_otro'] ?? null, 'nombre' => 'Concentración (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'lenguaje', 'detalle' => implode(',' , $request['lenguaje']) ?? null, 'nombre' => 'Lenguaje'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'lenguaje_otro', 'detalle' => $request['lenguaje_otro'] ?? null, 'nombre' => 'Lenguaje (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'pensamiento', 'detalle' => implode(',' , $request['pensamiento']) ?? null, 'nombre' => 'Pensamiento'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'pensamiento_otro', 'detalle' => $request['pensamiento_otro'] ?? null, 'nombre' => 'Pensamiento (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'afecto', 'detalle' => implode(',' , $request['afecto']) ?? null, 'nombre' => 'Afecto'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'afecto_otro', 'detalle' => $request['afecto_otro'] ?? null, 'nombre' => 'Afecto (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'sensopercepcion', 'detalle' => implode(',' , $request['sensopercepcion']) ?? null, 'nombre' => 'Sensopercepción'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'sensopercepcion_otro', 'detalle' => $request['sensopercepcion_otro'] ?? null, 'nombre' => 'Sensopercepción (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'psicomotricidad', 'detalle' => implode(',' , $request['psicomotricidad']) ?? null, 'nombre' => 'Psicomotricidad'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'psicomotricidad_otro', 'detalle' => $request['psicomotricidad_otro'] ?? null, 'nombre' => 'Psicomotricidad (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'juicio', 'detalle' => implode(',' , $request['juicio']) ?? null, 'nombre' => 'Juicio'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'juicio_otro', 'detalle' => $request['juicio_otro'] ?? null, 'nombre' => 'Juicio (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'inteligencia', 'detalle' => implode(',' , $request['inteligencia']) ?? null, 'nombre' => 'Inteligencia'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'inteligencia_otro', 'detalle' => $request['inteligencia_otro'] ?? null, 'nombre' => 'Inteligencia (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'conciencia_enfermedad', 'detalle' => implode(',' , $request['conciencia_enfermedad']) ?? null, 'nombre' => 'Conciencia de enfermedad'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'conciencia_enfermedad_otro', 'detalle' => $request['conciencia_enfermedad_otro'] ?? null, 'nombre' => 'Conciencia de enfermedad (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'sufrimiento_psicologico', 'detalle' => implode(',' , $request['sufrimiento_psicologico']) ?? null, 'nombre' => 'Sufrimiento psicológico'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'sufrimiento_psicologico_otro', 'detalle' => $request['sufrimiento_psicologico_otro'] ?? null, 'nombre' => 'Sufrimiento psicológico (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'motivacion_tratamiento', 'detalle' => implode(',' , $request['motivacion_tratamiento']) ?? null, 'nombre' => 'Motivación al tratamiento'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'motivacion_tratamiento_otro', 'detalle' => $request['motivacion_tratamiento_otro'] ?? null, 'nombre' => 'Motivación al tratamiento (otro)']
                    ], function ($item) {
                        return !empty($item['detalle']);
                    });
                    DB::table('funciones_cognitivas_neuro')->insert($funcionesSomaticas);

                    // Insertar Funciones Somáticas
                    DB::table('funciones_somaticas_neuro')->where('id_historia', $idHistoria)->delete();
                    $examenMental = array_filter([
                        'id_historia' => $idHistoria,
                        'ciclos_del_sueno' => $request['ciclos_sueno'],
                        'apetito' => $request['apetito'],
                        'actividades_autocuidado' => $request['autocuidado'],
                    ]);
                    DB::table('funciones_somaticas_neuro')->insert($examenMental);

                    /// En el caso de que sea pediatria
                    if ($request['tipoPsicologia'] == "Pediatría") {
                        // Insertar antecedentes prenatales
                        DB::table('antecedentes_prenatales_neuro')->where('id_historia', $idHistoria)->delete();
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
                        DB::table('antecedentes_prenatales_neuro')->insert($antecedentesFamiliares);

                        // Insertar antecedentes natales
                        DB::table('antecedentes_natales_neuro')->where('id_historia', $idHistoria)->delete();
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
                        DB::table('antecedentes_natales_neuro')->insert($antecedentesFamiliares);

                        // Insertar antecedentes posnatales
                        DB::table('antecedentes_posnatales_neuro')->where('id_historia', $idHistoria)->delete();
                        $antecedentesFamiliares = array_filter([
                            ['id_historia' => $idHistoria, 'tipo' => 'hospitalizaciones_postnatales', 'detalle' => $request['hospitalizaciones'], 'nombre' => 'Hospitalizaciones recién nacido'],
                            ['id_historia' => $idHistoria, 'tipo' => 'desarrollo_psicomotor', 'detalle' => $request['desarrollo_psicomotor'], 'nombre' => 'Desarrollo psicomotor']
                        ], function ($item) {
                            return !empty($item['detalle']);
                        });
                        DB::table('antecedentes_posnatales_neuro')->insert($antecedentesFamiliares);

                        // Insertar desarrollo psicomotor
                        DB::table('desarrollo_psicomotor_neuro')->where('id_historia', $idHistoria)->delete();
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
                        DB::table('desarrollo_psicomotor_neuro')->insert($antecedentesFamiliares);
                    }

                    DB::commit();
                    return ['idHistoria' => $idHistoria];
                } catch (\Exception $e) {
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
                    $idConsulta = DB::table('consultas_psicologica_neuro')->insertGetId(array_filter([
                        'id_historia' => $request['idHist'] ?? null,
                        'id_profesional' => $request['profesionalConsulta'] ?? null,
                        'fecha_consulta' => $request['fechaEvolucion'] . ' ' . $request['horaSeleccionada'] ?? null,
                        'evolucion_y_o_plantrabajo' => $request['evolucion_plan']  ?? null,
                        'estado' => 'ACTIVO'
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
                        'evolucion_y_o_plantrabajo' => $request['evolucion_plan']  ?? null,
                        'id_profesional' => $request['profesionalConsulta'] ?? null,
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

    public static function busquedaConsulta($idConsulta)
    {
        return DB::connection('mysql')->table('consultas_psicologica_neuro')
            ->where("id", $idConsulta)
            ->first();
    }

    public static function historialConsultas($idHisto)
    {
        return DB::connection('mysql')->table('consultas_psicologica_neuro')
            ->leftJoin("profesionales", "profesionales.usuario", "consultas_psicologica_neuro.id_profesional")
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
            
        if ($historia) {
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
                ->where('id', $historia->motivo_consulta)
                ->first();

            $historia->impresion_diagnostica_detalle = DB::connection('mysql')
                ->table('referencia_cie10')
                ->where('id', $historia->codigo_diagnostico)
                ->first();

            $historia->plan_intervension_detalle = DB::connection('mysql')
                ->table('opciones_hc_psicologia')
                ->where('id', $historia->plan_intervension)
                ->first();

                $historia->profesional_detalle = DB::connection('mysql')->table('profesionales')
                ->join("users", "users.id", "profesionales.usuario")
                ->where("profesionales.usuario", $historia->id_profesional)
                ->select("profesionales.*", "users.login_usuario", "users.estado_usuario", "users.id as idUsuario")
                ->first();
        }

        return $historia;
    }

    public static function busquedaHistoriaNeuroPaciente($idPac)
    {
        return DB::connection('mysql')->table('historia_clinica_neuro')
            ->where("id_paciente", $idPac)
            ->first();
    }

    public static function busquedaHistoriaPaciente($idPac)
    {
        return DB::connection('mysql')->table('historia_clinica_neuro')
            ->where("id_paciente", $idPac)
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
                        'observacion' => $request['observaciones'] ?? null,
                        'estado' => 'ACTIVO'
                    ]));

                    if (isset($request['archivo']) && is_array($request['archivo'])) {
                        foreach ($request['archivo'] as $key => $archivo) {
                            DB::connection('mysql')->table('anexos_informe_neuropsicologia')->insert([
                                'id_informe' => $idInforme,
                                'url' => $archivo,
                                'tipo_archivo' => $request['tipoArc'][$key] ?? null,
                                'nombre_archivo' => $request['nombre'][$key] ?? null,
                                'peso' => $request['peso'][$key] ?? null,
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
                    // Insertar en informe_evolucion`
                    DB::table('informe_evolucion_neuropsicologia')->where('id', $idInforme)->update(array_filter([
                        'id_paciente' => $request['idPaciente'] ?? null,
                        'id_profesional' => $request['profesionalInforme'] ?? null,
                        'fecha_creacion' => $request['fechaEvolucion'] . ' ' . $request['horaSeleccionada'],
                        'observacion' => $request['observaciones'] ?? null,
                    ]));

                    if (isset($request['archivo']) && is_array($request['archivo'])) {
                        foreach ($request['archivo'] as $key => $archivo) {
                            DB::connection('mysql')->table('anexos_informe_neuropsicologia')->insert([
                                'id_informe' => $idInforme,
                                'url' => $archivo,
                                'tipo_archivo' => $request['tipoArc'][$key] ?? null,
                                'nombre_archivo' => $request['nombre'][$key] ?? null,
                                'peso' => $request['peso'][$key] ?? null,
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
}
