@extends('Plantilla.Principal')
@section('title', 'Gestionar cajas')
@section('Contenido')
    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h4 class="page-title">Gestionar cajas</h4>
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
                        <h5 class="card-title">Listado de cajas </h5>
                    </div>
                    <div class="card-body">
                        <div class="box-controls pull-right">
                            <div class="box-header-actions">
                                <div class="input-group input-group-merge">
                                    {{-- <input type="date" value="" id="fechafiltro" class="form-control"> --}}
                                    <input type="text" id="busqueda" class="form-control">
                                    <div class="input-group-text" data-password="false">
                                        <span class="fa fa-search"></span>
                                    </div>
                                    <button type="button" onclick="nuevoRegistro(1);"
                                        class="btn btn-xs btn-primary font-bold"><i class="fa fa-plus"></i> Nueva
                                        caja</button>
                                </div>

                            </div>
                        </div>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Apertura</th>
                                    <th>Cierre</th>
                                    <th>Saldo inicial</th>
                                    <th>Recaudado</th>
                                    <th>Gastos</th>
                                    <th>Estado</th>
                                    <th>Opciones</th>
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
    <!-- MODAL CAJA -->
    <div class="modal fade" id="modalCaja" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 40%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="tituloAccion">Agregar caja</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                    <form id="formCaja">
                        <input type="hidden" name="idCaja" id="idCaja" value="" />
                        <input type="hidden" name="accion" id="accion" value="">
                        <div class="form-body">
                            <h4 class="form-section"><i class="fa fa-calculator"></i> Información basica de caja
                            </h4>
                        </div>

                        <div class="row">
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="saldoAnteVis">Saldo caja anterior:</label>
                                    <input type="text" class="form-control" disabled id="saldoAnteVis"
                                        name="saldoAnteVis" placeholder="" value="">
                                    <input type="hidden" value="" id="saldoAnte" name="saldoAnte">
                                </div>
                            </div>

                            <div class="col-4">
                                <div class="form-group">
                                    <label for="abonoIni">Abono Inicial:</label>
                                    <input type="text" onchange="cambioFormato(this.id);"
                                        onkeypress="return validartxtnum(event)" value="$ 0,00" onclick="this.select()"
                                        class="form-control" id="abonoIni" name="abonoIni">
                                    <input type="hidden" value="0" id="abono" name="abono">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="fechaApertura">Fecha apertura:</label>
                                    <input type="date" class="form-control" id="fechaApertura" name="fechaApertura"
                                        placeholder="" value="">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="responsable">Responsable Caja:</label>
                                    <input type="text" class="form-control" id="responsable" name="responsable"
                                        placeholder="" disabled value="{{ Auth::user()->nombre_usuario }}">
                                </div>
                            </div>

                            <div class="box-footer text-end">
                                <button type="button" onclick="salirCaja();" class="btn btn-warning mr-1">
                                    <i class="fa fa-reply"></i> Salir
                                </button>
                                <button type="button" id="btnGuardar" onclick="guardarCaja()" class="btn btn-success">
                                    <i class="fa fa-check-square-o"></i> Abrir Caja
                                </button>
                                <button type="button" id="btnNuevo" style="display: none;" onclick="nuevaCaja()"
                                    class="btn btn-primary">
                                    <i class="feather icon-plus"></i> Nuevo
                                </button>
                            </div>

                        </div>
                    </form>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
    </div><!-- /.modal -->

    {{--  Modal detalle caja  --}}
    <div class="modal fade text-left" id="modaldetCaja" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title text-bold-600" id="tituloCaja"></h4>
                    <h2 class="text-primary" id="valTotalGeneral">$ 0,00</h2>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                        <div class="row invoice-adress-info py-2 p-2" style="width: 100%;">
                            <div class="col-4 mt-1 from-info">
                                <div class="company-name mb-1">
                                    <span class="font-weight-bold no-wrap"><strong> Fecha de apertura:</strong>
                                    </span><br>
                                    <span id="fecApertura"></span>
                                </div>
                            </div>
                            <div class="col-4 mt-1 from-info">
                                <div id="div-infoCierre" class="company-name mb-1" style="display: none;">
                                    <span class="font-weight-bold no-wrap"><strong>Fecha de cierre:</strong>
                                    </span><br>
                                    <span id="fecCierre"></span>
                                </div>
                            </div>
                            <div class="col-4 mt-1 to-info">
                                <div class="company-name mb-1">
                                    <span class="font-weight-bold no-wrap"><strong>Usuario apertura: </strong> </span><br>
                                    <span id="usuApertura"></span>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <tbody>
                                    <tr>
                                        <td style="width: 70%">Saldo anterior</td>
                                        <td></td>
                                        <td></td>
                                        <td style="text-align:right;" id="infSaldoAnterior">$ 0,00</td>
                                    </tr>
                                    <tr>
                                        <td style="width: 70%">Abono inicial</td>
                                        <td></td>
                                        <td></td>
                                        <td style="text-align:right;" id="infAbonoInicial">$ 0,00</td>
                                    </tr>
                                </tbody>
                                <tfoot class="bg-teal bg-lighten-4 height-50">
                                    <tr>
                                        <th style="width: 70%">Saldo inicial total</th>
                                        <th></th>
                                        <th></th>
                                        <th style="text-align:right;" id="infSaldoInicial">$ 0,00</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <tbody id="tr-mediosPago">

                                </tbody>
                                <tfoot class="bg-teal bg-lighten-4 height-50">
                                    <tr>
                                        <th style="width: 70%">Recaudos</th>
                                        <th></th>
                                        <th></th>
                                        <th style="text-align:right;" id="totalMedioPago">$ 0,00</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <tbody id="tr-mediosPago">

                                </tbody>

                            </table>
                        </div>
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <tbody>
                                    <tr>
                                        <td style="width: 70%">Gastos Efectivo (-)</td>
                                        <td></td>
                                        <td></td>
                                        <td style="text-align:right;" id="infGastos">$ 0,00</td>
                                    </tr>
                                    <tr>
                                        <td style="width: 70%">Gastos Transferencia (-)</td>
                                        <td></td>
                                        <td></td>
                                        <td style="text-align:right;" id="infGastosT">$ 0,00</td>
                                    </tr>

                                </tbody>
                                <tfoot class="bg-teal bg-lighten-4 height-50">
                                    <tr>
                                        <th style="width: 70%">Total gastos:
                                        </th>
                                        <th></th>
                                        <th></th>
                                        <th style="text-align:right;" id="infTotalGastosIni">$ 0,00</th>
                                    </tr>
                                    <tr>
                                        <th style="width: 70%">Total caja efectivo (saldo inicial + recaudado -
                                            gastos):
                                        </th>
                                        <th></th>
                                        <th></th>
                                        <th style="text-align:right;" id="infTotalCaja">$ 0,00</th>
                                    </tr>
                                    <tr>
                                        <th style="width: 70%">Total caja Transferencia (recaudado - gastos):
                                        </th>
                                        <th></th>
                                        <th></th>
                                        <th style="text-align:right;" id="infTotalCajaT">$ 0,00</th>
                                    </tr>
                                </tfoot>

                            </table>
                        </div>
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <tfoot id="div-saldoCierre" style="display: none;"
                                    class="bg-teal bg-lighten-4 height-50">
                                    <tr>
                                        <th style="width: 70%">Saldo cierre::
                                        </th>
                                        <th></th>
                                        <th></th>
                                        <th style="text-align:right;" id="infSaldoCierre">$ 0,00</th>
                                    </tr>
                                </tfoot>

                            </table>
                        </div>
                        <hr>

                        <h4>Detalle de Recaudos</h4>
                        <div class="table-responsive" style="height: auto;">
                            <table class="table mb-0">
                                <thead class="border-bottom border-dark">
                                    <tr>
                                        <th style="width: 3%;">No.</th>
                                        <th style="width: 37%;">Paciente</th>
                                        <th style="width: 15%;">Fecha de pago</th>
                                        <th style="width: 20%;">Medio de Pago</th>
                                        <th style="width: 10%;">Referencia</th>
                                        <th style="width: 15%;text-align: right;">Valor pagado</th>
                                    </tr>
                                </thead>
                                <tbody id="tr_detaRecaudo">

                                </tbody>
                                <tfoot class="bg-teal bg-lighten-4 height-50">
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th>Total</th>
                                        <th id="infTotalRecaudos" style="text-align: right;">$ 0,00</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <hr>
                        <h4>Detalle de gastos</h4>
                        <div class="table-responsive" style="height: auto;">
                            <table class="table mb-0">
                                <thead class="border-bottom border-dark">
                                    <tr>
                                        <th style="width: 5%;">No.</th>
                                        <th style="width: 55%;">Descripción</th>
                                        <th style="width: 15%;">Fecha de gasto</th>
                                        <th style="width: 15%;">Medio de pago</th>
                                        <th style="width: 15%;">Referencia</th>
                                        <th style="width: 15%;text-align: right;">Valor pagado</th>
                                    </tr>
                                </thead>
                                <tbody id="tr_detaGastos">

                                </tbody>
                                <tfoot class="bg-teal bg-lighten-4 height-50">
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th>Total</th>
                                        <th id="infTotalGastos" style="text-align: right;">$ 0,00</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-12" style="text-align: right">
                                <div class="form-actions right">
                                    <button type="button" onclick="salirDetCaja()" class="btn btn-warning ">
                                        <i class="fa fa-reply"></i> Salir
                                    </button>
                                    <button type="button" onclick="imprimir()" class="btn btn-info ">
                                        <i class="fa fa-print"></i> Imprimir
                                    </button>
                                    <button type="button" id="btn-cierre" onclick="cerrarCaja();"
                                        class="btn btn-primary">
                                        <i class="fa fa-check-square-o"></i> Cerrar caja
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{--  Modal cierre caja  --}}
    <div class="modal fade text-left" id="modalCierre" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Cierre de caja</h4>

                </div>
                <div class="modal-body">
                    <div class="card-body">

                        <form id="formGuardarCierreCaja">
                            @csrf
                            <div class="row">
                                <div class="col-6">
                                    <label for="userinput8">Fecha de ciere:</label>

                                    <div class="form-group d-flex align-items-center position-relative">
                                        <!-- date picker -->
                                        <div class="date-icon mr-50 font-medium-3">

                                            <i class='feather icon-calendar'></i>

                                        </div>
                                        <div class="date-picker">
                                            <input type="date" id="fecCierre" name="fecCierre" cplaceholder="">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="userinput8">Monto en caja:</label>
                                        <input type="text" onchange="cambioFormato(this.id);"
                                            onkeypress="return validartxtnum(event)" onclick="this.select()"
                                            class="form-control" id="valorVisMontoCierre" value="0,00"
                                            name="valorVisMontoCierre">
                                        <input type="hidden" value="" id="valorMontoCierre"
                                            name="valorMontoCierre">
                                        <input type="hidden" value="" id="valorMontoGastos"
                                            name="valorMontoGastos">
                                        <input type="hidden" value="" id="valorMontoGastosT"
                                            name="valorMontoGastosT">
                                        <input type="hidden" value="" id="valorMontoRecaudos"
                                            name="valorMontoRecaudos">
                                        <input type="hidden" name="idCajaCierre" id="idCajaCierre" value="" />

                                    </div>
                                </div>
                                <div class="col-12" style="text-align: right">
                                    <div class="form-actions right">
                                        <button type="button" onclick="salirConfcierre();" class="btn btn-warning mr-1">
                                            <i class="fa fa-reply"></i>
                                            Salir
                                        </button>
                                        <button type="button" onclick="confirmarCierre();" class="btn btn-success mr-1">
                                            <i class="fa fa-check-square-o"></i>
                                            Confirmar cierre
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
            let menuS = document.getElementById("principalRecaudoGestionCaja")

            menuP.classList.add("active", "menu-open")
            menuS.classList.add("active")

            let respuestaGlobal;

            loader = document.getElementById('loader')
            loadNow(1)

            $("#formCaja").validate({
                rules: {
                    fechaApertura: {
                        required: true,
                    }
                },
                messages: {
                    fechaApertura: {
                        required: "Por favor, ingrese la fecha de apertura.."
                    },
                },
                submitHandler: function(form) {
                    guardarCaja()
                }
            });

            $("#formGuardarCierreCaja").validate({
                rules: {
                    fecCierre: {
                        required: true,
                    }
                },
                messages: {
                    fecCierre: {
                        required: "Por favor, ingrese la fecha de cierre.."
                    },
                },
                submitHandler: function(form) {
                    confirmarCierre()
                }
            });

            cargar(1)

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

        function salirCaja() {
            $('#modalCaja').modal('hide');
            limpiar();
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

        function guardarCaja() {

            if ($("#formCaja").valid()) {

                const formCaja = document.getElementById('formCaja');
                const formData = new FormData(formCaja);

                const url = "{{ route('cajas.guardarCaja') }}";

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
                            document.getElementById('btnGuardar').setAttribute('disabled', 'disabled')
                            cargar(1)
                            document.getElementById("accion").value = "guardar"

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
            if (numero.value == '') {
                numero.value = 0
            }
            document.getElementById("abono").value = numero.value
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
            var modal = new bootstrap.Modal(document.getElementById("modalCaja"), {
                backdrop: 'static',
                keyboard: false
            });
            modal.show()
            document.getElementById('accion').value = 'guardar'
        }

        function limpiar() {
            document.getElementById('abonoIni').value = '$ 0,00'
            document.getElementById('abono').value = '0'
            document.getElementById('fechaApertura').value = ''
            document.getElementById('accion').value = 'guardar'
        }

        function cargar(page, searchTerm = '') {

            let url = "{{ route('cajas.listaCajas') }}"; // Definir la URL

            var data = {
                page: page,
                search: searchTerm
            };

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
                    document.getElementById('trRegistros').innerHTML = responseData.cajas;
                    feather.replace();
                    // Colocar los enlaces de paginación
                    document.getElementById('pagination-links').innerHTML = responseData.links;
                    loadNow(0);

                    $("#saldoAnte").val(responseData.saldoAnterior);
                    $("#saldoAnteVis").val(formatCurrency(responseData.saldoAnterior,
                        'es-CO', 'COP'));

                })
                .catch(error => console.error('Error:', error));


        }

        function verDetalle(idCaja) {
            document.getElementById("idCajaCierre").value = idCaja
            var modal = new bootstrap.Modal(document.getElementById("modaldetCaja"), {
                backdrop: 'static',
                keyboard: false
            });
            modal.show()

            let url = "{{ route('cajas.detalleCaja') }}";

            fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        idCaja: idCaja
                    })
                })
                .then(response => response.json())
                .then(data => {
                    respuestaGlobal = data
                    document.getElementById("tituloCaja").innerHTML = `Caja # ${agregarCeros(data.caja.id, 5)}`
                    document.getElementById("fecApertura").innerHTML = convertirFecha(data.caja.fecha_apertura)
                    document.getElementById("usuApertura").innerHTML = data.caja.nombre_usuario
                    document.getElementById("infSaldoAnterior").innerHTML = formatCurrency(data.caja.saldo_anterior,
                        'es-CO', 'COP')
                    document.getElementById("infAbonoInicial").innerHTML = formatCurrency(data.caja.abono_inicial,
                        'es-CO', 'COP')
                    document.getElementById("infSaldoInicial").innerHTML = formatCurrency(data.caja.saldo_inicial,
                        'es-CO', 'COP')

                    if (data.caja.estado_caja == 'Cerrada') {
                        document.getElementById("btn-cierre").style.display = 'none'
                        document.getElementById("div-saldoCierre").style.display = 'initial'
                        document.getElementById("div-infoCierre").style.display = 'initial'

                        document.getElementById("infSaldoCierre").innerHTML = formatCurrency(data.caja.saldo_cierre,
                            'es-CO', 'COP')
                        document.getElementById("fecCierre").innerHTML = convertirFecha(data.caja.fecha_cierre)

                    } else {
                        document.getElementById("btn-cierre").style.display = 'initial'
                        document.getElementById("div-saldoCierre").style.display = 'none'
                        document.getElementById("div-infoCierre").style.display = 'none'
                    }

                    var totals = {};

                    data.recaudos.forEach(function(recaudo) {
                        // Mapear los medios de pago a nombres deseados
                        var medioPagoNombre = {
                            "e": "Efectivo",
                            "tc": "Tarjeta de crédito",
                            "td": "Tarjeta de débito",
                            "t": "Transferencia"
                            // Puedes agregar más mapeos según tus necesidades
                        } [recaudo.medio_pago];

                        // Obtener o crear la entrada para el medio de pago
                        if (!totals[medioPagoNombre]) {
                            totals[medioPagoNombre] = {
                                count: 0,
                                total: 0
                            };
                        }

                        // Incrementar la cantidad y sumar al total
                        totals[medioPagoNombre].count++;
                        totals[medioPagoNombre].total += parseInt(recaudo
                            .valor);
                    });

                    let mediosPago = '';
                    let totalMedioPago = 0;
                    let totalMedioPagoT = 0;

                    for (var medioPagoNombre in totals) {
                        mediosPago += '<tr>' +
                            '<td style="width: 70%">' + medioPagoNombre + ' (' +
                            totals[medioPagoNombre].count + ')</td>' +
                            '<td></td>' +
                            '<td></td>' +
                            '<td style="text-align: right;">' + formatCurrency(totals[medioPagoNombre].total,
                                'es-CO', 'COP') + '</td>' +
                            '</tr>';

                        if (medioPagoNombre == 'Efectivo') {
                            totalMedioPago += totals[medioPagoNombre].total;

                        } else {
                            totalMedioPagoT += totals[medioPagoNombre].total;
                        }
                    }

                    let totalRecaudos = totalMedioPago + totalMedioPagoT;

                    document.getElementById("totalMedioPago").innerHTML = formatCurrency(totalRecaudos, 'es-CO', 'COP');
                    document.getElementById("valorMontoRecaudos").value = totalRecaudos
                    document.getElementById("tr-mediosPago").innerHTML = mediosPago

                    var totalEfectivo = 0;
                    var totalTransferencia = 0;

                    data.gastos.forEach(function(gasto) {
                        // Sumar al total correspondiente
                        if (gasto.forma_pago === "Efectivo") {
                            totalEfectivo += parseInt(gasto.valor);
                        } else if (gasto.forma_pago === "Transferencia") {
                            totalTransferencia += parseInt(gasto.valor);
                        }
                    });

                    document.getElementById("infGastos").innerHTML = formatCurrency(totalEfectivo, 'es-CO', 'COP')
                    document.getElementById("infGastosT").innerHTML = formatCurrency(totalTransferencia, 'es-CO', 'COP')
                  
                    let totalCaja = (parseInt(data.caja.saldo_inicial) + parseInt(totalMedioPago)) 

                    let totalCajaT = totalMedioPagoT - totalTransferencia;

                    totalCaja = totalCaja - parseInt(totalEfectivo)
                    let totalGastos = totalEfectivo + totalTransferencia
                    let totalCierre = totalCaja + (totalCajaT)
                    document.getElementById("valorMontoCierre").value = totalCierre
                    document.getElementById("valorVisMontoCierre").value = formatCurrency(totalCierre, 'es-CO', 'COP')

                    document.getElementById("infTotalCaja").innerHTML = formatCurrency(totalCaja, 'es-CO', 'COP')
                    document.getElementById("infTotalCajaT").innerHTML = formatCurrency(totalCajaT, 'es-CO', 'COP')
                    document.getElementById("valTotalGeneral").innerHTML = formatCurrency(totalCierre, 'es-CO', 'COP')
                    document.getElementById("infTotalGastosIni").innerHTML = formatCurrency(totalGastos, 'es-CO', 'COP')

                    ////MOSTRAR DETALLES RECAUDO
                    let detaRecaudos = '';
                    let referencia = '';
                    var medioPagoNombre;
                    let totalRecaudosDet = 0;

                    data.recaudos.forEach(function(recaudo, index) {
                        // Mapear los medios de pago a nombres deseados
                        medioPagoNombre = {
                            "e": "Efectivo",
                            "tc": "Tarjeta de crédito",
                            "td": "Tarjeta de débito",
                            "t": "Transferencia"
                            // Puedes agregar más mapeos según tus necesidades
                        } [recaudo.medio_pago];

                        // Obtener o crear la entrada para el medio de pago
                        if (!totals[medioPagoNombre]) {
                            totals[medioPagoNombre] = {
                                count: 0,
                                total: 0
                            };
                        }

                        referencia = recaudo.referencia = recaudo
                            .referencia !== null ? recaudo.referencia :
                            "---";


                        // Incrementar la cantidad y sumar al total
                        totals[medioPagoNombre].count++;
                        totals[medioPagoNombre].total += parseInt(recaudo
                            .valor);

                        detaRecaudos += '<tr>' +
                            '<td>' + (index + 1) + '</td>' +
                            '<td >' + recaudo.primer_nombre + " " + recaudo.primer_apellido + ' (' + recaudo
                            .servicio + ')</td>' +
                            '<td>' + convertirFecha(recaudo.fecha_pago) + '</td>' +
                            '<td>' + medioPagoNombre + '</td>' +
                            '<td>' + referencia + '</td>' +
                            '<td style="text-align: right;">' + formatCurrency(recaudo.valor,
                                'es-CO', 'COP') + '</td>' +
                            '</tr>';

                        totalRecaudosDet += parseInt(recaudo.valor);
                    });

                    document.getElementById("infTotalRecaudos").innerHTML = formatCurrency(totalRecaudosDet, 'es-CO',
                        'COP');
                    document.getElementById("tr_detaRecaudo").innerHTML = detaRecaudos;

                    ////MOSTRAR DETALLES GASTOS
                    let detaGastos = '';
                    let referenciaG = '';
                    var medioPagoNombreG;
                    let totalGastosDet = 0;
                    $.each(data.gastos, function(i, item) {


                        referenciaG = item.referencia = item
                            .referencia !== null ? item.referencia :
                            "---";

                        detaGastos += '<tr>' +
                            '<td>' + agregarCeros(item.id, 5) + '</td>' +
                            '<td>' + item.ncategoria + ": " + item
                            .descripcion + '</td>' +
                            '<td>' + convertirFecha(item.fecha_gasto) +
                            '</td>' +
                            '<td>' + item.forma_pago + '</td>' +
                            '<td>' + referenciaG + '</td>' +
                            '<td>' + formatCurrency(item.valor, 'es-CO',
                                'COP') + '</td>' +
                            '</tr>'
                        totalGastosDet += parseInt(item.valor);

                    });

                    document.getElementById("infTotalGastos").innerHTML = formatCurrency(totalGastosDet, 'es-CO', 'COP')
                    document.getElementById("tr_detaGastos").innerHTML = detaGastos
                    document.getElementById("valorMontoGastos").value = totalGastosDet
                })
        }

        function salirDetCaja() {
            $('#modaldetCaja').modal('hide');
        }

        function agregarCeros(numero, longitud) {

            return numero.toString().padStart(longitud, '0');
        }

        function convertirFecha(fecha) {
            // Dividir la fecha en año, mes y día
            const [año, mes, dia] = fecha.split('-')
            /**
             * 
             **/
            //Formatear la fecha en el formato dd/mm/yyyy
            const fechaFormateada = `${dia.padStart(2, '0')}/${mes.padStart(2, '0')}/${año}`

            return fechaFormateada
        }

        function cerrarCaja() {
            var modal = new bootstrap.Modal(document.getElementById("modalCierre"), {
                backdrop: 'static',
                keyboard: false
            });
            modal.show()

            // cerrar modal detalle caja
            $('#modaldetCaja').modal('hide');

        }

        function salirConfcierre() {
            $('#modalCierre').modal('hide');
            // abrir modal detalle caja
            var modal = new bootstrap.Modal(document.getElementById("modaldetCaja"), {
                backdrop: 'static',
                keyboard: false
            });
            modal.show()
        }

        function confirmarCierre() {
            //confirmar si desea cerrar la caja
            if ($("#formGuardarCierreCaja").valid()) {
                swal({
                    title: "Esta seguro?",
                    text: "No podrás recuperar este registro!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#fec801",
                    confirmButtonText: "Si, cerrar caja!",
                    cancelButtonText: "No, cancelar!",
                    closeOnConfirm: false,
                    closeOnCancel: false
                }, function(isConfirm) {
                    if (isConfirm) {
                        let url = "{{ route('cajas.cerrarCaja') }}";
                        const formGuardarCierreCaja = document.getElementById('formGuardarCierreCaja');
                        const formData = new FormData(formGuardarCierreCaja);

                        fetch(url, {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                        .getAttribute(
                                            'content')
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    swal("¡Buen trabajo!",
                                        data.message,
                                        "success");
                                    $('#modalCierre').modal('hide');
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
        }

        function imprimir(){
            var totals = {};
            let totalRecaudosDet = 0;
                    respuestaGlobal.recaudos.forEach(function(recaudo) {
                        // Mapear los medios de pago a nombres deseados
                        var medioPagoNombre = {
                            "e": "Efectivo",
                            "tc": "Tarjeta de crédito",
                            "td": "Tarjeta de débito",
                            "t": "Transferencia"
                            // Puedes agregar más mapeos según tus necesidades
                        } [recaudo.medio_pago];

                        // Obtener o crear la entrada para el medio de pago
                        if (!totals[medioPagoNombre]) {
                            totals[medioPagoNombre] = {
                                count: 0,
                                total: 0
                            };
                        }

                        // Incrementar la cantidad y sumar al total
                        totals[medioPagoNombre].count++;
                        totals[medioPagoNombre].total += parseInt(recaudo
                            .valor);
                        totalRecaudosDet += parseInt(recaudo.valor);
                    });


                    let totalMedioPago = 0;
                    let totalMedioPagoT = 0;

                    for (var medioPagoNombre in totals) {
                        if (medioPagoNombre == 'Efectivo') {
                            totalMedioPago += totals[medioPagoNombre].total;
                        } else {
                            totalMedioPagoT += totals[medioPagoNombre].total;
                        }
                    }


                    var totalEfectivo = 0;
                    var totalTransferencia = 0;
                    let totalGastosDet = 0;
                    // Recorrer el arreglo de gastos
                    respuestaGlobal.gastos.forEach(function(gasto) {
                        // Sumar al total correspondiente
                        if (gasto.forma_pago === "Efectivo") {
                            totalEfectivo += parseInt(gasto.valor);
                        } else if (gasto.forma_pago === "Transferencia") {
                            totalTransferencia += parseInt(gasto.valor);
                        }
                        totalGastosDet += parseInt(gasto.valor);
                    });


                    // total caja
                    let totalCaja = parseInt(respuestaGlobal.caja.saldo_inicial) + parseInt(
                        totalMedioPago);
                    totalCaja = totalCaja - parseInt(totalEfectivo)
                   

                    let totalCajaT = totalMedioPagoT - totalTransferencia

                    let totalGastos = totalEfectivo + totalTransferencia
                    let totalCierre = totalCaja + (totalCajaT)


                    let colorCaja = "";
                    let estadoCaja = "";
                    if (respuestaGlobal.caja.estado_caja == "Cerrada") {
                        estadoCaja = "Estado Cerrada";
                        colorCaja = "#FF4545";
                    } else {
                        estadOCaja = "Estado Abierta";
                        colorCaja = "#16D39A";

                    }


                    var docDefinition = {
                        content: [{

                                columns: [{
                                        text: "",
                                        style: 'title'
                                    },
                                    {
                                        text: estadoCaja,
                                        style: 'colorTextoCaja',
                                    },
                                ]

                            },
                            {
                                style: 'header',
                                columns: [

                                    {
                                        text: "Caja #" + agregarCeros(respuestaGlobal.caja
                                            .id, 5),
                                        style: 'title'
                                    },
                                    {
                                        text: formatCurrency(totalCierre, 'es-CO', 'COP'),
                                        style: 'total'
                                    }
                                ]

                            },
                            {
                                style: 'body',
                                columns: [{
                                        width: '33%',
                                        stack: [{
                                                text: 'Fecha de apertura:',
                                                style: 'subTitle'
                                            },
                                            {
                                                text: respuestaGlobal.caja
                                                    .fecha_apertura,
                                                style: 'info'
                                            }
                                        ]
                                    },
                                    {
                                        width: '33%',
                                        stack: [{
                                                text: 'Fecha de cierre:',
                                                style: 'subTitle'
                                            },
                                            {
                                                text: (respuestaGlobal.caja
                                                    .estado_caja === 'Cerrada' ?
                                                    respuestaGlobal.caja
                                                    .fecha_cierre : 'No aplicable'),
                                                style: 'info'
                                            }
                                        ]
                                    },
                                    {
                                        width: '33%',
                                        stack: [{
                                                text: 'Usuario apertura:',
                                                style: 'subTitle'
                                            },
                                            {
                                                text: respuestaGlobal.caja
                                                    .nombre_usuario,
                                                style: 'info'
                                            }
                                        ]
                                    }
                                ]
                            },
                            {
                                style: 'tableExample',
                                table: {
                                    widths: ['75%', '25%'],
                                    body: [
                                        ['Saldo anterior', {
                                            text: formatCurrency(parseInt(
                                                    respuestaGlobal.caja
                                                    .saldo_anterior), 'es-CO',
                                                'COP'),
                                            alignment: 'right'
                                        }],
                                        ['Abono inicial', {
                                            text: formatCurrency(respuestaGlobal.caja
                                                .abono_inicial, 'es-CO', 'COP'),
                                            alignment: 'right'
                                        }],
                                        [{
                                            text: 'Saldo inicial total',
                                            fillColor: '#D7D7DB'
                                        }, {
                                            text: formatCurrency(respuestaGlobal.caja
                                                .saldo_inicial, 'es-CO', 'COP'),
                                            alignment: 'right',
                                            fillColor: '#D7D7DB'
                                        }]
                                    ]
                                }
                            },
                            {
                                style: 'tableExample', // Puedes ajustar el estilo según tus necesidades
                                table: {
                                    headerRows: 0, // Sin filas de encabezado
                                    widths: ['75%', '25%'],
                                    body: [
                                        ...Object.keys(totals).map(medioPago => [
                                            `${medioPago} (${totals[medioPago].count})`,
                                            {
                                                text: formatCurrency(totals[medioPago]
                                                    .total, 'es-CO', 'COP'),
                                                alignment: 'right'
                                            }
                                        ])
                                    ],
                                    // Configuración de estilos de la tabla
                                    margin: [0, 0, 0,
                                        0
                                    ], // Configuración de márgenes para quitar el borde superior
                                    layout: {
                                        hLineWidth: function(i, node) {
                                            return (i === 0) ? 0 :
                                                1; // 0 para quitar el borde superior, 1 para mantener el resto de los bordes
                                        },
                                        vLineWidth: function(i, node) {
                                            return 0;
                                        },
                                        hLineColor: function(i, node) {
                                            return '#fff'; // Color blanco para ocultar el borde superior
                                        },
                                        vLineColor: function(i, node) {
                                            return '#fff';
                                        },
                                    }
                                }
                            },
                            {
                                style: 'tableExample', // Puedes ajustar el estilo según tus necesidades
                                table: {
                                    headerRows: 0, // Sin filas de encabezado
                                    widths: ['75%', '25%'],
                                    body: [
                                        [{
                                            text: 'Recaudos',
                                            fillColor: '#D7D7DB'
                                        }, {
                                            text: formatCurrency(totalRecaudosDet,
                                                'es-CO',
                                                'COP'),
                                            fillColor: '#D7D7DB',
                                            alignment: 'right'
                                        }]
                                    ],
                                    // Configuración de estilos de la tabla
                                    layout: {
                                        hLineWidth: function(i, node) {
                                            return (i === 0) ? 0 :
                                                1; // 0 para quitar el borde superior, 1 para mantener el resto de los bordes
                                        },
                                        vLineWidth: function(i, node) {
                                            return 0;
                                        },
                                        hLineColor: function(i, node) {
                                            return '#fff'; // Color blanco para ocultar el borde superior
                                        },
                                        vLineColor: function(i, node) {
                                            return '#fff';
                                        },
                                    }
                                }
                            },
                            {
                                style: 'tableExample', // Puedes ajustar el estilo según tus necesidades
                                table: {
                                    headerRows: 0, // Sin filas de encabezado
                                    widths: ['75%', '25%'],
                                    body: [
                                        [{
                                            text: 'Gastos en efectivo'
                                        }, {
                                            text: formatCurrency(totalEfectivo,
                                                'es-CO', 'COP'),
                                            alignment: 'right'
                                        }],
                                        [{
                                            text: 'Gastos en transferencia'
                                        }, {
                                            text: formatCurrency(totalTransferencia,
                                                'es-CO', 'COP'),
                                            alignment: 'right'
                                        }]
                                    ],
                                    // Configuración de estilos de la tabla
                                    layout: {
                                        hLineWidth: function(i, node) {
                                            return (i === 0) ? 0 :
                                                1; // 0 para quitar el borde superior, 1 para mantener el resto de los bordes
                                        },
                                        vLineWidth: function(i, node) {
                                            return 0;
                                        },
                                        hLineColor: function(i, node) {
                                            return '#fff'; // Color blanco para ocultar el borde superior
                                        },
                                        vLineColor: function(i, node) {
                                            return '#fff';
                                        },
                                    }
                                }
                            },
                            {
                                style: 'tableExample', // Puedes ajustar el estilo según tus necesidades
                                table: {
                                    headerRows: 0, // Sin filas de encabezado
                                    widths: ['75%', '25%'],
                                    body: [
                                        [{
                                            text: 'Gastos',
                                            fillColor: '#D7D7DB'
                                        }, {
                                            text: formatCurrency(totalGastos,
                                                'es-CO',
                                                'COP'),
                                            fillColor: '#D7D7DB',
                                            alignment: 'right'
                                        }]
                                    ],
                                    // Configuración de estilos de la tabla
                                    layout: {
                                        hLineWidth: function(i, node) {
                                            return (i === 0) ? 0 :
                                                1; // 0 para quitar el borde superior, 1 para mantener el resto de los bordes
                                        },
                                        vLineWidth: function(i, node) {
                                            return 0;
                                        },
                                        hLineColor: function(i, node) {
                                            return '#fff'; // Color blanco para ocultar el borde superior
                                        },
                                        vLineColor: function(i, node) {
                                            return '#fff';
                                        },
                                    }
                                }
                            },
                            {
                                style: 'tableExample', // Puedes ajustar el estilo según tus necesidades
                                table: {
                                    headerRows: 0, // Sin filas de encabezado
                                    widths: ['75%', '25%'],
                                    body: [
                                        [{
                                            text: 'Total caja efectivo(saldo inicial + recaudado - gastos): ',
                                            fillColor: '#D7D7DB'
                                        }, {
                                            text: formatCurrency(totalCaja, 'es-CO',
                                                'COP'),
                                            fillColor: '#D7D7DB',
                                            alignment: 'right'
                                        }],
                                        [{
                                            text: 'Total caja transferencia (recaudado - gastos): ',
                                            fillColor: '#D7D7DB'
                                        }, {
                                            text: formatCurrency(totalCajaT, 'es-CO',
                                                'COP'),
                                            fillColor: '#D7D7DB',
                                            alignment: 'right'
                                        }]
                                    ],
                                    // Configuración de estilos de la tabla
                                    layout: {
                                        hLineWidth: function(i, node) {
                                            return (i === 0) ? 0 :
                                                1; // 0 para quitar el borde superior, 1 para mantener el resto de los bordes
                                        },
                                        vLineWidth: function(i, node) {
                                            return 0;
                                        },
                                        hLineColor: function(i, node) {
                                            return '#fff'; // Color blanco para ocultar el borde superior
                                        },
                                        vLineColor: function(i, node) {
                                            return '#fff';
                                        },
                                    }
                                }
                            },


                        ],
                        styles: {
                            header: {
                                margin: [0, 20, 0, 20]
                            },
                            title: {
                                fontSize: 18,
                                bold: true
                            },
                            title2: {
                                fontSize: 17,
                                bold: true,
                                alignment: 'left',
                                margin: [0, 20, 0, 10] // Ajusta los márgenes según tus preferencias
                            },
                            total: {
                                fontSize: 22,
                                bold: true,
                                alignment: 'right'
                            },
                            total2: {
                                fontSize: 15,
                                bold: true,
                                alignment: 'right'
                            },
                            body: {
                                margin: [0, 20, 0, 20]
                            },
                            subTitle: {
                                fontSize: 12,
                                bold: true
                            },
                            info: {
                                fontSize: 12
                            },
                            table: {
                                margin: [0, 10, 0, 10]
                            },
                            footerButtons: {
                                margin: [0, 20, 0, 0]
                            },
                            tableExample: {
                                margin: [0, 10, 0, 0]
                            },
                            tableExample1: {
                                margin: [0, 0, 0, 10]
                            },
                            colorTextoCaja: {
                                color: colorCaja,
                                bold: true,
                                fontSize: 22,
                                alignment: 'right'
                            },
                            tableStyle: {
                                margin: [0, 5, 0, 15], // Ajusta los márgenes según tus necesidades
                                fontSize: 9,
                                color: '#333', // Color del texto de la tabla
                                alignment: 'center',
                                width: '100%',
                                fillColor: '#F2F2F2', // Color de fondo para las filas normales
                            },

                            headerStyle: {
                                bold: true,
                                fontSize: 11,
                                color: '#333', // Color del texto del encabezado
                                fillColor: '#D7D7DB', // Color de fondo del encabezado
                                alignment: 'center',
                                width: '100%',
                                margin: [0, 5, 0, 7], // Ajusta los márgenes según tus necesidades
                            }
                        }
                    };

                    if (respuestaGlobal.caja.estado_caja === 'Cerrada') {
                        // Agregar la tabla de saldo de cierre al documento PDF
                        docDefinition.content.push({
                            style: 'tableExample',
                            table: {
                                headerRows: 0,
                                widths: ['75%', '25%'],
                                body: [
                                    [{
                                        text: 'Saldo cierre:',
                                        fillColor: '#D7D7DB'
                                    }, {
                                        text: formatCurrency(respuestaGlobal.caja
                                            .saldo_cierre, 'es-CO', 'COP'),
                                        fillColor: '#D7D7DB',
                                        alignment: 'right'
                                    }]
                                ],
                                // Configuración de estilos de la tabla
                                margin: [0, 0, 0,
                                    0
                                ], // Configuración de márgenes para quitar el borde superior
                                layout: {
                                    hLineWidth: function(i, node) {
                                        return (i === 0) ? 0 :
                                            1; // 0 para quitar el borde superior, 1 para mantener el resto de los bordes
                                    },
                                    vLineWidth: function(i, node) {
                                        return 0;
                                    },
                                    hLineColor: function(i, node) {
                                        return '#fff'; // Color blanco para ocultar el borde superior
                                    },
                                    vLineColor: function(i, node) {
                                        return '#fff';
                                    },
                                }
                            }
                        });
                    }


                    docDefinition.content.push({
                        text: 'Detalle de recaudos',
                        style: 'title2'
                    });

                    const medioPagoMap = {
                        "e": "Efectivo",
                        "tc": "Tarjeta de crédito",
                        "td": "Tarjeta de débito",
                        "t": "Transferencia"
                    };


                    var recaudosTable = {

                        table: {
                            headerRows: 0,
                            widths: ['auto', 'auto', 'auto', 'auto', 'auto',
                                'auto'
                            ], // ajusta según tus necesidades
                            body: [
                                [{
                                        text: 'No.',
                                        style: 'headerStyle'
                                    },
                                    {
                                        text: 'Paciente',
                                        style: 'headerStyle'
                                    },
                                    {
                                        text: 'Fecha de pago',
                                        style: 'headerStyle'
                                    },
                                    {
                                        text: 'Medio de pago',
                                        style: 'headerStyle'
                                    },
                                    {
                                        text: 'Referencia',
                                        style: 'headerStyle'
                                    },
                                    {
                                        text: 'Valor',
                                        style: 'headerStyle'
                                    }
                                ],
                                // ... puedes agregar más filas según la cantidad de detalles de recaudos
                                // Ejemplo con los datos proporcionados:
                                ...respuestaGlobal.recaudos.map(recaudo => [
                                    agregarCeros(recaudo.id, 5),
                                    `${recaudo.primer_nombre} ${recaudo.primer_apellido} (${recaudo.servicio})`,
                                    convertirFecha(recaudo.fecha_pago),
                                    medioPagoMap[recaudo.medio_pago] || recaudo.medio_pago,
                                    recaudo.referencia = recaudo.referencia !== null ?
                                    recaudo.referencia : "---",
                                    formatCurrency(recaudo.valor, 'es-CO', 'COP')
                                ])
                            ]
                        },
                        style: 'tableStyle', // Aplica el estilo a las filas normales
                        layout: {
                            hLineWidth: function(i, node) {
                                return (i === 0 || i === node.table.body.length) ? 1 : 0;
                            },
                            vLineWidth: function(i, node) {
                                return 0;
                            },
                        }
                    };

                    var totalRow = [{
                            text: 'Total:',
                            colSpan: 5,
                            style: 'total2'
                        },
                        {},
                        {},
                        {},
                        {},
                        {
                            text: formatCurrency(totalRecaudosDet, 'es-CO', 'COP'),
                            style: 'total2'
                        },

                    ];

                    recaudosTable.table.body.push(totalRow);
                    docDefinition.content.push(recaudosTable);


                    ///imprimir gastos
                    docDefinition.content.push({
                        text: 'Detalle de gastos',
                        style: 'title2'
                    });

                    var gastosTable = {

                        table: {
                            headerRows: 0,
                            widths: ['auto', 'auto', 'auto', 'auto', 'auto',
                                'auto'
                            ], // ajusta según tus necesidades
                            body: [
                                [{
                                        text: 'No.',
                                        style: 'headerStyle'
                                    },
                                    {
                                        text: 'Descripción',
                                        style: 'headerStyle'
                                    },
                                    {
                                        text: 'Fecha de gasto',
                                        style: 'headerStyle'
                                    },
                                    {
                                        text: 'Medio de pago',
                                        style: 'headerStyle'
                                    },
                                    {
                                        text: 'Referencia',
                                        style: 'headerStyle'
                                    },
                                    {
                                        text: 'Valor',
                                        style: 'headerStyle'
                                    }
                                ],
                                // ... puedes agregar más filas según la cantidad de detalles de recaudos
                                // Ejemplo con los datos proporcionados:
                                ...respuestaGlobal.gastos.map(gasto => [
                                    agregarCeros(gasto.id, 5),
                                    `${gasto.ncategoria}: ${gasto.descripcion}`,
                                    convertirFecha(gasto.fecha_gasto),
                                    medioPagoMap[gasto.forma_pago] || gasto.forma_pago,
                                    gasto.referencia = gasto.referencia !== null ?
                                    gasto.referencia : "---",
                                    formatCurrency(gasto.valor, 'es-CO', 'COP')
                                ])
                            ]
                        },
                        style: 'tableStyle', // Aplica el estilo a las filas normales
                        layout: {
                            hLineWidth: function(i, node) {
                                return (i === 0 || i === node.table.body.length) ? 1 : 0;
                            },
                            vLineWidth: function(i, node) {
                                return 0;
                            },
                        }
                    };

                    var totalRowGast = [{
                            text: 'Total:',
                            colSpan: 5,
                            style: 'total2'
                        },
                        {},
                        {},
                        {},
                        {},
                        {
                            text: formatCurrency(totalGastosDet, 'es-CO', 'COP'),
                            style: 'total2'
                        },

                    ];

                    gastosTable.table.body.push(totalRowGast);
                    docDefinition.content.push(gastosTable);
                    
                    // Generar el PDF y descargarlo
                    pdfMake.createPdf(docDefinition).download('InformeCaja.pdf');

        }

        function eliminar(idCaja){
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
                    let url = "{{ route('cajas.eliminarCaja') }}";
                    const formData = new FormData();
                    formData.append('idCaja', idCaja);

                    fetch(url, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute(
                                        'content')
                            }
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
    </script>

@endsection
