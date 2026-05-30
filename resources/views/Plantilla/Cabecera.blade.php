<header class="main-header">
    <div class="d-flex align-items-center logo-box justify-content-start">
        <!-- Logo -->
        <a href="{{ url('/Administracion') }}" class="logo">
            <!-- logo-->
            <div class="logo-mini w-40">
                <span class="light-logo"><img src="{{ asset('app-assets/images/prasca-center.webp') }}"
                        alt="logo"></span>
            </div>
            <div class="logo-lg">
                <span class="light-logo"><img src="{{ asset('app-assets/images/logo-dark-text.png') }}"
                        alt="logo"></span>
                <span class="dark-logo"><img src="{{ asset('app-assets/images/logo-light-text.png') }}"
                        alt="logo"></span>
            </div>
        </a>
    </div>
    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top">
        <!-- Sidebar toggle button-->
        <div class="app-menu">
            <ul class="header-megamenu nav">
                <li class="btn-group nav-item">
                    <a href="#" class="waves-effect waves-light nav-link push-btn btn-primary-light"
                        data-toggle="push-menu" role="button">
                        <i data-feather="menu"></i>
                    </a>
                </li>
            </ul>
        </div>
        <div class="navbar-custom-menu r-side">
            <ul class="nav navbar-nav">
                <!-- Notificación de Cumpleaños -->
                <li class="btn-group nav-item d-xl-inline-flex d-none">
                    <a id="cumpleanos-btn" onclick="abrirCumpleanos()"
                        class="waves-effect waves-light nav-link btn-warning-light svg-bt-icon position-relative" title="Cumpleaños">
                        <i data-feather="gift"></i>
                        <span class="badge bg-danger rounded-pill position-absolute top-0 start-60 translate-middle"
                            id="cumpleanos-badge" style="display: none; font-size: 0.7rem;">0</span>
                    </a>
                </li>

                <li class="btn-group nav-item d-xl-inline-flex d-none">
                    <a href="#" data-provide="fullscreen"
                        class="waves-effect waves-light nav-link btn-primary-light svg-bt-icon" title="Notificaciones">
                        <i data-feather="maximize"></i>
                    </a>
                </li>
                <!-- User Account-->

                <li class="dropdown user user-menu">
                    <a href="#"
                        class="waves-effect waves-light dropdown-toggle w-auto l-h-12 bg-transparent p-0 no-shadow"
                        title="User" data-bs-toggle="modal" data-bs-target="#quick_user_toggle">
                        <img src="{{ asset('app-assets/images/FotosUsuarios/' . Auth::user()->foto_usuario) }}"
                            class="avatar rounded bg-primary-light" alt="" />
                    </a>
                </li>

            </ul>
        </div>
    </nav>
</header>




