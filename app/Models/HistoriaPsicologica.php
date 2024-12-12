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
                        'motivo_consulta' => $request['motivoConsulta'] ?? null,
                        'otro_motivo_consulta' => $request['otroMotivo'] ?? null,
                        'enfermedad_actual' => $request['enfermedadActual'] ?? null,
                        'dx_principal' => $request['codDiagnostico'] ?? null,
                        'codigo_diagnostico' => $request['codImpresionDiagnostico'] ?? null,
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
                        ['id_historia' => $idHistoria, 'tipo' => 'toxicos', 'detalle' => $request['toxico']],
                        ['id_historia' => $idHistoria, 'tipo' => 'traumaticos', 'detalle' => $request['traumaticos']],
                        ['id_historia' => $idHistoria, 'tipo' => 'medicacion_actual', 'detalle' => $request['medicacion']],
                        ['id_historia' => $idHistoria, 'tipo' => 'paraclinicos', 'detalle' => $request['paraclinicos']],
                        ['id_historia' => $idHistoria, 'tipo' => 'hospitalizaciones', 'detalle' => $request['hospitalizaciones']],
                        ['id_historia' => $idHistoria, 'tipo' => 'patologia', 'detalle' => $request['patologia']]
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
                        ['id_historia' => $idHistoria, 'tipo' => 'psiquiatria', 'detalle' => $request['intervencion_psiquiatria']],
                        ['id_historia' => $idHistoria, 'tipo' => 'neurologia', 'detalle' => $request['intervencion_neurologia']],
                        ['id_historia' => $idHistoria, 'tipo' => 'intervencion_neuropsicologia', 'detalle' => $request['intervencion_neuropsicologia']],
                    ], function ($item) {
                        return !empty($item['detalle']);
                    });
                    DB::table('interconsultas')->insert($interconsultas);

                    // Insertar apariencia personal
                    $aparienciaPersonal = array_filter([
                        'id_historia' => $idHistoria,
                        'edad' => $request['edad'] ?? null,
                        'otro_edad' => $request['edad_otro'] ?? null,
                        'desarrollo_pondoestatural' => $request['desarrollo'] ?? null,
                        'otro_desarrollo_pondoestatural' => $request['desarrollo_otro'] ?? null,
                        'aseo_y_arreglo' => $request['aseo'] ?? null,
                        'otro_aseo_y_arreglo' => $request['aseo_otro'] ?? null,
                        'salud_somatica' => $request['salud'] ?? null,
                        'otro_salud_somatica' => $request['salud_otro'] ?? null,
                        'facies' => $request['facies'] ?? null,
                        'otro_facies' => $request['facies_otro'] ?? null,
                        'biotipo' => $request['biotipo'] ?? null,
                        'otro_biotipo' => $request['biotipo_otro'] ?? null,
                        'actitud' => $request['actitud'] ?? null,
                        'otro_actitud' => $request['actitud_otro'] ?? null,
                    ]);
                    DB::table('apariencia_personal')->insert($aparienciaPersonal);

                    // Insertar funciones somáticas
                    $funcionesSomaticas = array_filter([
                        'id_historia' => $idHistoria,
                        'consciencia' => $request['consciencia'] ?? null,
                        'otro_consciencia' => $request['consciencia_otro'] ?? null,
                        'orientacion' => $request['orientacion'] ?? null,
                        'otro_orientacion' => $request['orientacion_otro'] ?? null,
                        'memoria' => $request['memoria'] ?? null,
                        'otro_memoria' => $request['memoria_otro'] ?? null,
                        'atencion' => $request['atencion'] ?? null,
                        'otro_atencion' => $request['atencion_otro'] ?? null,
                        'concentracion' => $request['concentracion'] ?? null,
                        'otro_concentracion' => $request['concentracion_otro'] ?? null,
                        'lenguaje' => $request['lenguaje'] ?? null,
                        'otro_lenguaje' => $request['lenguaje_otro'] ?? null,
                        'pensamiento' => $request['pensamiento'] ?? null,
                        'otro_pensamiento' => $request['pensamiento_otro'] ?? null,
                        'afecto' => $request['afecto'] ?? null,
                        'otro_afecto' => $request['afecto_otro'] ?? null,
                        'sensopercepcion' => $request['sensopercepcion'] ?? null,
                        'otro_sensopercepcion' => $request['sensopercepcion_otro'] ?? null,
                        'psicomotricidad' => $request['psicomotricidad'] ?? null,
                        'otro_psicomotricidad' => $request['psicomotricidad_otro'] ?? null,
                        'juicio' => $request['juicio'] ?? null,
                        'otro_juicio' => $request['juicio_otro'] ?? null,
                        'inteligencia' => $request['inteligencia'] ?? null,
                        'otro_inteligencia' => $request['inteligencia_otro'] ?? null,
                        'conciencia_de_enfermedad' => $request['conciencia_enfermedad'] ?? null,
                        'otro_conciencia_de_enfermedad' => $request['conciencia_enfermedad_otro'] ?? null,
                        'sufrimiento_psicologico' => $request['sufrimiento_psicologico'] ?? null,
                        'otro_sufrimiento_psicologico' => $request['sufrimiento_psicologico_otro'] ?? null,
                        'motivacion_al_tratamiento' => $request['motivacion_tratamiento'] ?? null,
                        'otro_motivacion_al_tratamiento' => $request['motivacion_tratamiento_otro'] ?? null
                    ]);
                    DB::table('funciones_cognitivas')->insert($funcionesSomaticas);


                      // Insertar Funciones Somáticas
                    $examenMental = array_filter([
                        'id_historia' => $idHistoria,
                        'ciclos_del_sueno' => $request['ciclos_sueno'],
                        'apetito' => $request['apetito'],
                        'actividades_autocuidado' => $request['autocuidado'],
                    ]);
                    DB::table('funciones_somaticas')->insert($examenMental);
                    // Confirmar transacción
                    DB::commit();
                    return  $idHistoria;
                } catch (\Exception $e) {
                    // Revertir transacción en caso de error
                    DB::rollBack();
                    throw $e;
                }
            } else {
                // Lógica de actualización
                $respuesta = DB::connection('mysql')->table('especialidades')
                    ->where('id', $request['idRegistro'])  // Identificar el registro a actualizar
                    ->update([
                        'nombre' => $request['nombre'],
                        'observacion' => $request['observaciones'] ?? '',
                    ]);
                $respuesta = $request['idRegistro'];
            }
        } catch (Exception $e) {
            // Manejo del error
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al procesar el formulario: ' . $e->getMessage(),
            ], 500);
        }
        
    }

    public static function busquedaHistoria($idHisto){
        return DB::connection('mysql')->table('historia_clinica')
        ->where("id", $idHisto)
        ->first();
    }
}
