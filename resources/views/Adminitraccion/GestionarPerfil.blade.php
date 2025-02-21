@extends('Plantilla.Principal')
@section('title', 'Gestionar perfil')
@section('Contenido')
    <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
    <input type="hidden" id="Ruta" data-ruta="{{ asset('/app-assets/') }}" />
    <input type="hidden" id="conTrata" name="conTrata" value="" />
    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h4 class="page-title">Gestionar datos del usuario</h4>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#"><i class="mdi mdi-home-outline"></i></a></li>
                            <li class="breadcrumb-item" aria-current="page">Inicio</li>
                            <li class="breadcrumb-item active" aria-current="page">Datos del usuario/li>
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
                        <h5 class="card-title">Detalles del usuario</h5>
                    </div>
                    <div class="card-body">
                        <form class="form" method="post" id="formGuardar"
                                    action="{{ url('/') }}/Administracion/UpdatePerfil">

                                    <input type="hidden" name="idPaciente" id="idPaciente" value="">
                                    <input type="hidden" name="accion" id="accion" value="">
                                    <input type="hidden" name="fotoCargada" id="fotoCargada" value="{{Auth::user()->foto_usuario}}">
                                    <div class="row">
                                        <div class="col-xl-12 col-lg-12">
                                            <div class="box">
                                                <a class="media-single" style="padding: 5px;">
                                                    <img id="previewImage" class="avatar pull-left me-10"
                                                        src="{{ asset('app-assets/images/FotosUsuarios/'.Auth::user()->foto_usuario) }}"
                                                        alt="">
                                                    <div>
                                                        <label class="btn btn-xs btn-primary cursor-pointer" for="account-upload">Subir
                                                            una foto</label>
                                                        <input type="file" name="fotoUsuario" id="account-upload" hidden>
                                                        <button type="button" onclick="clearImage()"
                                                            class="btn btn-light btn-xs">Limpiar</button>
                                                        <p class="text-fade fs-12 mb-0">Solo JPG, GIF o PNG.
                                                            Tam. Max. de 800kB</p>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-5">
                                            <div class="form-group">
                                                <div class="controls">
                                                    <label for="account-name">Nombre</label>
                                                    <input type="text" class="form-control" id="nombre" name="nombre"
                                                        onkeypress="return validartxt(event)" placeholder="Nombre"
                                                        value="{{ Auth::user()->nombre_usuario }}">
                                                </div>
                                            </div>
                                        </div>


                                        <div class="col-4">
                                            <div class="form-group">
                                                <div class="controls">
                                                    <label for="account-e-mail">Email</label>
                                                    <input type="email" class="form-control" id="email" name="email"
                                                        placeholder="Email" value="{{ Auth::user()->email_usuario }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-group">
                                                <label for="account-company">Teléfono</label>
                                                <input type="text" class="form-control" id="telefono" name="telefono"
                                                    placeholder="Teléfono" value="{{ Auth::user()->telefono }}">
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-group">
                                                <label for="account-company">Usuario</label>
                                                <input type="text" class="form-control"
                                                    onchange="$.validaUsuario(this.value)" id="usuario" name="usuario"
                                                    placeholder="usuario" value="{{ Auth::user()->login_usuario }}">
                                            </div>
                                        </div>

                                        <div class="col-2">
                                            <div id="div-cambioPasw"  class="form-group">
                                                <label for="cambioPasw" class="form-label">Actualizar contraseña :</label><br>
        
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
                                                <label for="pasw" class="form-label">Contraseña :</label>
                                                <div class="input-group mb-3">
                                                    <span class="input-group-text"><i class="ti-lock"></i></span>
                                                    <input type="password" disabled id="pasw" name="pasw" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-group">
                                                <label for="confPasw" class="form-label">Confirmar Contraseña :</label>
                                                <div class="input-group mb-3">
                                                    <span class="input-group-text"><i class="ti-lock"></i></span>
                                                    <input type="password" disabled id="confPasw" name="confPasw" class="form-control">
                                                </div>
                                            </div>                                           
                                        </div>
                                    </div>
                                    <div class="box-footer text-end">
                                        
                                        <button type="button" id="savePaciente" onclick="$.guardar();"
                                            class="btn btn-primary">
                                            <i class="ti-save"></i> Guardar
                                        </button>
                                    </div>
                                </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <form action="{{ url('/AdminPacientes/CargarPacientes') }}" id="formCargarPacientes" method="POST">
        @csrf
        <!-- Tus campos del formulario aquí -->
    </form>
    <form action="{{ url('/Administracion/VerificarUsuario') }}" id="formValidarUsuario" method="POST">
        @csrf
        <!-- Tus campos del formulario aquí -->
    </form>
    <form action="{{ url('/AdminPacientes/updateServiciosTerminados') }}" id="formServTerminados" method="POST">
        @csrf
        <!-- Tus campos del formulario aquí -->
    </form>


