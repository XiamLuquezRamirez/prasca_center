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


class HistoriaNeuroPsicologica extends Model
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
                    $idHistoria = DB::table('historia_clinica_neuro')->insertGetId(array_filter([
                        'id_paciente' => $request['idPaciente'] ?? null,
                        'id_profesional' => Auth::user()->id,
                        'remision' => $request['remision'] ?? null,
                        'codigo_consulta' => $request['codConsulta'] ?? null,
                        'motivo_consulta' => $request['motivoConsulta'] ?? null,
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
                        'estado_hitoria' => 'abierta',
                        'estado_registro' => 'ACTIVO',
                    ]));

                    // Insertar antecedentes médicos
                    $antecedentesMedicos = array_filter([
                        ['id_historia' => $idHistoria, 'tipo' => 'quirurgicos', 'detalle' => $request['quirurgicos']],
                        ['id_historia' => $idHistoria, 'tipo' => 'toxicos', 'detalle' => $request['toxico']],
                        ['id_historia' => $idHistoria, 'tipo' => 'traumaticos', 'detalle' => $request['traumaticos']],
                        ['id_historia' => $idHistoria, 'tipo' => 'medicacion', 'detalle' => $request['medicacion']],
                        ['id_historia' => $idHistoria, 'tipo' => 'paraclinicos', 'detalle' => $request['paraclinicos']],
                        ['id_historia' => $idHistoria, 'tipo' => 'hospitalizaciones', 'detalle' => $request['hospitalizaciones']],
                        ['id_historia' => $idHistoria, 'tipo' => 'patologia', 'detalle' => $request['patologia']]
                    ], function ($item) {
                        return !empty($item['detalle']);
                    });
                    DB::table('antecedentes_medicos_neuro')->insert($antecedentesMedicos);


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
                    DB::table('antecedentes_familiares_neuro')->insert($antecedentesFamiliares);
                    

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
                    DB::table('historia_ajuste_desempeno_neuro')->insert($ajusteDesempeno);
                    

                    // Insertar interconsultas
                    $interconsultas = array_filter([
                        ['id_historia' => $idHistoria, 'tipo' => 'intervencion_psiquiatria', 'detalle' => $request['intervencion_psiquiatria']],
                        ['id_historia' => $idHistoria, 'tipo' => 'intervencion_neurologia', 'detalle' => $request['intervencion_neurologia']],
                        ['id_historia' => $idHistoria, 'tipo' => 'intervencion_neuropsicologia', 'detalle' => $request['intervencion_neuropsicologia']],
                    ], function ($item) {
                        return !empty($item['detalle']);
                    });
                    DB::table('interconsultas_neuro')->insert($interconsultas);

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
                    DB::table('apariencia_personal_neuro')->insert($aparienciaPersonal);

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
                        ['id_historia' => $idHistoria, 'caracteristica' => 'motivacion_tratamiento', 'detalle' => $request['motivacion_tratamiento'] ?? null],
                        ['id_historia' => $idHistoria, 'caracteristica' => 'motivacion_tratamiento_otro', 'detalle' => $request['motivacion_tratamiento_otro'] ?? null]
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
                    
                    // Confirmar transacción
                    DB::commit();
                    return  $idHistoria;
                } catch (\Exception $e) {
                    // Revertir transacción en caso de error
                    DB::rollBack();
                    throw $e;
                }
            } else {
               
            }
        } catch (Exception $e) {
            // Manejo del error
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al procesar el formulario: ' . $e->getMessage(),
            ], 500);
        }
        
    }

    public static function busquedaHistoriaNeuro($idHisto){
        return DB::connection('mysql')->table('historia_clinica_neuro')
        ->where("id", $idHisto)
        ->first();
    }

    public static function busquedaHistoriaNeuroPaciente($idPac)
    {
        return DB::connection('mysql')->table('historia_clinica_neuro')
        ->where("id_paciente", $idPac)
        ->exists();
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
        return DB::connection('mysql')->table('apariencia_personal_neuro')
            ->where("id_historia", $idHisto)
            ->get();
    }
    public static function busquedaFuncionesCognitivas($idHisto)
    {
        return DB::connection('mysql')->table('funciones_cognitivas_neuro')
            ->where("id_historia", $idHisto)
            ->get();
    }
    public static function busquedaFuncionesSomaticas($idHisto)
    {
        return DB::connection('mysql')->table('funciones_somaticas_neuro')
            ->where("id_historia", $idHisto)
            ->first();
    }
}
