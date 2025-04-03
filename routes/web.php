<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuariosController;
use App\Http\Controllers\PacientesController;
use App\Http\Controllers\AdminitraccionController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\HistoriasController;
use App\Http\Controllers\HistoriaNeuroPsicologicaController;
use App\Models\HistoriaNeuroPsicologica;

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
})->name('home');

///INICIO DE SESIÓN
Route::post('/Login', [UsuariosController::class, 'Login'])->name('login');
Route::get('/Administracion', [UsuariosController::class,'Administracion'])->name('inicio');
Route::get('/Logout', [UsuariosController::class,'Logout']);
Route::get('/Administracion/perfil', [UsuariosController::class,'perfil']);
Route::post('/Administracion/VerificarUsuario', [UsuariosController::class,'VerificarUsuarioPerfil']);
Route::post('/Administracion/UpdatePerfil', [UsuariosController::class,'UpdatePerfil']);


///GESTIONAR PACIENTES
Route::middleware(['auth', 'permission:paciente'])->group(function () {
    Route::get('/Pacientes/Gestionar', [PacientesController::class, 'Pacientes'])->name('pacientes.listaPacientes');
});
Route::post('/pacientes/listaPacientes', [PacientesController::class, 'listaPacientes'])->name('pacientes.listaPacientes');
Route::get('/pacientes/ocupaciones', [PacientesController::class, 'ocupaciones'])->name('pacientes.ocupaciones');
Route::post('/pacientes/municipio', [PacientesController::class, 'municipios'])->name('pacientes.municipio');
Route::get('/pacientes/departamentos', [PacientesController::class, 'departamentos'])->name('pacientes.departamentos');
Route::get('/pacientes/tipoUSuario', [PacientesController::class, 'tipoUSuario'])->name('pacientes.tipoUSuario');
Route::get('/pacientes/eps', [PacientesController::class, 'eps'])->name('pacientes.eps');
Route::post('/verificar-identificacion', [PacientesController::class, 'verificarIdentPaciente']);
Route::post('/pacientes/guardar', [PacientesController::class, 'guardarPaciente'])->name('form.guardarPaciente');
Route::post('/pacientes/buscaPaciente', [PacientesController::class, 'busquedaPaciente'])->name('pacientes.buscaPaciente');
Route::post('/pacientes/eliminarPac', [PacientesController::class, 'eliminarPaciente'])->name('pacientes.eliminarPac');
Route::get('/pacientes/historiaPsicologica', [PacientesController::class,'historiaPsicologica']);
Route::get('/pacientes/historiaNeuropsicologica', [PacientesController::class,'historiaNeuropsicologica']);
Route::get('/pacientes/consultas', [PacientesController::class,'consultas'])->name('pacientes.consultas');
Route::post('/pacientes/listaVentaServiciosPacientes', [PacientesController::class,'listaVentaServiciosPacientes'])->name('pacientes.listaVentaServiciosPacientes');
Route::post('/pacientes/buscaServicioVenta', [PacientesController::class,'buscaServicioVenta'])->name('pacientes.buscaServicioVenta');
Route::post('/pacientes/eliminarServicioVenta', [PacientesController::class,'eliminarServicioVenta'])->name('pacientes.eliminarServicioVenta');
Route::get('/pacientes/sesiones', [PacientesController::class,'sesiones'])->name('pacientes.sesiones');
Route::get('/pacientes/paquetes', [PacientesController::class,'paquetes'])->name('pacientes.paquetes');
Route::get('/pacientes/pruebas', [PacientesController::class,'pruebas'])->name('pacientes.pruebas');

////ADMINISTRACCION
///GESTIONAR ESPECIALIDADES
Route::middleware(['auth', 'permission:AdminMotivoConsulta'])->group(function () {
    Route::get('/Administracion/Especialidades', [AdminitraccionController::class, 'Especialidades']);
});
Route::post('/especialidad/guardar', [AdminitraccionController::class, 'guardarEspecialidad'])->name('form.guardarEspecialidad');
Route::post('/especialidad/listaEspecialidades', [AdminitraccionController::class, 'listaEspecialidades'])->name('especialidades.listaEspecialidades');
Route::post('/especialidad/buscaEspecialidad', [AdminitraccionController::class, 'busquedaEspecialidad'])->name('especialidades.buscaEspecialidad');
Route::post('/especialidad/eliminarEspecialidad', [AdminitraccionController::class, 'eliminarEspecialidad'])->name('especialidades.eliminarEspecialidad');

