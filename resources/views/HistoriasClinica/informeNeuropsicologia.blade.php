@extends('Plantilla.Principal')
@section('title', 'Informes de neuropsicología')
@section('Contenido')
<input type="hidden" id="RutaTotal" data-ruta="{{ asset('/') }}" />

<div class="content-header">
    <div class="d-flex align-items-center">
        <div class="me-auto">
            <h4 class="page-title">Informe de neuropsicología</h4>
            <div class="d-inline-block align-items-center">
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#"><i class="mdi mdi-home-outline"></i></a></li>
                        <li class="breadcrumb-item" aria-current="page">Inicio</li>
                        <li class="breadcrumb-item active" aria-current="page">Informe de psicología</li>
                    </ol>
                </nav>
            </div>

        </div>

    </div>
</div>

<!-- Loading Spinner -->
<div id="loadingSpinner" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999;">
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">Cargando...</span>
        </div>
    </div>
</div>

<section class="content">
    <div class="row">
        <div id="listado" class="col-12 col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Listado de pacientes evolucionados</h5>
                </div>
                <div class="card-body">
                    <div class="box-controls pull-right">
                        <div class="box-header-actions">
                            <div class="input-group input-group-merge">
                                <input type="text" id="busqueda" class="form-control">
                                <div class="input-group-text" data-password="false">
                                    <span class="fa fa-search"></span>
                                </div>
                                <button type="button" onclick="nuevoRegistro(1);"
                                    class="btn btn-xs btn-primary font-bold"><i class="fa fa-plus"></i> Nuevo
                                    informe</button>
                            </div>

                        </div>
                    </div>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th style="width:40%;">Paciente</th>
                                <th style="width:20%;">Fecha de creación</th>
                                <th style="width:30%;">Profesional</th>
                                <th style="width:10%;">Acción</th>
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
<!-- MODAL MOTIVO DE CONSULTA -->
<div class="modal fade" id="modalEvoluciones" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 70%;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="tituloAccion">Historial de evoluciones</h4>
                <button type="button" class="btn-close" onclick="salirEvolucion();" aria-hidden="true"></button>
            </div>
            <div class="modal-body">
                <div id="listadoEvoluciones">

                </div>
                <div id="detalleEvoluciones" style="display: none;">

                </div>
            </div><!-- /.modal-content -->
            <div class="box-footer text-end">
                <button type="button" id="salieEv" onclick="salirEvolucion();" class="btn btn-primary-light me-1">
                    <i class="ti-close"></i> Salir
                </button>
                <button type="button" style="display: none;" id="atrasEv" onclick="astrasEvolucion();"
                    class="btn btn-primary-light me-1">
                    <i class="ti-arrow-left"></i> Atras
                </button>
            </div>
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
</div>

