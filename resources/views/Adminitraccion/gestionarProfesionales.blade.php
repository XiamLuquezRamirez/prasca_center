@extends('Plantilla.Principal')
@section('title', 'Gestionar profesionales')
@section('Contenido')
    <input type="hidden" id="Ruta" data-ruta="{{ asset('/app-assets/') }}" />
    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h4 class="page-title">Gestionar profesionales</h4>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#"><i class="mdi mdi-home-outline"></i></a></li>
                            <li class="breadcrumb-item" aria-current="page">Inicio</li>
                            <li class="breadcrumb-item active" aria-current="page">Gestionar profesionales</li>
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
                        <h5 class="card-title">Listado de profesionales</h5>
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
                                        profesional</button>
                                </div>
                            </div>
                        </div>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th style="width:5%;">#</th>
                                    <th style="width:10%;">Identificación</th>
                                    <th style="width:50%;">Nombre</th>
                                    <th style="width:25%;">Email</th>
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
    <!-- MODAL PROFESIONAL -->
    <div class="modal fade" id="modalProfesional" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 40%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">Agregar profesional</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                    <form id="formProfesional">
                        <input type="hidden" name="accRegistro" id="accRegistro" value="guardar" />
                        <input type="hidden" name="idRegistro" id="idRegistro" value="" />
                        <input type="hidden" name="usuarioOriginal" id="usuarioOriginal" value="" />
                        <input type="hidden" name="identOriginal" id="identOriginal" value="" />
                        <input type="hidden" name="firmaOriginal" id="firmaOriginal" value="" />

                        <h5 class="mb-4 text-uppercase"><i class="mdi mdi-account-circle me-1"></i> Información personal
                        </h5>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="identificacion" class="form-label">Identificación :</label>
                                    <input type="text" class="form-control" id="identificacion" name="identificacion">
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="nombre" class="form-label">Nombre :</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="registroProf" class="form-label">Registro profesional :</label>
                                    <input type="text" class="form-control" id="registroProf" name="registroProf">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email" class="form-label">Email :</label>
                                    <input type="text" class="form-control" id="email" name="email">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="telefono" class="form-label">Teléfono :</label>
                                    <input type="text" class="form-control" id="telefono" name="telefono">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="observaciones" class="form-label">Observaciones :</label>
                                    <textarea class="form-control" id="observaciones" name="observaciones" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <input type="hidden" name="firmaCargada" id="firmaCargada" value="">
                                <div class="form-group">
                                    <label for="observaciones" class="form-label">Firma del profesional :</label><br />
                                    <input type="file" id="firmaProf" name="firmaProf">
                                    <div class="btn-group" id="verFirma" style="display: none;">
                                        <button type="button" onclick="verFima()"
                                            class="waves-effect waves-light btn btn-info btn-xs">Ver firma</button>
                                        <button type="button" onclick="cambiarFirma()"
                                            class="waves-effect waves-light btn btn-success btn-xs">Cambiar Firma</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h5 class="mb-4 text-uppercase"><i class="mdi mdi-account-key me-1"></i> Información del usuario
                        </h5>
                        <div class="row">
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="usuario">Usuario :</label>
                                    <input type="text" class="form-control" id="usuario" name="usuario"
                                        placeholder="" value="">
                                </div>
                            </div>

                            <div id="div-cambioPasw" style="" class="col-2">
                                <div class="form-group">
                                    <label for="cambioPasw" class="form-label">Actualizar contraseña :</label>

                                    <label class="switch switch-border switch-primary">
                                        <input type="checkbox" onchange="habilitarPasw()" id="cambioPasw"
                                            name="cambioPasw">
                                        <span class="switch-indicator"></span>
                                        <span class="switch-description"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                    <label for="pasword">Contraseña :</label>
                                    <input type="password" class="form-control" id="pasword" name="pasword"
                                        placeholder="" value="">
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                    <label for="rpasword">Repetir contraseña :</label>
                                    <input type="password" class="form-control" id="rpasword" name="rpasword"
                                        placeholder="" value="">
                                </div>
                            </div>
                            <div class="col-md-6 ">
                                <div class="form-group">
                                    <label for="tipo">Tipo de usuario :</label>
                                    <select class="form-control" id="tipo" name="tipo" aria-invalid="false">
                                      
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 ">
                                <div class="form-group">
                                    <label for="estado">Estado de la cuenta :</label>
                                    <select class="form-control" id="estado" name="estado" aria-invalid="false">
                                        <option value="Habilitada">
                                            Habilitada </option>
                                        <option value="Deshabilitada">
                                            Deshabilitada </option>
                                    </select>
                                </div>
                            </div>
                            <div class="box-footer text-end">
                                <button type="button" onclick="nuevoRegistro(2);" style="display: none;"
                                    id="newRegistro" class="btn btn-primary-light me-1">
                                    <i class="ti-plus"></i> Nuevo
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


    <div class="modal fade" id="modalFirmaProfesional" tabindex="-1" role="dialog"
        aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 40%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">Firma del profesional</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                    <div style="display: flex; justify-content: center;">
                        <img width="200" src="" id="imgfirma" />
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let menuP = document.getElementById("principalParametros");
            let menuS = document.getElementById("principalParametrosProfesionales");

            menuP.classList.add("active", "menu-open");
            menuS.classList.add("active");

            loader = document.getElementById('loader');
            loadNow(1);

            $("#formProfesional").validate({
                rules: {
                    identificacion: {
                        required: true,
                        remote: {
                            url: "/verificar-identificacion-profesional",
                            type: "post",
                            data: {
                                identificacion: function() {
                                    return $("#identificacion").val();
                                },
                                identOriginal: function() {
                                    return $("#identOriginal")
                                        .val(); // Valor original de identificación
                                },
                                _token: function() {
                                    return "{{ csrf_token() }}"; // Token CSRF
                                }
                            },
                            beforeSend: function(xhr, settings) {
                                // Cancelar la validación si la identificación no cambió
                                if ($("#identificacion").val() === $("#identOriginal").val()) {
                                    xhr.abort();
                                }
                            }
                        }
                    },
                    usuario: {
                        required: true,
                        remote: {
                            url: "/verificar-usuario",
                            type: "post",
                            data: {
                                usuario: function() {
                                    return $("#usuario").val();
                                },
                                usuarioOriginal: function() {
                                    return $("#usuarioOriginal").val(); // Usuario original
                                },
                                _token: function() {
                                    return "{{ csrf_token() }}";
                                }
                            },
                            beforeSend: function(xhr, settings) {
                                // Cancelar la validación si el usuario no cambió
                                if ($("#usuario").val() === $("#usuarioOriginal").val()) {
                                    xhr.abort();
                                }
                            }
                        }
                    },
                    pasword: {
                        required: true,
                        minlength: 6
                    },
                    rpasword: {
                        required: true,
                        equalTo: "#pasword"
                    }
                },
                messages: {
                    identificacion: {
                        required: "Por favor, ingresa una identificación.",
                        remote: "Esta identificación ya está registrada."
                    },
                    nombre: {
                        required: "Por favor, ingresa el nombre del profesional."
                    },
                    email: {
                        required: "Por favor, ingresa un email.",
                        email: "Por favor, ingresa un email válido."
                    },
                    usuario: {
                        required: "Por favor, ingresa el nombre de usuario.",
                        remote: "Este nombre de usuario ya está registrado. Por favor, elige otro."
                    },
                    pasword: {
                        required: "Por favor, ingresa una contraseña.",
                        minlength: "La contraseña debe tener al menos 6 caracteres."
                    },
                    rpasword: {
                        required: "Por favor, confirma la contraseña.",
                        equalTo: "Las contraseñas no coinciden."
                    }
                },
                submitHandler: function(form) {
                    guardar();
                }
            });


            cargar(1); 
            cargarPerfiles()

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

        function cargarPerfiles(){
            return new Promise((resolve, reject) => {
                let select = document.getElementById("tipo")
                select.innerHTML = ""
                let url = "{{ route('usuario.buscaListPerfiles') }}"

                let defaultOption = document.createElement("option")
                defaultOption.value = "" // Valor en blanco
                defaultOption.text = "Selecciona una opción" // Texto que se mostrará
                select.appendChild(defaultOption)

                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                       
                        data.forEach(perfil => {
                            let option = document.createElement("option")
                            option.value = perfil.id
                            option.text = perfil.nombre
                            select.appendChild(option)
                        })
                        resolve() // Resuelve la promesa cuando los datos han sido cargados
                    })
                    .catch(error => {
                        console.error('Error:', error)
                        reject(error) // Rechaza la promesa si ocurre un error
                    })
            }) 
        }

        function guardarRegistro() {

            if ($("#formProfesional").valid()) {

                const formProfesional = document.getElementById('formProfesional');
                const formData = new FormData(formProfesional);

                const url = "{{ route('form.guardarProfesional') }}";

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
            var modal = new bootstrap.Modal(document.getElementById("modalProfesional"), {
                backdrop: 'static',
                keyboard: false
            });
            cancelarRegistro();

            document.getElementById("accRegistro").value = 'editar'
            document.getElementById("idRegistro").value = idRegistro
            document.getElementById("div-cambioPasw").style.display = ''
            document.getElementById('pasword').setAttribute('disabled', 'disabled')
            document.getElementById('rpasword').setAttribute('disabled', 'disabled')
            document.getElementById('cambioPasw').checked = false
            document.getElementById('saveRegistro').removeAttribute('disabled')
            document.getElementById('newRegistro').style.display = 'none'
            document.getElementById('cancelRegistro').style.display = 'initial'

            modal.show();

            let url = "{{ route('profesionales.buscaProfesional') }}";

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

                    document.getElementById("identificacion").value = data.identificacion
                    document.getElementById("identOriginal").value = data.identificacion
                    document.getElementById("nombre").value = data.nombre
                    document.getElementById("telefono").value = data.celular
                    document.getElementById("email").value = data.correo
                    document.getElementById("observaciones").value = data.observaciones
                    document.getElementById("usuarioOriginal").value = data.login_usuario
                    document.getElementById("usuario").value = data.login_usuario
                    document.getElementById("estado").value = data.estado_usuario
                    document.getElementById("tipo").value = data.tipo_usuario

                    document.getElementById("registroProf").value = data.registro
                    document.getElementById("firmaOriginal").value = data.firma
                    
                    if (data.firma) {
                        document.getElementById("firmaProf").style.display = 'none'
                        document.getElementById("verFirma").style.display = 'block'
                    } else {
                        document.getElementById("firmaProf").style.display = 'block'
                        document.getElementById("verFirma").style.display = 'none'
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        function cancelarRegistro() {
            const formProfesional = document.getElementById('formProfesional');
            formProfesional.reset();
        }

        function nuevoRegistro(opc) {
            if (opc == 1) {
                var modal = new bootstrap.Modal(document.getElementById("modalProfesional"), {
                    backdrop: 'static',
                    keyboard: false
                });
                modal.show();
            }
            cancelarRegistro();

            document.getElementById('saveRegistro').removeAttribute('disabled')
            document.getElementById('newRegistro').style.display = 'none'
            document.getElementById('cancelRegistro').style.display = 'initial'
            document.getElementById("div-cambioPasw").style.display = 'none'
            document.getElementById('pasword').removeAttribute('disabled', 'disabled')
            document.getElementById('rpasword').removeAttribute('disabled', 'disabled')
            document.getElementById("accRegistro").value = "guardar"

            document.getElementById("firmaProf").style.display = 'block'
            document.getElementById("verFirma").style.display = 'none'

        }

        function habilitarPasw() {
            const pasw = document.getElementById("pasword");

            if (pasw.disabled) {
                document.getElementById('pasword').removeAttribute('disabled', 'disabled')
                document.getElementById('rpasword').removeAttribute('disabled', 'disabled')
            } else {
                document.getElementById('pasword').setAttribute('disabled', 'disabled')
                document.getElementById('rpasword').setAttribute('disabled', 'disabled')
            }
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
                    let url = "{{ route('profesionales.eliminarProf') }}";
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

        function verFima() {

            var modal = new bootstrap.Modal(document.getElementById("modalFirmaProfesional"), {
                backdrop: 'static',
                keyboard: false
            });
            modal.show();

            const previewFirma = document.getElementById('imgfirma');
            const firma = document.getElementById('firmaOriginal').value;
            let url = $('#Ruta').data("ruta");
            previewFirma.src = url + "/images/firmasProfesionales/" + firma;
        }

        function cambiarFirma() {
            const imgFirma = document.getElementById('firmaProf');
            imgFirma.style.display = 'block';
            document.getElementById('verFirma').style.display = 'none';
        }

        function cargar(page, searchTerm = '') {


            let url = "{{ route('profesionales.listaProfesionales') }}"; // Definir la URL

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
                    document.getElementById('trRegistros').innerHTML = responseData.profesionales;
                    feather.replace();
                    // Colocar los enlaces de paginación
                    document.getElementById('pagination-links').innerHTML = responseData.links;
                    loadNow(0);
                })
                .catch(error => console.error('Error:', error));

        }
    </script>

@endsection