///GESTIONAR PROFESIONALES
Route::middleware(['auth', 'permission:adminProfesionales'])->group(function () {
    Route::get('/Administracion/Profesionales', [AdminitraccionController::class, 'Profesionales']);
});

Route::post('/profesionales/listaProfesionales', [AdminitraccionController::class, 'listaProfesionales'])->name('profesionales.listaProfesionales');
Route::post('/verificar-identificacion-profesional', [AdminitraccionController::class, 'verificarIdentProfesional']);
Route::post('/profesional/guardar', [AdminitraccionController::class, 'guardarProfesional'])->name('form.guardarProfesional');
Route::post('/verificar-usuario', [UsuariosController::class, 'verificarUsuario']);
Route::post('/profesional/buscaProfesional', [AdminitraccionController::class, 'busquedaProfesional'])->name('profesionales.buscaProfesional');
Route::post('/profesional/eliminarProf', [AdminitraccionController::class, 'eliminarProfesional'])->name('profesionales.eliminarProf');

///GESTIONAR CUPS
Route::middleware(['auth', 'permission:AdminCUPS'])->group(function () {
    Route::get('/Administracion/CUPS', [AdminitraccionController::class, 'CUPS']);
});
Route::post('/verificar-codigo-cups', [AdminitraccionController::class, 'verificarCodigoCUPS']);
Route::post('/cups/guardar', [AdminitraccionController::class, 'guardarCUPS'])->name('form.guardarCUPS');
Route::post('/cups/buscaCUPS', [AdminitraccionController::class, 'buscaCUPS'])->name('cups.buscaCUPS');
Route::post('/cups/eliminarCUPS', [AdminitraccionController::class, 'eliminarCUPS'])->name('cups.eliminarCUPS');
Route::post('/cups/listaCUPS', [AdminitraccionController::class, 'listaCUPS'])->name('cups.listaCUPS');

///GESTIONAR CIE10
Route::middleware(['auth', 'permission:AdminCIE10'])->group(function () {
    Route::get('/Administracion/CIE10', [AdminitraccionController::class, 'CIE10']);
});
Route::post('/verificar-codigo-cie10', [AdminitraccionController::class, 'verificarCodigoCIE10']);
Route::post('/cie10/guardar', [AdminitraccionController::class, 'guardarCIE10'])->name('form.guardarCIE10');
Route::post('/cie10/buscaCIE10', [AdminitraccionController::class, 'buscaCIE10'])->name('cie10.buscaCIE10');
Route::post('/cie10/eliminarCIE10', [AdminitraccionController::class, 'eliminarCIE10'])->name('cie10.eliminarCIE10');
Route::post('/cie10/listaCIE10', [AdminitraccionController::class, 'listaCIE10'])->name('cie10.listaCIE10');





///GESTIONAR ENTIDADES
Route::middleware(['auth', 'permission:Admineps'])->group(function () {
    Route::get('/Administracion/Entidades', [AdminitraccionController::class, 'Entidades']);
});
Route::post('/entidades/guardar', [AdminitraccionController::class, 'guardarEntidades'])->name('form.guardarEntidades');
Route::post('/entidades/listaEntidades', [AdminitraccionController::class, 'listaEntidades'])->name('entidades.listaEntidades');
Route::post('/entidades/buscaEntidad', [AdminitraccionController::class, 'buscaEntidad'])->name('entidades.buscaEntidad');
Route::post('/entidades/eliminarEntidad', [AdminitraccionController::class, 'eliminarEntidad'])->name('entidades.eliminarEntidad');
Route::post('/verificar-codigo-entidad', [AdminitraccionController::class, 'verificarCodigoEntidad']);

///GESTIONAR PAQUETES
Route::middleware(['auth', 'permission:Admineps'])->group(function () {
    Route::get('/Administracion/Paquetes', [AdminitraccionController::class, 'Paquetes']);
});
Route::post('/paquetes/listaPaquetes', [AdminitraccionController::class, 'listaPaquetes'])->name('paquetes.listaPaquetes');
Route::post('/paquetes/guardar', [AdminitraccionController::class, 'guardarPaquete'])->name('form.guardarPaquete');
Route::post('/paquetes/buscarPaquete', [AdminitraccionController::class, 'buscarPaquete'])->name('paquetes.buscarPaquete');
Route::post('/paquetes/eliminarPaqueteLista', [AdminitraccionController::class, 'eliminarPaquete'])->name('paquetes.eliminarPaqueteLista');

