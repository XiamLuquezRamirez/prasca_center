@extends('Plantilla.Principal')
@section('title', 'Gestionar recaudos de pacientes.')

<style>
    /* Estilos para manejar el overflow del body */
    body.modal-open {
        overflow: hidden !important;
        padding-right: 0 !important;
    }

    body:not(.modal-open) {
        overflow: auto !important;
        padding-right: 0 !important;
    }

    /* Asegurar que los modales se muestren correctamente */
    .modal {
        overflow-y: auto !important;
    }

    .modal-backdrop {
        z-index: 1040;
    }

    .modal {
        z-index: 1050;
    }
</style>

@section('Contenido')
<input type="hidden" id="consMedio" value="0">
<input type="hidden" id="tipoEdicion" value="0">
<input type="hidden" id="idPagoEditarHistorial" value="0">
<div class="content-header">
    <div class="d-flex align-items-center">
        <div class="row">
            <div class="col-xl-3 col-lg-3 col-12" onclick="verPagos('pendientes');" style="cursor: pointer;">
                <div class="box box-body pull-up Sales_Profit">
                    <div class="row">
                        <div class="col-12">
                            <h4 class="hover-primary"><i class="fa fa-fw fa-gg-circle text-primary"></i> Pagos
                                pendientes:
                            </h4>
                            <div class="d-flex" style="justify-content: flex-end">
                                <p class="fs-35 fw-600 mb-0" id="pagosPendientesVis"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-12" onclick="verPagos('completados');" style="cursor: pointer;">
                <div class="box box-body pull-up Sales_Profit ">
                    <div class="row">
                        <div class="col-12">
                            <h4 class="hover-success"><i class="fa fa-fw fa-gg-circle text-primary"></i> Pagos
                                completados: </h4>
                            <div class="d-flex" style="justify-content: flex-end">
                                <p class="fs-35 fw-600 mb-0" id="pagosSaldoVis"> </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-12" onclick="verPagos('mes');" style="cursor: pointer;">
                <div class="box box-body pull-up Sales_Profit ">
                    <div class="row">
                        <div class="col-12">
                            <h4 class="hover-success"><i class="fa fa-fw fa-gg-circle text-primary"></i> Recaudo mes:
                            </h4>
                            <div class="d-flex" style="justify-content: flex-end">
                                <p class="fs-35 fw-600 mb-0 " id="recaudoMes"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-12" onclick="verPagos('hoy');" style="cursor: pointer;">
                <div class="box box-body pull-up Sales_Profit ">
                    <div class="row">
                        <div class="col-12 ">
                            <h4 class="hover-success"><i class="fa fa-fw fa-gg-circle text-primary"></i> Recaudo hoy:
                            </h4>
                            <div class="d-flex" style="justify-content: flex-end">
                                <p class="fs-35 fw-600 mb-0" id="recaudodia"> </p>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-8 col-md-12 col-12">
                        <div class="box">
                            <div class="box-body">
                                <h1>Gestionar recaudos.</h1>
                                <div class="tab-pane show active" id="default-tabs-preview">
                                    <ul class="nav nav-pills bg-nav-pills nav-justified mb-3">
                                        <li class="nav-item">
                                            <a href="#home" data-bs-toggle="tab" aria-expanded="false"
                                                class="nav-link active border-0">
                                                <h5 class="d-md-block fw-200">Pagos Pendientes pacientes.</h5>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="#homeEps" data-bs-toggle="tab" aria-expanded="false"
                                                class="nav-link border-0">
                                                <h5 class="d-md-block fw-200">Pagos Pendientes entidades.</h5>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="#vetas" data-bs-toggle="tab" aria-expanded="false"
                                                class="nav-link  border-0">
                                                <h5 class="d-md-block fw-200">Pagos completados pacientes.</h5>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="#vetasEps" data-bs-toggle="tab" aria-expanded="false"
                                                class="nav-link  border-0">
                                                <h5 class="d-md-block fw-200">Pagos completados entidades.</h5>
                                            </a>
                                        </li>
                                    </ul>

                                    <div class="tab-content px-20">
                                        <div class="tab-pane active" id="home">
                                            <div class="box-body p-0">
                                                <!-- Campo de búsqueda -->
                                                <div class="mb-3">
                                                    <input type="text" id="buscarVentas" class="form-control"
                                                        placeholder="Buscar por paciente, servicio o descripción">
                                                </div>

                                                <!-- Tabla de ventas pendientes -->
                                                <div class="table-responsive">
                                                    <!-- Encabezado fijo -->
                                                    <table class="table table-head" style="table-layout: fixed; width: 100%;">
                                                        <thead>
                                                            <tr>
                                                                <th style="width: 40%;">Paciente</th>
                                                                <th style="width: 20%;">Fecha venta</th>
                                                                <th style="width: 20%;">Valor</th>
                                                                <th style="width: 20%;">Saldo</th>
                                                            </tr>
                                                        </thead>
                                                    </table>

                                                    <!-- Cuerpo scrollable -->
                                                    <div style="max-height: 520px; overflow-y: auto;">
                                                        <table class="table table-body" style="table-layout: fixed; width: 100%;">
                                                            <tbody id="trRegistrosVentas">
                                                                <!-- Más filas -->
                                                            </tbody>
                                                        </table>
                                                    </div>

                                                    <div id="pagination-links" class="text-center ml-1 mt-2"></div>
                                                </div>


                                            </div>
                                        </div>
                                        <div class="tab-pane" id="homeEps">
                                            <div class="box-body p-0">
                                                <!-- Campo de búsqueda -->
                                                <div class="mb-3">
                                                    <input type="text" id="buscarVentasEps" class="form-control"
                                                        placeholder="Buscar por EPS o servicio">
                                                </div>

                                                <!-- Tabla de ventas pendientes -->
                                                <div class="table-responsive">
                                                    <!-- Encabezado fijo -->
                                                    <table class="table table-head" style="table-layout: fixed; width: 100%;">
                                                        <thead>
                                                            <tr>
                                                                <th style="width: 40%;">Entidad</th>
                                                                <th style="width: 20%;">Fecha venta</th>
                                                                <th style="width: 20%;">Valor</th>
                                                                <th style="width: 20%;">Saldo</th>
                                                            </tr>
                                                        </thead>
                                                    </table>

                                                    <!-- Cuerpo scrollable -->
                                                    <div style="max-height: 520px; overflow-y: auto;">
                                                        <table class="table table-body" style="table-layout: fixed; width: 100%;">
                                                            <tbody id="trRegistrosVentasEps">
                                                           
                                                                <!-- Más filas -->
                                                            </tbody>
                                                        </table>
                                                    </div>

                                                    <div id="pagination-links-eps" class="text-center ml-1 mt-2"></div>
                                                </div>


                                            </div>
                                        </div>
                                        <div class="tab-pane" id="vetas">
                                            <div class="box-body p-0">
                                                <!-- Campo de búsqueda -->
                                                <div class="mb-3">
                                                    <input type="text" id="buscarVentasCompletas"
                                                        class="form-control"
                                                        placeholder="Buscar por paciente o servicio">
                                                </div>

                                                <!-- Tabla de ventas pendientes -->
                                                <div class="table-responsive">
                                                    <table class="table no-border table-vertical-center"
                                                        id="recaudosCompletosTable">
                                                        <thead>
                                                            <tr class="bb-1">
                                                                <th class="p-5 px-15" style="min-width: 300px">
                                                                    <h3>Paciente</h3>
                                                                </th>
                                                                <th class="p-5 px-15" style="min-width: 150px">
                                                                    <h3>Fecha pago</h3>
                                                                </th>
                                                                <th class="p-5 px-15" style="min-width: 150px">
                                                                    <h3>Valor</h3>
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="trRegistrosVentasCompleto">
                                                            <!-- Aquí se cargan las ventas pendientes -->
                                                        </tbody>
                                                    </table>
                                                    <div id="pagination-links-pagosCompletos"
                                                        class="text-center ml-1 mt-2">

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="vetasEps">
                                            <div class="box-body p-0">
                                                <!-- Campo de búsqueda -->
                                                <div class="mb-3">
                                                    <input type="text" id="buscarVentasCompletasEps"
                                                        class="form-control"
                                                        placeholder="Buscar por paciente o servicio">
                                                </div>

                                                <!-- Tabla de ventas pendientes -->
                                                <div class="table-responsive">
                                                    <table class="table no-border table-vertical-center"
                                                        id="recaudosCompletosTableEps">
                                                        <thead>
                                                            <tr class="bb-1">
                                                                <th class="p-5 px-15" style="min-width: 300px">
                                                                    <h3>Entidad</h3>
                                                                </th>
                                                                <th class="p-5 px-15" style="min-width: 150px">
                                                                    <h3>Fecha pago</h3>
                                                                </th>
                                                                <th class="p-5 px-15" style="min-width: 150px">
                                                                    <h3>Valor</h3>
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="trRegistrosVentasCompletoEps">
                                                            <!-- Aquí se cargan las ventas pendientes -->
                                                        </tbody>
                                                    </table>
                                                    <div id="pagination-links-pagosCompletosEps"
                                                        class="text-center ml-1 mt-2">

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-12 col-12">
                        <div class="box">
                            <div class="box-body">
                                <h3>Historial ultimos pagos realizados.</h3>
                                <div id="infHistoria">
                                    <!-- Aquí se cargan los pagos realizados -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- MODAL PAGOS -->
