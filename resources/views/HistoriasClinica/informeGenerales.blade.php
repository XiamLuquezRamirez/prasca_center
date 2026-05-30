@extends('Plantilla.Principal')
@section('title', 'Informes generales')
@section('Contenido')
<input type="hidden" id="RutaTotal" data-ruta="{{ asset('/') }}" />


<section class="content">
    <div class="box">
        <div class="box-body">

            <div class="tab-content">
                <div class="tab-pane show active" id="bordered-justified-tabs-preview">
                    <ul class="nav nav-tabs nav-justified nav-bordered mb-3">
                        <li class="nav-item">
                            <a href="#home-b2" data-bs-toggle="tab" aria-expanded="true" class="nav-link active">

                                <span class="d-none d-md-block">Informes generales</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#profile-b2" data-bs-toggle="tab" aria-expanded="false" class="nav-link">

                                <span class="d-none d-md-block">Otros informes</span>
                            </a>
                        </li>

                    </ul>

                    <div class="tab-content px-20">
                        <div class="tab-pane  show active" id="home-b2">
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
                                    <div class="col-lg-3 col-md-6 col-sm-6" style="cursor: pointer;"
                                        onclick="mostrarCitas('todas')">
                                        <div class="box pull-up index-two">
                                            <div class="box-body">
                                                <h5 class="fw-600">Número<br>de citas</h5>
                                                <div class="text-end mt-100">
                                                    <h2 class="fw-600" id="nCitas"></h2>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-6 col-sm-6" style="cursor: pointer;"
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
                                    <div class="col-lg-2 col-md-6 col-sm-6" style="cursor: pointer;"
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

                                    <div class="col-lg-2 col-md-6 col-sm-6" style="cursor: pointer;"
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
                                    <div class="col-lg-3 col-md-6 col-sm-6" style="cursor: pointer;"
                                        onclick="mostrarCitas('paciente')">
                                        <div class="box pull-up index-two">
                                            <div class="box-body">
                                                <h5 class="fw-600">Citas<br>por paciente</h5>
                                                <div class="text-end mt-100">
                                                    <i class="fa fa-search fw-600 mt-10" style="font-size: 20px;"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-12">
                                        <div class="row">
                                            <div class="col-lg-7 col-7">
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

                                            <div class="col-lg-5 col-5">
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
                                                                                    <p class="text-fade m-0">Tasa de
                                                                                        asistencia</p>
                                                                                    <div> <label
                                                                                            id="tasaPorcentaje">29.60%</label>
                                                                                        <div
                                                                                            class="progress progress-lg">
                                                                                            <div id="tasaBarraPorcentaje"
                                                                                                class="progress-bar bg-success"
                                                                                                role="progressbar"
                                                                                                style="width: 29.60%"
                                                                                                aria-valuenow="75"
                                                                                                aria-valuemin="0"
                                                                                                aria-valuemax="100">
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="text-end">
                                                                                    <p class="text-fade">Citas
                                                                                        programadas/Citas
                                                                                        atendidas</p>
                                                                                    <h3 class="text-success"
                                                                                        id="tasaProgAtendi">30/50
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
                                                            <div class="col-lg-4 col-md-4 col-sm-4" style="cursor: pointer;" onclick="mostrarServicios('total')">
                                                                <div class="box pull-up">
                                                                    <div class="box-body media-list">
                                                                        <div>
                                                                            <div
                                                                                class="d-flex align-items-center justify-content-between">
                                                                                <div class="d-flex align-items-center">
                                                                                    <div
                                                                                        class="bg-primary h-40 w-40 l-h-35 product_icon rounded text-center">
                                                                                        <p
                                                                                            class="mb-0 fs-20 w-40 fw-600">
                                                                                            <i class="fa fa-dollar"
                                                                                                aria-hidden="true"></i>
                                                                                        </p>
                                                                                    </div>
                                                                                    <div
                                                                                        class="d-flex flex-column fw-500 mx-10">
                                                                                        <a href="#"
                                                                                            class="text-dark hover-primary mb-0 fs-17">Total
                                                                                            vendido</a>
                                                                                    </div>
                                                                                </div>
                                                                                <div>
                                                                                    <div
                                                                                        class="d-flex flex-column font-weight-500">
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
                                                                                    <p class="fs-30 mb-0"
                                                                                        id="totalServiciosVendidos">
                                                                                    </p>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-4 col-md-4 col-sm-4" style="cursor: pointer;" onclick="mostrarServicios('paquetes')">
                                                                <div class="box pull-up">
                                                                    <div class="box-body media-list">
                                                                        <div>
                                                                            <div
                                                                                class="d-flex align-items-center justify-content-between">
                                                                                <div class="d-flex align-items-center">
                                                                                    <div
                                                                                        class="bg-primary h-40 w-40 l-h-35 product_icon rounded text-center">
                                                                                        <p
                                                                                            class="mb-0 fs-20 w-40 fw-600">
                                                                                            <i class="fa fa-dollar"
                                                                                                aria-hidden="true"></i>
                                                                                        </p>
                                                                                    </div>
                                                                                    <div
                                                                                        class="d-flex flex-column fw-500 mx-10">
                                                                                        <a href="#"
                                                                                            class="text-dark hover-primary mb-0 fs-17">Paquetes
                                                                                            vendidos</a>
                                                                                    </div>
                                                                                </div>
                                                                                <div>
                                                                                    <div
                                                                                        class="d-flex flex-column font-weight-500">
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
                                                                                    <p class="fs-30 mb-0"
                                                                                        id="totalPaquetesVendidos">
                                                                                    </p>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-4 col-md-4 col-sm-4" style="cursor: pointer;" onclick="mostrarServicios('sesiones')">
                                                                <div class="box pull-up">
                                                                    <div class="box-body media-list">
                                                                        <div>
                                                                            <div
                                                                                class="d-flex align-items-center justify-content-between">
                                                                                <div class="d-flex align-items-center">
                                                                                    <div
                                                                                        class="bg-primary h-40 w-40 l-h-35 product_icon rounded text-center">
                                                                                        <p
                                                                                            class="mb-0 fs-20 w-40 fw-600">
                                                                                            <i class="fa fa-dollar"
                                                                                                aria-hidden="true"></i>
                                                                                        </p>
                                                                                    </div>
                                                                                    <div
                                                                                        class="d-flex flex-column fw-500 mx-10">
                                                                                        <a href="#"
                                                                                            class="text-dark hover-primary mb-0 fs-17">Sesiones
                                                                                            vendidas</a>
                                                                                    </div>
                                                                                </div>
                                                                                <div>
                                                                                    <div
                                                                                        class="d-flex flex-column font-weight-500">
                                                                                        <a href="#"
                                                                                            class="text-fade text-end hover-primary mb-0 fs-14"
                                                                                            id="sesVendidas">9
                                                                                            Sesiones</a>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="media mt-10 p-0">
                                                                            <div class="media-body m-0">
                                                                                <span class="text-fade">Total</span>
                                                                                <br>
                                                                                <div>
                                                                                    <p class="fs-30 mb-0"
                                                                                        id="totalSesionesVendidos">

                                                                                    </p>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-4 col-md-4 col-sm-4" style="cursor: pointer;" onclick="mostrarServicios('consultas')">
                                                                <div class="box pull-up">
                                                                    <div class="box-body media-list">
                                                                        <div>
                                                                            <div
                                                                                class="d-flex align-items-center justify-content-between">
                                                                                <div class="d-flex align-items-center">
                                                                                    <div
                                                                                        class="bg-primary h-40 w-40 l-h-35 product_icon rounded text-center">
                                                                                        <p
                                                                                            class="mb-0 fs-20 w-40 fw-600">
                                                                                            <i class="fa fa-dollar"
                                                                                                aria-hidden="true"></i>
                                                                                        </p>
                                                                                    </div>
                                                                                    <div
                                                                                        class="d-flex flex-column fw-500 mx-10">
                                                                                        <a href="#"
                                                                                            class="text-dark hover-primary mb-0 fs-17">Consultas
                                                                                            vendidas</a>
                                                                                    </div>
                                                                                </div>
                                                                                <div>
                                                                                    <div
                                                                                        class="d-flex flex-column font-weight-500">
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
                                                                                    <p class="fs-30 mb-0"
                                                                                        id="totalConsultasVendidos">
                                                                                    </p>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-4 col-md-4 col-sm-4" style="cursor: pointer;" onclick="mostrarServicios('asesorias')">
                                                                <div class="box pull-up">
                                                                    <div class="box-body media-list">
                                                                        <div>
                                                                            <div
                                                                                class="d-flex align-items-center justify-content-between">
                                                                                <div class="d-flex align-items-center">
                                                                                    <div
                                                                                        class="bg-primary h-40 w-40 l-h-35 product_icon rounded text-center">
                                                                                        <p
                                                                                            class="mb-0 fs-20 w-40 fw-600">
                                                                                            <i class="fa fa-dollar"
                                                                                                aria-hidden="true"></i>
                                                                                        </p>
                                                                                    </div>
                                                                                    <div
                                                                                        class="d-flex flex-column fw-500 mx-10">
                                                                                        <a href="#"
                                                                                            class="text-dark hover-primary mb-0 fs-17">Asesorías
                                                                                            vendidas</a>
                                                                                    </div>
                                                                                </div>
                                                                                <div>
                                                                                    <div
                                                                                        class="d-flex flex-column font-weight-500">
                                                                                        <a href="#"
                                                                                            class="text-fade text-end hover-primary mb-0 fs-14"
                                                                                            id="asesVendidas"></a>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="media mt-10 p-0">
                                                                            <div class="media-body m-0">
                                                                                <span class="text-fade">Total</span>
                                                                                <br>
                                                                                <div>
                                                                                    <p class="fs-30 mb-0"
                                                                                        id="totalAsesoriasVendidos">
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
                                                            <div class="col-lg-4 col-md-4 col-sm-4" style="cursor: pointer;" onclick="mostrarServicios('ingresos')">
                                                                <div class="box pull-up">
                                                                    <div class="box-body media-list">
                                                                        <div>
                                                                            <div
                                                                                class="d-flex align-items-center justify-content-between">
                                                                                <div class="d-flex align-items-center">
                                                                                    <div
                                                                                        class="bg-primary h-40 w-40 l-h-35 product_icon rounded text-center">
                                                                                        <p
                                                                                            class="mb-0 fs-20 w-40 fw-600">
                                                                                            <i class="fa fa-dollar"
                                                                                                aria-hidden="true"></i>
                                                                                        </p>
                                                                                    </div>
                                                                                    <div
                                                                                        class="d-flex flex-column fw-500 mx-10">
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
                                                                                    <p class="fs-30 mb-0"
                                                                                        id="ingresosTotal">
                                                                                    </p>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-4 col-md-4 col-sm-4" style="cursor: pointer; display: none;" onclick="mostrarServicios('servicios')">
                                                                <div class="box pull-up">
                                                                    <div class="box-body media-list">
                                                                        <div>
                                                                            <div
                                                                                class="d-flex align-items-center justify-content-between">
                                                                                <div class="d-flex align-items-center">
                                                                                    <div
                                                                                        class="bg-primary h-40 w-40 l-h-35 product_icon rounded text-center">
                                                                                        <p
                                                                                            class="mb-0 fs-20 w-40 fw-600">
                                                                                            <i class="fa fa-dollar"
                                                                                                aria-hidden="true"></i>
                                                                                        </p>
                                                                                    </div>
                                                                                    <div
                                                                                        class="d-flex flex-column fw-500 mx-10">
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
                                                                                    <p class="fs-30 mb-0"
                                                                                        id="totalPagosPendientes">
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
                        <div class="tab-pane" id="profile-b2">
                            <div class="row">
                                <div class="col-lg-12 col-12">
                                    <!-- Date range -->
                                    <div class="form-group">
                                        <label class="form-label">Rango de fecha:</label>

                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                            <input type="text" class="form-control pull-right" id="reservation2">
                                        </div>
                                        <!-- /.input group -->
                                    </div>
                                    <!-- /.form group -->
                                </div>
                            </div>
                            <h4 class="header-title mb-4">Grafico recaudos</h4>
                            <div class="row">
                                <div class="col-lg-8 col-12">
                                    <div id="chartdiv" style="width: 100%; height: 500px;"></div>
                                </div>
                                <div class="col-lg-4 col-12">
                                    <table id="tablaRecaudos" class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>Mes</th>
                                                <th>Recaudo ($)</th>
                                                <th>Variación (%)</th>
                                                <th>Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <hr />
                            <h4 class="header-title mb-4">Grafico citas</h4>
                            <div class="row">
                                <div class="col-lg-12 col-12">
                                    <div id="chartdivCitas" style="width: 100%; height: 500px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> <!-- end preview-->

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
<div class="modal fade" id="modalListCitasPaciente" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 70%;">
        <div class="modal-content" id="zonaImprimir">
            <div class="modal-header">
                <h4 class="modal-title" id="tituloListCitas">Listado de citas por paciente</h4>
                <button type="button" class="btn-close" onclick="salirListcitasPaciente();" aria-hidden="true"></button>
            </div>
            <div class="modal-body">
                <form id="formCitasPaciente">
                    <div class="row g-3">
                        <div class="col-lg-5 col-md-6 col-sm-6">
                            <div class="form-group mb-3">
                                <label class="form-label d-block" for="pacienteInfCitas">Paciente:</label>
                                <select class="form-control" style="width: 100%;" id="pacienteInfCitas" name="pacienteInfCitas">
                                    <option value="">Seleccione un paciente</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-sm-6">
                            <div class="form-group mb-3">
                                <label class="form-label d-block" for="profesionalInfCitas">Profesional:</label>
                                <select class="form-control" style="width: 100%;" id="profesionalInfCitas" name="profesionalInfCitas">
                                    <option value="">Seleccione un profesional</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-6">
                            <div class="form-group mb-3">
                                <label class="form-label d-block" for="pacienteInfCitas">Estado de la cita:</label>
                                <select class="select2-bg form-control" id="estadocita" name="estadocita">
                                    <option value="">Todas las citas</option>
                                    <option value="Por atender" class="por-atender">Por atender
                                    </option>
                                    <option value="Atendida" class="atendida">Atendida</option>
                                    <option value="Confirmada" class="confirmada">Confirmada
                                    </option>
                                    <option value="No Confirmada" class="no-confirmada">No
                                        Confirmada</option>
                                    <option value="No Asistio" class="no-asistio">No Asistio</option>
                                    <option value="Anulada" class="anulada">Anulada</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="fechaInicioPaciente">Fecha de inicio:</label>
                                <input type="date" class="form-control" id="fechaInicioPaciente" name="fechaInicioPaciente">
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-6 col-sm-6">
                            <div class="form-group">
                                <label class="form-label" for="fechaFinPaciente">Fecha de fin:</label>
                                <input type="date" class="form-control" id="fechaFinPaciente" name="fechaFinPaciente">
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-6 d-flex align-items-end">
                            <button type="button" class="btn btn-primary w-100" onclick="cargarCitasPaciente()">
                                <i class="fa fa-search me-1"></i> Cargar citas
                            </button>
                        </div>
                    </div>
                </form>

                <div class="mt-4">
                    <div id="listadoCitasPaciente">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Fecha y hora</th>
                                    <th>Profesional</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody id="trCitasPaciente">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="box-footer text-end">
                <button type="button" id="btn-imprimir" onclick="imprimirCitasPaciente();"
                    class="btn btn-primary-light me-1">
                    <i class="ti-printer"></i> Imprimir
                </button>
            </div>
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
</div>