<!-- /.modal -->
<!-- MODAL MOTIVO DE CONSULTA -->
<div class="modal fade" id="modalInformeEvoluciones" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 70%;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="tituloInforme">Listado de informes</h4>
                <button type="button" class="btn-close" onclick="salirInformeEvolucion()" aria-hidden="true"></button>
            </div>
            <div class="modal-body">

                <div id="detalleInformeEvoluciones">
                    <form id="formInformeEvolucion">
                        <input type="hidden" id="accInforme" name="accInforme" value="guardar" />
                        <input type="hidden" id="idInforme" name="idInforme" />
                        <input type="hidden" id="idHistoria" name="idHistoria" />
                        <input type="hidden" id="idUsuario" value="{{ Auth::user()->id }}" />
                        <input type="hidden" id="idPaciente" name="idPaciente" value="" />
                        <div class="card">
                            <div class="card-body">
                                <ul class="nav nav-tabs" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-bs-toggle="tab" href="#informacion-general"
                                            role="tab">Información General</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#resultados-Evaluacion"
                                            role="tab">Resultados de Evaluación</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#impresion-Diagnostica"
                                            role="tab">Impresión Diagnóstica</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#orden-medica"
                                            role="tab">Orden medica</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#anexos"
                                            role="tab">Anexos</a>
                                    </li>
                                </ul>


                                <div class="tab-content mt-3">
                                    <!-- Información General -->
                                    <div class="tab-pane active" id="informacion-general" role="tabpanel">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label for="fechaEvolucion" class="form-label">Fecha y Hora:</label>
                                                <div class="input-group">
                                                    <input type="date" class="form-control" id="fechaEvolucion"
                                                        name="fechaEvolucion" placeholder="Seleccione la fecha" />
                                                    <input type="time" id="horaSeleccionada"
                                                        name="horaSeleccionada" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <label for="account-username">Profesional:</label>
                                                <select class="form-control select2" style="width: 100%;"
                                                    id="profesionalInforme" name="profesionalInforme">
                                                </select>
                                            </div>

                                            <div class="col-md-12 mt-3">
                                                <label class="form-label" for="motivoConsulta">INFORMACIÓN GENERAL:</label>
                                                <textarea class="form-control" id="motivoConsulta" name="motivoConsulta" rows="3"
                                                    placeholder="Ingrese el motivo de consulta.."></textarea>
                                            </div>
                                            <div class="col-md-12 mt-3" style="display: none;">
                                                <label class="form-label" for="estadoActual" >ESTADO ACTUAL:</label>
                                                <textarea class="form-control" id="estadoActual" name="estadoActual" rows="3"
                                                    placeholder="Ingrese el estado actual del paciente.."></textarea>
                                            </div>
                                            <div class="col-md-12 mt-3" style="display: none;">
                                                <label class="form-label" for="historiaPersonal">HISTORIA PERSONAL:</label>
                                                <textarea class="form-control" id="historiaPersonal" name="historiaPersonal" rows="3"
                                                    placeholder="Ingrese la historia personal del paciente.."></textarea>
                                            </div>
                                            <div class="col-md-12 mt-3" style="display: none;">
                                                <label class="form-label" for="desarrolloPsicomotor">DESARROLLO PSICOMOTOR:</label>
                                                <textarea class="form-control" id="desarrolloPsicomotor" name="desarrolloPsicomotor" rows="3"
                                                    placeholder="Ingrese el desarrollo psicomotor del paciente.."></textarea>
                                            </div>
                                            <div class="col-md-12 mt-3" style="display: none;">
                                                <label class="form-label" for="desarrolloLenguaje">DESARROLLO DE LENGUAJE:</label>
                                                <textarea class="form-control" id="desarrolloLenguaje" name="desarrolloLenguaje" rows="3"
                                                    placeholder="Ingrese el desarrollo de lenguaje del paciente.."></textarea>
                                            </div>
                                            <div class="col-md-12 mt-3" style="display: none;">
                                                <label class="form-label" for="abc">ABC:</label>
                                                <textarea class="form-control" id="abc" name="abc" rows="3"
                                                    placeholder="Ingrese el abc del paciente.."></textarea>
                                            </div>
                                            <div class="col-md-12 mt-3" style="display: none;">
                                                <label class="form-label" for="antecedentesMedicosFamiliares">ANTECEDENTES MEDICOS FAMILIARES:</label>
                                                <textarea class="form-control" id="antecedentesMedicosFamiliares" name="antecedentesMedicosFamiliares" rows="3"
                                                    placeholder="Ingrese los antecedentes medicos familiares del paciente.."></textarea>
                                            </div>
                                            <div class="col-md-12 mt-3" style="display: none;">
                                                <label class="form-label" for="antecedentesPersonales">ANTECEDENTES PERSONALES:</label>
                                                <textarea class="form-control" id="antecedentesPersonales" name="antecedentesPersonales" rows="3"
                                                    placeholder="Ingrese los antecedentes personales del paciente.."></textarea>
                                            </div>
                                            <div class="col-md-12 mt-3" style="display: none;">
                                                <label class="form-label" for="historiaDesarrollo">HISTORIA DEL DESARROLLO:</label>
                                                <textarea class="form-control" id="historiaDesarrollo" name="historiaDesarrollo" rows="3"
                                                    placeholder="Ingrese la historia del desarrollo del paciente.."></textarea>
                                            </div>
                                            <div class="col-md-12 mt-3" style="display: none;">
                                                <label class="form-label" for="historiaEscolar">HISTORIA ESCOLAR:</label>
                                                <textarea class="form-control" id="historiaEscolar" name="historiaEscolar" rows="3"
                                                    placeholder="Ingrese la historia escolar del paciente.."></textarea>
                                            </div>
                                            <div class="col-md-12 mt-3" style="display: none;">
                                                <label class="form-label" for="historiaSocioAfectiva">HISTORIA SOCIOAFECTIVA:</label>
                                                <textarea class="form-control" id="historiaSocioAfectiva" name="historiaSocioAfectiva" rows="3"
                                                    placeholder="Ingrese la historia socioafectiva del paciente.."></textarea>
                                            </div>

                                            <div class="col-md-12 mt-3" style="display: none;">
                                                <label class="form-label" for="condicionPaciente">CONDICIÓN DEL PACIENTE EN LA CONSULTA:</label>
                                                <textarea class="form-control" id="condicionPaciente" name="condicionPaciente" rows="3"
                                                    placeholder="Ingrese la condición del paciente en la consulta.."></textarea>
                                            </div>
                                        </div>

                                    </div>

                                    <!-- Resultados de Evaluación -->
                                    <div class="tab-pane" id="resultados-Evaluacion">
                                        <div class="row">
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label" for="resultadosEvaluacion">RESULTADOS DE EVALUACIÓN:</label>
                                                <textarea class="form-control" id="resultadosEvaluacion" name="resultadosEvaluacion" rows="3"
                                                    placeholder="Ingrese los resultados de la evaluación.."></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane" id="impresion-Diagnostica">
                                        <div class="row">
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label" for="impresionDiagnostica">IMPRESIÓN DIAGNOSTICA:</label>
                                                <textarea class="form-control" id="impresionDiagnostica" name="impresionDiagnostica" rows="3"
                                                    placeholder="Ingrese la impresión diagnóstica.."></textarea>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label" for="impresionDiagnostica">IMPRESIÓN DIAGNOSTICA PRINCIPAL:</label>
                                                <div class="d-flex gap-2">
                                                    <select class="form-control select2"
                                                        id="codImpresionDiagnostico"
                                                        name="codImpresionDiagnostico" aria-invalid="false">
                                                    </select>
                                                    <button type="button" class="btn btn-secondary" onclick="clearSelect('codImpresionDiagnostico')">
                                                        <i class="fa fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label" for="impresionDiagnostica">IMPRESIÓN DIAGNOSTICA RELACIONADA 1:</label>
                                                <div class="d-flex gap-2">
                                                    <select class="form-control select2"
                                                        id="codImpresionDiagnosticoRelacionado1"
                                                        name="codImpresionDiagnosticoRelacionado1" aria-invalid="false">
                                                    </select>
                                                    <button type="button" class="btn btn-secondary" onclick="clearSelect('codImpresionDiagnosticoRelacionado1')">
                                                        <i class="fa fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="impresion_diagnostica"
                                                    class="form-label">IMPRESIÓN DIAGNOSTICA RELACIONADA 2:</label>
                                                <div class="d-flex gap-2">
                                                    <select class="form-control select2"
                                                        id="codImpresionDiagnosticoRelacionado2"
                                                        name="codImpresionDiagnosticoRelacionado2" aria-invalid="false">
                                                    </select>
                                                    <button type="button" class="btn btn-secondary" onclick="clearSelect('codImpresionDiagnosticoRelacionado2')">
                                                        <i class="fa fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane" id="orden-medica">
                                        <div class="row">
                                            <div class="col-md-10 mt-3">
                                                <label class="form-label" for="codigoOrdenMedica">Código de la orden medica:</label>
                                                <div class="d-flex gap-2">
                                                    <select class="form-control select2"
                                                        id="codConsultaConsulta"
                                                        name="codConsultaConsulta" aria-invalid="false">
                                                    </select>
                                                    <button type="button" class="btn btn-secondary" onclick="clearSelect('codConsultaConsulta')">
                                                        <i class="fa fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col-md-2 mt-3">
                                                <label class="form-label" for="cantidad">Cantidad:</label>
                                                <input type="number" class="form-control" min="1" id="cantidad" name="cantidad" placeholder="Cantidad">
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label" for="observacion">Observación:</label>
                                                <textarea class="form-control" id="observacion" name="observacion" rows="3"
                                                    placeholder="Ingrese la observación.."></textarea>
                                            </div>
                                            <div class="col-md-12 mt-3 text-end">
                                                <button type="button" class="btn btn-primary" onclick="agregarOrdenMedica()">
                                                    <i class="fa fa-plus"></i> Agregar
                                                </button>
                                            </div>

                                            <div class="col-md-12 mt-3" id="listadoOrdenesMedicas">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h5 class="card-title">Ordenes Medicas</h5>
                                                        <table class="table table-striped table-hover">
                                                            <thead>
                                                                <tr>
                                                                    <th>No.</th>
                                                                    <th>Procedimiento</th>
                                                                    <th>Cantidad</th>
                                                                    <th>Acciones</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="tbodyOrdenesMedicas">

                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

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
                        </div>
                    </form>
                </div>
            </div><!-- /.modal-content -->


            <div class="box-footer text-end">
                <button type="button" id="salirInfo" onclick="salirInformeEvolucion();"
                    class="btn btn-primary-light me-1">
                    <i class="ti-close"></i> Salir
                </button>
                <button type="button" id="guardarInf" onclick="guardarInformeEvolucion();"
                    class="btn btn-success me-1">
                    <i class="ti-save"></i> guardar
                </button>
            </div>
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
</div>

