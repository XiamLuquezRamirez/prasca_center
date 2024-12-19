@extends('Plantilla.Principal')
@section('title', 'Gestionar pacientes')
@section('Contenido')
    <input type="hidden" id="Ruta" data-ruta="{{ asset('/app-assets/') }}" />
    <input type="hidden" id="page" />
    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h4 class="page-title">Gestionar pacientes</h4>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#"><i class="mdi mdi-home-outline"></i></a></li>
                            <li class="breadcrumb-item" aria-current="page">Inicio</li>
                            <li class="breadcrumb-item active" aria-current="page">Gestionar pacientes</li>
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
                        <h5 class="card-title">Listado de pacientes</h5>
                    </div>
                    <div class="card-body">
                        <div class="box-controls pull-right">
                            <div class="box-header-actions">
                                <div class="input-group input-group-merge">
                                    <input type="text" id="busqueda" class="form-control">
                                    <div class="input-group-text" data-password="false">
                                        <span class="fa fa-search"></span>
                                    </div>
                                    <button type="button" onclick="nuevoRegistro();"
                                        class="btn btn-xs btn-primary font-bold"><i class="fa fa-plus"></i> Nuevo
                                        paciente</button>
                                </div>

                            </div>
                        </div>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th style="width:10%;">Identificación</th>
                                    <th style="width:30%;">Nombre</th>
                                    <th style="width:13%;">Regimen</th>
                                    <th style="width:6%;">Sexo</th>
                                    <th style="width:8%;">Edad</th>
                                    <th style="width:8%;">Teléfono</th>
                                    <th style="width:10%;">Datos paciente</th>
                                    <th style="width:15%;">Acción</th>
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
    <!-- MODAL PACIENTES -->
    <div class="modal fade" id="modalPacientes" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 60%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">Agregar paciente</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                    <form id="formPaciente" lang="es">
                        <input type="hidden" name="accPacientes" id="accPacientes" value="guardar" />
                        <input type="hidden" name="idPaciente" id="idPaciente" value="" />
                        <input type="hidden" name="fotoCargada" id="fotoCargada" />
                        <h5 class="mb-4 text-uppercase"><i class="mdi mdi-account-circle me-1"></i> Información personal
                        </h5>
                        <div class="row">
                            <div class="col-xl-12 col-lg-12">
                                <div class="box">
                                    <a class="media-single" style="padding: 5px;">
                                        <img id="previewImage" class="avatar pull-left me-10"
                                            src="{{ asset('app-assets/images/FotosPacientes/default.jpg') }}"
                                            alt="">
                                        <div>
                                            <label class="btn btn-xs btn-primary cursor-pointer" for="account-upload">Subir
                                                una foto</label>
                                            <input type="file" name="fotoPaciente" id="account-upload" hidden>
                                            <button type="button" onclick="clearImage()"
                                                class="btn btn-light btn-xs">Limpiar</button>
                                            <p class="text-fade fs-12 mb-0">Solo JPG, GIF o PNG.
                                                Tam. Max. de 800kB</p>
                                        </div>
                                    </a>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tipoIdentificacion" class="form-label">Tipo de identificación :</label>
                                    <select class="form-control" id="tipoId" name="tipoId" aria-invalid="false">
                                            <option value="">Selecciona una
                                                opción</option>
                                            <option value="AS">
                                                Adulto sin Identificación </option>
                                            <option value="CC">
                                                Cédula Ciudadanía </option>
                                            <option value="CD">
                                                Carné Diplomático </option>
                                            <option value="CE">
                                                Cédula de Extranjería </option>
                                            <option value="MS">
                                                Menor sin Identificación </option>
                                            <option value="NV">
                                                Certificado de Nacido Vivo </option>
                                            <option value="PE">
                                                Permiso Especial del Permanencia </option>
                                            <option value="PT">
                                                Permiso por protección temporal </option>
                                            <option value="RC">
                                                Registro Civil </option>
                                            <option value="TI">
                                                Tarjeta de identidad </option>
                                        </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="identificación" class="form-label">Identificación :</label>
                                    <input type="text" minlength="4" maxlength="20" class="form-control"
                                        id="identificacion" name="identificacion">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tipoUsuario" class="form-label">Tipo de usuario :</label>
                                    <select class="form-control" id="tipoUsuario" name="tipoUsuario"
                                        aria-invalid="false">
                                        
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="fechaNacimiento" class="form-label">Fecha de nacimiento:</label>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" id="fechaNacimiento" placeholder="" name="fechaNacimiento"
                                            class="form-control" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="edad" class="form-label">Edad :</label>
                                    <input type="text" readonly class="form-control" id="edad" name="edad">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="primerNombre" class="form-label">Primer nombre :</label>
                                    <input type="text" class="form-control" id="primerNombre" name="primerNombre">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="segundoNombre" class="form-label">Segundo nombre :</label>
                                    <input type="text" class="form-control" id="segundoNombre" name="segundoNombre">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="primerApellido" class="form-label">Primer apellido :</label>
                                    <input type="text" class="form-control" id="primerApellido"
                                        name="primerApellido">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="segundoApellido" class="form-label">Segundo apellido :</label>
                                    <input type="text" class="form-control" id="segundoApellido"
                                        name="segundoApellido">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="sexo" class="form-label">Sexo :</label>
                                    <select class="form-control" id="sexo" name="sexo" aria-invalid="false">
                                        <option value="">Selecciona una
                                            opción</option>
                                        <option value="H">
                                            Hombre </option>
                                        <option value="I">
                                            Indeterminado o Intersexual </option>
                                        <option value="M">
                                            Mujer</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="estadocivil" class="form-label">Estado civil :</label>
                                    <select class="form-control" id="estadocivil" name="estadocivil"
                                        aria-invalid="false">
                                        <option value="">Selecciona una
                                            opción</option>
                                        <option value="soltero">Soltero/a</option>
                                        <option value="casado">Casado/a</option>
                                        <option value="divorciado">Divorciado/a</option>
                                        <option value="viudo">Viudo/a</option>
                                        <option value="unionLibre">Unión libre</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="ocupacion" class="form-label">Ocupación :</label>
                                    <input type="text" class="form-control" id="ocupacion" name="ocupacion">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="lateralidad" class="form-label">Lateralidad :</label>
                                    <select class="form-control" id="lateralidad" name="lateralidad"
                                        aria-invalid="false">
                                        <option value="">Selecciona una
                                            opción</option>
                                        <option value="diestro">Diestro/a</option>
                                        <option value="zurdo">Zurdo/a</option>
                                        <option value="ambidiestro">Ambidiestro/a</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="religion" class="form-label">Religión :</label>
                                    <select class="form-control" id="religion" name="religion" aria-invalid="false">
                                        <option value="">Selecciona una
                                            opción</option>
                                        <option value="catolica">Católica</option>
                                        <option value="cristiana">Cristiana</option>
                                        <option value="judaica">Judaica</option>
                                        <option value="islamica">Islámica</option>
                                        <option value="budista">Budista</option>
                                        <option value="hinduista">Hinduista</option>
                                        <option value="agnostico">Agnóstico/a</option>
                                        <option value="ateo">Ateo/a</option>
                                        <option value="otra">Otra</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="lugarNacimiento" class="form-label">Lugar de nacimiento :</label>
                                    <input type="text" class="form-control" id="lugarNacimiento"
                                        name="lugarNacimiento">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="email" class="form-label">Email :</label>
                                    <input type="text" class="form-control" id="email" name="email">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="telefono" class="form-label">Teléfono :</label>
                                    <input type="text" class="form-control" id="telefono" name="telefono">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="direccion" class="form-label">Dirección :</label>
                                    <input type="text" class="form-control" id="direccion" name="direccion">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="zonaResidencial" class="form-label">Zona de residencia :</label>
                                    <select class="form-control" id="zonaResidencial" name="zonaResidencial"
                                        aria-invalid="false">
                                        <option value="">Selecciona una
                                            opción</option>
                                        <option value="01">Rural</option>
                                        <option value="02">Urbano</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="departamento" class="form-label">Departamento de residencia:</label>
                                    <select class="form-control select2" onchange="cargarMunicipios(this.value)"
                                        id="departamento" name="departamento" aria-invalid="false">

                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="municipio" class="form-label">Municipio de residencia:</label>
                                    <select class="form-control select2" id="municipio" name="municipio"
                                        aria-invalid="false">
                                        <option value=" ">Selecciona una opción</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="observaciones" class="form-label">Observaciones :</label>
                                    <textarea class="form-control" id="observaciones" name="observaciones" rows="3"></textarea>
                                </div>
                            </div>
                        </div>

                        <h5 class="mb-4 text-uppercase"><i class="mdi mdi-account-circle me-1"></i> Información
                            acompañante</h5>
                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="nombreAcompanante" class="form-label">Nombre :</label>
                                    <input type="text" class="form-control" id="nombreAcompanante"
                                        name="nombreAcompanante">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="parentesco" class="form-label">Parentesco :</label>
                                    <select class="form-control" id="parentesco" name="parentesco" aria-invalid="false">
                                        <option value="">Selecciona una
                                            opción</option>
                                        <option value="padre">Padre</option>
                                        <option value="madre">Madre</option>
                                        <option value="hijo">Hijo/a</option>
                                        <option value="hermano">Hermano/a</option>
                                        <option value="abuelo">Abuelo/a</option>
                                        <option value="tio">Tío/a</option>
                                        <option value="primo">Primo/a</option>
                                        <option value="sobrino">Sobrino/a</option>
                                        <option value="nieto">Nieto/a</option>
                                        <option value="otro">Otro</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="telefonoAcompanante" class="form-label">Teléfono :</label>
                                    <input type="text" class="form-control" id="telefonoAcompanante"
                                        name="telefonoAcompanante">
                                </div>
                            </div>

                            <div class="box-footer text-end">
                                <button type="button" onclick="nuevoRegistro();" style="display: none;"
                                    id="newPaciente" class="btn btn-primary-light me-1">
                                    <i class="ti-plus "></i> Nuevo
                                </button>
                                <button type="button" id="cancelPacientes" onclick="cancelarPacientes();"
                                    class="btn btn-primary-light me-1">
                                    <i class="ti-close"></i> Cancelar
                                </button>
                                <button type="button" id="savePaciente" onclick="guardarPacientes();"
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
            let menuP = document.getElementById("principalPacientes");

            menuP.classList.add("active");

            loader = document.getElementById('loader');
            loadNow(1);


            $('#departamento').select2({
                dropdownParent: $('#modalPacientes'),
                width: '100%'
            });

            $('#municipio').select2({
                dropdownParent: $('#modalPacientes'),
                width: '100%'
            });


            $('[data-mask]').inputmask();

            $.validator.addMethod("dateFormat", function(value, element) {
                // Verificar si la fecha está en el formato dd/mm/yyyy

                var dateParts = value.split("/");
                if (dateParts.length === 3) {
                    var day = parseInt(dateParts[0], 10);
                    var month = parseInt(dateParts[1], 10);
                    var year = parseInt(dateParts[2], 10);

                    // Comprobar si es una fecha válida
                    var date = new Date(year, month - 1, day);
                    return date && (date.getFullYear() === year) && (date.getMonth() === month - 1) && (date
                        .getDate() === day);
                }
                return false;
            }, "Por favor, ingresa una fecha válida en formato dd/mm/yyyy.");

            $.validator.addMethod("maxDate", function(value, element) {
                var dateParts = value.split("/");
                if (dateParts.length === 3) {
                    var day = parseInt(dateParts[0], 10);
                    var month = parseInt(dateParts[1], 10);
                    var year = parseInt(dateParts[2], 10);

                    // Convertir a formato Date
                    var inputDate = new Date(year, month - 1, day);
                    var today = new Date(); // Fecha actual

                    return inputDate <= today; // Validar que no sea mayor
                }
                return false;
            }, "La fecha no puede ser mayor a hoy.");

            $("#formPaciente").validate({
                rules: {
                    tipoId: {
                        required: true
                    },
                    identificacion: {
                        required: true,
                        remote: {
                            url: "/verificar-identificacion", // URL para verificar
                            type: "post",
                            data: {
                                identificacion: function() {
                                    return $("#identificacion").val();
                                },
                                tipoId: function() {
                                    return $("#tipoId").val(); // Tipo de identificación
                                },
                                id: function() {
                                    return $("#idPaciente").val() || null; // Enviar id si es edición
                                },
                                _token: function() {
                                    return "{{ csrf_token() }}"; // Token CSRF para seguridad
                                }
                            }
                        },
                        minlength: 5,
                        maxlength: 20
                    },

                    primerNombre: {
                        required: true
                    },
                    primerApellido: {
                        required: true
                    },
                    tipoUsuario: {
                        required: true
                    },
                    sexo: {
                        required: true
                    },
                    zonaResidencial: {
                        required: true
                    },
                    departamento: {
                        required: true
                    },
                    municipio: {
                        required: true
                    },
                    email: {
                        required: true,
                        email: true
                    },
                    telefono: {
                        required: true,
                        digits: true,
                        minlength: 7
                    },
                    fechaNacimiento: {
                        required: true,
                        dateFormat: true,
                        maxDate: true
                    },
                },
                messages: {
                    tipoId: {
                        required: "Por favor, selecciona un tipo de identificación."
                    },
                    identificacion: {
                        required: "Por favor, ingresa una identificación.",
                        remote: "Esta identificación ya está registrada.",
                        minlength: "La identificación debe tener al menos 5 caracteres.",
                        maxlength: "La identificación no puede exceder los 20 caracteres."
                    },
                    primerNombre: {
                        required: "Por favor, ingresa el primer nombre."
                    },
                    primerApellido: {
                        required: "Por favor, ingresa el primer apellido."
                    },
                    tipoUsuario: {
                        required: "Por favor, seleccione el tipo de usuario."
                    },
                    sexo: {
                        required: "Por favor, seleccione el sexo."
                    },
                    zonaResidencial: {
                        required: "Por favor, seleccione la zona de residencia ."
                    },
                    departamento: {
                        required: "Por favor, seleccione el departamento de residencia ."
                    },
                    municipio: {
                        required: "Por favor, seleccione el municipio de residencia ."
                    },
                    email: {
                        required: "Por favor, ingresa un email.",
                        email: "Por favor, ingresa un email válido."
                    },
                    telefono: {
                        required: "Por favor, ingresa un número de teléfono.",
                        digits: "Por favor, ingresa solo dígitos.",
                        minlength: "El número de teléfono debe tener al menos 7 dígitos."
                    },
                    fechaNacimiento: {
                        required: "Por favor, ingresa tu fecha de nacimiento.",
                        dateFormat: "Por favor, ingresa una fecha válida en formato dd/mm/yyyy.",
                        maxDate: "La fecha de nacimiento no puede ser mayor que hoy."
                    },
                },
                submitHandler: function(form) {
                    guardarPacientes();
                }
            });

            cargarPacientes(1);

            document.getElementById('account-upload').addEventListener('change', function(event) {
                const file = event.target.files[0];
                const previewImage = document.getElementById('previewImage');

                if (file) {
                    const imageUrl = URL.createObjectURL(file);
                    previewImage.src = imageUrl;
                }
            });

            // Evento click para la paginación
            document.addEventListener('click', function(event) {
                if (event.target.matches('.pagination a')) {
                    event.preventDefault();
                    var href = event.target.getAttribute('href');
                    var page = href.split('page=')[1];

                    // Asegurarse de que 'page' sea un número antes de hacer la solicitud
                    if (!isNaN(page)) {
                        cargarPacientes(page);
                    }
                }
            });
            // Evento input para el campo de búsqueda
            document.getElementById('busqueda').addEventListener('input', function() {
                var searchTerm = this.value;
                cargarPacientes(1,
                    searchTerm);
            });

            const fechaNacimiento = document.getElementById("fechaNacimiento");


            fechaNacimiento.addEventListener("change", validarIdentificacionPorEdad);
            fechaNacimiento.addEventListener("input", validarIdentificacionPorEdad);
            fechaNacimiento.addEventListener("blur", validarIdentificacionPorEdad);
            document.getElementById("tipoId").addEventListener("change", validarIdentificacionPorEdad);

        });

        function validarIdentificacionPorEdad() {

            const tipoId = document.getElementById("tipoId").value;
            const fechaNacimiento = document.getElementById("fechaNacimiento").value;

            if (!fechaNacimiento) return; // Si no hay fecha de nacimiento, salir

            const hoy = new Date();
            const nacimiento = new Date(fechaNacimiento.split('/').reverse().join('-')); // Convertir a formato ISO

            // Cálculo inicial de años, meses y días
            let anios = hoy.getFullYear() - nacimiento.getFullYear();
            let meses = hoy.getMonth() - nacimiento.getMonth();
            let dias = hoy.getDate() - nacimiento.getDate();

            // Ajustar si los meses o días son negativos
            if (meses < 0 || (meses === 0 && dias < 0)) {
                anios--;
                meses += 12;
            }
            if (dias < 0) {
                const ultimoDiaMesAnterior = new Date(hoy.getFullYear(), hoy.getMonth(), 0).getDate();
                dias += ultimoDiaMesAnterior;
                meses--;
            }

            let tiposPermitidos = [];
            let edad;

            if (anios > 0) {
                edad = `${anios} ${anios === 1 ? 'año' : 'años'}`;
            } else if (meses > 0) {
                edad = `${meses} ${meses === 1 ? 'mes' : 'meses'}`;
            } else {
                edad = `${dias} ${dias === 1 ? 'día' : 'días'}`;
            }

            // Determinar los tipos de documento permitidos según la edad

            if (anios <= 6) {
                tiposPermitidos = ["RC", "NV", "PT", "CD", "PE", "MS"];
            } else if (anios >= 7 && anios <= 17) {
                tiposPermitidos = ["TI", "CE", "PT", "CD", "PE", "MS"];
            } else if (anios >= 18) {
                tiposPermitidos = ["CC", "TI", "CE", "PT", "CD", "PE", "AS"];
            }

            // Verificar si el tipo de identificación es válido
            if (!tiposPermitidos.includes(tipoId)) {
                document.getElementById('fechaNacimiento').value = ""
                document.getElementById('edad').value = ""
                swal("¡Alerta!", `El tipo de identificación "${tipoId}" no corresponde con la edad de ${anios} años.`,
                    "warning")

            } else {
                document.getElementById('edad').value = edad;
            }
        }


        function guardarPacientes() {

            if ($("#formPaciente").valid()) {

                const formPaciente = document.getElementById('formPaciente');
                const formData = new FormData(formPaciente);

                const url = "{{ route('form.guardarPaciente') }}";

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
                        if (data.success = 'success') {

                            swal(data.title, data.message, data.success)

                            document.getElementById('savePaciente').setAttribute('disabled', 'disabled')
                            document.getElementById('newPaciente').style.display = 'initial'
                            document.getElementById('cancelPacientes').style.display = 'none'

                            cargarPacientes(1)
                            document.getElementById("accPacientes").value = "guardar"

                        } else {
                            swal(data.title, data.message, data.success)
                        }
                    })
                    .catch(error => {
                        console.error("Error al enviar los datos:", error);
                    });

            }
        }

        function editarPaciente(idPaciente) {
            var modal = new bootstrap.Modal(document.getElementById("modalPacientes"), {
                backdrop: 'static',
                keyboard: false
            });
            document.getElementById("idPaciente").value = idPaciente
            document.getElementById("accPacientes").value = 'editar'

            modal.show();
            cargarDepartamento();

            let url = "{{ route('pacientes.buscaPaciente') }}";

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

                    var foto = data.foto;
                    $("#fotoCargada").val(foto);
                    const previewImage = document.getElementById('previewImage');
                    let url = $('#Ruta').data("ruta");
                    previewImage.src = url + "/images/FotosPacientes/" + foto;

                    document.getElementById("tipoId").value = data.tipo_identificacion
                    document.getElementById("identificacion").value = data.identificacion

                    var fechForm = convertirFecha(data.fecha_nacimiento);
                    $("#fechaNacimiento").val(fechForm);
                    document.getElementById("edad").value = data.edad
                    document.getElementById("lugarNacimiento").value = data.lugar_nacimiento
                    document.getElementById("primerNombre").value = data.primer_nombre
                    document.getElementById("segundoNombre").value = data.segundo_nombre
                    document.getElementById("primerApellido").value = data.primer_apellido
                    document.getElementById("segundoApellido").value = data.segundo_apellido
                    document.getElementById("sexo").value = data.sexo
                    document.getElementById("estadocivil").value = data.estado_civil
                    $('#ocupacion').value = data.ocupacion
                    document.getElementById("tipoUsuario").value = data.tipo_usuario
                    document.getElementById("lateralidad").value = data.lateralidad
                    document.getElementById("religion").value = data.religion
                    document.getElementById("email").value = data.email
                    document.getElementById("telefono").value = data.telefono
                    document.getElementById("direccion").value = data.direccion
                    $('#departamento').val(data.departamento).trigger('change.select2')
                    $('#municipio').val(data.municipio).trigger('change.select2')
                    document.getElementById("zonaResidencial").value = data.zona_residencial

                    document.getElementById("observaciones").value = data.observaciones
                    document.getElementById("nombreAcompanante").value = data.acompanante
                    document.getElementById("parentesco").value = data.parentesco
                    document.getElementById("telefonoAcompanante").value = data.telefono_acompanate
                })
                .catch(error => console.error('Error:', error));

        }

        function convertirFecha(fecha) {
            // Dividir la fecha en año, mes y día
            const [año, mes, dia] = fecha.split('-');

            // Formatear la fecha en el formato dd/mm/yyyy
            const fechaFormateada = `${dia.padStart(2, '0')}/${mes.padStart(2, '0')}/${año}`;

            return fechaFormateada;
        }

        function mostrarListado() {
            let listado = document.getElementById("listado");
            let formulario = document.getElementById("formulario");

            listado.style.display = "block";
            formulario.style.display = "none";
        }

        function cancelarPacientes() {
            const formPaciente = document.getElementById('formPaciente');
            formPaciente.reset();
            document.getElementById("municipio").innerHTML = "<option value="
            ">Selecciona una opción</option>"
        }

        function nuevoRegistro() {
            var modal = new bootstrap.Modal(document.getElementById("modalPacientes"), {
                backdrop: 'static',
                keyboard: false
            });

            modal.show();

            cancelarPacientes();

            document.getElementById('savePaciente').removeAttribute('disabled')
            document.getElementById('newPaciente').style.display = 'none'
            document.getElementById('cancelPacientes').style.display = 'initial'
            document.getElementById("accPacientes").value = "guardar"

            cargarDepartamento();
            cargarTipoUsuario();
        }

        function eliminarPaciente(idPac) {

            swal({
                title: "Esta seguro de eliminar este paciente ?",
                text: "¡No podrás revertir esto!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Si, eliminar!",
                cancelButtonText: "Cancelar",
                confirmButtonClass: "btn btn-warning",
                cancelButtonClass: "btn btn-danger ml-1",
                buttonsStyling: false
            }, function(isConfirm) {
                if (isConfirm) {
                    let url = "{{ route('pacientes.eliminarPac') }}";
                    fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content')
                            },
                            body: JSON.stringify({
                                idPaciente: idPac
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                swal("¡Buen trabajo!",
                                    data.message,
                                    "success");
                                cargarPacientes(1);
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

        function cargarDepartamento() {
            return new Promise((resolve, reject) => {
                let select = document.getElementById("departamento");
                let url = "{{ route('pacientes.departamentos') }}";

                let defaultOption = document.createElement("option");
                defaultOption.value = ""; // Valor en blanco
                defaultOption.text = "Selecciona una opción"; // Texto que se mostrará
                defaultOption.disabled = true; // Deshabilitar para que no pueda ser seleccionada
                defaultOption.selected = true; // Que aparezca seleccionada por defecto
                select.appendChild(defaultOption);

                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        console.log(data)
                        data.forEach(departamento => {
                            let option = document.createElement("option");
                            option.value = departamento.codigo;
                            option.text = departamento.nombre;
                            select.appendChild(option);
                        });
                        resolve(); // Resuelve la promesa cuando los datos han sido cargados
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        reject(error); // Rechaza la promesa si ocurre un error
                    });
            });
        }

        function cargarTipoUsuario() {
            return new Promise((resolve, reject) => {
                let select = document.getElementById("tipoUsuario");
                select.innerHTML = ""
                let url = "{{ route('pacientes.tipoUSuario') }}";

                let defaultOption = document.createElement("option");
                defaultOption.value = ""; // Valor en blanco
                defaultOption.text = "Selecciona una opción"; // Texto que se mostrará
                defaultOption.disabled = true; // Deshabilitar para que no pueda ser seleccionada
                defaultOption.selected = true; // Que aparezca seleccionada por defecto
                select.appendChild(defaultOption);

                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        console.log(data)
                        data.forEach(tipo => {
                            let option = document.createElement("option");
                            option.value = tipo.id;
                            option.text = tipo.descripcion;
                            select.appendChild(option);
                        });
                        resolve(); // Resuelve la promesa cuando los datos han sido cargados
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        reject(error); // Rechaza la promesa si ocurre un error
                    });
            });
        }


        function cargarMunicipios(muni) {
            return new Promise((resolve, reject) => {
                let select = document.getElementById("municipio");
                select.innerHTML = "";
                let url = "{{ route('pacientes.municipio') }}";

                let defaultOption = document.createElement("option");
                defaultOption.value = ""; // Valor en blanco
                defaultOption.text = "Selecciona una opción"; // Texto que se mostrará
                defaultOption.disabled = true; // Deshabilitar para que no pueda ser seleccionada
                defaultOption.selected = true; // Que aparezca seleccionada por defecto
                select.appendChild(defaultOption);

                fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        body: JSON.stringify({
                            idMuni: muni
                        })
                    })
                    .then(response => response.json())
                    .then(data => {

                        data.forEach(municipios => {
                            let option = document.createElement("option");
                            option.value = municipios.codigo;
                            option.text = municipios.nombre;
                            select.appendChild(option);
                        });
                        resolve(); // Resuelve la promesa cuando los datos han sido cargados
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        reject(error); // Rechaza la promesa si ocurre un error
                    });
            });
        }

        function clearImage() {
            const previewImage = document.getElementById('previewImage');
            let url = $('#Ruta').data("ruta");
            previewImage.src = url + "/images/FotosPacientes/default.jpg";
        }

        function cargarPacientes(page, searchTerm = '') {


            let url = "{{ route('pacientes.listaPacientes') }}"; // Definir la URL

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
                    document.getElementById('trRegistros').innerHTML = responseData.pacientes;
                    feather.replace();
                    // Colocar los enlaces de paginación
                    document.getElementById('pagination-links').innerHTML = responseData.links;
                    loadNow(0);
                })
                .catch(error => console.error('Error:', error));

        }
    </script>

@endsection
