@extends('Plantilla.Principal')
@section('title', 'Tablero Inicial')
@section('Contenido')

    <meta name="citas-agenda-url" content="{{ route('citas.agenda') }}">
    <input type="hidden" id="Ruta" data-ruta="{{ asset('/app-assets/') }}" />
    <input type="hidden" id="urlBase" data-ruta="{{ asset('/') }}" />

    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h4 class="page-title">Agenda de citas</h4>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#"><i class="mdi mdi-home-outline"></i></a></li>
                            <li class="breadcrumb-item" aria-current="page">Extra</li>
                            <li class="breadcrumb-item active" aria-current="page">Agenda de citas</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-12">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="mb-30 mb-xl-0">
                                    <div id="calendar"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="modal fade" id="event-modal-add-cita" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered" style="max-width: 85%;">
                        <div class="modal-content">
                            <form class="needs-validation" id="formCita" id="form-event" novalidate>
                                <div class="modal-header py-3 px-4 border-bottom-0">
                                    <h4 class="modal-title" id="modal-title">Agregar cita</h4>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body px-4 pb-4 pt-0">
                                    <div class="row">
                                        <div class="col-3">
                                            <div class="form-body">
                                                <h4 class="form-section"><i class="fa fa-list-alt"></i> Información</h4>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <div class="controls">
                                                                <label for="account-username">Profesional:</label>
                                                                <select onchange="cargarDisponibilidad(this.value)"
                                                                    class="form-control select2" style="width: 100%;"
                                                                    id="profesional" name="profesional">
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <div class="controls">
                                                                <label for="account-username">Motivo de la
                                                                    Consulta:</label>
                                                                <select class="form-control select2" style="width: 100%;"
                                                                    id="especialidad" name="especialidad">
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <div class="controls">
                                                                <label for="account-username">Duración: </label>
                                                                <select class="form-control" id="duracionCita"
                                                                    name="duracionCita" aria-invalid="false">
                                                                    <option value="20">20 minutos</option>
                                                                    <option value="40">40 minutos</option>
                                                                    <option value="60">1 hora</option>
                                                                    <option value="90">1 hora y 30 min.</option>
                                                                    <option value="120">2 horas</option>
                                                                    <option value="150">2 horas y 30 min.</option>
                                                                    <option value="180">3 horas</option>
                                                                    <option value="240">4 horas</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <div class="controls">
                                                                <label for="account-username">Cita
                                                                    seleccionada para: </label>
                                                                <input type="hidden" class="form-control"
                                                                    id="fechaHoraInicio" name="fechaHoraInicio"
                                                                    placeholder="Fecha cita">
                                                                <input type="hidden" class="form-control"
                                                                    id="fechaHoraFinal" name="fechaHoraFinal"
                                                                    placeholder="Fecha cita">
                                                                <input disabled type="text" class="form-control"
                                                                    id="fechaHoraSelCita" name="fechaHoraSelCita"
                                                                    placeholder="Fecha cita">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group"
                                                            style="inline-flex: flex; align-items: center;">
                                                            <div class="controls align-content-center">
                                                                <label for="account-username">&nbsp;</label>
                                                                <fieldset>
                                                                    <label for="input-16" style="cursor: pointer;">
                                                                        <label class="switch switch-border switch-primary">
                                                                            <input type="checkbox" checked
                                                                                id="notifCliente" name="notifCliente">
                                                                            <span class="switch-indicator"></span>
                                                                            <span class="switch-description"></span>
                                                                        </label>
                                                                        <li class="fa fa-envelope"></li> Notificar a
                                                                        paciente
                                                                        por correo
                                                                    </label>
                                                                </fieldset>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="box-footer text-end">
                                                    <button type="button" id="cancelRegistro" onclick="salirAddCita();"
                                                        class="btn btn-primary-light me-1">
                                                        <i class="ti-close"></i> Salir
                                                    </button>
                                                    <button type="button" onclick="atrasAddCita();"
                                                        style="display: none;" id="btnAtras"
                                                        class="btn btn-primary-light me-1">
                                                        <i class="ti-arrow-left"></i> Atras
                                                    </button>
                                                    <button type="button" id="btnGuardar" onclick="continuar();"
                                                        class="btn btn-primary">
                                                        <i class="ti-arrow-right"></i> Continuar
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-9">
                                            <div class="form-body" id="calendaCita">
                                                <h4 class="form-section"><i class="fa fa-calendar"></i> Fecha</h4>
                                                <div class="card-body">
                                                    <div id="calendarCita" style=" width: 100%;  height: 600px;"></div>
                                                </div>

                                            </div>
                                            <div class="form-body" id="calendaCitaPaci" style="display: none;">
                                                <h4 class="form-section"><i class="fa fa-user"></i> Información del
                                                    Paciente
                                                </h4>
                                                <div class="card-body">
                                                    <ul class="nav nav-tabs nav-bordered mb-3">
                                                        <li class="nav-item" onclick="habPacExist();">
                                                            <a href="#existente" data-bs-toggle="tab"
                                                                aria-expanded="false" class="nav-link active">
                                                                <i class="fa fa-user"></i> Paciente
                                                                existente</a>
                                                        </li>
                                                        <li onclick="habPacNuevo();">
                                                            <a data-bs-toggle="tab" aria-expanded="true" class="nav-link"
                                                                href="#nuevo">
                                                                <i class="fa fa-user-plus"></i> Paciente nuevo</a>
                                                        </li>
                                                    </ul>
                                                    <div class="tab-content">
                                                        <div class="tab-pane show active" id="existente">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="form-group">
                                                                        <div class="controls">
                                                                            <label for="account-username">Paciente:</label>
                                                                            <select onchange="selecPaciente(this.value)"
                                                                                class="select2 form-control"
                                                                                id="paciente" name="paciente">
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12">
                                                                    <div class="form-group">
                                                                        <div class="controls">
                                                                            <label
                                                                                for="account-username">Comentario:</label>
                                                                            <textarea class="form-control" id="comentario" name="comentario" rows="3"
                                                                                placeholder="Ingrese un comentario"></textarea>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="tab-pane" id="nuevo">
                                                            <input type="hidden" name="idPaciente" id="idPaciente"
                                                                value="">
                                                            <input type="hidden" name="accionCita" id="accionCita"
                                                                value="agregar">
                                                            <input type="hidden" name="idCita" id="idCita"
                                                                value="agregar">
                                                            <input type="hidden" name="opc" id="opc"
                                                                value="agregar">
                                                            <div class="row">
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label for="tipoIdentificacion"
                                                                            class="form-label">Tipo de
                                                                            identificación:</label>
                                                                        <select class="form-control" id="tipoId"
                                                                            name="tipoId" aria-invalid="false">
                                                                            <option value="">Selecciona una
                                                                                opción</option>
                                                                            <option value="AS">
                                                                                Adulto sin Identificación </option>
                                                                            <option value="CC">
                                                                                Cédula Ciudadanía </option>
                                                                            <option value="CD">
                                                                                Carné Diplomático </option>
                                                                            <option value="CE">
                                                                                Cédula de Extranjería </option>
                                                                            <option value="MS">
                                                                                Menor sin Identificación </option>
                                                                            <option value="NV">
                                                                                Certificado de Nacido Vivo </option>
                                                                            <option value="PE">
                                                                                Permiso Especial del Permanencia </option>
                                                                            <option value="PT">
                                                                                Permiso por protección temporal </option>
                                                                            <option value="RC">
                                                                                Registro Civil </option>
                                                                            <option value="TI">
                                                                                Tarjeta de identidad </option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="form-group">
                                                                        <label for="identificación"
                                                                            class="form-label">Identificación:</label>
                                                                        <input type="text" minlength="4"
                                                                            maxlength="20" class="form-control"
                                                                            id="identificacion" name="identificacion">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label for="tipoUsuario" class="form-label">Tipo
                                                                            de usuario:</label>
                                                                        <select class="form-control" id="tipoUsuario"
                                                                            name="tipoUsuario" aria-invalid="false">
                                                                          
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="form-group">
                                                                        <label for="fechaNacimiento"
                                                                            class="form-label">Fecha de nacimiento:</label>
                                                                            <div class="input-group">

                                                                                <input type="date" id="fechaNacimiento" placeholder="" name="fechaNacimiento"
                                                                                    class="form-control" data-inputmask="'alias': 'dd/mm/yyyy'">
                                                                            </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="form-group">
                                                                        <label for="edad"
                                                                            class="form-label">Edad:</label>
                                                                        <input type="text" readonly
                                                                            class="form-control" id="edad"
                                                                            name="edad">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label for="primerNombre"
                                                                            class="form-label">Primer nombre:</label>
                                                                        <input type="text" class="form-control"
                                                                            id="primerNombre" name="primerNombre">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label for="segundoNombre"
                                                                            class="form-label">Segundo nombre:</label>
                                                                        <input type="text" class="form-control"
                                                                            id="segundoNombre" name="segundoNombre">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label for="primerApellido"
                                                                            class="form-label">Primer apellido:</label>
                                                                        <input type="text" class="form-control"
                                                                            id="primerApellido" name="primerApellido">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label for="segundoApellido"
                                                                            class="form-label">Segundo apellido:</label>
                                                                        <input type="text" class="form-control"
                                                                            id="segundoApellido" name="segundoApellido">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="form-group">
                                                                        <label for="sexo"
                                                                            class="form-label">Sexo:</label>
                                                                        <select class="form-control" id="sexo"
                                                                            name="sexo" aria-invalid="false">
                                                                            <option value="">Selecciona una
                                                                                opción</option>
                                                                            <option value="H">
                                                                                Hombre </option>
                                                                            <option value="I">
                                                                                Indeterminado o Intersexual </option>
                                                                            <option value="M">
                                                                                Mujer</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label for="email"
                                                                            class="form-label">Email:</label>
                                                                        <input type="text" class="form-control"
                                                                            id="email" name="email">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="form-group">
                                                                        <label for="telefono"
                                                                            class="form-label">Teléfono:</label>
                                                                        <input type="text" class="form-control"
                                                                            id="telefono" name="telefono">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="form-group">
                                                                        <label for="direccion"
                                                                            class="form-label">Dirección:</label>
                                                                        <input type="text" class="form-control"
                                                                            id="direccion" name="direccion">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="form-group">
                                                                        <label for="zonaResidencial"
                                                                            class="form-label">Zona de residencia:</label>
                                                                        <select class="form-control" id="zonaResidencial"
                                                                            name="zonaResidencial" aria-invalid="false">
                                                                            <option value="">Selecciona una
                                                                                opción</option>
                                                                            <option value="01">Rural</option>
                                                                            <option value="02">Urbano</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12">
                                                                    <div class="form-group">
                                                                        <label for="account-company">Observaciones</label>
                                                                        <div claPss="form-group">
                                                                            <textarea name="observaciones" class="form-control textarea-maxlength" id="observaciones"
                                                                                placeholder="Ingrese alguna observación del paciente.." maxlength="250" rows="5"></textarea>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12">
                                                                    <div class="form-group">
                                                                        <label for="account-company">Comentario</label>
                                                                        <div class="form-group">
                                                                            <textarea name="comentario2" class="form-control textarea-maxlength" id="comentario2"
                                                                                placeholder="Ingrese un comentario" maxlength="250" rows="3"></textarea>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                            </div>

                                                        </div>


                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{--  Modal comentarios  --}}
        <div class="modal fade text-left" id="modalComentarios" tabindex="-1" role="dialog"
            aria-labelledby="myModalLabel1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Comentarios</h4>

                    </div>
                    <div class="modal-body">
                        <div class="card-body">

                            <form class="form" method="post" id="formGuardarComentario">

                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="userinput8">Comentario:</label>
                                            <div class="d-flex align-items-start">
                                                <textarea name="comentarioCitaVal" class="form-control textarea-maxlength" id="comentarioCitaVal"
                                                    placeholder="Ingrese un comentario.." maxlength="250" rows="5"></textarea>

                                            </div>
                                        </div>
                                    </div>


                                    <div class="col-12">
                                        <div class="form-actions right">
                                            <button type="button" onclick="guardarComentario();"
                                                class="btn btn-success mr-1">
                                                <i class="ti-save"></i>
                                                Guardar
                                            </button>
                                            <button type="button" onclick="salirComentario();"
                                                class="btn btn-warning mr-1">
                                                <i class="ti-close "></i>
                                                Salir
                                            </button>

                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="modal fade text-left" id="modalCitasDeta" tabindex="-1" role="dialog"
            aria-labelledby="myModalLabel1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 55%;"> role="document">
                <div class="modal-content">
                    <div class="modal-header" style="padding-bottom: 0px">
                        <div class="media p-1">
                            <div class="media-left pr-1"><span class="avatar avatar-online avatar-sm rounded-circle"
                                    style="width: 60px !important;  height: 60px !important;"><img src=""
                                        alt="avatar" id="previewImageDetCita"><i></i></span></div>
                            <div class="media-body media-middle">
                                <h5 id="npacientedetCita" style="text-transform: capitalize;"
                                    class="media-heading text-bold-600">77097205 - Xiamir Luquez Ramirez</h5>
                                <p id="edadDetaCita"></p>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="card-body">
                            <ul class="nav nav-tabs nav-bordered mb-3">
                                <li class="nav-item">
                                    <a href="#infCita" data-bs-toggle="tab" aria-expanded="false"
                                        class="nav-link active">
                                        <i class="fa fa-calendar"></i> Detalle de la Cita</a>
                                </li>
                                <li class="nav-item">
                                    <a href="#infHistoria" data-bs-toggle="tab" aria-expanded="false"
                                        class="nav-link"><i class="fa fa-street-view"></i> Historia clinica</a>
                                </li>
                                <li class="nav-item dropdown">
                                    <a href="#infDatos" data-bs-toggle="tab" aria-expanded="false" class="nav-link">
                                        <i class="fa fa-address-card-o"></i> Datos Personales</a>
                                </li>
                            </ul>
                            <div class="tab-content px-1 pt-1">
                                <div class="tab-pane active" id="infCita" aria-labelledby="homeIcon-tab"
                                    role="tabpanel">
                                    <div class="col-12">
                                        <h5 class="mb-1"><i class="feather icon-info"></i> Información de la cita</h5>
                                        <table class="table table-borderless">
                                            <tbody>
                                                <tr>
                                                    <td class="text-bold-600">Motivo de consulta:</td>
                                                    <td id="especialidadCita"></td>
                                                </tr>
                                                <tr>
                                                    <td class="text-bold-600">Profesional:</td>
                                                    <td id="profesionalCita"></td>
                                                </tr>
                                                <tr>
                                                    <td class="text-bold-600">Fecha y hora de Inicio:</td>
                                                    <td id="inicioCita"></td>
                                                </tr>
                                                <tr>
                                                    <td class="text-bold-600">Fecha y hora de finalización:</td>
                                                    <td id="finalcita"></td>
                                                </tr>
                                                <tr>
                                                    <td class="text-bold-600">Comentario:</td>
                                                    <td style="white-space: pre-line;" id="cometarioCita">Sin Comentario
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-bold-600">Cambiar estado:</td>
                                                    <td id="final">
                                                        <select class="select2-bg form-control"
                                                            onchange="cambioEstado(this.value);" id="estadocita">
                                                            <option value="Por atender" class="por-atender">Por atender
                                                            </option>
                                                            <option value="Atendida" class="atendida">Atendida</option>
                                                            <option value="Confirmada" class="confirmada">Confirmada
                                                            </option>
                                                            <option value="No Confirmada" class="no-confirmada">No
                                                                Confirmada</option>
                                                            <option value="Anulada" class="anulada">Anulada</option>
                                                        </select>

                                                    </td>
                                                </tr>
                                                <tr>

                                                    <td colspan="2">
                                                        <div class="form-actions right">

                                                            <div class="btn-group mb-5">
                                                                <button type="button"
                                                                    class="waves-effect waves-light btn btn-warning dropdown-toggle"
                                                                    data-bs-toggle="dropdown"><i
                                                                        class="fa fa-paper-plane-o"></i> Notificar
                                                                    recordatorio al
                                                                    cliente</button>
                                                                <div class="dropdown-menu">
                                                                    <a class="dropdown-item" style="cursor: pointer;"
                                                                        onclick="notifCPaciente();">Notificar al correo</a>
                                                                    <a class="dropdown-item" style="cursor: pointer;"
                                                                        onclick="notifCPacienteWP();">Notificar al
                                                                        whatsapp</a>
                                                                </div>
                                                            </div>
                                                            <button type="button" onclick="addComentario();"
                                                                class="btn btn-primary">
                                                                <i class="fa fa-comment-o"></i> Agregar
                                                                comentario
                                                            </button>
                                                            <button type="button" onclick="editCita();"
                                                                class="btn btn-info">
                                                                <i class="fa fa-calendar-minus-o"></i> Editar cita
                                                            </button>
                                                            <button type="button" onclick="eliminarCita();"
                                                                class="btn btn-danger">
                                                                <i class="fa fa-trash-o"></i> Eliminar cita
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>

                                            </tbody>
                                        </table>


                                    </div>
                                </div>
                                <div class="tab-pane" id="infHistoria" aria-labelledby="profileIcon-tab"
                                    role="tabpanel">
                                    <div class="clearfix" style="display: flex; justify-content: center; ">
                                        <a style="padding: 30px; height: 100%;font-size: 20px;"
                                            onclick="irHistoria('psicologia')"
                                            class="waves-effect waves-light btn btn-app btn-info" href="#">
                                            <i class="fa fa-user-md"></i> Psicolólogia
                                        </a>
                                        <a style="padding: 30px; height: 100%; font-size: 20px;"
                                            onclick="irHistoria('neuro')"
                                            class="waves-effect waves-light btn btn-app btn-success" href="#">
                                            <i class="fa fa-user-md"></i> Neuropsicología
                                        </a>
                                    </div>
                                </div>
                                <div class="tab-pane" id="infDatos" aria-labelledby="dropdownIcon1-tab"
                                    role="tabpanel">
                                    <div class="col-12">

                                        <input type="hidden" id="edadPacienteCita" name="edadPacienteCita"
                                            value="">

                                        <h5 class="mb-1"><i class="feather icon-info"></i> Información Personal</h5>
                                        <table class="table table-borderless mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-bold-600">Identificación:</td>
                                                    <td id="identificacionCita"></td>
                                                    <td class="text-bold-600">nombre:</td>
                                                    <td id="nombreCita"></td>
                                                </tr>

                                                <tr>
                                                    <td class="text-bold-600">Sexo:</td>
                                                    <td id="sexoCita"></td>
                                                    <td class="text-bold-600">Fecha Nacimiento:</td>
                                                    <td id="nacimientoCita"></td>
                                                </tr>

                                                <tr>
                                                    <td class="text-bold-600">Teléfono:</td>
                                                    <td id="telefonoCita"></td>
                                                    <td class="text-bold-600">Email:</td>
                                                    <td id="emailCita"></td>
                                                </tr>

                                                <tr>
                                                    <td class="text-bold-600">Dirección:</td>
                                                    <td id="direccionCita"></td>
                                                    <td class="text-bold-600">Tipo de usuario:</td>
                                                    <td id="tipoUsuarioCita"></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <h5 class="mb-1"><i class="feather icon-info"></i> Información acompañante</h5>
                                        <table class="table table-borderless mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="text-bold-600">Nombre:</td>
                                                    <td id="acompananteNombreCita"></td>
                                                    <td class="text-bold-600">Parentesco:</td>
                                                    <td id="acompananteParentescoCita"></td>
                                                    <td class="text-bold-600">Teléfono:</td>
                                                    <td id="acompananteTelefonoCita"></td>
                                                </tr>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane" id="infReca" aria-labelledby="dropdownIcon2-tab"
                                    role="tabpanel">
                                    <div class="card-content">
                                        <div class="card-body">
                                            <p><span class="float-right"><a
                                                        style="color: #009c9f;text-decoration: none; background-color: transparent;"
                                                        onclick="$.verRecaudos();" target="_blank">Ver Recaudos <i
                                                            class="feather icon-arrow-right"></i></a></span></p>
                                        </div>
                                        <div class="table-responsive">
                                            <table id="recent-orders"
                                                class="table table-hover mb-0 ps-container ps-theme-default">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>No. tratamiento</th>
                                                        <th>Tratamiento</th>
                                                        <th>Profesional</th>
                                                        <th>Total</th>
                                                        <th>Saldo</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tratamientosRecaudo-citas">

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="modal fade none-border" id="add-new-events">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title"><strong>Add</strong> a category</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form role="form">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">Category Name</label>
                                    <input class="form-control form-white" placeholder="Enter name" type="text"
                                        name="category-name" />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Choose Category Color</label>
                                    <select class="form-select form-white" data-placeholder="Choose a color..."
                                        name="category-color">
                                        <option value="success">Success</option>
                                        <option value="danger">Danger</option>
                                        <option value="info">Info</option>
                                        <option value="primary">Primary</option>
                                        <option value="warning">Warning</option>
                                        <option value="inverse">Inverse</option>
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success save-category"
                            data-bs-dismiss="modal">Save</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        window.userPermissions = @json(Auth::user()->permissions);
        document.addEventListener("DOMContentLoaded", async function() {
            loader = document.getElementById('loader');
            loadNow(1);
            localStorage.clear();

            let menuP = document.getElementById("agenda");
            menuP.classList.add("active");

            $('#paciente').select2({
                dropdownParent: $('#event-modal-add-cita'),
                width: '100%'
            });

            $('#profesional').select2({
                dropdownParent: $('#event-modal-add-cita'),
                width: '100%'
            });

            $('#especialidad').select2({
                dropdownParent: $('#event-modal-add-cita'),
                width: '100%'
            });

        });

        function hasPermission(permission) {
            return window.userPermissions && window.userPermissions.includes(permission);
        }

        function irHistoria(tipo) {

            let idPaciente = document.getElementById("idPaciente").value
            let edadPaciente = document.getElementById("edadPacienteCita").value

            localStorage.clear();
            localStorage.setItem('idPaciente', idPaciente);
            localStorage.setItem('edadPaciente', edadPaciente);
            let prascaURL;
            if (tipo == 'psicologia') {
                if (hasPermission('histPsicologia')) {
                    prascaURL = '{{ url('/pacientes/historiaPsicologica') }}';
                    const nuevaPestana = window.open(prascaURL, '_blank');
                    nuevaPestana.focus();
                } else {
                    swal("¡Alerta!",
                        "No tiene el permiso necesario para realizar esta acción",
                        "warning")
                }

            } else {
                if (hasPermission('histNeuro')) {
                    prascaURL = '{{ url('/pacientes/historiaNeuropsicologica') }}';
                    const nuevaPestana = window.open(prascaURL, '_blank');
                    nuevaPestana.focus();
                } else {
                    swal("¡Alerta!",
                        "No tiene el permiso necesario para realizar esta acción",
                        "warning")
                }
            }
        }

        function notifCPacienteWP() {
            let parCita = document.getElementById("inicioCita").innerText.split(" ")

            let numeroTelefono = document.getElementById("telefonoCita").innerText; // Obtén el número desde el HTML
            let mensaje =
                `*Prasca Center*
        Hola, este es un recordatorio de tu cita médica:
            
          *- Fecha:* ${parCita[0]}
          *- Hora:* ${parCita[1]} ${parCita[2]}
          *- Lugar:* Calle 11 # 11 - 07 San Joaquin 
        
        Por favor, confirma tu asistencia respondiendo este mensaje. Gracias.`; // Mensaje de prueba; reemplázalo por el mensaje real

            // Número de prueba; reemplázalo por el valor real en producción
            numeroTelefono = "573164915332";

            // Codificar el mensaje y generar el enlace de WhatsApp
            const url = `https://wa.me/${numeroTelefono}?text=${encodeURIComponent(mensaje)}`;

            // Abrir el enlace en una nueva pestaña
            window.open(url, '_blank');
        }
    </script>
@endsection
