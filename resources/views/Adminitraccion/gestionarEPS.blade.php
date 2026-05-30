@extends('Plantilla.Principal')
@section('title', 'Gestionar Entidades Promotoras')
@section('Contenido')
<div class="content-header">
    <div class="d-flex align-items-center">
        <div class="me-auto">
            <h4 class="page-title">Gestionar Entidades Promotoras </h4>
            <div class="d-inline-block align-items-center">
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#"><i class="mdi mdi-home-outline"></i></a></li>
                        <li class="breadcrumb-item" aria-current="page">Inicio</li>
                        <li class="breadcrumb-item active" aria-current="page">Gestionar entidades Promotoras</li>
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
                    <h5 class="card-title">Listado de Entidades Promotoras </h5>
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
                                    class="btn btn-xs btn-primary font-bold"><i class="fa fa-plus"></i> Nueva
                                    Entidad promotora</button>
                            </div>

                        </div>
                    </div>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th style="width:10%;">Código</th>
                                <th style="width:80%;">Entidad</th>
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
<div class="modal fade" id="modalEPS" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 40%;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="tituloAccion">Agregar entidad</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body">
                <form id="formEntidades">
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
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="nit" class="form-label">NIT :</label>
                                <input type="text" class="form-control" id="nit" name="nit">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nombre" class="form-label">Nombre :</label>
                                <input type="text" class="form-control" id="nombre" name="nombre">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email" class="form-label">Email :</label>
                                <input type="text" class="form-control" id="email" name="email">
                            </div>
                        </div>
                        <div class="col-md-6">
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

<!-- MODAL VENTA ASESORIA   -->
<div class="modal fade" id="modalVentaAsesoria" tabindex="-1" aria-labelledby="modalVentaAsesoriaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 60%;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myLargeModalLabelCotizacion">Venta de asesoria</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="input-group input-group-merge d-flex justify-content-end p-3">

                <button type="button" onclick="cancelarVentaAsesoria();" style="display: none;"
                    id="btnRegresarVentaAsesoria"
                    class="btn btn-secondary">
                    <i class="ti-arrow-left"></i> Regresar
                </button>
                <button type="button" onclick="nuevaVentaAsesoria();" id="newVentaAsesoria"
                    class="btn btn-primary">
                    <i class="ti-plus"></i> Nueva venta
                </button>
            </div>
            <div class="modal-body">
                <div id="listadoVentaAsesoria">
                    <table id="tablaVentaAsesoria" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Asesoria</th>
                                <th>Valor</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="trRegistrosVentaAsesoria">


                        </tbody>
                    </table>
                    <div id="pagination-links-ventaAsesoria"></div>
                </div>
                <div id="ventaAsesoriaForm" style="display: none;">
                    <form id="formVentaAsesoria">
                        <input type="hidden" id="idVentaAsesoria" name="idVentaAsesoria" />
                        <input type="hidden" id="accVentaAsesoria" name="accVentaAsesoria" />
                        <input type="hidden" id="idEPS" name="idEPS" />
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="tipoAsesoria" class="form-label">tipo de asesoria :</label>
                                    <select class="form-control select2" id="tipoAsesoria" onchange="cargarAsesoria(this)" name="tipoAsesoria">
                                        <option value="">Seleccione el tipo de asesoria</option>


                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="descripcion" class="form-label">Fecha :</label>
                                    <input type="date" class="form-control" min="1" id="fechaAsesoria" name="fechaAsesoria" value="1">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="descripcion" class="form-label">Valor :</label>
                                    <input type="text" class="form-control" placeholder="$ 0,00"
                                        onchange="cambioFormato(this.id);" onkeypress="return validartxtnum(event)"
                                        onclick="this.select();" id="valorAsesoriaVis" name="valorAsesoriaVis" value="$0,00">
                                    <input type="hidden" class="form-control" id="valorAsesoria" name="valorAsesoria" value="0">

                                </div>
                            </div>

                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12 text-end">
                                <button class="btn btn-secondary" type="button" onclick="cancelarVentaAsesoria();"> <i class="ti-close"></i> Cancelar</button>
                                <button class="btn btn-primary" type="button" onclick="guardarVentaAsesoria()"> <i class="ti-save"></i> Guardar cotización</button>
                            </div>
                        </div>

                </div>
            </div>
        </div>
    </div>