<div class="modal modal-right fade" id="quick_user_toggle" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content slim-scroll3">
            <div class="modal-body p-30 bg-white">
                <div class="d-flex align-items-center justify-content-between pb-30">
                    <h4 class="m-0">{{ Auth::user()->nombre_usuario }}
                    </h4>
                    <a href="#" class="btn btn-icon btn-primary-light btn-sm no-shadow" data-bs-dismiss="modal">
                        <span class="fa fa-close"></span>
                    </a>
                </div>
                <div>
                    <div class="d-flex flex-row">
                        <div class=""><img
                                src="{{ asset('app-assets/images/FotosUsuarios/' . Auth::user()->foto_usuario) }}"
                                alt="user" class="rounded bg-primary-light w-150" width="100"></div>
                        <div class="ps-20">
                            <h5 class="mb-0">{{ Auth::user()->nombre_usuario }}</h5>
                            <p class="my-5 text-fade">{{ Session::get('perfilUsuario') }}</p>
                            <a href="mailto:dummy@gmail.com"><span
                                    class="icon-Mail-notification me-5 text-primary"><span class="path1"></span><span
                                        class="path2"></span></span> {{ Auth::user()->email_usuario }}</a>
                        </div>
                    </div>
                </div>
                <div class="dropdown-divider my-30"></div>
                <div>
                    <div class="d-flex align-items-center mb-30">
                        <div class="me-15 bg-primary-light h-50 w-50 l-h-60 rounded text-center">
                            <span class="icon-Library fs-24"><span class="path1"></span><span
                                    class="path2"></span></span>
                        </div>
                        <div class="d-flex flex-column fw-500">
                            <a href="{{url('/Administracion/perfil')}}" class="text-dark hover-primary mb-1 fs-16">Mi perfil</a>
                            <span class="text-fade">Configuración de cuenta</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-30">
                        <div class="me-15 bg-primary-light h-50 w-50 l-h-60 rounded text-center">
                            <span class="icon-Sign-out fs-24"><span class="path1"></span><span
                                    class="path2"></span></span>
                        </div>
                        <div class="d-flex flex-column fw-500">
                            <a href="{{ url('/Logout') }}" class="text-dark hover-primary mb-1 fs-16">Cerrar sesión</a>
                            <span class="text-fade">Salir de la plataforma</span>
                        </div>
                    </div>
                </div>
                <div class="dropdown-divider my-30"></div>

            </div>
        </div>
    </div>
</div>