<div class="modal fade" id="modalPagos" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 60%;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="tituloAccion">Agregar pago</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs nav-bordered mb-3">
                    <li class="nav-item">
                        <a href="#infPago" data-bs-toggle="tab" aria-expanded="false" class="nav-link active">
                            <i class="fa fa-calendar"></i> Realizar pago</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a href="#infHistorialPago" data-bs-toggle="tab" aria-expanded="false" class="nav-link">
                            <i class="fa fa-address-card-o"></i> Historial de pagos</a>

                    </li>
                </ul>
                <div class="tab-content px-1 pt-1">
                    <div class="tab-pane active" id="infPago" aria-labelledby="homeIcon-tab" role="tabpanel">
                        <h5 class="mb-1"><i class="feather icon-info"></i> Información del la venta del servicio
                        </h5>
                        <form id="formVenta">
                            <input type="hidden" id="idVentaServicio" name="idVentaServicio">
                            <input type="hidden" id="idPago" name="idPago">
                            <input type="hidden" id="accPago" name="accPago">
                            <div class="border p-3 mt-4 mt-lg-0 rounded">
                                <h4 class="header-title mb-3">Venta pendiente</h4>

                                <div class="table-responsive">
                                    <table class="table table-centered mb-0">
                                        <tbody>
                                            <tr>
                                                <td colspan="2">
                                                    <div class="form-group row">
                                                        <label for="example-text-input"
                                                            class="col-sm-4 col-form-label">Fecha de pago:</label>
                                                            <input type="date"  class="form-control"
                                                            id="fechaPago" name="fechaPago">
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <p class="m-0 d-inline-block align-middle ml-2"
                                                        style="font-size: 20px;">
                                                        <a href="#" class="text-body fw-500"
                                                            id="descripcionPaquete"></a>
                                                        <br>
                                                        <small id="sesionesPaquete"></small>
                                                    </p>
                                                </td>
                                                <td id="valorPaquete" class="text-end">
                                                </td>
                                            </tr>
                                            <tr class="text-end">
                                                <td>
                                                    <h6 class="m-0">Abono realizado:</h6>
                                                </td>
                                                <td id="valorAbonoPrevioVis" class="text-end fw-500">
                                                </td>
                                                <input type="hidden" value="0" id="valorAbonoPrevio"
                                                    name="valorAbonoPrevio">
                                            </tr>
                                            <tr class="text-end">
                                                <td>
                                                    <h6 class="m-0">Total a pagar:</h6>
                                                </td>
                                                <td id="valorTotalPaquete" class="text-end fw-500">
                                                </td>
                                                <input type="hidden" value="0" id="valotTotalVentPaq"
                                                    name="valotTotalVentPaq">
                                            </tr>
                                            <tr class="text-end">
                                                <td>
                                                    <h6 class="m-0">Ingresar abono libre:</h6>
                                                    <label class="switch switch-border switch-primary">
                                                        <input type="checkbox" onchange="habilitarAb()"
                                                            id="habilitarAbono" name="habilitarAbono">
                                                        <span class="switch-indicator"></span>
                                                        <span class="switch-description"></span>
                                                    </label>

                                                </td>
                                                <td style="width: 200px" class="text-end">
                                                    <input type="text" class="form-control text-end" readonly
                                                        onchange="cambioFormato(this.id);"
                                                        onkeypress="return validartxtnum(event)"
                                                        onclick="this.select();" id="abonoVis" name="abonoVis"
                                                        value="$ 0,00" placeholder="Ingrese el abono">
                                                    <input type="hidden" value="0" id="abono"
                                                        name="abono">
                                                </td>
                                            </tr>

                                        </tbody>
                                    </table>
                                </div>
                                <!-- end table-responsive -->
                            </div>

                            <div class="border p-3 mt-4 mt-lg-0 rounded">
                                <h4 class="header-title mb-3">Medio de pago</h4>
                                <div id="medioPago">

                                </div>

                                <div class="col-12 mt-2">
                                    <div class="form-group">
                                        <button onclick="cargarMedioPago();" class="btn btn-primary mt-1"
                                            type="button">
                                            <i class="fa fa-plus"></i> Agregar Medio de pago
                                        </button>
                                    </div>
                                </div>

                            </div>

                            <div class="box-footer text-end">

                                <button type="button" id="cancelRegistro" onclick="cancelarPAgo();"
                                    class="btn btn-primary-light me-1">
                                    <i class="ti-close"></i> Cancelar
                                </button>
                                <button type="button" id="imprimirRegistro" style="display: none"
                                    onclick="imprimirPago();" class="btn btn-primary-light me-1">
                                    <i class="ti-printer"></i> Imprimir recibo de caja
                                </button>
                                <button type="button" id="saveRegistro" onclick="guardarPago();"
                                    class="btn btn-primary">
                                    <i class="ti-save"></i> Confirmar pago
                                </button>
                            </div>

                        </form>
                    </div>
                    <div class="tab-pane" id="infHistorialPago" aria-labelledby="dropdownIcon1-tab" role="tabpanel">
                        <h5 class="mb-1"><i class="feather icon-info"></i> Historial de pagos</h5>
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Usuario</th>
                                    <th>Valor</th>
                                    <th>Medio de pago</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="infHistorialPagosPaquete">
                                <!-- Aquí se cargan las ventas pendientes -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
