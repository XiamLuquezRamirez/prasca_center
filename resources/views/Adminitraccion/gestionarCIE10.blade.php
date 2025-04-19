@extends('Plantilla.Principal')
@section('title', 'Gestionar CIE10')
@section('Contenido')
    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h4 class="page-title">Gestionar CIE10 - DSM5 </h4>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#"><i class="mdi mdi-home-outline"></i></a></li>
                            <li class="breadcrumb-item" aria-current="page">Inicio</li>
                            <li class="breadcrumb-item active" aria-current="page">Gestionar CIE10 - DSM5</li>
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
                        <h5 class="card-title">Listado de CIE10 - DSM5 </h5>
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
                                        CIE10 - DSM5</button>
                                </div>

                            </div>
                        </div>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th style="width:10%;">Código</th>
                                    <th style="width:60%;">Nombre</th>
                                    <th style="width:10%;">Clasificación</th>
                                    <th style="width:10%;">Estado</th>
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
    <!-- MODAL ENTIDADES -->
    <div class="modal fade" id="modalCIE10" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 40%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="tituloAccion">Agregar CIE10 - DSM5</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                    <form id="formCIE10">
                        <input type="hidden" name="accRegistro" id="accRegistro" value="guardar" />
                        <input type="hidden" name="idRegistro" id="idRegistro" value="" />
                        <input type="hidden" name="codigoOriginal" id="codigoOriginal" value="" />
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="codigo" class="form-label">Código :</label>
                                    <input type="text" class="form-control" id="codigo" name="codigo">
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label for="nombre" class="form-label">Nombre :</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="descripcion" class="form-label">Descripción :</label>
                                    <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="clasificacion" class="form-label">Tipo de clasificacíon :</label>
                                    <select class="form-control" id="clasificacion" name="clasificacion">
                                            <option value="CIE10">CIE10</option>
                                            <option value="DSM5">DSM5</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="habilitado" class="form-label">Habilitado :</label>
                                    <select class="form-control" id="habilitado" name="habilitado">
                                        <option value="SI">SI</option>
                                        <option value="NO">NO</option>
                                    </select>
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
            let menuP = document.getElementById("principalParametros");
            let menuS = document.getElementById("principalParametrosCIE10");

            menuP.classList.add("active", "menu-open");
            menuS.classList.add("active");

            loader = document.getElementById('loader');
            loadNow(1);

            $("#formCIE10").validate({
                rules: {

                    codigo: {
                        required: true,
                        remote: {
                            url: "/verificar-codigo-cie10", // URL para verificar
                            type: "post",
                            data: {
                                codigo: function() {
                                    return $("#codigo").val()
                                },
                                idRegistro: function() {
                                    return $("#idRegistro").val()
                                },
                                _token: function() {
                                    return "{{ csrf_token() }}" // Token CSRF para seguridad
                                }
                            }
                        },
                    },
                    nombre: {
                        required: true
                    },
                    clasificacion: {
                        required: true
                    }
                },
                messages: {
                    nombre: {
                        required: "Por favor, ingrese el nombre de la CIE10 o DSM5."
                    },
                    codigo: {
                        required: "Por favor, ingrese el código de la CIE10 o DSM5.",
                        remote: "Esta código ya está registrado."
                    },
                    clasificacion: {
                        required: "Por favor, seleccione el tipo de clasificación."
                    }
                },
                submitHandler: function(form) {
                    guardarRegistro();
                }
            });

            cargar(1);

            // Evento click para la paginación
            document.addEventListener('click', function(event) {
                if (event.target.matches('.pagination a')) {
                    event.preventDefault();
                    var href = event.target.getAttribute('href');
                    var page = href.split('page=')[1];

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

        function guardarRegistro() {

            if ($("#formCIE10").valid()) {

                const formCIE10 = document.getElementById('formCIE10');
                const formData = new FormData(formCIE10);

                const url = "{{ route('form.guardarCIE10') }}";

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
                            console.error('Error en el procesamiento:', data.message);
                        }
                    })
                    .catch(error => {
                        console.error("Error al enviar los datos:", error);
                    });

            }
        }

        function editarRegistro(idRegistro) {
            var modal = new bootstrap.Modal(document.getElementById("modalCIE10" ), {
                backdrop: 'static',
                keyboard: false
            });
            document.getElementById("accRegistro").value = 'editar'
            document.getElementById("idRegistro").value = idRegistro
            document.getElementById('saveRegistro').removeAttribute('disabled')

            document.getElementById("tituloAccion").innerHTML  = "Editar CIE10"

            modal.show();

            let url = "{{ route('cie10.buscaCIE10') }}";

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
                    document.getElementById("nombre").value = data.nombre
                    document.getElementById("codigo").value = data.codigo
                    document.getElementById("descripcion").value = data.descripcion
                    document.getElementById("habilitado").value = data.habilitado
                    document.getElementById("clasificacion").value = data.clasificacion
                })
                .catch(error => console.error('Error:', error));

        }

        function cancelarRegistro() {
            const formCIE10 = document.getElementById('formCIE10');
            formCIE10.reset();
        }

        function nuevoRegistro(opc) {

            if (opc == 1) {
                var modal = new bootstrap.Modal(document.getElementById("modalCIE10"), {
                    backdrop: 'static',
                    keyboard: false
                });
                modal.show();
            }

            cancelarRegistro();
            document.getElementById('saveRegistro').removeAttribute('disabled')
            document.getElementById('newRegistro').style.display = 'none'
            document.getElementById('cancelRegistro').style.display = 'initial'
            document.getElementById("accRegistro").value = "guardar"

            
            document.getElementById("tituloAccion").innerHTML  = "Agregar CUPS"

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
                    let url = "{{ route('cie10.eliminarCIE10') }}";
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

            let url = "{{ route('cie10.listaCIE10') }}"; // Definir la URL

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
                    document.getElementById('trRegistros').innerHTML = responseData.cie10;
                    feather.replace();
                    // Colocar los enlaces de paginación
                    document.getElementById('pagination-links').innerHTML = responseData.links;
                    loadNow(0);
                })
                .catch(error => console.error('Error:', error));

        }
    </script>

@endsection
