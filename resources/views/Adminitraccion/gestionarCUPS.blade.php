@extends('Plantilla.Principal')
@section('title', 'Gestionar Códigos (CUPS)')
@section('Contenido')
    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h4 class="page-title">Gestionar Códigos (CUPS)</h4>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#"><i class="mdi mdi-home-outline"></i></a></li>
                            <li class="breadcrumb-item" aria-current="page">Inicio</li>
                            <li class="breadcrumb-item active" aria-current="page">Gestionar Códigos (CUPS)</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header p-0 border-bottom-0">
                        <ul class="nav nav-tabs" id="tabsCUPS" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="tab-cups" data-bs-toggle="tab" href="#panel-cups" role="tab">
                                    <i class="fa fa-list me-1"></i> Códigos CUPS
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab-sh" data-bs-toggle="tab" href="#panel-sh" role="tab"
                                   onclick="cargarServiciosHabilitados(1)">
                                    <i class="fa fa-link me-1"></i> Servicios Habilitados
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="tab-content card-body">

                        {{-- ===== TAB 1: CUPS ===== --}}
                        <div class="tab-pane fade show active" id="panel-cups" role="tabpanel">
                            <div class="box-controls pull-right mb-3">
                                <div class="input-group input-group-merge">
                                    <input type="text" id="busqueda" class="form-control" placeholder="Buscar...">
                                    <div class="input-group-text"><span class="fa fa-search"></span></div>
                                    <button type="button" onclick="nuevoRegistro(1);" class="btn btn-xs btn-primary font-bold ms-2">
                                        <i class="fa fa-plus"></i> Nuevo CUPS
                                    </button>
                                </div>
                            </div>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th style="width:10%;">Código</th>
                                        <th style="width:70%;">Nombre</th>
                                        <th style="width:10%;">Estado</th>
                                        <th style="width:10%;">Acción</th>
                                    </tr>
                                </thead>
                                <tbody id="trRegistros"></tbody>
                            </table>
                            <div id="pagination-links" class="text-center mt-2"></div>
                        </div>

                        {{-- ===== TAB 2: SERVICIOS HABILITADOS ===== --}}
                        <div class="tab-pane fade" id="panel-sh" role="tabpanel">
                            <div class="box-controls pull-right mb-3">
                                <div class="input-group input-group-merge">
                                    <input type="text" id="busquedaSH" class="form-control" placeholder="Buscar por código CUPS o nombre...">
                                    <div class="input-group-text"><span class="fa fa-search"></span></div>
                                    <button type="button" onclick="nuevoServicioHabilitado();" class="btn btn-xs btn-primary font-bold ms-2">
                                        <i class="fa fa-plus"></i> Nuevo
                                    </button>
                                </div>
                            </div>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Código CUPS</th>
                                        <th>Cód. Servicio</th>
                                        <th>Nombre Servicio</th>
                                        <th>Grupo</th>
                                        <th>Modalidad</th>
                                        <th>Estado</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody id="trServiciosHabilitados"></tbody>
                            </table>
                            <div id="pagination-links-sh" class="text-center mt-2"></div>
                        </div>

                    </div><!-- /tab-content -->
                </div>
            </div>
        </div>
    </section>

    {{-- ===== MODAL CUPS ===== --}}
    <div class="modal fade" id="modalCUPS" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width:40%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="tituloAccion">Agregar Códigos (CUPS)</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formCUPS">
                        <input type="hidden" name="accRegistro" id="accRegistro" value="guardar">
                        <input type="hidden" name="idRegistro" id="idRegistro" value="">
                        <input type="hidden" name="codigoOriginal" id="codigoOriginal" value="">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Código:</label>
                                    <input type="text" class="form-control" id="codigo" name="codigo">
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label class="form-label">Nombre:</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label">Descripción:</label>
                                    <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label">Habilitado:</label>
                                    <select class="form-control" id="habilitado" name="habilitado">
                                        <option value="SI">SI</option>
                                        <option value="NO">NO</option>
                                    </select>
                                </div>
                            </div>
                            <div class="box-footer text-end mt-2">
                                <button type="button" onclick="nuevoRegistro(2);" style="display:none;" id="newRegistro" class="btn btn-primary-light me-1">
                                    <i class="ti-plus"></i> Nuevo
                                </button>
                                <button type="button" id="cancelRegistro" onclick="cancelarRegistro();" class="btn btn-primary-light me-1">
                                    <i class="ti-close"></i> Cancelar
                                </button>
                                <button type="button" id="saveRegistro" onclick="guardarRegistro();" class="btn btn-primary">
                                    <i class="ti-save"></i> Guardar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== MODAL SERVICIO HABILITADO ===== --}}
    <div class="modal fade" id="modalServicioHabilitado" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width:50%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="tituloSH">Agregar Servicio Habilitado</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formSH">
                        <input type="hidden" name="accRegistroSH" id="accRegistroSH" value="guardar">
                        <input type="hidden" name="idRegistroSH" id="idRegistroSH" value="">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Código CUPS: <span class="text-danger">*</span></label>
                                    <select class="form-control select2-cups-sh" id="codigoCupsSH" name="codigo_cups" style="width:100%;"></select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Código Servicio (REPS): <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="codigoServicioSH" name="codigo_servicio" placeholder="Ej: 344">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Grupo: <span class="text-danger">*</span></label>
                                    <select class="form-control" id="grupoServicioSH" name="grupo_servicio">
                                        <option value="01">01 - Consulta externa</option>
                                        <option value="02">02 - Urgencias</option>
                                        <option value="03">03 - Hospitalización</option>
                                        <option value="04">04 - Procedimientos quirúrgicos</option>
                                        <option value="05">05 - Apoyo diagnóstico</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Modalidad: <span class="text-danger">*</span></label>
                                    <select class="form-control" id="modalidadSH" name="modalidad">
                                        <option value="01">01 - Intramural</option>
                                        <option value="02">02 - Extramural</option>
                                        <option value="03">03 - Unidad móvil</option>
                                        <option value="04">04 - Domiciliaria</option>
                                        <option value="05">05 - Telesalud</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="form-label">Nombre del Servicio: <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nombreServicioSH" name="nombre_servicio" placeholder="Ej: Consulta Primera Vez por Psicología">
                                </div>
                            </div>
                            <div class="col-md-4" id="wrapActivoSH" style="display:none;">
                                <div class="form-group">
                                    <label class="form-label">Estado:</label>
                                    <select class="form-control" id="activoSH" name="activo">
                                        <option value="1">Activo</option>
                                        <option value="0">Inactivo</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12 text-end mt-2">
                                <button type="button" class="btn btn-primary-light me-1" data-bs-dismiss="modal">
                                    <i class="ti-close"></i> Cancelar
                                </button>
                                <button type="button" class="btn btn-primary" onclick="guardarServicioHabilitado();">
                                    <i class="ti-save"></i> Guardar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let menuP = document.getElementById("principalParametros");
            let menuS = document.getElementById("principalParametrosCUPS");
            menuP.classList.add("active", "menu-open");
            menuS.classList.add("active");

            loader = document.getElementById('loader');
            loadNow(1);

            // Validación CUPS
            $("#formCUPS").validate({
                rules: {
                    codigo: {
                        required: true,
                        remote: {
                            url: "/verificar-codigo-cups",
                            type: "post",
                            data: {
                                codigo:     function () { return $("#codigo").val(); },
                                idRegistro: function () { return $("#idRegistro").val(); },
                                _token:     function () { return "{{ csrf_token() }}"; }
                            }
                        }
                    },
                    nombre: { required: true }
                },
                messages: {
                    nombre: { required: "Por favor, ingrese el nombre del CUPS." },
                    codigo: { required: "Por favor, ingrese el código del CUPS.", remote: "Este código ya está registrado." }
                },
                submitHandler: function () { guardarRegistro(); }
            });

            cargar(1);

            // Paginación CUPS
            document.addEventListener('click', function (event) {
                if (event.target.matches('#paginacion a')) {
                    event.preventDefault();
                    var page = event.target.getAttribute('href').split('page=')[1];
                    if (!isNaN(page)) cargar(page, document.getElementById('busqueda').value);
                }
            });
            document.getElementById('busqueda').addEventListener('input', function () {
                cargar(1, this.value);
            });
            document.getElementById('busquedaSH').addEventListener('input', function () {
                cargarServiciosHabilitados(1, this.value);
            });

            // Select2 para CUPS en modal de Servicio Habilitado
            $('.select2-cups-sh').select2({
                dropdownParent: $('#modalServicioHabilitado'),
                placeholder: 'Buscar CUPS...',
                minimumInputLength: 1,
                language: {
                    inputTooShort: function () { return 'Ingresa al menos 1 carácter'; },
                    noResults:     function () { return 'No se encontraron resultados.'; },
                    searching:     function () { return 'Buscando...'; }
                },
                ajax: {
                    transport: function (params, success, failure) {
                        const q = params.data.q || '';
                        fetch(`/historia/buscaCUPS?q=${encodeURIComponent(q)}&page=1`, {
                            method: 'GET',
                            headers: { 'Content-Type': 'application/json' }
                        })
                        .then(r => r.json())
                        .then(data => success({ results: data.data, pagination: { more: false } }))
                        .catch(failure);
                    }
                },
                escapeMarkup: function (m) { return m; }
            });
        });

        // ==================== CUPS ====================

        function guardarRegistro() {
            if (!$("#formCUPS").valid()) return;
            const formData = new FormData(document.getElementById('formCUPS'));
            fetch("{{ route('form.guardarCUPS') }}", {
                method: 'POST',
                body: formData,
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    swal("¡Buen trabajo!", "La operación fue realizada exitosamente.", "success");
                    document.getElementById('saveRegistro').setAttribute('disabled', 'disabled');
                    document.getElementById('newRegistro').style.display = 'initial';
                    document.getElementById('cancelRegistro').style.display = 'none';
                    cargar(1);
                    document.getElementById("accRegistro").value = "guardar";
                }
            });
        }

        function editarRegistro(id) {
            var modal = new bootstrap.Modal(document.getElementById("modalCUPS"), { backdrop: 'static', keyboard: false });
            document.getElementById("accRegistro").value = 'editar';
            document.getElementById("idRegistro").value = id;
            document.getElementById('saveRegistro').removeAttribute('disabled');
            document.getElementById("tituloAccion").innerHTML = "Editar CUPS";
            modal.show();

            fetch("{{ route('cups.buscaCUPS') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
                body: JSON.stringify({ idRegistro: id })
            })
            .then(r => r.json())
            .then(data => {
                document.getElementById("nombre").value      = data.nombre;
                document.getElementById("codigo").value      = data.codigo;
                document.getElementById("descripcion").value = data.descripcion;
                document.getElementById("habilitado").value  = data.habilitado;
            });
        }

        function cancelarRegistro() { document.getElementById('formCUPS').reset(); }

        function nuevoRegistro(opc) {
            if (opc == 1) {
                var modal = new bootstrap.Modal(document.getElementById("modalCUPS"), { backdrop: 'static', keyboard: false });
                modal.show();
            }
            cancelarRegistro();
            document.getElementById('saveRegistro').removeAttribute('disabled');
            document.getElementById('newRegistro').style.display = 'none';
            document.getElementById('cancelRegistro').style.display = 'initial';
            document.getElementById("accRegistro").value = "guardar";
            document.getElementById("tituloAccion").innerHTML = "Agregar CUPS";
        }

        function eliminarRegistro(idReg) {
            swal({ title: "¿Está seguro?", text: "No podrá recuperar este registro.", type: "warning",
                   showCancelButton: true, confirmButtonColor: "#fec801", confirmButtonText: "Sí, eliminar",
                   cancelButtonText: "No, cancelar", closeOnConfirm: false, closeOnCancel: false
            }, function (isConfirm) {
                if (isConfirm) {
                    fetch("{{ route('cups.eliminarCUPS') }}", {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
                        body: JSON.stringify({ idReg: idReg })
                    }).then(r => r.json()).then(data => {
                        if (data.success) { swal("¡Hecho!", data.message, "success"); cargar(1); }
                    });
                } else { swal("Cancelado", "Tu registro está salvo :)", "error"); }
            });
        }

        function cargar(page, searchTerm = '') {
            fetch("{{ route('cups.listaCUPS') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
                body: JSON.stringify({ page: page, search: searchTerm })
            })
            .then(r => r.json())
            .then(data => {
                document.getElementById('trRegistros').innerHTML = data.cups;
                document.getElementById('pagination-links').innerHTML = data.links;
                feather.replace();
                loadNow(0);
            });
        }

        // ==================== SERVICIOS HABILITADOS ====================

        function cargarServiciosHabilitados(page, searchTerm = '') {
            fetch("{{ route('cups.listaServiciosHabilitados') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
                body: JSON.stringify({ page: page, search: searchTerm })
            })
            .then(r => r.json())
            .then(data => {
                document.getElementById('trServiciosHabilitados').innerHTML = data.registros;
                document.getElementById('pagination-links-sh').innerHTML = data.links;
                feather.replace();
            });
        }

        function nuevoServicioHabilitado() {
            document.getElementById('formSH').reset();
            document.getElementById('accRegistroSH').value = 'guardar';
            document.getElementById('idRegistroSH').value  = '';
            document.getElementById('wrapActivoSH').style.display = 'none';
            document.getElementById('tituloSH').innerHTML = 'Agregar Servicio Habilitado';
            $('.select2-cups-sh').val(null).trigger('change');
            new bootstrap.Modal(document.getElementById('modalServicioHabilitado'), { backdrop: 'static' }).show();
        }

        function editarServicioHabilitado(id) {
            document.getElementById('accRegistroSH').value = 'editar';
            document.getElementById('idRegistroSH').value  = id;
            document.getElementById('wrapActivoSH').style.display = 'block';
            document.getElementById('tituloSH').innerHTML = 'Editar Servicio Habilitado';

            fetch("{{ route('cups.buscarServicioHabilitado') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
                body: JSON.stringify({ idRegistroSH: id })
            })
            .then(r => r.json())
            .then(data => {
                // Cargar CUPS en select2
                var opt = new Option(data.codigo_cups, data.codigo_cups, true, true);
                $('.select2-cups-sh').append(opt).trigger('change');

                document.getElementById('codigoServicioSH').value = data.codigo_servicio;
                document.getElementById('nombreServicioSH').value = data.nombre_servicio;
                document.getElementById('grupoServicioSH').value  = data.grupo_servicio;
                document.getElementById('modalidadSH').value      = data.modalidad;
                document.getElementById('activoSH').value         = data.activo;

                new bootstrap.Modal(document.getElementById('modalServicioHabilitado'), { backdrop: 'static' }).show();
            });
        }

        function guardarServicioHabilitado() {
            const codigoCups     = $('.select2-cups-sh').val();
            const codigoServicio = document.getElementById('codigoServicioSH').value.trim();
            const nombreServicio = document.getElementById('nombreServicioSH').value.trim();

            if (!codigoCups || !codigoServicio || !nombreServicio) {
                swal("¡Alerta!", "Complete todos los campos obligatorios.", "warning");
                return;
            }

            const formData = new FormData(document.getElementById('formSH'));
            // Select2 no registra en FormData automáticamente — lo agregamos manualmente
            formData.set('codigo_cups', codigoCups);

            fetch("{{ route('cups.guardarServicioHabilitado') }}", {
                method: 'POST',
                body: formData,
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    swal("¡Buen trabajo!", data.message, "success");
                    bootstrap.Modal.getInstance(document.getElementById('modalServicioHabilitado')).hide();
                    cargarServiciosHabilitados(1, document.getElementById('busquedaSH').value);
                } else {
                    swal("Error", data.message || "No se pudo guardar.", "error");
                }
            });
        }

        function eliminarServicioHabilitado(id) {
            swal({ title: "¿Está seguro?", text: "Se eliminará el mapeo CUPS → Servicio.", type: "warning",
                   showCancelButton: true, confirmButtonColor: "#fec801", confirmButtonText: "Sí, eliminar",
                   cancelButtonText: "No, cancelar", closeOnConfirm: false, closeOnCancel: false
            }, function (isConfirm) {
                if (isConfirm) {
                    fetch("{{ route('cups.eliminarServicioHabilitado') }}", {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
                        body: JSON.stringify({ idReg: id })
                    }).then(r => r.json()).then(data => {
                        if (data.success) { swal("¡Hecho!", data.message, "success"); cargarServiciosHabilitados(1); }
                    });
                } else { swal("Cancelado", "Tu registro está salvo :)", "error"); }
            });
        }
    </script>
@endsection