<!-- Modal de Cumpleaños Mejorado -->
<div class="modal modal-right fade" id="cumpleanos" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary-light">
                <h4 class="modal-title m-0">
                   
                    🎉 Cumpleaños Hoy 🎉
                </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="text-center py-4 bg-light" style="background-color: #f7f7fb !important">
                    <h5 class="text-primary mb-2">¡Es un día especial para celebrar!</h5>
                    <p class="text-muted mb-0">Nuestros pacientes que cumplen años hoy</p>
                </div>
                <div id="cumpleanos-modal-content" class="p-4">
                    <div class="text-center">
                        <div class="spinner-border text-warning" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mt-2 text-muted">Cargando información de cumpleaños...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light" style="background-color: #f7f7fb !important">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                    <i class="fa fa-times me-1"></i> Cerrar
                </button>
                <button type="button" class="btn btn-warning" data-bs-dismiss="modal" onclick="noMostrarCumpleanos()">
                    <i class="fa fa-eye-slash me-1"></i> No mostrar más
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Función para cargar y mostrar cumpleaños
    function cargarCumpleanosModal() {
        $.ajax({
            url: '{{ route("cumpleanos.datos") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    // Actualizar el badge de la notificación
                    const badge = $('#cumpleanos-badge');
                    if (response.totalHoy > 0) {
                        badge.text(response.totalHoy).show();
                        mostrarCumpleanosEnModal(response.pacientesHoy);
                        $('#cumpleanos').modal('show');
                    } else {
                        badge.hide();
                        // Mostrar mensaje de que no hay cumpleaños hoy
                        $('#cumpleanos-modal-content').html(`
                            <div class="text-center py-5">
                                 <i class="fa fa-birthday-cake mb-3" style="font-size: 3rem; color: #E0BEF2;"></i>
                                <h5 class="text-muted">No hay cumpleaños hoy</h5>
                                <p class="text-muted">¡Pero siempre es un buen día para celebrar la vida!</p>
                            </div>
                        `);
                        $('#cumpleanos').modal('show');
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar cumpleaños:', error);
            }
        });
    }

    function noMostrarCumpleanos() {
        $('#cumpleanos').modal('hide');
        $('#cumpleanos-badge').hide();
        sessionStorage.setItem('noMostrarCumpleanos', 'true');
    }

    // Función para mostrar los cumpleaños en el modal
    function mostrarCumpleanosEnModal(pacientes) {
        console.log(pacientes);
        const modalContent = $('#cumpleanos-modal-content');
        let html = '<div class="row">';

        pacientes.forEach(function(paciente) {
            const nombreCompleto = paciente.primer_nombre + ' ' +
                (paciente.segundo_nombre ? paciente.segundo_nombre + ' ' : '') +
                paciente.primer_apellido + ' ' +
                (paciente.segundo_apellido ? paciente.segundo_apellido : '');

            const fechaNacimiento = new Date(paciente.fecha_nacimiento + 'T00:00:00');
            const fechaFormateada = fechaNacimiento.toLocaleDateString('es-ES', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });

            html += `
                      <div class="col-md-6 mb-3">
                        <div class="card border-primary shadow-sm">
                            <div class="card-body text-center">
                                <div class="position-relative">
                                    ${paciente.foto ? 
                                        `<img src="{{ asset('app-assets/images/FotosPacientes/') }}/${paciente.foto}" 
                                              class="rounded-circle mb-3" 
                                              style="width: 60px; height: 60px; object-fit: cover;" 
                                              alt="Foto del paciente">` :
                                        `<div class="rounded-circle bg-primary d-inline-flex align-items-center justify-content-center mb-3" 
                                              style="width: 60px; height: 60px;">
                                             <i class="fas fa-user text-white" style="font-size: 1.5rem;"></i>
                                         </div>`
                                    }
                                    <div class="position-absolute top-0 end-0">
                                        <span class="badge bg-primary text-dark">
                                            <i class="fa fa-birthday-cake"></i> ¡Hoy!
                                        </span>
                                    </div>
                                </div>
                                <h6 class="card-title">${nombreCompleto}</h6>
                                <p class="card-text text-muted">
                                    <i class="fa fa-birthday-cake"></i> ${calcularEdad(paciente.fecha_nacimiento)}años
                                </p>
                            </div>
                        </div>
                    </div>
            `;
        });

        html += '</div>';
        modalContent.html(html);
    }

    // Función para abrir el modal de cumpleaños
    function abrirCumpleanos() {
        
        sessionStorage.setItem('noMostrarCumpleanos', 'false');
        cargarCumpleanosModal();
    }

    function calcularEdad(fechaNacimiento) {
        const hoy = new Date();
        const fecha = new Date(fechaNacimiento);
        let anios = hoy.getFullYear() - fecha.getFullYear()
        let meses = hoy.getMonth() - fecha.getMonth()
        let dias = hoy.getDate() - fecha.getDate()

           // Ajustar si los meses o días son negativos
           if (meses < 0 || (meses === 0 && dias < 0)) {
            anios--
            meses += 12
        }
        if (dias < 0) {
            const ultimoDiaMesAnterior = new Date(hoy.getFullYear(), hoy.getMonth(), 0).getDate();
            dias += ultimoDiaMesAnterior
            meses--
        }

        const edad =
            `${anios} ${anios === 1 ? 'Año' : 'Años'}, ${meses} ${meses === 1 ? 'Mes' : 'Meses'} y ${dias} ${dias === 1 ? 'Día' : 'Días'}`;

        return edad;
    }

    // Cargar cumpleaños al cargar la página si hay cumpleaños hoy
    document.addEventListener('DOMContentLoaded', function() {
        // Verificar si hay cumpleaños hoy y mostrar modal automáticamente
        $.ajax({
            url: '{{ route("cumpleanos.datos") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    // Actualizar el badge de la notificación
                    const badge = $('#cumpleanos-badge');
                    if (response.totalHoy > 0 && sessionStorage.getItem('noMostrarCumpleanos') !== 'true') {
                        badge.text(response.totalHoy).show();
                        // Mostrar modal automáticamente si hay cumpleaños
                        
                        mostrarCumpleanosEnModal(response.pacientesHoy);
                        setTimeout(function() {
                            $('#cumpleanos').modal('show');
                            $('#modalCumpleanos').modal('hide');
                        }, 5000); // Mostrar después de 1 segundo para que la página cargue completamente
                    } else {
                        badge.hide();
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al verificar cumpleaños:', error);
            }
        });
    });
</script>