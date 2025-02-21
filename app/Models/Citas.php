<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Citas extends Model
{
    public static function AllCitas()
    {
        return DB::connection('mysql')->table('citas')
            ->join('pacientes', 'citas.paciente', 'pacientes.id')
            ->join('profesionales', 'citas.profesional', 'profesionales.id')
            ->selectRaw('citas.*, pacientes.primer_nombre, pacientes.primer_apellido, profesionales.nombre AS nomprof, "CITAS" AS tblo')
            ->where('citas.estado', '!=', 'Anulada')
            ->get();
    }

    public static function buscarCitas($fec1, $fec2){
        
        return DB::connection('mysql')->table('citas')
            ->join('pacientes', 'citas.paciente', 'pacientes.id')
            ->join('profesionales', 'citas.profesional', 'profesionales.id')
            ->selectRaw('citas.*, pacientes.primer_nombre, pacientes.primer_apellido, profesionales.id AS idprof, profesionales.nombre AS nomprof, "CITAS" AS tblo')
            ->whereBetween('citas.inicio', [$fec1, $fec2])
            ->get();
    }

    public static function infcitasEmail($idcita)
    {
        return DB::connection('mysql')->table('citas')
            ->leftJoin("especialidades", "especialidades.id", "citas.motivo")
            ->leftJoin('profesionales', 'citas.profesional', 'profesionales.id')
            ->leftJoin('pacientes', 'citas.paciente', 'pacientes.id')
            ->where('citas.id', $idcita)
            ->where('citas.estado', '!=', 'Anulada')
            ->select("citas.*", 
            "especialidades.nombre", 
            'profesionales.nombre AS nomprof',
            'pacientes.primer_nombre AS npaciente', 
            'pacientes.primer_apellido AS apaciente', 
            'pacientes.email')
            ->first();
    }

    public static function CitasProfesional($idProf, $idCita)
    {

        $citas = DB::connection('mysql')->table('citas')
            ->join('pacientes', 'citas.paciente', 'pacientes.id')
            ->selectRaw('citas.*, pacientes.primer_nombre, pacientes.primer_apellido, "CITAS" AS tblo')
            ->where('profesional', $idProf)
            ->where('citas.estado', '!=', 'Anulada');

        // Si $idCita no está vacío, excluye esa cita
        if (!empty($idCita)) {
            $citas->where('citas.id', '!=', $idCita);
        }

        return $citas->get();
    }

    public static function GuardarComentario($request)
    {
        $respuesta = DB::connection('mysql')->table('citas')->where('id', $request['idCita'])->update([
            'comentario' => $request['comentario'],
        ]);
        return "ok";
    }

    public static function CambioEstadocita($idCita, $estado)
    {
        $respuesta = DB::connection('mysql')->table('citas')->where('id', $idCita)->update([
            'estado' => $estado,
        ]);
        return "ok";
    }

    public static function buscaDetCitas($idCita)
    {
        $respuesta =  DB::connection('mysql')->table('citas')
            ->leftJoin('profesionales', 'citas.profesional', 'profesionales.id')
            ->leftJoin('especialidades', 'especialidades.id', 'citas.motivo')
            ->select('citas.*', 'profesionales.nombre AS nomprof', 'especialidades.nombre AS nespec')
            ->where('citas.id', $idCita)
            ->first();
        return $respuesta;
    }

    public static function GuardarCitas($request)
    {
        $respuesta = DB::connection('mysql')->table('citas')->insertGetId([
            'paciente' => $request['idPaciente'],
            'profesional' => $request['profesional'],
            'motivo' => $request['especialidad'],
            'inicio' => $request['fechaHoraInicio'],
            'final' => $request['fechaHoraFinal'],
            'comentario' => $request['comentario'],
            'duracion' => $request['duracionCita'],
            'estado' => "Por atender"
        ]);

        return $respuesta;
    }

    public static function EditarCitas($request)
    {
        $respuesta = DB::connection('mysql')->table('citas')->where('id', $request['idCita'])->update([
            'profesional' => $request['profesional'],
            'motivo' => $request['especialidad'],
            'inicio' => $request['fechaHoraInicio'],
            'final' => $request['fechaHoraFinal'],
            'duracion' => $request['duracionCita'],
            'comentario' => $request['comentario']
        ]);
        return "ok";
    }
}
