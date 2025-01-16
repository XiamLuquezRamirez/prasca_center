@extends('Plantilla.Principal')
@section('title', 'Gestionar perfiles')
@section('Contenido')

    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h4 class="page-title">Gestionar perfiles</h4>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#"><i class="mdi mdi-home-outline"></i></a></li>
                            <li class="breadcrumb-item" aria-current="page">Inicio</li>
                            <li class="breadcrumb-item active" aria-current="page">Gestionar perfiles</li>
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
                        <h5 class="card-title">Listado de perfiles</h5>
                    </div>
                    <div class="card-body">
                        <div class="box-controls pull-right">
                            <div class="box-header-actions">
                                <div class="input-group input-group-merge">
                                    <input type="text" id="busqueda" class="form-control">
                                    <div class="input-group-text" data-password="false">
                                        <span class="fa fa-search"></span>
                                    </div>
                                    <button type="button" onclick="openModalUsuario();" class="btn btn-xs btn-primary"><i
                                            class="fa fa-plus"></i> Nuevo
                                        registro</button>
                                </div>

                            </div>
                        </div>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th style="width:80%;">Nombre</th>
                                    <th style="width:20%;">Acción</th>
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

            <div class="modal fade" id="modalUsuario" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="tituloModal">Agregar perfil</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                        </div>
                        <div class="modal-body">
                            <form class="form" id="formUsuario">
                                <input type="hidden" name="accPerfil" value="guardar" id="accPerfil" />
                                <input type="hidden" name="idPerfil" value="" id="idPerfil" />

                                <div class="form-group">
                                    <label for="nombrePerfil">Nombre del Perfil</label>
                                    <input type="text" id="nombrePerfil" name="nombrePerfil" class="form-control"
                                        required>
                                </div>
                                <div class="form-group">
                                    <label>Permisos disponibles</label>
                                    <div class="checkbox-list">
                                        <label><input type="checkbox" name="permisos[]" value="1"> Agenda</label>
                                        <label><input type="checkbox" name="permisos[]" value="2"> Pacientes</label>
                                        <label><input type="checkbox" name="permisos[]" value="3"> Historias
                                            clínicas</label>
                                        <label><input type="checkbox" name="permisos[]" value="4"> Informes</label>
                                        <label><input type="checkbox" name="permisos[]" value="5">
                                            Administración</label>
                                        <label><input type="checkbox" name="permisos[]" value="6"> Gestionar
                                            usuarios</label>
                                    </div>
                                </div>

                                <!-- /.box-body -->
                                <div class="box-footer text-end">
                                    <button type="button" onclick="nuevo()" style="display: none" id="btn-nuevo"
                                        class="btn btn-primary-light me-1">
                                        <i class="ti-plus "></i> Nuevo
                                    </button>
                                    <button type="button" id="btn-cancel" onclick="cancelar()"
                                        class="btn btn-primary-light me-1">
                                        <i class="ti-close"></i> Cancelar
                                    </button>
                                    <button type="button" id="btn-guardar" onclick="guardar()" class="btn btn-primary">
                                        <i class="ti-save"></i> Guardar
                                    </button>
                                </div>
                            </form>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->
            </div><!-- /.modal -->
        </div>
    </section>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let menuP = document.getElementById("principalUsuarios");
            let menuS = document.getElementById("usuarios");

            menuP.classList.add("active", "menu-open");
            menuS.classList.add("active");

            loader = document.getElementById('loader')
            loadNow(1)


            cargarListaPerfiles(1)

            // Evento click para la paginación
            document.addEventListener('click', function(event) {
                if (event.target.matches('.pagination a')) {
                    event.preventDefault();
                    var href = event.target.getAttribute('href');
                    var page = href.split('page=')[1];

                    // Asegurarse de que 'page' sea un número antes de hacer la solicitud
                    if (!isNaN(page)) {
                        cargarListaPerfiles(page);
                    }
                }
            });
            // Evento input para el campo de búsqueda
            document.getElementById('busqueda').addEventListener('input', function() {
                var searchTerm = this.value;
                cargarListaPerfiles(1,
                    searchTerm);
            });

        })

        function cargarListaPerfiles(page, searchTerm = '') {
            let url = "{{ route('usuarios.listaPerfiles') }}"

            var oldPageInput = document.getElementById('page')
            var oldSearchTermInput = document.getElementById('searchTerm')
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
                    document.getElementById('trRegistros').innerHTML = responseData.perfiles
                    feather.replace()
                    document.getElementById('pagination-links').innerHTML = responseData.links
                    loadNow(0)
                })
                .catch(error => console.error('Error:', error))

        }

        function editarUsuario(idUsuario) {
            var modal = new bootstrap.Modal(document.getElementById("modalUsuario"), {
                backdrop: 'static',
                keyboard: false
            })

            modal.show()

            document.getElementById('btn-guardar').removeAttribute('disabled', 'disabled')
            document.getElementById('btn-nuevo').style.display = 'none'
            document.getElementById('btn-cancel').style.display = 'initial'


            document.getElementById("idUsuario").value = idUsuario
            document.getElementById("accUsuario").value = "editar"
            document.getElementById("div-cambioPasw").style.display = ''
            document.getElementById('pasw').setAttribute('disabled', 'disabled')
            document.getElementById('confPasw').setAttribute('disabled', 'disabled')
            document.getElementById('cambioPasw').checked = false

            document.getElementById("tituloModal").innerHTML = "Editar usuario"

            let url = "{{ route('usuario.buscaUsuario') }}"

            fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        idUsuario: idUsuario
                    })
                })
                .then(response => response.json())
                .then(data => {
                    document.getElementById("nombre").value = data.nombre_usuario
                    document.getElementById("tipo").value = data.tipo_usuario
                    data.tipo_usuario == "Profesional" ?
                        document.getElementById('tipo').setAttribute('disabled', 'disabled') :
                        document.getElementById('tipo').removeAttribute('disabled', 'disabled')

                    document.getElementById("estado_usuario").value = data.estado_usuario
                    document.getElementById("usuario").value = data.login_usuario
                    document.getElementById("usuarioOriginal").value = data.login_usuario
                    document.getElementById("email").value = data.email_usuario
                })
                .catch(error => console.error('Error:', error))

        }

        function eliminarUsuario(idUsuario) {
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
                    let url = "{{ route('usuario.eliminarUsuario') }}"
                    fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content')
                            },
                            body: JSON.stringify({
                                idUsuario: idUsuario
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                swal("¡Buen trabajo!",
                                    data.message,
                                    "success")
                                cargarListaUsuarios()
                            } else {
                                swal("¡Alerta!",
                                    "La operación fue realizada exitosamente",
                                    data.message,
                                    "success")
                            }
                        })

                } else {
                    swal("Cancelado", "Tu registro esta salvo :)", "error")
                }
            })
        }

        function openModalUsuario() {
            var modal = new bootstrap.Modal(document.getElementById("modalUsuario"), {
                backdrop: 'static',
                keyboard: false
            })


            nuevo()
            modal.show()
        }

        function cancelar() {
            const formUsuario = document.getElementById('formUsuario')
            formUsuario.reset()
        }

        function habilitarPasw() {
            const pasw = document.getElementById("pasw")

            if (pasw.disabled) {
                document.getElementById('pasw').removeAttribute('disabled', 'disabled')
                document.getElementById('confPasw').removeAttribute('disabled', 'disabled')
            } else {
                document.getElementById('pasw').setAttribute('disabled', 'disabled')
                document.getElementById('confPasw').setAttribute('disabled', 'disabled')
            }
        }

        function nuevo() {
            cancelar()
            document.getElementById('btn-guardar').removeAttribute('disabled')
            document.getElementById('btn-nuevo').style.display = 'none'
            document.getElementById('btn-cancel').style.display = 'initial'
            document.getElementById("accUsuario").value = "guardar"
            document.getElementById("div-cambioPasw").style.display = 'none'
            document.getElementById('pasw').removeAttribute('disabled', 'disabled')
            document.getElementById('confPasw').removeAttribute('disabled', 'disabled')
            document.getElementById("tituloModal").innerHTML = "Agregar usuario"
            document.getElementById('tipo').removeAttribute('disabled', 'disabled')

        }

        function guardar() {

            if ($("#formUsuario").valid()) {

                const formUsuario = document.getElementById('formUsuario')
                const formData = new FormData(formUsuario)

                const url = "{{ route('form.guardarUsusario') }}"

                fetch(url, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {

                        if (data.success) {

                            swal("¡Buen trabajo!", "La operación fue realizada exitosamente", "success")

                            document.getElementById('btn-guardar').setAttribute('disabled', 'disabled')
                            document.getElementById('btn-nuevo').style.display = 'initial'
                            document.getElementById('btn-cancel').style.display = 'none'

                            cargarListaUsuarios(1)
                            document.getElementById("accUsuario").value = "guardar"

                        } else {
                            console.error('Error en el procesamiento:', data.message)
                        }
                    })
                    .catch(error => {
                        console.error("Error al enviar los datos:", error)
                    })

            }
        }
    </script>

@endsection