<!-- MODAL DE PAGOS -->
<div class="modal fade" id="modalListPagosInforme" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 80%;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="tituloModalPagos"></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body">
                <div class="box">
                    <div class="box-body" id="divListPagos">

                    </div>
                </div>
            </div>
            <div class="modal-footer text-end">
                <button type="button" class="btn btn-primary" onclick="imprimir();" data-bs-dismiss="modal"><i class="ti-printer"></i> Imprimir</button>
            </div>
        </div>
    </div>
</div><!-- /.modal -->

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
        })

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
        })




        let modalControl;
        loader = document.getElementById('loader')
        loadNow(1)

        $('#reservation').on('apply.daterangepicker', function(ev, picker) {
            aplicarCambios()
        });
        $('#reservation2').on('apply.daterangepicker', function(ev, picker) {
            aplicarCambiosOtras()
        });



        aplicarCambios();
        cargarProfesionales();



        //inicializar select2
        $('#pacienteInfCitas').select2({
            placeholder: "Seleccione un paciente",
            allowClear: true,
            dropdownParent: $('#modalListCitasPaciente'),
            ajax: {
                url: "{{ route('pacientes.getPacientes') }}",
                dataType: 'json',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                delay: 250,
                data: function(params) {
                    return {
                        search: params.term
                    }
                },
                processResults: function(data) {
                    return {
                        results: data.map(function(item) {
                            return {
                                id: item.id,
                                text: item.identificacion + ' - ' + item.primer_nombre + ' ' + item.primer_apellido
                            };
                        })
                    };
                }
            }
        })

    })

    function cargarProfesionales() {
        let urlProfesionales = "{{ route('profesionales.cargarListaProf') }}" // Definir la URL

        fetch(urlProfesionales)
            .then(response => response.json())
            .then(data => {
                const selectProfesional = document.getElementById('profesionalInfCitas');

                llenarSelect(selectProfesional, data);
            })
            .catch(error => console.error('Error al cargar profesionales:', error));
    }

    function llenarSelect(selectElement, data) {
        selectElement.innerHTML = '<option value="">Todos los profesionales</option>';
        data.forEach(item => {
            const option = document.createElement('option');
            option.value = item.id;
            option.textContent = item.nombre;
            option.setAttribute('data-id', item.id);
            selectElement.appendChild(option);
        });
    }


    function imprimir() {
        var divToPrint = document.getElementById("divListPagos");
        var newWin = window.open("", "_blank");
        let titulo = document.getElementById("tituloModalPagos").innerText
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
                    <h1>${titulo}</h1>
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

    function mostrarServicios(opcion) {
        let modal = new bootstrap.Modal(document.getElementById("modalListPagosInforme"), {
            backdrop: 'static',
            keyboard: false
        })
        modal.show()

        switch (opcion) {
            case 'total':
                document.getElementById('tituloModalPagos').innerText = 'Total de servicios vendidos'
                break
            case 'paquetes':
                document.getElementById('tituloModalPagos').innerText = 'Total de paquetes vendidos'
                break
            case 'sesiones':
                document.getElementById('tituloModalPagos').innerText = 'Total de sesiones vendidas'
                break
            case 'consultas':
                document.getElementById('tituloModalPagos').innerText = 'Total de consultas vendidas'
                break
            case 'ingresos':
                document.getElementById('tituloModalPagos').innerText = 'Total de ingresos'
                break
            case 'servicios':
                document.getElementById('tituloModalPagos').innerText = 'Total de servicios vendidos'
                break
            default:
                document.getElementById('tituloModalPagos').innerText = 'Total de servicios vendidos'
                break
        }

        let fecha = $('#reservation').val()
        let fechas = fecha.split(" - ")
        fechas = {
            fechaInicio: fechas[0],
            fechaFin: fechas[1]
        }

        const url = "{{ route('informes.informeGeneralList') }}"
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
                    fecha2: fechas.fechaFin,
                    opcion: opcion
                })
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('divListPagos').innerHTML = data.html
            })
            .catch(error => console.error('Error:', error))
    }


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
                    document.getElementById("totalAsesoriasVendidos").textContent =
                        `${formatCurrency(data.totalAsesorias, 'es-CO', 'COP')}`;

                    document.getElementById("consVendidas").textContent = `${data.cantidadConsultas} Consultas`;
                    document.getElementById("sesVendidas").textContent = `${data.cantidadSesiones} Sesiones`;
                    document.getElementById("PaqVendidos").textContent = `${data.cantidadPaquetes} Paquetes`;
                    document.getElementById("servVendidos").textContent = `${data.cantidadTotal} Servicios`;
                    document.getElementById("asesVendidas").textContent = `${data.cantidadAsesorias} Asesorías`;
                    document.getElementById("ingresosTotal").textContent =
                        `${formatCurrency(data.recaudo, 'es-CO', 'COP')}`;
                    document.getElementById("totalPagosPendientes").textContent =
                        `${formatCurrency(data.pendiente, 'es-CO', 'COP')}`;
                } else {

                }
            })
    }

    function cargarCitasPaciente() {
        let paciente = $('#pacienteInfCitas').val()
        let fechaInicio = $('#fechaInicioPaciente').val()
        let fechaFin = $('#fechaFinPaciente').val()
        let estado = $('#estadocita').val()
        let profesional = $('#profesionalInfCitas').val()
        if (fechaInicio == '' || fechaFin == '') {
            swal('Oops...', 'Debe seleccionar una fecha de inicio y fin', 'warning')
            return
        }

        if (paciente == '') {
            swal('Oops...', 'Debe seleccionar un paciente', 'warning')
            return
        }

        let url = "{{ route('citas.listarCitasPaciente') }}"

        fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    paciente: paciente,
                    fechaInicio: fechaInicio,
                    fechaFin: fechaFin,
                    estado: estado,
                    profesional: profesional
                })
            })
            .then(response => response.json())
            .then(data => {
                //llenar tabla con los datos
                let tabla = document.getElementById('trCitasPaciente')
                tabla.innerHTML = ''
                let contador = 1;
                data.forEach(item => {
                    let tr = document.createElement('tr')
                    let fecha = new Date(item.inicio)
                    let fechaFormateada = fecha.toLocaleDateString('es-CO', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    })
                    tr.innerHTML = `
                        <td>${contador}</td>
                        <td>${fechaFormateada}</td>
                        <td>${item.nomprof}</td>
                        <td>${item.estado}</td>
                    `
                    tabla.appendChild(tr)
                    contador++;
                })
            })
            .catch(error => console.error('Error:', error))
    }



    function agruparPorMes(data) {


        let recaudosMensuales = {};

        // Nombres de los meses en español
        let mesesNombres = [
            "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
            "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"
        ];

        // Agrupar por mes
        data.forEach(item => {
            let partesFecha = item.fecha.split("-"); // Separar "2025-01-01" en ["2025", "01", "01"]
            let fecha = new Date(partesFecha[0], partesFecha[1] - 1, partesFecha[2]); // Crear fecha en horario local


            let claveMes = fecha.getFullYear() + "-" + (fecha.getMonth() + 1).toString().padStart(2,
                '0'); // YYYY-MM

            if (!recaudosMensuales[claveMes]) {
                recaudosMensuales[claveMes] = 0;
            }
            recaudosMensuales[claveMes] += item.total_recaudo;
        });

        let analisis = [];
        let meses = Object.keys(recaudosMensuales).sort(); // Ordenar meses en orden ascendente

        // Recorrer los meses para analizar variaciones
        for (let i = 0; i < meses.length; i++) {
            let mesActual = meses[i];
            let [año, mes] = mesActual.split("-");

            let nombreMesActual = `${mesesNombres[mes - 1]} (${año})`;
            let recaudoActual = recaudosMensuales[mesActual];

            let variacion = "N/A";
            let estado = "Sin comparación";

            if (i > 0) { // Comparar solo si hay un mes anterior
                let mesAnterior = meses[i - 1];
                let recaudoAnterior = recaudosMensuales[mesAnterior];

                let diferencia = recaudoActual - recaudoAnterior;
                let porcentaje = ((diferencia / recaudoAnterior) * 100).toFixed(2);

                variacion = (diferencia > 0 ? "+" : "") + porcentaje + "%";
                estado = diferencia > 0 ? "Aumento" : "Disminución";
            }

            analisis.push({
                mes: nombreMesActual,
                recaudo: recaudoActual,
                variacion: variacion,
                estado: estado
            });
        }

        return analisis;
    }

    function cargarTabla(chartData) {
        let resultado = agruparPorMes(chartData);

        let tbody = document.querySelector("#tablaRecaudos tbody");
        tbody.innerHTML = ""; // Limpiar tabla antes de insertar datos

        resultado.forEach(row => {
            let tr = document.createElement("tr");

            tr.innerHTML = `
                    <td>${row.mes}</td>
                    <td>$${row.recaudo.toLocaleString()}</td>
                    <td class="${row.estado === 'Aumento' ? 'aumento' : 'disminucion'}">${row.variacion}</td>
                    <td class="${row.estado === 'Aumento' ? 'aumento' : 'disminucion'}">${row.estado}</td>
                `;
            tbody.appendChild(tr);
        });
    }


    function aplicarCambiosOtras() {


        let fecha = $('#reservation2').val()
        let fechas = fecha.split(" - ")
        fechas = {
            fechaInicio: fechas[0],
            fechaFin: fechas[1]
        }

        let url = "{{ route('informes.otrosInformes') }}";

        fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    fecha1: fechas.fechaInicio,
                    fecha2: fechas.fechaFin
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log(data)
                actualizarGrafico(data.recaudo) // Pasamos los datos al gráfico
                cargarTabla(data.recaudo)
                actualizarGraficoCitas(data.citas) // Pasamos los datos al gráfico

            })
            .catch(error => console.error('Error en la petición:', error));
    }

    function actualizarGraficoCitas(datos) {
        if (!Array.isArray(datos)) {
            console.error("Formato incorrecto en la respuesta:", data);
            return;
        }

        var chart = am4core.create("chartdivCitas", am4charts.XYChart);
        chart.paddingRight = 20;

        let citasData = datos.map(item => ({
            date: new Date(item.fecha), // Convertir a fecha
            citas: item.cant
        }))

        chart.data = citasData

        // Eje X (Fechas)
        var dateAxis = chart.xAxes.push(new am4charts.DateAxis());
        dateAxis.renderer.minGridDistance = 50;
        dateAxis.title.text = "Fecha";

        // Eje Y (Número de citas)
        var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
        valueAxis.title.text = "Número de Citas";

        // Serie de barras
        var series = chart.series.push(new am4charts.ColumnSeries());
        series.dataFields.valueY = "citas";
        series.dataFields.dateX = "date";
        series.tooltipText = "{dateX.formatDate('dd MMM')}: {citas} citas";
        series.columns.template.fill = am4core.color("#4285F4"); // Azul
        series.columns.template.strokeWidth = 0;
        series.columns.template.column.cornerRadiusTopLeft = 5;
        series.columns.template.column.cornerRadiusTopRight = 5;

        var label = series.bullets.push(new am4charts.LabelBullet());
        label.label.text = "{citas}"; // Muestra la cantidad
        label.label.dy = -10; // Ajuste de posición (más arriba)
        label.label.fill = am4core.color("#000"); // Color negro para mayor visibilidad
        label.label.fontSize = 14; // Tamaño de fuente

        // Cursor interactivo
        chart.cursor = new am4charts.XYCursor();
        chart.scrollbarX = new am4core.Scrollbar();

    }

    function actualizarGrafico(data) {
        if (!Array.isArray(data)) {
            console.error("Formato incorrecto en la respuesta:", data);
            return;
        }

        let chart = am4core.create("chartdiv", am4charts.XYChart);
        chart.paddingRight = 20;

        chart.data = data.map(item => ({
            date: new Date(item.fecha + "T00:00:00"),
            revenue: item.total_recaudo
        }));

        var dateAxis = chart.xAxes.push(new am4charts.DateAxis());
        dateAxis.renderer.minGridDistance = 50;
        dateAxis.title.text = "Fecha";

        var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
        valueAxis.title.text = "Recaudo ($)";

        var series = chart.series.push(new am4charts.LineSeries());
        series.dataFields.valueY = "revenue";
        series.dataFields.dateX = "date";
        series.strokeWidth = 2;
        series.minBulletDistance = 10;
        series.tooltipText = "{dateX.formatDate('dd MMM')}: ${valueY}";

        var bullet = series.bullets.push(new am4charts.CircleBullet());
        bullet.circle.radius = 4;
        bullet.circle.fill = am4core.color("#fff");
        bullet.circle.strokeWidth = 2;

        series.adapter.add("stroke", function(stroke, target) {
            var index = target.dataItem.index;
            if (index > 0) {
                return chart.data[index].revenue > chart.data[index - 1].revenue ? am4core.color("#0f0") :
                    am4core.color("#f00");
            }
            return stroke;
        });

        bullet.adapter.add("fill", function(fill, target) {
            var index = target.dataItem.index;
            if (index > 0) {
                return chart.data[index].revenue > chart.data[index - 1].revenue ? am4core.color("#0f0") :
                    am4core.color("#f00");
            }
            return fill;
        });

        chart.cursor = new am4charts.XYCursor();
        chart.scrollbarX = new am4core.Scrollbar();
        chart.scrollbarX.parent = chart.bottomAxesContainer;
    }

    function formatCurrency(number, locale, currencySymbol) {
        return new Intl.NumberFormat(locale, {
            style: 'currency',
            currency: currencySymbol,
            minimumFractionDigits: 2
        }).format(number)
    }

    function mostrarCitas(tipo) {

        if (tipo == 'paciente') {
            var modal = new bootstrap.Modal(document.getElementById("modalListCitasPaciente"), {
                backdrop: 'static',
                keyboard: false
            });
            modal.show()
            return;
        }
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

    function salirListcitasPaciente() {
        const modal = document.getElementById('modalListCitasPaciente')
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

    function imprimirCitasPaciente() {
        var divToPrint = document.getElementById("listadoCitasPaciente");
        var newWin = window.open("", "_blank");

        // Obtener el texto del Select2
        let select = $('#pacienteInfCitas');
        let nombrePaciente = select.find('option:selected').text();
        if (nombrePaciente === 'Seleccione un paciente') {
            nombrePaciente = 'Paciente';
        }

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
                    <h1>Listado de citas del paciente ${nombrePaciente}</h1>
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