</div><!-- /.modal -->

<!-- MODAL DETALLE DE PAGOS -->
<div class="modal fade" id="modalDetallePagos" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 60%;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Detalle de pagos</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body">
                <div class="box">
                    <div class="box-body">
                        <h3>Historial ultimos pagos realizados.</h3>
                        <!-- Mostrar la informacion del paquete pagado -->
                        <div>
                            <div class="box-body ">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <div class="d-flex flex-column font-weight-500 mx-10">
                                            <a href="#" class="text-dark hover-primary mb-1  fs-17"
                                                id="descripcionPaquete"></a>
                                            <span class="text-fade" id="sesionesPaquete"></span>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="d-flex flex-column font-weight-500">
                                            <a href="#" class="text-dark text-end hover-primary mb-1"
                                                id="valorPaquete"></a>
                                            <span class="text-success" id="valorAbonoPrevioVis"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="infHistoriaPagos">
                                <!-- Aquí se cargan los pagos realizados -->
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div><!-- /.modal -->

<!-- MODAL DE PAGOS -->
<div class="modal fade" id="modalListPagos" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
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
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>N°</th>
                                    <th>Fecha</th>
                                    <th>Paciente</th>
                                    <th>Servicio</th>
                                    <th>Valor</th>
                                    <th>Medio de pago</th>
                                </tr>
                            </thead>
                            <tbody id="infListPagos">
                                <!-- Aquí se cargan los pagos realizados -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer text-end">
                <button type="button" class="btn btn-primary" onclick="imprimir();" data-bs-dismiss="modal"><i class="ti-printer"></i> Imprimir</button>
            </div>
        </div>
    </div>
</div><!-- /.modal -->

<!-- MODAL EDITAR PAGO -->
<div class="modal fade" id="modalEditarPago" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 50%;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Editar Pago</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true" onclick="restaurarOverflowBody()"></button>
            </div>
            <div class="modal-body">
                <form id="formEditarPago">
                    <input type="hidden" id="idPagoEditar" name="idPagoEditar">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fechaPagoEditar">Fecha de Pago:</label>
                                <input type="date" class="form-control" id="fechaPagoEditar" name="fechaPagoEditar" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="valorPagoEditar">Valor del Pago:</label>
                                <input disabled type="text" class="form-control text-end" id="valorPagoEditar" name="valorPagoEditar"
                                    onchange="cambioFormatoEditar(this);" onkeypress="return validartxtnum(event)"
                                    onclick="this.select();" required>
                                <input type="hidden" id="valorPagoEditarHidden" name="valorPagoEditarHidden">
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="medioPagoEditar">Medio de Pago:</label>
                                <select class="form-control" id="medioPagoEditar" name="medioPagoEditar" required>
                                    <option value="">Seleccione...</option>
                                    <option value="e">Efectivo</option>
                                    <option value="t">Transferencia</option>
                                    <option value="td">Tarjeta de débito</option>
                                    <option value="tc">Tarjeta de crédito</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group" id="divReferenciaEditar" style="display: none;">
                                <label for="referenciaPagoEditar">Número de Referencia:</label>
                                <input type="text" class="form-control" id="referenciaPagoEditar" name="referenciaPagoEditar">
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <strong>Información del Pago:</strong><br>
                                <span id="infoPacienteEditar"></span><br>
                                <span id="infoServicioEditar"></span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="restaurarOverflowBody()">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardarEdicionPago()">
                    <i class="fa fa-save"></i> Guardar Cambios
                </button>
            </div>
        </div>
    </div>
</div><!-- /.modal -->

