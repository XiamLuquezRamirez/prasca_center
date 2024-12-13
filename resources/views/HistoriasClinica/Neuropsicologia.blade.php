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

            cargarCategorias();
            
            const ids = [
                'enfermedadActual',
                'remision'
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
    </script>

@endsection