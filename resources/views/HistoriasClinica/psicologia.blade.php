@extends('Plantilla.Principal')
@section('title', 'Historia clínica psicológica')
@section('Contenido')
    <input type="hidden" id="Ruta" data-ruta="{{ asset('/app-assets/') }}" />
    <input type="hidden" id="RutaTotal" data-ruta="{{ asset('/') }}" />
    <input type="hidden" id="idUsuario" value="{{ Auth::user()->id }}" />
    <input type="hidden" id="page" />
    <input type="hidden" id="pagePac" />

    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h4 class="page-title">Gestionar historia clínica psicológica</h4>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#"><i class="mdi mdi-home-outline"></i></a></li>
                            <li class="breadcrumb-item" aria-current="page">Inicio</li>
                            <li class="breadcrumb-item active" aria-current="page">Gestionar historia clínica psicológica
                            </li>
                        </ol>
                    </nav>
                </div>

            </div>

        </div>
    </div>
    <section class="content">
        <div class="row">

            <div class="col-12 col-xl-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Gestionar historia clínica psicológica</h5>
                    </div>
                    <div class="card-body" id="listado">
                        <div class="box-controls pull-right mb-4">
                            <div class="box-header-actions">
                                <div class="input-group input-group-merge">
                                    <input type="text" id="busqueda" class="form-control">
                                    <div class="input-group-text" data-password="false">
                                        <span class="fa fa-search"></span>
                                    </div>
                                    <button type="button" onclick="nuevoRegistro();"
                                        class="btn btn-xs btn-primary font-bold"><i class="fa fa-plus"></i> Nueva historia
                                        clinica
                                    </button>
                                </div>

                            </div>
                        </div>
                        <div id="hisoriasListado">

                        </div>
                        <div id="pagination-links" class="text-center ml-1 mt-2">

                        </div>
                    </div>
                    <div id="historia" style="display: none;">
                        <div class="row">
                            <div class="col-xl-4 col-lg-5">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <img id="imgPaciente" src=""
                                            class="bg-light w-100 h-100 rounded-circle avatar-lg img-thumbnail"
                                            alt="profile-image">

                                        <h4 class="mb-0 mt-2" id="nombrePaciente"></h4>
                                        <p class="text-muted fs-14" id="edadPaciente"></p>
                                        <div class="text-start mt-3">

                                            <p class="text-muted mb-2 "><strong class="text-dark">Nombre completo
                                                    :</strong>
                                                <span class="ms-2" id="nombreCompletoPacienteHist"></span>
                                            </p>
                                            <p class="text-muted mb-2 "><strong class="text-dark">Identificación
                                                    :</strong><span class="ms-2" id="identificacionPacienteHist"></span>
                                            </p>
                                            <p class="text-muted mb-2 "><strong class="text-dark">Fecha de nacimiento
                                                    :</strong> <span class="ms-2" id="fechaNacimeintoPacienteHist"></span>
                                            </p>
                                            <p class="text-muted mb-1 "><strong class="text-dark">Tipo de usuario
                                                    :</strong>
                                                <span class="ms-2" id="tipoUsuarioPacienteHist"></span>
                                            </p>
                                            <p class="text-muted mb-1 "><strong class="text-dark">Sexo :</strong>
                                                <span class="ms-2" id="sexoPacienteHist"></span>
                                            </p>
                                        </div>
                                    </div> <!-- end card-body -->
                                </div> <!-- end card -->
                                <!-- Messages-->
                                <div class="card">
                                    <div class="card-body">
                                        <div class="dropdown float-end">
                                            <a href="#" class="dropdown-toggle no-caret" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                                <i class="mdi mdi-dots-vertical"></i>
                                            </a>
                                            {{-- <div class="dropdown-menu dropdown-menu-end">
                                                <!-- item-->
                                                <a href="javascript:void(0);" class="dropdown-item">Settings</a>
                                                <!-- item-->
                                                <a href="javascript:void(0);" class="dropdown-item">Action</a>
                                            </div> --}}
                                        </div>
                                        <h4 class="header-title mb-3">Información de Contacto</h4>
                                        <div class="text-start mt-3">
                                            <p class="text-muted mb-1 "><strong class="text-dark">Teléfono :</strong>
                                                <span class="ms-2" id="telefonoPacienteHist"></span>
                                            </p>
                                            <p class="text-muted mb-1 "><strong class="text-dark">Email :</strong>
                                                <span class="ms-2" id="emailPacienteHist"></span>
                                            </p>
                                            <p class="text-muted mb-1 "><strong class="text-dark">Dirección :</strong>
                                                <span class="ms-2" id="direccionPacienteHist"></span>
                                            </p>
                                            <p class="text-muted mb-1 "><strong class="text-dark">Zona residencial
                                                    :</strong>
                                                <span class="ms-2" id="zonaResidencialPacienteHist"></span>
                                            </p>
                                        </div>
                                    </div> <!-- end card-body-->
                                </div> <!-- end card-->
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="header-title mb-3">Historial de evoluciones</h4>
                                        <div class="text-start mt-3">
                                            <div class="activ_box_button " style="width: 100%;">
                                                <button class="btn btn-success" onclick="abrirConsultas(1)"
                                                    style="width: 100%;"><i class="fa fa-edit"></i> Gestionar
                                                    evoluciones</button>
                                            </div>
                                            <div id="historialConsulta">
                                            </div>
                                        </div>
                                    </div> <!-- end card-body-->
                                </div> <!-- end card-->
                            </div> <!-- end col-->
                            <div class="col-xl-8 col-lg-7">
                                <form id="formHistoria">
                                    <input type="hidden" id="accHistoria" name="accHistoria" />
                                    <input type="hidden" id="idHistoria" name="idHistoria" />
                                    <input type="hidden" id="idPaciente" name="idPaciente" />
                                    <input type="hidden" id="tipoPsicologia" name="tipoPsicologia" />
                                    <input type="hidden" id="estadoHistoria" name="estadoHistoria" />
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="card-header">
                                                <h5 class="text-uppercase"><i class="fa fa-h-square me-1"></i>
                                                    Evaluación clínica psicológica</h5>
                                                <div>
                                                    <button onclick="cancelarHistoria()" type="button"
                                                        class="btn btn-primary-light me-1">
                                                        <i class="ti-back-left"></i> Atras
                                                    </button>
                                                    <button type="button" style="display: none;"
                                                        id="btn-imprimirHistoria" onclick="imprimirHistoria()"
                                                        class="btn btn-info-light me-1"><i class="fa fa-print"></i>
                                                        Imprimir
                                                        historia</button>
                                                </div>

                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 d-flex align-items-center">
                                                    <label for="primeraVez" class="form-label me-2">¿Es la primera vez que
                                                        asiste al psicólogo?:</label>
                                                    <label class="switch switch-border switch-primary">
                                                        <input type="checkbox" id="primeraVez" value="0"
                                                            name="primeraVez">
                                                        <span class="switch-indicator"></span>
                                                        <span class="switch-description"></span>
                                                    </label>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="remision" class="form-label">Remisión :</label>
                                                        <textarea class="form-control" id="remision" name="remision" rows="3"
                                                            placeholder="Ingese de donde es remitido el paciente.."></textarea>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="codConsulta" class="form-label">Código de consulta
                                                            :</label>
                                                        <select class="form-control select2" id="codConsulta"
                                                            name="codConsulta">
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="motivoConsulta" class="form-label">Motivo de consulta
                                                            :</label>
                                                        <textarea class="form-control" id="motivoConsulta" name="motivoConsulta" rows="3"
                                                            placeholder="Describa la enfermedad actual del paciente..."></textarea>
                                                        <label for="motivoConsultaOtro" class="form-label mt-1">Motivos
                                                            relacionados:</label>
                                                        <select class="form-control select2" multiple="multiple"
                                                            id="motivoConsultaOtro" name="motivoConsultaOtro[]"
                                                            data-placeholder="Seleccione otros motivos relacionados"
                                                            style="width: 100%;">
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="codDiagnostico" class="form-label">DX Principal
                                                            :</label>
                                                        <select class="form-control select2" id="codDiagnostico"
                                                            name="codDiagnostico" aria-invalid="false">
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="otro_CodDiagnostico" class="form-label">
                                                            Otro tipo de diagnóstico:</label>
                                                        <input type="text" class="form-control"
                                                            id="otro_CodDiagnostico" name="otro_CodDiagnostico"
                                                            placeholder="Ingrese el diagnóstico">
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="enfermedadActual" class="form-label">Enfermedad actual
                                                            :</label>
                                                        <textarea class="form-control" id="enfermedadActual" name="enfermedadActual" rows="3"
                                                            placeholder="Describa la enfermedad actual del paciente..."></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <ul class="nav nav-pills bg-nav-pills nav-justified mb-3">
                                                <li class="nav-item">
                                                    <a href="#antecedentes" data-bs-toggle="tab"
                                                        class="nav-link rounded-0 active">
                                                        <i class="fa fa-history"></i> Antecedentes
                                                    </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a href="#ajustes" data-bs-toggle="tab" class="nav-link rounded-0">
                                                        <i class="fa fa-cogs"></i> Áreas de Ajuste
                                                    </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a href="#evaluacion" data-bs-toggle="tab"
                                                        class="nav-link rounded-0">
                                                        <i class="fa  fa-check-square-o"></i> Eval. Psicológica
                                                    </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a href="#examen" data-bs-toggle="tab" class="nav-link rounded-0">
                                                        <i class="fa fa-user-md"></i> Examen Mental
                                                    </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a href="#impresion" style="padding-left: 0;padding-right: 0;"
                                                        data-bs-toggle="tab" class="nav-link rounded-0">
                                                        <i class="fa fa-stethoscope"></i> Impresión Diagnóstica
                                                    </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a href="#plan" data-bs-toggle="tab" class="nav-link rounded-0">
                                                        <i class="fa fa-clipboard"></i> Plan e Intervención
                                                    </a>
                                                </li>
                                            </ul>
                                            <div class="tab-content">
                                                <!-- Antecedentes -->
                                                <div class="tab-pane active" id="antecedentes">
                                                    <div class="box-header pb-1">
                                                        <h5 class="text-uppercase"><i class="fa fa-user me-1"></i>
                                                            Médicos Personales</h5>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="quirurgicos"
                                                                    class="form-label">Quirúrgicos:</label>
                                                                <div class="input-group flex-nowrap">
                                                                    <div class="tags-default">
                                                                        <input type="text" id="quirurgicos"
                                                                            name="quirurgicos" value=""
                                                                            data-role="tagsinput"
                                                                            placeholder="Agregar antedecentes">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="toxicos" class="form-label">Tóxicos:</label>
                                                                <div class="input-group flex-nowrap">
                                                                    <div class="tags-default">
                                                                        <input type="text" id="toxicos"
                                                                            name="toxicos" value=""
                                                                            data-role="tagsinput"
                                                                            placeholder="Agregar antedecentes">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label for="hospitalizaciones"
                                                                    class="form-label">Hospitalizaciones:</label>
                                                                <textarea class="form-control" id="hospitalizaciones" name="hospitalizaciones" rows="3"
                                                                    placeholder="Describa las causas de hospitalización..."></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="traumaticos"
                                                                    class="form-label">Traumáticos:</label>
                                                                <select class="form-control" id="traumaticos"
                                                                    name="traumaticos">
                                                                    <option value="">Seleccione una
                                                                        opción...</option>
                                                                    <option value="fracturas óseas">Fracturas óseas
                                                                    </option>
                                                                    <option value="traumatismo craneoencefalico">
                                                                        Traumatismo
                                                                        craneoencefálico</option>
                                                                    <option value="luxaciones y esguinces">Luxaciones y
                                                                        esguinces
                                                                    </option>
                                                                    <option value="quemaduras">Quemaduras</option>
                                                                    <option value="accidente de transito">Accidente de
                                                                        transito
                                                                    </option>
                                                                    <option value="otro">Otro</option>
                                                                    <option value="ninguno">Ninguno</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <!-- Paraclínicos -->
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="paraclinicos"
                                                                    class="form-label">Paraclínicos:</label>
                                                                <div class="tags-default">
                                                                    <input type="text" id="paraclinicos"
                                                                        name="paraclinicos" value=""
                                                                        data-role="tagsinput"
                                                                        placeholder="Agregar antedecentes">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- patologia -->
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label for="patologia"
                                                                    class="form-label">Patología:</label>
                                                                <textarea class="form-control" id="patologia" name="patologia" rows="3"
                                                                    placeholder="Describa la patología..."></textarea>
                                                            </div>
                                                        </div>
                                                        <!-- Medicación -->
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label for="medicacion"
                                                                    class="form-label">Medicación:</label>
                                                                <textarea class="form-control" id="medicacion" name="medicacion" rows="3"
                                                                    placeholder="Describa la medicación actual..."></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div id="infPediatria" style="display: none;">
                                                        <!-- Prenatales -->
                                                        <div class="box-header pb-1">
                                                            <h5 class="text-uppercase">
                                                                <i class="fa fa-heartbeat me-1"></i> Prenatales
                                                            </h5>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="edad_madre" class="form-label">Edad de la
                                                                        madre en el embarazo:</label>
                                                                    <input type="text" class="form-control"
                                                                        id="edad_madre" name="edad_madre"
                                                                        placeholder="Ingrese la edad">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="enfermedades_madre"
                                                                        class="form-label">Enfermedades de la
                                                                        madre:</label>
                                                                    <select class="form-control" id="enfermedades_madre"
                                                                        name="enfermedades_madre">
                                                                        <option value="">Seleccione una opción...
                                                                        </option>
                                                                        <option value="no">No</option>
                                                                        <option value="diabetes">Diabetes</option>
                                                                        <option value="hipertension">Hipertensión</option>
                                                                        <option value="otro">Otro</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="numero_embarazo" class="form-label">Único
                                                                        embarazo:</label>
                                                                    <input type="text" class="form-control"
                                                                        id="numero_embarazo" name="numero_embarazo"
                                                                        placeholder="Ejemplo: 2do">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="enbarazo_controlado" class="form-label">El
                                                                        embarazo fue controlado por atención médica:</label>
                                                                    <select class="form-control" id="enbarazo_controlado"
                                                                        name="enbarazo_controlado">
                                                                        <option value="">Seleccione una opción...
                                                                        </option>
                                                                        <option value="Si">Si</option>
                                                                        <option value="No">No</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="planificacion" class="form-label">Uso de
                                                                        planificación en el momento del embarazo:</label>
                                                                    <select class="form-control" id="planificacion"
                                                                        name="planificacion">
                                                                        <option value="">Seleccione una opción...
                                                                        </option>
                                                                        <option value="Si">Si</option>
                                                                        <option value="No">No</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label for="estado_madre" class="form-label">Estado de
                                                                        la
                                                                        madre durante el embarazo:</label>
                                                                    <textarea class="form-control" id="estado_madre" name="estado_madre" rows="3"
                                                                        placeholder="Describa el estado"></textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Natales -->
                                                        <div class="box-header pb-1">
                                                            <h5 class="text-uppercase">
                                                                <i class="fa fa-heart me-1"></i> Natales
                                                            </h5>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="tipo_nacimiento" class="form-label">Tipo
                                                                        de
                                                                        nacimiento:</label>
                                                                    <select class="form-control" id="tipo_nacimiento"
                                                                        name="tipo_nacimiento">
                                                                        <option value="">Seleccione una opción...
                                                                        </option>
                                                                        <option value="cesarea">Cesárea</option>
                                                                        <option value="natural">Natural</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="causa_cesarea" class="form-label">Causa de
                                                                        la
                                                                        cesárea:</label>
                                                                    <input type="text" class="form-control"
                                                                        id="causa_cesarea" name="causa_cesarea"
                                                                        placeholder="Ejemplo: No dilataba">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="reanimacion" class="form-label">Uso de
                                                                        maniobras de reanimación:</label>
                                                                    <select class="form-control" id="reanimacion"
                                                                        name="reanimacion">
                                                                        <option value="">Seleccione una opción...
                                                                        </option>
                                                                        <option value="si">Sí</option>
                                                                        <option value="no">No</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="peso_nacer" class="form-label">Peso al
                                                                        nacer:</label>
                                                                    <input type="text" class="form-control"
                                                                        id="peso_nacer" name="peso_nacer"
                                                                        placeholder="Ejemplo: 1KG">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="talla_nacer" class="form-label">Talla al
                                                                        nacer:</label>
                                                                    <input type="text" class="form-control"
                                                                        id="talla_nacer" name="talla_nacer"
                                                                        placeholder="Ejemplo: 50 cm">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="llanto_nacer" class="form-label">Llanto al
                                                                        nacer:</label>
                                                                    <select class="form-control" id="llanto_nacer"
                                                                        name="llanto_nacer">
                                                                        <option value="">Seleccione una opción...
                                                                        </option>
                                                                        <option value="si">Sí</option>
                                                                        <option value="no">No</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Posnatales -->
                                                        <div class="box-header pb-1">
                                                            <h5 class="text-uppercase">
                                                                <i class="fa fa-heart-o me-1"></i> Posnatales
                                                            </h5>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label for="hospitalizaciones_postnatales"
                                                                        class="form-label">Hospitalizaciones recién
                                                                        nacido:</label>
                                                                    <textarea class="form-control" id="hospitalizaciones_postnatales" name="hospitalizaciones_postnatales"
                                                                        rows="3" placeholder="Describa las causas de hospitalización"></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label for="desarrollo_psicomotor"
                                                                        class="form-label">Desarrollo
                                                                        psicomotor:</label>
                                                                    <textarea class="form-control" id="desarrollo_psicomotor" name="desarrollo_psicomotor" rows="5"
                                                                        placeholder="Ejemplo: Control cefálico 2M, Marcha 10M"></textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Desarrollo Psicomotor -->
                                                        <div class="box-header pb-1">
                                                            <h5 class="text-uppercase">
                                                                <i class="mdi mdi-baby me-1"></i> Desarrollo Psicomotor
                                                            </h5>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="control_cefalico"
                                                                        class="form-label">Control
                                                                        cefálico:</label>
                                                                    <input type="text" class="form-control"
                                                                        id="control_cefalico" name="control_cefalico"
                                                                        placeholder="Ejemplo: 2M">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="rolado"
                                                                        class="form-label">Rolado:</label>
                                                                    <input type="text" class="form-control"
                                                                        id="rolado" name="rolado"
                                                                        placeholder="Ejemplo: 3M">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="sedente_solo" class="form-label">Sedente
                                                                        solo:</label>
                                                                    <input type="text" class="form-control"
                                                                        id="sedente_solo" name="sedente_solo"
                                                                        placeholder="Ejemplo: 6M">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="gateo"
                                                                        class="form-label">Gateo:</label>
                                                                    <input type="text" class="form-control"
                                                                        id="gateo" name="gateo"
                                                                        placeholder="Ejemplo: 6M">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="bipedo" class="form-label">Bípedo
                                                                        sin ayuda:</label>
                                                                    <input type="text" class="form-control"
                                                                        id="bipedo" name="bipedo"
                                                                        placeholder="Ejemplo: 9M">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="marcha"
                                                                        class="form-label">Marcha:</label>
                                                                    <input type="text" class="form-control"
                                                                        id="marcha" name="marcha"
                                                                        placeholder="Ejemplo: 10M">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="lenguaje_verbal"
                                                                        class="form-label">Lenguaje
                                                                        verbal:</label>
                                                                    <input type="text" class="form-control"
                                                                        id="lenguaje_verbal" name="lenguaje_verbal"
                                                                        placeholder="Ejemplo: 1 año">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="lenguaje_verbal_fluido"
                                                                        class="form-label">Lenguaje verbal
                                                                        fluido:</label>
                                                                    <input type="text" class="form-control"
                                                                        id="lenguaje_verbal_fluido"
                                                                        name="lenguaje_verbal_fluido"
                                                                        placeholder="Ejemplo: 6 años">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="box-header pb-1">
                                                        <h5 class="text-uppercase"><i class="fa fa-users me-1"></i>
                                                            Médicos Familiares</h5>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label for="depresion"
                                                                    class="form-label">Depresión:</label>
                                                                <select class="form-control" id="depresion"
                                                                    name="depresion">
                                                                    <option value="">Selecciona una opción
                                                                    </option>
                                                                    <option value="no refiere">No refiere</option>
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
                                                                <label for="ansiedad" class="form-label">Ansiedad:</label>
                                                                <select class="form-control" id="ansiedad"
                                                                    name="ansiedad">
                                                                    <option value="">Selecciona una opción
                                                                    </option>
                                                                    <option value="no refiere">No refiere</option>
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
                                                                <label for="demencia" class="form-label">Demencia:</label>
                                                                <select class="form-control" id="demencia"
                                                                    name="demencia">
                                                                    <option value="">Selecciona una opción
                                                                    </option>
                                                                    <option value="no refiere">No refiere</option>
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
                                                                <label for="alcoholismo"
                                                                    class="form-label">Alcoholismo:</label>
                                                                <select class="form-control" id="alcoholismo"
                                                                    name="alcoholismo">
                                                                    <option value="">Selecciona una opción
                                                                    </option>
                                                                    <option value="no refiere">No refiere</option>
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
                                                                <label for="drogadiccion"
                                                                    class="form-label">Drogadicción:</label>
                                                                <select class="form-control" id="drogadiccion"
                                                                    name="drogadiccion">
                                                                    <option value="">Selecciona una opción
                                                                    </option>
                                                                    <option value="no refiere">No refiere</option>
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
                                                                <label for="discapacidad_intelectual"
                                                                    class="form-label">Discapacidad
                                                                    Intelectual:</label>
                                                                <select class="form-control" id="discapacidad_intelectual"
                                                                    name="discapacidad_intelectual">
                                                                    <option value="">Selecciona una opción
                                                                    </option>
                                                                    <option value="no refiere">No refiere</option>
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
                                                                <label for="patologicos"
                                                                    class="form-label">Patológicos:</label>
                                                                <select class="form-control" id="patologicos"
                                                                    name="patologicos">
                                                                    <option value="">Selecciona una opción
                                                                    </option>
                                                                    <option value="no refiere">No refiere</option>
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
                                                                <label for="otros" class="form-label">Otros:</label>
                                                                <select class="form-control" id="otros"
                                                                    name="otros">
                                                                    <option value="">Selecciona una opción
                                                                    </option>
                                                                    <option value="no refiere">No refiere</option>
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
                                                    </div>
                                                </div>
                                                <!-- Áreas de Ajuste -->
                                                <div class="tab-pane" id="ajustes">
                                                    <div class="box-header pb-1">
                                                        <h5 class="text-uppercase"><i class="fa fa-book me-1"></i>
                                                            Áreas de
                                                            Ajuste
                                                            y/o Desempeño</h5>
                                                    </div>
                                                    <div class="row">
                                                        <!-- Historia Educativa -->
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label for="historia_educativa"
                                                                    class="form-label">Historia
                                                                    Educativa:</label>
                                                                <textarea class="form-control" id="historia_educativa" name="historia_educativa" rows="3"
                                                                    placeholder="Describa la historia educativa..."></textarea>
                                                            </div>
                                                        </div>
                                                        <!-- Historia Laboral -->
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label for="historia_laboral" class="form-label">
                                                                    Historia
                                                                    Laboral:</label>
                                                                <textarea class="form-control" id="historia_laboral" name="historia_laboral" rows="3"
                                                                    placeholder="Describa la historia laboral..."></textarea>
                                                            </div>
                                                        </div>
                                                        <!-- Historia Familiar -->
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label for="historia_familiar" class="form-label">
                                                                    Historia
                                                                    Familiar:</label>
                                                                <textarea class="form-control" id="historia_familiar" name="historia_familiar" rows="3"
                                                                    placeholder="Describa la historia familiar..."></textarea>
                                                            </div>
                                                        </div>
                                                        <!-- Historia Social -->
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label for="historia_social" class="form-label">
                                                                    Historia
                                                                    Social:</label>
                                                                <textarea class="form-control" id="historia_social" name="historia_social" rows="3"
                                                                    placeholder="Describa la historia social..."></textarea>
                                                            </div>
                                                        </div>
                                                        <!-- Historia Socio-Afectiva -->
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label for="historia_socio_afectiva" class="form-label">
                                                                    Historia
                                                                    Socio-Afectiva:</label>
                                                                <textarea class="form-control" id="historia_socio_afectiva" name="historia_socio_afectiva" rows="3"
                                                                    placeholder="Describa la historia socio afectiva..."></textarea>
                                                            </div>
                                                        </div>
                                                    </div>


                                                </div>
                                                <!-- Áreas de evaluacion -->
                                                <div class="tab-pane" id="evaluacion">
                                                    <div class="box-header pb-1">
                                                        <h5 class="text-uppercase mt-4"><i
                                                                class="fa  fa-check-square-o me-1"></i>
                                                            Evaluación psicolólogica</h5>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label for="resumen_evaluacion_inicial"
                                                                    class="form-label">Evaluación psicolólogica inicial
                                                                    :</label>
                                                                <textarea class="form-control" id="resumen_evaluacion_inicial" name="resumen_evaluacion_inicial" rows="3"
                                                                    placeholder="Resumen de evaluación psicológica inicial"></textarea>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="box-header pb-1">
                                                        <h5 class="text-uppercase mt-4"><i
                                                                class="fa fa-stethoscope me-1"></i>
                                                            Interconsultas e Intervenciones</h5>
                                                    </div>

                                                    <div class="row">
                                                        <!-- Intervención por Psiquiatría (Medicación) -->
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="intervencion_psiquiatria" class="form-label">
                                                                    Intervención por
                                                                    Psiquiatría :</label>
                                                                <textarea class="form-control" id="intervencion_psiquiatria" name="intervencion_psiquiatria" rows="3"
                                                                    placeholder="Describa la intervención por Psiquiatría..."></textarea>
                                                            </div>
                                                        </div>
                                                        <!-- Intervención por Neurología (Medicación) -->
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="intervencion_neurologia" class="form-label">
                                                                    Intervención por Neurología:</label>
                                                                <textarea class="form-control" id="intervencion_neurologia" name="intervencion_neurologia" rows="3"
                                                                    placeholder="Describa la intervención por Neurología..."></textarea>
                                                            </div>
                                                        </div>
                                                        <!-- Intervención por Neuropsicología -->
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="intervencion_neuropsicologia"
                                                                    class="form-label">
                                                                    Intervención por Neuropsicología:</label>
                                                                <textarea class="form-control" id="intervencion_neuropsicologia" name="intervencion_neuropsicologia" rows="3"
                                                                    placeholder="Describa la intervención por
                                                                Neuropsicología..."></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Examen Mental -->
                                                <div class="tab-pane" id="examen">
                                                    <div class="box-header pb-1">
                                                        <h5 class="text-uppercase mt-4"><i class="fa fa-user-md me-1"></i>
                                                            Examen
                                                            Mental</h5>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <h5 class="text-uppercase mt-4"><i
                                                                        class="fa fa-user me-1"></i> Apariencia
                                                                    personal
                                                                </h5>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="edad" class="form-label">Edad:</label>
                                                            <select id="edad" name="edad[]"
                                                                class="form-select select2 examen" multiple="multiple"
                                                                style="width: 100%" onchange="toggleOtro(this)">

                                                            </select>
                                                            <input type="text" class="form-control mt-2 d-none"
                                                                id="edad_otro" name="edad_otro"
                                                                placeholder="Especifique otro">
                                                        </div>
                                                        <!-- Desarrollo pondoestatural -->
                                                        <div class="col-md-6">
                                                            <label for="desarrollo" class="form-label">Desarrollo
                                                                pondoestatural:</label>
                                                            <select class="form-select select2 examen" multiple="multiple"
                                                                id="desarrollo" name="desarrollo[]" style="width: 100%"
                                                                onchange="toggleOtro(this)">

                                                            </select>
                                                            <input type="text" class="form-control mt-2 d-none"
                                                                id="desarrollo_otro" name="desarrollo_otro"
                                                                placeholder="Especifique otro">
                                                        </div>
                                                        <!-- Aseo y arreglo -->
                                                        <div class="col-md-6">
                                                            <label for="aseo" class="form-label">Aseo y
                                                                arreglo:</label>
                                                            <select class="form-select select2 examen" multiple="multiple"
                                                                id="aseo" name="aseo[]" style="width: 100%"
                                                                onchange="toggleOtro(this)">

                                                            </select>
                                                            <input type="text" class="form-control mt-2 d-none"
                                                                id="aseo_otro" name="aseo_otro"
                                                                placeholder="Especifique otro">
                                                        </div>
                                                        <!-- Salud somática -->
                                                        <div class="col-md-6">
                                                            <label for="salud" class="form-label">Salud
                                                                somática:</label>
                                                            <select class="form-select select2 examen" multiple="multiple"
                                                                id="salud" name="salud[]" style="width: 100%"
                                                                onchange="toggleOtro(this)">

                                                            </select>
                                                            <input type="text" class="form-control mt-2 d-none"
                                                                id="salud_otro" name="salud_otro"
                                                                placeholder="Especifique otro">
                                                        </div>
                                                        <!-- Facies -->
                                                        <div class="col-md-6">
                                                            <label for="facies" class="form-label">Facies:</label>
                                                            <select class="form-select select2 examen" multiple="multiple"
                                                                id="facies" name="facies[]" style="width: 100%"
                                                                onchange="toggleOtro(this)">

                                                            </select>
                                                            <input type="text" class="form-control mt-2 d-none"
                                                                id="facies_otro" name="facies_otro"
                                                                placeholder="Especifique otro">
                                                        </div>
                                                        <!-- Biotipo -->
                                                        <div class="col-md-6">
                                                            <label for="biotipo" class="form-label">Biotipo:</label>
                                                            <select class="form-select select2 examen" multiple="multiple"
                                                                id="biotipo" name="biotipo[]" style="width: 100%"
                                                                onchange="toggleOtro(this)">

                                                            </select>
                                                            <input type="text" class="form-control mt-2 d-none"
                                                                id="biotipo_otro" name="biotipo_otro"
                                                                placeholder="Especifique otro">
                                                        </div>
                                                        <!-- Actitud -->
                                                        <div class="col-md-6">
                                                            <label for="actitud" class="form-label">Actitud:</label>
                                                            <select class="form-select select2 examen" multiple="multiple"
                                                                id="actitud" name="actitud[]" style="width: 100%"
                                                                onchange="toggleOtro(this)">

                                                            </select>
                                                            <input type="text" class="form-control mt-2 d-none"
                                                                id="actitud_otro" name="actitud_otro"
                                                                placeholder="Especifique otro">
                                                        </div>
                                                    </div>
                                                    <div class="box-header pb-1">
                                                        <h5 class="text-uppercase mt-4"><i class="fa fa-cogs me-1"></i>
                                                            Funciones
                                                            Cognitivas</h5>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <label for="consciencia"
                                                                class="form-label">Consciencia:</label>
                                                            <select class="form-select select2 examen" multiple="multiple"
                                                                id="consciencia" name="consciencia[]" style="width: 100%"
                                                                onchange="toggleOtro(this)">

                                                            </select>
                                                            <input type="text" class="form-control mt-2 d-none"
                                                                id="consciencia_otro" name="consciencia_otro"
                                                                placeholder="Especifique otro">
                                                        </div>
                                                        <!-- Orientación -->
                                                        <div class="col-md-6">
                                                            <label for="orientacion"
                                                                class="form-label">Orientación:</label>
                                                            <select class="form-select select2 examen" multiple="multiple"
                                                                id="orientacion" name="orientacion[]" style="width: 100%"
                                                                onchange="toggleOtro(this)">

                                                            </select>
                                                            <input type="text" class="form-control mt-2 d-none"
                                                                id="orientacion_otro" name="orientacion_otro"
                                                                placeholder="Especifique otro">
                                                        </div>

                                                        <!-- Memoria -->
                                                        <div class="col-md-6">
                                                            <label for="memoria" class="form-label">Memoria:</label>
                                                            <select class="form-select select2 examen" multiple="multiple"
                                                                id="memoria" name="memoria[]" style="width: 100%"
                                                                onchange="toggleOtro(this)">

                                                            </select>
                                                            <input type="text" class="form-control mt-2 d-none"
                                                                id="memoria_otro" name="memoria_otro"
                                                                placeholder="Especifique otro">
                                                        </div>

                                                        <!-- Atención -->
                                                        <div class="col-md-6">
                                                            <label for="atencion" class="form-label">Atención:</label>
                                                            <select class="form-select select2 examen" multiple="multiple"
                                                                id="atencion" name="atencion[]" style="width: 100%"
                                                                onchange="toggleOtro(this)">

                                                            </select>
                                                            <input type="text" class="form-control mt-2 d-none"
                                                                id="atencion_otro" name="atencion_otro"
                                                                placeholder="Especifique otro">
                                                        </div>

                                                        <!-- Concentración -->
                                                        <div class="col-md-6">
                                                            <label for="concentracion"
                                                                class="form-label">Concentración:</label>
                                                            <select class="form-select select2 examen" multiple="multiple"
                                                                id="concentracion" name="concentracion[]"
                                                                style="width: 100%" onchange="toggleOtro(this)">

                                                            </select>
                                                            <input type="text" class="form-control mt-2 d-none"
                                                                id="concentracion_otro" name="concentracion_otro"
                                                                placeholder="Especifique otro">
                                                        </div>

                                                        <!-- Lenguaje -->
                                                        <div class="col-md-6">
                                                            <label for="lenguaje" class="form-label">Lenguaje:</label>
                                                            <select class="form-select select2 examen"
                                                                multiple="multiple" id="lenguaje" name="lenguaje[]"
                                                                style="width: 100%" onchange="toggleOtro(this)">

                                                            </select>
                                                            <input type="text" class="form-control mt-2 d-none"
                                                                id="lenguaje_otro" name="lenguaje_otro"
                                                                placeholder="Especifique otro">
                                                        </div>

                                                        <!-- Pensamiento -->
                                                        <div class="col-md-6">
                                                            <label for="pensamiento"
                                                                class="form-label">Pensamiento:</label>
                                                            <select class="form-select select2 examen"
                                                                multiple="multiple" id="pensamiento"
                                                                name="pensamiento[]" style="width: 100%"
                                                                onchange="toggleOtro(this)">

                                                            </select>
                                                            <input type="text" class="form-control mt-2 d-none"
                                                                id="pensamiento_otro" name="pensamiento_otro"
                                                                placeholder="Especifique otro">
                                                        </div>

                                                        <!-- Afecto -->
                                                        <div class="col-md-6">
                                                            <label for="afecto" class="form-label">Afecto:</label>
                                                            <select class="form-select select2 examen"
                                                                multiple="multiple" id="afecto" name="afecto[]"
                                                                style="width: 100%" onchange="toggleOtro(this)">

                                                            </select>
                                                            <input type="text" class="form-control mt-2 d-none"
                                                                id="afecto_otro" name="afecto_otro"
                                                                placeholder="Especifique otro">
                                                        </div>

                                                        <!-- Sensopercepción -->
                                                        <div class="col-md-6">
                                                            <label for="sensopercepcion"
                                                                class="form-label">Sensopercepción:</label>
                                                            <select class="form-select select2 examen"
                                                                multiple="multiple" id="sensopercepcion"
                                                                name="sensopercepcion[]" style="width: 100%"
                                                                onchange="toggleOtro(this)">

                                                            </select>
                                                            <input type="text" class="form-control mt-2 d-none"
                                                                id="sensopercepcion_otro" name="sensopercepcion_otro"
                                                                placeholder="Especifique otro">
                                                        </div>

                                                        <!-- Psicomotricidad -->
                                                        <div class="col-md-6">
                                                            <label for="psicomotricidad"
                                                                class="form-label">Psicomotricidad:</label>
                                                            <select class="form-select select2 examen"
                                                                multiple="multiple" id="psicomotricidad"
                                                                name="psicomotricidad[]" style="width: 100%"
                                                                onchange="toggleOtro(this)">

                                                            </select>
                                                            <input type="text" class="form-control mt-2 d-none"
                                                                id="psicomotricidad_otro" name="psicomotricidad_otro"
                                                                placeholder="Especifique otro">
                                                        </div>

                                                        <!-- Juicio -->
                                                        <div class="col-md-6">
                                                            <label for="juicio" class="form-label">Juicio:</label>
                                                            <select class="form-select select2 examen"
                                                                multiple="multiple" id="juicio" name="juicio[]"
                                                                style="width: 100%" onchange="toggleOtro(this)">

                                                            </select>
                                                            <input type="text" class="form-control mt-2 d-none"
                                                                id="juicio_otro" name="juicio_otro"
                                                                placeholder="Especifique otro">
                                                        </div>

                                                        <!-- Inteligencia -->
                                                        <div class="col-md-6">
                                                            <label for="inteligencia"
                                                                class="form-label">Inteligencia:</label>
                                                            <select class="form-select select2 examen"
                                                                multiple="multiple" id="inteligencia"
                                                                name="inteligencia[]" name="inteligencia[]"
                                                                style="width: 100%" onchange="toggleOtro(this)">

                                                            </select>
                                                            <input type="text" class="form-control mt-2 d-none"
                                                                id="inteligencia_otro" name="inteligencia_otro"
                                                                placeholder="Especifique otro">
                                                        </div>

                                                        <!-- Conciencia de enfermedad -->
                                                        <div class="col-md-6">
                                                            <label for="conciencia_enfermedad"
                                                                class="form-label">Conciencia
                                                                de enfermedad:</label>
                                                            <select class="form-select select2 examen"
                                                                multiple="multiple" id="conciencia_enfermedad"
                                                                name="conciencia_enfermedad[]" style="width: 100%"
                                                                onchange="toggleOtro(this)">
                                                                onchange="toggleOtro(this)">

                                                            </select>
                                                            <input type="text" class="form-control mt-2 d-none"
                                                                id="conciencia_enfermedad_otro"
                                                                name="conciencia_enfermedad_otro"
                                                                placeholder="Especifique otro">
                                                        </div>

                                                        <!-- Sufrimiento psicológico -->
                                                        <div class="col-md-6">
                                                            <label for="sufrimiento_psicologico"
                                                                class="form-label">Sufrimiento psicológico:</label>
                                                            <select class="form-select select2 examen"
                                                                multiple="multiple" id="sufrimiento_psicologico"
                                                                name="sufrimiento_psicologico[]" style="width: 100%"
                                                                onchange="toggleOtro(this)">
                                                                onchange="toggleOtro(this)">

                                                            </select>
                                                            <input type="text" class="form-control mt-2 d-none"
                                                                id="sufrimiento_psicologico_otro"
                                                                name="sufrimiento_psicologico_otro"
                                                                placeholder="Especifique otro">
                                                        </div>

                                                        <!-- Motivación al tratamiento -->
                                                        <div class="col-md-6">
                                                            <label for="motivacion_tratamiento"
                                                                class="form-label">Motivación al tratamiento:</label>
                                                            <select class="form-select select2 examen"
                                                                multiple="multiple" id="motivacion_tratamiento"
                                                                name="motivacion_tratamiento[]" style="width: 100%"
                                                                onchange="toggleOtro(this)">

                                                            </select>
                                                            <input type="text" class="form-control mt-2 d-none"
                                                                id="motivacion_tratamiento_otro"
                                                                name="motivacion_tratamiento_otro"
                                                                placeholder="Especifique otro">
                                                        </div>
                                                    </div>
                                                    <div class="box-header pb-1">
                                                        <h5 class="text-uppercase mt-4"><i
                                                                class="fa fa-heartbeat me-1"></i>
                                                            Funciones Somáticas</h5>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="ciclos_sueno" class="form-label">Ciclos
                                                                    del
                                                                    Sueño:</label>
                                                                <textarea class="form-control" id="ciclos_sueno" name="ciclos_sueno" rows="3"></textarea>
                                                            </div>
                                                        </div>

                                                        <!-- Apetito -->
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="apetito"
                                                                    class="form-label">Apetito:</label>
                                                                <textarea class="form-control" id="apetito" name="apetito" rows="3"></textarea>
                                                            </div>
                                                        </div>

                                                        <!-- Actividades de Autocuidado -->
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="autocuidado" class="form-label">Actividades
                                                                    de
                                                                    Autocuidado:</label>
                                                                <textarea class="form-control" id="autocuidado" name="autocuidado" rows="3"></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Impresion Diagnostica -->
                                                <div class="tab-pane" id="impresion">
                                                    <!-- 1. Impresión Diagnóstica -->
                                                    <div class="box-header pb-1">
                                                        <h5 class="text-uppercase"><i
                                                                class="fa fa-stethoscope me-1"></i>
                                                            Impresión Diagnóstica</h5>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label for="impresion_diagnostica"
                                                                    class="form-label">Impresión Diagnóstica (CIE 10 –
                                                                    DSM-V):</label>
                                                                <select class="form-control select2"
                                                                    id="codImpresionDiagnostico"
                                                                    name="codImpresionDiagnostico" aria-invalid="false">
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label for="otra_ImpresionDiagnostica"
                                                                    class="form-label">
                                                                    Otro tipo de impresión diagnóstica:</label>
                                                                <input type="text" class="form-control"
                                                                    id="otra_ImpresionDiagnostica"
                                                                    name="otra_ImpresionDiagnostica"
                                                                    placeholder="Ingrese el diagnóstico">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label for="impresion_diagnostica"
                                                                    class="form-label">Establecido por primera
                                                                    vez.</label>
                                                                <select class="form-control" id="establecidoPrimeraVez"
                                                                    name="establecidoPrimeraVez" aria-invalid="false">
                                                                    <option value="">Seleccione...</option>
                                                                    <option value="Si">Si</option>
                                                                    <option value="No">No</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>

                                                <!-- Plan de intervención -->
                                                <div class="tab-pane" id="plan">
                                                    <div class="tab-pane" id="diagnostico-plan">
                                                        <!-- 2. Plan de Intervención -->
                                                        <div class="box-header pb-1">
                                                            <h5 class="text-uppercase mt-4"><i
                                                                    class="fa fa-tasks me-1"></i>
                                                                Plan
                                                                de Intervención</h5>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label for="plan_intervencion"
                                                                        class="form-label">Plan
                                                                        de intervención:</label>
                                                                    <select class="form-control select2"
                                                                        id="plan_intervencion" name="plan_intervencion"
                                                                        data-placeholder="Seleccione el plan de intervención"
                                                                        style="width: 100%;">

                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Objetivo General -->
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label for="objetivo_general"
                                                                        class="form-label">Objetivo
                                                                        General:</label>
                                                                    <textarea class="form-control" id="objetivo_general" name="objetivo_general" rows="3"
                                                                        placeholder="Ingrese el objetivo general..."></textarea>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Objetivos Específicos -->
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label for="objetivos_especificos"
                                                                        class="form-label">Objetivos
                                                                        Específicos:</label>
                                                                    <textarea class="form-control" id="objetivos_especificos" name="objetivos_especificos" rows="3"
                                                                        placeholder="Ingrese los objetivos específicos..."></textarea>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- 3. Sugerencia para Interconsultas -->
                                                        <div class="box-header pb-1">
                                                            <h5 class="text-uppercase mt-4"><i
                                                                    class="fa fa-user-md me-1"></i>
                                                                Sugerencia para Interconsultas</h5>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label for="sugerencia_interconsultas"
                                                                        class="form-label">Sugerencia para
                                                                        Interconsultas:</label>
                                                                    <textarea class="form-control" id="sugerencia_interconsultas" name="sugerencia_interconsultas" rows="3"
                                                                        placeholder="Ingrese las sugerencias para interconsultas..."></textarea>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- 4. Observaciones y Recomendaciones -->
                                                        <div class="box-header pb-1">
                                                            <h5 class="text-uppercase mt-4"><i
                                                                    class="fa fa-comments me-1"></i>
                                                                Observaciones y Recomendaciones</h5>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label for="observaciones_recomendaciones"
                                                                        class="form-label">Observaciones y
                                                                        Recomendaciones:</label>
                                                                    <textarea class="form-control" id="observaciones_recomendaciones" name="observaciones_recomendaciones"
                                                                        rows="3" placeholder="Ingrese observaciones y recomendaciones..."></textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div> <!-- end tab-content -->
                                            <hr />
                                            <input type="hidden" id="idProfesional" name="idProfesional" />
                                            <div class="card-body" id="defaulProfesional">

                                                <h5 id="nombreProfesional"></h5>
                                                <img id="firmaProfesional" width="200" src=""
                                                    alt="">
                                                <p id="registroProfesional"><strong>Tarjeta Profesional:</strong>
                                                </p>
                                            </div>
                                            <div class="card-body" style="display: none;" id="divProfesional">
                                                <div class="row">
                                                    <label for="account-username">Profesional:</label>
                                                    <select class="form-control select2"
                                                        onchange="seleccionarProfesional(this)" style="width: 100%;"
                                                        id="profesionalSelect" name="profesionalSelect">
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="box-footer text-end">
                                                <button onclick="guardarHistoria()" id="btn-guardarHistoria"
                                                    type="button" class="btn btn-primary">
                                                    <i class="ti-save"></i> Guardar
                                                </button>
                                            </div>
                                        </div>
                                        <!-- end card body -->
                                    </div> <!-- end card -->
                                </form>
                            </div> <!-- end col -->

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- MODAL PACIENTES -->
    <div class="modal fade" id="modalHistoria" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 60%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">Seleccionar paciente</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                    <div class="card">
                        <div class="card-header">
                            <div class="app-menu" id="divBusquedaGen">
                                <div class="search-bx mx-5">
                                    <form>
                                        <div class="input-group">
                                            <input type="search" id="busquedaPa" name="busquedaPa"
                                                class="form-control" placeholder="Buscar paciente">
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="loaderPacientes" style="display: none; text-align: center; padding: 20px;">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                            </div>
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Paciente</th>
                                        <th>Tipo de usuario</th>
                                        <th>Sexo</th>
                                        <th>Edad</th>
                                    </tr>
                                </thead>
                                <tbody id="trRegistrosPacientes">

                                </tbody>
                            </table>
                            <div id="pagination-links-pacientes" class="text-center ml-1 mt-2">
                            </div>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
        </div><!-- /.modal -->
    </div>
    <!-- MODAL CONSULTA -->
    <div class="modal fade" id="modalConsulta" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 60%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="titConsulta">Gestionar evoluciones clínicas</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                    <div class="card">
                        <div id="listadoConsultas" class="col-12 col-xl-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Listado de evoluciones</h5>
                                </div>
                                <div class="card-body">
                                    <div class="box-controls
                                    pull-right">
                                        <div class="box-header-actions">
                                            <div class="input-group input-group-merge">
                                                <input type="text" id="busquedaCo" class="form-control">
                                                <div class="input-group-text" data-password="false">
                                                    <span class="fa fa-search"></span>
                                                </div>
                                                <button type="button" onclick="nuevoRegistroConsulta();"
                                                    class="btn btn-xs btn-primary font-bold"><i class="fa fa-plus"></i>
                                                    Nueva evolución</button>
                                            </div>

                                        </div>
                                    </div>
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th style="width:5%;">Fecha</th>
                                                <th style="width:37%;">Consulta</th>
                                                <th style="width:35%;">Diagnóstico</th>
                                                <th style="width:15%;">Profesional</th>
                                                <th style="width:10%;">Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody id="trRegistrosConsultas">
                                        </tbody>
                                    </table>
                                    <div id="pagination-links-consulta" class="text-center ml-1 mt-2">

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="fomrConsultas" style="display: none;">
                            <div class="card-body">
                                <form id="formConsulta">
                                    @csrf <!-- Directiva para el token CSRF de Laravel -->
                                    <input type="hidden" id="accHistoriaConsulta" name="accHistoriaConsulta" />
                                    <input type="hidden" id="idHistoriaConsulta" name="idHistoriaConsulta" />
                                    <div class="tab-content">
                                        <div class="tab-pane show active" id="justified-tabs-preview">
                                            <ul class="nav nav-pills bg-nav-pills nav-justified mb-3">
                                                <li class="nav-item">
                                                    <a href="#datos_iniciales" data-bs-toggle="tab"
                                                        aria-expanded="false" class="nav-link rounded-0 active">
                                                        <span class="d-none d-md-block"><i
                                                                class="fa fa-user-circle"></i> Datos Iniciales</span>
                                                    </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a href="#objetivo_desarrollo" data-bs-toggle="tab"
                                                        aria-expanded="false" class="nav-link rounded-0">
                                                        <span class="d-none d-md-block"><i class="fa fa-tasks"></i>
                                                            Objetivo y Desarrollo</span>
                                                    </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a href="#evolucion_evaluacion" data-bs-toggle="tab"
                                                        aria-expanded="false" class="nav-link rounded-0">
                                                        <span class="d-none d-md-block"><i class="fa fa-line-chart"></i>
                                                            Evolución y Evaluación</span>
                                                    </a>
                                                </li>
                                            </ul>

                                            <div class="tab-content px-20">
                                                <!-- Datos Iniciales -->
                                                <div class="tab-pane show active" id="datos_iniciales">
                                                    <div class="row">
                                                        <div class="col-md-8">
                                                            <div class="form-group">
                                                                <label for="codConsultaConsulta"
                                                                    class="form-label">Código
                                                                    de
                                                                    consulta:</label>
                                                                <select class="form-control select2"
                                                                    id="codConsultaConsulta"
                                                                    name="codConsultaConsulta"></select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label for="fechaEvolucion"
                                                                class="form-label">Fecha:</label>

                                                            <div class="input-group">
                                                                <input type="date" class="form-control"
                                                                    id="fechaEvolucion" name="fechaEvolucion"
                                                                    placeholder="Seleccione la fecha de la evolución" />
                                                                <input type="time" id="horaSeleccionadad"
                                                                    name="horaSeleccionada" class="form-control">
                                                                <div class="input-group-addon">
                                                                    <i class="fa fa-clock-o"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label for="remision" class="form-label">Motivo de
                                                                    consulta:</label>
                                                                <textarea class="form-control" id="motivoConsultaModal" name="motivoConsultaModal" rows="3"
                                                                    placeholder="Ingrese de dónde es remitido el paciente.."></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label for="codImpresionDiagnosticoConsulta"
                                                                    class="form-label">Impresión Diagnóstica:</label>
                                                                <select class="form-control select2"
                                                                    id="codImpresionDiagnosticoConsulta"
                                                                    name="codImpresionDiagnosticoConsulta"></select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Objetivo y Desarrollo -->
                                                <div class="tab-pane" id="objetivo_desarrollo">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label for="objetivo_sesion" class="form-label">Objetivo
                                                                    de la Sesión:</label>
                                                                <textarea class="form-control" id="objetivo_sesion" name="objetivo_sesion" rows="3"
                                                                    placeholder="Ingrese el objetivo de la sesión"></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="tecnicas_utilizadas"
                                                                    class="form-label">Técnicas Utilizadas:</label>
                                                                <textarea class="form-control" id="tecnicas_utilizadas" name="tecnicas_utilizadas" rows="3"
                                                                    placeholder="Ingrese las técnicas utilizadas"></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="actividades_especificas"
                                                                    class="form-label">Actividades Específicas:</label>
                                                                <textarea class="form-control" id="actividades_especificas" name="actividades_especificas" rows="3"
                                                                    placeholder="Ingrese las actividades específicas"></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Evolución y Evaluación -->
                                                <div class="tab-pane" id="evolucion_evaluacion">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label for="evaluacion_indicadores"
                                                                    class="form-label">Evaluación / Indicadores de
                                                                    Éxito:</label>
                                                                <textarea class="form-control" id="evaluacion_indicadores" name="evaluacion_indicadores" rows="3"
                                                                    placeholder="Ingrese los indicadores de éxito"></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label for="evolucion_sesion"
                                                                    class="form-label">Evolución de la Sesión:</label>
                                                                <textarea class="form-control" id="evolucion_sesion" name="evolucion_sesion" rows="3"
                                                                    placeholder="Describa la evolución de la sesión"></textarea>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>

                                            <div class="box-footer text-end mt-3">
                                                <button onclick="cancelarConsulta()" type="button"
                                                    class="btn btn-primary-light me-1">
                                                    <i class="ti-share-alt"></i> Cancelar
                                                </button>
                                                <button onclick="guardarConsulta()" type="button"
                                                    class="btn btn-primary">
                                                    <i class="ti-save"></i> Guardar
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
        </div><!-- /.modal -->
    </div>

    <!-- MODAL PAQUETES -->
    <div class="modal fade" id="modalPaquete" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 70%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="tituloPaquete">Listado de preuba</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                    <form id="formPaquetes" style="display: none;">
                        <input type="hidden" name="accVentaPaquete" id="accVentaPaquete" value="guardar" />
                        <input type="hidden" name="idVentaPaquete" id="idVentaPaquete" value="" />
                        <input type="hidden" name="tipoHistoria" id="tipoHistoria" value="PSICOLOGIA" />
                        <input type="hidden" id="idPacienteVentaPaquete" name="idPacienteVentaPaquete" />
                        <input type="hidden" name="descripcionVentaPaquete" id="descripcionVentaPaquete"
                            value="" />
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
                            <div class="box-footer text-end">
                                <button type="button" id="cancelRegistroPaq" onclick="cancelarPaquetes();"
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
                    <div id="listPaquetes">
                        <div class="box-controls pull-right">
                            <div class="box-header-actions">
                                <div class="input-group input-group-merge">
                                    <input type="text" id="busquedaP" class="form-control">
                                    <div class="input-group-text" data-password="false">
                                        <span class="fa fa-search"></span>
                                    </div>
                                    <button type="button" onclick="nuevoPaquete();"
                                        class="btn btn-xs btn-primary font-bold"><i class="fa fa-plus"></i> Nuevo
                                        paquete</button>
                                </div>

                            </div>
                        </div>
                        <table id="tblPaquetes" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Descripción</th>
                                    <th>Fecha de compra</th>
                                    <th>Número de sesiones</th>
                                    <th>Sesiones faltantes</th>
                                    <th>Valor</th>
                                    <th>Estado</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody id="trRegistrosPaquetes">

                            </tbody>
                        </table>
                        <div id="pagination-links-paquetes" class="text-center ml-1 mt-2">
                        </div>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
    </div><!-- /.modal -->
    <!-- MODAL PLAN INTERVENCION -->
    <div class="modal fade" id="modalPlanIntervencion" tabindex="-1" role="dialog"
        aria-labelledby="myLargeModalLabel" aria-hidden="true">`
        <div class="modal-dialog modal-dialog-centered" style="max-width: 60%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Plan de intervención</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                    <div class="card">

                        <div class="card-body">
                            <div class="card-body">
                                <form id="formPlanIntervencion">
                                    @csrf <!-- Directiva para el token CSRF de Laravel -->
                                    <input type="hidden" id="idHistoriaPlan" name="idHistoriaPlan" />
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="objetivoGeneralModal" class="form-label">Objetivo
                                                    General:</label>
                                                <textarea class="form-control" id="objetivoGeneralModal" name="objetivoGeneralModal" rows="3"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="objetivoEspecificoModal" class="form-label">Objetivos
                                                    Específicos:</label>
                                                <textarea class="form-control" id="objetivoEspecificoModal" name="objetivoEspecificoModal" rows="3"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="sugerenciasModal" class="form-label">Sugerencia para
                                                    Interconsultas:</label>
                                                <textarea class="form-control" id="sugerenciasModal" name="sugerenciasModal" rows="3"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="observacionesModal" class="form-label">Observaciones y
                                                    Recomendaciones:</label>
                                                <textarea class="form-control" id="observacionesModal" name="observacionesModal" rows="3"></textarea>
                                            </div>
                                        </div>
                                        <div class="box-footer text-end mt-3">
                                            <button onclick="cancelarPlan()" type="button"
                                                class="btn btn-primary-light me-1">
                                                <i class="ti-share-alt"></i> Cancelar
                                            </button>
                                            <button onclick="guardarPlan()" type="button" class="btn btn-primary">
                                                <i class="ti-save"></i> Guardar
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
        </div><!-- /.modal -->
    </div>
    <div class="modal fade" id="modalVentaConsulta" tabindex="-1" role="dialog"
        aria-labelledby="myLargeModalLabel" aria-hidden="true">`
        <div class="modal-dialog modal-dialog-centered" style="max-width: 60%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Venta de consulta</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                    <div class="card">

                        <div class="card-body">
                            <div class="card-body">
                                <form id="formVentaConsulta">
                                    @csrf <!-- Directiva para el token CSRF de Laravel -->
                                    <input type="hidden" id="idHistoriaVenta" name="idHistoriaVenta" />
                                    <input type="hidden" id="idPacienteVenta" name="idPacienteVenta" />
                                    <input type="hidden" id="accHistoriaVenta" name="accHistoriaVenta" />
                                    <input type="hidden" id="idConsultaVenta" name="idConsultaVenta" />
                                    <input type="hidden" name="tipoHistoria" id="tipoHistoria"
                                        value="PSICOLOGIA" />
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label for="ConsultaVenta" class="form-label">Descripción de la
                                                    consulta:</label>
                                                <input type="text" class="form-control" id="ConsultaVenta"
                                                    name="ConsultaVenta">
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
                                                    onchange="cambioFormato(this.id);"
                                                    onkeypress="return validartxtnum(event)" onclick="this.select();">
                                                <input type="hidden" class="form-control" id="valorServConsulta"
                                                    name="valorServConsulta">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="estadoServicio" class="form-label">Estado servicio :</label>
                                                <input type="text" class="form-control" id="estadoServicio"
                                                    name="estadoServicio" readonly>

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
                            </div>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
        </div><!-- /.modal -->
    </div>
    <div class="modal fade" id="modalVentaSesion" tabindex="-1" role="dialog"
        aria-labelledby="myLargeModalLabel" aria-hidden="true">`
        <div class="modal-dialog modal-dialog-centered" style="max-width: 60%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="tituloVentaSesion" class="modal-title">Sesiones vendidas</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                    <div class="card">

                        <div class="card-body">
                            <div class="card-body">
                                <form style="display: none;" id="formVentaSesion">
                                    @csrf <!-- Directiva para el token CSRF de Laravel -->
                                    <input type="hidden" id="idHistoriaVentaSesion" name="idHistoriaVentaSesion" />
                                    <input type="hidden" id="idPacienteVentaSesion" name="idPacienteVentaSesion" />
                                    <input type="hidden" id="accHistoriaVentaSesion" name="accHistoriaVentaSesion" />
                                    <input type="hidden" id="idVentaSesion" name="idVentaSesion" />
                                    <input type="hidden" name="tipoHistoria" id="tipoHistoria"
                                        value="PSICOLOGIA" />

                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label for="sugerenciasModal" class="form-label">Descripción de la
                                                    sesión:</label>
                                                <input type="text" class="form-control" id="descripcionVentaSesion"
                                                    name="descripcionVentaSesion">
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
                                                    onchange="cambioFormato(this.id);"
                                                    onkeypress="return validartxtnum(event)" onclick="this.select();">
                                                <input type="hidden" class="form-control" id="valorServSesion"
                                                    name="valorServSesion">
                                            </div>
                                        </div>
                                        <div class="box-footer text-end mt-3">
                                            <button onclick="cancelarVentaSesion()" type="button"
                                                class="btn btn-primary-light me-1">
                                                <i class="ti-share-alt"></i> Cancelar
                                            </button>
                                            <button onclick="guardarVentaSesion()" type="button"
                                                id="saveRegistroVentaSesion" class="btn btn-primary">
                                                <i class="ti-save"></i> Guardar
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <div id="listVentaSesion">
                                    <div class="box-controls pull-right">
                                        <div class="box-header-actions">
                                            <div class="input-group input-group-merge">
                                                <button type="button" onclick="nuevaVentaSesion();"
                                                    class="btn btn-xs btn-primary font-bold"><i class="fa fa-plus"></i>
                                                    Nueva venta sesión</button>
                                            </div>

                                        </div>
                                    </div>
                                    <table id="tblPaquetes" class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>Descripción</th>
                                                <th>Fecha de compra</th>
                                                <th>Valor</th>
                                                <th>Estado</th>
                                                <th>Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody id="trRegistrosVentaSesion">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
        </div><!-- /.modal -->
    </div>

    <div id="loaderPacientes" style="display: none; text-align: center; padding: 10px;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Cargando...</span>
        </div>
    </div>
    <script>
        window.userPermissions = @json(Auth::user()->permissions);

        var idHistoriaImprimir = "";
        document.addEventListener("DOMContentLoaded", function() {
            let menuP = document.getElementById("principalHistoriClinica")
            let menuS = document.getElementById("principalHistoriClinicaPsicologia")

            menuP.classList.add("active", "menu-open")
            menuS.classList.add("active")
            let rtotal = $("#RutaTotal").data("ruta")

            ///verifica si viene de pacientes 
            var ultimaParteURLAnterior = document.referrer.split('/').filter(Boolean).pop()

            if (ultimaParteURLAnterior == "Gestionar" || ultimaParteURLAnterior == "Administracion") {
                let elemento = document.createElement('div')
                elemento.setAttribute('data-id', localStorage.getItem('idPaciente'))
                elemento.setAttribute('data-edad', localStorage.getItem('edadPaciente'))
                if (localStorage.getItem('idPaciente')) {
                    cagaHistPaciente(elemento)
                    mapearDatosProfesional(document.getElementById("idUsuario").value)
                }
            }

            //Initialize Select2 Elements
            $('.select2').select2()

            $('#motivoConsultaOtro').select2({
                placeholder: "Seleccione otros motivos relacionados"
            })

            $('.examen').select2({
                placeholder: "Seleccione..."
            });

            $('#selPaquete').select2({
                dropdownParent: $('#modalPaquete'),
                width: '100%'
            });



            //VALIDAR FORMULARIO DE PAQUETES

            $("#formPaquetes").validate({
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

            //VALIDAR FORMULARIO VENTA CONSULTA
            $("#formVentaConsulta").validate({
                rules: {
                    ConsultaVenta: {
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
                    ConsultaVenta: {
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
            ////

            //VALIDAR FORMULARIO VENTA SESION
            $("#formVentaSesion").validate({
                rules: {
                    descripcionVentaSesion: {
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
                    descripcionVentaSesion: {
                        required: "Por favor, ingrese la descripción de la sesión"
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
            ////

            $('#codConsulta').select2({
                dropdownAutoWidth: true,
                width: '100%',
                placeholder: 'Buscar consulta por código o nombre...',
                language: {
                    inputTooShort: function() {
                        return 'Por favor, ingresa al menos un carácter'
                    },
                    noResults: function() {
                        return 'No se encontraron resultados.'
                    },
                    searching: function() {
                        return 'Buscando...'
                    }
                },
                minimumInputLength: 1, // Requiere al menos 1 carácter
                ajax: {
                    transport: function(params, success, failure) {
                        const query = params.data.q || '' // Término de búsqueda
                        const page = params.data.page || 1 // Número de página

                        fetch(`${rtotal}historia/buscaCUPS?q=${query}&page=${page}`, {
                                method: 'GET',
                                headers: {
                                    'Content-Type': 'application/json',
                                },
                                cache: 'no-cache'
                            })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Error en la solicitud')
                                }
                                return response.json()
                            })
                            .then(data => {

                                const results = {
                                    results: data.data,
                                    pagination: {
                                        more: (page * 30) < data.total_count
                                    }
                                }
                                success(results) // Envía los resultados a Select2
                            })
                            .catch(error => {
                                console.error('Error al buscar:', error)
                                failure(error) // Maneja errores en Select2
                            })
                    }
                },
                escapeMarkup: function(markup) {
                    return markup // Evita inyección de HTML
                }
            })
            $('#codConsultaConsulta').select2({
                dropdownAutoWidth: true,
                width: '100%',
                dropdownParent: $('#modalConsulta'),
                placeholder: 'Buscar consulta por código o nombre...',
                language: {
                    inputTooShort: function() {
                        return 'Por favor, ingresa al menos un carácter'
                    },
                    noResults: function() {
                        return 'No se encontraron resultados.'
                    },
                    searching: function() {
                        return 'Buscando...'
                    }
                },
                minimumInputLength: 1, // Requiere al menos 1 carácter
                ajax: {
                    transport: function(params, success, failure) {
                        const query = params.data.q || '' // Término de búsqueda
                        const page = params.data.page || 1 // Número de página

                        fetch(`${rtotal}historia/buscaCUPS?q=${query}&page=${page}`, {
                                method: 'GET',
                                headers: {
                                    'Content-Type': 'application/json',
                                },
                                cache: 'no-cache'
                            })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Error en la solicitud')
                                }
                                return response.json()
                            })
                            .then(data => {

                                const results = {
                                    results: data.data,
                                    pagination: {
                                        more: (page * 30) < data.total_count
                                    }
                                }
                                success(results) // Envía los resultados a Select2
                            })
                            .catch(error => {
                                console.error('Error al buscar:', error)
                                failure(error) // Maneja errores en Select2
                            })
                    }
                },
                escapeMarkup: function(markup) {
                    return markup // Evita inyección de HTML
                }
            })

            $('#codDiagnostico').select2({
                dropdownAutoWidth: true,
                width: '100%',
                placeholder: 'Buscar diagnóstico  por código o nombre...',
                language: {
                    inputTooShort: function() {
                        return 'Por favor, ingresa al menos un carácter'
                    },
                    noResults: function() {
                        return 'No se encontraron resultados.'
                    },
                    searching: function() {
                        return 'Buscando...'
                    }
                },
                minimumInputLength: 1, // Requiere al menos 1 carácter
                ajax: {
                    transport: function(params, success, failure) {
                        const query = params.data.q || '' // Término de búsqueda
                        const page = params.data.page || 1 // Número de página

                        fetch(`${rtotal}historia/buscaCIE?q=${query}&page=${page}`, {
                                method: 'GET',
                                headers: {
                                    'Content-Type': 'application/json',
                                },
                                cache: 'no-cache'
                            })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Error en la solicitud')
                                }
                                return response.json()
                            })
                            .then(data => {
                                const results = {
                                    results: data.data,
                                    pagination: {
                                        more: (page * 30) < data.total_count
                                    }
                                }
                                success(results) // Envía los resultados a Select2
                            })
                            .catch(error => {
                                console.error('Error al buscar:', error)
                                failure(error) // Maneja errores en Select2
                            })
                    }
                },
                escapeMarkup: function(markup) {
                    return markup // Evita inyección de HTML
                }
            })

            $('#codImpresionDiagnostico').select2({
                dropdownAutoWidth: true,
                width: '100%',
                placeholder: 'Buscar diagnóstico  por código o nombre...',
                language: {
                    inputTooShort: function() {
                        return 'Por favor, ingresa al menos un carácter'
                    },
                    noResults: function() {
                        return 'No se encontraron resultados.'
                    },
                    searching: function() {
                        return 'Buscando...'
                    }
                },
                minimumInputLength: 1, // Requiere al menos 1 carácter
                ajax: {
                    transport: function(params, success, failure) {
                        const query = params.data.q || '' // Término de búsqueda
                        const page = params.data.page || 1 // Número de página

                        fetch(`${rtotal}historia/buscaCIE?q=${query}&page=${page}`, {
                                method: 'GET',
                                headers: {
                                    'Content-Type': 'application/json',
                                },
                                cache: 'no-cache'
                            })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Error en la solicitud')
                                }
                                return response.json()
                            })
                            .then(data => {
                                const results = {
                                    results: data.data,
                                    pagination: {
                                        more: (page * 30) < data.total_count
                                    }
                                }
                                success(results) // Envía los resultados a Select2
                            })
                            .catch(error => {
                                console.error('Error al buscar:', error)
                                failure(error) // Maneja errores en Select2
                            })
                    }
                },
                escapeMarkup: function(markup) {
                    return markup // Evita inyección de HTML
                }
            })

            $('#codImpresionDiagnosticoConsulta').select2({
                dropdownAutoWidth: true,
                width: '100%',
                dropdownParent: $('#modalConsulta'),
                placeholder: 'Buscar diagnóstico  por código o nombre...',
                language: {
                    inputTooShort: function() {
                        return 'Por favor, ingresa al menos un carácter'
                    },
                    noResults: function() {
                        return 'No se encontraron resultados.'
                    },
                    searching: function() {
                        return 'Buscando...'
                    }
                },
                minimumInputLength: 1, // Requiere al menos 1 carácter
                ajax: {
                    transport: function(params, success, failure) {
                        const query = params.data.q || '' // Término de búsqueda
                        const page = params.data.page || 1 // Número de página

                        fetch(`${rtotal}historia/buscaCIE?q=${query}&page=${page}`, {
                                method: 'GET',
                                headers: {
                                    'Content-Type': 'application/json',
                                },
                                cache: 'no-cache'
                            })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Error en la solicitud')
                                }
                                return response.json()
                            })
                            .then(data => {
                                const results = {
                                    results: data.data,
                                    pagination: {
                                        more: (page * 30) < data.total_count
                                    }
                                }
                                success(results) // Envía los resultados a Select2
                            })
                            .catch(error => {
                                console.error('Error al buscar:', error)
                                failure(error) // Maneja errores en Select2
                            })
                    }
                },
                escapeMarkup: function(markup) {
                    return markup // Evita inyección de HTML
                }
            })

            const ids = ['enfermedadActual',
                'observaciones_recomendaciones',
                'sugerencia_interconsultas',
                'objetivos_especificos',
                'objetivo_general',
                'medicacion',
                'remision',
                'motivoConsultaModal',
                'resumen_evaluacion_inicial',
                'objetivo_sesion',
                'motivoConsulta',
                'tecnicas_utilizadas',
                'actividades_especificas',
                'evaluacion_indicadores',
                'evolucion_sesion',
                'intervencion_psiquiatria',
                'intervencion_neurologia',
                'intervencion_neuropsicologia',
                'sugerenciasModal',
                'observacionesModal',
                'objetivoGeneralModal',
                'objetivoEspecificoModal'

            ]

            $(function() {
                "use strict"
                ids.forEach(id => {
                    CKEDITOR.replace(id, {
                        extraPlugins: 'uploadimage,pastefromword,maximize',
                        toolbar: [{
                                name: 'basicstyles',
                                items: ['Bold', 'Italic', 'Underline']
                            }, // Formato básico
                            {
                                name: 'paragraph',
                                items: ['NumberedList', 'BulletedList']
                            }, // Listas
                            {
                                name: 'undo',
                                items: ['Undo', 'Redo']
                            }, // Deshacer/rehacer
                            {
                                name: 'maximize',
                                items: ['Maximize']
                            } // Maximizar
                        ],
                        removePlugins: 'elementspath,mediaembed,flash,image', // Eliminar plugins innecesarios
                        language: 'es', // Idioma en español
                        height: 100, // Altura del editor ajustada
                        resize_enabled: false, // Deshabilitar redimensionamiento del editor
                    })
                })
            })

            menuP.classList.add("active")

            loader = document.getElementById('loader')
            loadNow(1)

            //carga de categorias
            cargarCategorias()
            cargarProfesionales()
            cargarHistorias(1)

            // Evento click para la paginación
            document.addEventListener('click', function(event) {
                if (event.target.matches('.pagination a')) {
                    event.preventDefault()
                    var href = event.target.getAttribute('href')
                    var page = href.split('page=')[1]

                    // Asegurarse de que 'page' sea un número antes de hacer la solicitud
                    if (!isNaN(page)) {
                        cargarHistorias(page)
                    }
                }
            })
            // Evento input para el campo de búsqueda
            document.getElementById('busqueda').addEventListener('input', function() {
                var searchTerm = this.value
                cargarHistorias(1,
                    searchTerm)
            })

            document.getElementById('busquedaPa').addEventListener('input', function() {
                var searchTerm = this.value
                cargarPacientes(1,
                    searchTerm)
            })

            document.getElementById('busquedaCo').addEventListener('input', function() {
                var searchTerm = this.value
                cargarConsultas(1,
                    searchTerm)
            })
            document.getElementById('busquedaP').addEventListener('input', function() {
                var searchTerm = this.value
                cargarPaquetes(1,
                    searchTerm)
            })

            document.addEventListener('click', function(event) {
                if (event.target.matches('.pagPac a')) {
                    event.preventDefault()
                    var href = event.target.getAttribute('href')
                    var page = href.split('page=')[1]

                    // Asegurarse de que 'page' sea un número antes de hacer la solicitud
                    if (!isNaN(page)) {
                        cargarPacientes(page)
                    }
                }
            })

        })

        function hasPermission(permission) {
            return window.userPermissions && window.userPermissions.includes(permission);
        }

        function cargarProfesionales() {
            let urlProfesionales = "{{ route('profesionales.cargarListaProf') }}" // Definir la URL

            fetch(urlProfesionales)
                .then(response => response.json())
                .then(data => {
                    const selectProfesional = document.getElementById('profesionalSelect');
                    llenarSelect(selectProfesional, data);
                })
                .catch(error => console.error('Error al cargar profesionales:', error));
        }

        function llenarSelect(selectElement, data) {
            selectElement.innerHTML = '<option value="">Seleccione una opción</option>';
            data.forEach(item => {
                const option = document.createElement('option');
                option.value = item.id;
                option.textContent = item.nombre;
                option.setAttribute('data-id', item.usuario);
                selectElement.appendChild(option);
            });
        }

        function cagaHistPaciente(element) {
            let idPaciente = element.getAttribute("data-id")
            let edadPaciente = parseInt(element.getAttribute("data-edad"), 10)
            let tipoPsicologia = ""
            if (edadPaciente < 18) {
                tipoPsicologia = "Pediatría"
                document.getElementById("infPediatria").style.display = "initial"
            } else {
                tipoPsicologia = "Adulto"
                document.getElementById("infPediatria").style.display = "none"

            }

            let tipoText = document.getElementById("tipoPsicologia")
            tipoText.value = tipoPsicologia

            document.getElementById('idPaciente').value = idPaciente
            mostrarInformacionHistoria(idPaciente)
        }

        function cancelarHistoria() {
            document.getElementById('listado').style.display = 'block'
            document.getElementById('historia').style.display = 'none'
        }

        function cancelarConsulta() {
            document.getElementById('listadoConsultas').style.display = 'block'
            document.getElementById('fomrConsultas').style.display = 'none'
            document.getElementById("titConsulta").innerHTML = "Gestionar consulta"

        }

        function cancelarPaquetes() {
            document.getElementById('listPaquetes').style.display = 'block'
            document.getElementById('formPaquetes').style.display = 'none'
            document.getElementById("tituloPaquete").innerHTML = "Listado de paquetes"
        }

        function cargarHistorias(page, searchTerm = '') {

            let url = "{{ route('HistoriasClinicas.listaHistoriasPsicologica') }}" // Definir la URL

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
                    document.getElementById('hisoriasListado').innerHTML = responseData.historias
                    feather.replace()
                    // Colxocar los enlaces de paginación
                    document.getElementById('pagination-links').innerHTML = responseData.links
                    loadNow(0)
                })
                .catch(error => console.error('Error:', error))

        }

        function cargarCategorias() {
            return new Promise((resolve, reject) => {

                let url = "{{ route('hitoriaPsicologica.categorias') }}"
                const categoriaMap = {
                    motivoConsultaOtro: 'MOTIVO DE CONSULTA',
                    edad: 'APARIENCIA PERSONAL: Edad',
                    desarrollo: 'APARIENCIA PERSONAL: Desarrollo pondoestatural',
                    aseo: 'APARIENCIA PERSONAL: Aseo y arreglo',
                    salud: 'APARIENCIA PERSONAL: Salud somática',
                    facies: 'APARIENCIA PERSONAL: Facies',
                    biotipo: 'APARIENCIA PERSONAL: Biotipo',
                    actitud: 'APARIENCIA PERSONAL: Actitud',
                    consciencia: 'FUNCIONES COGNITIVAS: Consciencia',
                    orientacion: 'FUNCIONES COGNITIVAS: Orientación',
                    memoria: 'FUNCIONES COGNITIVAS: Memoria',
                    atencion: 'FUNCIONES COGNITIVAS: Atencion',
                    concentracion: 'FUNCIONES COGNITIVAS: Concentración',
                    lenguaje: 'FUNCIONES COGNITIVAS: Lenguaje',
                    pensamiento: 'FUNCIONES COGNITIVAS: Pensamiento',
                    afecto: 'FUNCIONES COGNITIVAS: Afecto',
                    sensopercepcion: 'FUNCIONES COGNITIVAS: Sensopercepcion',
                    psicomotricidad: 'FUNCIONES COGNITIVAS: Psicomotricidad',
                    juicio: 'FUNCIONES COGNITIVAS: Juicio',
                    inteligencia: 'FUNCIONES COGNITIVAS: Inteligencia',
                    conciencia_enfermedad: 'FUNCIONES COGNITIVAS: Conciencia de enfermedad',
                    sufrimiento_psicologico: 'FUNCIONES COGNITIVAS: Sufrimiento psicológico',
                    motivacion_tratamiento: 'FUNCIONES COGNITIVAS: Motivación al tratamiento',
                    plan_intervencion: 'PLAN DE INTERVENCIÓN',
                }

                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        // Recorrer el mapa de categorías
                        Object.keys(categoriaMap).forEach(selectId => {
                            const categoriaNom = categoriaMap[selectId]

                            // Filtrar las opciones de la categoría correspondiente
                            const categoria = data.find(cat => cat.nombre === categoriaNom)
                            if (categoria) {
                                const select = document.getElementById(selectId)
                                let defaultOption = document.createElement("option");
                                defaultOption.value = ""; // Valor en blanco
                                defaultOption.text = "Selecciona una opción"; // Texto que se mostrará
                                defaultOption.selected = true; // Que aparezca seleccionada por defecto
                                select.appendChild(defaultOption);
                                if (select) {
                                    categoria.opciones.forEach(opcion => {
                                        const option = document.createElement('option')
                                        option.value = opcion.id
                                        option.textContent = opcion.opcion
                                        option.placeholder = "Seleccione una opción"
                                        option.setAttribute('data-nombre', opcion.opcion
                                            .toLowerCase())
                                        select.appendChild(option)
                                    })
                                }
                            }
                        })
                    })
                    .catch(error => console.error('Error cargando las opciones:', error))
            })
        }

        function nuevoRegistro() {

            var modal = new bootstrap.Modal(document.getElementById("modalHistoria"), {
                backdrop: 'static',
                keyboard: false
            })
            modal.show()
            limpiarHistoria()
            var btnGuardar = document.getElementById("btn-guardarHistoria")
            btnGuardar.disabled = false

            document.getElementById("loaderPacientes").style.display = "block";

            cargarPacientes(1)
            mapearDatosProfesional(document.getElementById("idUsuario").value)
            document.getElementById("btn-imprimirHistoria").style.display = "none"
            document.getElementById("historialConsulta").innerHTML = ""
        }

        function limpiarHistoria() {
            let formHistoria = document.getElementById("formHistoria")
            formHistoria.reset()

            for (var instanceName in CKEDITOR.instances) {
                CKEDITOR.instances[instanceName].setData('');
            }

            $('#motivoConsultaOtro').val(null).trigger('change');
            $('#codConsulta').val(null).trigger('change');
            $('#codDiagnostico').val(null).trigger('change');
            $('#codImpresionDiagnostico').val(null).trigger('change');
            document.querySelectorAll('input[data-role="tagsinput"]').forEach(input => {
                $(input).tagsinput('removeAll');
            });
        }

        function limpiarConsulta() {
            let formHistoria = document.getElementById("formConsulta")
            formHistoria.reset()

            CKEDITOR.instances['motivoConsultaModal'].setData('')
            CKEDITOR.instances['objetivo_sesion'].setData('')
            CKEDITOR.instances['tecnicas_utilizadas'].setData('')
            CKEDITOR.instances['actividades_especificas'].setData('')
            CKEDITOR.instances['evaluacion_indicadores'].setData('')
            CKEDITOR.instances['evolucion_sesion'].setData('')

            $('#codConsultaConsulta').val(null).trigger('change');
            $('#codImpresionDiagnosticoConsulta').val(null).trigger('change');
        }

        function nuevoRegistroConsulta() {
            document.getElementById("listadoConsultas").style.display = "none"
            document.getElementById("fomrConsultas").style.display = "initial"
            document.getElementById("titConsulta").innerHTML = "Agregar evolución clínica"
            document.getElementById("accHistoriaConsulta").value = "guardar"
            limpiarConsulta()
        }

        function cargarPacientes(page, searchTerm = '') {
            let url = "{{ route('pacientes.listaPacientesModal') }}"

            var oldPageInput = document.getElementById('pagePac')
            var oldSearchTermInput = document.getElementById('busquedaPac')
            if (oldPageInput) oldPageInput.remove()
            if (oldSearchTermInput) oldSearchTermInput.remove()

            var data = {
                page: page,
                search: searchTerm
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

                    document.getElementById('trRegistrosPacientes').innerHTML = responseData.pacientes
                    feather.replace()

                    document.getElementById('pagination-links-pacientes').innerHTML = responseData.links
                    loadNow(0)
                })
                .catch(error => console.error('Error:', error))
                .finally(() => {
                    // Ocultar loader después de recibir la respuesta (éxito o error)
                    document.getElementById("loaderPacientes").style.display = "none";
                })
        }

        function seleccionarPaciente(element) {

            let idPaciente = element.getAttribute("data-id")
            let edadPaciente = parseInt(element.getAttribute("data-edad"), 10)
            let tipoPsicologia
            if (edadPaciente < 18) {
                tipoPsicologia = "Pediatría"
                document.getElementById("infPediatria").style.display = "initial"
            } else {
                tipoPsicologia = "Adulto"
                document.getElementById("infPediatria").style.display = "none"

            }

            let tipoText = document.getElementById("tipoPsicologia")
            tipoText.value = tipoPsicologia

            document.getElementById('idPaciente').value = idPaciente
            const modal = document.getElementById('modalHistoria')
            const modalInstance = bootstrap.Modal.getInstance(modal)
            modalInstance.hide()

            mostrarInformacionHistoria(idPaciente)
        }

        function toggleOtro(selectElement) {
            const inputId = selectElement.id + "_otro"
            const inputField = document.getElementById(inputId)

            // Obtener el atributo data-nombre del elemento seleccionado
            
            const selectedOption = selectElement.options[selectElement.selectedIndex]
            const dataNombre = selectedOption ? selectedOption.getAttribute('data-nombre') : null

            if (dataNombre === "otro") {
                inputField.classList.remove("d-none")
                inputField.focus()
            } else {
                inputField.classList.add("d-none")
            }
        }

        function eliminarHistoria(idHistoria) {
            if (hasPermission('editarHistoria')) {
                swal({
                    title: "Esta seguro de eliminar esta historia ?",
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
                        let url = "{{ route('historia.eliminarHistoria') }}";
                        fetch(url, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                        .getAttribute(
                                            'content')
                                },
                                body: JSON.stringify({
                                    idHistoria: idHistoria
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    swal("¡Buen trabajo!",
                                        data.message,
                                        "success")
                                    cargarHistorias(1)
                                } else {
                                    swal("¡Alerta!",
                                        "La operación fue realizada exitosamente",
                                        data.message,
                                        "success")
                                }
                            })
                    }
                })
            } else {
                swal("¡Alerta!",
                    "No tiene el permiso necesario para realizar esta acción",
                    "warning")
            }
        }

        function cerrarHistoria(element) {
            let idHist = element.getAttribute("data-id")
            let estado = element.getAttribute("data-estado")

            if (hasPermission('editarHistoria')) {
                if (estado == "abierta") {
                    swal({
                        title: "Cerrar historia clinica del paciente.",
                        text: "Al cerrar la historia clicnica del paciente esta no podra ser editada, ¿Desea cerrar la historia?",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Si, cerrarla!",
                        cancelButtonText: "Cancelar",
                        confirmButtonClass: "btn btn-warning",
                        cancelButtonClass: "btn btn-danger ml-1",
                        buttonsStyling: false
                    }, function(isConfirm) {
                        if (isConfirm) {
                            let url = "{{ route('historia.cerrarHistoria') }}"
                            fetch(url, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                            .getAttribute(
                                                'content')
                                    },
                                    body: JSON.stringify({
                                        idHist: idHist,
                                        estado: estado
                                    })
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        swal("¡Buen trabajo!",
                                            data.message,
                                            "success");
                                        cargarHistorias(1);
                                    } else {
                                        swal("¡Alerta!",
                                            "La operación fue realizada exitosamente",
                                            data.message,
                                            "success");
                                    }
                                })
                        }
                    })
                } else {
                    swal({
                        title: "Abrir historia clinica del paciente.",
                        text: "Al abrir la historia clicnica del paciente esta podra ser editada, ¿Desea abrir la historia?",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Si, abrirla!",
                        cancelButtonText: "Cancelar",
                        confirmButtonClass: "btn btn-warning",
                        cancelButtonClass: "btn btn-danger ml-1",
                        buttonsStyling: false
                    }, function(isConfirm) {
                        if (isConfirm) {
                            let url = "{{ route('historia.cerrarHistoria') }}";
                            fetch(url, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                            .getAttribute(
                                                'content')
                                    },
                                    body: JSON.stringify({
                                        idHist: idHist,
                                        estado: estado
                                    })
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        swal("¡Buen trabajo!",
                                            data.message,
                                            "success")
                                        cargarHistorias(1)
                                    } else {
                                        swal("¡Alerta!",
                                            "La operación fue realizada exitosamente",
                                            data.message,
                                            "success")
                                    }
                                })
                        }
                    })
                }
            } else {
                swal("¡Alerta!",
                    "No tiene el permiso necesario para realizar esta acción",
                    "warning")
            }
        }

        function mostrarInformacionHistoria(idPaciente) {
            let url = "{{ route('pacientes.buscaPacienteHistoria') }}"

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
                    //DATOS DEL PACIENTE
                    if (data.historia) {
                        swal({
                            title: "Este paciente ya tiene una historia clínica registrada.",
                            text: "Desea abrir la historia",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#3085d6",
                            cancelButtonColor: "#d33",
                            confirmButtonText: "Si, abrirla!",
                            cancelButtonText: "Cancelar",
                            confirmButtonClass: "btn btn-warning",
                            cancelButtonClass: "btn btn-danger ml-1",
                            buttonsStyling: false
                        }, function(isConfirm) {
                            if (isConfirm) {
                                let elemento = document.createElement('div'); // O cualquier otro elemento
                                elemento.setAttribute('data-id', data.historia.id);
                                elemento.setAttribute('data-tipo', data.historia.tipologia);
                                verHistoria(elemento)
                                document.getElementById("btn-imprimirHistoria").style.display = "initial"
                            }
                        })
                    } else {
                        mapearInfPaciente(data.paciente)
                        document.getElementById('listado').style.display = 'none'
                        document.getElementById('historia').style.display = 'block'
                    }
                })
                .catch(error => console.error('Error:', error))
        }

        function mapearInfPaciente(paciente) {

            var foto = paciente.foto
            const previewImage = document.getElementById('imgPaciente')
            let url = $('#Ruta').data("ruta")
            previewImage.src = url + "/images/FotosPacientes/" + foto

            document.getElementById("nombrePaciente").innerHTML =
                `${paciente.primer_nombre} ${paciente.primer_apellido} `
            document.getElementById("edadPaciente").innerHTML = paciente.edadTexto

            document.getElementById("identificacionPacienteHist").innerHTML =
                `${paciente.tipo_identificacion} - ${paciente.identificacion}`
            document.getElementById("nombreCompletoPacienteHist").innerHTML =
                `${paciente.primer_nombre} ${paciente.segundo_nombre} ${paciente.primer_apellido} ${paciente.segundo_apellido} `


            var fechForm = convertirFecha(paciente.fecha_nacimiento)
            document.getElementById("fechaNacimeintoPacienteHist").innerHTML =
                `${fechForm} (${paciente.edadTexto})`
            document.getElementById("tipoUsuarioPacienteHist").innerHTML = tipoUsuario(paciente
                .tipo_usuario)

            let sexo =
                (paciente.sexo === "H") ? "Hombre" :
                (paciente.sexo === "M") ? "Mujer" :
                "Indeterminado o Intersexual"

            document.getElementById("sexoPacienteHist").innerHTML = sexo

            document.getElementById("emailPacienteHist").innerHTML = paciente.email
            document.getElementById("telefonoPacienteHist").innerHTML = paciente.telefono
            document.getElementById("direccionPacienteHist").innerHTML = paciente.direccion

            let zona = (paciente.zona_residencial === "01") ? "Rural" : "Urbano"

            document.getElementById("zonaResidencialPacienteHist").innerHTML = zona

            document.getElementById("accHistoria").value = 'guardar'
        }

        function tipoUsuario(tipUsuario) {
            let usuario =
                (tipUsuario === "01") ? "Contributivo cotizante" :
                (tipUsuario === "02") ? "Contributivo beneficiario" :
                (tipUsuario === "03") ? "Contributivo adicional" :
                (tipUsuario === "04") ? "Subsidiado" :
                (tipUsuario === "05") ? "No afiliado" :
                (tipUsuario === "06") ? "Especial o Excepcion cotizante" :
                (tipUsuario === "07") ? "Especial o Excepcion beneficiario" :
                (tipUsuario === "08") ? "Personas privadas de la libertad a cargo del Fondo Nacional de Salud" :
                (tipUsuario === "09") ? "Tomador / Amparado ARL" :
                (tipUsuario === "10") ? "Tomador / Amparado SOAT" :
                "Sin Especificar"

            return usuario
        }

        function convertirFecha(fecha) {
            // Dividir la fecha en año, mes y día
            const [año, mes, dia] = fecha.split('-')
            /**
             * 
             **/
            //Formatear la fecha en el formato dd/mm/yyyy
            const fechaFormateada = `${dia.padStart(2, '0')}/${mes.padStart(2, '0')}/${año}`

            return fechaFormateada
        }

        function evolucionHistoria(element) {
            let idHist = element.getAttribute("data-id")
            let estadoHis = element.getAttribute("data-estado")
            document.getElementById("idHistoria").value = idHist
            document.getElementById("estadoHistoria").value = estadoHis
            abrirConsultas(1)
        }

        function abrirConsultas(op) {
            if (document.getElementById("idHistoria").value != "") {
                if (document.getElementById("estadoHistoria").value == "cerrada") {
                    var modal = new bootstrap.Modal(document.getElementById("modalConsulta"), {
                        backdrop: 'static',
                        keyboard: false
                    })

                    modal.show()
                    if (op == 1) {
                        document.getElementById("listadoConsultas").style.display = "initial"
                        document.getElementById("fomrConsultas").style.display = "none"
                    }

                    cargarConsultas(1)
                } else {
                    swal("¡Atención!",
                        "Para gestionar las evoluciones la historia clinica debe estar cerrada.",
                        "warning");
                }

            } else {
                swal("¡Atención!",
                    "El paciente no puede ser evolucionado ya que no cuenta con una historia clínica en el sistema.",
                    "warning");
            }

        }

        function cargarConsultas(page, searchTerm = '') {

            let url = "{{ route('historia.listaConsultasModal') }}" // Definir la URL
            // Eliminar los campos ocultos anteriores
            var oldPageInput = document.getElementById('pageConsulta')
            var oldSearchTermInput = document.getElementById('busquedaConsulta')
            if (oldPageInput) oldPageInput.remove()
            if (oldSearchTermInput) oldSearchTermInput.remove()

            var data = {
                page: page,
                search: searchTerm,
                idHist: document.getElementById("idHistoria").value
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
                    document.getElementById("historialConsulta").innerHTML = responseData.historialConsultas
                    document.getElementById('trRegistrosConsultas').innerHTML = responseData.consultas
                    feather.replace()
                    // Colocar los enlaces de paginación
                    document.getElementById('pagination-links-consulta').innerHTML = responseData.links
                    loadNow(0)
                })
                .catch(error => console.error('Error:', error))
        }

        function guardarHistoria() {
            if ($("#formHistoria").valid()) {
                // Actualizar elementos de CKEditor
                for (var instanceName in CKEDITOR.instances) {
                    CKEDITOR.instances[instanceName].updateElement();
                }

                // Obtener el formulario y preparar los datos
                const formHistoria = document.getElementById('formHistoria');
                const formData = new FormData(formHistoria);
                document.getElementById('primeraVez').checked ?
                    formData.append('primeraVez', '1') :
                    formData.append('primeraVez', '0');

                const url = "{{ route('form.guardarHistoriaPsicologica') }}";

                var error = validarFormularioEnvio();
                if (!error) {
                    fetch(url, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content')
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success === 'success') { // Comparación estricta
                                document.getElementById("idHistoria").value = data.id;
                                cargarHistorias(1);
                                var btnGuardar = document.getElementById("btn-guardarHistoria");
                                btnGuardar.disabled = true;

                                swal(data.title, data.message, data.success)



                            } else {
                                Swal.fire(data.title, data.message, "error");
                            }
                        })
                        .catch(error => {
                            console.error("Error al enviar los datos:", error);
                        });
                }
            }
        }

        function validarFormularioEnvio() {

            var error = false;
            var mensaje = "";
            if (document.getElementById("codConsulta").value == "") {
                error = true;
                mensaje += "Debe seleccionar un código de consulta. \n";
            }

            if (document.getElementById("codDiagnostico").value == "") {
                error = true;
                mensaje += "Debe seleccionar un código de diagnóstico. \n";
            }
          
            if (document.getElementById("enfermedadActual").value == "") {
                error = true;
                mensaje += "Debe ingresar la enfermedad actual. \n";
            }
                                  
            if (document.getElementById("medicacion").value == "") {
                error = true;
                mensaje += "Debe ingresar la medicación. \n";
            }
            if (document.getElementById("remision").value == "") {
                error = true;
                mensaje += "Debe ingresar la remisión. \n";
            }
            if (document.getElementById("motivoConsulta").value == "") {
                error = true;
                mensaje += "Debe ingresar el motivo de la consulta. \n";
            }
            if (document.getElementById("resumen_evaluacion_inicial").value == "") {
                error = true;
                mensaje += "Debe ingresar el resumen de la evaluación inicial. \n";
            }
            if (document.getElementById("plan_intervencion").value == "") {
                error = true;
                mensaje += "Debe ingresar el plan de intervención. \n";
            }
            if (document.getElementById("idProfesional").value == "") {
                error = true;
                mensaje += "Debe seleccionar un profesional. \n";
            }

            if(document.getElementById("edad").value == ""){
                error = true;
                mensaje += "Debe ingresar en apariencia personal la edad. \n";
            }

            if(document.getElementById("desarrollo").value == ""){
                error = true;
                mensaje += "Debe ingresar en apariencia personal el pondoestatural. \n";
            }

            if(document.getElementById("aseo").value == ""){
                error = true;
                mensaje += "Debe ingresar en apariencia personal el aseo. \n";
            }

            if(document.getElementById("salud").value == ""){
                error = true;
                mensaje += "Debe ingresar en apariencia personal la salud. \n";
            }

            if(document.getElementById("facies").value == ""){
                error = true;
                mensaje += "Debe ingresar en apariencia personal la facies. \n";
            }

            if(document.getElementById("biotipo").value == ""){
                error = true;
                mensaje += "Debe ingresar en apariencia personal el biotipo. \n";
            }

            if(document.getElementById("actitud").value == ""){
                error = true;
                mensaje += "Debe ingresar en apariencia personal la actitud. \n";
            }

            if(document.getElementById("consciencia").value == ""){
                error = true;
                mensaje += "Debe ingresar en funciones cognitivas la consciencia. \n";
            }

            if(document.getElementById("orientacion").value == ""){
                error = true;
                mensaje += "Debe ingresar en apariencia personal la orientacion. \n";
            }

            if(document.getElementById("memoria").value == ""){
                error = true;
                mensaje += "Debe ingresar en funciones cognitivas la memoria. \n";
            }

            if(document.getElementById("atencion").value == ""){
                error = true;
                mensaje += "Debe ingresar en funciones cognitivas la atención. \n";
            }

            if(document.getElementById("concentracion").value == ""){
                error = true;
                mensaje += "Debe ingresar en funciones cognitivas la concentración. \n";
            }

            if(document.getElementById("lenguaje").value == ""){
                error = true;
                mensaje += "Debe ingresar en funciones cognitivas el lenguaje. \n";
            }

            if(document.getElementById("pensamiento").value == ""){
                error = true;
                mensaje += "Debe ingresar en funciones cognitivas el pensamiento. \n";
            }

            if(document.getElementById("afecto").value == ""){
                error = true;
                mensaje += "Debe ingresar en funciones cognitivas el afecto. \n";
            }

            if(document.getElementById("sensopercepcion").value == ""){
                error = true;
                mensaje += "Debe ingresar en funciones cognitivas la sensopercepción. \n";
            }
            
            if(document.getElementById("psicomotricidad").value == ""){
                error = true;
                mensaje += "Debe ingresar en funciones cognitivas la psicomotricidad. \n";
            }

            if(document.getElementById("juicio").value == ""){
                error = true;
                mensaje += "Debe ingresar en funciones cognitivas el juicio. \n";
            }

            if(document.getElementById("inteligencia").value == ""){
                error = true;
                mensaje += "Debe ingresar en funciones cognitivas la inteligencia. \n";
            }

            if(document.getElementById("conciencia_enfermedad").value == ""){
                error = true;
                mensaje += "Debe ingresar en funciones cognitivas la conciencia de la enfermedad. \n";
            }

            if(document.getElementById("sufrimiento_psicologico").value == ""){
                error = true;
                mensaje += "Debe ingresar en funciones cognitivas el sufrimiento psicológico. \n";
            }

            if(document.getElementById("motivacion_tratamiento").value == ""){
                error = true;
                mensaje += "Debe ingresar en funciones cognitivas la motivación del tratamiento. \n";
            }

          

            if(document.getElementById("tipoPsicologia").value == "Pediatría"){
                if(document.getElementById("edad_madre").value == ""){
                    error = true;
                    mensaje += "Debe ingresar la edad de la madre en el embarazo. \n";
                }

                if(document.getElementById("enfermedades_madre").value == ""){
                    error = true;
                    mensaje += "Debe ingresar las enfermedades de la madre en el embarazo. \n";
                }

                if(document.getElementById("numero_embarazo").value == ""){
                    error = true;
                    mensaje += "Debe ingresar el número de embarazo. \n";
                }

                if(document.getElementById("enbarazo_controlado").value == ""){
                    error = true;
                    mensaje += "Debe ingresar si el embarazo fue controlado. \n";
                }

                if(document.getElementById("planificacion").value == ""){
                    error = true;
                    mensaje += "Debe ingresar si hubo planificacion. \n";
                }

                if(document.getElementById("estado_madre").value == ""){
                    error = true;
                    mensaje += "Debe ingresar el estado de la madre en el embarazo. \n";
                }

                if(document.getElementById("tipo_nacimiento").value == ""){
                    error = true;
                    mensaje += "Debe ingresar el tipo de nacimiento. \n";
                }

                if(document.getElementById("causa_cesarea").value == ""){
                    error = true;
                    mensaje += "Debe ingresar la causa de la cesarea. \n";
                }

                if(document.getElementById("reanimacion").value == ""){
                    error = true;
                    mensaje += "Debe ingresar si se realizo reanimacion. \n";
                }

                if(document.getElementById("peso_nacer").value == ""){
                    error = true;
                    mensaje += "Debe ingresar el peso al nacer. \n";
                }
                
                if(document.getElementById("talla_nacer").value == ""){
                    error = true;
                    mensaje += "Debe ingresar la talla al nacer. \n";
                }

                if(document.getElementById("llanto_nacer").value == ""){
                    error = true;
                    mensaje += "Debe ingresar el llanto al nacer. \n";
                }   

            }

            if (error) {
                swal("¡Alerta!", mensaje, "warning");
            }

            return error;

        }

        function guardarConsulta() {
            if ($("#formConsulta").valid()) {
                for (var instanceName in CKEDITOR.instances) {
                    CKEDITOR.instances[instanceName].updateElement()
                }
                const formConsulta = document.getElementById('formConsulta')
                const formData = new FormData(formConsulta)
                formData.append('idHist', document.getElementById("idHistoria").value)

                const url = "{{ route('form.guardarConsultaPsicologica') }}"

                var error = validarFormularioConsultaEnvio();
                if (!error) {
                    fetch(url, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content')
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            console.log(data)
                            if (data.success = 'success') {

                                swal(data.title, data.message, data.success)
                                cargarConsultas(1)
                                document.getElementById("listadoConsultas").style.display = "initial"
                                document.getElementById("fomrConsultas").style.display = "none"

                            } else {
                                swal(data.title, data.message, data.success)
                            }
                        })
                        .catch(error => {
                            console.error("Error al enviar los datos:", error)
                        })
                }

            }
        }

        function validarFormularioConsultaEnvio() {
            var error = false;
            var mensaje = "";
            if (document.getElementById("codConsultaConsulta").value == "") {
                error = true;
                mensaje += "Debe seleccionar un código de evolución. \n";
            }
            if (document.getElementById("codImpresionDiagnosticoConsulta").value == "") {
                error = true;
                mensaje += "Debe seleccionar un código de impresión diagnóstica. \n";
            }

            if (document.getElementById("objetivo_sesion").value == "") {
                error = true;
                mensaje += "Debe ingresar el objetivo de la sesión. \n";
            }
            if (document.getElementById("tecnicas_utilizadas").value == "") {
                error = true;
                mensaje += "Debe ingresar las técnicas utilizadas. \n";
            }
            if (document.getElementById("actividades_especificas").value == "") {
                error = true;
                mensaje += "Debe ingresar las actividades específicas. \n";
            }
            if (document.getElementById("evaluacion_indicadores").value == "") {
                error = true;
                mensaje += "Debe ingresar la evaluación de indicadores. \n";
            }
            if (document.getElementById("evolucion_sesion").value == "") {
                error = true;
                mensaje += "Debe ingresar la evolución de la sesión. \n";
            }

            if (error) {
                swal("¡Alerta!", mensaje, "warning");
            }

            return error;
        }

        function verHistoria(element) {
            editarHistoria(element);
            // var btnGuardar = document.getElementById("btn-guardarHistoria")
            // btnGuardar.disabled = true
            document.getElementById("btn-imprimirHistoria").style.display = "initial"
        }

        function editarHistoria(element) {
            loadNow(1);
            limpiarHistoria()
            document.getElementById('listado').style.display = 'none'
            document.getElementById('historia').style.display = 'block'

            let idHist = element.getAttribute("data-id")
            let tipoHis = element.getAttribute("data-tipo")
            idHistoriaImprimir = idHist;

            var btnGuardar = document.getElementById("btn-guardarHistoria")
            btnGuardar.disabled = false

            let url = "{{ route('historia.buscaHistoriaPsicologica') }}"

            fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        idHist: idHist
                    })
                })
                .then(response => response.json())
                .then(data => {

                    mapearInfPaciente(data.paciente)
                    mapearAntedentesPersonales(data.antecedentesPersonales)
                    mapearHistoria(data.historia)
                    mapearAntedentesFamiliares(data.antecedentesFamiliares)
                    mapearAreaDesempeno(data.areaAjuste)
                    mapearInterconsulta(data.interconuslta)
                    mapearAparienciaPersonal(data.aparienciaPersonal)
                    mapearFuncionesCognitivas(data.funcionesCognitiva)
                    mapearFuncionesSomaticas(data.funcionesSomaticas)

                    mapearHistorialConsultas(data.historialConsultas)

                    if (data.historia.tipologia == "Pediatría") {
                        document.getElementById("infPediatria").style.display = "initial"
                        mapearAntecedentesPrenatales(data.antecedentesPrenatales)
                        mapearAntecedentesNatales(data.antecedentesNatales)
                        mapearAntecedentesPosnatales(data.antecedentesPosnatales)
                        mapearDesarrolloPsicomotor(data.desarrolloPsicomotor)
                    } else {
                        document.getElementById("infPediatria").style.display = "none"

                    }
                    loadNow(0);

                })
                .catch(error => console.error('Error:', error))
        }



        function mapearHistorialConsultas(historialConsultas) {
            document.getElementById("historialConsulta").innerHTML = historialConsultas
        }

        function editarConsulta(idConsulta) {
            if (hasPermission('editarEvoluciones')) {
                document.getElementById("listadoConsultas").style.display = "none"
                document.getElementById("fomrConsultas").style.display = "initial"

                document.getElementById("idHistoriaConsulta").value = idConsulta
                document.getElementById("accHistoriaConsulta").value = "editar"

                let url = "{{ route('historia.buscaConsultaPsicologica') }}"

                fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            idConsulta: idConsulta
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        cargarSelConsulta(data.consulta.codigo_consulta, 'codConsultaConsulta')

                        cargarImpresion(data.consulta.impresion_diagnostica, 'codImpresionDiagnosticoConsulta')

                        CKEDITOR.instances['motivoConsultaModal'].setData(data.consulta.motivo)
                        CKEDITOR.instances['objetivo_sesion'].setData(data.consulta.objetivo_sesion)
                        CKEDITOR.instances['tecnicas_utilizadas'].setData(data.consulta.tecnicas_utilizadas)
                        CKEDITOR.instances['actividades_especificas'].setData(data.consulta.actividades_especificas)

                        const [fecha, hora] = data.consulta.fecha_consulta.split(' ')
                        document.getElementById('fechaEvolucion').value = fecha
                        document.getElementById('horaSeleccionadad').value = hora.slice(0, 5)


                        CKEDITOR.instances['evaluacion_indicadores'].setData(data.consulta.evaluacion_indicadores)
                        CKEDITOR.instances['evolucion_sesion'].setData(data.consulta.evolucion_sesion)

                    })
                    .catch(error => console.error('Error:', error))
            } else {
                swal("¡Alerta!",
                    "No tiene el permiso necesario para realizar esta acción",
                    "warning")
            }
        }

        function mapearDesarrolloPsicomotor(antecedentesPrenatales) {
            antecedentesPrenatales.forEach(item => {
                const element = document.getElementById(item.tipo) // Buscar el elemento por su ID

                if (element) {
                    if (element.tagName === "INPUT" || element.tagName === "TEXTAREA") {
                        element.value = item.detalle // Asignar el valor al input o textarea
                    } else if (element.tagName === "SELECT") {
                        element.value = item.detalle.toLowerCase() // Asignar el valor a un select
                        $('#' + item.tipo).val(item.detalle).trigger('change')
                    } else {
                        console.warn(`El elemento con ID "${item.tipo}" no es compatible.`)
                    }
                } else {
                    console.error(`No se encontró un elemento con el ID "${item.tipo}".`)
                }
            })
        }

        function mapearAntecedentesPosnatales(antecedentesPrenatales) {
            antecedentesPrenatales.forEach(item => {
                const element = document.getElementById(item.tipo) // Buscar el elemento por su ID

                if (element) {
                    if (element.tagName === "INPUT" || element.tagName === "TEXTAREA") {
                        element.value = item.detalle // Asignar el valor al input o textarea
                    } else if (element.tagName === "SELECT") {
                        element.value = item.detalle.toLowerCase() // Asignar el valor a un select
                        $('#' + item.tipo).val(item.detalle).trigger('change')
                    } else {
                        console.warn(`El elemento con ID "${item.tipo}" no es compatible.`)
                    }
                } else {
                    console.error(`No se encontró un elemento con el ID "${item.tipo}".`)
                }
            })
        }

        function mapearAntecedentesPrenatales(antecedentesPrenatales) {
            antecedentesPrenatales.forEach(item => {
                const element = document.getElementById(item.tipo) // Buscar el elemento por su ID
                
                if (element) {
                    if (element.tagName === "INPUT" || element.tagName === "TEXTAREA") {
                        element.value = item.detalle // Asignar el valor al input o textarea
                    } else if (element.tagName === "SELECT") {
                        element.value = item.detalle.toLowerCase() // Asignar el valor a un select
                        $('#' + item.tipo).val(item.detalle).trigger('change')
                    } else {
                        console.warn(`El elemento con ID "${item.tipo}" no es compatible.`)
                    }
                } else {
                    console.error(`No se encontró un elemento con el ID "${item.tipo}".`)
                }
            })
        }

        function mapearAntecedentesNatales(antecedentesPrenatales) {
            antecedentesPrenatales.forEach(item => {
                const element = document.getElementById(item.tipo) // Buscar el elemento por su ID

                if (element) {
                    if (element.tagName === "INPUT" || element.tagName === "TEXTAREA") {
                        element.value = item.detalle // Asignar el valor al input o textarea
                    } else if (element.tagName === "SELECT") {
                        element.value = item.detalle.toLowerCase() // Asignar el valor a un select
                        $('#' + item.tipo).val(item.detalle).trigger('change')
                    } else {
                        console.warn(`El elemento con ID "${item.tipo}" no es compatible.`)
                    }
                } else {
                    console.error(`No se encontró un elemento con el ID "${item.tipo}".`)
                }
            })
        }

        function mapearFuncionesSomaticas(somaticas) {
            if (somaticas) {
                document.getElementById("ciclos_sueno").vlaue = somaticas.ciclos_del_sueno
                document.getElementById("apetito").vlaue = somaticas.apetito
                document.getElementById("autocuidado").vlaue = somaticas.actividades_autocuidado
            }
        }

        function mapearFuncionesCognitivas(cognitivas) {

            cognitivas.forEach(item => {
                const element = document.getElementById(item.caracteristica)
                if (element) {
                    if (element.tagName === "INPUT") {
                        element.value = item.detalle // Asignar el valor al input o textarea
                    } else if (element.tagName === "SELECT") {
                        const valores = item.detalle ? item.detalle.split(',') : []
                        $('#' + item.caracteristica).val(valores).trigger('change')
                    } else {
                        console.warn(`El elemento con ID "${item.tipo}" no es compatible.`)
                    }
                } else {
                    console.error(`No se encontró un elemento con el ID "${item.tipo}".`)
                }
            })
        }

        function mapearAparienciaPersonal(apariencia) {

            apariencia.forEach(item => {
                const element = document.getElementById(item.caracteristica)
                if (element) {
                    if (element.tagName === "INPUT") {
                        element.value = item.detalle // Asignar el valor al input o textarea
                    } else if (element.tagName === "SELECT") { //convertir en array
                        const valores = item.detalle ? item.detalle.split(',') : []

                        // Asignar el valor en Select2 correctamente
                        $('#' + item.caracteristica).val(valores).trigger('change')
                    } else {
                        console.warn(`El elemento con ID "${item.tipo}" no es compatible.`)
                    }
                } else {
                    console.error(`No se encontró un elemento con el ID "${item.tipo}".`)
                }
            })
        }

        function mapearInterconsulta(interconuslta) {

            interconuslta.forEach(item => {

                const element = document.getElementById(item.tipo).id
                if (element) {
                    CKEDITOR.instances[element].setData(item.detalle)
                } else {
                    console.error(`No se encontró un elemento con el ID "${item.tipo}".`)
                }
            })
        }

        function mapearAreaDesempeno(antecedentesAreaAjuste) {

            antecedentesAreaAjuste.forEach(item => {

                const element = document.getElementById(item.area)
                if (element) {
                    element.value = item.detalle
                } else {
                    console.error(`No se encontró un elemento con el ID "${item.tipo}".`)
                }
            })
        }

        function mapearAntedentesPersonales(antecedentesPersonales) {
            setTimeout(() => {
                antecedentesPersonales.forEach(item => {
                    const element = document.getElementById(item.tipo);

                    if (element) {
                        if (element.dataset.role === "tagsinput") {
                            // Usar el método de la biblioteca para actualizar el valor del tagsinput
                            $(element).tagsinput('add', item.detalle);
                        } else if (element.tagName === "INPUT" || element.tagName === "TEXTAREA") {
                            if (element.style.visibility == "hidden") {
                                CKEDITOR.instances[item.tipo].setData(item.detalle);
                            } else {
                                element.value = item.detalle;
                            }
                        } else if (element.tagName === "SELECT") {
                            element.value = item.detalle.toLowerCase();
                            $('#' + item.tipo).val(item.detalle).trigger('change')
                        } else {
                            console.warn(`El elemento con ID "${item.tipo}" no es compatible.`);
                        }
                    } else {
                        console.error(`No se encontró un elemento con el ID "${item.tipo}".`);
                    }
                });
            }, 1000);
        }


        function mapearAntedentesFamiliares(antecedentesFamiliares) {

            antecedentesFamiliares.forEach(item => {
                const element = document.getElementById(item.tipo)
                if (element) {
                    if (element.tagName === "INPUT" || element.tagName === "TEXTAREA") {
                        element.value = item.detalle
                    } else if (element.tagName === "SELECT") {
                        element.value = item.detalle.toLowerCase()
                        $('#' + item.tipo).val(item.detalle).trigger('change')
                    } else {
                        console.warn(`El elemento con ID "${item.tipo}" no es compatible.`)
                    }
                } else {
                    console.error(`No se encontró un elemento con el ID "${item.tipo}".`)
                }
            })
        }

        function mapearHistoria(historia) {
            
            document.getElementById("accHistoria").value = "editar"
            document.getElementById("estadoHistoria").value = historia.estado_hitoria
            document.getElementById("idHistoria").value = historia.id
            document.getElementById("idPaciente").value = historia.id_paciente

            if (historia.primera_vez == 1) {
                document.getElementById('primeraVez').checked = true
            } else {
                document.getElementById('primeraVez').checked = false
            }

            CKEDITOR.instances['remision'].setData(historia.remision)
            CKEDITOR.instances['motivoConsulta'].setData(historia.motivo_consulta)
            CKEDITOR.instances['resumen_evaluacion_inicial'].setData(historia.eval_inicial)

            cargarSelConsulta(historia.codigo_consulta, 'codConsulta')
            document.getElementById('otro_CodDiagnostico').value = historia.otro_dx_principal
            document.getElementById('otra_ImpresionDiagnostica').value = historia.otro_cod_diagnostico

            document.getElementById('plan_intervencion').value = historia.plan_intervencion
            $('#plan_intervencion').trigger('change')

            if (historia.otro_motivo_consulta != null) {
                const valoresConsulta = historia.otro_motivo_consulta.split(',')
                const motivoConsulta = document.getElementById('motivoConsultaOtro')

                // Restablecer las selecciones actuales (solo en este select)
                Array.from(motivoConsulta.options).forEach(option => {
                    option.selected = false
                })

                // Marcar las opciones como seleccionadas (solo las especificadas)
                valoresConsulta.forEach(value => {
                    const option = motivoConsulta.querySelector(`option[value="${value}"]`)
                    if (option) {
                        option.selected = true
                    }
                })
                // Actualizar Select2 para reflejar los cambios
                if ($(motivoConsulta).hasClass('select2')) {
                    $(motivoConsulta).val(valoresConsulta).trigger(
                        'change') // Cambiar el valor y disparar el evento 'change'
                }
               

            }

            
            if(historia.estado_hitoria == "cerrada"){
                    var btnGuardar = document.getElementById("btn-guardarHistoria")
                    btnGuardar.disabled = true
                }else{
                    var btnGuardar = document.getElementById("btn-guardarHistoria")
                    btnGuardar.disabled = false
                }

            cargarDxPrincipa(historia.dx_principal)

            document.getElementById('establecidoPrimeraVez').value = historia.diagnostico_primera_vez
            $('#establecidoPrimeraVez').trigger('change')

            cargarImpresion(historia.codigo_diagnostico, 'codDiagnostico')

            CKEDITOR.instances['enfermedadActual'].setData(historia.enfermedad_actual)
            CKEDITOR.instances['objetivo_general'].setData(historia.objetivo_general)
            CKEDITOR.instances['objetivos_especificos'].setData(historia.objetivos_especificos)
            CKEDITOR.instances['sugerencia_interconsultas'].setData(historia.sugerencias_interconsultas)
            CKEDITOR.instances['observaciones_recomendaciones'].setData(historia.observaciones_recomendaciones)
            document.getElementById("tipoPsicologia").value = historia.tipologia

            mapearDatosProfesional(historia.id_profesional)
        }

        function mapearDatosProfesional(idProf) {

            let url = "{{ route('historia.buscaProfesionalHistoria') }}"
            fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        idProf: idProf
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.profesional) {
                        document.getElementById("defaulProfesional").style.display = "initial"
                        document.getElementById("divProfesional").style.display = "none"
                        document.getElementById("idProfesional").value = data.profesional.usuario
                        document.getElementById("nombreProfesional").innerHTML = data.profesional.nombre
                        document.getElementById("registroProfesional").innerHTML =
                            `<strong>Tarjeta Profesional:</strong> ${data.profesional.registro}`

                        let firmaProfesional = document.getElementById('firmaProfesional')
                        let url = $('#Ruta').data("ruta")
                        firmaProfesional.src = url + "/images/firmasProfesionales/" + data.profesional.firma

                    } else {
                        document.getElementById("defaulProfesional").style.display = "none"
                        document.getElementById("divProfesional").style.display = "initial"

                    }

                })
                .catch(error => console.error('Error:', error))
        }

        function cargarSelConsulta(codigo_consulta, id) {
            let rtotal = $("#RutaTotal").data("ruta")

            if (codigo_consulta) {
                // Hacer una petición para buscar el texto correspondiente al ID
                fetch(`${rtotal}historia/buscaCUPS?id=${codigo_consulta}`, {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Error al obtener el valor para codConsulta')
                        }
                        return response.json()
                    })
                    .then(data => {
                        if (data && data.id && data.text) {
                            // Agregar opción al select si no está ya presente
                            const newOption = new Option(data.text, data.id, true, true)
                            $('#' + id).append(newOption).trigger('change')
                        }
                    })
                    .catch(error => {
                        console.error('Error al cargar codConsulta:', error)
                    })
            }
        }

        function cargarDxPrincipa(codigo_dx) {
            let rtotal = $("#RutaTotal").data("ruta")

            if (codigo_dx) {
                // Hacer una petición para buscar el texto correspondiente al ID
                fetch(`${rtotal}historia/buscaCIE?id=${codigo_dx}`, {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Error al obtener el valor para codConsulta')
                        }
                        return response.json()
                    })
                    .then(data => {
                        if (data && data.id && data.text) {
                            // Agregar opción al select si no está ya presente
                            const newOption = new Option(data.text, data.id, true, true)
                            $('#codDiagnostico').append(newOption).trigger('change')
                        }
                    })
                    .catch(error => {
                        console.error('Error al cargar codDiagnostico:', error)
                    })
            }
        }

        function cargarImpresion(codigo_dx, id) {

            let rtotal = $("#RutaTotal").data("ruta")

            if (codigo_dx) {
                // Hacer una petición para buscar el texto correspondiente al ID
                fetch(`${rtotal}historia/buscaCIE?id=${codigo_dx}`, {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Error al obtener el valor para codConsulta')
                        }
                        return response.json()
                    })
                    .then(data => {
                        if (data && data.id && data.text) {
                            // Agregar opción al select si no está ya presente
                            const newOption = new Option(data.text, data.id, true, true)
                            $('#' + id).append(newOption).trigger('change')
                        }
                    })
                    .catch(error => {
                        console.error('Error al cargar codDiagnostico:', error)
                    })
            }
        }

        function eliminarConsulta(idCons) {
            if (hasPermission('editarEvoluciones')) {
                swal({
                    title: "Esta seguro de eliminar esta consulta ?",
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
                        let url = "{{ route('historia.eliminarConsulta') }}";
                        fetch(url, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                        .getAttribute(
                                            'content')
                                },
                                body: JSON.stringify({
                                    idConsulta: idCons
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    swal("¡Buen trabajo!",
                                        data.message,
                                        "success");
                                    cargarConsultas(1);
                                } else {
                                    swal("¡Alerta!",
                                        "La operación fue realizada exitosamente",
                                        data.message,
                                        "success");
                                }
                            })

                    } else {
                        swal("Cancelado", "Tu registro esta salvo :)", "error");
                    }
                });
            } else {
                swal("¡Alerta!",
                    "No tiene el permiso necesario para realizar esta acción",
                    "warning")
            }
        }

        function imprimirHistoria(id) {
            let url = "{{ route('historia.imprimirHistoria') }}";

            let idHisto = id || idHistoriaImprimir;

            let params = new URLSearchParams({
                idHist: idHisto
            });

            url += '?' + params.toString();


            swal({
                title: 'Cargando...',
                text: 'Espere mientras se genera el PDF, gracias.',
                allowOutsideClick: false,
                showConfirmButton: false,
            });

            fetch(url, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.url) {
                        swal({
                            title: "Se genero el PDF correctamente.",
                            type: "success",
                            showConfirmButton: true,
                            confirmButtonText: "Visualizar",
                            allowOutsideClick: false,
                        }, function(isConfirm) {
                            if (isConfirm) {
                                window.open(data.url, '_blank');
                            }
                        })
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        function ComprarPaquete(element) {
            // obtener data-id de element 
            let id = element.getAttribute("data-id")
            let idPaciente = element.getAttribute("data-paciente")
            document.getElementById("idHistoria").value = id
            document.getElementById("idPacienteVentaPaquete").value = idPaciente

            var modal = new bootstrap.Modal(document.getElementById("modalPaquete"), {
                backdrop: 'static',
                keyboard: false
            });
            modal.show()

            cargarPaquetes(1)
            cargarSelectPaquetes()

        }

        function cargarSelectPaquetes() {
            return new Promise((resolve, reject) => {
                let select = document.getElementById("selPaquete");
                let url = "{{ route('paquetes.listaPaquetesSel') }}";

                // Limpia el select antes de agregar nuevas opciones
                select.innerHTML = "";

                // Crear la opción por defecto
                let defaultOption = document.createElement("option");
                defaultOption.value = ""; // Valor en blanco
                defaultOption.text = "Selecciona una opción"; // Texto que se mostrará
                defaultOption.selected = true; // Que aparezca seleccionada por defecto
                select.appendChild(defaultOption);

                // Fetch para obtener los datos y cargar las opciones
                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(paquete => {
                            let option = document.createElement("option");
                            option.value = paquete.id;
                            option.setAttribute('data-valor', paquete.precio_por_sesion);
                            option.setAttribute('data-descripcion', paquete.descripcion);
                            option.text = paquete.descripcion;
                            select.appendChild(option);
                        });
                        resolve(); // Resuelve la promesa cuando los datos han sido cargados
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        reject(error); // Rechaza la promesa si ocurre un error
                    });
            });
        }

        function calValorMonto() {

            let valor = document.getElementById("precioSesion").value
            let nsesiones = document.getElementById("numSesiones").value
            let total = valor * nsesiones
            document.getElementById("montoFinal").value = total
            let formatoMoneda = formatCurrency(total, 'es-CO', 'COP')
            document.getElementById("montoFinalVis").value = formatoMoneda

        }

        function seleccionarPaquete(element) {
            let precio = element.options[element.selectedIndex].getAttribute('data-valor')
            var formatoMoneda = formatCurrency(precio, 'es-CO', 'COP')
            document.getElementById("precioSesion").value = precio
            document.getElementById("precioSesionVis").value = formatoMoneda
            let descripcion = element.options[element.selectedIndex].getAttribute('data-descripcion')
            document.getElementById("descripcionVentaPaquete").value = descripcion
            calValorMonto()
        }


        function cambioFormato(id) {
            let numero = document.getElementById(id)
            //elimina ultimos 3 caracateres de id de numero 
            let idPrecio = numero.id.slice(0, -3)
            document.getElementById(idPrecio).value = numero.value
            let formatoMoneda = formatCurrency(numero.value, 'es-CO', 'COP')
            numero.value = formatoMoneda

        }

        function formatCurrency(number, locale, currencySymbol) {
            return new Intl.NumberFormat(locale, {
                style: 'currency',
                currency: currencySymbol,
                minimumFractionDigits: 2
            }).format(number)
        }

        function validartxtnum(e) {
            tecla = e.which || e.keyCode
            patron = /[0-9]+$/
            te = String.fromCharCode(tecla)
            return (patron.test(te) || tecla == 9 || tecla == 8 || tecla == 37 || tecla == 39 || tecla == 44)
        }

        function cancelarVentaConsulta() {
            let formVentaSesion = document.getElementById("formVentaConsulta")
            formVentaConsulta.reset()
            const modal = document.getElementById('modalVentaConsulta')
            const modalInstance = bootstrap.Modal.getInstance(modal)
            modalInstance.hide()
        }

        function cancelarVentaSesion() {
            let formVentaSesion = document.getElementById("formVentaSesion")
            formVentaSesion.reset()
            document.getElementById("formVentaSesion").style.display = "none"
            document.getElementById("listVentaSesion").style.display = "initial"
        }

        function cargarPaquetes(page, searchTerm = '') {
            let url = "{{ route('paquetes.listaPaquetesModal') }}" // Definir la URL
            // Eliminar los campos ocultos anteriores
            var oldPageInput = document.getElementById('pagePaquetes')
            var oldSearchTermInput = document.getElementById('busquedaPaq')
            if (oldPageInput) oldPageInput.remove()
            if (oldSearchTermInput) oldSearchTermInput.remove()

            var data = {
                page: page,
                search: searchTerm,
                idHist: document.getElementById("idHistoria").value,
                tipoHist: "PSICOLOGIA"
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
                    document.getElementById("trRegistrosPaquetes").innerHTML = responseData.paquetes
                    feather.replace()
                    // Colocar los enlaces de paginación
                    document.getElementById('pagination-links-paquetes').innerHTML = responseData.links
                    loadNow(0)
                })
                .catch(error => console.error('Error:', error))
        }

        function nuevoPaquete() {
            //recorrer en la tabla html y verificar si existem paqueter sin terminar
            let table = document.getElementById("tblPaquetes")
            let rows = table.getElementsByTagName("tr")

            for (let i = 0; i < rows.length; i++) {
                let cells = rows[i].getElementsByTagName("td")
                if (cells.length > 0) {
                    let estado = cells[5].innerHTML
                    if (estado == "PENDIENTE") {
                        swal("¡Alerta!", "No puede crear un nuevo paquete, mientras tenga uno Pendiente", "warning")
                        document.getElementById("listPaquetes").style.display = "initial"
                        document.getElementById("formPaquetes").style.display = "none"
                        return
                    }
                }
            }

            let formPaquete = document.getElementById("formPaquetes")
            formPaquete.reset()
            document.getElementById("accVentaPaquete").value = "guardar"
            document.getElementById("idVentaPaquete").value = ""
            document.getElementById('saveRegistroPaq').removeAttribute('disabled')
            document.getElementById("tituloPaquete").innerHTML = "Nuevo Paquete"
            formPaquete.style.display = "initial"
            document.getElementById("listPaquetes").style.display = "none"

        }


        function guardarPaquetes() {
            if ($("#formPaquetes").valid()) {

                const formPaquetes = document.getElementById('formPaquetes')
                const formData = new FormData(formPaquetes)
                formData.append('idHist', document.getElementById("idHistoria").value)

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
                            cargarPaquetes(1)
                            document.getElementById("listPaquetes").style.display = "initial"
                            document.getElementById("formPaquetes").style.display = "none"

                        } else {
                            swal(data.title, data.message, data.success)
                        }
                    })
                    .catch(error => {
                        console.error("Error al enviar los datos:", error)
                    })

            }
        }

        function editarPaquete(idPaquete) {

            document.getElementById("listPaquetes").style.display = "none"
            document.getElementById("formPaquetes").style.display = "initial"

            document.getElementById("idVentaPaquete").value = idPaquete
            document.getElementById("accVentaPaquete").value = "editar"

            let url = "{{ route('paquetes.buscaPaqueteVenta') }}"

            fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        idPaquete: idPaquete
                    })
                })
                .then(response => response.json())
                .then(data => {
                    document.getElementById("tituloPaquete").innerHTML = "Editar venta paquete"
                    document.getElementById("precioSesion").value = data.paquete.valor
                    document.getElementById("numSesiones").value = data.paquete.cantidad
                    document.getElementById("montoFinal").value = data.paquete.precio
                    document.getElementById("montoFinalVis").value = formatCurrency(data.paquete.precio, 'es-CO',
                        'COP')
                    let paraFecha = data.paquete.fecha.split(" ")
                    document.getElementById("fechaPaquete").value = paraFecha[0]
                    $('#selPaquete').val(data.paquete.id_paquete).trigger('change.select2')
                    document.getElementById("precioSesionVis").value = formatCurrency(data.paquete.valor,
                        'es-CO', 'COP')

                    document.getElementById("descripcionVentaPaquete").value = data.paquete.descripion_paquete
                        .descripcion
                    document.getElementById("saveRegistroPaq").removeAttribute('disabled')
                })
                .catch(error => console.error('Error:', error))
        }

        function editarVentaSesion(element) {
            let idVenta = element.getAttribute("data-id")
            let estado = element.getAttribute("data-estado")

            if (estado == "PAGADO") {
                swal("¡Alerta!", "No puede editar una sesión que ya ha sido pagada", "warning")
                return
            }

            document.getElementById("listVentaSesion").style.display = "none"
            document.getElementById("formVentaSesion").style.display = "initial"

            document.getElementById("idVentaSesion").value = idVenta
            document.getElementById("accHistoriaVentaSesion").value = "editar"

            let url = "{{ route('historia.buscaSesionVenta') }}"

            fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        idVenta: idVenta
                    })
                })
                .then(response => response.json())
                .then(data => {
                    document.getElementById("tituloVentaSesion").innerHTML = "Editar sesión"
                    document.getElementById("idVentaSesion").value = data.servicioSesion.id
                    document.getElementById("descripcionVentaSesion").value = data.servicioSesion.descripcion
                    let parFecha = data.servicioSesion.fecha.split(" ")
                    document.getElementById("fechaVentaSesion").value = parFecha[0]
                    document.getElementById("horaSeleccionadaVentaSesion").value = parFecha[1]
                    document.getElementById("valorServSesionVis").value = formatCurrency(data.servicioSesion.precio,
                        'es-CO',
                        'COP')
                    document.getElementById("valorServSesion").value = data.servicioSesion.precio

                    document.getElementById("saveRegistroVentaSesion").removeAttribute('disabled')
                })
                .catch(error => console.error('Error:', error))
        }

        function eliminarPaquete(idPaquete) {

            swal({
                title: "Esta seguro de eliminar esta venta de paquete ?",
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
                    let url = "{{ route('paquetes.eliminarPaquete') }}";
                    fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute(
                                        'content')
                            },
                            body: JSON.stringify({
                                idPaquete: idPaquete
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                swal("¡Buen trabajo!",
                                    data.message,
                                    "success");
                                cargarPaquetes(1);
                            } else {
                                swal("¡Alerta!",
                                    "La operación fue realizada exitosamente",
                                    data.message,
                                    "success");
                            }
                        })

                } else {
                    swal("Cancelado", "Tu registro esta salvo :)", "error");
                }
            });

        }

        function eliminarVentaSesion(element) {
            let idVenta = element.getAttribute("data-id")
            let estado = element.getAttribute("data-estado")

            if (estado == "PAGADO") {
                swal("¡Alerta!", "No puede eliminar una sesión que ya ha sido pagada", "warning")
                return
            }

            swal({
                title: "Esta seguro de eliminar esta sesión ?",
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
                    let url = "{{ route('historia.eliminarSesionVenta') }}";
                    fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute(
                                        'content')
                            },
                            body: JSON.stringify({
                                idVenta: idVenta
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                swal("¡Buen trabajo!",
                                    data.message,
                                    "success");
                                cargarSesionesVentas(1);
                            } else {
                                swal("¡Alerta!",
                                    "La operación fue realizada exitosamente",
                                    data.message,
                                    "success");
                            }
                        })

                } else {
                    swal("Cancelado", "Tu registro esta salvo :)", "error");
                }
            });

        }

        function PlanIntervencionHistoria(element) {
            let idHist = element.getAttribute("data-id")
            document.getElementById("idHistoriaPlan").value = idHist

            var modal = new bootstrap.Modal(document.getElementById("modalPlanIntervencion"), {
                backdrop: 'static',
                keyboard: false
            })

            modal.show()
            let url = "{{ route('historia.buscaPlanIntervencion') }}"
            fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        idHist: idHist
                    })
                })
                .then(response => response.json())
                .then(data => {
                    // document.getElementById("idPlanIntervencion").value = data.historia.id_plan_intervencion
                    CKEDITOR.instances['sugerenciasModal'].setData(data.planIntervencion.sugerencias_interconsultas)
                    CKEDITOR.instances['observacionesModal'].setData(data.planIntervencion
                        .observaciones_recomendaciones)
                    CKEDITOR.instances['objetivoGeneralModal'].setData(data.planIntervencion.objetivo_general)
                    CKEDITOR.instances['objetivoEspecificoModal'].setData(data.planIntervencion.objetivos_especificos)
                })
                .catch(error => console.error('Error:', error))

        }

        function cancelarPlan() {
            document.getElementById("formPlanIntervencion").reset()
            CKEDITOR.instances['sugerenciasModal'].setData("")
            const modal = document.getElementById('modalPlanIntervencion')
            const modalInstance = bootstrap.Modal.getInstance(modal)
            modalInstance.hide()

        }

        function guardarPlan() {
            if ($("#formPlanIntervencion").valid()) {
                for (var instanceName in CKEDITOR.instances) {
                    CKEDITOR.instances[instanceName].updateElement()
                }
                const formPlanIntervencion = document.getElementById('formPlanIntervencion')
                const formData = new FormData(formPlanIntervencion)

                const url = "{{ route('form.guardarPlanIntervencion') }}"

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

                            swal(data.title, data.message, data.success)
                            const modal = document.getElementById('modalPlanIntervencion')
                            const modalInstance = bootstrap.Modal.getInstance(modal)
                            modalInstance.hide()

                        } else {
                            swal(data.title, data.message, data.success)
                        }
                    })
                    .catch(error => {
                        console.error("Error al enviar los datos:", error)
                    })

            }
        }

        function guardarVentaConsulta() {
            if ($("#formVentaConsulta").valid()) {

                const formVentaConsulta = document.getElementById('formVentaConsulta')
                const formData = new FormData(formVentaConsulta)

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
                            const modal = document.getElementById('modalVentaConsulta')
                            const modalInstance = bootstrap.Modal.getInstance(modal)
                            modalInstance.hide()

                        } else {
                            swal(data.title, data.message, data.success)
                        }
                    })
                    .catch(error => {
                        console.error("Error al enviar los datos:", error)
                    })
            }
        }

        function guardarVentaSesion() {
            if ($("#formVentaSesion").valid()) {

                const formVentaSesion = document.getElementById('formVentaSesion')
                const formData = new FormData(formVentaSesion)

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
                            document.getElementById("formVentaSesion").style.display = "none"
                            document.getElementById("listVentaSesion").style.display = "initial"
                            cargarSesionesVentas()

                        } else {
                            swal(data.title, data.message, data.success)
                        }
                    })
                    .catch(error => {
                        console.error("Error al enviar los datos:", error)
                    })
            }
        }

        function ventaConsulta(element) {
            let idHist = element.getAttribute("data-id")
            let idpaciente = element.getAttribute("data-paciente")
            document.getElementById("idHistoriaVenta").value = idHist
            document.getElementById("idPacienteVenta").value = idpaciente
            var modal = new bootstrap.Modal(document.getElementById("modalVentaConsulta"), {
                backdrop: 'static',
                keyboard: false
            })
            modal.show()
            //cargar informacion si ya fue guardada la venta
            let url = "{{ route('historia.buscaVentaConsulta') }}"
            fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        idHist: idHist
                    })
                })
                .then(response => response.json())
                .then(data => {
                    console.log(data.servicioConsulta.estadoVentaConsulta)
                    if (data.servicioConsulta.estadoVentaConsulta == 'PENDIENTE') {
                        document.getElementById("ConsultaVenta").value = data.servicioConsulta.descripcion.nombre
                        document.getElementById("estadoServicio").value = data.servicioConsulta.estadoVentaConsulta
                        document.getElementById("valorServConsultaVis").value = ""
                        document.getElementById("valorServConsulta").value = "0"
                        document.getElementById("accHistoriaVenta").value = "guardar"
                        document.getElementById("saveRegistroVentaConsulta").removeAttribute('disabled')
                    } else {
                        document.getElementById("idConsultaVenta").value = data.servicioConsulta.id
                        document.getElementById("ConsultaVenta").value = data.servicioConsulta.descripcion
                        document.getElementById("estadoServicio").value = data.servicioConsulta.estadoVentaConsulta
                        document.getElementById("valorServConsultaVis").value = formatCurrency(data.servicioConsulta
                            .precio, 'es-CO', 'COP')
                        document.getElementById("valorServConsulta").value = data.servicioConsulta.precio
                        let parFecha = data.servicioConsulta.fecha.split(' ')
                        document.getElementById("fechaVentaConsulta").value = parFecha[0]
                        document.getElementById("horaSeleccionadaVentaConsulta").value = parFecha[1]
                        document.getElementById("accHistoriaVenta").value = "editar"

                        if (data.servicioConsulta.estadoVentaConsulta == 'PAGADO') {
                            document.getElementById("saveRegistroVentaConsulta").setAttribute('disabled', 'disabled')
                        }

                    }
                })
                .catch(error => console.error('Error:', error))

        }


        function ventaSesion(element) {
            let idHist = element.getAttribute("data-id")
            let idPaciente = element.getAttribute("data-paciente")
            document.getElementById("idHistoriaVentaSesion").value = idHist
            document.getElementById("idPacienteVentaSesion").value = idPaciente
            var modal = new bootstrap.Modal(document.getElementById("modalVentaSesion"), {
                backdrop: 'static',
                keyboard: false
            })
            modal.show()
            cargarSesionesVentas()
        }

        function cargarSesionesVentas() {
            let url = "{{ route('historia.buscaVentaSesion') }}"
            fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        idHist: document.getElementById("idHistoriaVentaSesion").value,
                        tipoHistoria: "PSICOLOGIA"
                    })
                })
                .then(response => response.json())
                .then(data => {
                    document.getElementById("trRegistrosVentaSesion").innerHTML = data.ventasSesiones
                    feather.replace()
                })
                .catch(error => console.error('Error:', error))
        }

        function nuevaVentaSesion() {
            document.getElementById("formVentaSesion").reset()
            document.getElementById("accHistoriaVentaSesion").value = "guardar"
            document.getElementById("saveRegistroVentaSesion").removeAttribute('disabled')
            document.getElementById("tituloVentaSesion").innerHTML = "Nueva Venta de Sesión"
            document.getElementById("listVentaSesion").style.display = "none"
            document.getElementById("formVentaSesion").style.display = "initial"

        }

        function seleccionarProfesional(element) {
            let selectedOption = element.options[element.selectedIndex];
            // Obtener el valor del data-id
            let idProfesional = selectedOption.getAttribute("data-id");

            document.getElementById("idProfesional").value = idProfesional

        }
    </script>

@endsection