@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            localStorage.clear();

            loader = document.getElementById('loader')
            loadNow(1)


            document.getElementById('account-upload').addEventListener('change', function(event) {
                const file = event.target.files[0];
                const previewImage = document.getElementById('previewImage');

                if (file) {
                    const imageUrl = URL.createObjectURL(file);
                    previewImage.src = imageUrl;
                }
            });


            $.extend({
                cargar: function(page, searchTerm = '') {
                    var form = $("#formCargarPacientes");
                    var url = form.attr("action");
                    $('#page').remove();
                    $('#searchTerm').remove();
                    form.append("<input type='hidden' id='page' name='page'  value='" + page + "'>");
                    form.append("<input type='hidden' id='searchTerm' name='search'  value='" +
                        searchTerm +
                        "'>");
                    var datos = form.serialize();

                    $('#tdTable').empty();

                    let x = 1;
                    let tdTable = '';
                    $.ajax({
                        type: "POST",
                        url: url,
                        data: datos,
                        async: false,
                        dataType: "json",
                        success: function(response) {
                            $('#trRegistros').html(response
                                .temas); // Rellenamos la tabla con las filas generadas
                            $('#pagination-links').html(response
                                .links); // Colocamos los enlaces de paginación
                        }
                    });
                },
                habilitarContra: function(elemt) {

                    if (elemt.checked) {
                        document.getElementById("cambioPasw").readOnly = false;
                    } else {
                        document.getElementById("cambioPasw").readOnly = true;
                        document.getElementById("cambioPasw").value = "";
                    }
                },
                validaUsuario: function(valida) {
                    var form = $("#formValidarUsuario");
                    var url = form.attr("action");
                    $('#Usuario').remove();
                    form.append("<input type='hidden' id='Usuario' name='Usuario'  value='" + valida +
                        "'>");
                    var datos = form.serialize();

                    $.ajax({
                        type: "POST",
                        url: url,
                        data: datos,
                        async: false,
                        dataType: "json",
                        success: function(response) {
                            if (response.usuario > 0) {
                                Swal.fire({
                                    type: "warning",
                                    title: "Oops...",
                                    text: "Este usuario se enuentra registrado",
                                    confirmButtonClass: "btn btn-primary",
                                    timer: 1500,
                                    buttonsStyling: false
                                });
                                $("#usuario").val("");
                                return;
                            }

                        }
                    });
                },
                guardar: function() {
                    if ($("#nombre").val().trim() === "") {
                        Swal.fire({
                            type: "warning",
                            title: "Oops...",
                            text: "Debes de ingresar su nombre",
                            confirmButtonClass: "btn btn-primary",
                            timer: 1500,
                            buttonsStyling: false
                        });
                        return;
                    }

                    if ($("#email").val().trim() === "") {
                        Swal.fire({
                            type: "warning",
                            title: "Oops...",
                            text: "Debes de ingresar su Email",
                            confirmButtonClass: "btn btn-primary",
                            timer: 1500,
                            buttonsStyling: false
                        });
                        return;
                    }
                    if ($("#telefono").val().trim() === "") {
                        swal({
                            type: "warning",
                            title: "Oops...",
                            text: "Debes de ingresar su teléfono",
                            confirmButtonClass: "btn btn-primary",
                            timer: 1500,
                            buttonsStyling: false
                        });
                        return;
                    }
                    if ($("#usuario").val().trim() === "") {
                        swal({
                            type: "warning",
                            title: "Oops...",
                            text: "Debes de ingresar su usuario",
                            confirmButtonClass: "btn btn-primary",
                            timer: 1500,
                            buttonsStyling: false
                        });
                        return;
                    }
                    let checkPasw = document.getElementById("cambioPasw");

                    if (checkPasw.checked && document.getElementById("pasw").value == "") {
                        swal({
                            type: "warning",
                            title: "Oops...",
                            text: "Debes de ingresar la contraseña si se desea cambiar",
                            confirmButtonClass: "btn btn-primary",
                            timer: 1500,
                            buttonsStyling: false
                        });
                        return;
                    }

                    var form = $("#formGuardar");
                    var url = form.attr("action");
                    var accion = $("#accion").val();
                    var token = $("#token").val();
                    $("#idtoken").remove();
                    $("#accion").remove();

                    form.append("<input type='hidden' id='idtoken' name='_token'  value='" + token +
                        "'>");

                    $.ajax({
                        type: "POST",
                        url: url,
                        data: new FormData($('#formGuardar')[0]),
                        processData: false,
                        contentType: false,
                        success: function(respuesta) {
                            if (respuesta.estado == "ok") {
                                swal("¡Buen trabajo!", "La operación fue realizada exitosamente", "success")
                            }
                        },
                        error: function() {
                            console.error('Error en el procesamiento:', data.message)
                        }
                    });

                }
            });
        })

        function clearImage() {
            const previewImage = document.getElementById('previewImage');
            let ruta = $('#Ruta').data('ruta');
            console.log
            previewImage.src = ruta + '/images/FotosUsuarios/avatar-s-1.png';
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

        function validartxtnum(e) {
            tecla = e.which || e.keyCode;
            patron = /[0-9]+$/;
            te = String.fromCharCode(tecla);
            //    if(e.which==46 || e.keyCode==46) {
            //        tecla = 44;
            //    }
            return (patron.test(te) || tecla == 9 || tecla == 8 || tecla == 37 || tecla == 39 || tecla == 44);
        }


        function validartxt(e) {
            tecla = e.which || e.keyCode;
            patron = /[a-zA-Z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF\s]+$/;
            te = String.fromCharCode(tecla);
            return (patron.test(te) || tecla == 9 || tecla == 8 || tecla == 37 || tecla == 39 || tecla == 46);
        }

        function clearImage() {
            const previewImage = document.getElementById('previewImage');
            previewImage.src = '../../../app-assets/images/FotosUsuarios/avatar-13.png';
        }
    </script>

    </script>
@endsection
