<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informe Historia Clínica</title>
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            background-color: transparent;
            margin: 0px;
            font-size: 10px;
        }

        body::before {
            content: "";
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 400px;
            height: 400px;
            background-image: url('app-assets/images/logo/logo_prasca.png');
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            opacity: 0.2;
            z-index: -1;
            pointer-events: none;
        }

        .container {
            width: 100%;
            background-color: transparent;
        }

        @page {
            margin: 10px;
        }

        .page-break {
            page-break-before: auto;
        }

        td,
        tr {
            border: 1px solid black;
            padding: 3px;
            text-transform: capitalize;
        }

        .flex_div {
            width: 100%;
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: transparent;
        }

        .encabezado {
            background-color: rgba(191, 191, 191, 0.15);
            font-weight: bold;
            width: 97.3%;
            padding: 10px;
            text-align: left;
        }

        .seccion {
            border-left: 0.5px solid grey;
            border-right: 0.5px solid grey;
            border-top: 0.5px solid grey;
            padding: 3px;
            border-radius: 5px 5px 0px 0px;
            background-color: transparent;
            margin-bottom: 2px;
        }

        p {
            margin: 2px 0 !important;
            line-height: 1.2;
        }

        h3 {
            margin: 2px 0;
            font-size: 11px;
        }

        .antecedentes-section {
            margin-bottom: 5px;
        }

        .antecedentes-title {
            margin-bottom: 5px;
        }

        br {
            display: none;
        }

        .section-separator {
            margin: 5px 0;
        }

        @media print {
            .seccion {
                page-break-inside: avoid;
            }

            .page-break {
                page-break-before: auto;
            }

            h3,
            table,
            .flex_div {
                page-break-after: avoid;
            }

            .seccion-grupo {
                page-break-inside: avoid;
            }

            table {
                page-break-inside: auto;
            }

            tr {
                page-break-inside: avoid;
            }
        }

        .seccion+.seccion {
            margin-top: 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        <table>
            <tr>
                <td rowspan="2" style="width: 20%">
                    <div class="flex_div">
                        <img width="150" src="app-assets/images/logo/logo_prasca.png" alt="">
                    </div>
                </td>
                <td colspan="2">
                    <div class="flex_div">
                        <strong>IMPRESIÓN DE HISTORIA CLÍNICA PSICOLOGÍA</strong>
                    </div>
                </td>
            </tr>
            <tr>
                @php
                $numero_historia = '';
                if ($historia->id < 10) {
                    $numero_historia='0000' . $historia->id;
                    } elseif ($historia->id < 100) {
                        $numero_historia='000' . $historia->id;
                        } elseif ($historia->id < 1000) {
                            $numero_historia='00' . $historia->id;
                            } elseif ($historia->id < 10000) {
                                $numero_historia='0' . $historia->id;
                                } else {
                                $numero_historia = $historia->id;
                                }
                                @endphp
                                <td>Historia # {{ $numero_historia }}</td>
                                <td>Fecha de impresión: {{ date('d-m-Y H:i:s') }}</td>
            </tr>
        </table>
        <div style="margin: 5px 0;"></div>
        <table>
            <tr>
                <td colspan="8">
                    <div class="flex_div">
                        <strong>Datos Personales</strong>
                    </div>
                </td>
            </tr>
            <tr>
                <td><strong>Nombre: </strong></td>
                <td>{{ isset($paciente->primer_nombre) ? $paciente->primer_nombre : '' }} {{ isset($paciente->segundo_nombre) ? $paciente->segundo_nombre : '' }} {{ isset($paciente->primer_apellido) ? $paciente->primer_apellido : '' }}
                    {{ isset($paciente->segundo_apellido) ? $paciente->segundo_apellido : '' }}
                </td>
                <td><strong>Sexo biológ.:</strong></td>
                <td>{{ isset($paciente->sexo) ? $paciente->sexo : '' }}</td>
                <td><strong>F. Nacimiento: </strong></td>
                <td>{{ isset($paciente->fecha_nacimiento) ? $paciente->fecha_nacimiento : '' }}</td>
                <td><strong>Edad</strong></td>
                <td>
                    @php
                    $fechaNacimiento = isset($paciente->fecha_nacimiento) ? $paciente->fecha_nacimiento : null;
                    if ($fechaNacimiento) {
                    // Parsear la fecha de nacimiento
                    $fechaNacimiento = \Carbon\Carbon::parse($fechaNacimiento);

                    // Obtener la fecha actual
                    $fechaActual = \Carbon\Carbon::now();

                    // Calcular la diferencia
                    $diferencia = $fechaActual->diff($fechaNacimiento);

                    // Crear la cadena de edad
                    $edadTexto = "{$diferencia->y} años, {$diferencia->m} meses, y {$diferencia->d} días";
                    } else {
                    $edadTexto = 'Fecha de nacimiento no válida.';
                    }

                    @endphp

                    {{ $edadTexto }}
                </td>
            </tr>
            <tr>
                <td><strong>Identificación: </strong></td>
                <td>{{ isset($paciente->tipo_identificacion) ? $paciente->tipo_identificacion : '' }} - {{ isset($paciente->identificacion) ? $paciente->identificacion : '' }}</td>
                <td><strong>lateralidad: </strong></td>
                <td>{{ isset($paciente->lateralidad) ? $paciente->lateralidad : '' }}</td>
                <td><strong>Estado civil:</strong></td>
                <td>{{ isset($paciente->estado_civil) ? $paciente->estado_civil : '' }}</td>
                <td><strong>Ocupación: </strong></td>
                <td>{{ isset($paciente->ocupacion) ? $paciente->ocupacion : '' }}</td>
            </tr>
            <tr>
                <td><strong>Correo electrónico: </strong></td>
                <td colspan="3">{{ isset($paciente->email) ? $paciente->email : '' }} - {{ isset($paciente->identificacion) ? $paciente->identificacion : '' }}</td>
                <td><strong>Dirección:</strong></td>
                <td colspan="3">{{ isset($paciente->direccion) ? $paciente->direccion : '' }}</td>
            </tr>
            <tr>
                <td><strong>Departamento: </strong></td>
                <td colspan="2">{{ isset($paciente->departamento_info->nombre) ? $paciente->departamento_info->nombre : '' }}</td>
                <td><strong>Municipio: </strong></td>
                <td colspan="2">{{ isset($paciente->municipio_info->nombre) ? $paciente->municipio_info->nombre : '' }}</td>
                <td><strong>Zona: </strong></td>
                <td>{{ $paciente->zona_residencial == '01' ? 'Rural' : 'Urbana' }}</td>
            </tr>

            <tr>
                <td colspan="1"><strong>Empresa: </strong></td>
                @if ($paciente->eps_info == "Sin EPS")
                <td colspan="7">Particular</td>
                @else
                <td colspan="7">{{ isset($paciente->eps_info->codigo) ? $paciente->eps_info->codigo : '' }} - ({{ isset($paciente->eps_info->entidad) ? $paciente->eps_info->entidad : '' }})</td>
                @endif
            </tr>

            @if (isset($paciente->acompanante) && $paciente->acompanante != '')
            <tr>
                <td colspan="2"><strong>En caso de emergencia llamar a: </strong></td>
                <td colspan="6">{{ isset($paciente->acompanante) ? $paciente->acompanante : '' }} - Parentesco ({{ isset($paciente->parentesco) ? $paciente->parentesco : '' }}) -
                    Teléfono ({{ isset($paciente->telefono_acompanate) ? $paciente->telefono_acompanate : '' }})</td>
            </tr>
            @endif
        </table>
        <div style="margin: 5px 0;"></div>
        <div>
            <div class="encabezado" style="margin-bottom: 5px;">
                <strong>Apertura psicología del {{ $historia->fecha_historia }}: {{ $edadTexto }} </strong>
            </div>
            <div class="antecedentes-section">
                <div class="seccion" style="background-color:rgba(243, 243, 243, 0.5);">
                    <h3 class="antecedentes-title"><strong>DATOS GENERALES</strong></h3>
                    <div class="seccion">
                        <p style="font-weight: bold;">Remisión</p>
                        <p>{!! $historia->remision !!}</p>
                    </div>
                    <div class="seccion">
                        @if (isset($historia->dx_principal_detalle) && $historia->dx_principal_detalle)
                        <p><strong>DX principal</strong></p>
                        <p>{{ $historia->dx_principal_detalle->codigo }} - {{ $historia->dx_principal_detalle->nombre }}</p>
                        @endif

                        @if (isset($historia->dx_principal1_detalle) && $historia->dx_principal1_detalle)
                        <p><strong>DX Relacionado 1</strong></p>
                        <p>{{ $historia->dx_principal1_detalle->codigo }} - {{ $historia->dx_principal1_detalle->nombre }}</p>
                        @endif
                        @if (isset($historia->dx_principal2_detalle) && $historia->dx_principal2_detalle)
                        <p><strong>DX Relacionado 2</strong></p>
                        <p>{{ $historia->dx_principal2_detalle->codigo }} - {{ $historia->dx_principal2_detalle->nombre }}</p>
                        @endif
                    </div>
                    <div class="seccion">
                        <p><strong>Código de consulta</strong></p>
                        @if (isset($historia->codigo_consulta_detalle) && $historia->codigo_consulta_detalle)
                        <p>{{ $historia->codigo_consulta_detalle->codigo }} - {{ $historia->codigo_consulta_detalle->nombre }}</p>
                        @else
                        <p>No se ha seleccionado un código de consulta</p>
                        @endif
                    </div>
                    <div class="seccion">
                        <p><strong>Motivo de Consulta</strong></p>
                        {!! isset($historia->motivo_consulta) ? $historia->motivo_consulta : '' !!}
                    </div>
                    <div class="seccion">
                        <p><strong>Enfermedad Actual</strong></p>
                        <p>{!! isset($historia->enfermedad_actual) ? $historia->enfermedad_actual : '' !!}</p>
                    </div>
                </div>
            </div>
            <div style="margin: 3px 0;"></div>
            <div class="antecedentes-section">
                <div class="seccion" style="background-color:rgba(243, 243, 243, 0.5);">
                    <h3 class="antecedentes-title"><strong>ANTECEDENTES</strong></h3>
                    <h3 class="antecedentes-title"><strong>Médicos Personales</strong></h3>
                    <table style="border: none; width: 100%; table-layout: fixed; margin-bottom: 3px;">
                        <tr style="border: none;">
                            <td style="border: none; width: 50%; vertical-align: top; padding-right: 5px;">
                                @foreach ($antecedentesPersonales as $index => $item)
                                @if ($index % 2 == 0)
                                <div class="seccion">
                                    <p><strong>{{ $item->nombre }}</strong></p>
                                    <p>{!! $item->detalle !!}</p>
                                </div>
                                @endif
                                @endforeach
                            </td>
                            <td style="border: none; width: 50%; vertical-align: top; padding-left: 5px;">
                                @foreach ($antecedentesPersonales as $index => $item)
                                @if ($index % 2 != 0)
                                <div class="seccion">
                                    <p><strong>{{ $item->nombre }}</strong></p>
                                    <p>{!! $item->detalle !!}</p>
                                </div>
                                @endif
                                @endforeach
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div style="margin: 3px 0;"></div>
            @if ($historia->tipologia == 'Pediatría')
            <div class="antecedentes-section">
                <div class="seccion" style="background-color:rgba(243, 243, 243, 0.5);">
                    <h3 class="antecedentes-title"><strong>ANTECEDENTES</strong></h3>
                    <h3 class="antecedentes-title"><strong>Prenatales</strong></h3>
                    <table style="border: none; width: 100%; table-layout: fixed; margin-bottom: 3px;">
                        <tr style="border: none;">
                            <td style="border: none; width: 50%; vertical-align: top; padding-right: 5px;">
                                @foreach ($antecedentesPrenatales as $index => $item)
                                @if ($index % 2 == 0)
                                <div class="seccion">
                                    <p><strong>{{ $item->nombre }}</strong></p>
                                    <p>{!! $item->detalle !!}</p>
                                </div>
                                @endif
                                @endforeach
                            </td>
                            <td style="border: none; width: 50%; vertical-align: top; padding-left: 5px;">
                                @foreach ($antecedentesPrenatales as $index => $item)
                                @if ($index % 2 != 0)
                                <div class="seccion">
                                    <p><strong>{{ $item->nombre }}</strong></p>
                                    <p>{!! $item->detalle !!}</p>
                                </div>
                                @endif
                                @endforeach
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div style="margin: 3px 0;"></div>
            <div class="antecedentes-section">
                <div class="seccion" style="background-color:rgba(243, 243, 243, 0.5);">
                    <h3 class="antecedentes-title"><strong>ANTECEDENTES</strong></h3>
                    <h3 class="antecedentes-title"><strong>Natales</strong></h3>
                    <table style="border: none; width: 100%; table-layout: fixed; margin-bottom: 3px;">
                        <tr style="border: none;">
                            <td style="border: none; width: 50%; vertical-align: top; padding-right: 5px;">
                                @foreach ($antecedentesNatales as $index => $item)
                                @if ($index % 2 == 0)
                                <div class="seccion">
                                    <p><strong>{{ $item->nombre }}</strong></p>
                                    <p>{!! $item->detalle !!}</p>
                                </div>
                                @endif
                                @endforeach
                            </td>
                            <td style="border: none; width: 50%; vertical-align: top; padding-left: 5px;">
                                @foreach ($antecedentesNatales as $index => $item)
                                @if ($index % 2 != 0)
                                <div class="seccion">
                                    <p><strong>{{ $item->nombre }}</strong></p>
                                    <p>{!! $item->detalle !!}</p>
                                </div>
                                @endif
                                @endforeach
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div style="margin: 3px 0;"></div>
            <div class="antecedentes-section">
                <div class="seccion" style="background-color:rgba(243, 243, 243, 0.5);">
                    <h3 class="antecedentes-title"><strong>ANTECEDENTES</strong></h3>
                    <h3 class="antecedentes-title"><strong>Posnatales</strong></h3>
                    <table style="border: none; width: 100%; table-layout: fixed; margin-bottom: 3px;">
                        <tr style="border: none;">
                            <td style="border: none; width: 50%; vertical-align: top; padding-right: 5px;">
                                @foreach ($antecedentesPosnatales as $index => $item)
                                @if ($index % 2 == 0)
                                <div class="seccion">
                                    <p><strong>{{ $item->nombre }}</strong></p>
                                    <p>{!! $item->detalle !!}</p>
                                </div>
                                @endif
                                @endforeach
                            </td>
                            <td style="border: none; width: 50%; vertical-align: top; padding-left: 5px;">
                                @foreach ($antecedentesPosnatales as $index => $item)
                                @if ($index % 2 != 0)
                                <div class="seccion">
                                    <p><strong>{{ $item->nombre }}</strong></p>
                                    <p>{!! $item->detalle !!}</p>
                                </div>
                                @endif
                                @endforeach
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div style="margin: 3px 0;"></div>
            <div class="antecedentes-section">
                <div class="seccion" style="background-color:rgba(243, 243, 243, 0.5);">
                    <h3 class="antecedentes-title"><strong>ANTECEDENTES</strong></h3>
                    <h3 class="antecedentes-title"><strong>Desarrollo Psicomotor</strong></h3>
                    <table style="border: none; width: 100%; table-layout: fixed; margin-bottom: 3px;">
                        <tr style="border: none;">
                            <td style="border: none; width: 50%; vertical-align: top; padding-right: 5px;">
                                @foreach ($desarrolloPsicomotor as $index => $item)
                                @if ($index % 2 == 0)
                                <div class="seccion">
                                    <p><strong>{{ $item->nombre }}</strong></p>
                                    <p>{!! $item->detalle !!}</p>
                                </div>
                                @endif
                                @endforeach
                            </td>
                            <td style="border: none; width: 50%; vertical-align: top; padding-left: 5px;">
                                @foreach ($desarrolloPsicomotor as $index => $item)
                                @if ($index % 2 != 0)
                                <div class="seccion">
                                    <p><strong>{{ $item->nombre }}</strong></p>
                                    <p>{!! $item->detalle !!}</p>
                                </div>
                                @endif
                                @endforeach
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            @endif
            <div style="margin: 3px 0;"></div>
            <div class="antecedentes-section">
                <div class="seccion" style="background-color:rgba(243, 243, 243, 0.5);">
                    <h3 class="antecedentes-title"><strong>ANTECEDENTES</strong></h3>
                    <h3 class="antecedentes-title"><strong>Médicos Familiares</strong></h3>
                    <table style="border: none; width: 100%; table-layout: fixed; margin-bottom: 3px;">
                        <tr style="border: none;">
                            <td style="border: none; width: 50%; vertical-align: top; padding-right: 5px;">
                                @foreach ($antecedentesFamiliares as $index => $item)
                                @if ($index % 2 == 0)
                                <div class="seccion">
                                    <p><strong>{{ $item->nombre }}</strong></p>
                                    <p>{!! $item->detalle !!}</p>
                                </div>
                                @endif
                                @endforeach
                            </td>
                            <td style="border: none; width: 50%; vertical-align: top; padding-left: 5px;">
                                @foreach ($antecedentesFamiliares as $index => $item)
                                @if ($index % 2 != 0)
                                <div class="seccion">
                                    <p><strong>{{ $item->nombre }}</strong></p>
                                    <p>{!! $item->detalle !!}</p>
                                </div>
                                @endif
                                @endforeach
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div style="margin: 3px 0;"></div>
            <div class="antecedentes-section">
                <div class="seccion" style="background-color:rgba(243, 243, 243, 0.5);">
                    <h3 class="antecedentes-title"><strong>Áreas de Ajuste y/o Desempeño</strong></h3>
                    @foreach ($areaAjuste as $index => $item)
                    <div class="seccion">
                        <p><strong>{{ $item->nombre }}</strong></p>
                        <p>{!! $item->detalle !!}</p>
                    </div>
                    @endforeach
                </div>
            </div>
            <div style="margin: 3px 0;"></div>
            <div class="antecedentes-section">
            <div class="seccion" style="background-color:rgba(243, 243, 243, 0.5);">
                <h3 class="antecedentes-title"><strong>EVALUACIÓN PSICOLÓGICA</strong></h3>

                <div class="seccion">
                    <p><strong>Evaluación psicológica inicial</strong></p>
                    <p>{!! isset($historia->eval_inicial) ? $historia->eval_inicial : '' !!}</p>
                </div>
            </div>
        </div>

            <div style="margin: 3px 0;"></div>

            <div class="antecedentes-section">
                <div class="seccion" style="background-color:rgba(243, 243, 243, 0.5);">
                    <h3 class="antecedentes-title"><strong>Interconsultas e Intervenciones</strong></h3>
                    @foreach ($interconuslta as $index => $item)
                    <div class="seccion">
                        <p><strong>{{ $item->nombre }}</strong></p>
                        <p>{!! $item->detalle !!}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>


        <div style="margin: 3px 0;"></div>
        <div class="antecedentes-section">
            <div class="seccion" style="background-color:rgba(243, 243, 243, 0.5);">
                <h3 class="antecedentes-title"><strong>EXAMEN MENTAL</strong></h3>

                <div class="seccion">
                    <p><strong>Examen Mental</strong></p>
                    <p>{!! isset($examenMental->examen_mental) ? $examenMental->examen_mental : '' !!}</p>
                </div>

                <div class="seccion">
                    <p><strong>Ciclos del Sueño</strong></p>
                    <p>{!! isset($examenMental->ciclos_del_sueno) ? $examenMental->ciclos_del_sueno : '' !!}</p>
                </div>
                <div class="seccion">
                    <p><strong>Apetito</strong></p>
                    <p>{!! isset($examenMental->apetito) ? $examenMental->apetito : '' !!}</p>
                </div>
                <div class="seccion">
                    <p><strong>Actividades de Autocuidado</strong></p>
                    <p>{!! isset($examenMental->actividades_autocuidado) ? $examenMental->actividades_autocuidado : '' !!}</p>
                </div>
            </div>
        </div>
        <div style="margin: 3px 0;"></div>
        <div class="antecedentes-section">
            <div class="seccion" style="background-color:rgba(243, 243, 243, 0.5); margin-top: 15px;">
                <h3 class="antecedentes-title"><strong>IMPRESIÓN DIAGNOSTICA</strong></h3>
                <div class="seccion">
                    @if (isset($historia->impresion_diagnostica_detalle) && $historia->impresion_diagnostica_detalle)
                    <p><strong>Impresión Diagnóstica (CIE 10 - DSM-V):</strong></p>
                    <p>{{ $historia->impresion_diagnostica_detalle->codigo }} - {{ $historia->impresion_diagnostica_detalle->nombre }}</p>
                    @endif
                    @if (isset($historia->codigo_diagnostico1_detalle) && $historia->codigo_diagnostico1_detalle)
                    <p><strong>Impresión Diagnóstica Relacionada 1</strong></p>
                    <p>{{ $historia->codigo_diagnostico1_detalle->codigo }} - {{ $historia->codigo_diagnostico1_detalle->nombre }}</p>
                    @endif
                    @if (isset($historia->codigo_diagnostico2_detalle) && $historia->codigo_diagnostico2_detalle)
                    <p><strong>Impresión Diagnóstica Relacionada 2</strong></p>
                    <p>{{ $historia->codigo_diagnostico2_detalle->codigo }} - {{ $historia->codigo_diagnostico2_detalle->nombre }}</p>
                    @endif
                </div>
                <div class="seccion">
                    <p><strong>Establecido por primera vez</strong></p>
                    <p>{!! isset($historia->diagnostico_primera_vez) ? $historia->diagnostico_primera_vez : '' !!}</p>
                </div>
                <div class="seccion">
                    <p><strong>Objeto de atención clínica</strong></p>
                    <p>{!! isset($historia->objeto_atencion_clinica) ? $historia->objeto_atencion_clinica : '' !!}</p>
                </div>
            </div>
        </div>
        <div style="margin: 3px 0;"></div>
        <div class="antecedentes-section">
            <div class="seccion" style="background-color:rgba(243, 243, 243, 0.5);">
                <h3 class="antecedentes-title"><strong>PLAN DE INTERVENCIÓN</strong></h3>
                <div class="seccion">
                    <p><strong>Plan de Intervención</strong></p>
                    <p>{!! isset($historia->plan_intervension_detalle) ? $historia->plan_intervension_detalle : '' !!}</p>
                </div>
                <div class="seccion">
                    <p><strong>Objetivo General</strong></p>
                    <p>{!! isset($historia->objetivo_general) ? $historia->objetivo_general : '' !!}</p>
                </div>
                <div class="seccion">
                    <p><strong>Objetivos Específicos</strong></p>
                    <p>{!! isset($historia->objetivos_especificos) ? $historia->objetivos_especificos : '' !!}</p>
                </div>
                <div class="seccion">
                    <p><strong>Sugerencias e Interconsultas</strong></p>
                    <p>{!! isset($historia->sugerencias_interconsultas) ? $historia->sugerencias_interconsultas : '' !!}</p>
                </div>
                <div class="seccion">
                    <p><strong>Observaciones y Recomendaciones</strong></p>
                    <p>{!! isset($historia->observaciones_recomendaciones) ? $historia->observaciones_recomendaciones : '' !!}</p>
                </div>
            </div>
        </div>
    </div>
    <div style="margin: 5px 0;"></div>
    <div style="width: 100%; border-top: 1px solid grey;">
        <br>
        @if ($historia->profesional_detalle->firma != "sinFima.jpg")
        <img width="180"
            src="app-assets/images/firmasProfesionales/{{ $historia->profesional_detalle->firma }}"
            alt="">
        @endif

        <h2>{{ $historia->profesional_detalle->nombre }}</h2>
        <h3>Psicóloga clínica</h3>
        <h3><strong>Tarjeta Profesional: </strong>{{ $historia->profesional_detalle->registro }}</h3>
        <h3><strong>Mag. TP3G - VIU</strong></h3>
    </div>

    <!-- ORDENES MEDICAS NUEVA HOJA -->

    @if ($ordenMedica != null && $ordenMedica->count() > 0)
    <div style="page-break-before: always;"></div>

    <!-- NUEVA HOJA CON CABECERA -->
    <table>
        <tr>
            <td rowspan="2" style="width: 20%">
                <div class="flex_div">
                    <img width="150" src="app-assets/images/logo/logo_prasca.png" alt="">
                </div>
            </td>
            <td colspan="2">
                <div class="flex_div">
                    <strong>ÓRDENES MÉDICAS - HISTORIA CLÍNICA PSICOLOGÍA</strong>
                </div>
            </td>
        </tr>
        <tr>
            <td>Historia # {{ $numero_historia }}</td>
            <td>Fecha de impresión: {{ date('d-m-Y H:i:s') }}</td>
        </tr>
    </table>
    <div style="margin: 5px 0;"></div>
    <table>
        <tr>
            <td colspan="8">
                <div class="flex_div">
                    <strong>Datos Personales</strong>
                </div>
            </td>
        </tr>
        <tr>
            <td><strong>Nombre: </strong></td>
            <td>{{ isset($paciente->primer_nombre) ? $paciente->primer_nombre : '' }} {{ isset($paciente->segundo_nombre) ? $paciente->segundo_nombre : '' }} {{ isset($paciente->primer_apellido) ? $paciente->primer_apellido : '' }}
                {{ isset($paciente->segundo_apellido) ? $paciente->segundo_apellido : '' }}
            </td>
            <td><strong>Sexo biológ.:</strong></td>
            <td>{{ isset($paciente->sexo) ? $paciente->sexo : '' }}</td>
            <td><strong>F. Nacimiento: </strong></td>
            <td>{{ isset($paciente->fecha_nacimiento) ? $paciente->fecha_nacimiento : '' }}</td>
            <td><strong>Edad</strong></td>
            <td>{{ $edadTexto }}</td>
        </tr>
        <tr>
            <td><strong>Identificación: </strong></td>
            <td>{{ isset($paciente->tipo_identificacion) ? $paciente->tipo_identificacion : '' }} - {{ isset($paciente->identificacion) ? $paciente->identificacion : '' }}</td>
            <td><strong>lateralidad: </strong></td>
            <td>{{ isset($paciente->lateralidad) ? $paciente->lateralidad : '' }}</td>
            <td><strong>Estado civil:</strong></td>
            <td>{{ isset($paciente->estado_civil) ? $paciente->estado_civil : '' }}</td>
            <td><strong>Ocupación: </strong></td>
            <td>{{ isset($paciente->ocupacion) ? $paciente->ocupacion : '' }}</td>
        </tr>
        <tr>
            <td><strong>Correo electrónico: </strong></td>
            <td colspan="3">{{ isset($paciente->email) ? $paciente->email : '' }} - {{ isset($paciente->identificacion) ? $paciente->identificacion : '' }}</td>
            <td><strong>Dirección:</strong></td>
            <td colspan="3">{{ isset($paciente->direccion) ? $paciente->direccion : '' }}</td>
        </tr>
        <tr>
            <td><strong>Departamento: </strong></td>
            <td colspan="2">{{ isset($paciente->departamento_info->nombre) ? $paciente->departamento_info->nombre : '' }}</td>
            <td><strong>Municipio: </strong></td>
            <td colspan="2">{{ isset($paciente->municipio_info->nombre) ? $paciente->municipio_info->nombre : '' }}</td>
            <td><strong>Zona: </strong></td>
            <td>{{ $paciente->zona_residencial == '01' ? 'Rural' : 'Urbana' }}</td>
        </tr>
        <tr>
            <td colspan="1"><strong>Empresa: </strong></td>
            @if ($paciente->eps_info == "Sin EPS")
            <td colspan="7">Particular</td>
            @else
            <td colspan="7">{{ isset($paciente->eps_info->codigo) ? $paciente->eps_info->codigo : '' }} - ({{ isset($paciente->eps_info->entidad) ? $paciente->eps_info->entidad : '' }})</td>
            @endif
        </tr>
        @if (isset($paciente->acompanante) && $paciente->acompanante != '')
        <tr>
            <td colspan="2"><strong>En caso de emergencia llamar a: </strong></td>
            <td colspan="6">{{ isset($paciente->acompanante) ? $paciente->acompanante : '' }} - Parentesco ({{ isset($paciente->parentesco) ? $paciente->parentesco : '' }}) -
                Teléfono ({{ isset($paciente->telefono_acompanate) ? $paciente->telefono_acompanate : '' }})</td>
        </tr>
        @endif
    </table>
    <div style="margin: 5px 0;"></div>
    <!-- AGREGAR DIAGNOSTICO DE LA HISTORIA -->
    <div class="seccion" style="background-color:rgba(243, 243, 243, 0.5);">
        @if (isset($historia->impresion_diagnostica_detalle) && $historia->impresion_diagnostica_detalle)
        <p><strong>Impresión Diagnóstica:</strong></p>
        <p>{{ $historia->impresion_diagnostica_detalle->codigo }} - {{ $historia->impresion_diagnostica_detalle->nombre }}</p>
        @endif
        @if (isset($historia->codigo_diagnostico1_detalle) && $historia->codigo_diagnostico1_detalle)
        <p><strong>Impresión Diagnóstica Relacionada 1</strong></p>
        <p>{{ $historia->codigo_diagnostico1_detalle->codigo }} - {{ $historia->codigo_diagnostico1_detalle->nombre }}</p>
        @endif
        @if (isset($historia->codigo_diagnostico2_detalle) && $historia->codigo_diagnostico2_detalle)
        <p><strong>Impresión Diagnóstica Relacionada 2</strong></p>
        <p>{{ $historia->codigo_diagnostico2_detalle->codigo }} - {{ $historia->codigo_diagnostico2_detalle->nombre }}</p>
        @endif
    </div>

    <!-- SECCIÓN DE ÓRDENES MÉDICAS -->
    <div class="antecedentes-section">
        <div class="seccion" style="background-color:rgb(243, 243, 243);">
            <h3 class="antecedentes-title"><strong>ÓRDENES MÉDICAS</strong></h3>
            <!-- Aquí puedes agregar el contenido de las órdenes médicas -->
            <div class="seccion">
                @if (!empty($ordenMedica))
                <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                    <thead>
                        <tr style="background-color:rgba(234, 235, 244, 0.47);">
                            <th style="border: 1px solid #000; padding: 8px; text-align: center; width: 10%;">No.</th>
                            <th style="border: 1px solid #000; padding: 8px; text-align: center; width: 75%;">PROCEDIMIENTO</th>
                            <th style="border: 1px solid #000; padding: 8px; text-align: center; width: 15%;">CANTIDAD</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $contador = 1; @endphp
                        @foreach ($ordenMedica as $orden)
                        <tr>
                            <td style="border: 1px solid #000; padding: 8px; text-align: center;">{{ $contador }}</td>
                            <td style="border: 1px solid #000; padding: 8px;" class="text-procedimiento">
                                <p>{{ $orden->codigo }} - {{ $orden->textoCodigo }}</p>
                                <p><strong>OBSERVACIÓN:</strong> {{ $orden->observacion }}</p>
                            </td>
                            <td style="border: 1px solid #000; padding: 8px; text-align: center;">{{ $orden->cantidad }}</td>
                        </tr>
                        @php $contador++; @endphp
                        @endforeach
                    </tbody>
                </table>
                @else
                <p>No hay órdenes médicas registradas.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- FIRMA EN LA NUEVA HOJA -->
    <div style="margin: 5px 0;"></div>
    <div style="width: 100%; border-top: 1px solid grey;">
        @if ($historia->profesional_detalle->firma != "sinFima.jpg")
        <img width="180"
            src="app-assets/images/firmasProfesionales/{{ $historia->profesional_detalle->firma }}"
            alt="">
        @endif

        <h2>{{ $historia->profesional_detalle->nombre }}</h2>
        <h3>Psicóloga clínica</h3>
        <h3><strong>Tarjeta Profesional: </strong>{{ $historia->profesional_detalle->registro }}</h3>
        <h3><strong>Mag. TP3G - VIU</strong></h3>
    </div>
    @endif
    </div>

</body>

</html>