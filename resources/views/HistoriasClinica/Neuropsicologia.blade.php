@extends('Plantilla.Principal')
@section('title', 'Historia clínica psicológica')
@section('Contenido')
    <input type="hidden" id="Ruta" data-ruta="{{ asset('/app-assets/') }}" />
    <input type="hidden" id="RutaTotal" data-ruta="{{ asset('/') }}" />
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
                                        <h4 class="header-title mb-3">Historial de consultas</h4>

                                        <div class="text-start mt-3">
                                            <div class="activ_box_button " style="width: 100%;">
                                                <button class="btn btn-success" style="width: 100%;"><i
                                                        class="fa fa-edit"></i> Iniciar consulta</button>
                                            </div>
                                            <div class="mt-4">
                                                <div class="pb-20">
                                                    <div class="dropdown float-end">
                                                        <a href="#" class="dropdown-toggle no-caret"
                                                            data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="mdi mdi-dots-vertical"></i>
                                                        </a>
                                                        <div class="dropdown-menu dropdown-menu-end">
                                                            <a href="javascript:void(0);"
                                                                class="dropdown-item">Settings</a>
                                                            <!-- item-->
                                                            <a href="javascript:void(0);" class="dropdown-item">Action</a>
                                                        </div> <!-- item-->

                                                    </div>
                                                    <p class="fs-16">7 Julio 2024</p>
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <div class="d-flex align-items-center">
                                                            <div
                                                                class="bg-transparent h-50 w-50 border border-light product_icon text-center">
                                                                <p class="mb-0 fs-20 w-50 fw-600 l-h-40"><i
                                                                        class="fa fa-stethoscope" aria-hidden="true"></i>
                                                                </p>
                                                            </div>
                                                            <div class="d-flex flex-column font-weight-500 mx-10">
                                                                <a href="#"
                                                                    class="text-dark hover-primary mb-1  fs-15">CONSULTA DE
                                                                    PRIMERA VEZ POR PSICOLOGIA</a>
                                                                <span class="text-fade"><i
                                                                        class="fa fa-fw fa-circle fs-10 text-success"></i>
                                                                    OTROS TRASTORNOS DEL DESARROLLO PSICOLOGICO</span>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <div class="d-flex flex-column font-weight-500">
                                                                <span class="text-fade text-end"><i
                                                                        class="fa fa-user-md"></i> Maria Pumarejo</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-0">
                                                <div class="pb-20">
                                                    <div class="dropdown float-end">
                                                        <a href="#" class="dropdown-toggle no-caret"
                                                            data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="mdi mdi-dots-vertical"></i>
                                                        </a>
                                                        <div class="dropdown-menu dropdown-menu-end">
                                                            <a href="javascript:void(0);"
                                                                class="dropdown-item">Settings</a>
                                                            <!-- item-->
                                                            <a href="javascript:void(0);" class="dropdown-item">Action</a>
                                                        </div> <!-- item-->

                                                    </div>
                                                    <p class="fs-16">7 Julio 2024</p>
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <div class="d-flex align-items-center">
                                                            <div
                                                                class="bg-transparent h-50 w-50 border border-light product_icon text-center">
                                                                <p class="mb-0 fs-20 w-50 fw-600 l-h-40"><i
                                                                        class="fa fa-stethoscope" aria-hidden="true"></i>
                                                                </p>
                                                            </div>
                                                            <div class="d-flex flex-column font-weight-500 mx-10">
                                                                <a href="#"
                                                                    class="text-dark hover-primary mb-1  fs-15">CONSULTA DE
                                                                    PRIMERA VEZ POR PSICOLOGIA</a>
                                                                <span class="text-fade"><i
                                                                        class="fa fa-fw fa-circle fs-10 text-success"></i>
                                                                    OTROS TRASTORNOS DEL DESARROLLO PSICOLOGICO</span>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <div class="d-flex flex-column font-weight-500">
                                                                <span class="text-fade text-end"><i
                                                                        class="fa fa-user-md"></i> Maria Pumarejo</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-0">
                                                <div class="pb-20">
                                                    <p class="fs-16">7 Julio 2024</p>
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <div class="d-flex align-items-center">
                                                            <div
                                                                class="bg-transparent h-50 w-50 border border-light product_icon text-center">
                                                                <p class="mb-0 fs-20 w-50 fw-600 l-h-40"><i
                                                                        class="fa fa-stethoscope" aria-hidden="true"></i>
                                                                </p>
                                                            </div>
                                                            <div class="d-flex flex-column font-weight-500 mx-10">
                                                                <a href="#"
                                                                    class="text-dark hover-primary mb-1  fs-15">CONSULTA DE
                                                                    PRIMERA VEZ POR PSICOLOGIA</a>
                                                                <span class="text-fade"><i
                                                                        class="fa fa-fw fa-circle fs-10 text-success"></i>
                                                                    OTROS TRASTORNOS DEL DESARROLLO PSICOLOGICO</span>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <div class="d-flex flex-column font-weight-500">
                                                                <span class="text-fade text-end"><i
                                                                        class="fa fa-user-md"></i> Maria Pumarejo</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
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

                                    <div class="card">
                                        <div class="card-body">
                                            <div class="card-header">
                                                <h5 class="text-uppercase"><i class="fa fa-h-square me-1"></i>
                                                    Evaluación clínica psicológica</h5>
                                                <button type="button" class="btn btn-info btn-sm mb-2"><i
                                                        class="fa fa-print"></i> Imprimir historia</button>

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
                                                    <a href="#examen" data-bs-toggle="tab" class="nav-link rounded-0">
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
                                                    <a href="#plan" data-bs-toggle="tab" class="nav-link rounded-0">
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
                                                                        <input type="text" id="toxico"
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
                                                                    <option value="fracturas">Fracturas óseas</option>
                                                                    <option value="traumatismo_craneoencefalico">
                                                                        Traumatismo
                                                                        craneoencefálico</option>
                                                                    <option value="luxaciones_esguinces">Luxaciones y
                                                                        esguinces
                                                                    </option>
                                                                    <option value="quemaduras">Quemaduras</option>
                                                                    <option value="accidente_transito">Accidente de tráfico
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
                                                                    <option value="no_refiere">No refiere</option>
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
                                                                    <option value="no_refiere">No refiere</option>
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
                                                                    <option value="no_refiere">No refiere</option>
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
                                                                    <option value="no_refiere">No refiere</option>
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
                                                                    <option value="no_refiere">No refiere</option>
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
                                                                    <option value="no_refiere">No refiere</option>
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
                                                                    <option value="no_refiere">No refiere</option>
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
                                                                    <option value="no_refiere">No refiere</option>
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
                                                                <label for="ciclos_sueno" class="form-label">Ciclos del
                                                                    Sueño:</label>
                                                                <textarea class="form-control" id="ciclos_sueno" name="ciclos_sueno" rows="3"></textarea>
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

                                            <div class="card-body">
                                                <h5> {{ Session::get('nombreProfesional') }}</h5>
                                                <img width="200"
                                                    src="{{ asset('app-assets/images/firmasProfesionales/' . Session::get('firmaProfesional')) }}"
                                                    alt="">
                                                <p><strong>Tarjeta Profesional:</strong>
                                                    {{ Session::get('registroProfesional') }} </p>
                                            </div>
                                            <div class="box-footer text-end">
                                                <button onclick="cancelarHistoria()" type="button"
                                                    class="btn btn-primary-light me-1">
                                                    <i class="ti-share-alt "></i> Cancelar
                                                </button>
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

    <script>
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

            cargarCategorias();
            cargarHistorias(1)
            
            const ids = [
                'enfermedadActual',
                'remision',
                'medicacion',
                'objetivos_especificos',
                'objetivo_general',
                'observaciones_recomendaciones',
                'sugerencia_interconsultas',
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
        });

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
                    conciencia_enfermedad: 'FUNCIONES COGNITIVAS: Conciencia de enfermedad neuro',
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

            modal.show();
            cargarPacientes(1);
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
            let edadPaciente = parseInt(element.getAttribute("data-edad"), 10);
            let tipoPsicologia = edadPaciente < 18 ? "Pediatría" : "Adulto";
            let tipoText = document.getElementById("tipoPsicologia");
            tipoText.value = tipoPsicologia;

            document.getElementById('idPaciente').value = idPaciente;
            const modal = document.getElementById('modalHistoria');
            const modalInstance = bootstrap.Modal.getInstance(modal);
            modalInstance.hide();

            document.getElementById('listado').style.display = 'none';
            document.getElementById('historia').style.display = 'block';
            mostrarInformacionHistoria(idPaciente)
        }

        function mostrarInformacionHistoria(idPaciente) {
            let url = "{{ route('pacientes.buscaPacienteHistoria') }}";

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
                    mapearInfPaciente(data.paciente)

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
                        if (data.success = 'success') {
                            swal('Atención', data.message, data.success);
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        } else {
                            swal('Atención', data.message, data.success);
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
    </script>

@endsection