<script>
    document.addEventListener("DOMContentLoaded", function() {
        let menuP = document.getElementById("principalRecaudo")
        let menuS = document.getElementById("principalRecaudoGestionRecaudo")

        menuP.classList.add("active", "menu-open")
        menuS.classList.add("active")

        loader = document.getElementById('loader')
        loadNow(1);
        cargarTablaRecaudos(1)
        cargarTablaRecaudosPagos(1)
        cargarOtraInformacion()

        //cargar tabla de ventas eps
        cargarTablaRecaudosEps(1)
        cargarTablaRecaudosPagosEps(1)

        // Evento para limpiar backdrop cuando se cierra el modal manualmente
        document.getElementById('modalEditarPago').addEventListener('hidden.bs.modal', function() {
            setTimeout(function() {
                const backdrops = document.querySelectorAll('.modal-backdrop');
                backdrops.forEach(backdrop => {
                    backdrop.remove();
                });
                document.body.classList.remove('modal-open');
                // Restaurar el overflow del body
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
            }, 100);
        });

        // Evento adicional para cuando se abre el modal
        document.getElementById('modalEditarPago').addEventListener('shown.bs.modal', function() {
            // Asegurar que el modal esté correctamente configurado
            document.body.style.overflow = 'hidden';
        });

        // Evento global para todos los modales
        document.addEventListener('hidden.bs.modal', function(event) {
            setTimeout(function() {
                restaurarOverflowBody();
            }, 100);
        });

        // Evento para cuando se presiona ESC o se hace clic fuera del modal
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                setTimeout(function() {
                    restaurarOverflowBody();
                }, 100);
            }
        });

        document.addEventListener('click', function(event) {
            if (event.target.matches('#paginationRecaudos a')) {
                event.preventDefault();
                var href = event.target.getAttribute('href');
                var page = href.split('page=')[1];
                var searchTerm = document.getElementById('buscarVentas').value;

                // Asegurarse de que 'page' sea un número antes de hacer la solicitud
                if (!isNaN(page)) {
                    cargarTablaRecaudos(page, searchTerm);
                }
            }
        });

        document.addEventListener('click', function(event) {
            if (event.target.matches('#paginationRecaudosEps a')) {
                event.preventDefault();
                var href = event.target.getAttribute('href');
                var page = href.split('page=')[1];
                var searchTerm = document.getElementById('buscarVentasEps').value;

                // Asegurarse de que 'page' sea un número antes de hacer la solicitud
                if (!isNaN(page)) {
                    cargarTablaRecaudosEps(page, searchTerm);
                }
            }
        });

        // Evento input para el campo de búsqueda
        document.getElementById('buscarVentas').addEventListener('input', function() {
            var searchTerm = this.value;
            cargarTablaRecaudos(1,
                searchTerm); // Cargar la primera página con el término de búsqueda
        });

        document.getElementById('buscarVentasEps').addEventListener('input', function() {
            var searchTerm = this.value;
            cargarTablaRecaudosEps(1,
                searchTerm); // Cargar la primera página con el término de búsqueda
        });

        document.getElementById('buscarVentasCompletasEps').addEventListener('input', function() {
            var searchTerm = this.value;
            cargarTablaRecaudosPagosEps(1,
                searchTerm); // Cargar la primera página con el término de búsqueda
        });

        ///PAGINACION DE PAGOS COMPLETADOS
        document.addEventListener('click', function(event) {
            if (event.target.matches('#paginationRecaudosPagos a')) {
                event.preventDefault();
                var href = event.target.getAttribute('href');
                var page = href.split('page=')[1];
                var searchTerm = document.getElementById('buscarVentasCompletas').value;
                // Asegurarse de que 'page' sea un número antes de hacer la solicitud
                if (!isNaN(page)) {
                    cargarTablaRecaudosPagos(page, searchTerm);
                }
            }
        });
        document.addEventListener('click', function(event) {
            if (event.target.matches('#paginationRecaudosPagosEps a')) {
                event.preventDefault();
                var href = event.target.getAttribute('href');
                var page = href.split('page=')[1];
                var searchTerm = document.getElementById('buscarVentasCompletas').value;
                // Asegurarse de que 'page' sea un número antes de hacer la solicitud
                if (!isNaN(page)) {
                    cargarTablaRecaudosPagosEps(page, searchTerm);
                }
            }
        });
        // Evento input para el campo de búsqueda
        document.getElementById('buscarVentasCompletas').addEventListener('input', function() {
            var searchTerm = this.value;
            cargarTablaRecaudosPagos(1,
                searchTerm); // Cargar la primera página con el término de búsqueda
        });

    });

    // Función global para restaurar el overflow del body
    function restaurarOverflowBody() {
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
        document.body.classList.remove('modal-open');

        // Remover backdrops residuales
        const backdrops = document.querySelectorAll('.modal-backdrop');
        backdrops.forEach(backdrop => {
            backdrop.remove();
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

    function cargarOtraInformacion() {
        let url = "{{ route('Administracion.otraInformacionRecaudos') }}"; // Definir la URL

        fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(responseData => {
                document.getElementById("pagosPendientesVis").innerText = responseData.pagosPendientes
                document.getElementById("pagosSaldoVis").innerText = responseData.ventasPagadas
                document.getElementById("recaudoMes").innerText = formatCurrency(responseData.recaudoMes, 'es-CO',
                    'COP')
                document.getElementById("recaudodia").innerText = formatCurrency(responseData.recaudoDia, 'es-CO',
                    'COP')

                //recorrer historial de pagos
                let historialPagos = responseData.historialPagos
                console.log(typeof historialPagos)
                let html = ""
                Object.values(historialPagos).forEach(pago => {
                    let nombre = ""
                    if(pago.tipo == "ASESORIA"){
                        nombre = pago.entidad
                    }else{
                        nombre = pago.primer_nombre + " " + pago.primer_apellido
                    }
                    html += `<div class="box mb-15 pull-up">
                                    <div class="box-body ">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center">
                                                <div class="d-flex flex-column font-weight-500 mx-10">
                                                    <a href="#" class="text-dark hover-primary mb-1  fs-17">
                                                    ${nombre}
                                                    </a>
                                                    <span class="text-fade">${pago.descripcion}</span>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="d-flex flex-column font-weight-500">
                                                    <a href="#"
                                                        class="text-dark text-end hover-primary mb-1">${formatCurrency(pago.pago_realizado, 'es-CO', 'COP')}</a>
                                                    <span class="text-success">${convertirFecha(pago.fecha_pago)}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>`
                });
                document.getElementById("infHistoria").innerHTML = html

            })
            .catch(error => console.error('Error:', error));
    }

    function habilitarAb() {
        let habilitarAbono = document.getElementById("habilitarAbono").checked
        if (habilitarAbono) {
            document.getElementById("abonoVis").readOnly = false
        } else {
            document.getElementById("abonoVis").readOnly = true
            document.getElementById("abonoVis").value = "$ 0,00"
        }
    }

    function cargarTablaRecaudos(page, searchTerm = '') {


        let url = "{{ route('Administracion.listaVentasPacientes') }}"; // Definir la URL

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
                document.getElementById('trRegistrosVentas').innerHTML = responseData.paquetesVentas;
                feather.replace();
                // Colocar los enlaces de paginación
                document.getElementById('pagination-links').innerHTML = responseData.links;

                loadNow(0);
            })
            .catch(error => console.error('Error:', error));

    }

    function cargarTablaRecaudosEps(page, searchTerm = '') {

        let url = "{{ route('Administracion.listaVentasEps') }}"; // Definir la URL

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
                document.getElementById('trRegistrosVentasEps').innerHTML = responseData.paquetesVentas;
                feather.replace();
                // Colocar los enlaces de paginación
                document.getElementById('pagination-links-eps').innerHTML = responseData.links;
            })
            .catch(error => console.error('Error:', error));

    }

    function cargarTablaRecaudosPagosEps(page, searchTerm = '') {
        

        let url = "{{ route('Administracion.listaVentasPacientesPagosEps') }}"; // Definir la URL

        var data = {
            pagePago: page,
            searchPago: searchTerm
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
                document.getElementById('trRegistrosVentasCompletoEps').innerHTML = responseData.paquetesVentas;
                feather.replace();
                // Colocar los enlaces de paginación
                document.getElementById('pagination-links-pagosCompletosEps').innerHTML = responseData.links;
            })
            .catch(error => console.error('Error:', error));
    }

    function cargarTablaRecaudosPagos(page, searchTerm = '') {

        let url = "{{ route('Administracion.listaVentasPacientesPagos') }}"; // Definir la URL

        var data = {
            pagePago: page,
            searchPago: searchTerm
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
                document.getElementById('trRegistrosVentasCompleto').innerHTML = responseData.paquetesVentas;
                feather.replace();
                // Colocar los enlaces de paginación
                document.getElementById('pagination-links-pagosCompletos').innerHTML = responseData.links;
            })
            .catch(error => console.error('Error:', error));

    }

    function verPagos(tipo) {
        var modal = new bootstrap.Modal(document.getElementById("modalListPagos"), {
            backdrop: 'static',
            keyboard: false
        })
        modal.show()
        document.getElementById("tituloModalPagos").innerText = "Pagos " + tipo
        document.getElementById("infListPagos").innerHTML = ""
        let url = "{{ route('Administracion.listaPagos') }}"; // Definir la URL

        var data = {
            tipo: tipo
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
                let html = ""
                let n = 1
                responseData.pagos.forEach(pago => {
                    if (pago.medio_pago == undefined) {
                        pago.medio_pago = "N/A"
                    }



                    html += `<tr>
                                    <td>${n}</td>
                                    <td>${convertirFecha(pago.fecha)}</td>
                                    <td>${pago.nombre_cliente}</td>
                                    <td>${pago.descripcion}</td>
                                    <td>${formatCurrency(pago.total, 'es-CO', 'COP')}</td>
                                    <td>${pago.medio_pago}</td>
                                </tr>`
                    n++
                });
                document.getElementById('infListPagos').innerHTML = html;
            })
            .catch(error => console.error('Error:', error));


    }

    function convertirFecha(fecha) {
        // Dividir la fecha en año, mes y día
        let fechaFormateada = fecha.split(' ')[0]
        const [año, mes, dia] = fechaFormateada.split('-')
        /**
         * 
         **/
        //Formatear la fecha en el formato dd/mm/yyyy
        fechaFormateada = `${dia.padStart(2, '0')}/${mes.padStart(2, '0')}/${año}`

        return fechaFormateada
    }

    function realizarPago(idVenta) {
        var modal = new bootstrap.Modal(document.getElementById("modalPagos"), {
            backdrop: 'static',
            keyboard: false
        })
        modal.show()
        document.getElementById("accPago").value = "guardar"
        document.getElementById("saveRegistro").removeAttribute('disabled')
        document.getElementById("imprimirRegistro").style.display = 'none'
        document.getElementById("abonoVis").value = "$ 0,00"
        document.getElementById("abono").value = "0"
        document.getElementById('habilitarAbono').checked = false
        document.getElementById("abonoVis").readOnly = true
        document.getElementById("fechaPago").value = ""


        let url = "{{ route('Administracion.detalleVentaServicioPaciente') }}";

        fetch(url, {
                method: 'POST',
                async: false,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    idVenta: idVenta
                })
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById("idVentaServicio").value = data.PaqueteVenta.id
                document.getElementById("descripcionPaquete").innerText = data.PaqueteVenta.descripcion.descripcion

                if (data.PaqueteVenta.tipo != "PRUEBAS") {
                    document.getElementById("sesionesPaquete").innerText =
                        `Cantidad de sesiones: ${data.PaqueteVenta.cantidad}`
                } else {
                    document.getElementById("sesionesPaquete").innerHTML = ""
                }


                document.getElementById("valorPaquete").innerText = formatCurrency(data.PaqueteVenta.precio,
                    'es-CO', 'COP')

                document.getElementById("valorAbonoPrevioVis").innerText = formatCurrency(data.totalAbonos,
                    'es-CO',
                    'COP')
                document.getElementById("valorAbonoPrevio").value = data.totalAbonos

                let valorPagar = parseFloat(data.PaqueteVenta.precio) - parseFloat(data.totalAbonos)

                document.getElementById("valorTotalPaquete").innerText = formatCurrency(valorPagar, 'es-CO',
                    'COP')
                document.getElementById("valotTotalVentPaq").value = valorPagar
                document.getElementById("consMedio").value = 0
                document.getElementById("medioPago").innerHTML = ""
                document.getElementById("imprimirRegistro").style.display = "none"
                cargarMedioPago()

                //cargar historial de pagos
                let html = ""
                data.historialpagos.forEach(pago => {
                    html += `<tr>
                                <td>${convertirFecha(pago.fecha_pago)}</td>
                                <td>${pago.nombre_usuario}</td>
                                <td>${formatCurrency(pago.pago_realizado, 'es-CO', 'COP')}</td>
                                <td>${pago.nombreMedioPago}</td>
                                <td>
                                        <a onclick="imprimirPagoRecaudo(${pago.id});" style="cursor: pointer;" title="Imprimir recibo de caja" class="text-fade hover-primary"><i class="align-middle"
                                                data-feather="printer"></i></a>
                                        <a onclick="editarPagoRecaudo(${pago.id});" style="cursor: pointer;" title="Editar" class="text-fade hover-warning"><i class="align-middle"
                                                data-feather="edit"></i></a>
                                        <a onclick="eliminarPagoRecaudo(${pago.id});" style="cursor: pointer;" title="Eliminar" class="text-fade hover-warning"><i class="align-middle"
                                                data-feather="trash"></i></a>
                                

                                </td>
                            </tr>`
                });
                document.getElementById("infHistorialPagosPaquete").innerHTML = html
                feather.replace()

            })
            .catch(error => console.error('Error:', error))
    }

    // Función para limpiar el formulario de edición
    function limpiarFormularioEdicion() {
        document.getElementById("idPagoEditar").value = "";
        document.getElementById("fechaPagoEditar").value = "";
        document.getElementById("valorPagoEditar").value = "";
        document.getElementById("valorPagoEditarHidden").value = "";
        document.getElementById("medioPagoEditar").value = "";
        document.getElementById("referenciaPagoEditar").value = "";
        document.getElementById("infoPacienteEditar").innerHTML = "";
        document.getElementById("infoServicioEditar").innerHTML = "";
        document.getElementById("divReferenciaEditar").style.display = "none";
    }

    function editarPagoRecaudo(idPago, tipoEdicion) {
        // Limpiar el formulario antes de abrir
        limpiarFormularioEdicion();

        // Mostrar el modal de edición
        var modal = new bootstrap.Modal(document.getElementById("modalEditarPago"), {
            backdrop: 'static',
            keyboard: false
        });
        modal.show();
        document.getElementById("tipoEdicion").value = tipoEdicion
        // Cargar los datos del pago
        let url = "{{ route('Administracion.obtenerDatosPago') }}";

        fetch(url, {
                method: 'POST',
                async: false,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    idPago: idPago
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const pago = data.pago;

                    // Llenar el formulario con los datos actuales
                    document.getElementById("idPagoEditar").value = pago.id;

                    // Formatear la fecha para el input date (DD-MM-YYYY)
                    const fechaFormateada = pago.fecha;
                    document.getElementById("fechaPagoEditar").value = fechaFormateada;

                    // Formatear el valor como moneda
                    document.getElementById("valorPagoEditar").value = formatCurrency(pago.total, 'es-CO', 'COP');
                    document.getElementById("valorPagoEditarHidden").value = pago.total;

                    // Seleccionar el medio de pago
                    document.getElementById("medioPagoEditar").value = pago.medio_pago;

                    // Mostrar/ocultar campo de referencia según el medio de pago
                    if (pago.medio_pago === 'e') {
                        document.getElementById("divReferenciaEditar").style.display = "none";
                    } else {
                        document.getElementById("divReferenciaEditar").style.display = "block";
                        document.getElementById("referenciaPagoEditar").value = pago.referencia || '';
                    }

                    // Mostrar información del paciente y servicio
                    document.getElementById("infoPacienteEditar").innerHTML =
                        `<strong>Paciente:</strong> ${pago.primer_nombre} ${pago.primer_apellido}`;
                    document.getElementById("infoServicioEditar").innerHTML =
                        `<strong>Servicio:</strong> ${pago.descripcion}`;

                } else {
                    swal("Error", "No se pudieron cargar los datos del pago.", "error");
                }
            })
            .catch(error => {
                console.error('Error:', error);
                swal("Error", "Error al cargar los datos del pago.", "error");
            });
    }

    // Función para manejar el cambio de formato en el campo de valor al editar
    function cambioFormatoEditar(element) {
        let numero = element.value;
        let formatoMoneda = formatCurrency(numero, 'es-CO', 'COP');
        element.value = formatoMoneda;

        // Actualizar el campo hidden con el valor numérico
        document.getElementById("valorPagoEditarHidden").value = numero;
    }

    // Función para manejar el cambio de medio de pago en el modal de edición
    document.getElementById("medioPagoEditar").addEventListener('change', function() {
        const medioPago = this.value;
        const divReferencia = document.getElementById("divReferenciaEditar");

        if (medioPago === 'e') {
            divReferencia.style.display = "none";
            document.getElementById("referenciaPagoEditar").value = '';
        } else {
            divReferencia.style.display = "block";
        }
    });

    // Función para guardar la edición del pago
    function guardarEdicionPago() {
        const idPago = document.getElementById("idPagoEditar").value;
        const fechaPago = document.getElementById("fechaPagoEditar").value;
        const valorPago = document.getElementById("valorPagoEditarHidden").value;
        const medioPago = document.getElementById("medioPagoEditar").value;
        const referencia = document.getElementById("referenciaPagoEditar").value;

        // Validaciones
        if (!fechaPago || !valorPago || !medioPago) {
            swal("Error", "Por favor complete todos los campos obligatorios.", "error");
            return;
        }

        if (parseFloat(valorPago) <= 0) {
            swal("Error", "El valor del pago debe ser mayor a 0.", "error");
            return;
        }

        if (medioPago !== 'e' && !referencia) {
            swal("Error", "Por favor ingrese el número de referencia.", "error");
            return;
        }

        // Enviar datos al servidor
        let url = "{{ route('Administracion.actualizarPagoRecaudo') }}";

        fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    idPago: idPago,
                    fechaPago: fechaPago,
                    valorPago: valorPago,
                    medioPago: medioPago,
                    referencia: referencia
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    swal("¡Éxito!", "El pago fue actualizado exitosamente.", "success");

                    // Cerrar el modal correctamente
                    var modal = bootstrap.Modal.getInstance(document.getElementById("modalEditarPago"));
                    if (modal) {
                        modal.hide();
                    }

                    // Recargar las tablas
                    if (document.getElementById("tipoEdicion").value == "HISTORIAL") {
                        verPago(document.getElementById("idPagoEditarHistorial").value);
                    } else {
                        cargarTablaRecaudos(1);
                        cargarTablaRecaudosPagos(1);
                    }

                    cargarOtraInformacion();

                    // Si estamos en el modal de pagos, recargar también ese historial
                    if (document.getElementById("modalPagos").classList.contains('show')) {
                        realizarPago(document.getElementById("idVentaServicio").value);
                    }

                } else {
                    swal("Alerta", data.message || "Error al actualizar el pago.", "warning");
                }
            })
            .catch(error => {
                console.error('Error:', error);
                swal("Error", "Error al actualizar el pago.", "error");
            });
    }

    function imprimirPagoRecaudo(idRecaudo) {
        fetch("{{ route('Administracion.imprimirRecaudo') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    idRecaudo: idRecaudo
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error al generar el informe.');
                }
                return response.blob(); // Cambiar a blob
            })
            .then(blob => {
                var a = document.createElement('a');
                var url = window.URL.createObjectURL(blob);
                a.href = url;
                a.download = 'Recaudo.pdf';
                a.click();
                window.URL.revokeObjectURL(url);
            })
            .catch(error => console.error('Error:', error));


    }

    function eliminarPagoRecaudo(idPago) {
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
                let url = "{{ route('Administracion.eliminarPagoRecaudo') }}";

                fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        body: JSON.stringify({
                            idPago: idPago
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            swal("¡Buen trabajo!", "El pago fue eliminado exitosamente.", "success")
                            cargarTablaRecaudos(1)
                            cargarTablaRecaudosPagos(1)
                            cargarOtraInformacion()
                            cargarTablaRecaudosEps(1)
                            cargarTablaRecaudosPagosEps(1)
                            var modal = bootstrap.Modal.getInstance(document.getElementById("modalPagos"))
                            modal.hide()

                        } else {
                            console.error('Error en el procesamiento:', data.message)
                        }
                    })
                    .catch(error => console.error('Error:', error))
            }
        });
    }

    function cargarMedioPago() {
        let consMedio = document.getElementById("consMedio").value

        consMedio++;
        let medioPago = '<div style="width: 100%;" id="medioPago' + consMedio +
            '" class="bs-callout-blue-grey callout-border-left callout-bordered callout-transparent mt-1 p-1 medioPago">' +
            '<div class="row">' +
            ' <div class="col-xl-4 col-md-4 col-4">' +
            '     <div class="form-group">' +
            '         <div class="controls">' +
            '             <label>Medio de pago:</label>' +
            '             <select class="select2 form-control"' +
            '                 id="selMedioPago' + consMedio + '" name="selMedioPago[]"' +
            '                 onchange="cammbioMedioPago(' + consMedio + ');">' +
            '                 <option value="">Seleccione...' +
            '                 </option>' +
            '                 <option value="e">Efectivo</option>' +
            '                 <option value="t">Transferencia</option>' +
            '                 <option value="td">Tarjeta de débito</option>' +
            '                 <option value="tc">Tarjeta de crédito</option>' +
            '             </select>' +
            '         </div>' +
            '     </div>' +
            ' </div>' +
            ' <div class="col-xl-3 col-md-3 col-3">' +
            '     <div class="form-group">' +
            '         <div class="controls ">' +
            '             <label>Valor:</label>' +
            '             <input type="text"  valor="0,00" data-cons="' + consMedio +
            '" onchange="cambioFormatoMedioPago(this);" onkeypress="return validartxtnum(event)" onclick="this.select();" class="form-control text-end" id="valorVisPago' +
            consMedio + '" name="valorVisPago">' +
            '             <input type="hidden" class="montMedio" value="0" id="valorPago' +
            consMedio + '" name="valorPago[]">' +
            '         </div>' +
            '     </div>' +
            ' </div>' +
            '<div class="col-xl-4 col-md-4 col-4">' +
            '    <div class="form-group" id="div-tranfe' + consMedio +
            '" style="display: none;">' +
            '         <div class="controls">' +
            '             <label>Número de Referencia:</label>' +
            '             <input type="text"  valor=""  class="form-control" id="referenciaPago" name="referenciaPago[]">' +
            '         </div>' +
            '     </div>' +
            ' </div>' +
            ' <div class="col-xl-1 col-md-3 col-3 align-content-end">' +
            '     <button type="button" title="Eliminar medio de pago" onclick="delMedioPago(' +
            consMedio +
            ');" class="btn btn-icon btn-pure danger mr-1"><i class="fa fa-trash-o"></i></button>' +
            ' </div>' +
            ' </div>' +
            '</div>';

        consMedio++;
        document.getElementById("consMedio").value = consMedio

        document.getElementById("medioPago").insertAdjacentHTML('beforeend', medioPago)
    }

    function cancelarPAgo() {
        let consMedio = document.getElementById("consMedio").value
        for (let i = 1; i <= consMedio; i++) {
            document.getElementById("medioPago" + i).remove()
        }
        document.getElementById("consMedio").value = 0
        var modal = bootstrap.Modal.getInstance(document.getElementById("modalPagos"))
        modal.hide()
    }

    function delMedioPago(cons) {
        let medPag = document.getElementsByClassName("medioPago");
        if (medPag.length == 1) {
            swal("¡Atención!", "Debe haber al menos un medio de pago.", "warning")
            return;
        } else {
            document.getElementById("medioPago" + cons).remove();
            let consMedio = document.getElementById("consMedio").value;
            consMedio--;
            document.getElementById("consMedio").value = consMedio;
        }
    }

    function cammbioMedioPago(cons) {

        let medioPago = document.getElementById("selMedioPago" + cons).value
        if (medioPago == 'e') {
            document.getElementById("div-tranfe" + cons).style.display = "none"
        } else {
            document.getElementById("div-tranfe" + cons).style.display = "block"
        }
        let habilitarAbono = document.getElementById("habilitarAbono").checked

        if (habilitarAbono) {
            let abono = document.getElementById("abono").value
            document.getElementById("valorVisPago" + cons).value = formatCurrency(abono, 'es-CO', 'COP')
            document.getElementById("valorPago" + cons).value = abono
        } else {
            let valorTotal = document.getElementById("valotTotalVentPaq").value
            document.getElementById("valorVisPago" + cons).value = formatCurrency(valorTotal, 'es-CO', 'COP')
            document.getElementById("valorPago" + cons).value = valorTotal
        }

    }

    function cambioFormato(id) {
        let numero = document.getElementById(id)
        let valorVenta = document.getElementById("valotTotalVentPaq").value

        if (parseFloat(numero.value) == 0) {
            swal("¡Atención!", "El valor del abono no puede ser 0.", "warning")
            return

        }

        if (parseFloat(numero.value) > parseFloat(valorVenta)) {
            swal("¡Atención!", "El valor del abono no puede ser mayor al total de la venta.", "warning")
            document.getElementById("abonoVis").value = "$ 0,00"
            document.getElementById("abono").value = "0"
            return
        }
        document.getElementById("abono").value = numero.value
        let formatoMoneda = formatCurrency(numero.value, 'es-CO', 'COP')
        numero.value = formatoMoneda
    }

    function guardarPago() {
        let valorPago = document.getElementById("valorPago1").value
        if (parseFloat(valorPago) == 0) {
            swal("¡Atención!", "Debes de ingresar un valor para el pago", "warning")
            return
        }
        let fechaPago = document.getElementById("fechaPago").value
        if (fechaPago == "") {
            swal("¡Atención!", "Debes de ingresar una fecha para el pago", "warning")
            return
        }

        //bloquear el boton de guardar
        document.getElementById("saveRegistro").setAttribute("disabled", "disabled")

        let valTotalPago = document.getElementById("valotTotalVentPaq").value
        let habilitarAbono = document.getElementById("habilitarAbono").checked
        if (habilitarAbono) {
            let abono = document.getElementById("abono").value
            if (parseFloat(abono) == 0) {
                swal("¡Atención!", "Debes de ingresar un valor para el abono", "warning")
                return
            }

            if (parseFloat(abono) > parseFloat(valTotalPago)) {
                swal("¡Atención!", "El valor del abono no puede ser mayor al total de la venta.", "warning")
                document.getElementById("abonoVis").value = "$ 0,00"
                document.getElementById("abono").value = "0"
                return
            }

            valTotalPago = document.getElementById("abono").value

        }

        var medioPagoMonto = document.getElementsByClassName('montMedio');
        var sumMont = 0;
        for (var i = 0; i < medioPagoMonto.length; i++) {
            var dataIdValor = parseInt(medioPagoMonto[i].getAttribute('value'));
            sumMont = sumMont + dataIdValor;
        }


        if (parseFloat(sumMont) != parseFloat(valTotalPago)) {
            swal("¡Atención!", "El valor de los medios de pago no coincide con el valor total de la venta.", "warning")
            return
        }

        const formVenta = document.getElementById('formVenta');
        const formData = new FormData(formVenta);

        const url = "{{ route('form.guardarPagoVenta') }}";

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

                    //  document.getElementById('idPago').value = data.idPago

                    //mostrar mensaje para indicar que se guardo correctamente y si se quiere imprimir el recibo de caja

                    swal({
                        title: "¡Buen trabajo!",
                        text: "El pago fue registrado exitosamente, ¿Desea imprimir el recibo de caja?",
                        type: "success",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Si, imprimir",
                        cancelButtonText: "No, cancelar",
                        confirmButtonClass: "btn btn-primary",
                        cancelButtonClass: "btn btn-danger ml-1",
                        buttonsStyling: false
                    }, function(isConfirm) {
                        if (isConfirm) {
                            imprimirPagoRecaudo(data.idPago)
                        }
                    });

                    document.getElementById('saveRegistro').setAttribute('disabled', 'disabled')
                    document.getElementById('cancelRegistro').style.display = 'none'
                    document.getElementById('imprimirRegistro').style.display = 'initial'

                    cargarTablaRecaudos(1)
                    cargarTablaRecaudosPagos(1)
                    cargarOtraInformacion()
                    cargarTablaRecaudosEps(1)
                    cargarTablaRecaudosPagosEps(1)

                    //CERRAR MODAL 
                    var modal = bootstrap.Modal.getInstance(document.getElementById("modalPagos"))
                    modal.hide()

                    //  realizarPago(document.getElementById("idVentaServicio").value)

                    document.getElementById("accPago").value = "guardar"

                } else {
                    console.error('Error en el procesamiento:', data.message)
                }
            })
            .catch(error => {
                console.error("Error al enviar los datos:", error)
            });

    }

    function cambioFormatoMedioPago(element) {

        let id = element.getAttribute('data-cons')
        let numero = document.getElementById("valorPago" + id)
        let valorVenta = document.getElementById("valotTotalVentPaq").value


        numero.value = element.value
        let formatoMoneda = formatCurrency(element.value, 'es-CO', 'COP')
        element.value = formatoMoneda

        if (parseFloat(numero.value) == 0) {
            swal("¡Atención!", "El valor del pago no puede ser 0.", "warning")
            return
        }

        if (parseFloat(numero.value) > parseFloat(valorVenta)) {
            swal("¡Atención!", "El valor del pago no puede ser mayor al total de la venta.", "warning")
            element.value = "$ 0,00"
            numero.value = "0"
            return
        }
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

    function verPago(idVenta) {
        var modal = new bootstrap.Modal(document.getElementById("modalDetallePagos"), {
            backdrop: 'static',
            keyboard: false
        })
        modal.show()
        let url = "{{ route('Administracion.detalleVentaPagosPaciente') }}";
        document.getElementById("idPagoEditarHistorial").value = idVenta;

        fetch(url, {
                method: 'POST',
                async: false,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    idVenta: idVenta
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.PaqueteVenta.tipo == "PAQUETE") {
                    document.getElementById("descripcionPaquete").innerText = data.PaqueteVenta.descripcion.descripcion
                        .descripcion
                } else {
                    document.getElementById("descripcionPaquete").innerText = data.PaqueteVenta.descripcion
                        .descripcion
                }

                document.getElementById("sesionesPaquete").innerText =
                    `Sesión pagadas  ${data.PaqueteVenta.cantidad}`
                document.getElementById("valorPaquete").innerText = formatCurrency(data.PaqueteVenta.valor,
                    'es-CO', 'COP')
                // pagos realizados 
                let html = ""

                data.historialpagos.forEach(pago => {
                    html += `<div class="box mb-15 pull-up">
                                    <div class="box-body ">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center">
                                                <div class="d-flex flex-column font-weight-500 mx-10">
                                                    <a href="#"
                                                        class="text-dark hover-primary mb-1  fs-17">Fecha de pago: ${convertirFecha(pago.fecha_pago)}</a>
                                                    <span class="text-fade">${formatCurrency(pago.pago_realizado, 'es-CO', 'COP')}</span>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="d-flex flex-column font-weight-500">
                                                    <a href="#"
                                                        class="text-dark text-end hover-primary mb-1">Usuario responsable: ${pago.nombre_usuario}</a>
                                                    <span class="text-success"></span>
                                                </div>
                                            </div>
                                            <div class="text-end mt-3">
                                            <button class="btn btn-primary btn-sm" onclick="imprimirPagoRecaudo(${pago.id})">
                                            <i class="fa fa-print"></i> Imprimir recibo de caja
                                            </button>
                                            <button class="btn btn-success btn-sm" onclick="editarPagoRecaudo(${pago.id }, 'HISTORIAL')">
                                            <i class="fa fa-edit"></i> Editar pago
                                            </button>
                                            <button class="btn btn-warning btn-sm" onclick="eliminarRecaudo(${pago.id})">
                                            <i class="fa fa-trash"></i> Eliminar pago
                                            </button>
                                        </div>
                                        </div>
                                    </div>
                                </div>`
                });
                document.getElementById("infHistoriaPagos").innerHTML = html


            })
            .catch(error => console.error('Error:', error))


    }

    function eliminarRecaudo(idPago) {

        swal({
            title: "¡Atención!",
            text: "¿Estás seguro de eliminar el pago?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Si, eliminar",
            cancelButtonText: "No, cancelar",
            confirmButtonClass: "btn btn-primary",
            cancelButtonClass: "btn btn-danger ml-1",
            buttonsStyling: false
        }, function(isConfirm) {
            if (isConfirm) {
                let url = "{{ route('Administracion.eliminarPagoRecaudo') }}";

                fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        body: JSON.stringify({
                            idPago: idPago
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            swal("¡Buen trabajo!", "El pago fue eliminado exitosamente.", "success")
                            cargarTablaRecaudos(1)
                            cargarTablaRecaudosPagos(1)
                            cargarOtraInformacion()
                            cargarTablaRecaudosEps(1)
                            cargarTablaRecaudosPagosEps(1)

                            var modal = bootstrap.Modal.getInstance(document.getElementById(
                                "modalDetallePagos"))
                            modal.hide()

                        } else {
                            console.error('Error en el procesamiento:', data.message)
                        }
                    })
                    .catch(error => console.error('Error:', error))
            }
        });
    }
</script>

@endsection