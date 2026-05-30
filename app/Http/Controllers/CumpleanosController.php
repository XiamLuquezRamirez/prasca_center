<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pacientes;
use Illuminate\Support\Facades\Auth;

class CumpleanosController extends Controller
{
    /**
     * Muestra el panel de cumpleaños
     */
    public function panelCumpleanos()
    {
        $pacientesHoy = Pacientes::getPacientesCumpleanosHoy();
        $pacientesProximos = Pacientes::getPacientesCumpleanosProximos();
        $totalHoy = Pacientes::contarPacientesCumpleanosHoy();
        
        return view('Cumpleanos.panel', compact('pacientesHoy', 'pacientesProximos', 'totalHoy'));
    }

    /**
     * Obtiene los datos de cumpleaños para AJAX
     */
    public function getDatosCumpleanos()
    {
        $pacientesHoy = Pacientes::getPacientesCumpleanosHoy();
        $totalHoy = Pacientes::contarPacientesCumpleanosHoy();
        
        return response()->json([
            'success' => true,
            'pacientesHoy' => $pacientesHoy,
            'totalHoy' => $totalHoy
        ]);
    }

    /**
     * Obtiene los pacientes con cumpleaños próximos
     */
    public function getCumpleanosProximos()
    {
        $pacientesProximos = Pacientes::getPacientesCumpleanosProximos();
        
        return response()->json([
            'success' => true,
            'pacientesProximos' => $pacientesProximos
        ]);
    }
}
