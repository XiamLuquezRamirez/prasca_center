@extends('Plantilla.Principal')
@section('title', 'Panel de Cumpleaños')
@section('Contenido')

<div class="content-header">
    <div class="d-flex align-items-center">
        <div class="me-auto">
            <h4 class="page-title">🎉 Panel de Cumpleaños</h4>
            <div class="d-inline-block align-items-center">
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#"><i class="mdi mdi-home-outline"></i></a></li>
                        <li class="breadcrumb-item" aria-current="page">Cumpleaños</li>
                        <li class="breadcrumb-item active" aria-current="page">Panel</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<section class="content">
    <div class="row">
        <!-- Pacientes con cumpleaños hoy -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-birthday-cake text-warning"></i>
                        Pacientes con Cumpleaños Hoy ({{ $totalHoy }})
                    </h3>
                </div>
                <div class="card-body">
                    @if($pacientesHoy->count() > 0)
                        <div class="row">
                            @foreach($pacientesHoy as $paciente)
                                <div class="col-md-4 col-lg-3 mb-3">
                                    <div class="card border-warning">
                                        <div class="card-body text-center">
                                            <div class="position-relative">
                                                @if($paciente->foto)
                                                    <img src="{{ asset('app-assets/images/FotosPacientes/' . $paciente->foto) }}" 
                                                         class="rounded-circle mb-3" 
                                                         style="width: 80px; height: 80px; object-fit: cover;" 
                                                         alt="Foto del paciente">
                                                @else
                                                    <div class="rounded-circle bg-primary d-inline-flex align-items-center justify-content-center mb-3" 
                                                         style="width: 80px; height: 80px;">
                                                        <i class="fas fa-user text-white" style="font-size: 2rem;"></i>
                                                    </div>
                                                @endif
                                                <div class="position-absolute top-0 end-0">
                                                    <span class="badge bg-warning text-dark">
                                                        <i class="fas fa-birthday-cake"></i> ¡Hoy!
                                                    </span>
                                                </div>
                                            </div>
                                            <h6 class="card-title">
                                                {{ $paciente->primer_nombre }} {{ $paciente->segundo_nombre }}
                                                <br>
                                                {{ $paciente->primer_apellido }} {{ $paciente->segundo_apellido }}
                                            </h6>
                                            <p class="card-text text-muted">
                                                <i class="fas fa-calendar-alt"></i> 
                                                {{ \Carbon\Carbon::parse($paciente->fecha_nacimiento)->format('d/m/Y') }}
                                                <br>
                                                <i class="fas fa-birthday-cake"></i> 
                                                {{ $paciente->edad }} años
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-birthday-cake text-muted" style="font-size: 4rem;"></i>
                            <h5 class="text-muted mt-3">No hay pacientes con cumpleaños hoy</h5>
                            <p class="text-muted">¡Pero siempre es un buen día para celebrar la salud!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Pacientes con cumpleaños próximos -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-week text-info"></i>
                        Próximos Cumpleaños (Próximos 7 días)
                    </h3>
                </div>
                <div class="card-body">
                    @if($pacientesProximos->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Foto</th>
                                        <th>Nombre Completo</th>
                                        <th>Fecha de Nacimiento</th>
                                        <th>Edad</th>
                                        <th>Días para Cumpleaños</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pacientesProximos as $paciente)
                                        @php
                                            $fechaNacimiento = \Carbon\Carbon::parse($paciente->fecha_nacimiento);
                                            $hoy = \Carbon\Carbon::now();
                                            $proximoCumpleanos = $fechaNacimiento->copy()->year($hoy->year);
                                            
                                            if ($proximoCumpleanos->lt($hoy)) {
                                                $proximoCumpleanos->addYear();
                                            }
                                            
                                            $diasRestantes = $hoy->diffInDays($proximoCumpleanos, false);
                                        @endphp
                                        <tr>
                                            <td>
                                                @if($paciente->foto)
                                                    <img src="{{ asset('app-assets/images/FotosPacientes/' . $paciente->foto) }}" 
                                                         class="rounded-circle" 
                                                         style="width: 40px; height: 40px; object-fit: cover;" 
                                                         alt="Foto del paciente">
                                                @else
                                                    <div class="rounded-circle bg-secondary d-inline-flex align-items-center justify-content-center" 
                                                         style="width: 40px; height: 40px;">
                                                        <i class="fas fa-user text-white"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <strong>{{ $paciente->primer_nombre }} {{ $paciente->segundo_nombre }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $paciente->primer_apellido }} {{ $paciente->segundo_apellido }}</small>
                                            </td>
                                            <td>{{ $fechaNacimiento->format('d/m/Y') }}</td>
                                            <td>{{ $paciente->edad }} años</td>
                                            <td>
                                                @if($diasRestantes == 0)
                                                    <span class="badge bg-warning text-dark">
                                                        <i class="fas fa-birthday-cake"></i> ¡Hoy!
                                                    </span>
                                                @elseif($diasRestantes == 1)
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-clock"></i> Mañana
                                                    </span>
                                                @elseif($diasRestantes <= 3)
                                                    <span class="badge bg-warning">
                                                        <i class="fas fa-clock"></i> En {{ $diasRestantes }} días
                                                    </span>
                                                @else
                                                    <span class="badge bg-info">
                                                        <i class="fas fa-clock"></i> En {{ $diasRestantes }} días
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-week text-muted" style="font-size: 4rem;"></i>
                            <h5 class="text-muted mt-3">No hay cumpleaños próximos</h5>
                            <p class="text-muted">En los próximos 7 días no hay pacientes que cumplan años</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Auto-refresh cada 5 minutos
    setInterval(function() {
        location.reload();
    }, 300000); // 5 minutos
});
</script>
@endsection
