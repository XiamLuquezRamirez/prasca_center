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

                                </ul>


                                <div class="tab-content mt-3">
                                    <!-- Información General -->
                                    <div class="tab-pane active" id="informacion-general" role="tabpanel">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label for="fechaEvolucion" class="form-label">Fecha y Hora:</label>
                                                <div class="input-group">
                                                    <input type="date" class="form-control" id="fechaEvolucion"
                                                        name="fechaEvolucion" placeholder="Seleccione la fecha" />
                                                    <input type="time" id="horaSeleccionada"
                                                        name="horaSeleccionada" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-9">
                                                <label for="account-username">Profesional:</label>
                                                <select class="form-control select2" style="width: 100%;"
                                                    id="profesionalInforme" name="profesionalInforme">
                                                </select>
                                            </div>

                                            <div class="col-md-12 mt-3">
                                                <label class="form-label" for="motivoConsulta">MOTIVO DE CONSULTA:</label>
                                                <textarea class="form-control" id="motivoConsulta" name="motivoConsulta" rows="3"
                                                    placeholder="Ingrese el motivo de consulta.."></textarea>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label" for="estadoActual">ESTADO ACTUAL:</label>
                                                <textarea class="form-control" id="estadoActual" name="estadoActual" rows="3"
                                                    placeholder="Ingrese el estado actual del paciente.."></textarea>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label" for="historiaPersonal">HISTORIA PERSONAL:</label>
                                                <textarea class="form-control" id="historiaPersonal" name="historiaPersonal" rows="3"
                                                    placeholder="Ingrese la historia personal del paciente.."></textarea>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label" for="desarrolloPsicomotor">DESARROLLO PSICOMOTOR:</label>
                                                <textarea class="form-control" id="desarrolloPsicomotor" name="desarrolloPsicomotor" rows="3"
                                                    placeholder="Ingrese el desarrollo psicomotor del paciente.."></textarea>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label" for="desarrolloLenguaje">DESARROLLO DE LENGUAJE:</label>
                                                <textarea class="form-control" id="desarrolloLenguaje" name="desarrolloLenguaje" rows="3"
                                                    placeholder="Ingrese el desarrollo de lenguaje del paciente.."></textarea>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label" for="abc">ABC:</label>
                                                <textarea class="form-control" id="abc" name="abc" rows="3"
                                                    placeholder="Ingrese el abc del paciente.."></textarea>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label" for="antecedentesMedicosFamiliares">ANTECEDENTES MEDICOS FAMILIARES:</label>
                                                <textarea class="form-control" id="antecedentesMedicosFamiliares" name="antecedentesMedicosFamiliares" rows="3"
                                                    placeholder="Ingrese los antecedentes medicos familiares del paciente.."></textarea>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label" for="antecedentesPersonales">ANTECEDENTES PERSONALES:</label>
                                                <textarea class="form-control" id="antecedentesPersonales" name="antecedentesPersonales" rows="3"
                                                    placeholder="Ingrese los antecedentes personales del paciente.."></textarea>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label" for="historiaDesarrollo">HISTORIA DEL DESARROLLO:</label>
                                                <textarea class="form-control" id="historiaDesarrollo" name="historiaDesarrollo" rows="3"
                                                    placeholder="Ingrese la historia del desarrollo del paciente.."></textarea>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label" for="historiaEscolar">HISTORIA ESCOLAR:</label>
                                                <textarea class="form-control" id="historiaEscolar" name="historiaEscolar" rows="3"
                                                    placeholder="Ingrese la historia escolar del paciente.."></textarea>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label class="form-label" for="historiaSocioAfectiva">HISTORIA SOCIOAFECTIVA:</label>
                                                <textarea class="form-control" id="historiaSocioAfectiva" name="historiaSocioAfectiva" rows="3"
                                                    placeholder="Ingrese la historia socioafectiva del paciente.."></textarea>
                                            </div>

                                            <div class="col-md-12 mt-3">
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
    <div class="modal-dialog modal-dialog-centered" style="max-width: 20%;">
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
            "use strict"
            ids.forEach(id => {
                CKEDITOR.replace(id, {
                    extraPlugins: 'uploadimage,pastefromword,maximize,justify,font', // ← Se agrega 'font'
                    toolbar: [{
                            name: 'basicstyles',
                            items: ['Bold', 'Italic', 'Underline']
                        },
                        {
                            name: 'paragraph',
                            items: ['NumberedList', 'BulletedList', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock']
                        },
                        {
                            name: 'styles',
                            items: ['FontSize'] // ← Se agrega el botón de tamaño de fuente
                        },
                        {
                            name: 'undo',
                            items: ['Undo', 'Redo']
                        },
                        {
                            name: 'maximize',
                            items: ['Maximize']
                        },
                        {
                            name: 'insertImage',
                            items: ['Image']
                        }
                    ],
                    language: 'es',
                    height: 200,
                    resize_enabled: false
                })


            })
        })

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

                CKEDITOR.instances['motivoConsulta'].setData(datosInforme.informe.motivo_consulta)
                CKEDITOR.instances['estadoActual'].setData(datosInforme.informe.estado_actual)
                CKEDITOR.instances['historiaPersonal'].setData(datosInforme.informe.historia_personal)
                CKEDITOR.instances['desarrolloPsicomotor'].setData(datosInforme.informe.desarrollo_psicomotor)
                CKEDITOR.instances['desarrolloLenguaje'].setData(datosInforme.informe.desarrollo_lenguaje)
                CKEDITOR.instances['abc'].setData(datosInforme.informe.abc)
                CKEDITOR.instances['antecedentesMedicosFamiliares'].setData(datosInforme.informe.antecedentes_medicos_familiares)
                CKEDITOR.instances['antecedentesPersonales'].setData(datosInforme.informe.antecedentes_personales)
                CKEDITOR.instances['historiaDesarrollo'].setData(datosInforme.informe.historia_desarrollo)
                CKEDITOR.instances['historiaEscolar'].setData(datosInforme.informe.historia_escolar)
                CKEDITOR.instances['historiaSocioAfectiva'].setData(datosInforme.informe.historia_socio_afectiva)
                CKEDITOR.instances['condicionPaciente'].setData(datosInforme.informe.condicion_paciente)
                CKEDITOR.instances['resultadosEvaluacion'].setData(datosInforme.informe.resultados_evaluacion)
                CKEDITOR.instances['impresionDiagnostica'].setData(datosInforme.informe.impresion_diagnostica)

                document.getElementById('tituloInforme').innerHTML = 'Editar informe'
                feather.replace();
            })
            .catch(error => console.error('Error:', error))
            .finally(() => {
                toggleSpinner(false);
            });
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
</script>

@endsection