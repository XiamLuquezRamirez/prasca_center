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

            <div class="modal fade" id="modalPerfiles" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="tituloModal">Agregar perfil</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                        </div>
                        <div class="modal-body">
                            <form class="form" id="formPerfil">
                                <input type="hidden" name="accPerfil" value="guardar" id="accPerfil" />
                                <input type="hidden" name="idPerfil" value="" id="idPerfil" />

                                <div class="form-group">
                                    <label for="nombrePerfil">Nombre del Perfil</label>
                                    <input type="text" id="nombrePerfil" name="nombrePerfil" class="form-control"
                                        required>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label"><strong>Permisos disponibles </strong></label>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="form-group mb-3">
                                                <!-- Agenda -->
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="permisos_agenda"
                                                        name="permisos[]" value="agenda">
                                                    <label class="form-check-label" for="permisos_agenda">Agenda</label>
                                                </div>

                                                <!-- Pacientes -->
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="permisos_paciente"
                                                        name="permisos[]" value="paciente">
                                                    <label class="form-check-label"
                                                        for="permisos_paciente">Pacientes</label>
                                                </div>

                                                <!-- Historias Clínicas -->
                                                <div class="form-check">
                                                    <label class="form-check-label" for="permisoHistorias"><strong>Historias
                                                            clínicas</strong> </label>
                                                </div>
                                                <div class="form-check ms-3">
                                                    <input class="form-check-input" type="checkbox"
                                                        id="permisos_histPsicologia" name="permisos[]"
                                                        value="histPsicologia">
                                                    <label class="form-check-label"
                                                        for="permisos_histPsicologia">Psicológica</label>
                                                </div>
                                                <div class="form-check ms-3">
                                                    <input class="form-check-input" type="checkbox"
                                                        id="permisos_histNeuro" name="permisos[]" value="histNeuro">
                                                    <label class="form-check-label"
                                                        for="permisos_histNeuro">Neuropsicológica</label>
                                                </div>

                                                <!-- Administración -->
                                                <div class="form-check">
                                                    <label class="form-check-label"
                                                        for="permisoAdministracion"><strong>Administración</strong></label>
                                                </div>
                                                <div class="form-check ms-3">
                                                    <input class="form-check-input" type="checkbox"
                                                        id="permisos_adminProfesionales" name="permisos[]"
                                                        value="adminProfesionales">
                                                    <label class="form-check-label"
                                                        for="permisos_adminProfesionales">Profesionales</label>
                                                </div>
                                                <div class="form-check ms-3">
                                                    <input class="form-check-input" type="checkbox"
                                                        id="permisos_AdminMotivoConsulta" name="permisos[]"
                                                        value="AdminMotivoConsulta">
                                                    <label class="form-check-label"
                                                        for="permisos_AdminMotivoConsulta">Motivo de
                                                        consulta</label>
                                                </div>
                                                <div class="form-check ms-3">
                                                    <input class="form-check-input" type="checkbox"
                                                        id="permisos_Admineps" name="permisos[]"
                                                        value="Admineps">
                                                    <label class="form-check-label"
                                                        for="permisos_Admineps">Entidad promotora de salud</label>
                                                </div>
                                                <div class="form-check ms-3">
                                                    <input class="form-check-input" type="checkbox"
                                                        id="permisos_AdminPaquetes" name="permisos[]"
                                                        value="AdminPaquetes">
                                                    <label class="form-check-label"
                                                        for="permisos_AdminPaquetes">Paquetes de sesiones</label>
                                                </div>
                                                <div class="form-check ms-3">
                                                    <input class="form-check-input" type="checkbox"
                                                        id="permisos_AdminPruebas" name="permisos[]"
                                                        value="AdminPruebas">
                                                    <label class="form-check-label"
                                                        for="permisos_AdminPruebas">Pruebas</label>
                                                </div>
                                                <div class="form-check ms-3">
                                                    <input class="form-check-input" type="checkbox"
                                                        id="permisos_AdminSesiones" name="permisos[]"
                                                        value="AdminSesiones">
                                                    <label class="form-check-label"
                                                        for="permisos_AdminSesiones">Sesiones</label>
                                                </div>
                                                <div class="form-check ms-3">
                                                    <input class="form-check-input" type="checkbox"
                                                        id="permisos_AdminCUPS" name="permisos[]"
                                                        value="AdminCUPS">
                                                    <label class="form-check-label"
                                                        for="permisos_AdminCUPS">CUPS</label>
                                                </div>
                                                <div class="form-check ms-3">
                                                    <input class="form-check-input" type="checkbox"
                                                        id="permisos_AdminCIE10" name="permisos[]"
                                                        value="AdminCIE10">
                                                    <label class="form-check-label"
                                                        for="permisos_AdminCIE10">CIE10</label>
                                                </div>

                                                <!-- informes -->
                                                <div class="form-check">
                                                    <label class="form-check-label"
                                                        for="permisoAdministracion"><strong>Infomres</strong></label>
                                                </div>
                                                <div class="form-check ms-3">
                                                    <input class="form-check-input" type="checkbox"
                                                        id="permisos_informePsicologico" name="permisos[]"
                                                        value="informePsicologico">
                                                    <label class="form-check-label"
                                                        for="permisos_informePsicologico">Informe
                                                        psicológico</label>
                                                </div>
                                                <div class="form-check ms-3">
                                                    <input class="form-check-input" type="checkbox"
                                                        id="permisos_informeNeuro" name="permisos[]"
                                                        value="informeNeuro">
                                                    <label class="form-check-label" for="permisos_informeNeuro">Informe
                                                        neuropsicológico</label>
                                                </div>
                                                <div class="form-check ms-3">
                                                    <input class="form-check-input" type="checkbox"
                                                        id="permisos_reportes" name="permisos[]" value="reportes">
                                                    <label class="form-check-label" for="permisos_reportes">Reportes
                                                        estadisticos</label>
                                                </div>


                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <!-- Gestión de Recaudo -->
                                            <div class="form-check">
                                                <label class="form-check-label" for="permisoRecaudo"><strong>Gestión de
                                                        Recaudo</strong></label>
                                            </div>
                                            <div class="form-check  ms-3">
                                                <input class="form-check-input" type="checkbox"
                                                    id="permisos_gestionRecaudo" name="permisos[]"
                                                    value="gestionRecaudo">
                                                <label class="form-check-label"
                                                    for="permisos_gestionRecaudo">Recaudo</label>
                                            </div>
                                            <div class="form-check  ms-3">
                                                <input class="form-check-input" type="checkbox"
                                                    id="permisos_gestionGastos" name="permisos[]"
                                                    value="gestionGastos">
                                                <label class="form-check-label"
                                                    for="permisos_gestionGastos">Gastos</label>
                                            </div>
                                            <div class="form-check  ms-3">
                                                <input class="form-check-input" type="checkbox"
                                                    id="permisos_gestionCaja" name="permisos[]"
                                                    value="gestionCaja">
                                                <label class="form-check-label"
                                                    for="permisos_gestionCaja">Caja</label>
                                            </div>
                                            <!-- Gestión de Usuarios -->
                                            <div class="form-check">
                                                <label class="form-check-label" for="permisoUsuarios"><strong>Gestión de
                                                        Usuarios</strong></label>
                                            </div>
                                            <div class="form-check  ms-3">
                                                <input class="form-check-input" type="checkbox"
                                                    id="permisos_gestionUsuarios" name="permisos[]"
                                                    value="gestionUsuarios">
                                                <label class="form-check-label"
                                                    for="permisos_gestionUsuarios">Usuarios</label>
                                            </div>
                                            <div class="form-check  ms-3">
                                                <input class="form-check-input" type="checkbox"
                                                    id="permisos_gestionPerfiles" name="permisos[]"
                                                    value="gestionPerfiles">
                                                <label class="form-check-label"
                                                    for="permisos_gestionPerfiles">Perfiles</label>
                                            </div>
                                            <div class="form-check  ms-3">
                                                <input class="form-check-input" type="checkbox"
                                                    id="permisos_gestionLog" name="permisos[]"
                                                    value="gestionLog">
                                                <label class="form-check-label"
                                                    for="permisos_gestionLog">Historial de acciones</label>
                                            </div>

                                            <div class="form-check">
                                                <label class="form-check-label" for="permisoUsuarios"><strong>Otros permisos</strong></label>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        id="permisos_editarHistoria" name="permisos[]"
                                                        value="editarHistoria">
                                                    <label class="form-check-label"
                                                        for="permisos_editarHistoria">Editar y eliminar historias clinicas</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        id="permisos_editarEvoluciones" name="permisos[]"
                                                        value="editarEvoluciones">
                                                    <label class="form-check-label"
                                                        for="permisos_editarEvoluciones">Editar y eliminar evoluciones</label>
                                                </div>
                                            </div>
                                        </div>
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
            let menuS = document.getElementById("perfiles");

            menuP.classList.add("active", "menu-open");
            menuS.classList.add("active");

            loader = document.getElementById('loader')
            loadNow(1)

            $("#formPerfil").validate({
                rules: {
                    nombrePerfil: {
                        required: true
                    }
                },
                messages: {
                    nombrePerfil: {
                        required: "Por favor, ingresa el nombre del perfil."
                    }
                },
                submitHandler: function(form) {
                    guardar();
                }
            });


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
            let url = "{{ route('usuario.listaPerfiles') }}"

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

        function editarPerfil(idPerfil) {
            var modal = new bootstrap.Modal(document.getElementById("modalPerfiles"), {
                backdrop: 'static',
                keyboard: false
            })

            modal.show()

            document.getElementById('btn-guardar').removeAttribute('disabled', 'disabled')
            document.getElementById('btn-nuevo').style.display = 'none'
            document.getElementById('btn-cancel').style.display = 'initial'


            document.getElementById("idPerfil").value = idPerfil
            document.getElementById("accPerfil").value = "editar"

            document.getElementById("tituloModal").innerHTML = "Editar perfil"

            let url = "{{ route('usuario.buscaPerfil') }}"

            fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        idPerfil: idPerfil
                    })
                })
                .then(response => response.json())
                .then(data => {
                    document.getElementById("nombrePerfil").value = data.nombre
                    data.permisos.forEach(permiso => {

                        document.getElementById(`permisos_${permiso.permiso}`).checked = true
                    })
                })
                .catch(error => console.error('Error:', error))

        }

        function eliminarPerfil(idPerfil) {
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
                    let url = "{{ route('usuario.eliminarPerfil') }}"
                    fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content')
                            },
                            body: JSON.stringify({
                                idPerfil: idPerfil
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                swal("¡Buen trabajo!",
                                    data.message,
                                    "success")
                                cargarListaPerfiles()
                            } else {
                                if (data.resp === 'relacionado') {
                                    swal("¡Alerta!",
                                        data.message,
                                        "warning")
                                } else {
                                    swal("¡Alerta!",
                                        data.message,
                                        "warning")
                                }
                            }
                        })

                } else {
                    swal("Cancelado", "Tu registro esta salvo :)", "error")
                }
            })
        }

        function openModalUsuario() {
            var modal = new bootstrap.Modal(document.getElementById("modalPerfiles"), {
                backdrop: 'static',
                keyboard: false
            })

            nuevo()
            modal.show()
        }

        function cancelar() {
            const formPerfil = document.getElementById('formPerfil')
            formPerfil.reset()
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
            document.getElementById("accPerfil").value = "guardar"
            document.getElementById("tituloModal").innerHTML = "Agregar perfil"
        }

        function guardar() {
            // Verificar que el formulario es válido
            if ($("#formPerfil").valid()) {
                const formPerfil = document.getElementById('formPerfil');

                // Crear un nuevo objeto FormData y agregar los permisos seleccionados
                const formData = new FormData(formPerfil);

                // Obtener los permisos seleccionados   
                const permisos = [];
                document.querySelectorAll('input[name="permisos[]"]:checked').forEach(checkbox => {
                    permisos.push(checkbox.value);
                });

                if (permisos.length === 0) {
                    swal("¡Alerta!", "Debe seleccionar al menos un permiso", "warning");
                    return;
                }

                // Añadir los permisos al objeto FormData
                formData.append('permisos', JSON.stringify(permisos));

                // URL de la ruta en Laravel para guardar el usuario
                const url = "{{ route('form.guardarPerfil') }}";

                // Enviar los datos a Laravel usando fetch
                fetch(url, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content') // Token CSRF
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data) {
                            swal("¡Buen trabajo!", "La operación fue realizada exitosamente", "success");
                            document.getElementById('btn-guardar').setAttribute('disabled', 'disabled');
                            document.getElementById('btn-nuevo').style.display = 'initial';
                            document.getElementById('btn-cancel').style.display = 'none';

                            cargarListaPerfiles(1);
                            document.getElementById("accPErfil").value = "guardar";
                        } else {
                            console.error('Error en el procesamiento:', data.message);
                        }
                    })
                    .catch(error => {
                        console.error("Error al enviar los datos:", error);
                    });
            }
        }
    </script>

@endsection