///GESTIONAR PRUEBAS
Route::middleware(['auth', 'permission:AdminPruebas'])->group(function () {
    Route::get('/Administracion/Pruebas', [AdminitraccionController::class, 'Pruebas']);
});
Route::post('/pruebas/listaPruebas', [AdminitraccionController::class, 'listaPruebas'])->name('pruebas.listaPruebas');
Route::post('/pruebas/guardar', [AdminitraccionController::class, 'guardarPrueba'])->name('form.guardarPrueba');
Route::post('/pruebas/buscarPrueba', [AdminitraccionController::class, 'buscarPrueba'])->name('pruebas.buscarPrueba');
Route::post('/pruebas/eliminarPruebaLista', [AdminitraccionController::class, 'eliminarPrueba'])->name('pruebas.eliminarPruebaLista');


///GESTIONAR SESIONES
Route::middleware(['auth', 'permission:AdminSesiones'])->group(function () {
    Route::get('/Administracion/Sesiones', [AdminitraccionController::class, 'Sesiones']);
});
Route::post('/sesiones/listaSesiones', [AdminitraccionController::class, 'listaSesiones'])->name('sesiones.listaSesiones');
Route::post('/sesiones/guardarSesion', [AdminitraccionController::class, 'guardarSesion'])->name('sesiones.guardarSesion');
Route::post('/sesiones/buscarSesion', [AdminitraccionController::class, 'buscarSesion'])->name('sesiones.buscarSesion');
Route::post('/sesiones/eliminarSesion', [AdminitraccionController::class, 'eliminarSesion'])->name('sesiones.eliminarSesion');

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
Route::post('/citas/notificarPaciente', [AgendaController::class, 'notificaccionCita'])->name('citas.notificarPaciente');
Route::post('/citas/eliminarcita', [AgendaController::class, 'eliminarcita'])->name('citas.eliminarcita');
Route::post('/citas/listaCitasEstado', [AgendaController::class, 'listaCitasEstado'])->name('citas.listaCitasEstado');
Route::post('/citas/listaCitasProfesional', [AgendaController::class, 'listaCitasProfesional'])->name('citas.listaCitasProfesional');



/// HISTORIAS CLINICAS
Route::middleware(['auth', 'permission:histPsicologia'])->group(function () {
    Route::get('/HistoriasClinicas/GestionarHistoriaPsicologia', [HistoriasController::class, 'historiaPsicologia'])->name('gestionar.psicologia');
});
Route::post('/pacientes/listaPacientesModal', [PacientesController::class, 'listaPacientesModal'])->name('pacientes.listaPacientesModal');
Route::post('/pacientes/buscaPacienteHistoria', [PacientesController::class, 'buscaPacienteHistoria'])->name('pacientes.buscaPacienteHistoria');
Route::get('/historia/buscaCUPS', [HistoriasController::class, 'buscaCUPS'])->name('historia.buscaCUPS');
Route::get('/historia/buscaCIE', [HistoriasController::class, 'buscaCIE'])->name('historia.buscaCIE');
Route::post('/historia/guardarHistoriaPsicologica', [HistoriasController::class, 'guardarHistoriaPsicologica'])->name('form.guardarHistoriaPsicologica');
Route::get('/hitoriaPsicologica/categorias', [HistoriasController::class, 'obtenerOpcionesHCP'])->name('hitoriaPsicologica.categorias');
Route::post('/HistoriasClinicas/listaHistoriasPsicologica', [HistoriasController::class, 'listaHistoriasPsicologica'])->name('HistoriasClinicas.listaHistoriasPsicologica');
Route::post('/historia/buscaHistoriaPsicologica', [HistoriasController::class, 'buscaHistoriaPsicologica'])->name('historia.buscaHistoriaPsicologica');
Route::post('/historia/buscaProfesionalHistoria', [HistoriasController::class, 'buscaProfesionalHistoria'])->name('historia.buscaProfesionalHistoria');
Route::post('/historia/cerrarHistoria', [HistoriasController::class, 'cerrarHistoria'])->name('historia.cerrarHistoria');
Route::post('/historia/notasHistoria', [HistoriasController::class, 'notasHistoria'])->name('historia.notasHistoria');
Route::get('/historia/imprimirHistoria', [HistoriasController::class, 'imprimirHistoria'])->name('historia.imprimirHistoria');
Route::post('/historia/eliminarHistoria', [HistoriasController::class, 'eliminarHistoria'])->name('historia.eliminarHistoria');
Route::post('/historia/buscaPlanIntervencion', [HistoriasController::class, 'buscaPlanIntervencion'])->name('historia.buscaPlanIntervencion');
Route::post('/historia/guardarPlanIntervencion', [HistoriasController::class, 'guardarPlanIntervencion'])->name('form.guardarPlanIntervencion');

