@extends('Plantilla.Principal')
@section('title', 'Informe de psicología')
@section('Contenido')
    <input type="hidden" id="RutaTotal" data-ruta="{{ asset('/') }}" />

    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h4 class="page-title">Informe de psicología</h4>
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
                                </div>

                            </div>
                        </div>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th style="width:40%;">Paciente</th>
                                    <th style="width:20%;">Ultima evolución</th>
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
                    <div id="listadoInformeEvoluciones">
                        <div class="box-controls pull-right mb-4">
                            <div class="box-header-actions">
                                <div class="input-group input-group-merge">
                                    <button type="button" onclick="nuevoInforme();"
                                        class="btn btn-xs btn-primary font-bold"><i class="fa fa-plus"></i> Nuevo informe

                                    </button>
                                </div>

                            </div>
                        </div>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th style="width:10%;">#</th>
                                    <th style="width:60%;">Profesional</th>
                                    <th style="width:20%;">Fecha creación</th>
                                    <th style="width:10%;">Acción</th>
                                </tr>
                            </thead>
                            <tbody id="trInformes">


                            </tbody>
                        </table>
                        <div id="pagination-links-consulta" class="text-center ml-1 mt-2">

                        </div>
                    </div>
                    <div id="detalleInformeEvoluciones" style="display: none;">
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
                                            <a class="nav-link" data-bs-toggle="tab" href="#evaluacion"
                                                role="tab">Evaluación y Diagnóstico</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" href="#tratamiento"
                                                role="tab">Tratamiento</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" href="#intervenciones"
                                                role="tab">Intervenciones</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" href="#observaciones"
                                                role="tab">Observaciones</a>
                                        </li>
                                    </ul>
                                    <div class="tab-content mt-3">
                                        <!-- Información General -->
                                        <div class="tab-pane active" id="informacion-general" role="tabpanel">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="numeroSesiones" class="form-label">Número de sesiones
                                                            realizadas:</label>
                                                        <input type="number" class="form-control" id="numeroSesiones"
                                                            name="numeroSesiones"
                                                            placeholder="Ingrese el número de sesiones realizadas..">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <label for="fechaEvolucion" class="form-label">Fecha y Hora:</label>
                                                    <div class="input-group">
                                                        <input type="date" class="form-control" id="fechaEvolucion"
                                                            name="fechaEvolucion" placeholder="Seleccione la fecha" />
                                                        <input type="time" id="horaSeleccionada"
                                                            name="horaSeleccionada" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-md-5">
                                                    <label for="account-username">Profesional:</label>
                                                    <select class="form-control select2" style="width: 100%;"
                                                        id="profesionalInforme" name="profesionalInforme">
                                                    </select>
                                                </div>

                                            </div>
                                        </div>

                                        <!-- Evaluación y Diagnóstico -->
                                        <div class="tab-pane" id="evaluacion" role="tabpanel">
                                            <div class="form-group">
                                                <label for="codImpresionDiagnosticoConsulta" class="form-label">Impresión
                                                    Diagnóstica :</label>
                                                <select class="form-control select2" id="codImpresionDiagnosticoConsulta"
                                                    name="codImpresionDiagnosticoConsulta"></select>
                                            </div>


                                            <div class="form-group">
                                                <label for="impresion_diagnostica" class="form-label">Establecido por
                                                    primera
                                                    vez.</label>
                                                <select class="form-control" id="establecidoPrimeraVez"
                                                    name="establecidoPrimeraVez" aria-invalid="false">
                                                    <option value="">Seleccione...</option>
                                                    <option value="Si">Si</option>
                                                    <option value="No">No</option>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label for="remision" class="form-label">Remisión:</label>
                                                <textarea class="form-control" id="remision" name="remision" rows="3"
                                                    placeholder="Ingrese la remisión del paciente.."></textarea>
                                            </div>
                                            <div class="form-group">
                                                <label for="resumen_evaluacion_inicial" class="form-label">Resumen de
                                                    evaluación psicológica inicial:</label>
                                                <textarea class="form-control" id="resumen_evaluacion_inicial" name="resumen_evaluacion_inicial" rows="3"
                                                    placeholder="Resumen de evaluación psicológica inicial"></textarea>
                                            </div>
                                        </div>

                                        <!-- Tratamiento -->
                                        <div class="tab-pane" id="tratamiento" role="tabpanel">
                                            <div class="form-group">
                                                <label for="objetivo_terapeutico" class="form-label">Objetivos
                                                    terapéuticos
                                                    iniciales:</label>
                                                <textarea class="form-control" id="objetivo_terapeutico" name="objetivo_terapeutico" rows="3"
                                                    placeholder="Ingrese los objetivos terapéuticos iniciales.."></textarea>
                                            </div>
                                            <div class="form-group">
                                                <label for="evolucion_tratamiento" class="form-label">Evolución del
                                                    tratamiento psicológico actual:</label>
                                                <textarea class="form-control" id="evolucion_tratamiento" name="evolucion_tratamiento" rows="3"
                                                    placeholder="Describa la evolución actual del tratamiento.."></textarea>
                                            </div>
                                            <div class="form-group">
                                                <label for="evaluacion_actual" class="form-label">Evaluación
                                                    actual:</label>
                                                <textarea class="form-control" id="evaluacion_actual" name="evaluacion_actual" rows="3"
                                                    placeholder="Ingrese la evaluación actual.."></textarea>
                                            </div>
                                            <div class="form-group">
                                                <label for="plan_continuidad" class="form-label">Plan de
                                                    continuidad:</label>
                                                <textarea class="form-control" id="plan_continuidad" name="plan_continuidad" rows="3"
                                                    placeholder="Describa el plan de continuidad.."></textarea>
                                            </div>
                                        </div>

                                        <!-- Intervenciones -->
                                        <div class="tab-pane" id="intervenciones" role="tabpanel">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label for="intervencion_psiquiatria" class="form-label">Intervención
                                                        por
                                                        Psiquiatría:</label>
                                                    <textarea class="form-control" id="intervencion_psiquiatria" name="intervencion_psiquiatria" rows="3"
                                                        placeholder="Describa la intervención por Psiquiatría.."></textarea>
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="intervencion_neurologia" class="form-label">Intervención
                                                        por
                                                        Neurología:</label>
                                                    <textarea class="form-control" id="intervencion_neurologia" name="intervencion_neurologia" rows="3"
                                                        placeholder="Describa la intervención por Neurología.."></textarea>
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="intervencion_neuropsicologia"
                                                        class="form-label">Intervención
                                                        por Neuropsicología:</label>
                                                    <textarea class="form-control" id="intervencion_neuropsicologia" name="intervencion_neuropsicologia" rows="3"
                                                        placeholder="Describa la intervención por Neuropsicología.."></textarea>
                                                </div>
                                            </div>
                                            <div class="form-group mt-2">
                                                <label for="sugerencia_consulta" class="form-label">Sugerencias para
                                                    Interconsultas:</label>
                                                <textarea class="form-control" id="sugerencia_consulta" name="sugerencia_consulta" rows="3"
                                                    placeholder="Ingrese sugerencias para interconsultas.."></textarea>
                                            </div>
                                        </div>

                                        <!-- Observaciones -->
                                        <div class="tab-pane" id="observaciones" role="tabpanel">
                                            <div class="form-group">
                                                <label for="observaciones_consulta" class="form-label">Observaciones y
                                                    Recomendaciones:</label>
                                                <textarea class="form-control" id="observaciones_consulta" name="observaciones_consulta" rows="3"
                                                    placeholder="Ingrese observaciones y recomendaciones.."></textarea>
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
                    <button type="button" style="display: none;" id="verEvol" onclick="verEvoluciones();"
                        class="waves-effect waves-light btn btn-info me-1">
                        <i class="ti-eye"></i> Evoluciones
                    </button>
                    <button type="button" style="display: none;" id="guardarInf" onclick="guardarInformeEvolucion();"
                        class="btn btn-success me-1">
                        <i class="ti-save"></i> guardar
                    </button>
                    <button type="button" style="display: none;" id="atrasInf" onclick="astrasInformeEvolucion();"
                        class="btn btn-primary-light me-1">
                        <i class="ti-arrow-left"></i> Atras
                    </button>
                </div>
            </div><!-- /.modal-dialog -->
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
            let menuS = document.getElementById("informePsicologia")

            menuP.classList.add("active", "menu-open")
            menuS.classList.add("active")

            let rtotal = $("#RutaTotal").data("ruta")
            
            let modalControl;

            loader = document.getElementById('loader')
            loadNow(1)
            cargar(1)
            cargarProfesionales()

            $('#codImpresionDiagnosticoConsulta').select2({
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
                minimumInputLength: 1, // Requiere al menos 1 ca|ácter
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
                'remision',
                'resumen_evaluacion_inicial',
                'objetivo_terapeutico',
                'evolucion_tratamiento',
                'evaluacion_actual',
                'plan_continuidad',
                'sugerencia_consulta',
                'observaciones_consulta',
                'intervencion_psiquiatria',
                'intervencion_neurologia',
                'intervencion_neuropsicologia'
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

            // Evento click para la paginación
            document.addEventListener('click', function(event) {
                if (event.target.matches('.pagination a')) {
                    event.preventDefault()
                    var href = event.target.getAttribute('href')
                    var page = href.split('page=')[1]

                    // Asegurarse de que 'page' sea un número antes de hacer la solicitud
                    if (!isNaN(page)) {
                        cargar(page);
                    }
                }
            });
            // Evento input para el campo de búsqueda
            document.getElementById('busqueda').addEventListener('input', function() {
                var searchTerm = this.value;
                cargar(1,
                    searchTerm); // Cargar la primera página con el término de búsqueda
            });

        });

        function cargar(page, searchTerm = '') {

            let url = "{{ route('informes.psicologia') }}"; // Definir la URL

            // Eliminar los campos ocultos anteriores
            var oldPageInput = document.getElementById('page');
            var oldSearchTermInput = document.getElementById('searchTerm');
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
                    document.getElementById('trRegistros').innerHTML = responseData.pacientesEvol;
                    feather.replace();
                    // Colocar los enlaces de paginación
                    document.getElementById('pagination-links').innerHTML = responseData.links;
                    loadNow(0);
                })
                .catch(error => console.error('Error:', error));

        }

        function cargarInformes(page, searchTerm = '') {


            let url = "{{ route('informes.informePsicologia') }}"; // Definir la URL

            // Eliminar los campos ocultos anteriores
            var oldPageInput = document.getElementById('pageConsulta');
            if (oldPageInput) oldPageInput.remove();

            var data = {
                page: page,
                idPac: document.getElementById("idPaciente").value
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
                    document.getElementById('trInformes').innerHTML = responseData.informes;
                    feather.replace();
                    // Colocar los enlaces de paginación
                    document.getElementById('pagination-links-consulta').innerHTML = responseData.links;
                    loadNow(0);
                })
                .catch(error => console.error('Error:', error));



        }

        function editarInforme(idInforme) {
            fetch("{{ route('informes.buscaInformePsicologica') }}", {
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
                    document.getElementById("numeroSesiones").value = datosInforme.informe.numero_sesiones
                    const [fecha, hora] = datosInforme.informe.fecha_creacion.split(' ')
                    document.getElementById('fechaEvolucion').value = fecha
                    document.getElementById('horaSeleccionada').value = hora.slice(0, 5)
                    document.getElementById("profesionalInforme").value = datosInforme.informe.id_profesional
                    $('#profesionalInforme').trigger('change')

                    CKEDITOR.instances['remision'].setData(datosInforme.informe.remision)
                    CKEDITOR.instances['resumen_evaluacion_inicial'].setData(datosInforme.informe
                        .resumen_evaluacion_psicologica)
                    CKEDITOR.instances['objetivo_terapeutico'].setData(datosInforme.informe
                        .objetivos_terapeuticos_iniciales)
                    CKEDITOR.instances['evolucion_tratamiento'].setData(datosInforme.informe
                        .evolucion_tratamiento_actual)
                    CKEDITOR.instances['evaluacion_actual'].setData(datosInforme.informe.evaluacion_actual)
                    CKEDITOR.instances['plan_continuidad'].setData(datosInforme.informe.plan_continuidad)
                    CKEDITOR.instances['sugerencia_consulta'].setData(datosInforme.informe.sugerencias_interconsultas)
                    CKEDITOR.instances['observaciones_consulta'].setData(datosInforme.informe
                        .observaciones_recomendaciones)
                    CKEDITOR.instances['intervencion_psiquiatria'].setData(datosInforme.informe
                        .intervencion_psiquiatria)
                    CKEDITOR.instances['intervencion_neurologia'].setData(datosInforme.informe
                        .intervencion_neurologia)
                    CKEDITOR.instances['intervencion_neuropsicologia'].setData(datosInforme.informe
                        .intervencion_neurologia)

                    document.getElementById('establecidoPrimeraVez').value = datosInforme.informe.establecido_primera
                    $('#establecidoPrimeraVez').trigger('change')
                    cargarImpresion(datosInforme.informe.impresion_diagnostica, 'codImpresionDiagnosticoConsulta')


                    document.getElementById('tituloInforme').innerHTML = 'Editar informe'
                    document.querySelector('#listadoInformeEvoluciones').style.display = 'none'
                    document.querySelector('#detalleInformeEvoluciones').style.display = 'initial'
                    document.querySelector('#guardarInf').style.display = 'initial'
                    document.querySelector('#verEvol').style.display = 'initial'
                    document.querySelector('#atrasInf').style.display = 'initial'
                    document.querySelector('#salirInfo').style.display = 'none'
                    feather.replace();
                })
                .catch(error => console.error('Error:', error))


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
                    let url = "{{ route('informes.eliminarInforme') }}";
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
                                cargarInformes(1);
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

        function verHistorial(idPaciente) {
            // Obtener las referencias de los modales

            const modalInforme = document.getElementById('modalInformeEvoluciones');
            const modalEvoluciones = document.getElementById('modalEvoluciones');

            // Cerrar el modal modalInformeEvoluciones si está abierto
            const modalInformeInstance = bootstrap.Modal.getInstance(modalInforme);
            if (modalInformeInstance) {
                modalInformeInstance.hide();
            }

            // Crear una instancia y mostrar el modal modalEvoluciones
            const modalEvolucionesInstance = new bootstrap.Modal(modalEvoluciones, {
                backdrop: 'static',
                keyboard: false
            });
            modalEvolucionesInstance.show();

            // Realizar la solicitud fetch
            fetch("{{ route('informes.verHistorial') }}", {
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
                .then(responseData => {
                    // Rellenar la tabla con las filas generadas
                    document.querySelector('#listadoEvoluciones').innerHTML = responseData.evoluciones;
                    feather.replace(); // Asegúrate de que feather.js esté cargado
                })
                .catch(error => console.error('Error:', error));
        }


        function verEvoluciones() {
            modalControl = true
            let id_paciente = document.getElementById("idPaciente").value
            verHistorial(id_paciente)
        }

        function verEvolucion(idEvolucion) {

            fetch("{{ route('informes.buscaEvolucionPsicologica') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        idEvolucion: idEvolucion
                    })
                })
                .then(response => response.json())
                .then(datosConsulta => {

                    document.getElementById("detalleEvoluciones").innerHTML = datosConsulta.evolucion

                    document.querySelector('#listadoEvoluciones').style.display = 'none'
                    document.querySelector('#detalleEvoluciones').style.display = 'initial'
                    document.querySelector('#atrasEv').style.display = 'initial'
                    document.querySelector('#salieEv').style.display = 'none'
                    feather.replace();
                })
                .catch(error => console.error('Error:', error))
        }

        function generarInforme(idPaciente) {
            var modal = new bootstrap.Modal(document.getElementById("modalInformeEvoluciones"), {
                backdrop: 'static',
                keyboard: false
            });
            modal.show();

            document.getElementById('idPaciente').value = idPaciente
            cargarInformes()
        }

        function imprimirInforme(idInforme) {
            document.getElementById("idInforme").value = idInforme
            var modal = new bootstrap.Modal(document.getElementById("modalEnviarImprimir"   ), {
                backdrop: 'static',
                keyboard: false
            })

            modal.show()
        }

        function salirInformeEvolucion() {
            modalControl = false
            const modal = document.getElementById('modalInformeEvoluciones')
            const modalInstance = bootstrap.Modal.getInstance(modal)
            modalInstance.hide()
        }

        function astrasInformeEvolucion() {
            document.querySelector('#listadoInformeEvoluciones').style.display = 'initial'
            document.querySelector('#detalleInformeEvoluciones').style.display = 'none'
            document.querySelector('#atrasInf').style.display = 'none'
            document.querySelector('#verEvol').style.display = 'none'
            document.querySelector('#guardarInf').style.display = 'none'
            document.querySelector('#salirInfo').style.display = 'initial'
            document.getElementById('tituloInforme').innerHTML = 'Listado de informe'
        }

        function nuevoInforme() {
            document.querySelector('#listadoInformeEvoluciones').style.display = 'none'
            document.querySelector('#detalleInformeEvoluciones').style.display = 'initial'
            document.querySelector('#guardarInf').style.display = 'initial'
            document.querySelector('#verEvol').style.display = 'initial'
            document.querySelector('#atrasInf').style.display = 'initial'
            document.querySelector('#salirInfo').style.display = 'none'
            document.getElementById('tituloInforme').innerHTML = 'Crear nuevo informe'
            feather.replace();

            //limpiar los campos
            let formInformeEvolucion = document.getElementById('formInformeEvolucion')
            formInformeEvolucion.reset()

            document.getElementById("accInforme").value = "guardar"

            cargarinformacionHistoria()
        }

        function cargarinformacionHistoria() {
            let url = "{{ route('informes.buscaHistoriaPsicologicaInforme') }}"
            let idPaciente = document.getElementById('idPaciente').value

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
                    mapearHistoria(data.historia)
                    mapearInterconsulta(data.interconuslta)
                    loadNow(0)
                })
                .catch(error => console.error('Error:', error))
        }

        function mapearHistoria(historia) {
            document.getElementById("accInforme").value = "guardar"
            document.getElementById("idHistoria").value = historia.id

            document.getElementById('establecidoPrimeraVez').value = historia.diagnostico_primera_vez
            $('#establecidoPrimeraVez').trigger('change')

            CKEDITOR.instances['remision'].setData(historia.remision)
            CKEDITOR.instances['resumen_evaluacion_inicial'].setData(historia.eval_inicial)
            CKEDITOR.instances['resumen_evaluacion_inicial'].setData(historia.eval_inicial)
            document.getElementById('establecidoPrimeraVez').value = historia.diagnostico_primera_vez
            $('#establecidoPrimeraVez').trigger('change') //
            cargarImpresion(historia.codigo_diagnostico, 'codImpresionDiagnosticoConsulta')

            CKEDITOR.instances['sugerencia_consulta'].setData(historia.sugerencias_interconsultas)
            CKEDITOR.instances['observaciones_consulta'].setData(historia.observaciones_recomendaciones)

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

        function guardarInformeEvolucion() {
            for (var instanceName in CKEDITOR.instances) {
                CKEDITOR.instances[instanceName].updateElement()
            }

            //validar que el profesional esté seleccionado
            if (document.getElementById('profesionalInforme').value === '') {
                swal('Alerta', 'Debe seleccionar un profesional.', 'warning')
                return;
            }

            //validar que la fecha esté seleccionada
            if (document.getElementById('fechaEvolucion').value === '') {
                swal('Alerta', 'Debe seleccionar una fecha.', 'warning')
                return;
            }

            //validar hora
            if (document.getElementById('horaSeleccionada').value === '') {
                swal('Alerta', 'Debe seleccionar una hora.', 'warning')
                return;
            }
            
            //validar que el numero de sesiones esté seleccionado
            if (document.getElementById('numeroSesiones').value === '') {
                swal('Alerta', 'Debe seleccionar un numero de sesiones.', 'warning')
                return;
            }         
            
            const formInformeEvolucion = document.getElementById('formInformeEvolucion')
            const formData = new FormData(formInformeEvolucion)

            const url = "{{ route('form.guardarInformePsicologica') }}"
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
                        cargarInformes(1)
                        document.getElementById("listadoInformeEvoluciones").style.display = "initial"
                        document.getElementById("detalleInformeEvoluciones").style.display = "none"

                        document.getElementById("verEvol").style.display = "none"
                        document.getElementById("guardarInf").style.display = "none"
                    } else {
                        swal(data.title, data.message, data.success)
                    }
                })
                .catch(error => {
                    console.error("Error al enviar los datos:", error)
                })

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


        function generarPDF() {
            $("#loader-pdf").show();
            $("#titulo_loader_pdf").text("Generando PDF");
            let idInforme = document.getElementById('idInforme').value
            fetch("{{ route('informes.imprimirInformePsicologia') }}", {
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
                    return response.blob(); // Cambiar a blob
                })
                .then(blob => {
                    var a = document.createElement('a');
                    var url = window.URL.createObjectURL(blob);
                    a.href = url;
                    a.download = 'InformeEvolucion.pdf';
                    a.click();
                    window.URL.revokeObjectURL(url);
                    $("#loader-pdf").hide();
                })
                .catch(error => console.error('Error:', error));
        }

        function enviarInforme() {
        $("#loader-pdf").show();
        $("#titulo_loader_pdf").text("Enviando informe");
        fetch("{{ route('informes.enviarInforme') }}", {
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
    </script>

@endsection
