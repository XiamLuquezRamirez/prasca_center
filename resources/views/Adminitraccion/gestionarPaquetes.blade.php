@extends('Plantilla.Principal')
@section('title', 'Gestionar paquetes de Sesiones')
@section('Contenido')
    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h4 class="page-title">Gestionar paquetes de Sesiones</h4>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#"><i class="mdi mdi-home-outline"></i></a></li>
                            <li class="breadcrumb-item" aria-current="page">Inicio</li>
                            <li class="breadcrumb-item active" aria-current="page">Gestionar paquetes de Sesiones</li>
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
                        <h5 class="card-title">Listado de Gestionar paquetes de Sesiones </h5>
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
                                        class="btn btn-xs btn-primary font-bold"><i class="fa fa-plus"></i> Nuevo paquete</button>
                                </div>

                            </div>
                        </div>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th style="width:70%;">Descripción</th>
                                    <th style="width:20%;">valor por sesión </th>
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
    <div class="modal fade" id="modalPaquete" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 40%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="tituloAccion">Agregar paquete</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                    <form id="formPaquetes">
                        <input type="hidden" name="accRegistro" id="accRegistro" value="guardar" />
                        <input type="hidden" name="idRegistro" id="idRegistro" value="" />
                        <div class="row">
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label for="descripcion" class="form-label">Descripción :</label>
                                    <input type="text" class="form-control" id="descripcion" name="descripcion">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="valor" class="form-label">Valor :</label>
                                    <input type="text" class="form-control" id="valorVis" name="valorVis"
                                    onchange="cambioFormato(this.id);"
                                    onkeypress="return validartxtnum(event)"
                                    onclick="this.select();">
                                    <input type="hidden" class="form-control"  id="valor" name="valor">
                                </div>
                            </div>                         
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="observaciones" class="form-label">Observaciones :</label>
                                    <textarea class="form-control" id="observaciones" name="observaciones" rows="3"></textarea>
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
            let menuS = document.getElementById("principalParametrosPaquetes")

            menuP.classList.add("active", "menu-open")
            menuS.classList.add("active")

            loader = document.getElementById('loader')
            loadNow(1)

            $("#formPaquetes").validate({
                rules: {

                    descripcion: {
                        required: true,                       
                    },
                    valorVis: {
                        required: true
                    }
                },
                messages: {
                    descripcion: {
                        required: "Por favor, ingrese la descripción del paquete."
                    },
                    valorVis: {
                        required: "Por favor, el valor unitario por sesión.",
                    }
                },
                submitHandler: function(form) {
                    guardarRegistro()
                }
            });

            cargar(1);

            // Evento click para la paginación
            document.addEventListener('click', function(event) {
                if (event.target.matches('.pagination a')) {
                    event.preventDefault()
                    var href = event.target.getAttribute('href')
                    var page = href.split('page=')[1]

                    // Asegurarse de que 'page' sea un número antes de hacer la solicitud
                    if (!isNaN(page)) {
                        cargar(page)
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

        function guardarRegistro() {

            if ($("#formPaquetes").valid()) {

                const formPaquetes = document.getElementById('formPaquetes');
                const formData = new FormData(formPaquetes);

                const url = "{{ route('form.guardarPaquete') }}";

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
            var modal = new bootstrap.Modal(document.getElementById("modalPaquete"), {
                backdrop: 'static',
                keyboard: false
            });
            document.getElementById("accRegistro").value = 'editar'
            document.getElementById("idRegistro").value = idRegistro
            document.getElementById('saveRegistro').removeAttribute('disabled')

            document.getElementById("tituloAccion").innerHTML  = "Editar entidad promotora de salud"

            modal.show();

            let url = "{{ route('paquetes.buscarPaquete') }}";

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
                    document.getElementById("descripcion").value = data.descripcion
                    var numero = data.precio_por_sesion
                    var formatoMoneda = formatCurrency(numero, 'es-CO', 'COP')
                    document.getElementById("valorVis").value = formatoMoneda
                    document.getElementById("valor").value = numero
                    document.getElementById("observaciones").value = data.observaciones

                })
                .catch(error => console.error('Error:', error));

        }

        function cancelarRegistro() {
            const formPaquetes = document.getElementById('formPaquetes')
            formPaquetes.reset();
        }

        function cambioFormato(id){
            let numero = document.getElementById(id)
            document.getElementById("valor").value = numero.value
            let formatoMoneda = formatCurrency(numero.value, 'es-CO', 'COP')
            numero.value = formatoMoneda

        }

        function formatCurrency(number, locale, currencySymbol) {
            return new Intl.NumberFormat(locale, {
                style: 'currency',
                currency: currencySymbol,
                minimumFractionDigits: 2
            }).format(number)
        }

        function validartxtnum(e) {
            tecla = e.which || e.keyCode
            patron = /[0-9]+$/
            te = String.fromCharCode(tecla)
            return (patron.test(te) || tecla == 9 || tecla == 8 || tecla == 37 || tecla == 39 || tecla == 44)
        }
        
        function nuevoRegistro(opc) {

            if (opc == 1) {
                var modal = new bootstrap.Modal(document.getElementById("modalPaquete"), {
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

            
            document.getElementById("tituloAccion").innerHTML  = "Agregar paquetes de sesiones"
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
                    let url = "{{ route('paquetes.eliminarPaquete') }}";
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


            let url = "{{ route('paquetes.listaPaquetes') }}"; // Definir la URL

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
                    document.getElementById('trRegistros').innerHTML = responseData.paquetes;
                    feather.replace();
                    // Colocar los enlaces de paginación
                    document.getElementById('pagination-links').innerHTML = responseData.links;
                    loadNow(0);
                })
                .catch(error => console.error('Error:', error));

        }
    </script>

@endsection
