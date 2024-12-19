<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Auth;


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

    public static function Guardar($request)
    {

        try {
            if ($request['accHistoria'] == 'guardar') {

                DB::beginTransaction();

                try {
                    // Insertar en `historia_clinica`
                    $idHistoria = DB::table('historia_clinica')->insertGetId(array_filter([
                        'id_paciente' => $request['idPaciente'] ?? null,
                        'id_profesional' => Auth::user()->id,
                        'primera_vez' => $request['primeraVez'] ?? null,
                        'remision' => $request['remision'] ?? null,
                        'codigo_consulta' => $request['codConsulta'] ?? null,
                        'motivo_consulta' => implode(',', $request['motivoConsulta']) ?? null,
                        'otro_motivo_consulta' => $request['otroMotivo'] ?? null,
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
                        'estado_hitoria' => 'abierta',
                        'estado_registro' => 'ACTIVO',
                    ]));

                    // insertar datos de consulta 

                    $idConsulta = DB::table('consultas_psicologica')->insertGetId(array_filter([
                        'id_historia' => $idHistoria,
                        'id_profesional' => Auth::user()->id,
                        'fecha_consulta' => now(),
                        'codigo_consulta' => $request['codConsulta'] ?? null,
                        'impresion_diagnostica' => $request['codDiagnostico']  ?? null,
                        'motivo' => "",
                        'evolucion_tratamiento' => "",
                        'plan_continuidad' => "",
                        'intervencion_psiquiatria' => $request['intervencion_psiquiatria'] ?? null,
                        'intervencion_neurologia' => $request['intervencion_neurologia'] ?? null,
                        'intervencion_neuropsicologia' => $request['intervencion_neuropsicologia'] ?? null,
                        'sugerencias_interconsultas' => $request['sugerencia_interconsultas'] ?? null,
                        'observaciones_recomendaciones' => $request['observaciones_recomendaciones'] ?? null,
                        'estado' => 'ACTIVO'
                    ]));

                    // Insertar antecedentes médicos
                    $antecedentesMedicos = array_filter([
                        ['id_historia' => $idHistoria, 'tipo' => 'quirurgicos', 'detalle' => $request['quirurgicos'], 'nombre' => 'Quirúrgico'],
                        ['id_historia' => $idHistoria, 'tipo' => 'toxicos', 'detalle' => $request['toxicos'], 'nombre' => 'Tóxicos'],
                        ['id_historia' => $idHistoria, 'tipo' => 'traumaticos', 'detalle' => $request['traumaticos'], 'nombre' => 'Traumáticos'],
                        ['id_historia' => $idHistoria, 'tipo' => 'medicacion', 'detalle' => $request['medicacion'], 'nombre' => 'Medicación'],
                        ['id_historia' => $idHistoria, 'tipo' => 'paraclinicos', 'detalle' => $request['paraclinicos'], 'nombre' => 'Paraclínicos'],
                        ['id_historia' => $idHistoria, 'tipo' => 'hospitalizaciones', 'detalle' => $request['hospitalizaciones'], 'nombre' => 'Hospitalizaciones'],
                        ['id_historia' => $idHistoria, 'tipo' => 'patologia', 'detalle' => $request['patologias'], 'nombre' => 'Patología']
                    ], function ($item) {
                        return !empty($item['detalle']);
                    });
                    DB::table('antecedentes_medicos')->insert($antecedentesMedicos);

                    // Insertar antecedentes familiares
                    $antecedentesFamiliares = array_filter([
                        ['id_historia' => $idHistoria, 'tipo' => 'depresion', 'detalle' => $request['depresion'], 'nombre' => 'Depresión'],
                        ['id_historia' => $idHistoria, 'tipo' => 'ansiedad', 'detalle' => $request['ansiedad'], 'nombre' => 'Ansiedad'],
                        ['id_historia' => $idHistoria, 'tipo' => 'demencia', 'detalle' => $request['demencia'], 'nombre' => 'Demencia'],
                        ['id_historia' => $idHistoria, 'tipo' => 'alcoholismo', 'detalle' => $request['alcoholismo'], 'nombre' => 'Alcoholismo'],
                        ['id_historia' => $idHistoria, 'tipo' => 'drogadiccion', 'detalle' => $request['drogadiccion'], 'nombre' => 'Drogadicción'],
                        ['id_historia' => $idHistoria, 'tipo' => 'discapacidad_intelectual', 'detalle' => $request['discapacidad_intelectual'], 'nombre' => 'Discapacidad intelectual'],
                        ['id_historia' => $idHistoria, 'tipo' => 'patologicos', 'detalle' => $request['patologicos'], 'nombre' => 'Patológicos'],
                        ['id_historia' => $idHistoria, 'tipo' => 'otros', 'detalle' => $request['otros'], 'nombre' => 'Otros'],
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
                        ['id_historia' => $idHistoria, 'caracteristica' => 'edad', 'detalle' => $request['edad'] ?? null, 'nombre' => 'Edad'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'edad_otro', 'detalle' => $request['edad_otro'] ?? null, 'nombre' => 'Edad (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'desarrollo', 'detalle' => $request['desarrollo'] ?? null, 'nombre' => 'Desarrollo pondoestatural'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'desarrollo_otro', 'detalle' => $request['desarrollo_otro'] ?? null, 'nombre' => 'Desarrollo pondoestatural (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'aseo', 'detalle' => $request['aseo'] ?? null, 'nombre' => 'Aseo y Arreglo'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'aseo_otro', 'detalle' => $request['aseo_otro'] ?? null, 'nombre' => 'Aseo y arreglo (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'salud', 'detalle' => $request['salud'] ?? null, 'nombre' => 'Salud somática'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'salud_otro', 'detalle' => $request['salud_otro'] ?? null, 'nombre' => 'Salud somática (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'facies', 'detalle' => $request['facies'] ?? null, 'nombre' => 'Facies'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'facies_otro', 'detalle' => $request['facies_otro'] ?? null, 'nombre' => 'Facies (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'biotipo', 'detalle' => $request['biotipo'] ?? null, 'nombre' => 'Biotipo'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'biotipo_otro', 'detalle' => $request['biotipo_otro'] ?? null, 'nombre' => 'Biotipo (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'actitud', 'detalle' => $request['actitud'] ?? null, 'nombre' => 'Actitud'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'actitud_otro', 'detalle' => $request['actitud_otro'] ?? null, 'nombre' => 'Actitud (otro)']
                    ], function ($item) {
                        return !empty($item['detalle']);
                    });

                    // Inserta los datos filtrados
                    DB::table('apariencia_personal')->insert($aparienciaPersonal);

                    // Insertar funciones cognitivas
                    $funcionesSomaticas = array_filter([
                        ['id_historia' => $idHistoria, 'caracteristica' => 'consciencia', 'detalle' => $request['consciencia'] ?? null, 'nombre' => 'Consciencia'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'consciencia_otro', 'detalle' => $request['consciencia_otro'] ?? null, 'nombre' => 'Consciencia (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'orientacion', 'detalle' => $request['orientacion'] ?? null, 'nombre' => 'Orientación'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'orientacion_otro', 'detalle' => $request['orientacion_otro'] ?? null, 'nombre' => 'Orientación (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'memoria', 'detalle' => $request['memoria'] ?? null, 'nombre' => 'Memoria'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'memoria_otro', 'detalle' => $request['memoria_otro'] ?? null, 'nombre' => 'Memoria (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'atencion', 'detalle' => $request['atencion'] ?? null, 'nombre' => 'Atención'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'atencion_otro', 'detalle' => $request['atencion_otro'] ?? null, 'nombre' => 'Atención (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'concentracion', 'detalle' => $request['concentracion'] ?? null, 'nombre' => 'Concentración'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'concentracion_otro', 'detalle' => $request['concentracion_otro'] ?? null, 'nombre' => 'Concentración (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'lenguaje', 'detalle' => $request['lenguaje'] ?? null, 'nombre' => 'Lenguaje'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'lenguaje_otro', 'detalle' => $request['lenguaje_otro'] ?? null, 'nombre' => 'Lenguaje (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'pensamiento', 'detalle' => $request['pensamiento'] ?? null, 'nombre' => 'Pensamiento'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'pensamiento_otro', 'detalle' => $request['pensamiento_otro'] ?? null, 'nombre' => 'Pensamiento (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'afecto', 'detalle' => $request['afecto'] ?? null, 'nombre' => 'Afecto'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'afecto_otro', 'detalle' => $request['afecto_otro'] ?? null, 'nombre' => 'Afecto (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'sensopercepcion', 'detalle' => $request['sensopercepcion'] ?? null, 'nombre' => 'Sensopercepción'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'sensopercepcion_otro', 'detalle' => $request['sensopercepcion_otro'] ?? null, 'nombre' => 'Sensopercepción (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'psicomotricidad', 'detalle' => $request['psicomotricidad'] ?? null, 'nombre' => 'Psicomotricidad'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'psicomotricidad_otro', 'detalle' => $request['psicomotricidad_otro'] ?? null, 'nombre' => 'Psicomotricidad (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'juicio', 'detalle' => $request['juicio'] ?? null, 'nombre' => 'Juicio'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'juicio_otro', 'detalle' => $request['juicio_otro'] ?? null, 'nombre' => 'Juicio (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'inteligencia', 'detalle' => $request['inteligencia'] ?? null, 'nombre' => 'Inteligencia'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'inteligencia_otro', 'detalle' => $request['inteligencia_otro'] ?? null, 'nombre' => 'Inteligencia (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'conciencia_enfermedad', 'detalle' => $request['conciencia_enfermedad'] ?? null, 'nombre' => 'Conciencia de enfermedad'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'conciencia_enfermedad_otro', 'detalle' => $request['conciencia_enfermedad_otro'] ?? null, 'nombre' => 'Conciencia de enfermedad (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'sufrimiento_psicologico', 'detalle' => $request['sufrimiento_psicologico'] ?? null, 'nombre' => 'Sufrimiento psicológico'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'sufrimiento_psicologico_otro', 'detalle' => $request['sufrimiento_psicologico_otro'] ?? null, 'nombre' => 'Sufrimiento psicológico (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'motivacion_tratamiento', 'detalle' => $request['motivacion_tratamiento'] ?? null, 'nombre' => 'Motivación al tratamiento'],
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
                    if ($request['tipoPsicologia'] == "Pediatria") {
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
                            ['id_historia' => $idHistoria, 'tipo' => 'hospitalizaciones_postnatales', 'detalle' => $request['hospitalizaciones'], 'nombre' => 'Hospitalizaciones recién nacido'],
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

                    return ['idHistoria' => $idHistoria, 'idConsulta' => $idConsulta];
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
                        'id_profesional' => Auth::user()->id,
                        'primera_vez' => $request['primeraVez'] ?? null,
                        'remision' => $request['remision'] ?? null,
                        'codigo_consulta' => $request['codConsulta'] ?? null,
                        'motivo_consulta' => implode(',', $request['motivoConsulta']) ?? null,
                        'otro_motivo_consulta' => $request['otroMotivo'] ?? null,
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
                        'estado_hitoria' => 'abierta'
                    ]));


                    $consulta = DB::connection('mysql')->table('consultas_psicologica')
                        ->where("id_historia", $idHistoria)
                        ->orderBy("fecha_consulta", "asc")
                        ->first();


                    $idConsulta =  DB::table('consultas_psicologica')->where('id', $consulta->id)->update(array_filter([
                        'id_historia' => $idHistoria,
                        'id_profesional' => Auth::user()->id,
                        'fecha_consulta' => now(),
                        'codigo_consulta' => $request['codConsulta'] ?? null,
                        'impresion_diagnostica' => $request['codDiagnostico']  ?? null,
                        'intervencion_psiquiatria' => $request['intervencion_psiquiatria'] ?? null,
                        'intervencion_neurologia' => $request['intervencion_neurologia'] ?? null,
                        'intervencion_neuropsicologia' => $request['intervencion_neuropsicologia'] ?? null,
                        'sugerencias_interconsultas' => $request['sugerencia_interconsultas'] ?? null,
                        'observaciones_recomendaciones' => $request['observaciones_recomendaciones'] ?? null,
                        'estado' => 'ACTIVO'
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
                        ['id_historia' => $idHistoria, 'tipo' => 'patologia', 'detalle' => $request['patologias'], 'nombre' => 'Patología']
                    ], function ($item) {
                        return !empty($item['detalle']);
                    });
                    DB::table('antecedentes_medicos')->insert($antecedentesMedicos);

                    // Insertar antecedentes familiares
                    DB::table('antecedentes_familiares')->where('id_historia', $idHistoria)->delete();
                    $antecedentesFamiliares = array_filter([
                        ['id_historia' => $idHistoria, 'tipo' => 'depresion', 'detalle' => $request['depresion'], 'nombre' => 'Depresión'],
                        ['id_historia' => $idHistoria, 'tipo' => 'ansiedad', 'detalle' => $request['ansiedad'], 'nombre' => 'Ansiedad'],
                        ['id_historia' => $idHistoria, 'tipo' => 'demencia', 'detalle' => $request['demencia'], 'nombre' => 'Demencia'],
                        ['id_historia' => $idHistoria, 'tipo' => 'alcoholismo', 'detalle' => $request['alcoholismo'], 'nombre' => 'Alcoholismo'],
                        ['id_historia' => $idHistoria, 'tipo' => 'drogadiccion', 'detalle' => $request['drogadiccion'], 'nombre' => 'Drogadicción'],
                        ['id_historia' => $idHistoria, 'tipo' => 'discapacidad_intelectual', 'detalle' => $request['discapacidad_intelectual'], 'nombre' => 'Discapacidad intelectual'],
                        ['id_historia' => $idHistoria, 'tipo' => 'patologicos', 'detalle' => $request['patologicos'], 'nombre' => 'Patológicos'],
                        ['id_historia' => $idHistoria, 'tipo' => 'otros', 'detalle' => $request['otros'], 'nombre' => 'Otros'],
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
                        ['id_historia' => $idHistoria, 'caracteristica' => 'edad', 'detalle' => $request['edad'] ?? null, 'nombre' => 'Edad'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'edad_otro', 'detalle' => $request['edad_otro'] ?? null, 'nombre' => 'Edad (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'desarrollo', 'detalle' => $request['desarrollo'] ?? null, 'nombre' => 'Desarrollo pondoestatural'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'desarrollo_otro', 'detalle' => $request['desarrollo_otro'] ?? null, 'nombre' => 'Desarrollo pondoestatural (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'aseo', 'detalle' => $request['aseo'] ?? null, 'nombre' => 'Aseo y Arreglo'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'aseo_otro', 'detalle' => $request['aseo_otro'] ?? null, 'nombre' => 'Aseo y arreglo (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'salud', 'detalle' => $request['salud'] ?? null, 'nombre' => 'Salud somática'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'salud_otro', 'detalle' => $request['salud_otro'] ?? null, 'nombre' => 'Salud somática (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'facies', 'detalle' => $request['facies'] ?? null, 'nombre' => 'Facies'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'facies_otro', 'detalle' => $request['facies_otro'] ?? null, 'nombre' => 'Facies (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'biotipo', 'detalle' => $request['biotipo'] ?? null, 'nombre' => 'Biotipo'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'biotipo_otro', 'detalle' => $request['biotipo_otro'] ?? null, 'nombre' => 'Biotipo (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'actitud', 'detalle' => $request['actitud'] ?? null, 'nombre' => 'Actitud'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'actitud_otro', 'detalle' => $request['actitud_otro'] ?? null, 'nombre' => 'Actitud (otro)']
                    ], function ($item) {
                        return !empty($item['detalle']);
                    });

                    // Inserta los datos filtrados
                    DB::table('apariencia_personal')->insert($aparienciaPersonal);

                    // Insertar funciones cognitivas
                    DB::table('funciones_cognitivas')->where('id_historia', $idHistoria)->delete();

                    $funcionesSomaticas = array_filter([
                        ['id_historia' => $idHistoria, 'caracteristica' => 'consciencia', 'detalle' => $request['consciencia'] ?? null, 'nombre' => 'Consciencia'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'consciencia_otro', 'detalle' => $request['consciencia_otro'] ?? null, 'nombre' => 'Consciencia (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'orientacion', 'detalle' => $request['orientacion'] ?? null, 'nombre' => 'Orientación'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'orientacion_otro', 'detalle' => $request['orientacion_otro'] ?? null, 'nombre' => 'Orientación (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'memoria', 'detalle' => $request['memoria'] ?? null, 'nombre' => 'Memoria'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'memoria_otro', 'detalle' => $request['memoria_otro'] ?? null, 'nombre' => 'Memoria (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'atencion', 'detalle' => $request['atencion'] ?? null, 'nombre' => 'Atención'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'atencion_otro', 'detalle' => $request['atencion_otro'] ?? null, 'nombre' => 'Atención (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'concentracion', 'detalle' => $request['concentracion'] ?? null, 'nombre' => 'Concentración'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'concentracion_otro', 'detalle' => $request['concentracion_otro'] ?? null, 'nombre' => 'Concentración (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'lenguaje', 'detalle' => $request['lenguaje'] ?? null, 'nombre' => 'Lenguaje'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'lenguaje_otro', 'detalle' => $request['lenguaje_otro'] ?? null, 'nombre' => 'Lenguaje (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'pensamiento', 'detalle' => $request['pensamiento'] ?? null, 'nombre' => 'Pensamiento'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'pensamiento_otro', 'detalle' => $request['pensamiento_otro'] ?? null, 'nombre' => 'Pensamiento (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'afecto', 'detalle' => $request['afecto'] ?? null, 'nombre' => 'Afecto'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'afecto_otro', 'detalle' => $request['afecto_otro'] ?? null, 'nombre' => 'Afecto (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'sensopercepcion', 'detalle' => $request['sensopercepcion'] ?? null, 'nombre' => 'Sensopercepción'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'sensopercepcion_otro', 'detalle' => $request['sensopercepcion_otro'] ?? null, 'nombre' => 'Sensopercepción (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'psicomotricidad', 'detalle' => $request['psicomotricidad'] ?? null, 'nombre' => 'Psicomotricidad'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'psicomotricidad_otro', 'detalle' => $request['psicomotricidad_otro'] ?? null, 'nombre' => 'Psicomotricidad (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'juicio', 'detalle' => $request['juicio'] ?? null, 'nombre' => 'Juicio'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'juicio_otro', 'detalle' => $request['juicio_otro'] ?? null, 'nombre' => 'Juicio (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'inteligencia', 'detalle' => $request['inteligencia'] ?? null, 'nombre' => 'Inteligencia'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'inteligencia_otro', 'detalle' => $request['inteligencia_otro'] ?? null, 'nombre' => 'Inteligencia (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'conciencia_enfermedad', 'detalle' => $request['conciencia_enfermedad'] ?? null, 'nombre' => 'Conciencia de enfermedad'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'conciencia_enfermedad_otro', 'detalle' => $request['conciencia_enfermedad_otro'] ?? null, 'nombre' => 'Conciencia de enfermedad (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'sufrimiento_psicologico', 'detalle' => $request['sufrimiento_psicologico'] ?? null, 'nombre' => 'Sufrimiento psicológico'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'sufrimiento_psicologico_otro', 'detalle' => $request['sufrimiento_psicologico_otro'] ?? null, 'nombre' => 'Sufrimiento psicológico (otro)'],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'motivacion_tratamiento', 'detalle' => $request['motivacion_tratamiento'] ?? null, 'nombre' => 'Motivación al tratamiento'],
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
                            ['id_historia' => $idHistoria, 'tipo' => 'hospitalizaciones_postnatales', 'detalle' => $request['hospitalizaciones'], 'nombre' => 'Hospitalizaciones recién nacido'],
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
                    return ['idHistoria' => $idHistoria, 'idConsulta' => $idConsulta];
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
                        'fecha_consulta' => now(),
                        'codigo_consulta' => $request['codConsultaConsulta'] ?? null,
                        'impresion_diagnostica' => $request['codImpresionDiagnosticoConsulta']  ?? null,
                        'motivo' => $request['motivoConsultaModal'] ?? null,
                        'resumen_evaluacion' => $request['resumen_evaluacion_inicial'] ?? null,
                        'evolucion_tratamiento' => $request['evolucion_tratamiento'] ?? null,
                        'plan_continuidad' => $request['plan_continuidad'] ?? null,
                        'intervencion_psiquiatria' => $request['intervencion_psiquiatria_consulta'] ?? null,
                        'intervencion_neurologia' => $request['intervencion_neurologia_consulta'] ?? null,
                        'intervencion_neuropsicologia' => $request['intervencion_neuropsicologia_consulta'] ?? null,
                        'sugerencias_interconsultas' => $request['sugerencia_consulta'] ?? null,
                        'observaciones_recomendaciones' => $request['observaciones_consulta'] ?? null,
                        'estado' => 'ACTIVO'
                    ]));

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
                        'codigo_consulta' => $request['codConsultaConsulta'] ?? null,
                        'impresion_diagnostica' => $request['codImpresionDiagnosticoConsulta']  ?? null,
                        'motivo' => $request['motivoConsultaModal'] ?? null,
                        'resumen_evaluacion' => $request['resumen_evaluacion_inicial'] ?? null,
                        'evolucion_tratamiento' => $request['evolucion_tratamiento'] ?? null,
                        'plan_continuidad' => $request['plan_continuidad'] ?? null,
                        'intervencion_psiquiatria' => $request['intervencion_psiquiatria_consulta'] ?? null,
                        'intervencion_neurologia' => $request['intervencion_neurologia_consulta'] ?? null,
                        'intervencion_neuropsicologia' => $request['intervencion_neuropsicologia_consulta'] ?? null,
                        'sugerencias_interconsultas' => $request['sugerencia_consulta'] ?? null,
                        'observaciones_recomendaciones' => $request['observaciones_consulta'] ?? null
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

    public static function busquedaHistoria($idHisto)
    {
        return DB::connection('mysql')->table('historia_clinica')
            ->where("id", $idHisto)
            ->first();
    }

    public static function busquedaConsulta($idConsulta)
    {
        return DB::connection('mysql')->table('consultas_psicologica')
            ->where("id", $idConsulta)
            ->first();
    }

    public static function busquedaHistoriaPaciente($idPac)
    {
        return DB::connection('mysql')->table('historia_clinica')
            ->where("id_paciente", $idPac)
            ->first();
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

    public static function busquedaInterconsulta($idHisto)
    {
        return DB::connection('mysql')->table('interconsultas')
            ->where("id_historia", $idHisto)
            ->get();
    }
    public static function busquedaAparienciaPersonal($idHisto)
    {
        return DB::connection('mysql')->table('apariencia_personal')
            ->where("id_historia", $idHisto)
            ->get();
    }
    public static function busquedaFuncionesCognitivas($idHisto)
    {
        return DB::connection('mysql')->table('funciones_cognitivas')
            ->where("id_historia", $idHisto)
            ->get();
    }
    public static function busquedaFuncionesSomaticas($idHisto)
    {
        return DB::connection('mysql')->table('funciones_somaticas')
            ->where("id_historia", $idHisto)
            ->first();
    }
}
