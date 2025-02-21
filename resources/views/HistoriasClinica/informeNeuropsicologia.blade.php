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
                                                role="tab">Anexos</a>
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
                                                    <label for="observaciones">Observaciones:</label>
                                                    <textarea class="form-control" id="observaciones" name="observaciones" rows="3"
                                                        placeholder="Ingrese las observaciones.."></textarea>
                                                </div>

                                            </div>
                                        </div>

                                        <!-- Evaluación y Diagnóstico -->
                                        <div class="tab-pane" id="evaluacion" role="tabpanel">
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
                                                                <input type="file" name="archivos[]"
                                                                    class="form-control"
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

            $("#formInformeEvolucion").validate({
                rules: {
                    fechaEvolucion: {
                        required: true
                    },
                    profesionalInforme: {
                        required: true
                    },
                    'archivos[]': {
                        required: function() {
                            // Verifica si al menos un archivo está seleccionado
                            let hasFile = false;
                            $("input[name='archivos[]']").each(function() {
                                if ($(this).val()) {
                                    hasFile = true;
                                    return false; // Sale del loop si encuentra al menos uno
                                }
                            });
                            return !hasFile; // Retorna verdadero si no hay ningún archivo seleccionado
                        }
                    }
                },
                messages: {
                    fechaEvolucion: {
                        required: "Por favor, selecciona la fecha."
                    },
                    profesionalInforme: {
                        required: "Por favor, selecciona el paciente responsable."
                    },
                    'archivos[]': {
                        required: "Por favor, selecciona al menos un archivo."
                    }
                },
                submitHandler: function(form) {
                    guardarInformeEvolucion();
                }
            });


            loadNow(1)
            cargar(1)
            cargarProfesionales()
            const ids = [
                'observaciones'
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

        })

        function imprimirInforme(idPaciente) {
            var modal = new bootstrap.Modal(document.getElementById("modalInformeEvoluciones"), {
                backdrop: 'static',
                keyboard: false
            });
            modal.show();
            document.getElementById('fileList').innerHTML = ""

            document.getElementById('idPaciente').value = idPaciente
            cargarInformes()
        }

        function cargar(page, searchTerm = '') {

            let url = "{{ route('informes.neuropsicologia') }}"; // Definir la URL

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

            let url = "{{ route('informes.informeNeuropsicologia') }}"; // Definir la URL

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

            agregarArchivo()
            document.getElementById('fileList').innerHTML = ""
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

                    CKEDITOR.instances['observaciones'].setData(datosInforme.informe.observacion)
                    if (datosInforme.anexos.length > 0) {
                        document.getElementById('listAnexos').style.display = 'initial'

                        let anexos = document.getElementById('anexosAdd')
                        anexos.innerHTML = ""
                        let listAnexos = ""

                        datosInforme.anexos.forEach(anexo => {
                            let parTipo = anexo.tipo_archivo.split('/')
                            listAnexos = `<div class="col-xl-6" id="anexo_${anexo.id}">
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
                                                                        <a  href="javascript:eliminarArchivo('${anexo.id}');" class="p-10 fs-18 link">
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
                    document.getElementById('tituloInforme').innerHTML = 'Editar informe'
                    document.querySelector('#listadoInformeEvoluciones').style.display = 'none'
                    document.querySelector('#detalleInformeEvoluciones').style.display = 'initial'
                    document.querySelector('#guardarInf').style.display = 'initial'
                    document.querySelector('#atrasInf').style.display = 'initial'
                    document.querySelector('#salirInfo').style.display = 'none'
                    feather.replace();
                })
                .catch(error => console.error('Error:', error))


        }

        function formatearTamano(kb) {
            if (kb < 1024) {
                return `${kb} KB`
            } else {
                const mb = kb / 1024
                return `${mb.toFixed(2)} MB`
            }
        }

        function eliminarArchivo(idAnexo){
            swal({
                title: "Esta seguro de eliminar este anexo?",
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
                    let url = "{{ route('informes.eliminarAnexoInforme') }}";
                    fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content')
                            },
                            body: JSON.stringify({
                                idAnexo: idAnexo
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                swal("¡Buen trabajo!",
                                    data.message,
                                    "success");
                                $("#anexo_"+idAnexo).remove();
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

        function nuevoInforme() {
            document.querySelector('#listadoInformeEvoluciones').style.display = 'none'
            document.querySelector('#detalleInformeEvoluciones').style.display = 'initial'
            document.querySelector('#guardarInf').style.display = 'initial'
            document.querySelector('#atrasInf').style.display = 'initial'
            document.querySelector('#salirInfo').style.display = 'none'
            document.getElementById('tituloInforme').innerHTML = 'Crear nuevo informe'
            document.getElementById('fileList').innerHTML = ""
            feather.replace();
            CKEDITOR.instances['observaciones'].setData("")
            document.getElementById("accInforme").value = "guardar"
            agregarArchivo()
        }

        function guardarInformeEvolucion() {
            if ($("#formInformeEvolucion").valid()) {
                for (var instanceName in CKEDITOR.instances) {
                    CKEDITOR.instances[instanceName].updateElement()
                }
                const formInformeEvolucion = document.getElementById('formInformeEvolucion')
                const formData = new FormData(formInformeEvolucion)

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
                        console.log(data)
                        if (data.success = 'success') {

                            swal(data.title, data.message, data.success)
                            cargarInformes(1)
                            document.getElementById("listadoInformeEvoluciones").style.display = "initial"
                            document.getElementById("detalleInformeEvoluciones").style.display = "none"

                            document.getElementById("guardarInf").style.display = "none"
                        } else {
                            swal(data.title, data.message, data.success)
                        }
                    })
                    .catch(error => {
                        console.error("Error al enviar los datos:", error)
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
