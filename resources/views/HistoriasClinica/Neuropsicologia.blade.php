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
                <h4 class="page-title">Gestionar historia clínica Neuropsicológica</h4>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#"><i class="mdi mdi-home-outline"></i></a></li>
                            <li class="breadcrumb-item" aria-current="page">Inicio</li>
                            <li class="breadcrumb-item active" aria-current="page">Gestionar historia clínica Neuropsicológica
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
                        <h5 class="card-title">Gestionar historia clínica Neuropsicológica</h5>
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
                                            <p class="header-title mb-2"><strong>Notas Rapidas :</strong></p>
                                            <div class="my-3"><a href="#" id="inline-comments" data-type="textarea"
                                                    data-pk="1" data-placeholder="Agregue notas rapidas del paciente..."
                                                    data-title="Agrege notas rapidas">Sin nota</a></div>
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
                                                    style="width: 100%;"><i class="fa fa-edit"></i> Gestionar evolución</button>
                                            </div>
                                            <div id="historialConsulta">
                                            </div>
                                        </div>
                                    </div> <!-- end card-body-->
                                </div> <!-- end card-->
                            </div>
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
                                                    <button id="btnCancelar" onclick="cancelarHistoria()" type="button"
                                                        class="btn btn-primary-light me-1">
                                                        <i class="ti-share-alt "></i> Cancelar
                                                    </button>
                                                    <button onclick="imprimirHistoria()" id="btnImprimir" type="button" class="btn btn-info-light me-1"><i class="fa fa-print"></i> Imprimir historia</button>

                                                </div>
                                            </div>
                                            <br>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="remision" class="form-label">Remisión :</label>
                                                        <textarea class="form-control" id="remision" name="remision" rows="3"
                                                            placeholder="Ingese de donde es remitido el paciente.."></textarea>
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
                                                        <select class="form-control select2" multiple="multiple"
                                                            id="motivoConsulta" name="motivoConsulta"
                                                            data-placeholder="Seleccione los motivos de consulta"
                                                            style="width: 100%;">

                                                        </select>
                                                        <input type="text" placeholder="Otro motivo de consulta"
                                                            id="otroMotivo" name="otroMotivo"
                                                            class="form-control mt-1" />

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
                                                    <a href="#antecedentes" style="height: 100%; display: flex;justify-content: center;align-items: center;" data-bs-toggle="tab"
                                                        class="nav-link rounded-0 active">
                                                        <i class="fa fa-history"></i> Antecedentes
                                                    </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a href="#ajustes" style="height: 100%; display: flex;justify-content: center;align-items: center;" data-bs-toggle="tab" class="nav-link rounded-0">
                                                        <i class="fa fa-cogs"></i> Áreas de Ajuste
                                                    </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a href="#examen" style="height: 100%; display: flex;justify-content: center;align-items: center;" data-bs-toggle="tab" class="nav-link rounded-0">
                                                        <i class="fa fa-user-md"></i> Examen Mental
                                                    </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a href="#impresion" style="padding-left: 0;padding-right: 0;
}"
                                                        data-bs-toggle="tab" class="nav-link rounded-0">
                                                        <i class="fa fa-stethoscope"></i> Impresión Diagnóstica
                                                    </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a href="#plan"  data-bs-toggle="tab" class="nav-link rounded-0">
                                                        <i class="fa fa-clipboard"></i> Plan e Intervención
                                                    </a>
                                                </li>
                                            </ul>
                                            <div class="tab-content">
                                                <!-- Antecedentes -->
                                                <div class="tab-pane active" id="antecedentes">
                                                    <h5 class="text-uppercase"><i class="fa fa-user me-1"></i>
                                                        Médicos Personales</h5>
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
                                                                            name="toxico" value=""
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
                                                                    <option value="fracturas óseas">Fracturas óseas</option>
                                                                    <option value="traumatismo craneoencefálico">
                                                                        Traumatismo
                                                                        craneoencefálico</option>
                                                                    <option value="luxaciones y esguinces">Luxaciones y
                                                                        esguinces
                                                                    </option>
                                                                    <option value="quemaduras">Quemaduras</option>
                                                                    <option value="accidente de transito">Accidente de transito
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
                                                                    class="form-label">patología:</label>
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
                                                        <div class="box-header pb-1" style="padding: 20px 1px 0px 0px;">
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
                                                                        <option value="si">Si</option>
                                                                        <option value="no">No</option>
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
                                                                        <option value="si">Si</option>
                                                                        <option value="no">No</option>
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
                                                        <div class="box-header pb-1" style="padding: 20px 1px 0px 0px;">
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
                                                        <div class="box-header pb-1" style="padding: 20px 1px 0px 0px;">
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
                                                                    <textarea class="form-control" id="hospitalizaciones_postnatales" name="hospitalizaciones_postnatales" rows="3"
                                                                        placeholder="Describa las causas de hospitalización"></textarea>
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
                                                        <div class="box-header pb-1" style="padding: 20px 1px 0px 0px;">
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
                                                    <h5 class="text-uppercase"><i class="fa fa-users me-1"></i>
                                                        Médicos Familiares</h5>
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label for="depresion"
                                                                    class="form-label">Depresión:</label>
                                                                <select class="form-control" id="depresion"
                                                                    name="depresion">
                                                                    <option value="">Selecciona una opción</option>
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
                                                                    <option value="">Selecciona una opción</option>
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
                                                                    <option value="">Selecciona una opción</option>
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
                                                                    <option value="">Selecciona una opción</option>
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
                                                                    <option value="">Selecciona una opción</option>
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
                                                                    class="form-label">Discapacidad Intelectual:</label>
                                                                <select class="form-control" id="discapacidad_intelectual"
                                                                    name="discapacidad_intelectual">
                                                                    <option value="">Selecciona una opción</option>
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
                                                                    <option value="">Selecciona una opción</option>
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
                                                                    <option value="">Selecciona una opción</option>
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
                                                    <h5 class="text-uppercase"><i class="fa fa-book me-1"></i> Áreas de
                                                        Ajuste
                                                        y/o Desempeño</h5>
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
                                                                <label for="historia_laboral" class="form-label"> Historia
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
                                                                <label for="historia_social" class="form-label"> Historia
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

                                                    <h5 class="text-uppercase mt-4"><i class="fa fa-stethoscope me-1"></i>
                                                        Interconsultas e Intervenciones</h5>
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

                                                    <h5 class="text-uppercase mt-4"><i class="fa fa-brain me-1"></i>
                                                        Examen
                                                        Mental</h5>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <h5 class="text-uppercase mt-4"><i
                                                                        class="fa fa-user me-1"></i> Apariencia personal
                                                                </h5>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <label for="edad" class="form-label">Edad:</label>
                                                            <select class="form-select" id="edad" name="edad"
                                                                onchange="toggleOtro(this)">
                                                                <option value="">Seleccione...</option>

                                                            </select>
                                                            <input type="text" class="form-control mt-2 d-none"
                                                                id="edad_otro" name="edad_otro"
                                                                placeholder="Especifique otro">
                                                        </div>

                                                        <!-- Desarrollo pondoestatural -->
                                                        <div class="col-md-6">
                                                            <label for="desarrollo" class="form-label">Desarrollo
                                                                pondoestatural:</label>
                                                            <select class="form-select" id="desarrollo" id="desarrollo"
                                                                onchange="toggleOtro(this)">
                                                                <option value="">Seleccione...</option>
                                                            </select>
                                                            <input type="text" class="form-control mt-2 d-none"
                                                                id="desarrollo_otro" name="desarrollo_otro"
                                                                placeholder="Especifique otro">
                                                        </div>

                                                        <!-- Aseo y arreglo -->
                                                        <div class="col-md-6">
                                                            <label for="aseo" class="form-label">Aseo y
                                                                arreglo:</label>
                                                            <select class="form-select" id="aseo" name="aseo"
                                                                onchange="toggleOtro(this)">
                                                                <option value="">Seleccione...</option>
                                                            </select>
                                                            <input type="text" class="form-control mt-2 d-none"
                                                                id="aseo_otro" name="aseo_otro"
                                                                placeholder="Especifique otro">
                                                        </div>

                                                        <!-- Salud somática -->
                                                        <div class="col-md-6">
                                                            <label for="salud" class="form-label">Salud
                                                                somática:</label>
                                                            <select class="form-select" id="salud" name="salud"
                                                                onchange="toggleOtro(this)">
                                                                <option value="">Seleccione...</option>
                                                            </select>
                                                            <input type="text" class="form-control mt-2 d-none"
                                                                id="salud_otro" name="salud_otro"
                                                                placeholder="Especifique otro">
                                                        </div>

                                                        <!-- Facies -->
                                                        <div class="col-md-6">
                                                            <label for="facies" class="form-label">Facies:</label>
                                                            <select class="form-select" id="facies" name="facies"
                                                                onchange="toggleOtro(this)">
                                                                <option value="">Seleccione...</option>
                                                            </select>
                                                            <input type="text" class="form-control mt-2 d-none"
                                                                id="facies_otro" name="facies_otro"
                                                                placeholder="Especifique otro">
                                                        </div>

                                                        <!-- Biotipo -->
                                                        <div class="col-md-6">
                                                            <label for="biotipo" class="form-label">Biotipo:</label>
                                                            <select class="form-select" id="biotipo" name="biotipo"
                                                                onchange="toggleOtro(this)">
                                                                <option value="">Seleccione...</option>
                                                            </select>
                                                            <input type="text" class="form-control mt-2 d-none"
                                                                id="biotipo_otro" name="biotipo_otro"
                                                                placeholder="Especifique otro">
                                                        </div>

                                                        <!-- Actitud -->
                                                        <div class="col-md-6">
                                                            <label for="actitud" class="form-label">Actitud:</label>
                                                            <select class="form-select" id="actitud" name="actitud"
                                                                onchange="toggleOtro(this)">
                                                                <option value="">Seleccione...</option>
                                                            </select>
                                                            <input type="text" class="form-control mt-2 d-none"
                                                                id="actitud_otro" name="actitud_otro"
                                                                placeholder="Especifique otro">
                                                        </div>
                                                    </div>

                                                    <h5 class="text-uppercase mt-4"><i class="fa fa-cogs me-1"></i>
                                                        Funciones
                                                        Cognitivas</h5>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <label for="consciencia"
                                                                class="form-label">Consciencia:</label>
                                                            <select class="form-select" id="consciencia"
                                                                name="consciencia" onchange="toggleOtro(this)">
                                                                <option value="">Seleccione...</option>
                                                            </select>
                                                            <input type="text" class="form-control mt-2 d-none"
                                                                id="consciencia_otro" name="consciencia_otro"
                                                                placeholder="Especifique otro">
                                                        </div>

                                                        <!-- Orientación -->
                                                        <div class="col-md-6">
                                                            <label for="orientacion"
                                                                class="form-label">Orientación:</label>
                                                            <select class="form-select" id="orientacion"
                                                                name="orientacion" onchange="toggleOtro(this)">
                                                                <option value="">Seleccione...</option>
                                                            </select>
                                                            <input type="text" class="form-control mt-2 d-none"
                                                                id="orientacion_otro" name="orientacion_otro"
                                                                placeholder="Especifique otro">
                                                        </div>

                                                        <!-- Memoria -->
                                                        <div class="col-md-6">
                                                            <label for="memoria" class="form-label">Memoria:</label>
                                                            <select class="form-select" id="memoria" name="memoria"
                                                                onchange="toggleOtro(this)">
                                                                <option value="">Seleccione...</option>
                                                            </select>
                                                            <input type="text" class="form-control mt-2 d-none"
                                                                id="memoria_otro" name="memoria_otro"
                                                                placeholder="Especifique otro">
                                                        </div>

                                                        <!-- Atención -->
                                                        <div class="col-md-6">
                                                            <label for="atencion" class="form-label">Atención:</label>
                                                            <select class="form-select" id="atencion" name="atencion"
                                                                onchange="toggleOtro(this)">
                                                                <option value="">Seleccione...</option>
                                                            </select>
                                                            <input type="text" class="form-control mt-2 d-none"
                                                                id="atencion_otro" name="atencion_otro"
                                                                placeholder="Especifique otro">
                                                        </div>

                                                        <!-- Concentración -->
                                                        <div class="col-md-6">
                                                            <label for="concentracion"
                                                                class="form-label">Concentración:</label>
                                                            <select class="form-select" id="concentracion"
                                                                name="concentracion" onchange="toggleOtro(this)">
                                                                <option value="">Seleccione...</option>
                                                            </select>
                                                            <input type="text" class="form-control mt-2 d-none"
                                                                id="concentracion_otro" name="concentracion_otro"
                                                                placeholder="Especifique otro">
                                                        </div>

                                                        <!-- Lenguaje -->
                                                        <div class="col-md-6">
                                                            <label for="lenguaje" class="form-label">Lenguaje:</label>
                                                            <select class="form-select" id="lenguaje" name="lenguaje"
                                                                onchange="toggleOtro(this)">
                                                                <option value="">Seleccione...</option>
                                                            </select>
                                                            <input type="text" class="form-control mt-2 d-none"
                                                                id="lenguaje_otro" name="lenguaje_otro"
                                                                placeholder="Especifique otro">
                                                        </div>

                                                        <!-- Pensamiento -->
                                                        <div class="col-md-6">
                                                            <label for="pensamiento"
                                                                class="form-label">Pensamiento:</label>
                                                            <select class="form-select" id="pensamiento"
                                                                name="pensamiento" onchange="toggleOtro(this)">
                                                                <option value="">Seleccione...</option>
                                                            </select>
                                                            <input type="text" class="form-control mt-2 d-none"
                                                                id="pensamiento_otro" name="pensamiento_otro"
                                                                placeholder="Especifique otro">
                                                        </div>

                                                        <!-- Afecto -->
                                                        <div class="col-md-6">
                                                            <label for="afecto" class="form-label">Afecto:</label>
                                                            <select class="form-select" id="afecto" name="afecto"
                                                                onchange="toggleOtro(this)">
                                                                <option value="">Seleccione...</option>
                                                            </select>
                                                            <input type="text" class="form-control mt-2 d-none"
                                                                id="afecto_otro" name="afecto_otro"
                                                                placeholder="Especifique otro">
                                                        </div>

                                                        <!-- Sensopercepción -->
                                                        <div class="col-md-6">
                                                            <label for="sensopercepcion"
                                                                class="form-label">Sensopercepción:</label>
                                                            <select class="form-select" id="sensopercepcion"
                                                                name="sensopercepcion" onchange="toggleOtro(this)">
                                                                <option value="">Seleccione...</option>
                                                            </select>
                                                            <input type="text" class="form-control mt-2 d-none"
                                                                id="sensopercepcion_otro" name="sensopercepcion_otro"
                                                                placeholder="Especifique otro">
                                                        </div>

                                                        <!-- Psicomotricidad -->
                                                        <div class="col-md-6">
                                                            <label for="psicomotricidad"
                                                                class="form-label">Psicomotricidad:</label>
                                                            <select class="form-select" id="psicomotricidad"
                                                                name="psicomotricidad" onchange="toggleOtro(this)">
                                                                <option value="">Seleccione...</option>
                                                            </select>
                                                            <input type="text" class="form-control mt-2 d-none"
                                                                id="psicomotricidad_otro" name="psicomotricidad_otro"
                                                                placeholder="Especifique otro">
                                                        </div>

                                                        <!-- Juicio -->
                                                        <div class="col-md-6">
                                                            <label for="juicio" class="form-label">Juicio:</label>
                                                            <select class="form-select" id="juicio" name="juicio"
                                                                onchange="toggleOtro(this)">
                                                                <option value="">Seleccione...</option>
                                                            </select>
                                                            <input type="text" class="form-control mt-2 d-none"
                                                                id="juicio_otro" name="juicio_otro"
                                                                placeholder="Especifique otro">
                                                        </div>

                                                        <!-- Inteligencia -->
                                                        <div class="col-md-6">
                                                            <label for="inteligencia"
                                                                class="form-label">Inteligencia:</label>
                                                            <select class="form-select" id="inteligencia"
                                                                name="inteligencia" onchange="toggleOtro(this)">
                                                                <option value="">Seleccione...</option>
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
                                                            <select class="form-select" id="conciencia_enfermedad"
                                                                name="conciencia_enfermedad" onchange="toggleOtro(this)">
                                                                <option value="">Seleccione...</option>
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
                                                            <select class="form-select" id="sufrimiento_psicologico"
                                                                name="sufrimiento_psicologico"
                                                                onchange="toggleOtro(this)">
                                                                <option value="">Seleccione...</option>
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
                                                            <select class="form-select" id="motivacion_tratamiento"
                                                                name="motivacion_tratamiento" onchange="toggleOtro(this)">
                                                                <option value="">Seleccione...</option>
                                                            </select>
                                                            <input type="text" class="form-control mt-2 d-none"
                                                                id="motivacion_tratamiento_otro"
                                                                name="motivacion_tratamiento_otro"
                                                                placeholder="Especifique otro">
                                                        </div>
                                                    </div>

                                                    <h5 class="text-uppercase mt-4"><i class="fa fa-heartbeat me-1"></i>
                                                        Funciones Somáticas</h5>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="ciclos_del_sueno" class="form-label">Ciclos del
                                                                    Sueño:</label>
                                                                <textarea class="form-control" id="ciclos_del_sueno" name="ciclos_sueno" rows="3"></textarea>
                                                            </div>
                                                        </div>

                                                        <!-- Apetito -->
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="apetito" class="form-label">Apetito:</label>
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
                                                    <h5 class="text-uppercase"><i class="fa fa-notes-medical me-1"></i>
                                                        Impresión Diagnóstica</h5>
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
                                                                <label for="establecidoPrimeraVez"
                                                                    class="form-label">Establecido por primera vez:</label>
                                                                <select class="form-control"
                                                                    id="establecidoPrimeraVez"
                                                                    name="establecidoPrimeraVez" aria-invalid="false">
                                                                    <option value="">Seleccione una opción</option>
                                                                    <option value="si">Si</option>
                                                                    <option value="no">No</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Plan de intervención -->
                                                <div class="tab-pane" id="plan">
                                                    <div class="tab-pane" id="diagnostico-plan">
                                                        <!-- 2. Plan de Intervención -->
                                                        <h5 class="text-uppercase mt-4"><i class="fa fa-tasks me-1"></i>
                                                            Plan
                                                            de Intervención</h5>

                                                            <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label for="plan_intervencion" class="form-label">Plan
                                                                        de intervención:</label>
                                                                    <select class="form-control select2"
                                                                        id="plan_intervencion" name="planIntervencion"
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
                                                                        class="form-label">Objetivos Específicos:</label>
                                                                    <textarea class="form-control" id="objetivos_especificos" name="objetivos_especificos" rows="3"
                                                                        placeholder="Ingrese los objetivos específicos..."></textarea>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- 3. Sugerencia para Interconsultas -->
                                                        <h5 class="text-uppercase mt-4"><i class="fa fa-user-md me-1"></i>
                                                            Sugerencia para Interconsultas</h5>
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
                                                        <h5 class="text-uppercase mt-4"><i
                                                                class="fa fa-comments me-1"></i>
                                                            Observaciones y Recomendaciones</h5>
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
                                            </div>
                                            <div class="card-body ">
                                                <input type="hidden" id="idProfesional" name="idProfesional" />
                                                <h5 id="nombreProfesional"></h5>
                                                <img id="firmaProfesional" width="200" src=""
                                                    alt="">

                                                <p id="registroProfesional"><strong>Tarjeta Profesional:</strong>
                                                </p>
                                            </div>
                                            <div class="box-footer text-end">
                                                <button onclick="guardarHistoria()" type="button"
                                                    class="btn btn-primary">
                                                    <i class="ti-save-alt"></i> Guardar
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
        </div>
    </section>

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
                                            <input type="search" id="busquedaPa" name="busquedaPa" class="form-control" placeholder="Buscar paciente">
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
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
                    <h4 class="modal-title" id="titConsulta">Gestionar evoluciones</h4>
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
                                    <div class="box-controls pull-right">
                                        <div class="box-header-actions">
                                            <div class="input-group input-group-merge">
                                                <input type="text" id="busquedaConsultaa" class="form-control">
                                                <div class="input-group-text" data-password="false">
                                                    <span class="fa fa-search"></span>
                                                </div>
                                                <button type="button" onclick="nuevoRegistroConsulta();"
                                                    class="btn btn-xs btn-primary font-bold"><i class="fa fa-plus"></i>
                                                    Nueva
                                                    evolución</button>
                                            </div>

                                        </div>
                                    </div>
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th style="width:40%;">Fecha</th>
                                                <th style="width:40%;">Profesional</th>
                                                <th style="width:20%;">Acción</th>
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
                                                <!-- 
                                                <li class="nav-item">
                                                    <a href="#datos_iniciales" data-bs-toggle="tab"
                                                        aria-expanded="false" class="nav-link rounded-0 active">
                                                        <span class="d-none d-md-block">
                                                            <i class="fa fa-user-circle"></i> 
                                                            Datos Iniciales
                                                        </span>
                                                    </a>
                                                </li>
                                                -->
                                                <li class="nav-item">
                                                    <a href="#resumenEval" data-bs-toggle="tab" aria-expanded="true"
                                                        class="nav-link rounded-0">
                                                        <span class="d-none d-md-block">
                                                            <i class="fa fa-check"></i>
                                                            Evolución y/o Plan de manejo
                                                        </span>
                                                    </a>
                                                </li>                                        
                                            </ul>

                                            <div class="tab-content px-20">
                                                <!-- Resumen de evaluación -->
                                                <div class="tab-pane show active" id="resumenEval">
                                                    <div class="col-md-4">
                                                        <label for="codConsultaConsulta"
                                                            class="form-label">Fecha:</label>
                                        
                                                        <div class="input-group">
                                                            <input type="date" class="form-control"
                                                                id="fechaEvolucion" name="fechaEvolucion"
                                                                placeholder="Seleccione la fecha de la evolución" />
                                                            <input type="time" id="horaSeleccionada" name="horaSeleccionada" 
                                                                class="form-control">
                                                            <div class="input-group-addon">
                                                                <i class="fa fa-clock-o"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <br>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="evolucion_plan"
                                                                class="form-label">Evolución y/o Plan de manejo:</label>
                                                            <textarea class="form-control" id="evolucion_plan" name="evolucion_plan" rows="3"
                                                                placeholder="Evolución del tratamiento psicolólogico actual"></textarea>
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
                                                    <i class="ti-save-alt"></i> Guardar
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

    <script>

        var idHistoriaImprimir = "";
        document.addEventListener("DOMContentLoaded", function() {

            let menuP = document.getElementById("principalHistoriClinica");
            let menuS = document.getElementById("principalHistoriClinicaNeuropsicología");

            menuP.classList.add("active", "menu-open");
            menuS.classList.add("active");
            let rtotal = $("#RutaTotal").data("ruta");

            //Initialize Select2 Elements
            $('.select2').select2();

            $('#codConsulta').select2({
                dropdownAutoWidth: true,
                width: '100%',
                placeholder: 'Buscar consulta por código o nombre...',
                language: {
                    inputTooShort: function() {
                        return 'Por favor, ingresa al menos un carácter';
                    },
                    noResults: function() {
                        return 'No se encontraron resultados.';
                    },
                    searching: function() {
                        return 'Buscando...';
                    }
                },
                minimumInputLength: 1, // Requiere al menos 1 carácter
                ajax: {
                    transport: function(params, success, failure) {
                        const query = params.data.q || ''; // Término de búsqueda
                        const page = params.data.page || 1; // Número de página

                        fetch(`${rtotal}historia/buscaCUPS?q=${query}&page=${page}`, {
                                method: 'GET',
                                headers: {
                                    'Content-Type': 'application/json',
                                },
                                cache: 'no-cache'
                            })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Error en la solicitud');
                                }
                                return response.json();
                            })
                            .then(data => {

                                const results = {
                                    results: data.data,
                                    pagination: {
                                        more: (page * 30) < data.total_count
                                    }
                                };
                                success(results); // Envía los resultados a Select2
                            })
                            .catch(error => {
                                console.error('Error al buscar:', error);
                                failure(error); // Maneja errores en Select2
                            });
                    }
                },
                escapeMarkup: function(markup) {
                    return markup; // Evita inyección de HTML
                }
            });

            $('#codDiagnostico').select2({
                dropdownAutoWidth: true,
                width: '100%',
                placeholder: 'Buscar diagnóstico  por código o nombre...',
                language: {
                    inputTooShort: function() {
                        return 'Por favor, ingresa al menos un carácter';
                    },
                    noResults: function() {
                        return 'No se encontraron resultados.';
                    },
                    searching: function() {
                        return 'Buscando...';
                    }
                },
                minimumInputLength: 1, // Requiere al menos 1 carácter
                ajax: {
                    transport: function(params, success, failure) {
                        const query = params.data.q || ''; // Término de búsqueda
                        const page = params.data.page || 1; // Número de página

                        fetch(`${rtotal}historia/buscaCIE?q=${query}&page=${page}`, {
                                method: 'GET',
                                headers: {
                                    'Content-Type': 'application/json',
                                },
                                cache: 'no-cache'
                            })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Error en la solicitud');
                                }
                                return response.json();
                            })
                            .then(data => {
                                const results = {
                                    results: data.data,
                                    pagination: {
                                        more: (page * 30) < data.total_count
                                    }
                                };
                                success(results); // Envía los resultados a Select2
                            })
                            .catch(error => {
                                console.error('Error al buscar:', error);
                                failure(error); // Maneja errores en Select2
                            });
                    }
                },
                escapeMarkup: function(markup) {
                    return markup; // Evita inyección de HTML
                }
            });

            $('#codImpresionDiagnostico').select2({
                dropdownAutoWidth: true,
                width: '100%',
                placeholder: 'Buscar diagnóstico  por código o nombre...',
                language: {
                    inputTooShort: function() {
                        return 'Por favor, ingresa al menos un carácter';
                    },
                    noResults: function() {
                        return 'No se encontraron resultados.';
                    },
                    searching: function() {
                        return 'Buscando...';
                    }
                },
                minimumInputLength: 1, // Requiere al menos 1 carácter
                ajax: {
                    transport: function(params, success, failure) {
                        const query = params.data.q || ''; // Término de búsqueda
                        const page = params.data.page || 1; // Número de página

                        fetch(`${rtotal}historia/buscaCIE?q=${query}&page=${page}`, {
                                method: 'GET',
                                headers: {
                                    'Content-Type': 'application/json',
                                },
                                cache: 'no-cache'
                            })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Error en la solicitud');
                                }
                                return response.json();
                            })
                            .then(data => {
                                const results = {
                                    results: data.data,
                                    pagination: {
                                        more: (page * 30) < data.total_count
                                    }
                                };
                                success(results); // Envía los resultados a Select2
                            })
                            .catch(error => {
                                console.error('Error al buscar:', error);
                                failure(error); // Maneja errores en Select2
                            });
                    }
                },
                escapeMarkup: function(markup) {
                    return markup; // Evita inyección de HTML
                }
            });
            
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


            const ids = [
                'enfermedadActual',
                'remision',
                'medicacion',
                'objetivos_especificos',
                'objetivo_general',
                'observaciones_recomendaciones',
                'sugerencia_interconsultas',
                'evolucion_plan',
            ];

            $(function() {
                "use strict";
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
                        resize_enabled: false // Deshabilitar redimensionamiento del editor
                    });
                });
            });

            menuP.classList.add("active");

            loader = document.getElementById('loader');
            loadNow(1);

            cargarCategorias();
            cargarHistorias(1);

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

            document.getElementById('busquedaConsultaa').addEventListener('input', function() {
                var searchTerm = this.value
                cargarConsultas(1,
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

            $('#inline-comments').editable({
                showbuttons: 'bottom',
                mode: 'inline'
            })

            var ultimaParteURLAnterior = document.referrer.split('/').filter(Boolean).pop()

            if (ultimaParteURLAnterior == "Gestionar") {
                let elemento = document.createElement('div')
                elemento.setAttribute('data-id', localStorage.getItem('idPaciente'))
                elemento.setAttribute('data-edad', localStorage.getItem('edadPaciente'))
                if (localStorage.getItem('idPaciente')) {
                    cagaHistPaciente(elemento)
                }

            }
        });

        function cagaHistPaciente(element) {
            let idPaciente = element.getAttribute("data-id")
            let edadPaciente = parseInt(element.getAttribute("data-edad"), 10)
            let tipoPsicologia = "";
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

        function cargarCategorias() {
            return new Promise((resolve, reject) => {

                let url = "{{ route('hitoriaPsicologica.categorias') }}";
                const categoriaMap = {
                    motivoConsulta: 'MOTIVO DE CONSULTA: neuro',
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
                };

                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        // Recorrer el mapa de categorías
                        Object.keys(categoriaMap).forEach(selectId => {
                            const categoriaNom = categoriaMap[selectId];

                            // Filtrar las opciones de la categoría correspondiente
                            const categoria = data.find(cat => cat.nombre === categoriaNom);
                            if (categoria) {
                                const select = document.getElementById(selectId);
                                if (select) {
                                    categoria.opciones.forEach(opcion => {
                                        const option = document.createElement('option');
                                        option.value = opcion.id;
                                        option.textContent = opcion.opcion;
                                        option.setAttribute('data-nombre', opcion.opcion
                                            .toLowerCase());
                                        select.appendChild(option);
                                    });
                                }
                            }
                        });
                    })
                    .catch(error => console.error('Error cargando las opciones:', error));
            });
        }
         
        function nuevoRegistro() {
            var modal = new bootstrap.Modal(document.getElementById("modalHistoria"), {
                backdrop: 'static',
                keyboard: false
            });

            limpiarHistoria();

            modal.show()
            let formHistoria = document.getElementById("formHistoria")
            formHistoria.reset()
            cargarPacientes(1)
            mapearDatosProfesional(document.getElementById("idUsuario").value)
        }

        function limpiarHistoria() {
            let formHistoria = document.getElementById("formHistoria")
            formHistoria.reset()

            for (var instanceName in CKEDITOR.instances) {
                CKEDITOR.instances[instanceName].setData('');
            }

            $('#motivoConsulta').val(null).trigger('change');
            $('#codConsulta').val(null).trigger('change');
            $('#codDiagnostico').val(null).trigger('change');
            $('#codImpresionDiagnostico').val(null).trigger('change');
            document.querySelectorAll('input[data-role="tagsinput"]').forEach(input => {
                $(input).tagsinput('removeAll');
            });
        }

        function cargarPacientes(page, searchTerm = '') {
            let url = "{{ route('pacientes.listaPacientesModal') }}"; // Definir la URL
            // Eliminar los campos ocultos anteriores
            var oldPageInput = document.getElementById('pagePac');
            var oldSearchTermInput = document.getElementById('busquedaPac');
            if (oldPageInput) oldPageInput.remove();
            if (oldSearchTermInput) oldSearchTermInput.remove();

            var data = {
                page: page,
                search: searchTerm
            };

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
                document.getElementById('trRegistrosPacientes').innerHTML = responseData.pacientes;
                feather.replace();
                // Colocar los enlaces de paginación
                document.getElementById('pagination-links-pacientes').innerHTML = responseData.links;
                loadNow(0);
            })
            .catch(error => console.error('Error:', error));

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

            document.getElementById('idPaciente').value = idPaciente;
            const modal = document.getElementById('modalHistoria');
            const modalInstance = bootstrap.Modal.getInstance(modal);
            modalInstance.hide();

            mostrarInformacionHistoria(idPaciente)
        }

        function mostrarInformacionHistoria(idPaciente) {
            let url = "{{ route('pacientes.buscaPacienteHistoriaNeuro') }}";

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
                                mapearInfPaciente(data.paciente)
                                document.getElementById('listado').style.display = 'none'
                                document.getElementById('historia').style.display = 'block'
                                verHistoria(data.historia.id)
                            }
                        });
                    } else {
                        mapearInfPaciente(data.paciente)
                        document.getElementById('listado').style.display = 'none'
                        document.getElementById('historia').style.display = 'block'
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        function mapearInfPaciente(paciente) {
            var foto = paciente.foto;
            const previewImage = document.getElementById('imgPaciente');
            let url = $('#Ruta').data("ruta");
            previewImage.src = url + "/images/FotosPacientes/" + foto;

            document.getElementById("nombrePaciente").innerHTML =
                `${paciente.primer_nombre} ${paciente.primer_apellido} `
            document.getElementById("edadPaciente").innerHTML = paciente.edad

            document.getElementById("identificacionPacienteHist").innerHTML =
                `${paciente.tipo_identificacion} - ${paciente.identificacion}`
            document.getElementById("nombreCompletoPacienteHist").innerHTML =
                `${paciente.primer_nombre} ${paciente.primer_apellido} ${paciente.segundo_nombre} ${paciente.segundo_apellido} `


            var fechForm = convertirFecha(paciente.fecha_nacimiento);
            document.getElementById("fechaNacimeintoPacienteHist").innerHTML =
                `${fechForm} (${paciente.edad})`
            document.getElementById("tipoUsuarioPacienteHist").innerHTML = tipoUsuario(paciente
                .tipo_usuario)

            let sexo =
                (paciente.sexo === "H") ? "Hombre" :
                (paciente.sexo === "M") ? "Mujer" :
                "Indeterminado o Intersexual";

            document.getElementById("sexoPacienteHist").innerHTML = sexo
            document.getElementById("emailPacienteHist").innerHTML = paciente.email
            document.getElementById("telefonoPacienteHist").innerHTML = paciente.telefono
            document.getElementById("direccionPacienteHist").innerHTML = paciente.direccion
            let zona = (paciente.zona_residencial === "01") ? "Rural" : "Urbano";
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
            const [año, mes, dia] = fecha.split('-');
            const fechaFormateada = `${dia.padStart(2, '0')}/${mes.padStart(2, '0')}/${año}`;
            return fechaFormateada;
        }

        function toggleOtro(selectElement) {
            const inputId = selectElement.id + "_otro";
            const inputField = document.getElementById(inputId);

            // Obtener el atributo data-nombre del elemento seleccionado
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            const dataNombre = selectedOption ? selectedOption.getAttribute('data-nombre') : null;

            if (dataNombre === "otro") {
                inputField.classList.remove("d-none");
                inputField.focus();
            } else {
                inputField.classList.add("d-none");
            }
        }

        function cancelarHistoria() {
            document.getElementById('listado').style.display = 'block';
            document.getElementById('historia').style.display = 'none';
        }

        function guardarHistoria() {
            if ($("#formHistoria").valid()) {

                for (var instanceName in CKEDITOR.instances) {
                    CKEDITOR.instances[instanceName].updateElement();
                }

                const formHistoria = document.getElementById('formHistoria');
                const formData = new FormData(formHistoria);

                const url = "{{ route('form.guardarHistoriaNeuroPsicologica') }}";

                fetch(url, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log(data);
                        if (data.success == 'success') {
                            document.getElementById("idHistoria").value = data.id;
                            cargarHistorias(1);
                            swal(data.title, data.message, data.success);
                        } else {
                            swal(data.tittle, data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error("Error al enviar los datos:", error);
                    });

            }
        }

        function cargarHistorias(page, searchTerm = '') {
            let url = "{{ route('HistoriasClinicas.listaHistoriasNeuroPsicologica') }}"; // Definir la URL

            var oldPageInput = document.getElementById('page');
            var oldSearchTermInput = document.getElementById('searchTerm');
            if (oldPageInput) oldPageInput.remove();
            if (oldSearchTermInput) oldSearchTermInput.remove();

            var data = {
                page: page,
                search: searchTerm
            };

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
                document.getElementById('hisoriasListado').innerHTML = responseData.historias;
                feather.replace();
                // Colxocar los enlaces de paginación
                document.getElementById('pagination-links').innerHTML = responseData.links;
                loadNow(0);
            })
            .catch(error => console.error('Error:', error));
        }

        function verHistoria(idHist) {

            idHistoriaImprimir = idHist;

            document.getElementById('listado').style.display = 'none'
            document.getElementById('historia').style.display = 'block'

            let url = "{{ route('historia.buscaHistoriaNeuroPsicologica') }}";

            let params = new URLSearchParams({
                idHist: idHist
            });

            url += '?' + params.toString();

            fetch(url, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
            })
            .then(response => response.json())
            .then(data => {
                mapearInfPaciente(data.paciente)
                mapearHisoria(data.historia)
                mapearAntedentesPersonales(data.antecedentesPersonales)
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
                
                deshabilitarInputsBotones();
            })
            .catch(error => console.error('Error:', error));
        }

        function deshabilitarInputsBotones(){
            var formulario = document.getElementById("formHistoria");
            var elementos = formulario.querySelectorAll("input, select, button, textarea");
            elementos.forEach(function(elemento) {
                elemento.disabled = true;
            });

            document.getElementById("cke_remision").style.pointerEvents = 'none';
            document.getElementById("cke_enfermedadActual").style.pointerEvents = 'none';
            document.getElementById("cke_medicacion").style.pointerEvents = 'none';
            document.getElementById("cke_objetivo_general").style.pointerEvents = 'none';
            document.getElementById("cke_objetivos_especificos").style.pointerEvents = 'none';
            document.getElementById("cke_sugerencia_interconsultas").style.pointerEvents = 'none';
            document.getElementById("cke_observaciones_recomendaciones").style.pointerEvents = 'none';            
            document.getElementById("btnImprimir").disabled = false;
            document.getElementById("btnCancelar").disabled = false;
        }

        function editarHistoria(element) {
            document.getElementById('listado').style.display = 'none'
            document.getElementById('historia').style.display = 'block'

            let idHist = element.getAttribute("data-id")
            let tipoHis = element.getAttribute("data-tipo")

            idHistoriaImprimir = idHist;

            let url = "{{ route('historia.buscaHistoriaNeuroPsicologica') }}";

            let params = new URLSearchParams({
                idHist: idHist
            });

            url += '?' + params.toString();

            fetch(url, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
            })
            .then(response => response.json())
            .then(data => {
                mapearInfPaciente(data.paciente)
                mapearHisoria(data.historia)
                mapearAntedentesPersonales(data.antecedentesPersonales)
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

            })
            .catch(error => console.error('Error:', error));
        }

        function mapearHisoria(historia) {
            document.getElementById("accHistoria").value = "editar"
            document.getElementById("idHistoria").value = historia.id
            document.getElementById("idPaciente").value = historia.id_paciente
            document.getElementById("estadoHistoria").value = historia.estado_hitoria
            
            CKEDITOR.instances['remision'].setData(historia.remision)
            cargarSelConsulta(historia.codigo_consulta, 'codConsulta')

            const valoresConsulta = historia.motivo_consulta.split(',')

            const motivoConsulta = document.getElementById('motivoConsulta')

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
                $(motivoConsulta).val(valoresConsulta).trigger('change') // Cambiar el valor y disparar el evento 'change'
            }

            cargarDxPrincipa(historia.dx_principal)

            document.getElementById('establecidoPrimeraVez').value = historia.diagnostico_primera_vez
            $('#establecidoPrimeraVez').trigger('change')

            document.getElementById("otroMotivo").value = historia.otro_motivo_consulta
            cargarImpresion(historia.codigo_diagnostico, 'codDiagnostico')

            CKEDITOR.instances['enfermedadActual'].setData(historia.enfermedad_actual)
            CKEDITOR.instances['objetivo_general'].setData(historia.objetivo_general)
            CKEDITOR.instances['objetivos_especificos'].setData(historia.objetivos_especificos)
            CKEDITOR.instances['sugerencia_interconsultas'].setData(historia.sugerencias_interconsultas)
            CKEDITOR.instances['observaciones_recomendaciones'].setData(historia.observaciones_recomendaciones)
            document.getElementById("tipoPsicologia").value = historia.tipologia

            mapearDatosProfesional(historia.id_profesional)
        }

        function mapearAntedentesPersonales(antecedentesPersonales) {
            antecedentesPersonales.forEach(item => {
                const element = document.getElementById(item.tipo) // Buscar el elemento por su ID

                if (element) {
                    if(item.tipo=="medicacion"){
                        CKEDITOR.instances['medicacion'].setData(item.detalle)
                    }else{
                        if (element.dataset.role === "tagsinput") {
                            $(element).tagsinput('add', item.detalle);
                        }
                        else if (element.tagName === "INPUT" || element.tagName === "TEXTAREA") {
                            element.value = item.detalle // Asignar el valor al input o textarea
                        } else if (element.tagName === "SELECT") {
                            element.value = item.detalle.toLowerCase() // Asignar el valor a un select
                        } else {
                            console.warn(`El elemento con ID "${item.tipo}" no es compatible.`)
                        }
                    }
                    
                } else {
                    console.error(`No se encontró un elemento con el ID "${item.tipo}".`)
                }
            })
        }

        function mapearAntedentesFamiliares(antecedentesFamiliares) {
            antecedentesFamiliares.forEach(item => {
                const element = document.getElementById(item.tipo) // Buscar el elemento por su ID
                if (element) {
                    if (element.tagName === "INPUT" || element.tagName === "TEXTAREA") {
                        element.value = item.detalle // Asignar el valor al input o textarea
                    } else if (element.tagName === "SELECT") {
                        element.value = item.detalle.toLowerCase() // Asignar el valor a un select
                    } else {
                        console.warn(`El elemento con ID "${item.tipo}" no es compatible.`)
                    }
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

        function mapearInterconsulta(interconuslta) {
            interconuslta.forEach(item => {

                const element = document.getElementById(item.tipo)
                if (element) {
                    element.value = item.detalle
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
                    } else if (element.tagName === "SELECT") {
                        element.value = item.detalle.toLowerCase() // Asignar el valor a un select
                        $('#' + item.caracteristica).trigger('change')
                    } else {
                        console.warn(`El elemento con ID "${item.tipo}" no es compatible.`)
                    }
                } else {
                    console.error(`No se encontró un elemento con el ID "${item.tipo}".`)
                }
            })
        }

        function mapearFuncionesCognitivas(cognitivas) {
            cognitivas.forEach(item => {
                const element = document.getElementById(item.caracteristica)
                if (element) {
                    if (element.tagName === "INPUT") {
                        element.value = item.detalle // Asignar el valor al input o textarea
                    } else if (element.tagName === "SELECT") {
                        element.value = item.detalle.toLowerCase() // Asignar el valor a un select
                        $('#' + item.caracteristica).trigger('change')
                    } else {
                        console.warn(`El elemento con ID "${item.caracteristica}" no es compatible.`)
                    }
                } else {
                    console.error(`No se encontró un elemento con el ID "${item.caracteristica}".`)
                }
            })
        }

        function mapearFuncionesSomaticas(somaticas) {
            if (somaticas) {
                document.getElementById("ciclos_del_sueno").value = somaticas.ciclos_del_sueno
                document.getElementById("apetito").value = somaticas.apetito
                document.getElementById("autocuidado").value = somaticas.actividades_autocuidado
            }
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
                document.getElementById("idProfesional").value = data.id
                document.getElementById("nombreProfesional").innerHTML = data.nombre
                document.getElementById("registroProfesional").innerHTML =
                    `<strong>Tarjeta Profesional:</strong> ${data.registro}`

                let firmaProfesional = document.getElementById('firmaProfesional')
                let url = $('#Ruta').data("ruta")
                firmaProfesional.src = url + "/images/firmasProfesionales/" + data.firma

            })
            .catch(error => console.error('Error:', error))
        }

        function mapearDesarrolloPsicomotor(antecedentesPrenatales) {
            antecedentesPrenatales.forEach(item => {
                const element = document.getElementById(item.tipo) // Buscar el elemento por su ID

                if (element) {
                    if (element.tagName === "INPUT" || element.tagName === "TEXTAREA") {
                        element.value = item.detalle // Asignar el valor al input o textarea
                    } else if (element.tagName === "SELECT") {
                        element.value = item.detalle.toLowerCase() // Asignar el valor a un select
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
                    } else {
                        console.warn(`El elemento con ID "${item.tipo}" no es compatible.`)
                    }
                } else {
                    console.error(`No se encontró un elemento con el ID "${item.tipo}".`)
                }
            })
        }

        function cerrarHistoria(element) {
            let idHist = element.getAttribute("data-id")
            let estado = element.getAttribute("data-estado")
            if(estado == "abierta"){
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
                        let url = "{{ route('historia.cerrarHistoriaNeuro') }}";
                        fetch(url, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        'content')
                                },
                                body: JSON.stringify({
                                    idHist: idHist
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
            }
            
        }

        function nuevoRegistroConsulta() {
            document.getElementById("listadoConsultas").style.display = "none"
            document.getElementById("fomrConsultas").style.display = "initial"
            document.getElementById("titConsulta").innerHTML = "Agregar consulta"
            document.getElementById("accHistoriaConsulta").value = "guardar"
            limpiarConsulta();
        }

        function guardarConsulta() {
            if ($("#formConsulta").valid()) {
                for (var instanceName in CKEDITOR.instances) {
                    CKEDITOR.instances[instanceName].updateElement()
                }
                const formConsulta = document.getElementById('formConsulta')
                const formData = new FormData(formConsulta)
                formData.append('idHist', document.getElementById("idHistoria").value)

                const url = "{{ route('form.guardarConsultaNeuroPsicologica') }}"

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


        function editarConsulta(idConsulta) {
            document.getElementById("listadoConsultas").style.display = "none"
            document.getElementById("fomrConsultas").style.display = "initial"

            document.getElementById("idHistoriaConsulta").value = idConsulta
            document.getElementById("accHistoriaConsulta").value = "editar"

            let url = "{{ route('historia.buscaConsultaNeuroPsicologica') }}"

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

                    CKEDITOR.instances['evolucion_plan'].setData(data.consulta.evolucion_y_o_plantrabajo)

                    document.getElementById("fechaEvolucion").value = data.consulta.fecha_consulta.split(' ')[0];
                    document.getElementById("horaSeleccionada").value = data.consulta.fecha_consulta.split(' ')[1];
                })
                .catch(error => console.error('Error:', error))
        }


        function cargarConsultas(page, searchTerm = '') {

            let url = "{{ route('historia.listaConsultasModalNeuro') }}" // Definir la URL
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
                }else{
                    swal("¡Atención!",
                    "Para gestionar las consultas la historia clinica debe estar cerrada.",
                    "warning");   
                }
            
            } else {
                swal("¡Atención!",
                    "El paciente no tiene una historia clínica abierta en el sistema.",
                    "warning");
            }

        }

        function mapearHistorialConsultas(historialConsultas) {
            document.getElementById("historialConsulta").innerHTML = historialConsultas
        }

        function cancelarConsulta() {
            document.getElementById('listadoConsultas').style.display = 'block'
            document.getElementById('fomrConsultas').style.display = 'none'
            document.getElementById("titConsulta").innerHTML = "Gestionar consulta"
        }

        function limpiarConsulta() {
            let formHistoria = document.getElementById("formConsulta")
            formHistoria.reset()
            CKEDITOR.instances['evolucion_plan'].setData('')
        }

        function imprimirHistoria(id) {
            let url = "{{ route('historia.imprimirHistoriaNeuro') }}";

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
                if(data.url){
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
</script>

@endsection