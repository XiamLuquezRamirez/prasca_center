<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuariosController;
use App\Http\Controllers\PacientesController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\HistoriasController;
use App\Http\Controllers\HistoriaNeuroPsicologicaController;
use App\Http\Controllers\CumpleanosController;
use App\Http\Controllers\ProfesionalController;
use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\RecaudosController;
use App\Http\Controllers\SistemaController;
use App\Http\Controllers\ContratosEpsController;
use App\Http\Controllers\AutorizacionesController;
use App\Models\HistoriaNeuroPsicologica;

// -------------------------------------------------------
// Rutas públicas (sin autenticación)
// -------------------------------------------------------
Route::get('/', function () {
    return view('Usuario.login');
})->name('home');

Route::post('/Login', [UsuariosController::class, 'Login'])->name('login');
Route::get('/Logout', [UsuariosController::class, 'Logout']);

// -------------------------------------------------------
// Rutas protegidas (requieren sesión iniciada)
// -------------------------------------------------------
Route::middleware(['auth'])->group(function () {

    ///INICIO DE SESIÓN / PERFIL
    Route::get('/Administracion', [UsuariosController::class, 'Administracion'])->name('inicio');
    Route::get('/Administracion/perfil', [UsuariosController::class, 'perfil']);
    Route::post('/Administracion/VerificarUsuario', [UsuariosController::class, 'VerificarUsuarioPerfil']);
    Route::post('/Administracion/UpdatePerfil', [UsuariosController::class, 'UpdatePerfil']);

    ///GESTIONAR PACIENTES
    Route::middleware(['permission:paciente'])->group(function () {
        Route::get('/Pacientes/Gestionar', [PacientesController::class, 'Pacientes'])->name('pacientes.gestionar');
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
    Route::get('/pacientes/historiaPsicologica', [PacientesController::class, 'historiaPsicologica']);
    Route::get('/pacientes/historiaNeuropsicologica', [PacientesController::class, 'historiaNeuropsicologica']);
    Route::get('/pacientes/consultas', [PacientesController::class, 'consultas'])->name('pacientes.consultas');
    Route::post('/pacientes/listaVentaServiciosPacientes', [PacientesController::class, 'listaVentaServiciosPacientes'])->name('pacientes.listaVentaServiciosPacientes');
    Route::post('/pacientes/buscaServicioVenta', [PacientesController::class, 'buscaServicioVenta'])->name('pacientes.buscaServicioVenta');
    Route::post('/pacientes/eliminarServicioVenta', [PacientesController::class, 'eliminarServicioVenta'])->name('pacientes.eliminarServicioVenta');
    Route::get('/pacientes/sesiones', [PacientesController::class, 'sesiones'])->name('pacientes.sesiones');
    Route::get('/pacientes/paquetes', [PacientesController::class, 'paquetes'])->name('pacientes.paquetes');
    Route::get('/pacientes/pruebas', [PacientesController::class, 'pruebas'])->name('pacientes.pruebas');
    Route::post('/pacientes/eliminarAnexo', [PacientesController::class, 'eliminarAnexo'])->name('pacientes.eliminarAnexo');

    ///GESTIONAR ESPECIALIDADES
    Route::middleware(['permission:AdminMotivoConsulta'])->group(function () {
        Route::get('/Administracion/Especialidades', [CatalogoController::class, 'Especialidades']);
    });
    Route::post('/especialidad/guardar', [CatalogoController::class, 'guardarEspecialidad'])->name('form.guardarEspecialidad');
    Route::post('/especialidad/listaEspecialidades', [CatalogoController::class, 'listaEspecialidades'])->name('especialidades.listaEspecialidades');
    Route::post('/especialidad/buscaEspecialidad', [CatalogoController::class, 'busquedaEspecialidad'])->name('especialidades.buscaEspecialidad');
    Route::post('/especialidad/eliminarEspecialidad', [CatalogoController::class, 'eliminarEspecialidad'])->name('especialidades.eliminarEspecialidad');

    ///GESTIONAR PROFESIONALES
    Route::middleware(['permission:adminProfesionales'])->group(function () {
        Route::get('/Administracion/Profesionales', [ProfesionalController::class, 'Profesionales']);
    });
    Route::post('/profesionales/listaProfesionales', [ProfesionalController::class, 'listaProfesionales'])->name('profesionales.listaProfesionales');
    Route::post('/verificar-identificacion-profesional', [ProfesionalController::class, 'verificarIdentProfesional']);
    Route::post('/profesional/guardar', [ProfesionalController::class, 'guardarProfesional'])->name('form.guardarProfesional');
    Route::post('/verificar-usuario', [UsuariosController::class, 'verificarUsuario']);
    Route::post('/profesional/buscaProfesional', [ProfesionalController::class, 'busquedaProfesional'])->name('profesionales.buscaProfesional');
    Route::post('/profesional/eliminarProf', [ProfesionalController::class, 'eliminarProfesional'])->name('profesionales.eliminarProf');

    ///GESTIONAR CUPS
    Route::middleware(['permission:AdminCUPS'])->group(function () {
        Route::get('/Administracion/CodigosConsultas', [CatalogoController::class, 'CUPS']);
    });
    Route::post('/verificar-codigo-cups', [CatalogoController::class, 'verificarCodigoCUPS']);
    Route::post('/cups/guardar', [CatalogoController::class, 'guardarCUPS'])->name('form.guardarCUPS');
    Route::post('/cups/buscaCUPS', [CatalogoController::class, 'buscaCUPS'])->name('cups.buscaCUPS');
    Route::post('/cups/eliminarCUPS', [CatalogoController::class, 'eliminarCUPS'])->name('cups.eliminarCUPS');
    Route::post('/cups/listaCUPS', [CatalogoController::class, 'listaCUPS'])->name('cups.listaCUPS');
    Route::post('/cups/servicios-habilitados/lista', [CatalogoController::class, 'listaServiciosHabilitados'])->name('cups.listaServiciosHabilitados');
    Route::post('/cups/servicios-habilitados/guardar', [CatalogoController::class, 'guardarServicioHabilitado'])->name('cups.guardarServicioHabilitado');
    Route::post('/cups/servicios-habilitados/buscar', [CatalogoController::class, 'buscarServicioHabilitado'])->name('cups.buscarServicioHabilitado');
    Route::post('/cups/servicios-habilitados/eliminar', [CatalogoController::class, 'eliminarServicioHabilitado'])->name('cups.eliminarServicioHabilitado');
    Route::get('/cups/servicio-por-cups', [CatalogoController::class, 'getServicioHabilitadoPorCUPS'])->name('cups.getServicioHabilitadoPorCUPS');

    ///GESTIONAR CIE10
    Route::middleware(['permission:AdminCIE10'])->group(function () {
        Route::get('/Administracion/CodigosDiagnosticos', [CatalogoController::class, 'CIE10']);
    });
    Route::post('/verificar-codigo-cie10', [CatalogoController::class, 'verificarCodigoCIE10']);
    Route::post('/cie10/guardar', [CatalogoController::class, 'guardarCIE10'])->name('form.guardarCIE10');
    Route::post('/cie10/buscaCIE10', [CatalogoController::class, 'buscaCIE10'])->name('cie10.buscaCIE10');
    Route::post('/cie10/eliminarCIE10', [CatalogoController::class, 'eliminarCIE10'])->name('cie10.eliminarCIE10');
    Route::post('/cie10/listaCIE10', [CatalogoController::class, 'listaCIE10'])->name('cie10.listaCIE10');

    ///GESTIONAR BACKUP DE FORMULARIOS
    Route::middleware(['permission:AdminBackup'])->group(function () {
        Route::get('/Administracion/Backup', [SistemaController::class, 'Backup']);
    });
    Route::post('/backup/listaBackup', [SistemaController::class, 'listaBackup'])->name('Adminitraccion.listaBackup');
    Route::post('/backup/verDetalleBackup', [SistemaController::class, 'verDetalleBackup'])->name('Adminitraccion.verDetalleBackup');

    ///GESTIONAR ASESORIAS
    Route::middleware(['permission:AdminAsesorias'])->group(function () {
        Route::get('/Administracion/Asesorias', [ServicioController::class, 'Asesorias']);
    });
    Route::post('/asesorias/listaAsesorias', [ServicioController::class, 'listaAsesorias'])->name('asesorias.listaAsesorias');
    Route::post('/asesorias/guardarAsesoria', [ServicioController::class, 'guardarAsesoria'])->name('form.guardarAsesoria');
    Route::post('/asesorias/buscarAsesoria', [ServicioController::class, 'buscarAsesoria'])->name('asesorias.buscarAsesoria');
    Route::post('/asesorias/eliminarAsesoriaLista', [ServicioController::class, 'eliminarAsesoria'])->name('asesorias.eliminarAsesoriaLista');
    Route::post('/asesorias/listaAsesoriasSelect', [ServicioController::class, 'AsesoriasList'])->name('asesorias.listaAsesoriasSelect');
    Route::post('/asesorias/guardarVentaAsesoria', [ServicioController::class, 'guardarVentaAsesoria'])->name('asesorias.guardarVentaAsesoria');
    Route::post('/asesorias/eliminarVentaAsesoria', [ServicioController::class, 'eliminarVentaAsesoria'])->name('asesorias.eliminarVentaAsesoria');

    ///GESTIONAR ENTIDADES
    Route::middleware(['permission:Admineps'])->group(function () {
        Route::get('/Administracion/Entidades', [CatalogoController::class, 'Entidades']);
    });
    Route::post('/entidades/guardar', [CatalogoController::class, 'guardarEntidades'])->name('form.guardarEntidades');
    Route::post('/entidades/listaEntidades', [CatalogoController::class, 'listaEntidades'])->name('entidades.listaEntidades');
    Route::post('/entidades/buscaEntidad', [CatalogoController::class, 'buscaEntidad'])->name('entidades.buscaEntidad');
    Route::post('/entidades/eliminarEntidad', [CatalogoController::class, 'eliminarEntidad'])->name('entidades.eliminarEntidad');
    Route::post('/verificar-codigo-entidad', [CatalogoController::class, 'verificarCodigoEntidad']);

    ///GESTIONAR PAQUETES
    Route::middleware(['permission:Admineps'])->group(function () {
        Route::get('/Administracion/Paquetes', [ServicioController::class, 'Paquetes']);
    });
    Route::post('/paquetes/listaPaquetes', [ServicioController::class, 'listaPaquetes'])->name('paquetes.listaPaquetes');
    Route::post('/paquetes/guardar', [ServicioController::class, 'guardarPaquete'])->name('form.guardarPaquete');
    Route::post('/paquetes/buscarPaquete', [ServicioController::class, 'buscarPaquete'])->name('paquetes.buscarPaquete');
    Route::post('/paquetes/eliminarPaqueteLista', [ServicioController::class, 'eliminarPaquete'])->name('paquetes.eliminarPaqueteLista');

    ///GESTIONAR PRUEBAS
    Route::middleware(['permission:AdminPruebas'])->group(function () {
        Route::get('/Administracion/Pruebas', [ServicioController::class, 'Pruebas']);
    });
    Route::post('/pruebas/listaPruebas', [ServicioController::class, 'listaPruebas'])->name('pruebas.listaPruebas');
    Route::post('/pruebas/guardar', [ServicioController::class, 'guardarPrueba'])->name('form.guardarPrueba');
    Route::post('/pruebas/buscarPrueba', [ServicioController::class, 'buscarPrueba'])->name('pruebas.buscarPrueba');
    Route::post('/pruebas/eliminarPruebaLista', [ServicioController::class, 'eliminarPrueba'])->name('pruebas.eliminarPruebaLista');
    Route::post('/asesorias/listaServiciosVenta', [ServicioController::class, 'listaServiciosVenta'])->name('asesorias.listaServiciosVenta');

    ///GESTIONAR SESIONES
    Route::middleware(['permission:AdminSesiones'])->group(function () {
        Route::get('/Administracion/Sesiones', [ServicioController::class, 'Sesiones']);
    });
    Route::post('/sesiones/listaSesiones', [ServicioController::class, 'listaSesiones'])->name('sesiones.listaSesiones');
    Route::post('/sesiones/guardarSesion', [ServicioController::class, 'guardarSesion'])->name('sesiones.guardarSesion');
    Route::post('/sesiones/buscarSesion', [ServicioController::class, 'buscarSesion'])->name('sesiones.buscarSesion');
    Route::post('/sesiones/eliminarSesion', [ServicioController::class, 'eliminarSesion'])->name('sesiones.eliminarSesion');

    /// AGENDA
    Route::post('/citas/agenda', [AgendaController::class, 'agenda'])->name('citas.agenda');
    Route::get('profesionales/cargarListaProf', [ProfesionalController::class, 'cargarListaProf'])->name('profesionales.cargarListaProf');
    Route::get('/especialidad/cargarListaEsp', [CatalogoController::class, 'cargarListaEsp'])->name('especialidad.cargarListaEsp');
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
    Route::post('/citas/guardarBloquear', [AgendaController::class, 'guardarBloquear'])->name('citas.guardarBloquear');
    Route::post('/citas/eliminarBloqueo', [AgendaController::class, 'eliminarBloqueo'])->name('citas.eliminarBloqueo');
    Route::post('/citas/obtenerFechaInicioFinBloqueo', [AgendaController::class, 'obtenerFechaInicioFinBloqueo'])->name('citas.obtenerFechaInicioFinBloqueo');

    /// HISTORIAS CLINICAS
    Route::middleware(['permission:histPsicologia'])->group(function () {
        Route::get('/HistoriasClinicas/GestionarHistoriaPsicologia', [HistoriasController::class, 'historiaPsicologia'])->name('gestionar.psicologia');
    });
    Route::post('/pacientes/listaPacientesModal', [PacientesController::class, 'listaPacientesModal'])->name('pacientes.listaPacientesModal');
    Route::post('/pacientes/buscaPacienteHistoria', [PacientesController::class, 'buscaPacienteHistoria'])->name('pacientes.buscaPacienteHistoria');
    Route::get('/historia/buscaCUPS', [HistoriasController::class, 'buscaCUPS'])->name('historia.buscaCUPS');
    Route::get('/historia/buscaCIE', [HistoriasController::class, 'buscaCIE'])->name('historia.buscaCIE');
    Route::get('/historia/citasPaciente', [HistoriasController::class, 'citasPaciente'])->name('historia.citasPaciente');
    Route::post('/historia/guardarHistoriaPsicologica', [HistoriasController::class, 'guardarHistoriaPsicologica'])->name('form.guardarHistoriaPsicologica');
    Route::get('/hitoriaPsicologica/categorias', [HistoriasController::class, 'obtenerOpcionesHCP'])->name('hitoriaPsicologica.categorias');
    Route::post('/HistoriasClinicas/listaHistoriasPsicologica', [HistoriasController::class, 'listaHistoriasPsicologica'])->name('HistoriasClinicas.listaHistoriasPsicologica');
    Route::post('/historia/buscaHistoriaPsicologica', [HistoriasController::class, 'buscaHistoriaPsicologica'])->name('historia.buscaHistoriaPsicologica');
    Route::post('/historia/buscaProfesionalHistoria', [HistoriasController::class, 'buscaProfesionalHistoria'])->name('historia.buscaProfesionalHistoria');
    Route::post('/historia/cerrarHistoria', [HistoriasController::class, 'cerrarHistoria'])->name('historia.cerrarHistoria');
    Route::post('/historia/notasHistoria', [HistoriasController::class, 'notasHistoria'])->name('historia.notasHistoria');
    Route::get('/historia/imprimirHistoria', [HistoriasController::class, 'imprimirHistoria'])->name('historia.imprimirHistoria');
    Route::post('/historia/enviarHistoriaCorreo', [HistoriasController::class, 'enviarHistoriaCorreo'])->name('historia.enviarHistoriaCorreo');
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
    Route::middleware(['permission:histNeuro'])->group(function () {
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
    Route::post('/historia/enviarHistoriaCorreoNeuro', [HistoriaNeuroPsicologicaController::class, 'enviarHistoriaCorreoNeuro'])->name('historia.enviarHistoriaCorreoNeuro');

    /// GESTIONAR CONSULTAS NEURO
    Route::post('/historia/guardarConsultaNeuroPsicologica', [HistoriaNeuroPsicologicaController::class, 'guardarConsultaNeuroPsicologica'])->name('form.guardarConsultaNeuroPsicologica');
    Route::post('/historia/buscaConsultaNeuroPsicologica', [HistoriaNeuroPsicologicaController::class, 'buscaConsultaNeuroPsicologica'])->name('historia.buscaConsultaNeuroPsicologica');
    Route::post('/historia/listaConsultasModalNeuro', [HistoriaNeuroPsicologicaController::class, 'listaConsultasModalNeuro'])->name('historia.listaConsultasModalNeuro');
    Route::post('/historia/eliminarConsultaNeuro', [HistoriaNeuroPsicologicaController::class, 'eliminarConsultaNeuro'])->name('historia.eliminarConsultaNeuro');
    Route::post('/informes/imprimirConsultaNeuro', [HistoriaNeuroPsicologicaController::class, 'imprimirConsultaNeuro'])->name('informes.imprimirConsultaNeuro');
    Route::post('/informes/enviarConsultaNeuro', [HistoriaNeuroPsicologicaController::class, 'enviarConsultaNeuro'])->name('informes.enviarConsultaNeuro');

    /// GESTIONAR USUARIOS
    Route::middleware(['permission:gestionUsuarios'])->group(function () {
        Route::get('/Administracion/Usuarios', [SistemaController::class, 'Usuarios']);
    });
    Route::post('/AdminUsuario/listaUsuarios', [UsuariosController::class, 'listaUsuarios'])->name('usuarios.listaUsuarios');
    Route::post('/AdminUsuario/guardar', [UsuariosController::class, 'guardarUsuario'])->name('form.guardarUsusario');
    Route::post('/AdminUsuario/guardarPerfil', [UsuariosController::class, 'guardarPerfil'])->name('form.guardarPerfil');
    Route::post('/AdminUsuario/buscaUsuario', [UsuariosController::class, 'busquedaUsuario'])->name('usuario.buscaUsuario');
    Route::post('/AdminUsuario/buscaPerfil', [UsuariosController::class, 'buscaPerfil'])->name('usuario.buscaPerfil');
    Route::post('/AdminUsuario/eliminarUsuario', [UsuariosController::class, 'eliminarUsuario'])->name('usuario.eliminarUsuario');
    Route::post('/AdminUsuario/eliminarPerfil', [UsuariosController::class, 'eliminarPerfil'])->name('usuario.eliminarPerfil');

    Route::middleware(['permission:gestionPerfiles'])->group(function () {
        Route::get('/Administracion/Perfiles', [SistemaController::class, 'Perfiles']);
    });
    Route::post('/AdminUsuario/listaPerfiles', [UsuariosController::class, 'listaPerfiles'])->name('usuario.listaPerfiles');
    Route::get('/Administracion/buscaListPerfiles', [UsuariosController::class, 'buscaListPerfiles'])->name('usuario.buscaListPerfiles');

    Route::middleware(['permission:gestionLog'])->group(function () {
        Route::get('/Administracion/Logs', [SistemaController::class, 'Logs']);
    });
    Route::post('/AdminUsuario/listaLogs', [UsuariosController::class, 'listaLogs'])->name('AdminUsuario.listaLogs');

    // INFORMES PSICOLOGIA
    Route::middleware(['permission:informePsicologico'])->group(function () {
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
    Route::post('/informes/enviarInforme', [HistoriasController::class, 'enviarInforme'])->name('informes.enviarInforme');

    // INFORMES NEUROPSICOLOGIA
    Route::middleware(['permission:informeNeuro'])->group(function () {
        Route::get('/HistoriasClinicas/InformeNeuropsicologico', [HistoriaNeuroPsicologicaController::class, 'informePsicologia'])->name('gestionar.InformeNeuropsicologico');
    });
    Route::post('/informes/neuropsicologia', [HistoriaNeuroPsicologicaController::class, 'listaPacientesInformeNeuropsicologia'])->name('informes.neuropsicologia');
    Route::post('/informes/informeNeuropsicologia', [HistoriaNeuroPsicologicaController::class, 'informeNeuropsicologiaList'])->name('informes.informeNeuropsicologia');
    Route::post('/informes/buscaInformeNeuropsicologica', [HistoriaNeuroPsicologicaController::class, 'buscaInformeNeuropsicologica'])->name('informes.buscaInformeNeuropsicologica');
    Route::post('/informes/eliminarInformeNeuro', [HistoriaNeuroPsicologicaController::class, 'eliminarInformeNeuro'])->name('informes.eliminarInformeNeuro');
    Route::post('/informes/guardarInformeNeuropsicologica', [HistoriaNeuroPsicologicaController::class, 'guardarInformeNeuropsicologica'])->name('form.guardarInformeNeuropsicologica');
    Route::post('/informes/buscarAnexosInforme', [HistoriaNeuroPsicologicaController::class, 'buscarAnexosInforme'])->name('informes.buscarAnexosInforme');
    Route::post('/informes/eliminarAnexoInforme', [HistoriaNeuroPsicologicaController::class, 'eliminarAnexoInforme'])->name('informes.eliminarAnexoInforme');
    Route::post('/informes/imprimirInformeNeuropsicologia', [HistoriaNeuroPsicologicaController::class, 'imprimirInformeNeuropsicologia'])->name('informes.imprimirInformeNeuropsicologia');
    Route::post('/informes/enviarInformeNeuropsicologia', [HistoriaNeuroPsicologicaController::class, 'enviarInformeNeuropsicologia'])->name('informes.enviarInformeNeuropsicologia');

    // INFORMES GENERALES
    Route::get('/HistoriasClinicas/informes', [HistoriasController::class, 'informes'])->name('gestionar.informes');
    Route::post('/informes/informeGeneral', [HistoriasController::class, 'informeGeneral'])->name('informes.informeGeneral');
    Route::post('/informes/otrosInformes', [HistoriasController::class, 'otrosInformes'])->name('informes.otrosInformes');
    Route::post('/informes/informeGeneralList', [HistoriasController::class, 'informeGeneralList'])->name('informes.informeGeneralList');

    //GESTIONAR RECAUDOS
    Route::get('/Administracion/Recaudos', [RecaudosController::class, 'Recaudos']);
    Route::post('/Administracion/listaVentasPacientes', [RecaudosController::class, 'listaVentasPacientes'])->name('Administracion.listaVentasPacientes');
    Route::post('/Administracion/otraInformacionRecaudos', [RecaudosController::class, 'otraInformacionRecaudos'])->name('Administracion.otraInformacionRecaudos');
    Route::post('/Administracion/listaVentasPacientesPagos', [RecaudosController::class, 'listaVentasPacientesPagos'])->name('Administracion.listaVentasPacientesPagos');
    Route::post('/Administracion/detalleVentaServicioPaciente', [RecaudosController::class, 'detalleVentaServicioPaciente'])->name('Administracion.detalleVentaServicioPaciente');
    Route::post('/Administracion/detalleVentaPagosPaciente', [RecaudosController::class, 'detalleVentaPagosPaciente'])->name('Administracion.detalleVentaPagosPaciente');
    Route::post('/Administracion/guardar', [RecaudosController::class, 'guardarPagoVenta'])->name('form.guardarPagoVenta');
    Route::post('/Administracion/eliminarPagoRecaudo', [RecaudosController::class, 'eliminarPagoRecaudo'])->name('Administracion.eliminarPagoRecaudo');
    Route::post('/Administracion/imprimirRecaudo', [RecaudosController::class, 'imprimirRecaudo'])->name('Administracion.imprimirRecaudo');
    Route::post('/Administracion/listaPagos', [RecaudosController::class, 'listaPagos'])->name('Administracion.listaPagos');
    Route::post('/Administracion/obtenerDatosPago', [RecaudosController::class, 'obtenerDatosPago'])->name('Administracion.obtenerDatosPago');
    Route::post('/Administracion/actualizarPagoRecaudo', [RecaudosController::class, 'actualizarPagoRecaudo'])->name('Administracion.actualizarPagoRecaudo');

    //GESTIONAR GASTOS
    Route::get('/Administracion/Gastos', [CajaController::class, 'Gastos']);
    Route::post('/gastos/listaGastos', [CajaController::class, 'listaGastos'])->name('gastos.listaGastos');
    Route::get('/gastos/listaCategorias', [CajaController::class, 'listaCategorias'])->name('gastos.listaCategorias');
    Route::post('/gastos/guardarGastos', [CajaController::class, 'guardarGastos'])->name('form.guardarGastos');
    Route::post('/gastos/guardarCategoria', [CajaController::class, 'guardarCategoria'])->name('gastos.guardarCategoria');
    Route::post('/gastos/eliminarCategoria', [CajaController::class, 'eliminarCategoria'])->name('gastos.eliminarCategoria');
    Route::post('/gastos/eliminarGasto', [CajaController::class, 'eliminarGasto'])->name('gastos.eliminarGasto');
    Route::post('/gastos/buscarGasto', [CajaController::class, 'buscarGasto'])->name('gastos.buscarGasto');

    //GESTIONAR CAJAS
    Route::get('/Administracion/Cajas', [CajaController::class, 'Cajas']);
    Route::post('/cajas/listaCajas', [CajaController::class, 'listaCajas'])->name('cajas.listaCajas');
    Route::post('/cajas/guardarCaja', [CajaController::class, 'guardarCaja'])->name('cajas.guardarCaja');
    Route::post('/cajas/detalleCaja', [CajaController::class, 'detalleCaja'])->name('cajas.detalleCaja');
    Route::post('/cajas/cerrarCaja', [CajaController::class, 'cerrarCaja'])->name('cajas.cerrarCaja');
    Route::post('/cajas/eliminarCaja', [CajaController::class, 'eliminarCaja'])->name('cajas.eliminarCaja');
    Route::post('/cajas/consultarMontoCierre', [CajaController::class, 'consultarMontoCierre'])->name('cajas.consultarMontoCierre');
    Route::post('/asesorias/buscaVentaAsesoria', [ServicioController::class, 'buscaVentaAsesoria'])->name('asesorias.buscaVentaAsesoria');

    Route::post('/historia/buscaVentaConsulta', [HistoriasController::class, 'buscaVentaConsulta'])->name('historia.buscaVentaConsulta');
    Route::post('/historia/buscaVentaSesion', [HistoriasController::class, 'buscaVentaSesion'])->name('historia.buscaVentaSesion');
    Route::post('/historia/guardarVentaConsulta', [HistoriasController::class, 'guardarVentaConsulta'])->name('form.guardarVentaConsulta');
    Route::post('/historia/guardarVentaSesion', [HistoriasController::class, 'guardarVentaSesion'])->name('form.guardarVentaSesion');
    Route::post('/historia/buscaSesionVenta', [HistoriasController::class, 'buscaSesionVenta'])->name('historia.buscaSesionVenta');
    Route::post('/historia/eliminarSesionVenta', [HistoriasController::class, 'eliminarSesionVenta'])->name('historia.eliminarSesionVenta');

    Route::post('/historiaNeuro/buscaVentaConsultaNeuro', [HistoriaNeuroPsicologicaController::class, 'buscaVentaConsultaNeuro'])->name('historiaNeuro.buscaVentaConsultaNeuro');
    Route::post('/pacientes/getPacientes', [PacientesController::class, 'getPacientes'])->name('pacientes.getPacientes');
    Route::post('/citas/listarCitasPaciente', [AgendaController::class, 'listarCitasPaciente'])->name('citas.listarCitasPaciente');

    //GESTIONAR COTIZACIONES
    Route::post('/pacientes/listaCotizaciones', [PacientesController::class, 'listaCotizaciones'])->name('pacientes.listaCotizaciones');
    Route::post('/pacientes/guardarCotizacion', [PacientesController::class, 'guardarCotizacion'])->name('form.guardarCotizacion');
    Route::post('/pacientes/eliminarCotizacion', [PacientesController::class, 'eliminarCotizacion'])->name('pacientes.eliminarCotizacion');
    Route::post('/pacientes/editarCotizacion', [PacientesController::class, 'editarCotizacion'])->name('pacientes.editarCotizacion');
    Route::post('/informes/imprimirCotizacion', [PacientesController::class, 'imprimirCotizacion'])->name('informes.imprimirCotizacion');
    Route::post('/informes/enviarCotizacion', [PacientesController::class, 'enviarCotizacion'])->name('informes.enviarCotizacion');

    //GESTIONAR VENTAS EPS
    Route::post('/Administracion/listaVentasEps', [RecaudosController::class, 'listaVentasEps'])->name('Administracion.listaVentasEps');
    Route::post('/Administracion/listaVentasEpsPagos', [RecaudosController::class, 'listaVentasEpsPagos'])->name('Administracion.listaVentasEpsPagos');
    Route::post('/Administracion/listaVentasPacientesPagosEps', [RecaudosController::class, 'listaVentasPacientesPagosEps'])->name('Administracion.listaVentasPacientesPagosEps');

    //GESTIONAR COMPONENTES
    Route::get('/Administracion/Componentes', [CatalogoController::class, 'Componentes']);
    Route::post('/componentes/listaComponentes', [CatalogoController::class, 'listaComponentes'])->name('componentes.listaComponentes');
    Route::post('/componentes/guardarComponente', [CatalogoController::class, 'guardarComponente'])->name('form.guardarComponente');
    Route::post('/componentes/eliminarComponente', [CatalogoController::class, 'eliminarComponente'])->name('componentes.eliminarComponente');
    Route::post('/componentes/buscarComponente', [CatalogoController::class, 'buscarComponente'])->name('componentes.buscarComponente');
    Route::get('/componentes/listaCategoriasSelect', [CatalogoController::class, 'listaCategoriasSelect'])->name('componentes.listaCategoriasSelect');

    // GESTIONAR CUMPLEAÑOS
    Route::get('/cumpleanos/panel', [CumpleanosController::class, 'panelCumpleanos'])->name('cumpleanos.panel');
    Route::post('/cumpleanos/datos', [CumpleanosController::class, 'getDatosCumpleanos'])->name('cumpleanos.datos');
    Route::post('/cumpleanos/proximos', [CumpleanosController::class, 'getCumpleanosProximos'])->name('cumpleanos.proximos');

    /// CONTRATOS EPS
    Route::get('/Administracion/ContratosEps', function () {
        return redirect('/Administracion/Entidades');
    })->name('contratosEps.index');
    Route::post('/contratosEps/listarContratos',         [ContratosEpsController::class, 'listarContratos'])->name('contratosEps.listarContratos');
    Route::post('/contratosEps/guardarContrato',          [ContratosEpsController::class, 'guardarContrato'])->name('contratosEps.guardarContrato');
    Route::post('/contratosEps/eliminarContrato',         [ContratosEpsController::class, 'eliminarContrato'])->name('contratosEps.eliminarContrato');
    Route::post('/contratosEps/listarPlanes',             [ContratosEpsController::class, 'listarPlanes'])->name('contratosEps.listarPlanes');
    Route::post('/contratosEps/guardarPlan',              [ContratosEpsController::class, 'guardarPlan'])->name('contratosEps.guardarPlan');
    Route::post('/contratosEps/eliminarPlan',             [ContratosEpsController::class, 'eliminarPlan'])->name('contratosEps.eliminarPlan');
    Route::post('/contratosEps/listarCopagos',            [ContratosEpsController::class, 'listarCopagos'])->name('contratosEps.listarCopagos');
    Route::post('/contratosEps/guardarCopago',            [ContratosEpsController::class, 'guardarCopago'])->name('contratosEps.guardarCopago');
    Route::post('/contratosEps/eliminarCopago',           [ContratosEpsController::class, 'eliminarCopago'])->name('contratosEps.eliminarCopago');
    Route::post('/contratosEps/planesPorEps',             [ContratosEpsController::class, 'planesPorEps'])->name('contratosEps.planesPorEps');
    Route::post('/contratosEps/listarEntidadesEps',       [ContratosEpsController::class, 'listarEntidadesEps'])->name('contratosEps.listarEntidadesEps');

    /// AUTORIZACIONES EPS
    Route::middleware(['permission:agenda'])->group(function () {
        Route::get('/Administracion/Autorizaciones', [AutorizacionesController::class, 'index'])->name('autorizaciones.index');
    });
    Route::post('/autorizaciones/listar',              [AutorizacionesController::class, 'listar'])->name('autorizaciones.listar');
    Route::post('/autorizaciones/guardar',             [AutorizacionesController::class, 'guardar'])->name('autorizaciones.guardar');
    Route::post('/autorizaciones/eliminar',            [AutorizacionesController::class, 'eliminar'])->name('autorizaciones.eliminar');
    Route::get('/autorizaciones/porPaciente',          [AutorizacionesController::class, 'porPaciente'])->name('autorizaciones.porPaciente');
    Route::get('/autorizaciones/planesPorPaciente',    [AutorizacionesController::class, 'planesPorPaciente'])->name('autorizaciones.planesPorPaciente');
    Route::get('/autorizaciones/serviciosPorPlan',     [AutorizacionesController::class, 'serviciosPorPlan'])->name('autorizaciones.serviciosPorPlan');

    // COBERTURA EPS - PACIENTES
    Route::post('/pacientes/guardarPlanEps',        [PacientesController::class, 'guardarPlanPaciente'])->name('pacientes.guardarPlanEps');
    Route::post('/pacientes/quitarPlanEps',         [PacientesController::class, 'quitarPlanPaciente'])->name('pacientes.quitarPlanEps');
    Route::post('/pacientes/obtenerCobertura',      [PacientesController::class, 'obtenerCoberturaPaciente'])->name('pacientes.obtenerCobertura');
    Route::post('/pacientes/listarAutorizaciones',  [PacientesController::class, 'listarAutorizaciones'])->name('pacientes.listarAutorizaciones');
    Route::post('/pacientes/registrarAutorizacion', [PacientesController::class, 'registrarAutorizacion'])->name('pacientes.registrarAutorizacion');
});
