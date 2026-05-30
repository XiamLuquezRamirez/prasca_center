@extends('Plantilla.Principal')
@section('title', 'Gestionar Componentes de la historia')
@section('Contenido')
    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h4 class="page-title">Gestionar Componentes de la historia</h4>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#"><i class="mdi mdi-home-outline"></i></a></li>
                            <li class="breadcrumb-item" aria-current="page">Inicio</li>
                            <li class="breadcrumb-item active" aria-current="page">Gestionar Componentes de la historia</li>
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
                        <h5 class="card-title">Listado de Componentes de la historia </h5>
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
                                        class="btn btn-xs btn-primary font-bold"><i class="fa fa-plus"></i> Nuevo componente</button>
                                </div>

                            </div>
                        </div>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th style="width:45%;">Categoria</th>
                                    <th style="width:45%;">Componente </th>
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
    <!-- MODAL PAUETES -->
    <div class="modal fade" id="modalComponente" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 40%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="tituloAccion">Agregar componente</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                    <form id="formComponentes">
                        <input type="hidden" name="accRegistro" id="accRegistro" value="guardar" />
                        <input type="hidden" name="idRegistro" id="idRegistro" value="" />
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="categoria" class="form-label">Categoria :</label>
                                    <select class="form-control" id="categoria" name="categoria">
                                        <option value="">Seleccione una categoría</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="valor" class="form-label">Componente:</label>
                                    <input type="text" class="form-control" id="componente" name="componente">
                                </div>
                            </div>                         

                            <div class="box-footer text-end">
                                <button type="button" onclick="nuevoRegistro(2);" style="display: none;" id="newRegistro"
                                    class="btn btn-primary-light me-1">
                                    <i class="ti-plus "></i> Nuevo
                                </button>
                                <button type="button" id="cancelRegistro" onclick="cancelarRegistro();"
                                    class="btn btn-primary-light me-1">
                                    <i class="ti-close"></i> Cancelar
                                </button>
                                <button type="button" id="saveRegistro" onclick="guardarRegistro();"
                                    class="btn btn-primary">
                                    <i class="ti-save"></i> Guardar
                                </button>
                            </div>

                        </div>
                    </form>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
    </div><!-- /.modal -->

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let menuP = document.getElementById("principalParametros")
            let menuS = document.getElementById("principalParametrosComponentes")

            menuP.classList.add("active", "menu-open")
            menuS.classList.add("active", "menu-open")

            loader = document.getElementById('loader')
            loadNow(1)

            $("#formComponentes").validate({
                rules: {

                    categoria: {
                        required: true,                       
                    },
                    componente: {
                        required: true
                    }
                },
                messages: {
                    categoria: {
                        required: "Por favor, ingrese la categoría del componente."
                    },
                    componente: {
                        required: "Por favor, ingrese el componente.",
                    }
                },
                submitHandler: function(form) {
                    guardarRegistro()
                }
            });

            cargar(1);
            cargarCategorias();

            // Evento click para la paginación
            document.addEventListener('click', function(event) {
                if (event.target.matches('#paginacion a')) {
                    event.preventDefault()
                    var href = event.target.getAttribute('href')
                    var page = href.split('page=')[1]
                    var search = document.getElementById('busqueda').value
                        
                    // Asegurarse de que 'page' sea un número antes de hacer la solicitud
                    if (!isNaN(page)) {
                        cargar(page, search)
                    }
                }
            });
            // Evento input para el campo de búsqueda
            document.getElementById('busqueda').addEventListener('input', function() {
                var searchTerm = this.value
                cargar(1,
                    searchTerm) // Cargar la primera página con el término de búsqueda
            });

        });

        function cargarCategorias() {
            let url = "{{ route('componentes.listaCategoriasSelect') }}";
            fetch(url, {
                method: 'GET'
            })
            .then(response => response.json())
            .then(data => {
                console.log(data);
                var select = document.getElementById('categoria');
                select.innerHTML = '<option value="">Seleccione una categoría</option>';
                data.forEach(item => {
                    var option = document.createElement('option');
                    option.value = item.id;
                    option.textContent = item.nombre;
                    select.appendChild(option);
                });
            })
            .catch(error => console.error('Error:', error));
        }

        function guardarRegistro() {

            if ($("#formComponentes").valid()) {

                const formComponentes = document.getElementById('formComponentes');
                const formData = new FormData(formComponentes);

                const url = "{{ route('form.guardarComponente') }}";

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
                        if (data.success) {

                            swal("¡Buen trabajo!", "La operación fue realizada exitosamente.", "success")

                            document.getElementById('saveRegistro').setAttribute('disabled', 'disabled')
                            document.getElementById('newRegistro').style.display = 'initial'
                            document.getElementById('cancelRegistro').style.display = 'none'

                            cargar(1)
                            document.getElementById("accRegistro").value = "guardar"

                        } else {
                            console.error('Error en el procesamiento:', data.message)
                        }
                    })
                    .catch(error => {
                        console.error("Error al enviar los datos:", error)
                    });

            }
        }

        function editarRegistro(idRegistro) {
            var modal = new bootstrap.Modal(document.getElementById("modalComponente"), {
                backdrop: 'static',
                keyboard: false
            });
            document.getElementById("accRegistro").value = 'editar'
            document.getElementById("idRegistro").value = idRegistro
            document.getElementById('saveRegistro').removeAttribute('disabled')

            document.getElementById("tituloAccion").innerHTML  = "Editar componente"

            modal.show();

            let url = "{{ route('componentes.buscarComponente') }}";

            fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        idRegistro: idRegistro
                    })
                })
                .then(response => response.json())
                .then(data => {
                    document.getElementById("categoria").value = data.categoria_id
                    document.getElementById("componente").value = data.opcion

                })
                .catch(error => console.error('Error:', error));

        }

        function cancelarRegistro() {
            const formComponentes = document.getElementById('formComponentes')
            formComponentes.reset();
        }

        
        
        function nuevoRegistro(opc) {

            if (opc == 1) {
                var modal = new bootstrap.Modal(document.getElementById("modalComponente"), {
                    backdrop: 'static',
                    keyboard: false
                });
                modal.show()
            }

            cancelarRegistro();
            document.getElementById('saveRegistro').removeAttribute('disabled')
            document.getElementById('newRegistro').style.display = 'none'
            document.getElementById('cancelRegistro').style.display = 'initial'
            document.getElementById("accRegistro").value = "guardar"

            
            document.getElementById("tituloAccion").innerHTML  = "Agregar componente"
        }

        function eliminarRegistro(idReg) {
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
                    let url = "{{ route('componentes.eliminarComponente') }}";
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

        function cargar(page, searchTerm = '') {


            let url = "{{ route('componentes.listaComponentes') }}"; // Definir la URL

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
                    document.getElementById('trRegistros').innerHTML = responseData.componentes;
                    feather.replace();
                    // Colocar los enlaces de paginación
                    document.getElementById('pagination-links').innerHTML = responseData.links;
                    loadNow(0);
                })
                .catch(error => console.error('Error:', error));

        }
    </script>

@endsection
