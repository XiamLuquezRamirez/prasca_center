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

                    // Insertar antecedentes médicos
                    $antecedentesMedicos = array_filter([
                        ['id_historia' => $idHistoria, 'tipo' => 'quirurgicos', 'detalle' => $request['quirurgicos']],
                        ['id_historia' => $idHistoria, 'tipo' => 'toxicos', 'detalle' => $request['toxicos']],
                        ['id_historia' => $idHistoria, 'tipo' => 'traumaticos', 'detalle' => $request['traumaticos']],
                        ['id_historia' => $idHistoria, 'tipo' => 'medicacion', 'detalle' => $request['medicacion']],
                        ['id_historia' => $idHistoria, 'tipo' => 'paraclinicos', 'detalle' => $request['paraclinicos']],
                        ['id_historia' => $idHistoria, 'tipo' => 'hospitalizaciones', 'detalle' => $request['hospitalizaciones']],
                        ['id_historia' => $idHistoria, 'tipo' => 'patologias', 'detalle' => $request['patologias']]
                    ], function ($item) {
                        return !empty($item['detalle']);
                    });
                    DB::table('antecedentes_medicos')->insert($antecedentesMedicos);

                    // Insertar antecedentes familiares
                    $antecedentesFamiliares = array_filter([
                        ['id_historia' => $idHistoria, 'tipo' => 'depresion', 'detalle' => $request['depresion']],
                        ['id_historia' => $idHistoria, 'tipo' => 'ansiedad', 'detalle' => $request['ansiedad']],
                        ['id_historia' => $idHistoria, 'tipo' => 'demencia', 'detalle' => $request['demencia']],
                        ['id_historia' => $idHistoria, 'tipo' => 'alcoholismo', 'detalle' => $request['alcoholismo']],
                        ['id_historia' => $idHistoria, 'tipo' => 'drogadiccion', 'detalle' => $request['drogadiccion']],
                        ['id_historia' => $idHistoria, 'tipo' => 'discapacidad_intelectual', 'detalle' => $request['discapacidad_intelectual']],
                        ['id_historia' => $idHistoria, 'tipo' => 'patologicos', 'detalle' => $request['patologicos']],
                        ['id_historia' => $idHistoria, 'tipo' => 'otros', 'detalle' => $request['otros']],
                    ], function ($item) {
                        return !empty($item['detalle']);
                    });
                    DB::table('antecedentes_familiares')->insert($antecedentesFamiliares);

                    // Insertar áreas de ajuste y/o desempeño
                    $ajusteDesempeno = array_filter([
                        ['id_historia' => $idHistoria, 'area' => 'historia_educativa', 'detalle' => $request['historia_educativa']],
                        ['id_historia' => $idHistoria, 'area' => 'historia_laboral', 'detalle' => $request['historia_laboral']],
                        ['id_historia' => $idHistoria, 'area' => 'historia_familiar', 'detalle' => $request['historia_familiar']],
                        ['id_historia' => $idHistoria, 'area' => 'historia_social', 'detalle' => $request['historia_social']],
                        ['id_historia' => $idHistoria, 'area' => 'historia_socio_afectiva', 'detalle' => $request['historia_socio_afectiva']]
                    ], function ($item) {
                        return !empty($item['detalle']);
                    });
                    DB::table('historia_ajuste_desempeno')->insert($ajusteDesempeno);

                    // Insertar interconsultas
                    $interconsultas = array_filter([
                        ['id_historia' => $idHistoria, 'tipo' => 'intervencion_psiquiatria', 'detalle' => $request['intervencion_psiquiatria']],
                        ['id_historia' => $idHistoria, 'tipo' => 'intervencion_neurologia', 'detalle' => $request['intervencion_neurologia']],
                        ['id_historia' => $idHistoria, 'tipo' => 'intervencion_neuropsicologia', 'detalle' => $request['intervencion_neuropsicologia']],
                    ], function ($item) {
                        return !empty($item['detalle']);
                    });
                    DB::table('interconsultas')->insert($interconsultas);

                    // Insertar apariencia personal
                    $aparienciaPersonal = array_filter([
                        ['id_historia' => $idHistoria, 'caracteristica' => 'edad', 'detalle' => $request['edad'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'edad_otro', 'detalle' => $request['edad_otro'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'desarrollo', 'detalle' => $request['desarrollo'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'desarrollo_otro', 'detalle' => $request['desarrollo_otro'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'aseo', 'detalle' => $request['aseo'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'aseo_otro', 'detalle' => $request['aseo_otro'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'salud', 'detalle' => $request['salud'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'salud_otro', 'detalle' => $request['salud_otro'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'facies', 'detalle' => $request['facies'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'facies_otro', 'detalle' => $request['facies_otro'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'biotipo', 'detalle' => $request['biotipo'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'biotipo_otro', 'detalle' => $request['biotipo_otro'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'actitud', 'detalle' => $request['actitud'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'actitud_otro', 'detalle' => $request['actitud_otro'] ?? null],
                    ], function ($item) {
                        return !empty($item['detalle']);
                    });

                    // Inserta los datos filtrados
                    DB::table('apariencia_personal')->insert($aparienciaPersonal);

                    // Insertar funciones cognitivas
                    $funcionesSomaticas = array_filter([
                        ['id_historia' => $idHistoria, 'caracteristica' => 'consciencia', 'detalle' => $request['consciencia'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'consciencia_otro', 'detalle' => $request['consciencia_otro'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'orientacion', 'detalle' => $request['orientacion'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'orientacion_otro', 'detalle' => $request['orientacion_otro'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'memoria', 'detalle' => $request['memoria'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'memoria_otro', 'detalle' => $request['memoria_otro'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'atencion', 'detalle' => $request['atencion'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'atencion_otro', 'detalle' => $request['atencion_otro'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'concentracion', 'detalle' => $request['concentracion'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'concentracion_otro', 'detalle' => $request['concentracion_otro'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'lenguaje', 'detalle' => $request['lenguaje'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'lenguaje_otro', 'detalle' => $request['lenguaje_otro'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'pensamiento', 'detalle' => $request['pensamiento'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'pensamiento_otro', 'detalle' => $request['pensamiento_otro'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'afecto', 'detalle' => $request['afecto'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'afecto_otro', 'detalle' => $request['afecto_otro'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'sensopercepcion', 'detalle' => $request['sensopercepcion'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'sensopercepcion_otro', 'detalle' => $request['sensopercepcion_otro'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'psicomotricidad', 'detalle' => $request['psicomotricidad'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'psicomotricidad_otro', 'detalle' => $request['psicomotricidad_otro'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'juicio', 'detalle' => $request['juicio'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'juicio_otro', 'detalle' => $request['juicio_otro'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'inteligencia', 'detalle' => $request['inteligencia'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'inteligencia_otro', 'detalle' => $request['inteligencia_otro'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'conciencia_enfermedad', 'detalle' => $request['conciencia_enfermedad'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'conciencia_enfermedad_otro', 'detalle' => $request['conciencia_enfermedad_otro'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'sufrimiento_psicologico', 'detalle' => $request['sufrimiento_psicologico'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'sufrimiento_psicologico_otro', 'detalle' => $request['sufrimiento_psicologico_otro'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'motivacion_al_tratamiento', 'detalle' => $request['motivacion_tratamiento'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'motivacion_tratamiento_otro', 'detalle' => $request['motivacion_tratamiento_otro'] ?? null]
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
                            ['id_historia' => $idHistoria, 'tipo' => 'edad_madre', 'detalle' => $request['edad_madre']],
                            ['id_historia' => $idHistoria, 'tipo' => 'enfermedades_madre', 'detalle' => $request['enfermedades_madre']],
                            ['id_historia' => $idHistoria, 'tipo' => 'demencia', 'detalle' => $request['numero_embarazo']],
                            ['id_historia' => $idHistoria, 'tipo' => 'alcoholismo', 'detalle' => $request['estado_madre']]
                        ], function ($item) {
                            return !empty($item['detalle']);
                        });
                        DB::table('antecedentes_prenatales')->insert($antecedentesFamiliares);

                        // Insertar antecedentes natales
                        $antecedentesFamiliares = array_filter([
                            ['id_historia' => $idHistoria, 'tipo' => 'tipo_nacimiento', 'detalle' => $request['tipo_nacimiento']],
                            ['id_historia' => $idHistoria, 'tipo' => 'causa_cesarea', 'detalle' => $request['causa_cesarea']],
                            ['id_historia' => $idHistoria, 'tipo' => 'reanimacion', 'detalle' => $request['reanimacion']]
                        ], function ($item) {
                            return !empty($item['detalle']);
                        });
                        DB::table('antecedentes_natales')->insert($antecedentesFamiliares);
                    
                        // Insertar antecedentes posnatales
                        $antecedentesFamiliares = array_filter([
                            ['id_historia' => $idHistoria, 'tipo' => 'hospitalizaciones', 'detalle' => $request['hospitalizaciones']],
                            ['id_historia' => $idHistoria, 'tipo' => 'desarrollo_psicomotor', 'detalle' => $request['desarrollo_psicomotor']]
                        ], function ($item) {
                            return !empty($item['detalle']);
                        });
                        DB::table('antecedentes_posnatales')->insert($antecedentesFamiliares);
                    
                        // Insertar desarrollo psicomotor
                        $antecedentesFamiliares = array_filter([
                            ['id_historia' => $idHistoria, 'tipo' => 'control_cefalico', 'detalle' => $request['control_cefalico']],
                            ['id_historia' => $idHistoria, 'tipo' => 'rolado', 'detalle' => $request['rolado']],
                            ['id_historia' => $idHistoria, 'tipo' => 'sedente_solo', 'detalle' => $request['sedente_solo']],
                            ['id_historia' => $idHistoria, 'tipo' => 'gateo', 'detalle' => $request['gateo']],
                            ['id_historia' => $idHistoria, 'tipo' => 'gateo', 'bipedo' => $request['bipedo']],
                            ['id_historia' => $idHistoria, 'tipo' => 'gateo', 'marcha' => $request['marcha']],
                            ['id_historia' => $idHistoria, 'tipo' => 'gateo', 'lenguaje_verbal' => $request['lenguaje_verbal']],
                            ['id_historia' => $idHistoria, 'tipo' => 'gateo', 'lenguaje_verbal_fluido' => $request['lenguaje_verbal_fluido']],
                        ], function ($item) {
                            return !empty($item['detalle']);
                        });
                        DB::table('desarrollo_psicomotor')->insert($antecedentesFamiliares);
                                        
                    }

                    // Confirmar transacción
                    DB::commit();
                    return  $idHistoria;
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

                    // Insertar antecedentes médicos
                    DB::table('antecedentes_medicos')->where('id_historia', $idHistoria)->delete();
                    $antecedentesMedicos = array_filter([
                        ['id_historia' => $idHistoria, 'tipo' => 'quirurgicos', 'detalle' => $request['quirurgicos']],
                        ['id_historia' => $idHistoria, 'tipo' => 'toxicos', 'detalle' => $request['toxicos']],
                        ['id_historia' => $idHistoria, 'tipo' => 'traumaticos', 'detalle' => $request['traumaticos']],
                        ['id_historia' => $idHistoria, 'tipo' => 'medicacion', 'detalle' => $request['medicacion']],
                        ['id_historia' => $idHistoria, 'tipo' => 'paraclinicos', 'detalle' => $request['paraclinicos']],
                        ['id_historia' => $idHistoria, 'tipo' => 'hospitalizaciones', 'detalle' => $request['hospitalizaciones']],
                        ['id_historia' => $idHistoria, 'tipo' => 'patologias', 'detalle' => $request['patologias']]
                    ], function ($item) {
                        return !empty($item['detalle']);
                    });
                    DB::table('antecedentes_medicos')->insert($antecedentesMedicos);

                    // Insertar antecedentes familiares
                    DB::table('antecedentes_familiares')->where('id_historia', $idHistoria)->delete();
                    $antecedentesFamiliares = array_filter([
                        ['id_historia' => $idHistoria, 'tipo' => 'depresion', 'detalle' => $request['depresion']],
                        ['id_historia' => $idHistoria, 'tipo' => 'ansiedad', 'detalle' => $request['ansiedad']],
                        ['id_historia' => $idHistoria, 'tipo' => 'demencia', 'detalle' => $request['demencia']],
                        ['id_historia' => $idHistoria, 'tipo' => 'alcoholismo', 'detalle' => $request['alcoholismo']],
                        ['id_historia' => $idHistoria, 'tipo' => 'drogadiccion', 'detalle' => $request['drogadiccion']],
                        ['id_historia' => $idHistoria, 'tipo' => 'discapacidad_intelectual', 'detalle' => $request['discapacidad_intelectual']],
                        ['id_historia' => $idHistoria, 'tipo' => 'patologicos', 'detalle' => $request['patologicos']],
                        ['id_historia' => $idHistoria, 'tipo' => 'otros', 'detalle' => $request['otros']],
                    ], function ($item) {
                        return !empty($item['detalle']);
                    });
                    DB::table('antecedentes_familiares')->insert($antecedentesFamiliares);

                    // Insertar áreas de ajuste y/o desempeño
                    DB::table('historia_ajuste_desempeno')->where('id_historia', $idHistoria)->delete();
                    $ajusteDesempeno = array_filter([
                        ['id_historia' => $idHistoria, 'area' => 'historia_educativa', 'detalle' => $request['historia_educativa']],
                        ['id_historia' => $idHistoria, 'area' => 'historia_laboral', 'detalle' => $request['historia_laboral']],
                        ['id_historia' => $idHistoria, 'area' => 'historia_familiar', 'detalle' => $request['historia_familiar']],
                        ['id_historia' => $idHistoria, 'area' => 'historia_social', 'detalle' => $request['historia_social']],
                        ['id_historia' => $idHistoria, 'area' => 'historia_socio_afectiva', 'detalle' => $request['historia_socio_afectiva']]
                    ], function ($item) {
                        return !empty($item['detalle']);
                    });
                    DB::table('historia_ajuste_desempeno')->insert($ajusteDesempeno);

                    // Insertar interconsultas
                    DB::table('interconsultas')->where('id_historia', $idHistoria)->delete();
                    $interconsultas = array_filter([
                        ['id_historia' => $idHistoria, 'tipo' => 'intervencion_psiquiatria', 'detalle' => $request['intervencion_psiquiatria']],
                        ['id_historia' => $idHistoria, 'tipo' => 'intervencion_neurologia', 'detalle' => $request['intervencion_neurologia']],
                        ['id_historia' => $idHistoria, 'tipo' => 'intervencion_neuropsicologia', 'detalle' => $request['intervencion_neuropsicologia']],
                    ], function ($item) {
                        return !empty($item['detalle']);
                    });
                    DB::table('interconsultas')->insert($interconsultas);

                    // Insertar apariencia personal
                    DB::table('apariencia_personal')->where('id_historia', $idHistoria)->delete();
                    $aparienciaPersonal = array_filter([
                        ['id_historia' => $idHistoria, 'caracteristica' => 'edad', 'detalle' => $request['edad'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'edad_otro', 'detalle' => $request['edad_otro'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'desarrollo', 'detalle' => $request['desarrollo'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'desarrollo_otro', 'detalle' => $request['desarrollo_otro'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'aseo', 'detalle' => $request['aseo'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'aseo_otro', 'detalle' => $request['aseo_otro'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'salud', 'detalle' => $request['salud'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'salud_otro', 'detalle' => $request['salud_otro'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'facies', 'detalle' => $request['facies'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'facies_otro', 'detalle' => $request['facies_otro'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'biotipo', 'detalle' => $request['biotipo'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'biotipo_otro', 'detalle' => $request['biotipo_otro'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'actitud', 'detalle' => $request['actitud'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'actitud_otro', 'detalle' => $request['actitud_otro'] ?? null],
                    ], function ($item) {
                        return !empty($item['detalle']);
                    });

                    // Inserta los datos filtrados
                    DB::table('apariencia_personal')->insert($aparienciaPersonal);

                    // Insertar funciones cognitivas
                    DB::table('funciones_cognitivas')->where('id_historia', $idHistoria)->delete();

                    $funcionesSomaticas = array_filter([
                        ['id_historia' => $idHistoria, 'caracteristica' => 'consciencia', 'detalle' => $request['consciencia'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'consciencia_otro', 'detalle' => $request['consciencia_otro'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'orientacion', 'detalle' => $request['orientacion'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'orientacion_otro', 'detalle' => $request['orientacion_otro'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'memoria', 'detalle' => $request['memoria'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'memoria_otro', 'detalle' => $request['memoria_otro'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'atencion', 'detalle' => $request['atencion'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'atencion_otro', 'detalle' => $request['atencion_otro'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'concentracion', 'detalle' => $request['concentracion'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'concentracion_otro', 'detalle' => $request['concentracion_otro'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'lenguaje', 'detalle' => $request['lenguaje'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'lenguaje_otro', 'detalle' => $request['lenguaje_otro'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'pensamiento', 'detalle' => $request['pensamiento'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'pensamiento_otro', 'detalle' => $request['pensamiento_otro'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'afecto', 'detalle' => $request['afecto'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'afecto_otro', 'detalle' => $request['afecto_otro'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'sensopercepcion', 'detalle' => $request['sensopercepcion'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'sensopercepcion_otro', 'detalle' => $request['sensopercepcion_otro'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'psicomotricidad', 'detalle' => $request['psicomotricidad'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'psicomotricidad_otro', 'detalle' => $request['psicomotricidad_otro'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'juicio', 'detalle' => $request['juicio'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'juicio_otro', 'detalle' => $request['juicio_otro'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'inteligencia', 'detalle' => $request['inteligencia'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'inteligencia_otro', 'detalle' => $request['inteligencia_otro'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'conciencia_enfermedad', 'detalle' => $request['conciencia_enfermedad'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'conciencia_enfermedad_otro', 'detalle' => $request['conciencia_enfermedad_otro'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'sufrimiento_psicologico', 'detalle' => $request['sufrimiento_psicologico'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'sufrimiento_psicologico_otro', 'detalle' => $request['sufrimiento_psicologico_otro'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'motivacion_al_tratamiento', 'detalle' => $request['motivacion_tratamiento'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'motivacion_tratamiento_otro', 'detalle' => $request['motivacion_tratamiento_otro'] ?? null]
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

                    if ($request['tipoPsicologia'] == "Pediatria") {
                    }


                    // Confirmar transacción
                    DB::commit();
                    return  $idHistoria;
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
    public static function busquedaHistoriaPaciente($idPac)
    {
        return DB::connection('mysql')->table('historia_clinica')
            ->where("id_paciente", $idPac)
            ->exists();
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

    public static function busquedaAreaAjuste($idHisto)
    {
        return DB::connection('mysql')->table('historia_ajuste_desempeno')
            ->where("id_historia", $idHisto)
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
