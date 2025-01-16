<aside class="main-sidebar">
    <!-- sidebar-->
    <section class="sidebar position-relative">
        <div class="multinav">
            <div class="multinav-scroll" style="height: 97%;">
                <!-- sidebar menu-->
                <ul class="sidebar-menu" data-widget="tree">
                    <li id="agenda">
                        <a href="{{ url('/Administracion') }}">
                            <i data-feather="calendar"></i>
                            <span>Agenda</span>
                        </a>
                    </li>
                    <li id="principalPacientes">
                        <a href="{{ url('/Pacientes/Gestionar') }}">
                            <i data-feather="users"></i>
                            <span>Pacientes</span>
                        </a>
                    </li>
                    <li id="principalHistoriClinica" class="treeview">
                        <a href="#">
                            <i data-feather="grid"></i>
                            <span>Historias clinicas</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-right pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li>
                            <li id="principalHistoriClinicaPsicologia">
                                <a href="{{ url('/HistoriasClinicas/GestionarHistoriaPsicologia') }}">
                                    <i class="icon-Commit"><span class="path1"></span><span
                                            class="path2"></span></i>Psicológica
                                </a>
                            </li>
                            <li id="principalHistoriClinicaNeuropsicología">
                                <a href="{{ url('/HistoriasClinicas/GestionarHistoriaNeuroPsicologia') }}">
                                    <i class="icon-Commit"><span class="path1"></span><span
                                            class="path2"></span></i>Neuropsicologíca
                                </a>
                            </li>
                    </li>
                </ul>
                </li>
                <li id="principalInformes" class="treeview">
                    <a href="#">
                        <i data-feather="bar-chart"></i>
                        <span>Informes</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-right pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <li>
                        <li id="informePsicologia">
                            <a href="{{ url('/HistoriasClinicas/InformePsicologia') }}">
                                <i class="icon-Commit"><span class="path1"></span><span
                                        class="path2"></span></i>Informe psicológico
                            </a>
                        </li>
                        <li id="principalInformes2" style="display: none;">
                            <a href="{{ url('/HistoriasClinicas/GestionarHistoriaNeuroPsicologia') }}">
                                <i class="icon-Commit"><span class="path1"></span><span
                                        class="path2"></span></i>Informe 2
                            </a>
                        </li>
                </li>
                </ul>
                </li>
                <li id="principalParametros" class="treeview">
                    <a href="#">
                        <i data-feather="settings"></i>
                        <span>Adminitración</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-right pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <li id="principalParametrosProfesionales"><a
                                href="{{ url('/Administracion/Profesionales') }}"><i class="icon-Commit"><span
                                        class="path1"></span><span class="path2"></span></i>Profesionales</a></li>
                        <li id="principalParametrosEspecialidades"><a
                                href="{{ url('/Administracion/Especialidades') }}"><i class="icon-Commit"><span
                                        class="path1"></span><span class="path2"></span></i>Motivo de consulta</a>
                        </li>
                    </ul>
                </li>

                <li id="principalUsuarios" class="treeview">
                    <a href="#">
                        <i data-feather="bar-chart"></i>
                        <span>Gestionar usuarios</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-right pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <li>
                        <li id="usuarios">
                            <a href="{{ url('/Administracion/Usuarios') }}">
                                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>
                                Usuarios
                            </a>
                        </li>
                        <li id="perfiles">
                            <a href="{{ url('/Administracion/Perfiles') }}">
                                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>
                                Perfiles
                            </a>
                        </li>
                </li>
                </ul>
                </li>


                </ul>
                <div class="sidebar-widgets">
                    <div class="mx-25 mb-30 pb-20 side-bx bg-primary-light rounded20">
                        <div class="text-center">
                            <img src="{{ asset('app-assets/images/svg-icon/Psicologia.png') }}" class="sideimg p-6"
                                alt="">
                            {{-- <h4 class="title-bx text-primary"></h4> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</aside>