///GESTIONAR VENTAS DE PAQUETE
Route::post('/paquetes/listaPaquetesModal', [HistoriasController::class, 'listaPaquetesModal'])->name('paquetes.listaPaquetesModal');
Route::get('/paquetes/listaPaquetesSel', [HistoriasController::class, 'listaPaquetesSel'])->name('paquetes.listaPaquetesSel');
Route::post('/paquetes/guardarPaqueteVenta', [HistoriasController::class, 'guardarPaqueteVenta'])->name('form.guardarPaqueteVenta');
Route::post('/paquetes/buscaPaqueteVenta', [HistoriasController::class, 'buscaPaqueteVenta'])->name('paquetes.buscaPaqueteVenta');
Route::post('/paquetes/eliminarPaquete', [HistoriasController::class, 'eliminarPaquete'])->name('paquetes.eliminarPaquete');

///GESTIONAR VENTAS DE PRUEBA
Route::post('/pruebas/listaPruebasModal', [HistoriaNeuroPsicologicaController::class, 'listaPruebasModal'])->name('pruebas.listaPruebasModal');
Route::get('/pruebas/listaPruebasSel', [HistoriaNeuroPsicologicaController::class, 'listaPaquetesSel'])->name('pruebas.listaPruebasSel');
Route::post('/pruebas/guardarPruebaVenta', [HistoriaNeuroPsicologicaController::class, 'guardarPruebaVenta'])->name('form.guardarPruebaVenta');
Route::post('/pruebas/buscaPruebaVenta', [HistoriaNeuroPsicologicaController::class, 'buscaPruebaVenta'])->name('pruebas.buscaPruebaVenta');
Route::post('/pruebas/eliminarPrueba', [HistoriaNeuroPsicologicaController::class, 'eliminarPrueba'])->name('pruebas.eliminarPrueba');

/// GESTIONAR CONSULTAS
Route::post('/historia/guardarConsultaPsicologica', [HistoriasController::class, 'guardarConsultaPsicologica'])->name('form.guardarConsultaPsicologica');
Route::post('/historia/listaConsultasModal', [HistoriasController::class, 'listaConsultasModal'])->name('historia.listaConsultasModal');
Route::post('/historia/buscaConsultaPsicologica', [HistoriasController::class, 'buscaConsultaPsicologica'])->name('historia.buscaConsultaPsicologica');
Route::post('/historia/eliminarConsulta', [HistoriasController::class, 'eliminarConsulta'])->name('historia.eliminarConsulta');
Route::post('/informes/imprimirConsulta', [HistoriasController::class, 'imprimirConsulta'])->name('informes.imprimirConsulta');
Route::post('/informes/enviarConsulta', [HistoriasController::class, 'enviarConsulta'])->name('informes.enviarConsulta');

