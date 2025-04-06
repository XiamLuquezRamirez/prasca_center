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
               
                <li class="btn-group nav-item">
                    <a href="#" class="waves-effect waves-light nav-link btn-primary-light svg-bt-icon" data-bs-toggle="modal" data-bs-target="#notificationModal">
                        <i data-feather="bell"></i>
                    </a>
                    <span class="notification-badge">1</span>
                </li>

                <li class="btn-group nav-item d-xl-inline-flex d-none">
                    <a href="#" data-provide="fullscreen"
                        class="waves-effect waves-light nav-link btn-primary-light svg-bt-icon" title="Full Screen">
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

<div class="modal modal-right fade" id="notificationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content slim-scroll3">
            <div class="modal-body p-30 bg-white">
                <h4 class="m-0">Notificaciones</h4>
                <div class="notification-section">
                    <div class="notification-icon">
                        <i data-feather="bell"></i>
                    </div>
                    <div class="notification-badge">
                        1
                    </div>
                </div>

                <div class="notifications-list">
                    <div class="notification-item">
                        <div class="notification-icon">
                            <i data-feather="bell"></i>
                        </div>
                        <div class="notification-content">
                            <h5>Notificación</h5>
                            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quos.</p>
                            <p>2024-01-01</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


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
