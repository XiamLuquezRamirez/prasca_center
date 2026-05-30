@extends('Plantilla.Principal')
@section('title', 'Backup de formularios')
@section('Contenido')

    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h4 class="page-title">Backup de formularios</h4>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#"><i class="mdi mdi-home-outline"></i></a></li>
                            <li class="breadcrumb-item" aria-current="page">Inicio</li>
                            <li class="breadcrumb-item active" aria-current="page">Backup de formularios</li>
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
                        <h5 class="card-title">Backup de formularios</h5>
                    </div>
                    <div class="card-body">
                        <div class="box-controls pull-right">
                            <div class="box-header-actions">
                                <div class="input-group input-group-merge">
                                    <input type="text" id="busqueda" class="form-control">
                                    <div class="input-group-text" data-password="false">
                                        <span class="fa fa-search"></span>
                                    </div>                                   
                                </div>

                            </div>
                        </div>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th style="width:30%;">Paciente</th>
                                    <th style="width:20%;">Usuario</th>
                                    <th style="width:20%;">Formulario</th>
                                    <th style="width:20%;">Fecha</th>
                                    <th style="width:10%;">Acciones</th>
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

    <div class="modal fade" id="modalDetalleBackup" tabindex="-1" role="dialog" aria-labelledby="modalDetalleBackupLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myLargeModalLabel">Detalle del Backup</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
                <div class="modal-body">
                    <div id="detalleBackupContent" class="card" style="height: 500px; overflow-y: auto;"></div>
                </div>
                <div class="modal-footer text-end">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"> <i class="ti-close"></i> Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    
    

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let menuP = document.getElementById("principalParametros");
            let menuS = document.getElementById("principalParametrosBackup");

            menuP.classList.add("active", "menu-open");
            menuS.classList.add("active");

            loader = document.getElementById('loader')
            loadNow(1)
   
            cargarListaBackup(1)

            // Evento click para la paginación
            document.addEventListener('click', function(event) {
                if (event.target.matches('#paginacion a')) {
                    event.preventDefault();
                    var href = event.target.getAttribute('href');
                    var page = href.split('page=')[1];
                    var searchTerm = document.getElementById('busqueda').value

                    // Asegurarse de que 'page' sea un número antes de hacer la solicitud
                    if (!isNaN(page)) {
                        cargarListaBackup(page, searchTerm);
                    }
                }
            });
            // Evento input para el campo de búsqueda
            document.getElementById('busqueda').addEventListener('input', function() {
                var searchTerm = this.value;
                cargarListaBackup(1,
                    searchTerm);
            });
        })

        function cargarListaBackup(page, searchTerm = '') {
            let url = "{{ route('Adminitraccion.listaBackup') }}"

            var oldPageInput = document.getElementById('page')
            var oldSearchTermInput = document.getElementById('searchTerm')
            if (oldPageInput) oldPageInput.remove()
            if (oldSearchTermInput) oldSearchTermInput.remove()

            var data = {
                page: page,
                search: searchTerm
            }   

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
                    document.getElementById('trRegistros').innerHTML = responseData.backups
                    feather.replace()
                    document.getElementById('pagination-links').innerHTML = responseData.links
                    loadNow(0)
                })
                .catch(error => console.error('Error:', error))

        }

        function verDetalleBackup(id) {
                var modal = new bootstrap.Modal(document.getElementById("modalDetalleBackup"), {
            backdrop: 'static',
            keyboard: false
        })

        modal.show()

            let url = "{{ route('Adminitraccion.verDetalleBackup') }}";
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    id: id
                })
            })
            .then(response => response.json())
            .then(data => {
                const datos = JSON.parse(data.datos);

                let html = '';
                for (const clave in datos) {
                    html += '<div class="card">';
                    html += '<div class="card-body">';
                    html += '<h5 class="card-title font-weight-bold">' + clave + '</h5>';
                    html += '<p class="card-text">' + (datos[clave] ? datos[clave] : 'No hay datos') + '</p>';
                    html += '</div>';
                    html += '</div>';
                }

                document.getElementById('detalleBackupContent').innerHTML = html;
            })
        }

    </script>

@endsection
