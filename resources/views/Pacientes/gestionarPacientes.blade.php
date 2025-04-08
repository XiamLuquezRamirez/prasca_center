@extends('Plantilla.Principal')
@section('title', 'Gestionar pacientes')
@section('Contenido')

    <input type="hidden" id="Ruta" data-ruta="{{ asset('/app-assets/') }}" />
    <input type="hidden" id="RutaTotal" data-ruta="{{ asset('/') }}" />
    <input type="hidden" id="page" />
    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h4 class="page-title">Gestionar pacientes</h4>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#"><i class="mdi mdi-home-outline"></i></a></li>
                            <li class="breadcrumb-item" aria-current="page">Inicio</li>
                            <li class="breadcrumb-item active" aria-current="page">Gestionar pacientes</li>
                        </ol>
                    </nav>
                </div>

            </div>

        </div>
    </div>
    <section class="content">
        <div class="row">

            <div id="listado" class="col-12 col-xl-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Listado de pacientes</h5>
                    </div>
                    <div class="card-body">
                        <div class="box-controls pull-right">
                            <div class="box-header-actions">
                                <div class="input-group input-group-merge">
                                    <input type="text" id="busqueda" class="form-control">
                                    <div class="input-group-text" data-password="false">
                                        <span class="fa fa-search"></span>
                                    </div>
                                    <button type="button" onclick="nuevoRegistro();"
                                        class="btn btn-xs btn-primary font-bold"><i class="fa fa-plus"></i> Nuevo
                                        paciente</button>
                                </div>

                            </div>
                        </div>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th style="width:10%;">Identificación</th>
                                    <th style="width:30%;">Nombre</th>
                                    <th style="width:13%;">Regimen</th>
                                    <th style="width:6%;">Sexo</th>
                                    <th style="width:8%;">Edad</th>
                                    <th style="width:8%;">Teléfono</th>
                                    <th style="width:10%;">Datos paciente</th>
                                    <th style="width:15%;">Acción</th>
                                </tr>
                            </thead>
                            <tbody id="trRegistros">


                            </tbody>
                        </table>
                        <div id="pagination-links" class="text-center ml-1 mt-2">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- MODAL PACIENTES -->
    <div class="modal fade" id="modalPacientes" tabindex="-1" data-bs-focus="false" role="dialog"
        aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 60%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">Agregar paciente</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                    <form id="formPaciente" lang="es">
                        <input type="hidden" name="accPacientes" id="accPacientes" value="guardar" />
                        <input type="hidden" name="idPaciente" id="idPaciente" value="" />
                        <input type="hidden" name="fotoCargada" id="fotoCargada" />
                        <h5 class="mb-4 text-uppercase"><i class="mdi mdi-account-circle me-1"></i> Información personal
                        </h5>
                        <div class="row">
                            <div class="col-xl-12 col-lg-12">
                                <div class="box">
                                    <a class="media-single" style="padding: 5px;">
                                        <img id="previewImage" class="avatar pull-left me-10"
                                            src="{{ asset('app-assets/images/FotosPacientes/default.jpg') }}"
                                            alt="">
                                        <div>
                                            <label class="btn btn-xs btn-primary cursor-pointer" for="account-upload">Subir
                                                una foto</label>
                                            <input type="file" name="fotoPaciente" id="account-upload" hidden>
                                            <button type="button" onclick="clearImage()"
                                                class="btn btn-light btn-xs">Limpiar</button>
                                            <p class="text-fade fs-12 mb-0">Solo JPG, GIF o PNG.
                                                Tam. Max. de 800kB</p>
                                        </div>
                                    </a>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="tipoIdentificacion" class="form-label">Tipo de identificación :</label>
                                    <select class="form-control" id="tipoId" name="tipoId" aria-invalid="false">
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
                                    <label for="identificación" class="form-label">Identificación :</label>
                                    <input type="text" minlength="4" maxlength="20" class="form-control"
                                        id="identificacion" name="identificacion">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tipoUsuario" class="form-label">Tipo de usuario :</label>
                                    <select class="form-control" id="tipoUsuario" name="tipoUsuario"
                                        aria-invalid="false">

                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="fechaNacimiento" class="form-label">Fecha de nacimiento:</label>
                                    <div class="input-group">

                                        <input type="date" id="fechaNacimiento" placeholder="" name="fechaNacimiento"
                                            class="form-control" data-inputmask="'alias': 'dd/mm/yyyy'">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="edad" class="form-label">Edad :</label>
                                    <input type="text" readonly class="form-control" id="edad" name="edad">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="primerNombre" class="form-label">Primer nombre :</label>
                                    <input type="text" class="form-control" id="primerNombre" name="primerNombre">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="segundoNombre" class="form-label">Segundo nombre :</label>
                                    <input type="text" class="form-control" id="segundoNombre" name="segundoNombre">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="primerApellido" class="form-label">Primer apellido :</label>
                                    <input type="text" class="form-control" id="primerApellido"
                                        name="primerApellido">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="segundoApellido" class="form-label">Segundo apellido :</label>
                                    <input type="text" class="form-control" id="segundoApellido"
                                        name="segundoApellido">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="sexo" class="form-label">Sexo :</label>
                                    <select class="form-control" id="sexo" name="sexo" aria-invalid="false">
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
                                    <label for="estadocivil" class="form-label">Estado civil :</label>
                                    <select class="form-control" id="estadocivil" name="estadocivil"
                                        aria-invalid="false">
                                        <option value="">Selecciona una
                                            opción</option>
                                        <option value="soltero">Soltero/a</option>
                                        <option value="casado">Casado/a</option>
                                        <option value="divorciado">Divorciado/a</option>
                                        <option value="viudo">Viudo/a</option>
                                        <option value="unionLibre">Unión libre</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="ocupacion" class="form-label">Ocupación :</label>
                                    <input type="text" class="form-control" id="ocupacion" name="ocupacion">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="lateralidad" class="form-label">Lateralidad :</label>
                                    <select class="form-control" id="lateralidad" name="lateralidad"
                                        aria-invalid="false">
                                        <option value="">Selecciona una
                                            opción</option>
                                        <option value="diestro">Diestro/a</option>
                                        <option value="zurdo">Zurdo/a</option>
                                        <option value="ambidiestro">Ambidiestro/a</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="religion" class="form-label">Religión :</label>
                                    <select class="form-control" id="religion" name="religion" aria-invalid="false">
                                        <option value="">Selecciona una
                                            opción</option>
                                        <option value="catolica">Católica</option>
                                        <option value="cristiana">Cristiana</option>
                                        <option value="judaica">Judaica</option>
                                        <option value="islamica">Islámica</option>
                                        <option value="budista">Budista</option>
                                        <option value="hinduista">Hinduista</option>
                                        <option value="agnostico">Agnóstico/a</option>
                                        <option value="ateo">Ateo/a</option>
                                        <option value="otra">Otra</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="lugarNacimiento" class="form-label">Lugar de nacimiento :</label>
                                    <input type="text" class="form-control" id="lugarNacimiento"
                                        name="lugarNacimiento">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="email" class="form-label">Email :</label>
                                    <input type="text" class="form-control" id="email" name="email">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="telefono" class="form-label">Teléfono :</label>
                                    <input type="text" class="form-control" id="telefono" name="telefono">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="direccion" class="form-label">Dirección :</label>
                                    <input type="text" class="form-control" id="direccion" name="direccion">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="zonaResidencial" class="form-label">Zona de residencia :</label>
                                    <select class="form-control" id="zonaResidencial" name="zonaResidencial"
                                        aria-invalid="false">
                                        <option value="">Selecciona una
                                            opción</option>
                                        <option value="01">Rural</option>
                                        <option value="02">Urbano</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="departamento" class="form-label">Departamento de residencia:</label>
                                    <select class="form-control" onchange="cargarMunicipios(this.value)"
                                        id="departamento" name="departamento" aria-invalid="false">

                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="municipio" class="form-label">Municipio de residencia:</label>
                                    <select class="form-control" id="municipio" name="municipio" aria-invalid="false">
                                        <option value=" ">Selecciona una opción</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="eps" class="form-label">EPS:</label>
                                    <select class="form-control" id="eps" name="eps" aria-invalid="false">

                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="observaciones" class="form-label">Observaciones :</label>
                                    <textarea class="form-control" id="observaciones" name="observaciones" rows="3"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane show active" id="justified-tabs-preview">
                            <ul class="nav nav-pills bg-nav-pills nav-justified mb-3">
                                <li class="nav-item">
                                    <a href="#acompanante" data-bs-toggle="tab" aria-expanded="true"
                                        class="nav-link rounded-0 active">

                                        <span class="d-none d-md-block"><i class="mdi mdi-account-circle me-1"></i>
                                            Información
                                            acompañante</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#anexos" data-bs-toggle="tab" aria-expanded="false"
                                        class="nav-link rounded-0 ">
                                        <span class="d-none d-md-block"><i class="mdi mdi-file-multiple me-1"></i>
                                            Anexos</span>
                                    </a>
                                </li>

                            </ul>

                            <div class="tab-content px-20">
                                <d.iv class="tab-pane show active" id="acompanante">
                                    <div class="row">
                                        <h5 class="mb-4 text-uppercase"><i class="mdi mdi-account-circle me-1"></i>
                                            Información
                                            acompañante</h5>
                                        <div class="row">
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    <label for="nombreAcompanante" class="form-label">Nombre :</label>
                                                    <input type="text" class="form-control" id="nombreAcompanante"
                                                        name="nombreAcompanante">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="parentesco" class="form-label">Parentesco :</label>
                                                    <select class="form-control" id="parentesco" name="parentesco"
                                                        aria-invalid="false">
                                                        <option value="">Selecciona una
                                                            opción</option>
                                                        <option value="padre">Padre</option>
                                                        <option value="madre">Madre</option>
                                                        <option value="hijo">Hijo/a</option>
                                                        <option value="hermano">Hermano/a</option>
                                                        <option value="abuelo">Abuelo/a</option>
                                                        <option value="tio">Tío/a</option>
                                                        <option value="primo">Primo/a</option>
                                                        <option value="sobrino">Sobrino/a</option>
                                                        <option value="nieto">Nieto/a</option>
                                                        <option value="otro">Otro</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="telefonoAcompanante" class="form-label">Teléfono :</label>
                                                    <input type="text" class="form-control" id="telefonoAcompanante"
                                                        name="telefonoAcompanante">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </d.iv>
                                <div class="tab-pane" id="anexos">
                                    <div class="box">
                                        <div class="box-header with-border">
                                            <div class="d-inline-block"></div>
                                            <div class="box-controls pull-right">
                                                <div class="box-header-actions" style="margin-top: -10px;">
                                                    <button onclick="agregarArchivo()" type="button"
                                                        class="waves-effect waves-light btn btn-info mb-5">
                                                        <li class="fa fa-plus"></li> Agregar archivos
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="row" style="margin-top: 20px" id="fileList">
                                                <div class="col-md-12">
                                                    <div class="form-group mt-4">
                                                        <input type="file" name="archivos[]" class="form-control"
                                                            placeholder="Seleccione un archivo">
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="listAnexos" style="display: none;">
                                                <hr />
                                                <h5 class="mb-3">Anexos agregados del paciente</h5>
                                                <div class="row" id="anexosAdd">


                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="box-footer text-end">
                            <button type="button" onclick="nuevoRegistro();" style="display: none;" id="newPaciente"
                                class="btn btn-primary-light me-1">
                                <i class="ti-plus "></i> Nuevo
                            </button>
                            <button type="button" id="cancelPacientes" onclick="cancelarPacientes();"
                                class="btn btn-primary-light me-1">
                                <i class="ti-close"></i> Cancelar
                            </button>
                            <button type="button" id="savePaciente" onclick="guardarPacientes();"
                                class="btn btn-primary">
                                <i class="ti-save"></i> Guardar
                            </button>
                        </div>
                </div>

                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <!-- MODAL VENTA DE SERVICIOS -->
    <div class="modal fade" id="modalVentaServicios" tabindex="-1" aria-labelledby="modalVentaServiciosLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content ">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalVentaServiciosLabel">Venta de servicios</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                </div>
                <div class="input-group input-group-merge d-flex justify-content-end p-3">
                    <button type="button" onclick="cancelarVentaServicio();" style="display: none;" id="btnRegresar"
                        class="btn btn-primary">
                        <i class="ti-arrow-left"></i> Regresar
                    </button>
                    <button type="button" onclick="nuevaVentaServicio();" id="newVentaServicio"
                        class="btn btn-primary">
                        <i class="ti-plus"></i> Nuevo servicio
                    </button>
                </div>
                <div class="modal-body">
                    <div id="listadoVentaServicios">
                        <table id="tablaServicios" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Descripción</th>
                                    <th>Tipo de Servicio</th>
                                    <th>Sesiones</th>
                                    <th>Fecha</th>
                                    <th>Valor</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="trRegistrosServicios">


                            </tbody>
                        </table>
                        <div id="pagination-links-servicios"></div>
                    </div>
                    <div id="VentaServicio" style="display: none;">
                        <div class="text-center mb-4">
                            <h3 class="text-primary">Seleccione el servicio a vender</h3>
                            <p class="text-muted">Escoja el tipo de servicio que desea registrar</p>
                        </div>
                        <div class="row g-4">
                            <!-- Card Consulta -->
                            <div class="col-lg-3 col-sm-6">
                                <div class="card service-card">
                                    <div class="card-body">
                                        <div class="icon-circle bg-primary-light">
                                            <i class="mdi mdi-stethoscope text-primary" style="font-size: 28px;"></i>
                                        </div>
                                        <div class="service-description">
                                            <h6 class="card-title">Venta de consulta</h6>
                                            <p class="text-muted small">Gestione consultas individuales</p>
                                        </div>
                                        <button class="btn btn-primary btn-sm"
                                            onclick="seleccionarServicio('consulta')">Seleccionar</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Card Sesión -->
                            <div class="col-lg-3 col-sm-6">
                                <div class="card service-card">
                                    <div class="card-body">
                                        <div class="icon-circle bg-success-light">
                                            <i class="mdi mdi-calendar-check text-success" style="font-size: 28px;"></i>
                                        </div>
                                        <div class="service-description">
                                            <h6 class="card-title">Venta de sesión</h6>
                                            <p class="text-muted small">Registre sesiones de terapia</p>
                                        </div>
                                        <button class="btn btn-success btn-sm"
                                            onclick="seleccionarServicio('sesion')">Seleccionar</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Card Paquete -->
                            <div class="col-lg-3 col-sm-6">
                                <div class="card service-card">
                                    <div class="card-body">
                                        <div class="icon-circle bg-warning-light">
                                            <i class="mdi mdi-package text-warning" style="font-size: 28px;"></i>
                                        </div>
                                        <div class="service-description">
                                            <h6 class="card-title">Venta de paquete</h6>
                                            <p class="text-muted small">Gestione paquetes completos</p>
                                        </div>
                                        <button class="btn btn-warning btn-sm"
                                            onclick="seleccionarServicio('paquete')">Seleccionar</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Card Prueba -->
                            <div class="col-lg-3 col-sm-6">
                                <div class="card service-card">
                                    <div class="card-body">
                                        <div class="icon-circle bg-info-light">
                                            <i class="mdi mdi-test-tube text-info" style="font-size: 28px;"></i>
                                        </div>
                                        <div class="service-description">
                                            <h6 class="card-title">Venta de prueba</h6>
                                            <p class="text-muted small">Registre pruebas diagnósticas</p>
                                        </div>
                                        <button class="btn btn-info btn-sm"
                                            onclick="seleccionarServicio('prueba')">Seleccionar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="formVentaServicio" style="display: none;">
                        <!-- FORMULARIO DE VENTA DE SERVICIO CONSULTA -->
                        <form id="formVentaConsulta" style="display: none;">
                            <input type="hidden" id="idConsultaVenta" name="idConsultaVenta" />
                            <input type="hidden" id="accHistoriaVenta" name="accHistoriaVenta" />
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="consultaVenta" class="form-label">Descripción de la
                                            consulta:</label>
                                        <select class="form-control" onchange="cargarPrecioConsulta(this)"
                                            id="consultaVenta" name="consultaVenta">
                                            <option value="">Seleccione la consulta</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="fechaVentaConsulta" class="form-label">Fecha:</label>
                                    <div class="input-group">
                                        <input type="date" class="form-control" id="fechaVentaConsulta"
                                            name="fechaVentaConsulta"
                                            placeholder="Seleccione la fecha de venta consulta" />
                                        <input type="time" id="horaSeleccionadaVentaConsulta"
                                            name="horaSeleccionadaVentaConsulta" class="form-control">
                                        <div class="input-group-addon">
                                            <i class="fa fa-clock-o"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="valorServConsultaVis" class="form-label">Valor :</label>
                                        <input type="text" class="form-control" id="valorServConsultaVis"
                                            name="valorServConsultaVis" placeholder="$ 0,00"
                                            onchange="cambioFormato(this.id);" onkeypress="return validartxtnum(event)"
                                            onclick="this.select();">
                                        <input type="hidden" class="form-control" id="valorServConsulta"
                                            name="valorServConsulta">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="tipoServicioConsulta" class="form-label">Servicio vendido para:</label>
                                        <select class="form-control" id="tipoServicioConsulta" name="tipoServicioConsulta">
                                            <option value="">Seleccione el tipo de servicio</option>
                                            <option value="PSICOLOGÍA">PSICOLOGÍA</option>
                                            <option value="NEUROPSICOLOGÍA">NEUROPSICOLOGÍA</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="box-footer text-end mt-3">
                                    <button onclick="cancelarVentaConsulta()" type="button"
                                        class="btn btn-primary-light me-1">
                                        <i class="ti-share-alt"></i> Cancelar
                                    </button>
                                    <button onclick="guardarVentaConsulta()" id="saveRegistroVentaConsulta"
                                        type="button" class="btn btn-primary">
                                        <i class="ti-save"></i> Guardar
                                    </button>
                                </div>
                            </div>
                        </form>
                        <!-- FORMULARIO DE VENTA DE SERVICIO SESSION -->
                        <form id="formVentaSesion" style="display: none;">
                            <input type="hidden" id="accHistoriaVentaSesion" value=""
                                name="accHistoriaVentaSesion" />
                            <input type="hidden" id="idVentaSesion" value="" name="idVentaSesion" />

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="sugerenciasModal" class="form-label">Descripción de la
                                            sesión:</label>
                                        <select class="form-control" id="sesionVenta" name="sesionVenta">
                                            <option value="">Seleccione la sesión</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="fechaVentaSesion" class="form-label">Fecha:</label>
                                    <div class="input-group">
                                        <input type="date" class="form-control" id="fechaVentaSesion"
                                            name="fechaVentaSesion"
                                            placeholder="Seleccione la fecha de venta de la sesión" />
                                        <input type="time" id="horaSeleccionadaVentaSesion"
                                            name="horaSeleccionadaVentaSesion" class="form-control">
                                        <div class="input-group-addon">
                                            <i class="fa fa-clock-o"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="valorServSesionVis" class="form-label">Valor :</label>
                                        <input type="text" class="form-control" id="valorServSesionVis"
                                            name="valorServSesionVis" placeholder="$ 0,00" value=""
                                            onchange="cambioFormato(this.id);" onkeypress="return validartxtnum(event)"
                                            onclick="this.select();">
                                        <input type="hidden" class="form-control" id="valorServSesion"
                                            name="valorServSesion">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="tipoServicioSesion" class="form-label">Servicio vendido para:</label>
                                        <select class="form-control" id="tipoServicioSesion" name="tipoServicioSesion">
                                            <option value="">Seleccione el tipo de servicio</option>
                                            <option value="PSICOLOGÍA">PSICOLOGÍA</option>
                                            <option value="NEUROPSICOLOGÍA">NEUROPSICOLOGÍA</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="box-footer text-end mt-3">
                                    <button onclick="cancelarVentaSesion()" type="button"
                                        class="btn btn-primary-light me-1">
                                        <i class="ti-share-alt"></i> Cancelar
                                    </button>
                                    <button onclick="guardarVentaSesion()" type="button" id="saveRegistroVentaSesion"
                                        class="btn btn-primary">
                                        <i class="ti-save"></i> Guardar
                                    </button>
                                </div>
                            </div>
                        </form>
                        <!-- FORMULARIO DE VENTA DE SERVICIO PAQUETE -->
                        <form id="formVentaPaquete" style="display: none;">
                            <input type="hidden" name="accVentaPaquete" id="accVentaPaquete" value="guardar" />
                            <input type="hidden" name="idVentaPaquete" id="idVentaPaquete" value="" />
                            <div class="row">
                                <div class="col-md-9">
                                    <div class="form-group">
                                        <label for="descripcion" class="form-label">Paquete :</label>
                                        <select class="form-control select2" onchange="seleccionarPaquete(this);"
                                            id="selPaquete" name="selPaquete" aria-invalid="false">

                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="fechaPaquete" class="form-label">Fecha :</label>
                                        <input type="date" class="form-control" id="fechaPaquete"
                                            name="fechaPaquete">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="precioSesionVis" class="form-label">Valor :</label>
                                        <input type="text" class="form-control" id="precioSesionVis"
                                            name="precioSesionVis" onchange="cambioFormato(this.id);"
                                            onkeypress="return validartxtnum(event)" onclick="this.select();">
                                        <input type="hidden" class="form-control" id="precioSesion"
                                            name="precioSesion">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="numSesiones" class="form-label">Numero de sesiones :</label>
                                        <input type="number" class="form-control" id="numSesiones" name="numSesiones"
                                            onchange="calValorMonto()" max="20" min="1"
                                            onkeypress="return validartxtnum(event)">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="montoFinal" class="form-label">Valor :</label>
                                        <input type="text" readonly class="form-control" id="montoFinalVis"
                                            name="montoFinalVis" value="$ 0,00">
                                        <input type="hidden" class="form-control" id="montoFinal" name="montoFinal">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="tipoServicioPaquete" class="form-label">Servicio vendido para:</label>
                                        <select class="form-control" id="tipoServicioPaquete" name="tipoServicioPaquete">
                                            <option value="">Seleccione el tipo de servicio</option>
                                            <option value="PSICOLOGÍA">PSICOLOGÍA</option>
                                            <option value="NEUROPSICOLOGÍA">NEUROPSICOLOGÍA</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="box-footer text-end">
                                    <button type="button" id="cancelRegistroPaq" onclick="cancelarVentaPaquete();"
                                        class="btn btn-primary-light me-1">
                                        <i class="ti-close"></i> Cancelar
                                    </button>
                                    <button type="button" id="saveRegistroPaq" onclick="guardarPaquetes();"
                                        class="btn btn-primary">
                                        <i class="ti-save"></i> Guardar
                                    </button>
                                </div>

                            </div>
                        </form>
                        <!-- FORMULARIO DE VENTA DE SERVICIO PRUEBA -->
                        <form id="formVentaPrueba" style="display: none;">
                            <input type="hidden" id="idVentaPrueba" name="idVentaPrueba" />
                            <input type="hidden" id="accVentaPrueba" name="accVentaPrueba" />
                            <div class="row">
                                <div class="col-md-9">
                                    <div class="form-group">
                                        <label for="selPrueba" class="form-label">Prueba :</label>
                                        <select class="form-control select2" onchange="seleccionarPrueba(this);"
                                            id="selPrueba" name="selPrueba" aria-invalid="false">
    
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="fechaPrueba" class="form-label">Fecha :</label>
                                        <input type="date" class="form-control" id="fechaPrueba" name="fechaPrueba">
                                    </div>
                                </div>
                                <div class="col-md-12" style="display: none;">
                                    <div class="form-group">
                                        <label for="descripcion" class="form-label">Descripción :</label>
                                        <textarea class="form-control" id="descripcionPrueba" name="descripcionPrueba"></textarea>
                                    </div>
    
                                </div>
                             
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="precioPruebaVis" class="form-label">Valor :</label>
                                        <input type="text" class="form-control" id="precioPruebaVis"
                                            name="precioPruebaVis" onchange="cambioFormato(this.id);"
                                            onkeypress="return validartxtnum(event)" onclick="this.select();">
                                        <input type="hidden" class="form-control" id="precioPrueba"
                                            name="precioPrueba">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="tipoServicioPrueba" class="form-label">Servicio vendido para:</label>
                                        <select class="form-control" id="tipoServicioPrueba" name="tipoServicioPrueba">
                                            <option value="">Seleccione el tipo de servicio</option>
                                            <option value="PSICOLOGÍA">PSICOLOGÍA</option>
                                            <option value="NEUROPSICOLOGÍA">NEUROPSICOLOGÍA</option>
                                        </select>
                                    </div>
                                </div>
    
    
                                <div class="box-footer text-end">
                                    <button type="button" id="cancelRegistroPrueba" onclick="cancelarPrueba();"
                                        class="btn btn-primary-light me-1">
                                        <i class="ti-close"></i> Cancelar
                                    </button>
                                    <button type="button" id="saveRegistroPrueba" onclick="guardarPruebas();"
                                        class="btn btn-primary">
                                        <i class="ti-save"></i> Guardar
                                    </button>
                                </div>
    
                            </div>

                        
                        </form>

                    </div>


                </div>


            </div>
        </div>
    </div>


    <script>
        window.userPermissions = @json(Auth::user()->permissions);
        document.addEventListener("DOMContentLoaded", function() {
            let menuP = document.getElementById("principalPacientes")

            menuP.classList.add("active")

            loader = document.getElementById('loader')
            loadNow(1)


            // $('#departamento').select2({
            //     dropdownParent: $('#modalPacientes'),
            //     width: '100%'
            // });

            $("#departamento").on("change", function() {
                $(this).valid(); // Dispara la validación cuando cambie el valor
            });

            // $('#eps').select2({
            //     dropdownParent: $('#modalPacientes'),
            //     width: '100%'
            // });

            // $('#municipio').select2({
            //     dropdownParent: $('#modalPacientes'),
            //     width: '100%',
            //     placeholder: "Selecciona una opción"
            // });

            $('#modalPacientes').on('shown.bs.modal', function() {
                $(document).off('focusin.modal');
            });


            // Detectar cuando el usuario suelta el clic sobre el select
            // $('#eps').on('mouseup', function() {
            //     $(this).select2('open');
            // });

            localStorage.clear()

            $('[data-mask]').inputmask()
            $.validator.addMethod("dateFormat", function(value, element) {
                // Verificar si la fecha está en el formato yyyy-mm-dd
                var dateParts = value.split("-")
                if (dateParts.length === 3) {
                    var year = parseInt(dateParts[0], 10)
                    var month = parseInt(dateParts[1], 10)
                    var day = parseInt(dateParts[2], 10)

                    // Comprobar si es una fecha válida
                    var date = new Date(year, month - 1, day)
                    return date && (date.getFullYear() === year) && (date.getMonth() === month - 1) && (date
                        .getDate() === day)
                }
                return false
            }, "Por favor, ingresa una fecha válida en formato yyyy-mm-dd.")


            $.validator.addMethod("maxDate", function(value, element) {
                // Dividir la fecha en partes según el formato yyyy-mm-dd
                var dateParts = value.split("-")
                if (dateParts.length === 3) {
                    var year = parseInt(dateParts[0], 10)
                    var month = parseInt(dateParts[1], 10)
                    var day = parseInt(dateParts[2], 10)

                    // Convertir a objeto Date
                    var inputDate = new Date(year, month - 1, day)
                    var today = new Date() // Fecha actual sin hora

                    // Asegurarse de que las horas, minutos y segundos no afecten la comparación
                    today.setHours(0, 0, 0, 0)

                    // Validar que la fecha de entrada no sea mayor a hoy
                    return inputDate <= today
                }
                return false
            }, "La fecha no puede ser mayor a hoy.")

            $.validator.setDefaults({
                ignore: []
            });

            $.validator.addMethod("select2Required", function(value, element) {

                return value && value !== "";

            }, "Este campo es obligatorio.");

            $("#formPaciente").validate({
                rules: {
                    tipoId: {
                        required: true
                    },
                    identificacion: {
                        required: true,
                        remote: {
                            url: "/verificar-identificacion", // URL para verificar
                            type: "post",
                            data: {
                                identificacion: function() {
                                    return $("#identificacion").val()
                                },
                                tipoId: function() {
                                    return $("#tipoId").val() // Tipo de identificación
                                },
                                id: function() {
                                    return $("#idPaciente").val() || null // Enviar id si es edición
                                },
                                _token: function() {
                                    return "{{ csrf_token() }}" // Token CSRF para seguridad
                                }
                            }
                        },
                        minlength: 5,
                        maxlength: 20
                    },

                    primerNombre: {
                        required: true
                    },
                    primerApellido: {
                        required: true
                    },
                    tipoUsuario: {
                        select2Required: true
                    },
                    sexo: {
                        required: true
                    },
                    zonaResidencial: {
                        required: true
                    },
                    departamento: {
                        select2Required: true
                    },
                    municipio: {
                        select2Required: true
                    },
                    email: {
                        required: true,
                        email: true
                    },
                    telefono: {
                        required: true,
                        digits: true,
                        minlength: 7
                    },
                    fechaNacimiento: {
                        required: true,
                        dateFormat: true,
                        maxDate: true
                    },
                },
                messages: {
                    tipoId: {
                        required: "Por favor, selecciona un tipo de identificación."
                    },
                    identificacion: {
                        required: "Por favor, ingresa una identificación.",
                        remote: "Esta identificación ya está registrada.",
                        minlength: "La identificación debe tener al menos 5 caracteres.",
                        maxlength: "La identificación no puede exceder los 20 caracteres."
                    },
                    primerNombre: {
                        required: "Por favor, ingresa el primer nombre."
                    },
                    primerApellido: {
                        required: "Por favor, ingresa el primer apellido."
                    },
                    tipoUsuario: {
                        select2Required: "Por favor, seleccione el tipo de usuario."
                    },
                    sexo: {
                        required: "Por favor, seleccione el sexo."
                    },
                    zonaResidencial: {
                        required: "Por favor, seleccione la zona de residencia ."
                    },
                    departamento: {
                        select2Required: "Por favor, seleccione el departamento de residencia ."
                    },
                    municipio: {
                        select2Required: "Por favor, seleccione el municipio de residencia ."
                    },
                    email: {
                        required: "Por favor, ingresa un email.",
                        email: "Por favor, ingresa un email válido."
                    },
                    telefono: {
                        required: "Por favor, ingresa un número de teléfono.",
                        digits: "Por favor, ingresa solo dígitos.",
                        minlength: "El número de teléfono debe tener al menos 7 dígitos."
                    },
                    fechaNacimiento: {
                        required: "Por favor, ingresa tu fecha de nacimiento.",
                        dateFormat: "Por favor, ingresa una fecha válida en formato dd/mm/yyyy.",
                        maxDate: "La fecha de nacimiento no puede ser mayor que hoy."
                    },
                },
                submitHandler: function(form) {
                    guardarPacientes()
                }
            });

            //VALIDAR FORMULARIO VENTA CONSULTA
            $("#formVentaConsulta").validate({
                rules: {
                    consultaVenta: {
                        required: true
                    },
                    fechaVentaConsulta: {
                        required: true
                    },
                    valorServConsultaVis: {
                        required: true
                    }


                },
                messages: {
                    consultaVenta: {
                        required: "Por favor, ingrese la descripción de la consulta"
                    },
                    fechaVentaConsulta: {
                        required: "Por favor, seleccione una fecha"
                    },
                    valorServConsultaVis: {
                        required: "Por favor, ingrese el valor de la consulta"
                    }

                },
                submitHandler: function(form) {
                    guardarVentaConsulta()
                }
            });

            //VALIDAR FORMULARIO VENTA SESION
            $("#formVentaSesion").validate({
                rules: {
                    sesionVenta: {
                        required: true
                    },
                    fechaVentaSesion: {
                        required: true
                    },
                    valorServSesionVis: {
                        required: true
                    }
                },
                messages: {
                    sesionVenta: {
                        required: "Por favor, seleccione la sesión"
                    },
                    fechaVentaSesion: {
                        required: "Por favor, seleccione una fecha"
                    },
                    valorServSesionVis: {
                        required: "Por favor, ingrese el valor de la sesión"
                    }

                },
                submitHandler: function(form) {
                    guardarVentaSesion()
                }
            });

            //VALIDAR FORMULARIO DE PAQUETES

            $("#formVentaPaquete").validate({
                rules: {
                    selPaquete: {
                        required: true
                    },
                    fechaPaquete: {
                        required: true
                    },
                    precioSesion: {
                        required: true
                    },
                    numSesiones: {
                        required: true
                    }

                },
                messages: {
                    selPaquete: {
                        required: "Por favor, seleccione un paquete"
                    },
                    fechaPaquete: {
                        required: "Por favor, seleccione una fecha"
                    },
                    precioSesion: {
                        required: "Por favor, ingrese el valor de la sesión"
                    },
                    numSesiones: {
                        required: "Por favor, ingrese el número de sesiones"
                    }
                },
                submitHandler: function(form) {
                    guardarPaquetes()
                }
            });

                //VALIDAR FORMULARIO DE PRUEBAS

                $("#formVentaPrueba").validate({
                    rules: {
                        selPrueba: {
                            required: true
                        },
                        fechaPrueba: {
                            required: true
                        },
                        precioPruebaVis: {
                            required: true
                        }

                    },
                    messages: {
                        selPrueba: {
                            required: "Por favor, seleccione una prueba"
                        },
                        fechaPrueba: {
                            required: "Por favor, seleccione una fecha"
                        },
                        precioPruebaVis: {
                            required: "Por favor, ingrese el valor de la prueba"
                        }
                    },
                    submitHandler: function(form) {
                        guardarPruebas()
                    }
                });

            cargarPacientes(1)
            cargarDepartamento()
            cargarTipoUsuario()
            cargarEps()

            document.getElementById('account-upload').addEventListener('change', function(event) {
                const file = event.target.files[0]
                const previewImage = document.getElementById('previewImage')

                if (file) {
                    const imageUrl = URL.createObjectURL(file)
                    previewImage.src = imageUrl
                }
            })

            // Evento click para la paginación
            document.addEventListener('click', function(event) {
                if (event.target.matches('.pagination a')) {
                    event.preventDefault()
                    var href = event.target.getAttribute('href')
                    var page = href.split('page=')[1]

                    // Asegurarse de que 'page' sea un número antes de hacer la solicitud
                    if (!isNaN(page)) {
                        cargarPacientes(page)
                    }
                }
            })
            // Evento input para el campo de búsqueda
            document.getElementById('busqueda').addEventListener('input', function() {
                var searchTerm = this.value
                cargarPacientes(1,
                    searchTerm)
            })

            const fechaNacimiento = document.getElementById("fechaNacimiento")


            fechaNacimiento.addEventListener("change", validarIdentificacionPorEdad)
            fechaNacimiento.addEventListener("input", validarIdentificacionPorEdad)
            fechaNacimiento.addEventListener("blur", validarIdentificacionPorEdad)
            document.getElementById("tipoId").addEventListener("change", validarIdentificacionPorEdad)

        })

        function hasPermission(permission) {
            return window.userPermissions && window.userPermissions.includes(permission);
        }

        function seleccionarPrueba(element) {
                let selectedOption = element.options[element.selectedIndex];
                let valor = selectedOption.getAttribute("data-valor");
                document.getElementById("precioPrueba").value = valor
                document.getElementById("precioPruebaVis").value = formatCurrency(valor, 'es-CO', 'COP')
            }

        function agregarArchivo() {
            // Crear un nuevo contenedor para el campo de entrada
            const newFileContainer = document.createElement('div')
            newFileContainer.classList.add('col-md-12', 'mt-1')

            // Crear el nuevo campo de entrada
            const newFileInput = document.createElement('input')
            newFileInput.type = 'file'
            newFileInput.name = 'archivos[]'
            newFileInput.classList.add('form-control')
            // Agregar el campo de entrada al contenedor
            newFileContainer.appendChild(newFileInput)

            // Agregar el contenedor al div con ID fileList
            document.getElementById('fileList').appendChild(newFileContainer)
        }

        function validarIdentificacionPorEdad() {
            const tipoId = document.getElementById("tipoId").value
            const fechaNacimiento = document.getElementById("fechaNacimiento").value

            if (!fechaNacimiento) return // Si no hay fecha de nacimiento, salir

            const hoy = new Date()
            const nacimiento = new Date(fechaNacimiento.split('/').reverse().join('-')) // Convertir a formato ISO

            // Cálculo inicial de años, meses y días
            let anios = hoy.getFullYear() - nacimiento.getFullYear()
            let meses = hoy.getMonth() - nacimiento.getMonth()
            let dias = hoy.getDate() - nacimiento.getDate()

            // Ajustar si los meses o días son negativos
            if (meses < 0 || (meses === 0 && dias < 0)) {
                anios--
                meses += 12
            }
            if (dias < 0) {
                const ultimoDiaMesAnterior = new Date(hoy.getFullYear(), hoy.getMonth(), 0).getDate();
                dias += ultimoDiaMesAnterior
                meses--
            }

            // Crear la cadena de edad en el formato deseado
            const edad =
                `${anios} ${anios === 1 ? 'Año' : 'Años'}, ${meses} ${meses === 1 ? 'Mes' : 'Meses'} y ${dias} ${dias === 1 ? 'Día' : 'Días'}`;

            let tiposPermitidos = []

            // Determinar los tipos de documento permitidos según la edad
            if (anios <= 6) {
                tiposPermitidos = ["RC", "NV", "PT", "CD", "PE", "MS"]
            } else if (anios >= 7 && anios <= 17) {
                tiposPermitidos = ["TI", "CE", "PT", "CD", "PE", "MS"]
            } else if (anios >= 18) {
                tiposPermitidos = ["CC", "TI", "CE", "PT", "CD", "PE", "AS"]
            }

            // Verificar si el tipo de identificación es válido
            if (!tiposPermitidos.includes(tipoId)) {
                document.getElementById('fechaNacimiento').value = ""
                document.getElementById('edad').value = ""
                swal("¡Alerta!", `El tipo de identificación "${tipoId}" no corresponde con la edad de ${edad}.`, "warning");
            } else {
                document.getElementById('edad').value = edad
            }
        }



        function guardarPacientes() {

            if ($("#formPaciente").valid()) {

                
                const formPaciente = document.getElementById('formPaciente')
                const formData = new FormData(formPaciente)

                const url = "{{ route('form.guardarPaciente') }}"

                fetch(url, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {

                        if (data.success = 'success') {

                            swal(data.title, data.message, data.success)

                            document.getElementById('savePaciente').setAttribute('disabled', 'disabled')
                            document.getElementById('newPaciente').style.display = 'initial'
                            document.getElementById('cancelPacientes').style.display = 'none'

                            cargarPacientes(1)
                            document.getElementById("accPacientes").value = "guardar"

                        } else {
                            swal(data.title, data.message, data.success)
                        }
                    })
                    .catch(error => {
                        console.error("Error al enviar los datos:", error)
                    })

            }
        }

        function editarPaciente(idPaciente) {
            var modal = new bootstrap.Modal(document.getElementById("modalPacientes"), {
                backdrop: 'static',
                keyboard: false
            })
            document.getElementById("idPaciente").value = idPaciente
            document.getElementById("accPacientes").value = 'editar'
            document.getElementById('savePaciente').removeAttribute('disabled')

            document.getElementById('listAnexos').style.display = 'none'
            document.getElementById('fileList').innerHTML = ""
            document.getElementById('anexosAdd').innerHTML = ""
            agregarArchivo()

            modal.show()
            cargarDepartamento()

            let url = "{{ route('pacientes.buscaPaciente') }}"

            fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        idPaciente: idPaciente
                    })
                })
                .then(response => response.json())
                .then(data => {

                    var foto = data.paciente.foto
                    $("#fotoCargada").val(foto)
                    const previewImage = document.getElementById('previewImage');
                    let url = $('#Ruta').data("ruta")
                    previewImage.src = url + "/images/FotosPacientes/" + foto

                    document.getElementById("tipoId").value = data.paciente.tipo_identificacion
                    document.getElementById("identificacion").value = data.paciente.identificacion

                    document.getElementById("fechaNacimiento").value = data.paciente.fecha_nacimiento
                    validarIdentificacionPorEdad()

                    document.getElementById("lugarNacimiento").value = data.paciente.lugar_nacimiento
                    document.getElementById("primerNombre").value = data.paciente.primer_nombre
                    document.getElementById("segundoNombre").value = data.paciente.segundo_nombre
                    document.getElementById("primerApellido").value = data.paciente.primer_apellido
                    document.getElementById("segundoApellido").value = data.paciente.segundo_apellido
                    document.getElementById("sexo").value = data.paciente.sexo
                    document.getElementById("estadocivil").value = data.paciente.estado_civil
                    document.getElementById("ocupacion").value = data.paciente.ocupacion
                    document.getElementById("tipoUsuario").value = data.paciente.tipo_usuario
                    document.getElementById("lateralidad").value = data.paciente.lateralidad
                    document.getElementById("religion").value = data.paciente.religion
                    document.getElementById("email").value = data.paciente.email
                    document.getElementById("telefono").value = data.paciente.telefono
                    document.getElementById("direccion").value = data.paciente.direccion
                    $('#departamento').val(data.paciente.departamento).trigger('change.select2')
                    $('#municipio').val(data.paciente.municipio).trigger('change.select2')
                    document.getElementById("zonaResidencial").value = data.paciente.zona_residencial

                    $('#eps').val(data.paciente.eps).trigger('change.select2')

                    document.getElementById("observaciones").value = data.paciente.observaciones
                    document.getElementById("nombreAcompanante").value = data.paciente.acompanante
                    document.getElementById("parentesco").value = data.paciente.parentesco
                    document.getElementById("telefonoAcompanante").value = data.paciente.telefono_acompanate

                    if (data.anexos.length > 0) {
                        document.getElementById('listAnexos').style.display = 'initial'

                        let anexos = document.getElementById('anexosAdd')
                        let listAnexos = ""

                        data.anexos.forEach(anexo => {
                            let parTipo = anexo.tipo_archivo.split('/')
                            listAnexos = `<div class="col-xl-6">
                                                        <div class="card mb-1 shadow-none border">
                                                            <div class="p-2">
                                                                <div class="row align-items-center">
                                                                    <div class="col-auto">
                                                                        <div class="p-10 bg-primary-light text-primary rounded">
                                                                            .${parTipo[1].toUpperCase()}
                                                                        </div>
                                                                    </div>
                                                                    <div class="col ps-0">
                                                                        <a href="javascript:verArchivo('${anexo.url}');" class="text-muted fw-500">${anexo.nombre_archivo}</a>
                                                                        <p class="mb-0">${formatearTamano(anexo.peso)}</p>
                                                                    </div>
                                                                    <div class="col-auto">
                                                                        <!-- Button -->
                                                                        <a href="javascript:verArchivo('${anexo.url}');" class="p-10 fs-18 link">
                                                                            <i class="fa fa-download"></i>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>`
                            anexos.innerHTML += listAnexos
                        })

                    }

                })
                .catch(error => console.error('Error:', error))

        }

        function verArchivo(url) {
            // Validar que la URL no esté vacía
            if (!url) {
                console.error("La URL proporcionada es inválida.");
                return;
            }
            let rtotal = $("#RutaTotal").data("ruta")
            // Abrir el archivo en una nueva pestaña
            window.open(`${rtotal}anexosPacientes/${url}`, '_blank');
        }

        function formatearTamano(kb) {
            if (kb < 1024) {
                return `${kb} KB`;
            } else {
                const mb = kb / 1024;
                return `${mb.toFixed(2)} MB`;
            }
        }

        function convertirFecha(fecha) {
            // Dividir la fecha en año, mes y día
            const [año, mes, dia] = fecha.split('-')

            // Formatear la fecha en el formato dd/mm/yyyy
            const fechaFormateada = `${dia.padStart(2, '0')}/${mes.padStart(2, '0')}/${año}`;

            return fechaFormateada
        }

        function mostrarListado() {
            let listado = document.getElementById("listado")
            let formulario = document.getElementById("formulario")

            listado.style.display = "block"
            formulario.style.display = "none"
        }

        function cancelarPacientes() {
            const formPaciente = document.getElementById('formPaciente')
            formPaciente.reset()
            document.getElementById("municipio").innerHTML = "<option value="
            ">Selecciona una opción</option>"
            document.getElementById('listAnexos').style.display = 'none'
            document.getElementById('fileList').innerHTML = ""
            agregarArchivo()
        }

        function nuevoRegistro() {
            var modal = new bootstrap.Modal(document.getElementById("modalPacientes"), {
                backdrop: 'static',
                keyboard: false
            })

            modal.show()
            cancelarPacientes()

            document.getElementById('savePaciente').removeAttribute('disabled')
            document.getElementById('newPaciente').style.display = 'none'
            document.getElementById('cancelPacientes').style.display = 'initial'
            document.getElementById("accPacientes").value = "guardar"

        }

        function eliminarPaciente(idPac) {

            swal({
                title: "Esta seguro de eliminar este paciente ?",
                text: "¡No podrás revertir esto!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Si, eliminar!",
                cancelButtonText: "Cancelar",
                confirmButtonClass: "btn btn-warning",
                cancelButtonClass: "btn btn-danger ml-1",
                buttonsStyling: false
            }, function(isConfirm) {
                if (isConfirm) {
                    let url = "{{ route('pacientes.eliminarPac') }}"
                    fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content')
                            },
                            body: JSON.stringify({
                                idPaciente: idPac
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                swal("¡Buen trabajo!",
                                    data.message,
                                    "success")
                                cargarPacientes(1)
                            } else {
                                swal("¡Alerta!",
                                    "La operación fue realizada exitosamente",
                                    data.message,
                                    "success");
                            }
                        })

                } else {
                    swal("Cancelado", "Tu registro esta salvo :)", "error")
                }
            })
        }

        function cargarDepartamento() {
            return new Promise((resolve, reject) => {
                let select = document.getElementById("departamento")
                let selectMun = document.getElementById("municipio")
                let url = "{{ route('pacientes.departamentos') }}"

                let defaultOption = document.createElement("option")
                defaultOption.value = "" // Valor en blanco
                defaultOption.text = "Selecciona una opción" // Texto que se mostrará
                defaultOption.selected = true // Que aparezca seleccionada por defecto
                select.appendChild(defaultOption)

                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(departamento => {
                            let option = document.createElement("option")
                            option.value = departamento.codigo
                            option.text = departamento.nombre
                            select.appendChild(option)
                        })
                        resolve() // Resuelve la promesa cuando los datos han sido cargados
                    })
                    .catch(error => {
                        console.error('Error:', error)
                        reject(error) // Rechaza la promesa si ocurre un error
                    })
            })
        }

        function cargarTipoUsuario() {
            return new Promise((resolve, reject) => {
                let select = document.getElementById("tipoUsuario")
                select.innerHTML = ""
                let url = "{{ route('pacientes.tipoUSuario') }}"

                let defaultOption = document.createElement("option")
                defaultOption.value = "" // Valor en blanco
                defaultOption.text = "Selecciona una opción" // Texto que se mostrará
                defaultOption.selected = true // Que aparezca seleccionada por defecto
                select.appendChild(defaultOption)

                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(tipo => {
                            let option = document.createElement("option")
                            option.value = tipo.id
                            option.text = tipo.descripcion
                            select.appendChild(option)
                        })
                        resolve() // Resuelve la promesa cuando los datos han sido cargados
                    })
                    .catch(error => {
                        console.error('Error:', error)
                        reject(error) // Rechaza la promesa si ocurre un error
                    })
            })
        }

        function cargarEps() {
            return new Promise((resolve, reject) => {
                let select = document.getElementById("eps")
                select.innerHTML = ""
                let url = "{{ route('pacientes.eps') }}"        

                let defaultOption = document.createElement("option")
                defaultOption.value = "" // Valor en blanco
                defaultOption.text = "Selecciona una opción" // Texto que se mostrará
                defaultOption.disabled = true // Deshabilitar para que no pueda ser seleccionada
                defaultOption.selected = true // Que aparezca seleccionada por defecto
                select.appendChild(defaultOption)

                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(eps => {
                            let option = document.createElement("option")
                            option.value = eps.id
                            option.text = `${eps.entidad}`
                            select.appendChild(option)
                        })
                        resolve() // Resuelve la promesa cuando los datos han sido cargados
                    })
                    .catch(error => {
                        console.error('Error:', error)
                        reject(error) // Rechaza la promesa si ocurre un error
                    })
            })
        }


        function cargarMunicipios(muni) {
            return new Promise((resolve, reject) => {
                let select = document.getElementById("municipio")
                select.innerHTML = ""
                let url = "{{ route('pacientes.municipio') }}"

                let defaultOption = document.createElement("option")
                defaultOption.value = "" // Valor en blanco
                defaultOption.text = "Selecciona una opción" // Texto que se mostrará
                defaultOption.disabled = true // Deshabilitar para que no pueda ser seleccionada
                defaultOption.selected = true // Que aparezca seleccionada por defecto
                select.appendChild(defaultOption)

                fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        body: JSON.stringify({
                            idMuni: muni
                        })
                    })
                    .then(response => response.json())
                    .then(data => {

                        data.forEach(municipios => {
                            let option = document.createElement("option")
                            option.value = municipios.codigo
                            option.text = municipios.nombre
                            select.appendChild(option)
                        })
                        resolve() // Resuelve la promesa cuando los datos han sido cargados
                    })
                    .catch(error => {
                        console.error('Error:', error)
                        reject(error) // Rechaza la promesa si ocurre un error
                    })
            })
        }

        function clearImage() {
            const previewImage = document.getElementById('previewImage')
            let url = $('#Ruta').data("ruta")
            previewImage.src = url + "/images/FotosPacientes/default.jpg"
        }

        function cargarPacientes(page, searchTerm = '') {


            let url = "{{ route('pacientes.listaPacientes') }}" // Definir la URL

            // Eliminar los campos ocultos anteriores
            var oldPageInput = document.getElementById('page')
            var oldSearchTermInput = document.getElementById('searchTerm')
            if (oldPageInput) oldPageInput.remove()
            if (oldSearchTermInput) oldSearchTermInput.remove()

            var data = {
                page: page,
                search: searchTerm
            }

            // Limpiar la tabla antes de cargar nuevos datos


            fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(responseData => {
                    // Rellenar la tabla con las filas generadas
                    document.getElementById('trRegistros').innerHTML = responseData.pacientes
                    feather.replace()
                    // Colocar los enlaces de paginación
                    document.getElementById('pagination-links').innerHTML = responseData.links
                    loadNow(0)
                })
                .catch(error => console.error('Error:', error))

        }

        function goHistoriaPsicologia(element) {
            if (hasPermission('histPsicologia')) {
                let idPaciente = element.getAttribute("data-id")
                let edadPaciente = element.getAttribute("data-edad")
                localStorage.clear()
                localStorage.setItem('idPaciente', idPaciente)
                localStorage.setItem('edadPaciente', edadPaciente)
                let prascaURL = '{{ url('/pacientes/historiaPsicologica') }}'
                const nuevaPestana = window.open(prascaURL, '_blank')
                nuevaPestana.focus()
            } else {
                swal("¡Alerta!",
                    "No tiene el permiso necesario para realizar esta acción",
                    "warning")
            }
        }

        function goHistoriaNeuropsicologia(element) {
            if (hasPermission('histNeuro')) {
                let idPaciente = element.getAttribute("data-id")
                let edadPaciente = element.getAttribute("data-edad")
                localStorage.clear()
                localStorage.setItem('idPaciente', idPaciente)
                localStorage.setItem('edadPaciente', edadPaciente)
                let prascaURL = '{{ url('/pacientes/historiaNeuropsicologica') }}'
                const nuevaPestana = window.open(prascaURL, '_blank')
                nuevaPestana.focus()
            } else {
                swal("¡Alerta!",
                    "No tiene el permiso necesario para realizar esta acción",
                    "warning")
            }
        }

        function verServiciosVenta(idPaciente) {
            var modal = new bootstrap.Modal(document.getElementById("modalVentaServicios"), {
                backdrop: 'static',
                keyboard: false
            })
            modal.show()
            document.getElementById("idPaciente").value = idPaciente

            cargarVentaServiciosPacientes(1)
            cargarConsultas()
            cargarSesiones()
            cargarPaquetes()
            cargarPrueba()

        }

        function cargarVentaServiciosPacientes(page, searchTerm = '') {
            let url = "{{ route('pacientes.listaVentaServiciosPacientes') }}"
            let data = {
                page: page,
                search: searchTerm,
                idPaciente: document.getElementById('idPaciente').value
            }

            fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(responseData => {
                    document.getElementById('trRegistrosServicios').innerHTML = responseData.servicios
                    feather.replace()
                    document.getElementById('pagination-links-servicios').innerHTML = responseData.links
                    loadNow(0)
                })
                .catch(error => console.error('Error:', error))
        }

        function nuevaVentaServicio() {

            let listado = document.getElementById("listadoVentaServicios")
            listado.style.display = "none"
            let formulario = document.getElementById("VentaServicio")
            formulario.style.display = "block"
            let btnNewVenta = document.getElementById("newVentaServicio")
            btnNewVenta.style.display = "none"
            let btnRegresar = document.getElementById("btnRegresar")
            btnRegresar.style.display = "block"
        
        }

        function cancelarVentaServicio() {
            let listado = document.getElementById("listadoVentaServicios")
            listado.style.display = "block"
            let formulario = document.getElementById("VentaServicio")
            formulario.style.display = "none"
            let btnNewVenta = document.getElementById("newVentaServicio")
            btnNewVenta.style.display = "block"
            let btnRegresar = document.getElementById("btnRegresar")
            btnRegresar.style.display = "none"

        }

        function seleccionarServicio(servicio) {
            document.getElementById("formVentaServicio").style.display = "block"
            
            switch (servicio) {
                case 'consulta':
                    document.getElementById("formVentaConsulta").style.display = "block"
                    document.getElementById("formVentaConsulta").reset()
                    document.getElementById("formVentaSesion").style.display = "none"
                    document.getElementById("formVentaPaquete").style.display = "none"
                    document.getElementById("formVentaPrueba").style.display = "none"
                    document.getElementById("VentaServicio").style.display = "none"
                    document.getElementById("accHistoriaVenta").value = "guardar"


                    break
                case 'sesion':
                    document.getElementById("formVentaSesion").style.display = "block"
                    document.getElementById("formVentaSesion").reset()
                    document.getElementById("formVentaConsulta").style.display = "none"
                    document.getElementById("formVentaPaquete").style.display = "none"
                    document.getElementById("formVentaPrueba").style.display = "none"
                    document.getElementById("VentaServicio").style.display = "none"
                    document.getElementById("accHistoriaVentaSesion").value = "guardar"

                    break
                case 'paquete':

                 //valirdar paquetes pendientes
                let tablaServicios = document.getElementById("tablaServicios")
                    let filas = tablaServicios.getElementsByTagName("tr")
                    
                    for (let i = 0; i < filas.length; i++) {
                        let celda = filas[i].getElementsByTagName("td")
                        if(celda.length > 0){
                        let estado = celda[5].innerHTML
                        let tipo = celda[1].innerText
                        if(estado == "PENDIENTE" && tipo == "PAQUETE"){
                            swal("¡Alerta!", "No puede crear un nuevo paquete, mientras tenga uno Pendiente", "warning")
                            return
                        }
                    }
                    }

                    document.getElementById("formVentaPaquete").style.display = "block"
                    document.getElementById("formVentaPaquete").reset()
                    document.getElementById("formVentaConsulta").style.display = "none"
                    document.getElementById("formVentaSesion").style.display = "none"
                    document.getElementById("formVentaPrueba").style.display = "none"
                    document.getElementById("VentaServicio").style.display = "none"
                    document.getElementById("accVentaPaquete").value = "guardar"

                    break
                case 'prueba':
                    document.getElementById("formVentaPrueba").style.display = "block"
                    document.getElementById("formVentaPrueba").reset()
                    document.getElementById("formVentaConsulta").style.display = "none"
                    document.getElementById("formVentaSesion").style.display = "none"
                    document.getElementById("formVentaPaquete").style.display = "none"
                    document.getElementById("VentaServicio").style.display = "none"
                    document.getElementById("accVentaPrueba").value = "guardar"
                    break
                default:
            }
            document.getElementById("btnRegresar").style.display = "none"
        }

        function cargarConsultas() {
            let select = document.getElementById("consultaVenta")
            select.innerHTML = ""
            let defaultOption = document.createElement("option")
            defaultOption.value = ""
            defaultOption.text = "Seleccione una consulta"
            defaultOption.setAttribute("data-precio", "0")
            select.appendChild(defaultOption)
            let url = "{{ route('pacientes.consultas') }}"
            fetch(url)
                .then(response => response.json())
                .then(data => {

                    data.forEach(consulta => {
                        let option = document.createElement("option")
                        option.value = consulta.id
                        option.text = consulta.nombre
                        option.setAttribute("data-precio", consulta.precio)

                        select.appendChild(option)
                    })
                })
                .catch(error => console.error('Error:', error))
        }

        function cargarSesiones() {
            let select = document.getElementById("sesionVenta")
            select.innerHTML = ""
            let defaultOption = document.createElement("option")
            defaultOption.value = ""
            defaultOption.text = "Seleccione una sesión"
            select.appendChild(defaultOption)
            let url = "{{ route('pacientes.sesiones') }}"
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    data.forEach(sesion => {
                        let option = document.createElement("option")
                        option.value = sesion.id
                        option.text = sesion.descripcion
                        select.appendChild(option)
                    })
                })
                .catch(error => console.error('Error:', error))

        }

        function cargarPaquetes() {
            let select = document.getElementById("selPaquete")
            select.innerHTML = ""
            let defaultOption = document.createElement("option")
            defaultOption.value = ""
            defaultOption.text = "Seleccione un paquete"
            select.appendChild(defaultOption)
            let url = "{{ route('pacientes.paquetes') }}"
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    data.forEach(paquete => {
                        let option = document.createElement("option")
                        option.value = paquete.id
                        option.text = paquete.descripcion
                        option.setAttribute("data-valor", paquete.precio_por_sesion)
                        select.appendChild(option)
                    })
                })
                .catch(error => console.error('Error:', error))
        }

        function cargarPrueba() {
            let select = document.getElementById("selPrueba")
            select.innerHTML = ""
            let defaultOption = document.createElement("option")
            defaultOption.value = ""
            defaultOption.text = "Seleccione una prueba"
            select.appendChild(defaultOption)
            let url = "{{ route('pacientes.pruebas') }}"
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    data.forEach(prueba => {
                        let option = document.createElement("option")
                        option.value = prueba.id
                        option.text = prueba.descripcion
                        option.setAttribute("data-valor", prueba.precio)
                        select.appendChild(option)
                    })
                })
                .catch(error => console.error('Error:', error))
        }


        function cargarPrecioConsulta(element) {
            let precio = element.options[element.selectedIndex].getAttribute("data-precio")
            let valorServConsultaVis = document.getElementById("valorServConsultaVis")
            valorServConsultaVis.value = formatCurrency(precio, 'es-CO', 'COP')
            let valorServConsulta = document.getElementById("valorServConsulta")
            valorServConsulta.value = precio
        }

        function cambioFormato(id) {
            let numero = document.getElementById(id)
            //elimina ultimos 3 caracateres de id de numero 
            let idPrecio = numero.id.slice(0, -3)
            document.getElementById(idPrecio).value = numero.value
            let formatoMoneda = formatCurrency(numero.value, 'es-CO', 'COP')
            numero.value = formatoMoneda

        }

        function validartxtnum(e) {
            tecla = e.which || e.keyCode
            patron = /[0-9]+$/
            te = String.fromCharCode(tecla)
            return (patron.test(te) || tecla == 9 || tecla == 8 || tecla == 37 || tecla == 39 || tecla == 44)
        }

        function formatCurrency(number, locale, currencySymbol) {
            return new Intl.NumberFormat(locale, {
                style: 'currency',
                currency: currencySymbol,
                minimumFractionDigits: 2
            }).format(number)
        }

        function guardarVentaConsulta() {

            if ($("#formVentaConsulta").valid()) {

                const formVentaConsulta = document.getElementById('formVentaConsulta')
                const formData = new FormData(formVentaConsulta)
                formData.append('idPaciente', document.getElementById('idPaciente').value)

                const url = "{{ route('form.guardarVentaConsulta') }}"

                fetch(url, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log(data)
                        if (data.success = 'success') {

                            swal("¡Buen trabajo!", data.message, data.success)
                            document.getElementById("formVentaConsulta").reset()
                            document.getElementById("formVentaServicio").style.display = "none"
                            document.getElementById("VentaServicio").style.display = "none"
                            document.getElementById("listadoVentaServicios").style.display = "block"
                            document.getElementById("btnRegresar").style.display = "none"
                            document.getElementById("newVentaServicio").style.display = "block"
                            cargarVentaServiciosPacientes(1)

                        } else {
                            swal(data.title, data.message, data.success)
                        }
                    })
                    .catch(error => {
                        console.error("Error al enviar los datos:", error)
                    })
            }
        }

        function cancelarVentaConsulta() {
            document.getElementById("formVentaConsulta").style.display = "none"
            document.getElementById("VentaServicio").style.display = "block"
            document.getElementById("btnRegresar").style.display = "block"
        }

        function cancelarVentaSesion() {
            document.getElementById("formVentaSesion").style.display = "none"
            document.getElementById("VentaServicio").style.display = "block"
            document.getElementById("btnRegresar").style.display = "block"
        }

        function cancelarVentaPaquete() {
            document.getElementById("formVentaPaquete").style.display = "none"
            document.getElementById("VentaServicio").style.display = "block"
            document.getElementById("btnRegresar").style.display = "block"
        }

        function cancelarPrueba() {
            document.getElementById("formVentaPrueba").style.display = "none"
            document.getElementById("VentaServicio").style.display = "block"
            document.getElementById("btnRegresar").style.display = "block"
        }

        function editarRegistro(idServicio) {

            let listadoVentaServicios = document.getElementById("listadoVentaServicios")
            listadoVentaServicios.style.display = "none"
            let formularioVentaServicio = document.getElementById("formVentaServicio")
            formVentaServicio.style.display = "block"
            let btnNewVenta = document.getElementById("newVentaServicio")
            btnNewVenta.style.display = "none"
            let btnRegresar = document.getElementById("btnRegresar")
            btnRegresar.style.display = "noen"

            let url = "{{ route('pacientes.buscaServicioVenta') }}"
            fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        idServicio: idServicio
                    })
                })
                .then(response => response.json())
                .then(data => {

                    if (data.tipo == 'CONSULTA') {
                        document.getElementById("formVentaConsulta").style.display = "block"
                        document.getElementById("formVentaSesion").style.display = "none"
                        document.getElementById("formVentaPaquete").style.display = "none"
                        document.getElementById("formVentaPrueba").style.display = "none"

                        document.getElementById("valorServConsultaVis").value = formatCurrency(data.precio, 'es-CO',
                            'COP')
                        document.getElementById("valorServConsulta").value = data.precio
                        document.getElementById("consultaVenta").value = data.id_tipo_servicio
                        let paraFecha = data.fecha.split(" ")
                        document.getElementById("fechaVentaConsulta").value = paraFecha[0]
                        document.getElementById("horaSeleccionadaVentaConsulta").value = paraFecha[1]
                        document.getElementById("idConsultaVenta").value = data.id
                        document.getElementById("tipoServicioConsulta").value = data.tipo_servicio
                        
                        document.getElementById("accHistoriaVenta").value = 'editar'

                    } else if (data.tipo == 'SESION') {

                        document.getElementById("formVentaSesion").style.display = "block"
                        document.getElementById("formVentaConsulta").style.display = "none"
                        document.getElementById("formVentaPaquete").style.display = "none"
                        document.getElementById("formVentaPrueba").style.display = "none"

                        document.getElementById("valorServSesionVis").value = formatCurrency(data.precio, 'es-CO',
                            'COP')
                        document.getElementById("valorServSesion").value = data.precio
                        document.getElementById("sesionVenta").value = data.id_tipo_servicio
                        let paraFecha = data.fecha.split(" ")
                        document.getElementById("fechaVentaSesion").value = paraFecha[0]
                        document.getElementById("horaSeleccionadaVentaSesion").value = paraFecha[1]
                        document.getElementById("idVentaSesion").value = data.id
                        document.getElementById("tipoServicioSesion").value = data.tipo_servicio

                        document.getElementById("accHistoriaVentaSesion").value = 'editar'
                    } else if (data.tipo == 'PAQUETE') {
                        document.getElementById("formVentaPaquete").style.display = "block"
                        document.getElementById("formVentaConsulta").style.display = "none"
                        document.getElementById("formVentaSesion").style.display = "none"
                        document.getElementById("formVentaPrueba").style.display = "none"
                        let paraFecha = data.fecha.split(" ")
                        document.getElementById("fechaPaquete").value = paraFecha[0]
                        document.getElementById("selPaquete").value = data.id_tipo_servicio
                        document.getElementById("numSesiones").value = data.cantidad
                        document.getElementById("precioSesion").value = data.precio	
                        document.getElementById("precioSesionVis").value = formatCurrency(data.precio, 'es-CO', 'COP')
                        document.getElementById("montoFinal").value = data.precio * data.cantidad
                        document.getElementById("montoFinalVis").value = formatCurrency(data.precio	* data.cantidad, 'es-CO', 'COP')
                        document.getElementById("idVentaPaquete").value = data.id
                        document.getElementById("tipoServicioPaquete").value = data.tipo_servicio
                        document.getElementById("accVentaPaquete").value = 'editar'
                        
                    } else if (data.tipo == 'PRUEBAS') {
                        document.getElementById("formVentaPrueba").style.display = "block"
                        document.getElementById("formVentaConsulta").style.display = "none"
                        document.getElementById("formVentaSesion").style.display = "none"
                        document.getElementById("formVentaPaquete").style.display = "none"

                        document.getElementById("selPrueba").value = data.id_tipo_servicio
                        let paraFecha = data.fecha.split(" ")
                        document.getElementById("fechaPrueba").value = paraFecha[0]
                        document.getElementById("idVentaPrueba").value = data.id
                        document.getElementById("tipoServicioPrueba").value = data.tipo_servicio
                        document.getElementById("accVentaPrueba").value = 'editar'
                        document.getElementById("precioPrueba").value = data.precio
                        document.getElementById("precioPruebaVis").value = formatCurrency(data.precio, 'es-CO', 'COP')
                    }
                })
                .catch(error => console.error('Error:', error))


        }

        function eliminarRegistro(idServicio) {


            swal({
                title: "Esta seguro de eliminar este servicio ?",
                text: "¡No podrás revertir esto!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Si, eliminar!",
                cancelButtonText: "Cancelar",
                confirmButtonClass: "btn btn-warning",
                cancelButtonClass: "btn btn-danger ml-1",
                buttonsStyling: false
            }, function(isConfirm) {
                if (isConfirm) {
                    let url = "{{ route('pacientes.eliminarServicioVenta') }}"
                    fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content')
                            },
                            body: JSON.stringify({
                                idServicio: idServicio
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                swal("¡Buen trabajo!",
                                    data.message,
                                    "success")
                                cargarVentaServiciosPacientes(1)
                            } else {
                                swal("¡Alerta!",
                                    "La operación fue realizada exitosamente",
                                    data.message,
                                    "success");
                            }
                        })

                } else {
                    swal("Cancelado", "Tu registro esta salvo :)", "error")
                }
            })
        }

        function guardarVentaSesion() {
            if ($("#formVentaSesion").valid()) {

                const formVentaSesion = document.getElementById('formVentaSesion')
                const formData = new FormData(formVentaSesion)
                formData.append('idPaciente', document.getElementById('idPaciente').value)

                const url = "{{ route('form.guardarVentaSesion') }}"

                fetch(url, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log(data)
                        if (data.success = 'success') {

                            swal("¡Buen trabajo!", data.message, data.success)
                            document.getElementById("formVentaSesion").reset()
                            document.getElementById("formVentaServicio").style.display = "none"
                            document.getElementById("VentaServicio").style.display = "none"
                            document.getElementById("listadoVentaServicios").style.display = "block"
                            document.getElementById("btnRegresar").style.display = "none"
                            document.getElementById("newVentaServicio").style.display = "block"
                            cargarVentaServiciosPacientes(1)

                        } else {
                            swal(data.title, data.message, data.success)
                        }
                    })
                    .catch(error => {
                        console.error("Error al enviar los datos:", error)
                    })
            }
        }

        function seleccionarPaquete(element) {
            let precio = element.options[element.selectedIndex].getAttribute('data-valor')
            var formatoMoneda = formatCurrency(precio, 'es-CO', 'COP')
            document.getElementById("precioSesion").value = precio
            document.getElementById("precioSesionVis").value = formatoMoneda
          
            calValorMonto()
        }

        function calValorMonto() {

            let valor = document.getElementById("precioSesion").value
            let nsesiones = document.getElementById("numSesiones").value
            let total = valor * nsesiones
            document.getElementById("montoFinal").value = total
            let formatoMoneda = formatCurrency(total, 'es-CO', 'COP')
            document.getElementById("montoFinalVis").value = formatoMoneda
        }

        function guardarPaquetes() {
            if ($("#formVentaPaquete").valid()) {

                const formVentaPaquete = document.getElementById('formVentaPaquete')
                const formData = new FormData(formVentaPaquete)
                formData.append('idPaciente', document.getElementById('idPaciente').value)

                const url = "{{ route('form.guardarPaqueteVenta') }}"

                fetch(url, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {

                        if (data.success = 'success') {
                            swal(data.title, data.message, data.success)
                            document.getElementById("formVentaPaquete").style.display = "none" 
                            document.getElementById("formVentaServicio").style.display = "none"
                            document.getElementById("VentaServicio").style.display = "none"
                            document.getElementById("btnRegresar").style.display = "none"
                            document.getElementById("listadoVentaServicios").style.display = "block"
                            document.getElementById("newVentaServicio").style.display = "block"
                            cargarVentaServiciosPacientes(1)
                        } else {
                            swal(data.title, data.message, data.success)
                        }
                    })
                    .catch(error => {
                        console.error("Error al enviar los datos:", error)
                    })

            }
        }


        function guardarPruebas() {
                if ($("#formVentaPrueba").valid()) {

                    const formVentaPrueba = document.getElementById('formVentaPrueba')
                    const formData = new FormData(formVentaPrueba)
                    formData.append('idPaciente', document.getElementById("idPaciente").value)

                    const url = "{{ route('form.guardarPruebaVenta') }}"

                    fetch(url, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        })
                        .then(response => response.json())
                        .then(data => {

                            if (data.success = 'success') {

                                swal(data.title, data.message, data.success)
                                document.getElementById("formVentaPrueba").style.display = "none"
                                document.getElementById("formVentaServicio").style.display = "none"
                                document.getElementById("VentaServicio").style.display = "none"
                                document.getElementById("btnRegresar").style.display = "none"
                                document.getElementById("listadoVentaServicios").style.display = "block"
                                document.getElementById("newVentaServicio").style.display = "block"
                                cargarVentaServiciosPacientes(1)

                            } else {
                                swal(data.title, data.message, data.success)
                            }
                        })
                        .catch(error => {
                            console.error("Error al enviar los datos:", error)
                        })

                }
            }

    </script>

    <style>
        /* Estilos mejorados para las cards */
        .modal-body {
            max-height: 75vh;
            overflow-y: auto;
            padding: 1.5rem;
        }

        .service-card {
            height: 100%;
            min-height: 220px;
            transition: all 0.3s ease;
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .service-card .card-body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 1.5rem;
            height: 100%;
        }

        .icon-circle {
            width: 60px;
            height: 60px;
            margin: 0 auto 1.25rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .service-card:hover .icon-circle {
            transform: scale(1.1);
        }

        .service-description {
            flex-grow: 1;
            margin: 1rem 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .service-description h6 {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .service-description p {
            color: #6c757d;
            margin-bottom: 0;
            line-height: 1.4;
        }

        .service-card .btn {
            width: 100%;
            padding: 0.5rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .service-card .btn:hover {
            transform: translateY(-2px);
        }

        /* Colores personalizados para los fondos de los iconos */
        .bg-primary-light {
            background-color: rgba(var(--bs-primary-rgb), 0.15);
        }

        .bg-success-light {
            background-color: rgba(var(--bs-success-rgb), 0.15);
        }

        .bg-warning-light {
            background-color: rgba(var(--bs-warning-rgb), 0.15);
        }

        .bg-info-light {
            background-color: rgba(var(--bs-info-rgb), 0.15);
        }
    </style>

@endsection
