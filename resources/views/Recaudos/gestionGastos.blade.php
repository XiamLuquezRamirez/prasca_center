@extends('Plantilla.Principal')
@section('title', 'Gestionar gastos')
@section('Contenido')
    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h4 class="page-title">Gestionar gastos</h4>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#"><i class="mdi mdi-home-outline"></i></a></li>
                            <li class="breadcrumb-item" aria-current="page">Inicio</li>
                            <li class="breadcrumb-item active" aria-current="page">Gestionar gastos</li>
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
                        <h5 class="card-title">Listado de gastos </h5>
                    </div>
                    <div class="card-body">
                        <strong style="font-size: 20px" id="gastosTotales">Gasto total: $ 45,000.00</strong>
                        <div class="box-controls pull-right">
                            <div class="box-header-actions">

                                <div class="input-group input-group-merge">
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" class="form-control pull-right" id="reservation2">
                                        <input type="text" id="busqueda" class="form-control">
                                        <div class="input-group-text" data-password="false">
                                            <span class="fa fa-search"></span>
                                        </div>
                                        <button type="button" onclick="nuevoRegistro(1);"
                                            class="btn btn-xs btn-primary font-bold"><i class="fa fa-plus"></i> Nuevo
                                            gasto</button>
                                    </div>



                                </div>

                            </div>
                        </div>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th style="width:40%;">Descripción</th>
                                    <th style="width:30%;">Categoria</th>
                                    <th style="width:10%;">Fecha</th>
                                    <th style="width:10%;">valor</th>
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
    <div class="modal fade" id="modalGasto" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 40%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="tituloAccion">Agregar gasto</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                    <form id="formGastos">
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
                                    <label for="fecha" class="form-label">Fecha :</label>
                                    <input type="date" class="form-control" id="fecha" name="fecha">
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label for="categoria" class="form-label">Categoria :</label>
                                    <div class="input-group input-group-merge">
                                        <select class="form-select" id="categoria" name="categoria">
                                        </select>
                                        <div class="input-group-text" title="Agregar una categoria"
                                            onclick="addCategoria()" style="cursor: pointer;" data-password="false">
                                            <span class="fa fa-plus"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="valor" class="form-label">Valor :</label>
                                    <input type="text" class="form-control" id="valorVis" name="valorVis"
                                        onchange="cambioFormato(this.id);" onkeypress="return validartxtnum(event)"
                                        onclick="this.select();">
                                    <input type="hidden" class="form-control" id="valor" name="valor">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="medioPago" class="form-label">Medio de pago:</label>
                                    <select class="form-select" id="medioPago" name="medioPago">
                                        <option value="Efectivo">Efectivo</option>
                                        <option value="Transferencia">Tranferencia</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="referencia" class="form-label">Referencia :</label>
                                    <input type="text" class="form-control" id="referencia" name="referencia">
                                </div>
                            </div>

                            <div class="box-footer text-end">
                                <button type="button" onclick="nuevoRegistro(2);" style="display: none;"
                                    id="newRegistro" class="btn btn-primary-light me-1">
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

    {{--  Modal gestion categorias  --}}
    <div class="modal fade text-left" id="modalCategoria" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Gestionar categorias</h4>

                </div>
                <div class="modal-body">
                    <div class="card-body">

                        <form id="formCategoria">
                            <input type="hidden" name="idCategoria" id="idCategoria" value="" />
                            <input type="hidden" name="accionCate" id="accionCate" value="" />

                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="userinput8">Descripción:</label>
                                        <div class="d-flex align-items-start">
                                            <input type="text" class="form-control" id="descripcionCategoria"
                                                name="descripcionCategoria" placeholder="" value="">
                                            <div class="input-group-append" id="button-addon4">
                                                <button class="btn btn-primary" onclick="guardarCategoria();"
                                                    title="Guardar categoria" type="button"><i
                                                        class="fa fa-check"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <table id="app-invoice-table" class="table" style="width: 100%;">
                                        <thead class="border-bottom border-dark">
                                            <tr>
                                                <th>Descripción</th>
                                                <th>Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody id="trRegistrosCategoria">
                                        </tbody>


                                    </table>
                                </div>

                                <div class="col-12">
                                    <div class="form-actions right">
                                        <button type="button" onclick="cancelarCategoria();"
                                            class="btn btn-warning mr-1">
                                            <i class="fa fa-angle-left"></i>
                                            Salir
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let menuP = document.getElementById("principalRecaudo")
            let menuS = document.getElementById("principalRecaudoGestionGastos")

            menuP.classList.add("active", "menu-open")
            menuS.classList.add("active")

            loader = document.getElementById('loader')
            loadNow(1)

            $('#reservation2').daterangepicker({
                locale: {
                    format: 'DD/MM/YYYY',
                    applyLabel: "Aplicar",
                    cancelLabel: "Cancelar",
                    fromLabel: "Desde",
                    toLabel: "Hasta",
                    customRangeLabel: "Personalizado",
                    weekLabel: "S",
                    daysOfWeek: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"],
                    monthNames: [
                        "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
                        "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"
                    ],
                    firstDay: 1
                }
            });

            $('#reservation2').on('apply.daterangepicker', function(ev, picker) {
                cargar(1)
            });


            $("#formGastos").validate({
                rules: {

                    descripcion: {
                        required: true,
                    },
                    fechafiltro: {
                        required: true,
                    },
                    categoria: {
                        required: true
                    },
                    valor: {
                        required: true
                    },
                    medioPago: {
                        required: true
                    }

                },
                messages: {
                    descripcion: {
                        required: "Por favor, ingrese la descripción del paquete."
                    },
                    fechafiltro: {
                        required: "Por favor, seleccione la fecha del gasto."
                    },
                    categoria: {
                        required: "Por favor, seleccione la categoria del gasto.",
                    },
                    valor: {
                        required: "Por favor, ingrese el valor del gasto."
                    },
                    medioPago: {
                        required: "Por favor, seleccione el medio de pago."
                    }
                },
                submitHandler: function(form) {
                    guardarRegistro()
                }
            });

            cargar(1)
            cargarCategorias()

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

            // Evento input para el campo de búsqueda por fecha
        

        });

        function cargarCategorias() {
            return new Promise((resolve, reject) => {
                let select = document.getElementById("categoria")
                select.innerHTML = ""
                let trCategoria = ""
                document.getElementById('trRegistrosCategoria').innerHTML = ""
                let url = "{{ route('gastos.listaCategorias') }}";

                let defaultOption = document.createElement("option")
                defaultOption.value = "" // Valor en blanco
                defaultOption.text = "Selecciona una opción" // Texto que se mostrará
                defaultOption.selected = true // Que aparezca seleccionada por defecto
                select.appendChild(defaultOption)

                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        data.categorias.forEach(item => {
                            let option = document.createElement("option")
                            option.value = item.id
                            option.text = item.descripcion
                            select.appendChild(option)


                            trCategoria = '<tr id="trC' + item.id + '">' +
                                '<td><span class="invoice-date">' + item
                                .descripcion + '</span></td>' +
                                '<td>' +
                                '    <div class="invoice-action">' +
                                '    <a data-id="' + item.id +
                                '" data-nombre="' + item.descripcion +
                                '" onclick="editarCategoria(this);" title="Editar" class="invoice-action-edit cursor-pointer mr-1">' +
                                '        <i style="cursor:pointer;" class="fa fa-edit"></i>' +
                                '    </a>' +
                                '    <a onclick="eliminarCategoria(' + item
                                .id +
                                ');" title="Eliminar" class="invoice-action-edit cursor-pointer">' +
                                '        <i style="cursor:pointer;" class="fa fa-trash"></i>' +
                                '    </a>' +
                                '    </div>' +
                                '</td>' +
                                '</tr>';
                            document.getElementById('trRegistrosCategoria').innerHTML += trCategoria

                        })
                        resolve() // Resuelve la promesa cuando los datos han sido cargados
                    })
                    .catch(error => {
                        console.error('Error:', error)
                        reject(error) // Rechaza la promesa si ocurre un error
                    })
            })
        }

        function editarRegistro(idGasto) {
            var modal = new bootstrap.Modal(document.getElementById("modalGasto"), {
                backdrop: 'static',
                keyboard: false
            });
            document.getElementById("accRegistro").value = 'editar'
            document.getElementById("idRegistro").value = idGasto
            document.getElementById('saveRegistro').removeAttribute('disabled')

            document.getElementById("tituloAccion").innerHTML = "Editar gasto"

            modal.show();

            let url = "{{ route('gastos.buscarGasto') }}";

            fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        idGasto: idGasto
                    })
                })
                .then(response => response.json())
                .then(data => {
                    document.getElementById("descripcion").value = data.descripcion
                    document.getElementById("fecha").value = data.fecha_gasto
                    document.getElementById("categoria").value = data.categoria
                    document.getElementById("valor").value = data.valor
                    document.getElementById("valorVis").value = formatCurrency(data.valor, 'es-CO', 'COP')
                    document.getElementById("medioPago").value = data.forma_pago
                    document.getElementById("referencia").value = data.referencia

                })
                .catch(error => console.error('Error:', error));
        }


        function eliminarRegistro(idGastos) {
            swal({
                title: "Esta seguro?",
                text: "No podrás recuperar este registro!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#fec801",
                confirmButtonText: "Si, eliminar!",
                cancelButtonText: "No, cancelar!",
                closeOnConfirm: false,
                closeOnCancel: false
            }, function(isConfirm) {
                if (isConfirm) {
                    let url = "{{ route('gastos.eliminarGasto') }}";
                    fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content')
                            },
                            body: JSON.stringify({
                                idGastos: idGastos
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
                                    data.message,
                                    "success");
                            }
                        })

                } else {
                    swal("Cancelado", "Tu registro esta salvo :)", "error");
                }
            });
        }

        function eliminarCategoria(idCategoria) {
            swal({
                title: "Esta seguro?",
                text: "No podrás recuperar este registro!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#fec801",
                confirmButtonText: "Si, eliminar!",
                cancelButtonText: "No, cancelar!",
                closeOnConfirm: false,
                closeOnCancel: false
            }, function(isConfirm) {
                if (isConfirm) {
                    let url = "{{ route('gastos.eliminarCategoria') }}";
                    fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content')
                            },
                            body: JSON.stringify({
                                idCategoria: idCategoria
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                swal("¡Buen trabajo!",
                                    data.message,
                                    "success");
                                cargarCategorias()
                            } else {
                                swal("¡Alerta!",
                                    data.message,
                                    "success");
                            }
                        })

                } else {
                    swal("Cancelado", "Tu registro esta salvo :)", "error");
                }
            });
        }

        function editarCategoria(element) {
            var categoria = element.getAttribute("data-nombre")
            var idcate = element.getAttribute("data-id")
            document.getElementById('descripcionCategoria').value = categoria
            document.getElementById('idCategoria').value = idcate
            document.getElementById('accionCate').value = 'editar'
        }

        function guardarRegistro() {

            if ($("#formGastos").valid()) {

                const formGastos = document.getElementById('formGastos');
                const formData = new FormData(formGastos);

                const url = "{{ route('form.guardarGastos') }}";

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



        function cancelarRegistro() {
            const formGastos = document.getElementById('formGastos')
            formGastos.reset();
        }

        function cambioFormato(id) {
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
                var modal = new bootstrap.Modal(document.getElementById("modalGasto"), {
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


            document.getElementById("tituloAccion").innerHTML = "Agregar gastos"
        }


        function cargar(page, searchTerm = '') {


            let url = "{{ route('gastos.listaGastos') }}"; // Definir la URL

            // Eliminar los campos ocultos anteriores
            var oldPageInput = document.getElementById('page');
            var oldSearchTermInput = document.getElementById('searchTerm');
            if (oldPageInput) oldPageInput.remove();
            if (oldSearchTermInput) oldSearchTermInput.remove();

            let fecha = $('#reservation2').val()
            let fechas = fecha.split(" - ")
            fechas = {
                fechaInicio: fechas[0],
                fechaFin: fechas[1]
            }


            var data = {
                page: page,
                search: searchTerm,
                fecha1: fechas.fechaInicio,
                fecha2: fechas.fechaFin
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
                    document.getElementById('trRegistros').innerHTML = responseData.gastos;
                    feather.replace();
                    // Colocar los enlaces de paginación
                    document.getElementById('pagination-links').innerHTML = responseData.links;
                    loadNow(0);

                    document.getElementById('gastosTotales').innerHTML = 'Gasto total: $' + responseData.gastosTotales
                })
                .catch(error => console.error('Error:', error));

        }

        function addCategoria() {
            var modal = new bootstrap.Modal(document.getElementById("modalCategoria"), {
                backdrop: 'static',
                keyboard: false
            });
            modal.show()
            $('#modalGastos').modal('toggle');
            document.getElementById('accionCate').value = 'guardar'
        }

        function cancelarCategoria() {
            $("#modalGastos").modal({
                backdrop: 'static',
                keyboard: false
            });

            $('#modalCategoria').modal('toggle');
        }

        function guardarCategoria() {
            let url = "{{ route('gastos.guardarCategoria') }}";
            let descripcion = document.getElementById('descripcionCategoria').value;
            let idCategoria = document.getElementById('idCategoria').value;
            let accionCate = document.getElementById('accionCate').value;

            fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        descripcion: descripcion,
                        idCategoria: idCategoria,
                        accionCate: accionCate
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        swal("¡Buen trabajo!", "La operación fue realizada exitosamente.", "success")
                        cargarCategorias()
                        document.getElementById('descripcionCategoria').value = ''
                        document.getElementById('idCategoria').value = ''
                        document.getElementById('accionCate').value = ''
                    } else {
                        console.error('Error en el procesamiento:', data.message)
                    }
                })
                .catch(error => {
                    console.error("Error al enviar los datos:", error)
                });
        }
    </script>

@endsection