</div>


<script>
    document.addEventListener("DOMContentLoaded", function() {
        let menuP = document.getElementById("principalParametros");
        let menuS = document.getElementById("principalParametrosEPS");

        menuP.classList.add("active", "menu-open");
        menuS.classList.add("active");

        loader = document.getElementById('loader');
        loadNow(1);

        $("#formEntidades").validate({
            rules: {
                nombre: {
                    required: true
                },
                email: {
                    required: true,
                    email: true
                }
            },
            messages: {
                nombre: {
                    required: "Por favor, ingrese el nombre de la entidad."
                },             
                email: {
                    required: "Por favor, ingrese el email de la entidad.",
                    email: "Por favor, ingrese un email válido."
                }
            },
            submitHandler: function(form) {
                guardarRegistro();
            }
        });



        cargar(1);
        cargarAsesorias();

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

    function editarRegistroVenta(idRegistro) {
   
        document.getElementById('idVentaAsesoria').value = idRegistro;
        document.getElementById('accVentaAsesoria').value = 'editar';

        let url = "{{ route('asesorias.buscaVentaAsesoria') }}";
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
            document.getElementById('listadoVentaAsesoria').style.display = 'none';
            document.getElementById('ventaAsesoriaForm').style.display = 'block';
            document.getElementById('btnRegresarVentaAsesoria').style.display = 'block';
            document.getElementById('newVentaAsesoria').style.display = 'none';

            document.getElementById('tipoAsesoria').value = data.id_tipo_servicio;
            document.getElementById('fechaAsesoria').value = data.fecha.split(' ')[0];
            document.getElementById('valorAsesoria').value = data.precio;
            document.getElementById('valorAsesoriaVis').value = formatCurrency(data.precio, 'es-CO', 'COP');

        })
        .catch(error => console.error('Error:', error));
    }

    function verServiciosVenta(idRegistro) {
        var modal = new bootstrap.Modal(document.getElementById("modalVentaAsesoria"), {
            backdrop: 'static',
            keyboard: false
        });
        modal.show();
        document.getElementById('idEPS').value = idRegistro;

        cagarServiciosVenta(idRegistro);

    }

    function cagarServiciosVenta(idRegistro) {
        let url = "{{ route('asesorias.listaServiciosVenta') }}";
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
            document.getElementById('trRegistrosVentaAsesoria').innerHTML = data;
        })
        .catch(error => console.error('Error:', error));
    }

    function cargarAsesorias() {
        let url = "{{ route('asesorias.listaAsesoriasSelect') }}";
        fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {

                let html = '';
                html += '<option value="">Seleccione la asesoria</option>';
                data.forEach(item => {
                    html += '<option data-valor="' + item.valor + '" data-tiempo="' + item.tiempo + '" value="' + item.id + '">' + item.descripcion + ' - ' + item.tiempo + '</option>';
                });

                document.getElementById('tipoAsesoria').innerHTML = html;
            })
            .catch(error => console.error('Error:', error));
    }

    function cancelarVentaAsesoria() {
        document.getElementById('ventaAsesoriaForm').style.display = 'none';
        document.getElementById('listadoVentaAsesoria').style.display = 'block';
        document.getElementById('btnRegresarVentaAsesoria').style.display = 'none';
        document.getElementById('newVentaAsesoria').style.display = 'block';
        document.getElementById('saveRegistro').style.display = 'block';
    }

    function nuevaVentaAsesoria() {
        document.getElementById('ventaAsesoriaForm').style.display = 'block';
        document.getElementById('listadoVentaAsesoria').style.display = 'none';
        document.getElementById('btnRegresarVentaAsesoria').style.display = 'block';
        document.getElementById('newVentaAsesoria').style.display = 'none';
        document.getElementById('saveRegistro').style.display = 'none';
        document.getElementById('cancelRegistro').style.display = 'none';
        document.getElementById('formVentaAsesoria').reset();
        document.getElementById('idVentaAsesoria').value = '';
        document.getElementById('accVentaAsesoria').value = 'guardar';        

    }

    function cargarAsesoria(select) {
        let valor = select.options[select.selectedIndex].getAttribute('data-valor');
        let tiempo = select.options[select.selectedIndex].getAttribute('data-tiempo');
        document.getElementById('valorAsesoria').value = valor;
        document.getElementById('valorAsesoriaVis').value = formatCurrency(valor, 'es-CO', 'COP');
        document.getElementById('fechaAsesoria').value = tiempo;
    }

    function cambioFormato(id) {
        let numero = document.getElementById(id)
        document.getElementById("valorAsesoria").value = numero.value
        let formatoMoneda = formatCurrency(numero.value, 'es-CO', 'COP')
        numero.value = formatoMoneda

    }

    function guardarVentaAsesoria() {
        let formVentaAsesoria = document.getElementById('formVentaAsesoria');
        let formData = new FormData(formVentaAsesoria);
        let url = "{{ route('asesorias.guardarVentaAsesoria') }}";
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
                    swal("¡Buen trabajo!", "La operación fue realizada exitosamente.", "success")
                    cancelarVentaAsesoria();
                    cagarServiciosVenta(document.getElementById('idEPS').value);
                } else {
                    swal("¡Alerta!", "No se realizo ningun cambio", "warning");
                }
            })
            .catch(error => console.error('Error:', error));
    }

    function eliminarRegistroVenta(idRegistro) {
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
        let url = "{{ route('asesorias.eliminarVentaAsesoria') }}";
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
            if (data.success) {
                swal("¡Buen trabajo!", "La operación fue realizada exitosamente.", "success")
                cagarServiciosVenta(document.getElementById('idEPS').value);
            } else {
                swal("¡Alerta!", "No se realizo ningun cambio", "warning");
            }
        })
        .catch(error => console.error('Error:', error));
            } else {
                swal("Cancelado", "Tu registro esta salvo :)", "error");
            }
        });
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


    function guardarRegistro() {

        if ($("#formEntidades").valid()) {

            const formEntidades = document.getElementById('formEntidades');
            const formData = new FormData(formEntidades);

            const url = "{{ route('form.guardarEntidades') }}";

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
        var modal = new bootstrap.Modal(document.getElementById("modalEPS"), {
            backdrop: 'static',
            keyboard: false
        });
        document.getElementById("accRegistro").value = 'editar'
        document.getElementById("idRegistro").value = idRegistro
        document.getElementById('saveRegistro').removeAttribute('disabled')

        document.getElementById("tituloAccion").innerHTML = "Editar entidad promotora"

        modal.show();

        let url = "{{ route('entidades.buscaEntidad') }}";

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
                document.getElementById("nombre").value = data.entidad
                document.getElementById("codigo").value = data.codigo
                document.getElementById("nit").value = data.nit
                document.getElementById("email").value = data.email
                document.getElementById("telefono").value = data.telefono
                document.getElementById("observaciones").value = data.observaciones

            })
            .catch(error => console.error('Error:', error));

    }



    function cancelarRegistro() {
        const formEntidades = document.getElementById('formEntidades');
        formEntidades.reset();
    }

    function nuevoRegistro(opc) {

        if (opc == 1) {
            var modal = new bootstrap.Modal(document.getElementById("modalEPS"), {
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


        document.getElementById("tituloAccion").innerHTML = "Agregar entidad promotora"

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
                let url = "{{ route('entidades.eliminarEntidad') }}";
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

        let url = "{{ route('entidades.listaEntidades') }}"; // Definir la URL

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
                document.getElementById('trRegistros').innerHTML = responseData.entidades;
                feather.replace();
                // Colocar los enlaces de paginación
                document.getElementById('pagination-links').innerHTML = responseData.links;
                loadNow(0);
            })
            .catch(error => console.error('Error:', error));

    }
</script>

@endsection