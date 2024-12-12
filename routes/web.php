<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuariosController;
use App\Http\Controllers\PacientesController;
use App\Http\Controllers\AdminitraccionController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\HistoriasController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('Usuario.login');
});

///INICIO DE SESIÓN
Route::post('/Login', [UsuariosController::class,'Login']);
Route::get('/Administracion', [UsuariosController::class,'Administracion'])->name('inicio');
Route::get('/Logout', [UsuariosController::class,'Logout']);


///GESTIONAR PACIENTES
Route::get('/Pacientes/Gestionar', [PacientesController::class,'Pacientes']);
Route::post('/pacientes/listaPacientes', [PacientesController::class, 'listaPacientes'])->name('pacientes.listaPacientes');
Route::get('/pacientes/ocupaciones', [PacientesController::class, 'ocupaciones'])->name('pacientes.ocupaciones');
Route::post('/pacientes/municipio', [PacientesController::class, 'municipios'])->name('pacientes.municipio');
Route::get('/pacientes/departamentos', [PacientesController::class, 'departamentos'])->name('pacientes.departamentos');
Route::post('/verificar-identificacion', [PacientesController::class, 'verificarIdentPaciente']);
Route::post('/pacientes/guardar', [PacientesController::class, 'guardarPaciente'])->name('form.guardarPaciente');
Route::post('/pacientes/buscaPaciente', [PacientesController::class, 'busquedaPaciente'])->name('pacientes.buscaPaciente');
Route::post('/pacientes/eliminarPac', [PacientesController::class, 'eliminarPaciente'])->name('pacientes.eliminarPac');

////ADMINISTRACCION
///GESTIONAR ESPECIALIDADES
Route::get('/Administracion/Especialidades', [AdminitraccionController::class,'Especialidades']);
Route::post('/especialidad/guardar', [AdminitraccionController::class, 'guardarEspecialidad'])->name('form.guardarEspecialidad');
Route::post('/especialidad/listaEspecialidades', [AdminitraccionController::class, 'listaEspecialidades'])->name('especialidades.listaEspecialidades');
Route::post('/especialidad/buscaEspecialidad', [AdminitraccionController::class, 'busquedaEspecialidad'])->name('especialidades.buscaEspecialidad');
Route::post('/especialidad/eliminarEsp', [AdminitraccionController::class, 'eliminarEspecialidad'])->name('especialidades.eliminarEsp');

///GESTIONAR PROFESIONALES
Route::get('/Administracion/Profesionales', [AdminitraccionController::class,'Profesionales']);
Route::post('/profesionales/listaProfesionales', [AdminitraccionController::class, 'listaProfesionales'])->name('profesionales.listaProfesionales');
Route::post('/verificar-identificacion-profesional', [AdminitraccionController::class, 'verificarIdentProfesional']);
Route::post('/profesional/guardar', [AdminitraccionController::class, 'guardarProfesional'])->name('form.guardarProfesional');
Route::post('/verificar-usuario', [UsuariosController::class, 'verificarUsuario']);
Route::post('/profesional/buscaProfesional', [AdminitraccionController::class, 'busquedaProfesional'])->name('profesionales.buscaProfesional');
Route::post('/profesional/eliminarProf', [AdminitraccionController::class, 'eliminarProfesional'])->name('profesionales.eliminarProf');

/// AGENDA
Route::post('/citas/agenda', [AgendaController::class, 'agenda'])->name('citas.agenda');
Route::get('profesionales/cargarListaProf', [AdminitraccionController::class, 'cargarListaProf'])->name('profesionales.cargarListaProf');
Route::get('/especialidad/cargarListaEsp', [AdminitraccionController::class, 'cargarListaEsp'])->name('especialidad.cargarListaEsp');
Route::post('/citas/disponibilidad', [AgendaController::class, 'disponibilidad'])->name('citas.disponibilidad');
Route::get('/pacientes/cargarListaPacientes', [AgendaController::class, 'cargarListaPacientes'])->name('pacientes.cargarListaEsp');
Route::post('/citas/guardar', [AgendaController::class, 'guardarCitas'])->name('form.guardarCita');
Route::post('/citas/informacionCita', [AgendaController::class, 'informacionCita'])->name('citas.informacionCita');
Route::post('/citas/cambiaEstadoCita', [AgendaController::class, 'CambioEstadocita'])->name('citas.cambiaEstadoCita');
Route::post('/citas/cargarComentario', [AgendaController::class, 'cargarComentario'])->name('citas.cargarComentario');
Route::post('/citas/guardarComentario', [AgendaController::class, 'GuardarComentario'])->name('citas.guardarComentario');

/// HISTORIAS CLINICAS
Route::get('/HistoriasClinicas/GestionarHistoriaPsicologia', [HistoriasController::class, 'historiaPsicologia'])->name('gestionar.psicologia');
Route::post('/pacientes/listaPacientesModal', [PacientesController::class, 'listaPacientesModal'])->name('pacientes.listaPacientesModal');
Route::post('/pacientes/buscaPacienteHistoria', [PacientesController::class, 'buscaPacienteHistoria'])->name('pacientes.buscaPacienteHistoria');
Route::get('/historia/buscaCUPS', [HistoriasController::class, 'buscaCUPS'])->name('historia.buscaCUPS');
Route::get('/historia/buscaCIE', [HistoriasController::class, 'buscaCIE'])->name('historia.buscaCIE');
Route::post('/historia/guardarHistoriaPsicologica', [HistoriasController::class, 'guardarHistoriaPsicologica'])->name('form.guardarHistoriaPsicologica');
Route::get('/hitoriaPsicologica/categorias', [HistoriasController::class, 'obtenerOpcionesHCP'])->name('hitoriaPsicologica.categorias');
Route::post('/HistoriasClinicas/listaHistoriasPsicologica', [HistoriasController::class, 'listaHistoriasPsicologica'])->name('HistoriasClinicas.listaHistoriasPsicologica');
Route::post('/historia/buscaHistoriaPsicologica', [HistoriasController::class, 'buscaHistoriaPsicologica'])->name('historia.buscaHistoriaPsicologica');


/// GESTIONAR USUARIOS
Route::get('/Administracion/Usuarios', [AdminitraccionController::class,'Usuarios']);
Route::post('/AdminUsuario/listaUsuarios', [UsuariosController::class, 'listaUsuarios'])->name('usuarios.listaUsuarios');
Route::post('/verificar-usuario', [UsuariosController::class, 'verificarUsuario']);
Route::post('/AdminUsuario/guardar', [UsuariosController::class, 'guardarUsuario'])->name('form.guardarUsusario');
Route::post('/AdminUsuario/buscaUsuario', [UsuariosController::class, 'busquedaUsuario'])->name('usuario.buscaUsuario');
Route::post('/AdminUsuario/eliminarUsuario', [UsuariosController::class, 'eliminarUsuario'])->name('usuario.eliminarUsuario');
