<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\HistoriaPsicologica;
use App\Models\CategoriaHCP;
use App\Models\Pacientes;


class HistoriaNeuroPsicologicaController extends Controller
{
    public function historiaNeuroPsicologia()
    {
        if (Auth::check()) {
            return view('HistoriasClinica.Neuropsicologia');
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

}