/// HISTORIAS CLINICAS NEURO
Route::middleware(['auth', 'permission:histNeuro'])->group(function () {
    Route::get('/HistoriasClinicas/GestionarHistoriaNeuroPsicologia', [HistoriaNeuroPsicologicaController::class, 'historiaNeuroPsicologia'])->name('gestionar.historiaNeuroPsicologia');
});
Route::post('/historia/guardarHistoriaNeuroPsicologica', [HistoriaNeuroPsicologicaController::class, 'guardarHistoriaNeuroPsicologica'])->name('form.guardarHistoriaNeuroPsicologica');
Route::post('/HistoriasClinicas/listaHistoriasNeuroPsicologica', [HistoriaNeuroPsicologicaController::class, 'listaHistoriasNeuroPsicologica'])->name('HistoriasClinicas.listaHistoriasNeuroPsicologica');
Route::post('/pacientes/buscaPacienteHistoriaNeuro', [PacientesController::class, 'buscaPacienteHistoriaNeuro'])->name('pacientes.buscaPacienteHistoriaNeuro');
Route::post('/historia/buscaHistoriaNeuroPsicologica', [HistoriaNeuroPsicologicaController::class, 'buscaHistoriaNeuroPsicologica'])->name('historia.buscaHistoriaNeuroPsicologica');
Route::get('/historia/imprimirHistoriaNeuro', [HistoriaNeuroPsicologicaController::class, 'imprimirHistoria'])->name('historia.imprimirHistoriaNeuro');
Route::post('/historia/cerrarHistoriaNeuro', [HistoriaNeuroPsicologicaController::class, 'cerrarHistoriaNeuro'])->name('historia.cerrarHistoriaNeuro');
Route::post('/historia/eliminarHistoriaNeuro', [HistoriaNeuroPsicologicaController::class, 'eliminarHistoriaNeuro'])->name('historia.eliminarHistoriaNeuro');
Route::post('/historia/buscaPlanIntervencionNeuro', [HistoriaNeuroPsicologicaController::class, 'buscaPlanIntervencionNeuro'])->name('historia.buscaPlanIntervencionNeuro');
Route::post('/historia/guardarPlanIntervencionNeuro', [HistoriaNeuroPsicologicaController::class, 'guardarPlanIntervencionNeuro'])->name('form.guardarPlanIntervencionNeuro');


/// GESTIONAR CONSULTAS NEURO
Route::post('/historia/guardarConsultaNeuroPsicologica', [HistoriaNeuroPsicologicaController::class, 'guardarConsultaNeuroPsicologica'])->name('form.guardarConsultaNeuroPsicologica');
Route::post('/historia/buscaConsultaNeuroPsicologica', [HistoriaNeuroPsicologicaController::class, 'buscaConsultaNeuroPsicologica'])->name('historia.buscaConsultaNeuroPsicologica');
Route::post('/historia/listaConsultasModalNeuro', [HistoriaNeuroPsicologicaController::class, 'listaConsultasModalNeuro'])->name('historia.listaConsultasModalNeuro');
Route::post('/historia/eliminarConsultaNeuro', [HistoriaNeuroPsicologicaController::class, 'eliminarConsultaNeuro'])->name('historia.eliminarConsultaNeuro');
Route::post('/informes/imprimirConsultaNeuro', [HistoriaNeuroPsicologicaController::class, 'imprimirConsultaNeuro'])->name('informes.imprimirConsultaNeuro');
Route::post('/informes/enviarConsultaNeuro', [HistoriaNeuroPsicologicaController::class, 'enviarConsultaNeuro'])->name('informes.enviarConsultaNeuro');

/// GESTIONAR USUARIOS
Route::middleware(['auth', 'permission:gestionUsuarios'])->group(function () {
    Route::get('/Administracion/Usuarios', [AdminitraccionController::class, 'Usuarios']);
});

Route::post('/AdminUsuario/listaUsuarios', [UsuariosController::class, 'listaUsuarios'])->name('usuarios.listaUsuarios');
Route::post('/verificar-usuario', [UsuariosController::class, 'verificarUsuario']);
Route::post('/AdminUsuario/guardar', [UsuariosController::class, 'guardarUsuario'])->name('form.guardarUsusario');
Route::post('/AdminUsuario/guardarPerfil', [UsuariosController::class, 'guardarPerfil'])->name('form.guardarPerfil');
Route::post('/AdminUsuario/buscaUsuario', [UsuariosController::class, 'busquedaUsuario'])->name('usuario.buscaUsuario');
Route::post('/AdminUsuario/buscaPerfil', [UsuariosController::class, 'buscaPerfil'])->name('usuario.buscaPerfil');
Route::post('/AdminUsuario/eliminarUsuario', [UsuariosController::class, 'eliminarUsuario'])->name('usuario.eliminarUsuario');
Route::post('/AdminUsuario/eliminarPerfil', [UsuariosController::class, 'eliminarPerfil'])->name('usuario.eliminarPerfil');
Route::middleware(['auth', 'permission:gestionPerfiles'])->group(function () {
    Route::get('/Administracion/Perfiles', [AdminitraccionController::class, 'Perfiles']);
});
Route::post('/AdminUsuario/listaPerfiles', [UsuariosController::class, 'listaPerfiles'])->name('usuario.listaPerfiles');
Route::get('/Administracion/buscaListPerfiles', [UsuariosController::class, 'buscaListPerfiles'])->name('usuario.buscaListPerfiles');

