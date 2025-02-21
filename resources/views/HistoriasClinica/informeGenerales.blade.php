@extends('Plantilla.Principal')
@section('title', 'Informes generales')
@section('Contenido')
    <input type="hidden" id="RutaTotal" data-ruta="{{ asset('/') }}" />


    <section class="content">
        <div class="box">
            <div class="box-body">
                <div class="row">
                    <div class="col-lg-12 col-12">
                        <!-- Date range -->
                        <div class="form-group">
                            <label class="form-label">Rango de fecha:</label>

                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" class="form-control pull-right" id="reservation">
                            </div>
                            <!-- /.input group -->
                        </div>
                        <!-- /.form group -->
                    </div>
                </div>
                <div class="index-section" style="padding: 15px !important;">
                    <div class="row">
                        <div class="col-lg-3 col-md-6 col-sm-6" style="cursor: pointer;" onclick="mostrarCitas('todas')">
                            <div class="box pull-up index-two">
                                <div class="box-body">
                                    <h5 class="fw-600">Número<br>de citas</h5>
                                    <div class="text-end mt-100">
                                        <h2 class="fw-600" id="nCitas"></h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-6" style="cursor: pointer;"
                            onclick="mostrarCitas('atendidas')">
                            <div class="box pull-up index-three">
                                <div class="box-body">
                                    <h5 class="fw-600">Citas<br>atendidas</h5>
                                    <div class="text-end">
                                        <h2 class="fw-600 mt-100" id="nCitasAtendidas"></h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-6" style="cursor: pointer;"
                            onclick="mostrarCitas('canceladas')">
                            <div class="box pull-up index-two">
                                <div class="box-body">
                                    <h5 class="fw-600">Citas<br>canceladas</h5>
                                    <div class="text-end mt-100">
                                        <h2 class="fw-600" id="ncitasCanceladas"></h2>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6 col-sm-6" style="cursor: pointer;"
                            onclick="mostrarCitas('no-confir')">
                            <div class="box pull-up index-three">
                                <div class="box-body">
                                    <h5 class="fw-600">Citas<br> sin atender</h5>
                                    <div class="text-end">
                                        <h2 class="fw-600 mt-100" id="ncitasNoConfirmadas"></h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-12">
                            <div class="row">
                                <div class="col-lg-8 col-8">
                                    <div>
                                        <div class="box-body">
                                            <h2 class="fw-600"> Citas por profesionales</h2>
                                            <hr />
                                            <div>
                                                <div class="box-body p-0" id="divCitasProfesional">

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-4 col-4">
                                    <div>
                                        <div class="box-body">
                                            <h2 class="fw-600"> Tasa de asistencia</h2>
                                            <hr />
                                            <div>
                                                <div class="box-body p-0">
                                                    <div class="row mb-1">
                                                        <div class="row">
                                                            <div class="col-lg-4">
                                                                <p class="text-fade">No. Citas</p>
                                                                <h3 id="nCitasTasa">30</h3>
                                                            </div>
                                                            <div class="col-lg-8">
                                                                <div class="d-flex justify-content-between">
                                                                    <div>
                                                                        <p class="text-fade m-0">Tasa de asistencia</p>
                                                                        <div> <label id="tasaPorcentaje">29.60%</label>
                                                                            <div class="progress progress-lg">
                                                                                <div id="tasaBarraPorcentaje"
                                                                                    class="progress-bar bg-success"
                                                                                    role="progressbar" style="width: 29.60%"
                                                                                    aria-valuenow="75" aria-valuemin="0"
                                                                                    aria-valuemax="100"></div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="text-end">
                                                                        <p class="text-fade">Citas programadas/Citas
                                                                            atendidas</p>
                                                                        <h3 class="text-success" id="tasaProgAtendi">30/50
                                                                        </h3>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12 col-12">
                                    <div>
                                        <div class="box-body">
                                            <h2 class="fw-600"> Venta de servicios</h2>
                                            <hr />
                                            <div class="row">
                                                <div class="col-lg-3 col-md-3 col-sm-3">
                                                    <div class="box pull-up">
                                                        <div class="box-body media-list">
                                                            <div>
                                                                <div
                                                                    class="d-flex align-items-center justify-content-between">
                                                                    <div class="d-flex align-items-center">
                                                                        <div
                                                                            class="bg-primary h-40 w-40 l-h-35 product_icon rounded text-center">
                                                                            <p class="mb-0 fs-20 w-40 fw-600"><i
                                                                                    class="fa fa-dollar"
                                                                                    aria-hidden="true"></i></p>
                                                                        </div>
                                                                        <div class="d-flex flex-column fw-500 mx-10">
                                                                            <a href="#"
                                                                                class="text-dark hover-primary mb-0 fs-17">Total
                                                                                vendido</a>
                                                                        </div>
                                                                    </div>
                                                                    <div>
                                                                        <div class="d-flex flex-column font-weight-500">
                                                                            <a href="#"
                                                                                class="text-fade text-end hover-primary mb-0 fs-14"
                                                                                id="servVendidos"></a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="media mt-10 p-0">
                                                                <div class="media-body m-0">
                                                                    <span class="text-fade">Total</span>

                                                                    <br>
                                                                    <div>
                                                                        <p class="fs-30 mb-0" id="totalServiciosVendidos">
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 col-md-3 col-sm-3">
                                                    <div class="box pull-up">
                                                        <div class="box-body media-list">
                                                            <div>
                                                                <div
                                                                    class="d-flex align-items-center justify-content-between">
                                                                    <div class="d-flex align-items-center">
                                                                        <div
                                                                            class="bg-primary h-40 w-40 l-h-35 product_icon rounded text-center">
                                                                            <p class="mb-0 fs-20 w-40 fw-600"><i
                                                                                    class="fa fa-dollar"
                                                                                    aria-hidden="true"></i></p>
                                                                        </div>
                                                                        <div class="d-flex flex-column fw-500 mx-10">
                                                                            <a href="#"
                                                                                class="text-dark hover-primary mb-0 fs-17">Paquetes
                                                                                vendidos</a>
                                                                        </div>
                                                                    </div>
                                                                    <div>
                                                                        <div class="d-flex flex-column font-weight-500">
                                                                            <a href="#"
                                                                                class="text-fade text-end hover-primary mb-0 fs-14"
                                                                                id="PaqVendidos"></a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="media mt-10 p-0">
                                                                <div class="media-body m-0">
                                                                    <span class="text-fade">Total</span>
                                                                    <br>
                                                                    <div>
                                                                        <p class="fs-30 mb-0" id="totalPaquetesVendidos">
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 col-md-3 col-sm-3">
                                                    <div class="box pull-up">
                                                        <div class="box-body media-list">
                                                            <div>
                                                                <div
                                                                    class="d-flex align-items-center justify-content-between">
                                                                    <div class="d-flex align-items-center">
                                                                        <div
                                                                            class="bg-primary h-40 w-40 l-h-35 product_icon rounded text-center">
                                                                            <p class="mb-0 fs-20 w-40 fw-600"><i
                                                                                    class="fa fa-dollar"
                                                                                    aria-hidden="true"></i></p>
                                                                        </div>
                                                                        <div class="d-flex flex-column fw-500 mx-10">
                                                                            <a href="#"
                                                                                class="text-dark hover-primary mb-0 fs-17">Sesiones
                                                                                vendidas</a>
                                                                        </div>
                                                                    </div>
                                                                    <div>
                                                                        <div class="d-flex flex-column font-weight-500">
                                                                            <a href="#"
                                                                                class="text-fade text-end hover-primary mb-0 fs-14"
                                                                                id="sesVendidas">9 Sesiones</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="media mt-10 p-0">
                                                                <div class="media-body m-0">
                                                                    <span class="text-fade">Total</span>
                                                                    <br>
                                                                    <div>
                                                                        <p class="fs-30 mb-0" id="totalSesionesVendidos">

                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 col-md-3 col-sm-3">
                                                    <div class="box pull-up">
                                                        <div class="box-body media-list">
                                                            <div>
                                                                <div
                                                                    class="d-flex align-items-center justify-content-between">
                                                                    <div class="d-flex align-items-center">
                                                                        <div
                                                                            class="bg-primary h-40 w-40 l-h-35 product_icon rounded text-center">
                                                                            <p class="mb-0 fs-20 w-40 fw-600"><i
                                                                                    class="fa fa-dollar"
                                                                                    aria-hidden="true"></i></p>
                                                                        </div>
                                                                        <div class="d-flex flex-column fw-500 mx-10">
                                                                            <a href="#"
                                                                                class="text-dark hover-primary mb-0 fs-17">Consultas
                                                                                vendidas</a>
                                                                        </div>
                                                                    </div>
                                                                    <div>
                                                                        <div class="d-flex flex-column font-weight-500">
                                                                            <a href="#"
                                                                                class="text-fade text-end hover-primary mb-0 fs-14"
                                                                                id="consVendidas"></a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="media mt-10 p-0">
                                                                <div class="media-body m-0">
                                                                    <span class="text-fade">Total</span>
                                                                    <br>
                                                                    <div>
                                                                        <p class="fs-30 mb-0" id="totalConsultasVendidos">
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-lg-12 col-12">
                                    <div>
                                        <div class="box-body">
                                            <h2 class="fw-600"> Recaudo y finanzas </h2>
                                            <hr />
                                            <div class="row">
                                                <div class="col-lg-3 col-md-3 col-sm-3">
                                                    <div class="box pull-up">
                                                        <div class="box-body media-list">
                                                            <div>
                                                                <div
                                                                    class="d-flex align-items-center justify-content-between">
                                                                    <div class="d-flex align-items-center">
                                                                        <div
                                                                            class="bg-primary h-40 w-40 l-h-35 product_icon rounded text-center">
                                                                            <p class="mb-0 fs-20 w-40 fw-600"><i
                                                                                    class="fa fa-dollar"
                                                                                    aria-hidden="true"></i></p>
                                                                        </div>
                                                                        <div class="d-flex flex-column fw-500 mx-10">
                                                                            <a href="#"
                                                                                class="text-dark hover-primary mb-0 fs-17">Ingresos
                                                                                totales</a>
                                                                        </div>
                                                                    </div>

                                                                </div>
                                                            </div>
                                                            <div class="media mt-10 p-0">
                                                                <div class="media-body m-0">
                                                                    <span class="text-fade">Total</span>
                                                                    <br>
                                                                    <div>
                                                                        <p class="fs-30 mb-0" id="ingresosTotal">
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 col-md-3 col-sm-3">
                                                    <div class="box pull-up">
                                                        <div class="box-body media-list">
                                                            <div>
                                                                <div
                                                                    class="d-flex align-items-center justify-content-between">
                                                                    <div class="d-flex align-items-center">
                                                                        <div
                                                                            class="bg-primary h-40 w-40 l-h-35 product_icon rounded text-center">
                                                                            <p class="mb-0 fs-20 w-40 fw-600"><i
                                                                                    class="fa fa-dollar"
                                                                                    aria-hidden="true"></i></p>
                                                                        </div>
                                                                        <div class="d-flex flex-column fw-500 mx-10">
                                                                            <a href="#"
                                                                                class="text-dark hover-primary mb-0 fs-17">Servicios
                                                                                vendidos</a>
                                                                        </div>
                                                                    </div>

                                                                </div>
                                                            </div>
                                                            <div class="media mt-10 p-0">
                                                                <div class="media-body m-0">
                                                                    <span class="text-fade">Total</span>

                                                                    <br>
                                                                    <div>
                                                                        <p class="fs-30 mb-0" id="totalPagosPendientes">
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- MODAL MOTIVO DE CONSULTA -->
    <div class="modal fade" id="modalListCitas" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 70%;">
            <div class="modal-content" id="zonaImprimir">
                <div class="modal-header">
                    <h4 class="modal-title" id="tituloListCitas">Listado de citas</h4>
                    <button type="button" class="btn-close" onclick="salirListcitas();" aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                    <div id="listadoCitas">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Paciente</th>
                                    <th>profesional</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody id="trCitas">

                            </tbody>
                        </table>
                    </div>
                </div><!-- /.modal-content -->
                <div class="box-footer text-end">
                    <button type="button" id="btn-imprimir" onclick="imprimirCitas();"
                        class="btn btn-primary-light me-1">
                        <i class="ti-printer"></i> Imprimir
                    </button>
                </div>
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let menuP = document.getElementById("principalInformes")
            let menuS = document.getElementById("reporteGeneral")
            menuP.classList.add("active", "menu-open")
            menuS.classList.add("active")
            let rtotal = $("#RutaTotal").data("ruta")

            $('#reservation').daterangepicker({
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




            let modalControl;
            loader = document.getElementById('loader')
            loadNow(1)

            $('#reservation').on('apply.daterangepicker', function(ev, picker) {
                aplicarCambios()
            });

            aplicarCambios();

        })

        function aplicarCambios() {

            let fecha = $('#reservation').val()
            let fechas = fecha.split(" - ")
            fechas = {
                fechaInicio: fechas[0],
                fechaFin: fechas[1]
            }

            let url = "{{ route('informes.informeGeneral') }}";
            fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                            .getAttribute(
                                'content')
                    },
                    body: JSON.stringify({
                        fecha1: fechas.fechaInicio,
                        fecha2: fechas.fechaFin
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data) {
                        document.getElementById('nCitas').innerText = data.totalCitas
                        document.getElementById('nCitasAtendidas').innerText = data.citasAtendidas
                        document.getElementById('ncitasCanceladas').innerText = data.citasCanceladas
                        document.getElementById('ncitasNoConfirmadas').innerText = data.citasNoConfirmadas
                        document.getElementById('nCitasTasa').innerText = data.totalCitas
                        document.getElementById('tasaPorcentaje').innerText = Math.round(data.tasaAsistencia) + '%'
                        document.getElementById('tasaBarraPorcentaje').style.width = Math.round(data.tasaAsistencia) +
                            '%'
                        document.getElementById('tasaProgAtendi').innerText = data.totalCitas + '/' + data
                            .citasAtendidas
                        document.getElementById('servVendidos').innerText = data.serviciosVendidos
                        document.getElementById('totalServiciosVendidos').innerText = '$' + data.totalServicios

                        //rcorrer citas profesionales
                        let citasProfe = "";
                        console.log(data.citasPorProfesional)
                        data.citasPorProfesional.forEach(citasProf => {

                            citasProfe += `<div class="mb-25 pb-25 sombrear" onclick="mostrarCitasProf('${citasProf.idprof}')">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <div>
                                                                <h5 class="mb-0">${citasProf.nombre}</h5>
                                                            </div>
                                                            <div class="d-flex align-items-center justify-content-between">
                                                                <div class="w-150 mx-20">
                                                                    <div class="progress progress-lg mb-0">
                                                                        <div class="progress-bar bg-success"
                                                                            role="progressbar" style="width: ${citasProf.porcentaje}%"
                                                                            aria-valuenow="${citasProf.porcentaje}" aria-valuemin="0"
                                                                            aria-valuemax="100"></div>${citasProf.porcentaje}%
                                                                    </div>
                                                                </div>
                                                                <h5 class="mb-0">${citasProf.totalCitas}</h5>
                                                            </div>
                                                        </div>
                                                    </div>`

                        })

                        document.getElementById("divCitasProfesional").innerHTML = citasProfe


                        document.getElementById("totalConsultasVendidos").textContent =
                            `${formatCurrency(data.totalConsultas, 'es-CO', 'COP')}`;
                        document.getElementById("totalSesionesVendidos").textContent =
                            `${formatCurrency(data.totalSesiones, 'es-CO', 'COP')}`;
                        document.getElementById("totalPaquetesVendidos").textContent =
                            `${formatCurrency(data.totalPaquetes, 'es-CO', 'COP')}`;
                        document.getElementById("totalServiciosVendidos").textContent =
                            `${formatCurrency(data.totalGeneral, 'es-CO', 'COP')}`;

                        document.getElementById("consVendidas").textContent = `${data.cantidadConsultas} Consultas`;
                        document.getElementById("sesVendidas").textContent = `${data.cantidadSesiones} Sesiones`;
                        document.getElementById("PaqVendidos").textContent = `${data.cantidadPaquetes} Paquetes`;
                        document.getElementById("servVendidos").textContent = `${data.cantidadTotal} Servicios`;

                        document.getElementById("ingresosTotal").textContent =
                            `${formatCurrency(data.recaudo, 'es-CO', 'COP')}`;
                        document.getElementById("totalPagosPendientes").textContent =
                            `${formatCurrency(data.pendiente, 'es-CO', 'COP')}`;
                    } else {

                    }
                })
        }

        function formatCurrency(number, locale, currencySymbol) {
            return new Intl.NumberFormat(locale, {
                style: 'currency',
                currency: currencySymbol,
                minimumFractionDigits: 2
            }).format(number)
        }

        function mostrarCitas(tipo) {
            var modal = new bootstrap.Modal(document.getElementById("modalListCitas"), {
                backdrop: 'static',
                keyboard: false
            });
            modal.show()

            let fecha = $('#reservation').val()
            let fechas = fecha.split(" - ")
            fechas = {
                fechaInicio: fechas[0],
                fechaFin: fechas[1]
            }

            let url = "{{ route('citas.listaCitasEstado') }}" // Definir la URL

            var data = {
                fecha1: fechas.fechaInicio,
                fecha2: fechas.fechaFin,
                tipo: tipo
            }

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
                    document.getElementById("trCitas").innerHTML = responseData.citas
                    loadNow(0)
                })
                .catch(error => console.error('Error:', error))
        }

        function salirListcitas() {
            const modal = document.getElementById('modalListCitas')
            const modalInstance = bootstrap.Modal.getInstance(modal)
            modalInstance.hide()
        }

        function mostrarCitasProf(idPrfo) {
            var modal = new bootstrap.Modal(document.getElementById("modalListCitas"), {
                backdrop: 'static',
                keyboard: false
            });
            modal.show()


            let fecha = $('#reservation').val()
            let fechas = fecha.split(" - ")
            fechas = {
                fechaInicio: fechas[0],
                fechaFin: fechas[1]
            }

            let url = "{{ route('citas.listaCitasProfesional') }}" // Definir la URL

            var data = {
                fecha1: fechas.fechaInicio,
                fecha2: fechas.fechaFin,
                idProf: idPrfo
            }

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
                    document.getElementById("trCitas").innerHTML = responseData.citas
                    loadNow(0)
                })
                .catch(error => console.error('Error:', error))

        }

        function imprimirCitas() {
            var divToPrint = document.getElementById("listadoCitas");
            var newWin = window.open("", "_blank");

            newWin.document.open();
            newWin.document.write(`
            <html>
                <head>
                    <title>impresión generada por PRASCA CENTER</title>
                    <style>
                        body { font-family: Arial, sans-serif; padding: 20px; }
                        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                        th, td { border: 1px solid black; padding: 8px; text-align: left; }
                        th { background-color: #f2f2f2; }
                        @media print {
                            body { margin: 0; padding: 0; }
                        }
                    </style>
                </head>
                <body>
                    <h1>Listado de citas</h1>
                    <div>${divToPrint.innerHTML}</div>
                    <script>
                        window.onload = function() { 
                            window.print();
                            window.onafterprint = function() { window.close(); };
                        };
                    <\/script>
                </body>
            </html>
            `);
            newWin.document.close();
        }
    </script>

@endsection
