<aside class="main-sidebar">
    <!-- sidebar-->
    <section class="sidebar position-relative">
        <div class="multinav">
            <div class="multinav-scroll" style="height: 97%;">
                <!-- sidebar menu-->
                <ul class="sidebar-menu" data-widget="tree">
                    {{-- Agenda --}}
                    @if (in_array('agenda', session('permisos', [])))
                        <li id="agenda">
                            <a href="{{ url('/Administracion') }}">
                                <i data-feather="calendar"></i>
                                <span>Agenda</span>
                            </a>
                        </li>
                    @endif

                    {{-- Pacientes --}}
                    @if (in_array('paciente', session('permisos', [])))
                        <li id="principalPacientes">
                            <a href="{{ url('/Pacientes/Gestionar') }}">
                                <i data-feather="users"></i>
                                <span>Pacientes</span>
                            </a>
                        </li>
                    @endif

                    {{-- Historias Clínicas --}}
                    @if (in_array('histPsicologia', session('permisos', [])) || in_array('histNeuro', session('permisos', [])))
                        <li id="principalHistoriClinica" class="treeview">
                            <a href="#">
                                <i data-feather="folder"></i>
                                <span>Historias clínicas</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-right pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                @if (in_array('histPsicologia', session('permisos', [])))
                                    <li id="principalHistoriClinicaPsicologia">
                                        <a href="{{ url('/HistoriasClinicas/GestionarHistoriaPsicologia') }}">
                                            <i class="icon-Commit"><span class="path1"></span><span
                                                class="path2"></span></i> Psicológica
                                        </a>
                                    </li>
                                @endif
                                @if (in_array('histNeuro', session('permisos', [])))
                                    <li id="principalHistoriClinicaNeuropsicología">
                                        <a href="{{ url('/HistoriasClinicas/GestionarHistoriaNeuroPsicologia') }}">
                                            <i class="icon-Commit"><span class="path1"></span><span
                                                class="path2"></span></i> Neuropsicológica
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    {{-- Recaudo --}}
                    @if (in_array('gestionRecaudo', session('permisos', [])) || in_array('gestionCaja', session('permisos', [])))
                    <li id="principalRecaudo" class="treeview">
                        <a href="#">
                            <i data-feather="dollar-sign"></i>
                            <span>Gestión Financiera</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-right pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            @if (in_array('gestionRecaudo', session('permisos', [])))
                                <li id="principalRecaudoGestionRecaudo">
                                    <a href="{{ url('/Administracion/Recaudos') }}">
                                        <i class="icon-Commit"><span class="path1"></span><span
                                            class="path2"></span></i> Recaudo
                                    </a>
                                </li>
                            @endif
                            @if (in_array('gestionGastos', session('permisos', [])))
                                <li id="principalRecaudoGestionGastos">
                                    <a href="{{ url('/Administracion/Gastos') }}">
                                        <i class="icon-Commit"><span class="path1"></span><span
                                            class="path2"></span></i> Gastos
                                    </a>
                                </li>
                            @endif
                            @if (in_array('gestionCaja', session('permisos', [])))
                                <li  id="principalRecaudoGestionCaja">
                                    <a href="{{ url('/Administracion/Cajas') }}">
                                        <i class="icon-Commit"><span class="path1"></span><span
                                            class="path2"></span></i> Caja
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif

                    {{-- Gestión de informes --}}
                    @if (in_array('informePsicologico', session('permisos', [])) || in_array('informeNeuro', session('permisos', [])))

                        <li id="principalInformes" class="treeview">
                            <a href="#">
                                <i data-feather="file-text"></i>
                                <span>Informes</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-right pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                @if (in_array('informePsicologico', session('permisos', [])))
                                    <li id="informePsicologia">
                                        <a href="{{ url('/HistoriasClinicas/InformePsicologia') }}">
                                            <i class="icon-Commit"><span class="path1"></span><span
                                                    class="path2"></span></i>Informe psicológico
                                        </a>
                                    </li>
                                @endif
                                @if (in_array('informeNeuro', session('permisos', [])))
                                    <li id="informeNeuropsicolologico">
                                        <a href="{{ url('/HistoriasClinicas/InformeNeuropsicologico') }}">
                                            <i class="icon-Commit"><span class="path1"></span><span
                                                    class="path2"></span></i>Informe neuropsicológico
                                        </a>
                                    </li>
                                @endif
                                @if (in_array('reportes', session('permisos', [])))
                                    <li id="reporteGeneral">
                                        <a href="{{ url('/HistoriasClinicas/informes') }}">
                                            <i class="icon-Commit"><span class="path1"></span><span
                                                    class="path2"></span></i>Informes generales
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    {{-- Administración --}}
                    @if (in_array('adminProfesionales', session('permisos', [])) || in_array('AdminMotivoConsulta', session('permisos', [])))
                        <li id="principalParametros" class="treeview">
                            <a href="#">
                                <i data-feather="sliders"></i>
                                <span>Administración</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-right pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                @if (in_array('adminProfesionales', session('permisos', [])))
                                    <li id="principalParametrosProfesionales">
                                        <a href="{{ url('/Administracion/Profesionales') }}">
                                            <i class="icon-Commit"><span class="path1"></span><span
                                                class="path2"></span></i> Profesionales
                                        </a>
                                    </li>
                                @endif
                           
                                @if (in_array('Admineps', session('permisos', [])))
                                    <li id="principalParametrosEPS">
                                        <a href="{{ url('/Administracion/Entidades') }}">
                                            <i class="icon-Commit"><span class="path1"></span><span
                                                class="path2"></span></i> Entidades promotoras
                                        </a>
                                    </li>
                                @endif
                                @if (in_array('AdminMotivoConsulta', session('permisos', [])))
                                <li id="principalParametrosEspecialidades">
                                    <a href="{{ url('/Administracion/Especialidades') }}">
                                        <i class="icon-Commit"><span class="path1"></span><span
                                            class="path2"></span></i> Motivo de consulta
                                    </a>
                                </li>
                            @endif
                                @if (in_array('AdminPaquetes', session('permisos', [])))
                                    <li id="principalParametrosPaquetes">
                                        <a href="{{ url('/Administracion/Paquetes') }}">
                                            <i class="icon-Commit"><span class="path1"></span><span
                                                class="path2"></span></i> Paquetes
                                        </a>
                                    </li>
                                @endif
                                @if (in_array('AdminPruebas', session('permisos', [])))
                                    <li id="principalParametrosPruebas">
                                        <a href="{{ url('/Administracion/Pruebas') }}">
                                            <i class="icon-Commit"><span class="path1"></span><span
                                                class="path2"></span></i> Pruebas
                                        </a>
                                    </li>
                                @endif
                                @if (in_array('AdminSesiones', session('permisos', [])))
                                    <li id="principalParametrosSesiones">
                                        <a href="{{ url('/Administracion/Sesiones') }}">
                                            <i class="icon-Commit"><span class="path1"></span><span
                                                class="path2"></span></i> Sesiones
                                        </a>
                                    </li>
                                @endif
                                @if (in_array('AdminCUPS', session('permisos', [])))
                                    <li id="principalParametrosCUPS">
                                        <a href="{{ url('/Administracion/CUPS') }}">
                                            <i class="icon-Commit"><span class="path1"></span><span
                                                class="path2"></span></i> CUPS
                                        </a>
                                    </li>
                                @endif
                                @if (in_array('AdminCIE10', session('permisos', [])))
                                    <li id="principalParametrosCIE10">
                                        <a href="{{ url('/Administracion/CIE10') }}">
                                            <i class="icon-Commit"><span class="path1"></span><span
                                                class="path2"></span></i> CIE10
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endif



                    {{-- Gestión de Usuarios --}}
                    @if (in_array('gestionUsuarios', session('permisos', [])) || in_array('gestionPerfiles', session('permisos', [])))
                        <li id="principalUsuarios" class="treeview">
                            <a href="#">
                                <i data-feather="users"></i>
                                <span>Gestionar usuarios</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-right pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                @if (in_array('gestionUsuarios', session('permisos', [])))
                                    <li id="usuarios">
                                        <a href="{{ url('/Administracion/Usuarios') }}">
                                            <i class="icon-Commit"><span class="path1"></span><span
                                                class="path2"></span></i> Usuarios
                                        </a>
                                    </li>
                                @endif
                                @if (in_array('gestionPerfiles', session('permisos', [])))
                                    <li id="perfiles">
                                        <a href="{{ url('/Administracion/Perfiles') }}">
                                            <i class="icon-Commit"><span class="path1"></span><span
                                                class="path2"></span></i> Perfiles
                                        </a>
                                    </li>
                                @endif
                                @if (in_array('gestionLog', session('permisos', [])))
                                    <li id="logs">
                                        <a href="{{ url('/Administracion/Logs') }}">
                                            <i class="icon-Commit"><span class="path1"></span><span
                                                class="path2"></span></i> Historial de acciones
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </section>
</aside>