Route::middleware(['auth', 'permission:gestionLog'])->group(function () {
    Route::get('/Administracion/Logs', [AdminitraccionController::class, 'Logs']);
});

Route::post('/AdminUsuario/listaLogs', [UsuariosController::class, 'listaLogs'])->name('AdminUsuario.listaLogs');

// INFORMES PSICOLOGIA
Route::middleware(['auth', 'permission:informePsicologico'])->group(function () {
    Route::get('/HistoriasClinicas/InformePsicologia', [HistoriasController::class, 'informePsicologia'])->name('gestionar.informePsicologia');
});

Route::post('/informes/psicologia', [HistoriasController::class, 'listaPacientesInformePsicologia'])->name('informes.psicologia');
Route::post('/informes/verHistorial', [HistoriasController::class, 'verHistorialEvoluciones'])->name('informes.verHistorial');
Route::post('/informes/buscaEvolucionPsicologica', [HistoriasController::class, 'buscaEvolucionPsicologica'])->name('informes.buscaEvolucionPsicologica');
Route::post('/informes/imprimirInformePsicologia', [HistoriasController::class, 'imprimirInformePsicologia'])->name('informes.imprimirInformePsicologia');
Route::post('/informes/buscaHistoriaPsicologicaInforme', [HistoriasController::class, 'buscaHistoriaPsicologicaInforme'])->name('informes.buscaHistoriaPsicologicaInforme');
Route::post('/informes/guardarInformePsicologica', [HistoriasController::class, 'guardarInformePsicologica'])->name('form.guardarInformePsicologica');
Route::post('/informes/informePsicologia', [HistoriasController::class, 'informePsicologiaList'])->name('informes.informePsicologia');
Route::post('/informes/buscaInformePsicologica', [HistoriasController::class, 'buscaInformePsicologica'])->name('informes.buscaInformePsicologica');
Route::post('/informes/eliminarInforme', [HistoriasController::class, 'eliminarInforme'])->name('informes.eliminarInforme');

// INFORMES NEUROPSICOLOGIA
Route::middleware(['auth', 'permission:informeNeuro'])->group(function () {
    Route::get('/HistoriasClinicas/InformeNeuropsicologico', [HistoriaNeuroPsicologicaController::class, 'informePsicologia'])->name('gestionar.InformeNeuropsicologico');
});
Route::post('/informes/neuropsicologia', [HistoriaNeuroPsicologicaController::class, 'listaPacientesInformeNeuropsicologia'])->name('informes.neuropsicologia');
Route::post('/informes/informeNeuropsicologia', [HistoriaNeuroPsicologicaController::class, 'informeNeuropsicologiaList'])->name('informes.informeNeuropsicologia');
Route::post('/informes/buscaInformeNeuropsicologica', [HistoriaNeuroPsicologicaController::class, 'buscaInformeNeuropsicologica'])->name('informes.buscaInformeNeuropsicologica');
Route::post('/informes/eliminarInformeNeuro', [HistoriaNeuroPsicologicaController::class, 'eliminarInformeNeuro'])->name('informes.eliminarInformeNeuro');
Route::post('/informes/guardarInformeNeuropsicologica', [HistoriaNeuroPsicologicaController::class, 'guardarInformeNeuropsicologica'])->name('form.guardarInformeNeuropsicologica');
Route::post('/informes/buscarAnexosInforme', [HistoriaNeuroPsicologicaController::class, 'buscarAnexosInforme'])->name('informes.buscarAnexosInforme');
Route::post('/informes/eliminarAnexoInforme', [HistoriaNeuroPsicologicaController::class, 'eliminarAnexoInforme'])->name('informes.eliminarAnexoInforme');


// INFORMES GENERALES
Route::get('/HistoriasClinicas/informes', [HistoriasController::class, 'informes'])->name('gestionar.informes');
Route::post('/informes/informeGeneral', [HistoriasController::class, 'informeGeneral'])->name('informes.informeGeneral');
Route::post('/informes/otrosInformes', [HistoriasController::class, 'otrosInformes'])->name('informes.otrosInformes');