<!-- MODAL PACIENTES -->
<div class="modal fade" id="modalPacientes" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
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

<!-- SELECCIONAR ENCIAR CONSULTA O IMPRIMIR -->
<div class="modal fade" id="modalEnviarImprimir" tabindex="-1" role="dialog"
    aria-labelledby="myLargeModalLabel" aria-hidden="true">`
    <div class="modal-dialog modal-dialog-centered" style="max-width: 25%;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Enviar Informe o Imprimir</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body">
                <div class="card">

                    <div class="card-body">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <button onclick="enviarInforme()" class="btn btn-success"> <i
                                            class="ti-email"></i> Enviar Informe</button>
                                </div>
                                <div class="col-md-6">
                                    <button onclick="generarPDF()" class="btn btn-primary"> <i
                                            class="ti-printer"></i> Imprimir Informe</button>
                                </div>
                            </div>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
        </div><!-- /.modal -->
    </div>
</div>
<!-- Agregar este HTML justo después del body -->
<div id="loader-pdf"
    style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100vh; background: rgba(0,0,0,0.7); z-index: 999999;">
    <div
        style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; background: white; padding: 20px; border-radius: 10px;">
        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">Cargando...</span>
        </div>
        <h4 class="mt-2" style="color: #333;" id="titulo_loader_pdf">Generando PDF</h4>
        <p style="margin: 0;">Por favor espere...</p>
    </div>
</div>

<!-- /.modal -->

<script>
    document.addEventListener("DOMContentLoaded", function() {
        let menuP = document.getElementById("principalInformes")
        let menuS = document.getElementById("informeNeuropsicolologico")
        menuP.classList.add("active", "menu-open")
        menuS.classList.add("active")
        let rtotal = $("#RutaTotal").data("ruta")

        let modalControl;
        loader = document.getElementById('loader')

        //Initialize Select2 Elements
        //$('.select2').select2()



        loadNow(1)
        cargar(1)
        cargarProfesionales()
        cargarPacientes(1)
        const ids = [
            'motivoConsulta',
            'impresionDiagnostica',
            'estadoActual',
            'historiaPersonal',
            'desarrolloPsicomotor',
            'desarrolloLenguaje',
            'abc',
            'antecedentesMedicosFamiliares',
            'antecedentesPersonales',
            'historiaDesarrollo',
            'historiaEscolar',
            'historiaSocioAfectiva',
            'condicionPaciente',
            'resultadosEvaluacion',
        ]


        $(function() {
            "use strict";

            ids.forEach(id => {
                CKEDITOR.replace(id, {
                    extraPlugins: 'uploadimage,pastefromword,maximize,justify,font,table',
                    toolbar: [{
                            name: 'basicstyles',
                            items: ['Bold', 'Italic', 'Underline']
                        },
                        {
                            name: 'paragraph',
                            items: [
                                'NumberedList',
                                'BulletedList',
                                'JustifyLeft',
                                'JustifyCenter',
                                'JustifyRight',
                                'JustifyBlock'
                            ]
                        },
                        {
                            name: 'styles',
                            items: ['Font', 'FontSize']
                        },
                        {
                            name: 'undo',
                            items: ['Undo', 'Redo']
                        },
                        {
                            name: 'insert',
                            items: ['Table', 'Image'] // 👈 Botón de tabla agregado
                        },
                        {
                            name: 'maximize',
                            items: ['Maximize']
                        }
                    ],
                    language: 'es',
                    height: 350,
                    resize_enabled: false,
                    allowedContent: true,
                    extraAllowedContent: 'table tr th td thead tbody caption; div(*); span(*); *[style,align,width,height]',
                    resize_enabled: false, // Deshabilitar redimensionamiento del editor
                    font_defaultLabel: 'Times New Roman',
                    font_names: 'Times New Roman/Times New Roman, Times, serif;' + (CKEDITOR.config.font_names || ''),
                    on: {
                        instanceReady: function(evt) {
                            evt.editor.document.getBody().setStyle('font-family', '"Times New Roman", Times, serif');
                        }
                    }
                });

                // 🔥 CSS adicional para que las tablas queden lado a lado
                CKEDITOR.addCss(`
            table {
                display: inline-block !important;
                margin-right: 10px;
                vertical-align: top;
            }
        `);
            });
        });

        if(localStorage.getItem('tipoInforme') == 'abrirInformeneuropsicologia'){
            idPaciente = localStorage.getItem('idPaciente');
            edadPaciente = localStorage.getItem('edadPaciente');
            localStorage.removeItem('tipoInforme');
            localStorage.removeItem('idPaciente');
            localStorage.removeItem('edadPaciente');
            

            seleccionarPacienteNeuropsicologia(idPaciente, edadPaciente);
        }


        $('#codImpresionDiagnostico').select2({
            dropdownAutoWidth: true,
            width: '100%',
            dropdownParent: $('#modalInformeEvoluciones'),
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


        $('#codImpresionDiagnosticoRelacionado1').select2({
            dropdownAutoWidth: true,
            width: '100%',
            dropdownParent: $('#modalInformeEvoluciones'),
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

        $('#codImpresionDiagnosticoRelacionado2').select2({
            dropdownAutoWidth: true,
            dropdownParent: $('#modalInformeEvoluciones'),
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

        $('#codConsultaConsulta').select2({
            dropdownAutoWidth: true,
            width: '100%',
            dropdownParent: $('#modalInformeEvoluciones'),
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


        document.getElementById('fechaEvolucion').addEventListener('input', function() {
            const valor = this.value;

            if (!/^\d{4}-\d{2}-\d{2}$/.test(valor)) {
                swal("Atención", "Formato de fecha invá lido.", "warning");
                this.value = '';
                return;
            }

            const [anio, mes, dia] = valor.split('-');

            if (anio.length !== 4 || parseInt(anio) < 1900 || parseInt(anio) > 2099) {
                swal("Atención", "El año debe tener 4 dígitos y estar entre 1900 y 2099.", "warning");
                this.value = '';
                return;
            }

            if (mes.length !== 2 || parseInt(mes) < 1 || parseInt(mes) > 12) {
                swal("Atención", "Mes inválido.", "warning");
                this.value = '';
                return;
            }

            if (dia.length !== 2 || parseInt(dia) < 1 || parseInt(dia) > 31) {
                swal("Atención", "Día inválido.", "warning");
                this.value = '';
                return;
            }
        });

        document.getElementById('busquedaPa').addEventListener('input', function() {
            var searchTerm = this.value
            cargarPacientes(1,
                searchTerm)
        })

        // Evento click para la paginación
        document.addEventListener('click', function(event) {
            if (event.target.matches('#pagination a')) {
                event.preventDefault()
                var href = event.target.getAttribute('href')
                var page = href.split('page=')[1]

                // Asegurarse de que 'page' sea un número antes de hacer la solicitud
                if (!isNaN(page)) {
                    cargar(page);
                }
            }
        });

        document.addEventListener('click', function(event) {
            if (event.target.matches('#paginationPac a')) {
                event.preventDefault()
                var href = event.target.getAttribute('href')
                var page = href.split('page=')[1]

                // Asegurarse de que 'page' sea un número antes de hacer la solicitud
                if (!isNaN(page)) {
                    cargarPacientes(page);
                }
            }
        });

        // Evento input para el campo de búsqueda
        document.getElementById('busqueda').addEventListener('input', function() {
            var searchTerm = this.value;
            cargar(1,
                searchTerm); // Cargar la primera página con el término de búsqueda
        });

    })

    function agregarOrdenMedica() {

        //obtener texto del select2
        let codigo = document.getElementById('codConsultaConsulta').value;
        let textoCodigo = $('#codConsultaConsulta').select2('data')[0]?.text;
        let cantidad = document.getElementById('cantidad').value;
        let textoObservacion = document.getElementById('observacion').value;

        if (textoCodigo == "" || cantidad == "" || textoObservacion == "") {
            swal("¡Alerta!", "Por favor, complete todos los campos", "warning")
            return;
        }

        agregarOrdenMedicaTr(codigo, textoCodigo, cantidad, textoObservacion)

    }

    function agregarOrdenMedicaTr(codigo, textoCodigo, cantidad, textoObservacion) {
        let tabla = document.getElementById('tbodyOrdenesMedicas')

        let fila = tabla.insertRow()
        fila.innerHTML = `
     
            <td>${tabla.rows.length}</td>
            <td class="texto-orden-medica"><b>${textoCodigo}</b><p><strong>Observación: </strong> ${textoObservacion}</p></td>
            <td>${cantidad}</td>
            <td>
                <button type="button" class="btn btn-danger" onclick="eliminarOrdenMedica(${fila.rowIndex})">
                    <i class="fa fa-trash"></i>
                </button>
            </td>
            <input type="hidden" name="codigoOrdenMedica[]" value="${codigo}">
            <input type="hidden" name="cantidadOrdenMedica[]" value="${cantidad}">
            <input type="hidden" name="observacionOrdenMedica[]" value="${textoObservacion}">
        `;

        fila.id = `filaOrdenMedica${tabla.rows.length}`

        tabla.appendChild(fila)

        $('#codConsultaConsulta').val(null).trigger('change');
        document.getElementById('cantidad').value = ""
        document.getElementById('observacion').value = ""

    }

    function eliminarOrdenMedica(index) {
        let fila = document.getElementById(`filaOrdenMedica${index}`)
        fila.remove()
    }

    // Función para mostrar/ocultar el spinner
    function toggleSpinner(show) {
        document.getElementById('loadingSpinner').style.display = show ? 'block' : 'none';
    }

    function cargarPacientes(page, searchTerm = '') {
        toggleSpinner(true);
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
                toggleSpinner(false);
            })
    }

    function seleccionarPaciente(element) {

        let idPaciente = element.getAttribute("data-id")
        let edadPaciente = parseInt(element.getAttribute("data-edad"), 10)

        document.getElementById('idPaciente').value = idPaciente
        const modal = document.getElementById('modalPacientes')
        const modalInstance = bootstrap.Modal.getInstance(modal)
        modalInstance.hide()

        //Mostrar modal de informe
        var modalInforme = new bootstrap.Modal(document.getElementById("modalInformeEvoluciones"), {
            backdrop: 'static',
            keyboard: false
        });
        modalInforme.show();
    }
    function seleccionarPacienteNeuropsicologia(idPaciente, edad) {
    
        let edadPaciente = parseInt(edad, 10)

        document.getElementById('idPaciente').value = idPaciente
      
        //Mostrar modal de informe
        var modalInforme = new bootstrap.Modal(document.getElementById("modalInformeEvoluciones"), {
            backdrop: 'static',
            keyboard: false
        });
        modalInforme.show();

        //titulo del modal
        document.getElementById('tituloInforme').innerHTML = 'Crear nuevo informe';
    }


    function imprimirInforme(idInforme) {

        document.getElementById('idInforme').value = idInforme
        var modal = new bootstrap.Modal(document.getElementById("modalEnviarImprimir"), {
            backdrop: 'static',
            keyboard: false
        });
        modal.show();
    }

    function generarPDF() {

        var idInforme = document.getElementById('idInforme').value
        $("#loader-pdf").show();
        $("#titulo_loader_pdf").text("Generando PDF");
        fetch("{{ route('informes.imprimirInformeNeuropsicologia') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    idInforme: idInforme
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error al generar el informe.');
                }
                return response.blob();
            })
            .then(blob => {
                // Cerrar el loader
                var a = document.createElement('a');
                var url = window.URL.createObjectURL(blob);
                a.href = url;
                a.download = 'InformeEvolucion.pdf';
                a.click();
                window.URL.revokeObjectURL(url);
                $("#loader-pdf").hide();
            })
            .catch(error => {
                // Cerrar el loader y mostrar error
                swal({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ocurrió un error al generar el informe: ' + error.message,
                    showConfirmButton: false,
                    timer: 2000
                });
                console.error('Error:', error);
            });
    }


    function enviarInforme() {
        $("#loader-pdf").show();
        $("#titulo_loader_pdf").text("Enviando informe");
        fetch("{{ route('informes.enviarInformeNeuropsicologia') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    idInforme: document.getElementById("idInforme").value
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.response == 'noCorreo') {
                    swal("¡Alerta!", "No se encontró un correo electrónico para enviar la consulta", "warning")
                } else {
                    swal("¡Buen trabajo!", "La consulta se ha enviado correctamente", "success")
                }
                $("#loader-pdf").hide();
            })
            .catch(error => console.error('Error:', error));
    }

    function nuevoRegistro() {

        var modal = new bootstrap.Modal(document.getElementById("modalPacientes"), {
            backdrop: 'static',
            keyboard: false
        })
        modal.show()

        document.getElementById('tituloInforme').innerHTML = 'Crear nuevo informe'
        feather.replace();

        //limpiar fecha y hora
        document.getElementById('fechaEvolucion').value = ""
        document.getElementById('horaSeleccionada').value = ""
        document.getElementById('profesionalInforme').value = ""

        CKEDITOR.instances['motivoConsulta'].setData("")
        CKEDITOR.instances['estadoActual'].setData("")
        CKEDITOR.instances['historiaPersonal'].setData("")
        CKEDITOR.instances['desarrolloPsicomotor'].setData("")
        CKEDITOR.instances['desarrolloLenguaje'].setData("")
        CKEDITOR.instances['abc'].setData("")
        CKEDITOR.instances['antecedentesMedicosFamiliares'].setData("")
        CKEDITOR.instances['antecedentesPersonales'].setData("")
        CKEDITOR.instances['historiaDesarrollo'].setData("")
        CKEDITOR.instances['historiaEscolar'].setData("")
        CKEDITOR.instances['historiaSocioAfectiva'].setData("")
        CKEDITOR.instances['condicionPaciente'].setData("")
        CKEDITOR.instances['resultadosEvaluacion'].setData("")
        CKEDITOR.instances['impresionDiagnostica'].setData("")
        document.getElementById("accInforme").value = "guardar"

    }

    function cargar(page, searchTerm = '') {
        toggleSpinner(true);
        let url = "{{ route('informes.neuropsicologia') }}";

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
                document.getElementById('trRegistros').innerHTML = responseData.pacientesEvol;
                feather.replace();
                document.getElementById('pagination-links').innerHTML = responseData.links;
                loadNow(0);
            })
            .catch(error => console.error('Error:', error))
            .finally(() => {
                toggleSpinner(false);
            });
    }


    function editarInforme(idInforme) {
        toggleSpinner(true);
        var modal = new bootstrap.Modal(document.getElementById("modalInformeEvoluciones"), {
            backdrop: 'static',
            keyboard: false
        });
        modal.show();

        document.getElementById('fileList').innerHTML = ""
        document.getElementById('listAnexos').style.display = 'none'
        document.getElementById('fileList').innerHTML = ""
        document.getElementById('anexosAdd').innerHTML = ""
        document.getElementById('tbodyOrdenesMedicas').innerHTML = ""
        $('#codImpresionDiagnostico').val(null).trigger('change');
        $('#codImpresionDiagnosticoRelacionado1').val(null).trigger('change');
        $('#codImpresionDiagnosticoRelacionado2').val(null).trigger('change');
        agregarArchivo()

        fetch("{{ route('informes.buscaInformeNeuropsicologica') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    idInforme: idInforme
                })
            })
            .then(response => response.json())
            .then(datosInforme => {
                document.getElementById("accInforme").value = "editar"
                document.getElementById("idInforme").value = datosInforme.informe.id
                document.getElementById("idPaciente").value = datosInforme.informe.id_paciente
                const [fecha, hora] = datosInforme.informe.fecha_creacion.split(' ')
                document.getElementById('fechaEvolucion').value = fecha
                document.getElementById('horaSeleccionada').value = hora.slice(0, 5)
                document.getElementById("profesionalInforme").value = datosInforme.informe.id_profesional
                $('#profesionalInforme').trigger('change')
                //consultar un solo campo si la fecha es menor a 2026-01-30
                let contenido = "";
                if(fecha > '2026-01-30'){
                    contenido += datosInforme.informe.motivo_consulta;
                }else{
                //concatenar todo el contenido en un solo editor sin son diferentes a null
               
                if (datosInforme.informe.motivo_consulta != null) {
                    contenido += "<strong>MOTIVO DE CONSULTA:</strong> " + datosInforme.informe.motivo_consulta + "\n";
                    contenido += datosInforme.informe.motivo_consulta + "\n";
                }

                if (datosInforme.informe.estado_actual != null) {
                    contenido += "<strong>ESTADO ACTUAL:</strong> " + datosInforme.informe.estado_actual + "\n";
                    contenido += datosInforme.informe.estado_actual + "\n";
                }
                if (datosInforme.informe.historia_personal != null) {
                    contenido += "<strong>HISTORIA PERSONAL:</strong> " + datosInforme.informe.historia_personal + "\n";
                    contenido += datosInforme.informe.historia_personal + "\n";
                }
                if (datosInforme.informe.desarrollo_psicomotor != null) {
                    contenido += "<strong>DESARROLLO PSICOMOTOR:</strong> " + datosInforme.informe.desarrollo_psicomotor + "\n";
                    contenido += datosInforme.informe.desarrollo_psicomotor + "\n";
                }
                if (datosInforme.informe.desarrollo_lenguaje != null) {
                    contenido += "<strong>DESARROLLO DE LENGUAJE:</strong> " + datosInforme.informe.desarrollo_lenguaje + "\n";
                    contenido += datosInforme.informe.desarrollo_lenguaje + "\n";
                }
                if (datosInforme.informe.abc != null) {
                    contenido += "<strong>ABC:</strong> " + datosInforme.informe.abc + "\n";
                    contenido += datosInforme.informe.abc + "\n";
                }
                if (datosInforme.informe.antecedentes_medicos_familiares != null) {
                    contenido += "<strong>ANTECEDENTES MEDICOS FAMILIARES:</strong> " + datosInforme.informe.antecedentes_medicos_familiares + "\n";
                    contenido += datosInforme.informe.antecedentes_medicos_familiares + "\n";
                }
                if (datosInforme.informe.antecedentes_personales != null) {
                    contenido += "<strong>ANTECEDENTES PERSONALES:</strong> " + datosInforme.informe.antecedentes_personales + "\n";
                    contenido += datosInforme.informe.antecedentes_personales + "\n";
                }
                if (datosInforme.informe.historia_desarrollo != null) {
                    contenido += "<strong>HISTORIA DEL DESARROLLO:</strong> " + datosInforme.informe.historia_desarrollo + "\n";
                    contenido += datosInforme.informe.historia_desarrollo + "\n";
                }
                if (datosInforme.informe.historia_escolar != null) {
                    contenido += "<strong>HISTORIA ESCOLAR:</strong> " + datosInforme.informe.historia_escolar + "\n";
                    contenido += datosInforme.informe.historia_escolar + "\n";
                }
                if (datosInforme.informe.historia_socio_afectiva != null) {
                    contenido += "<strong>HISTORIA SOCIOAFECTIVA:</strong> " + datosInforme.informe.historia_socio_afectiva + "\n";
                    contenido += datosInforme.informe.historia_socio_afectiva + "\n";
                }
                if (datosInforme.informe.condicion_paciente != null) {
                    contenido += "<strong>CONDICIÓN DEL PACIENTE EN LA CONSULTA:</strong> " + datosInforme.informe.condicion_paciente + "\n";
                    contenido += datosInforme.informe.condicion_paciente + "\n";
                }
                }
                CKEDITOR.instances['motivoConsulta'].setData(contenido)
                CKEDITOR.instances['resultadosEvaluacion'].setData(datosInforme.informe.resultados_evaluacion)
                CKEDITOR.instances['impresionDiagnostica'].setData(datosInforme.informe.impresion_diagnostica)


                //cargar diagnosticos

                if (datosInforme.informe.impresion_diagnostica_princippal != null) {
                    cargarCodigoCIE10(datosInforme.informe.impresion_diagnostica_princippal, 'codImpresionDiagnostico')
                }
                if (datosInforme.informe.impresion_diagnostica_relacionada_1 != null) {
                    cargarCodigoCIE10(datosInforme.informe.impresion_diagnostica_relacionada_1, 'codImpresionDiagnosticoRelacionado1')
                }

                if (datosInforme.informe.impresion_diagnostica_relacionada_2 != null) {
                    cargarCodigoCIE10(datosInforme.informe.impresion_diagnostica_relacionada_2, 'codImpresionDiagnosticoRelacionado2')
                }

                //cargar ordenes medicas              
                if (datosInforme.ordenMedica.length > 0) {
                    datosInforme.ordenMedica.forEach(orden => {
                        let codigo = orden.id
                        let textoCodigo = orden.codigo + ' - ' + orden.textoCodigo
                        let cantidad = orden.cantidad
                        let textoObservacion = orden.observacion
                        agregarOrdenMedicaTr(codigo, textoCodigo, cantidad, textoObservacion)
                    })
                }


                document.getElementById('tituloInforme').innerHTML = 'Editar informe'
                feather.replace();

                if (datosInforme.anexos.length > 0) {
                    document.getElementById('listAnexos').style.display = 'initial'

                    let anexos = document.getElementById('anexosAdd')
                    let listAnexos = ""

                    datosInforme.anexos.forEach(anexo => {
                        let parTipo = anexo.tipo_archivo.split('/')
                        listAnexos = `<div class="col-xl-6" id="anexo-${anexo.id}">
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
                                                                    <div class="col-auto">
                                                                        <a href="javascript:eliminarAnexo('${anexo.id}');" class="p-10 fs-18 link">
                                                                            <i class="fa fa-trash"></i>
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
            .finally(() => {
                toggleSpinner(false);
            });
    }


    function cargarCodigoCIE10(codigo_dx, id) {
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

    function eliminarAnexo(idAnexo) {
        swal({
            title: "¿Estás seguro de querer eliminar este anexo?",
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
                let url = "{{ route('informes.eliminarAnexoInforme') }}"
                fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            idAnexo: idAnexo
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        let anexo = document.getElementById(`anexo-${idAnexo}`)
                        anexo.remove()

                        if (data.success) {
                            swal("¡Buen trabajo!",
                                data.message,
                                "success")
                        }
                    })
            } else {
                swal("Cancelado", "Tu registro esta salvo :)", "error")
            }
        })
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
            return `${kb} KB`
        } else {
            const mb = kb / 1024
            return `${mb.toFixed(2)} MB`
        }
    }


    function eliminarInforme(idReg) {

        swal({
            title: "Esta seguro?",
            text: "No podrás recuperar este registrto!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#fec801",
            confirmButtonText: "Si, eliminar!",
            cancelButtonText: "No, cancelar!",
            closeOnConfirm: false,
            closeOnCancel: false
        }, function(isConfirm) {
            if (isConfirm) {
                let url = "{{ route('informes.eliminarInformeNeuro') }}";
                fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        body: JSON.stringify({
                            idReg: idReg
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            swal("¡Buen trabajo!",
                                data.message,
                                "success");
                            cargar(1);
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

    function salirInformeEvolucion() {
        const modal = document.getElementById('modalInformeEvoluciones')
        const modalInstance = bootstrap.Modal.getInstance(modal)
        modalInstance.hide()

    }

    function astrasInformeEvolucion() {
        document.querySelector('#listadoInformeEvoluciones').style.display = 'initial'
        document.querySelector('#detalleInformeEvoluciones').style.display = 'none'
        document.querySelector('#atrasInf').style.display = 'none'
        document.querySelector('#guardarInf').style.display = 'none'
        document.querySelector('#salirInfo').style.display = 'initial'
        document.getElementById('tituloInforme').innerHTML = 'Listado de informe'
    }

    function descargarArchivos(idInforme) {

        return new Promise((resolve, reject) => {
            let url = "{{ route('informes.buscarAnexosInforme') }}"
            fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    body: JSON.stringify({
                        idInforme: idInforme
                    })
                })
                .then(response => response.json())
                .then(data => {

                    data.anexos.forEach(informes => {
                        let rtotal = $("#RutaTotal").data("ruta")
                        window.open(`${rtotal}anexosPacientes/${informes.url}`, '_blank');
                    })
                    resolve() // Resuelve la promesa cuando los datos han sido cargados
                })
                .catch(error => {
                    console.error('Error:', error)
                    reject(error) // Rechaza la promesa si ocurre un error
                })
        })
    }


    function guardarInformeEvolucion() {
        if ($("#formInformeEvolucion").valid()) {
            for (var instanceName in CKEDITOR.instances) {
                CKEDITOR.instances[instanceName].updateElement()
            }
            const formInformeEvolucion = document.getElementById('formInformeEvolucion')
            const formData = new FormData(formInformeEvolucion)

            // Validar que el profesional esté seleccionado
            if (document.getElementById('profesionalInforme').value === '') {
                swal('Alerta', 'Debe seleccionar un profesional.', 'warning')
                return;
            }

            // Validar que la fecha esté seleccionada
            if (document.getElementById('fechaEvolucion').value === '') {
                swal('Alerta', 'Debe seleccionar una fecha.', 'warning')
                return;
            }

            //validar hora
            if (document.getElementById('horaSeleccionada').value === '') {
                swal('Alerta', 'Debe seleccionar una hora.', 'warning')
                return;
            }


            const url = "{{ route('form.guardarInformeNeuropsicologica') }}"
            fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success === 'success') {
                        swal(data.title, data.message, data.success)
                        cargar(1)
                        const modal = document.getElementById('modalInformeEvoluciones');
                        const modalInstance = bootstrap.Modal.getInstance(modal);
                        modalInstance.hide();
                    } else {
                        swal(data.title, data.message, data.success)
                    }
                })
                .catch(error => {
                    swal('Error', 'Ocurrió un error al procesar la solicitud', 'error')
                    console.error("Error al enviar los datos:", error);
                });
        }
    }

    function cargarProfesionales() {
        let rtotal = $("#RutaTotal").data("ruta")
        const urlProfesionales = `${rtotal}profesionales/cargarListaProf`
        fetch(urlProfesionales)
            .then(response => response.json())
            .then(data => {
                const selectProfesional = document.getElementById('profesionalInforme')
                llenarSelect(selectProfesional, data)
            })
            .catch(error => console.error('Error al cargar profesionales:', error));
    }

    function llenarSelect(selectElement, data) {
        selectElement.innerHTML = '<option value="">Seleccione una opción</option>'
        data.forEach(item => {
            const option = document.createElement('option')
            option.value = item.id
            option.textContent = item.nombre
            selectElement.appendChild(option)
        });
    }

    function salirEvolucion() {
        const modalInforme = document.getElementById('modalInformeEvoluciones');
        const modalEvoluciones = document.getElementById('modalEvoluciones');

        // Cerrar el modal actual (modalEvoluciones)
        const modalEvolucionesInstance = bootstrap.Modal.getInstance(modalEvoluciones);
        if (modalEvolucionesInstance) {
            modalEvolucionesInstance.hide();
        }

        if (modalControl) {
            const modalInformeInstance = new bootstrap.Modal(modalInforme, {
                backdrop: 'static',
                keyboard: false
            });
            modalInformeInstance.show();
        }

    }

    function astrasEvolucion() {
        document.querySelector('#listadoEvoluciones').style.display = 'initial'
        document.querySelector('#detalleEvoluciones').style.display = 'none'
        document.querySelector('#atrasEv').style.display = 'none'
        document.querySelector('#salieEv').style.display = 'initial'
    }

    function agregarArchivo() {
        // Crear un nuevo contenedor para el campo de entrada
        const newFileContainer = document.createElement('div')
        newFileContainer.classList.add('col-md-12', 'mb-2')

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

    function clearSelect(selectId) {
        $('#' + selectId).val(null).trigger('change');
    }
</script>

@endsection