//GESTIONNAR RECAUDOS
Route::get('/Administracion/Recaudos', [AdminitraccionController::class, 'Recaudos']);
Route::post('/Administracion/listaVentasPacientes', [AdminitraccionController::class, 'listaVentasPacientes'])->name('Administracion.listaVentasPacientes');
Route::post('/Administracion/otraInformacionRecaudos', [AdminitraccionController::class, 'otraInformacionRecaudos'])->name('Administracion.otraInformacionRecaudos');
Route::post('/Administracion/listaVentasPacientesPagos', [AdminitraccionController::class, 'listaVentasPacientesPagos'])->name('Administracion.listaVentasPacientesPagos');
Route::post('/Administracion/detalleVentaServicioPaciente', [AdminitraccionController::class, 'detalleVentaServicioPaciente'])->name('Administracion.detalleVentaServicioPaciente');
Route::post('/Administracion/detalleVentaPagosPaciente', [AdminitraccionController::class, 'detalleVentaPagosPaciente'])->name('Administracion.detalleVentaPagosPaciente');
Route::post('/Administracion/guardar', [AdminitraccionController::class, 'guardarPagoVenta'])->name('form.guardarPagoVenta');
Route::post('/Administracion/eliminarPagoRecaudo', [AdminitraccionController::class, 'eliminarPagoRecaudo'])->name('Administracion.eliminarPagoRecaudo');
Route::post('/Administracion/imprimirRecaudo', [AdminitraccionController::class, 'imprimirRecaudo'])->name('Administracion.imprimirRecaudo');

//GESTIONAR GASTOS
Route::get('/Administracion/Gastos', [AdminitraccionController::class, 'Gastos']);
Route::post('/gastos/listaGastos', [AdminitraccionController::class, 'listaGastos'])->name('gastos.listaGastos');
Route::get('/gastos/listaCategorias', [AdminitraccionController::class, 'listaCategorias'])->name('gastos.listaCategorias');
Route::post('/gastos/guardarGastos', [AdminitraccionController::class, 'guardarGastos'])->name('form.guardarGastos');
Route::post('/gastos/guardarCategoria', [AdminitraccionController::class, 'guardarCategoria'])->name('gastos.guardarCategoria');
Route::post('/gastos/eliminarCategoria', [AdminitraccionController::class, 'eliminarCategoria'])->name('gastos.eliminarCategoria');
Route::post('/gastos/eliminarGasto', [AdminitraccionController::class, 'eliminarGasto'])->name('gastos.eliminarGasto');
Route::post('/gastos/buscarGasto', [AdminitraccionController::class, 'buscarGasto'])->name('gastos.buscarGasto');

//GESTIONAR CAJAS
Route::get('/Administracion/Cajas', [AdminitraccionController::class, 'Cajas']);
Route::post('/cajas/listaCajas', [AdminitraccionController::class, 'listaCajas'])->name('cajas.listaCajas');
Route::post('/cajas/guardarCaja', [AdminitraccionController::class, 'guardarCaja'])->name('cajas.guardarCaja');
Route::post('/cajas/detalleCaja', [AdminitraccionController::class, 'detalleCaja'])->name('cajas.detalleCaja');
Route::post('/cajas/cerrarCaja', [AdminitraccionController::class, 'cerrarCaja'])->name('cajas.cerrarCaja');
Route::post('/cajas/eliminarCaja', [AdminitraccionController::class, 'eliminarCaja'])->name('cajas.eliminarCaja');


//
Route::post('/historia/buscaVentaConsulta', [HistoriasController::class, 'buscaVentaConsulta'])->name('historia.buscaVentaConsulta');
Route::post('/historia/buscaVentaSesion', [HistoriasController::class, 'buscaVentaSesion'])->name('historia.buscaVentaSesion');
Route::post('/historia/guardarVentaConsulta', [HistoriasController::class, 'guardarVentaConsulta'])->name('form.guardarVentaConsulta');
Route::post('/historia/guardarVentaSesion', [HistoriasController::class, 'guardarVentaSesion'])->name('form.guardarVentaSesion');
Route::post('/historia/buscaSesionVenta', [HistoriasController::class, 'buscaSesionVenta'])->name('historia.buscaSesionVenta');
Route::post('/historia/eliminarSesionVenta', [HistoriasController::class, 'eliminarSesionVenta'])->name('historia.eliminarSesionVenta');


Route::post('/historiaNeuro/buscaVentaConsultaNeuro', [HistoriaNeuroPsicologicaController::class, 'buscaVentaConsultaNeuro'])->name('historiaNeuro.buscaVentaConsultaNeuro');
