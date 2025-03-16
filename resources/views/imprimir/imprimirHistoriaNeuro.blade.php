<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informe Historia Clínica</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: white;
            margin: 0px;
            font-size: 10px;
        }

        .container {
            width: 100%;
        }

        @page { margin: 10px; }
        
        .page-break {
            page-break-before: auto;
        }
        

        td, tr {
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
            margin: 2px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .encabezado {
            background-color: #bfbfbf;
            font-weight: bold;
            width: 97.3%;
            padding: 5px;
            text-align: left;
            margin-bottom: 5px;
        }

        .seccion {
            border-left: 0.5px solid grey;
            border-right: 0.5px solid grey;
            border-top: 0.5px solid grey;
            padding: 3px;
            border-radius: 5px 5px 0px 0px;
            background-color: white;
            margin-bottom: 3px;
        }

        p {
            margin: 2px 0 !important;
            line-height: 1.2;
        }

        h3 {
            margin: 2px 0;
            font-size: 11px;
        }

        br {
            display: none;
        }

        .section-separator {
            margin: 3px 0;
        }

        @media print {
            .seccion {
                page-break-inside: avoid;
            }

            table {
                page-break-inside: auto;
            }

            tr {
                page-break-inside: avoid;
            }

            h3 {
                page-break-after: avoid;
            }

            .seccion-grupo {
                page-break-inside: avoid;
            }
        }

        .seccion + .seccion {
            margin-top: 3px;
        }

        table {
            margin-bottom: 3px;
        }

        .antecedentes-section {
            margin-bottom: 5px;
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
                        <strong>IMPRESIÓN DE HISTORIA CLÍNICA NEUROPSICOLOGÍA</strong>
                    </div>
                </td>
            </tr>
            <tr>
                @php
                    $numero_historia = "";

                    if($historia->id < 10){
                        $numero_historia = '0000'.$historia->id;
                    }else{
                        if ($historia->id < 100){
                            $numero_historia = '000'.$historia->id;
                        }else{
                            if($historia->id < 1000){
                                $numero_historia = '00'.$historia->id;
                            }else{
                                if($historia->id < 10000){
                                    $numero_historia = '0'.$historia->id;
                                }else{
                                    $numero_historia = $historia->id;
                                }
                            }
                        }
                    }
                @endphp
                <td>Historia # {{ $numero_historia }}</td>
                <td>Fecha de impresión: {{ date('d-m-Y H:i:s') }}</td>
            </tr>
        </table>
        <br>
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
                <td>{{$paciente->primer_nombre}} {{$paciente->segundo_nombre}} {{$paciente->primer_apellido}} {{$paciente->segundo_apellido}}</td>
                <td><strong>Sexo biológ.:</strong></td>
                <td>{{$paciente->sexo}}</td>
                <td><strong>F. Nacimiento: </strong></td>
                <td>{{$paciente->fecha_nacimiento}}</td>
                <td><strong>Edad</strong></td>
                <td>
                    @php
                        $fechaNacimiento = $paciente->fecha_nacimiento;
                        $edad = \Carbon\Carbon::parse($fechaNacimiento)->age;
                    @endphp

                    {{ $edad }} Años
                </td>
            </tr>
            <tr>
                <td><strong>Identificación: </strong></td>
                <td>{{$paciente->tipo_identificacion}} - {{$paciente->identificacion}}</td>
                <td><strong>lateralidad: </strong></td>
                <td>{{$paciente->lateralidad}}</td>
                <td><strong>Estado civil:</strong></td>
                <td>{{$paciente->estado_civil}}</td>
                <td><strong>Ocupación: </strong></td>
                <td>{{$paciente->ocupacion}}</td>
            </tr>
            <tr>
                <td><strong>Correo electrónico: </strong></td>
                <td colspan="3">{{$paciente->email}} - {{$paciente->identificacion}}</td>
                <td><strong>Dirección:</strong></td>
                <td colspan="3">{{$paciente->direccion}}</td>
            </tr>
            <tr>
                <td><strong>Departamento: </strong></td>
                <td colspan="2">{{$paciente->departamento_info->nombre}}</td>
                <td><strong>Municipio: </strong></td>
                <td colspan="2">{{$paciente->municipio_info->nombre}}</td>
                <td><strong>Zona: </strong></td>
                <td>{{$paciente->zona_residencial == '01' ? 'Rural' : 'Urbana'}}</td>
            </tr>
            <tr>
                <td colspan="1"><strong>Empresa: </strong></td>
                @if ($paciente->eps_info == "Sin EPS")
                <td colspan="7">Particular</td>
                @else
                <td colspan="7">{{ $paciente->eps_info->codigo }} - ({{ $paciente->eps_info->entidad }})</td>
                @endif
            </tr>

            @if ($paciente->acompanante != "")
                <tr>
                    <td colspan="2"><strong>En caso de emergencia llamar a: </strong></td>
                    <td colspan="6">{{$paciente->acompanante}} - Parentesco ({{$paciente->parentesco}})</td>
                </tr>
            @endif
        </table>
        <br>
        <div>
            <div class="encabezado">
                <strong>Apertura Neuropsicología del {{ $historia->fecha_historia }}: {{ $edad }} Años</strong>
            </div>
            <br>
            <div class="seccion" style="background-color:rgb(243, 243, 243);">
                <h3><strong>DATOS GENERALES</strong></h3>
                <div class="seccion">
                    <p style="font-weight: bold;">Remisión</p>
                    <p>{!! $historia->remision !!}</p>
                </div>
                <div class="seccion">
                    <p><strong>DX principal</strong></p>
                    <p>{{ $historia->dx_principal_detalle->nombre }}</p>
                    @if ($historia->otro_dx_principal != null)
                        <p><strong>Otro tipo de diagnóstico</strong></p>
                        <p>{{ $historia->otro_dx_principal }}</p>
                    @endif
                </div>
                <div class="seccion">
                    <p><strong>Código de consulta</strong></p>
                    <p>{{ $historia->codigo_consulta_detalle->nombre }}</p>
                </div>
                <div class="seccion">
                    <p><strong>Motivo de consulta</strong></p>
                    <p>{{ $historia->motivo_consulta_texto }}</p>
                </div>
                <div class="seccion">
                    <p><strong>Motivo de consulta relacionado</strong></p>
                    <p>{{ $historia->motivo_consulta_detalle->opcion }}</p>
                </div>
                @if ($historia->otro_motivo_consulta != null)
                    <div class="seccion">
                        <p><strong>Otro motivo de consulta relacionado</strong></p>
                        <p>{{ $historia->otro_motivo_consulta }}</p>
                    </div>
                @endif
                <div class="seccion">
                    <p><strong>Enfermedad actual</strong></p>
                    <p>{!! $historia->enfermedad_actual !!}</p>
                </div>
            </div>
            <br>
            <div class="seccion" style="background-color:rgb(243, 243, 243);">
                <h3><strong>ANTECEDENTES</strong></h3>
                <h3><strong>Médicos Personales</strong></h3>
                <table style="border: none; width: 100%; table-layout: fixed;">
                    <tr style="border: none; ">
                        <td style="border: none; width: 50%; vertical-align: top;">
                            @foreach($antecedentesPersonales as $index => $item)
                                @if($index % 2 == 0)
                                    <div class="seccion">
                                        <p><strong>{{$item->nombre}}</strong></p>
                                        <p>{!! $item->detalle !!}</p>
                                    </div>
                                @endif
                            @endforeach
                        </td>
                        <td style="border: none; width: 50%; vertical-align: top;">
                            @foreach($antecedentesPersonales as $index => $item)
                                @if($index % 2 != 0)
                                    <div class="seccion">
                                        <p><strong>{{$item->nombre}}</strong></p>
                                        <p>{!! $item->detalle !!}</p>
                                    </div>
                                @endif
                            @endforeach
                        </td>
                    </tr>
                </table>
            </div>
            <div class="page-break"></div>
            @if($historia->tipologia == 'Pediatría')
                <div class="seccion" style="background-color:rgb(243, 243, 243);">
                    <h3><strong>ANTECEDENTES</strong></h3>
                    <h3><strong>Prenatales</strong></h3>
                    <table style="border: none; width: 100%; table-layout: fixed;">
                        <tr style="border: none; ">
                            <td style="border: none; width: 50%; vertical-align: top;">
                                @foreach($antecedentesPrenatales as $index => $item)
                                    @if($index % 2 == 0)
                                        <div class="seccion">
                                            <p><strong>{{$item->nombre}}</strong></p>
                                            <p>{!! $item->detalle !!}</p>
                                        </div>
                                    @endif
                                @endforeach
                            </td>
                            <td style="border: none; width: 50%; vertical-align: top;">
                                @foreach($antecedentesPrenatales as $index => $item)
                                    @if($index % 2 != 0)
                                        <div class="seccion">
                                            <p><strong>{{$item->nombre}}</strong></p>
                                            <p>{!! $item->detalle !!}</p>
                                        </div>
                                    @endif
                                @endforeach
                            </td>
                        </tr>
                    </table>
                </div>
                <br><br>
                <div class="seccion" style="background-color:rgb(243, 243, 243);">
                    <h3><strong>ANTECEDENTES</strong></h3>
                    <h3><strong>Natales</strong></h3>
                    <table style="border: none; width: 100%; table-layout: fixed;">
                        <tr style="border: none; ">
                            <td style="border: none; width: 50%; vertical-align: top;">
                                @foreach($antecedentesNatales as $index => $item)
                                    @if($index % 2 == 0)
                                        <div class="seccion">
                                            <p><strong>{{$item->nombre}}</strong></p>
                                            <p>{!! $item->detalle !!}</p>
                                        </div>
                                    @endif
                                @endforeach
                            </td>
                            <td style="border: none; width: 50%; vertical-align: top;">
                                @foreach($antecedentesNatales as $index => $item)
                                    @if($index % 2 != 0)
                                        <div class="seccion">
                                            <p><strong>{{$item->nombre}}</strong></p>
                                            <p>{!! $item->detalle !!}</p>
                                        </div>
                                    @endif
                                @endforeach
                            </td>
                        </tr>
                    </table>
                </div>
                <br><br>
                <div class="seccion" style="background-color:rgb(243, 243, 243);">
                    <h3><strong>ANTECEDENTES</strong></h3>
                    <h3><strong>Posnatales</strong></h3>
                    <table style="border: none; width: 100%; table-layout: fixed;">
                        <tr style="border: none; ">
                            <td style="border: none; width: 50%; vertical-align: top;">
                                @foreach($antecedentesPosnatales as $index => $item)
                                    @if($index % 2 == 0)
                                        <div class="seccion">
                                            <p><strong>{{$item->nombre}}</strong></p>
                                            <p>{!! $item->detalle !!}</p>
                                        </div>
                                    @endif
                                @endforeach
                            </td>
                            <td style="border: none; width: 50%; vertical-align: top;">
                                @foreach($antecedentesPosnatales as $index => $item)
                                    @if($index % 2 != 0)
                                        <div class="seccion">
                                            <p><strong>{{$item->nombre}}</strong></p>
                                            <p>{!! $item->detalle !!}</p>
                                        </div>
                                    @endif
                                @endforeach
                            </td>
                        </tr>
                    </table>
                </div>
                <br><br>
                <div class="seccion" style="background-color:rgb(243, 243, 243);">
                    <h3><strong>ANTECEDENTES</strong></h3>
                    <h3><strong>Desarrollo Psicomotor</strong></h3>
                    <table style="border: none; width: 100%; table-layout: fixed;">
                        <tr style="border: none; ">
                            <td style="border: none; width: 50%; vertical-align: top;">
                                @foreach($desarrolloPsicomotor as $index => $item)
                                    @if($index % 2 == 0)
                                        <div class="seccion">
                                            <p><strong>{{$item->nombre}}</strong></p>
                                            <p>{!! $item->detalle !!}</p>
                                        </div>
                                    @endif
                                @endforeach
                            </td>
                            <td style="border: none; width: 50%; vertical-align: top;">
                                @foreach($desarrolloPsicomotor as $index => $item)
                                    @if($index % 2 != 0)
                                        <div class="seccion">
                                            <p><strong>{{$item->nombre}}</strong></p>
                                            <p>{!! $item->detalle !!}</p>
                                        </div>
                                    @endif
                                @endforeach
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="page-break"></div>
            @endif
            <div class="seccion" style="background-color:rgb(243, 243, 243);">
                <h3><strong>ANTECEDENTES</strong></h3>
                <h3><strong>Médicos Familiares</strong></h3>
                <table style="border: none; width: 100%; table-layout: fixed;">
                    <tr style="border: none; ">
                        <td style="border: none; width: 50%; vertical-align: top;">
                            @foreach($antecedentesFamiliares as $index => $item)
                                @if($index % 2 == 0)
                                    <div class="seccion">
                                        <p><strong>{{$item->nombre}}</strong></p>
                                        <p>{!! $item->detalle !!}</p>
                                    </div>
                                @endif
                            @endforeach
                        </td>
                        <td style="border: none; width: 50%; vertical-align: top;">
                            @foreach($antecedentesFamiliares as $index => $item)
                                @if($index % 2 != 0)
                                    <div class="seccion">
                                        <p><strong>{{$item->nombre}}</strong></p>
                                        <p>{!! $item->detalle !!}</p>
                                    </div>
                                @endif
                            @endforeach
                        </td>
                    </tr>
                </table>
            </div>
            <br><br>
            <div class="seccion" style="background-color:rgb(243, 243, 243);">
                <h3><strong>Áreas de Ajuste y/o Desempeño</strong></h3>
                @foreach($areaAjuste as $index => $item)
                    <div class="seccion">
                        <p><strong>{{$item->nombre}}</strong></p>
                        <p>{!! $item->detalle !!}</p>
                    </div>
                @endforeach
            </div>
            <br><br>
            <div class="seccion" style="background-color:rgb(243, 243, 243);">
                <h3><strong>Interconsultas e Intervenciones</strong></h3>
                @foreach($interconuslta as $index => $item)
                    <div class="seccion">
                        <p><strong>{{$item->nombre}}</strong></p>
                        <p>{!! $item->detalle !!}</p>
                    </div>
                @endforeach
            </div>
            <div class="page-break"></div>
            <div class="seccion" style="background-color:rgb(243, 243, 243);">
                <h3><strong>Apariencia personal</strong></h3>
                <table style="border: none; width: 100%; table-layout: fixed;">
                    <tr style="border: none; ">
                        <td style="border: none; width: 50%; vertical-align: top;">
                            @foreach($aparienciaPersonal as $index => $item)
                                @if($index % 2 == 0)
                                    <div class="seccion">
                                        <p><strong>{{$item->nombre}}</strong></p>
                                        <p>{!! $item->apariencia_detalle !!}</p>
                                    </div>
                                @endif
                            @endforeach
                        </td>
                        <td style="border: none; width: 50%; vertical-align: top;">
                            @foreach($aparienciaPersonal as $index => $item)
                                @if($index % 2 != 0)
                                    <div class="seccion">
                                        <p><strong>{{$item->nombre}}</strong></p>
                                        <p>{!! $item->apariencia_detalle !!}</p>
                                    </div>
                                @endif
                            @endforeach
                        </td>
                    </tr>
                </table>
            </div>
            <br><br>
            <div class="seccion" style="background-color:rgb(243, 243, 243);">
                <h3><strong>Funciones Cognitivas</strong></h3>
                <table style="border: none; width: 100%; table-layout: fixed;">
                    <tr style="border: none; ">
                        <td style="border: none; width: 50%; vertical-align: top;">
                            @foreach($funcionesCognitiva as $index => $item)
                                @if($index % 2 == 0)
                                    <div class="seccion">
                                        <p><strong>{{$item->nombre}}</strong></p>
                                        <p>{!! $item->funciones_detalle !!}</p>
                                    </div>
                                @endif
                            @endforeach
                        </td>
                        <td style="border: none; width: 50%; vertical-align: top;">
                            @foreach($funcionesCognitiva as $index => $item)
                                @if($index % 2 != 0)
                                    <div class="seccion">
                                        <p><strong>{{$item->nombre}}</strong></p>
                                        <p>{!! $item->funciones_detalle !!}</p>
                                    </div>
                                @endif
                            @endforeach
                        </td>
                    </tr>
                </table>
            </div>
            <br><br>
            <div class="seccion" style="background-color:rgb(243, 243, 243);">
                <h3><strong>Funciones Somáticas</strong></h3>
                <div class="seccion">
                    <p><strong>Ciclos del Sueño</strong></p>
                    <p>{!! $funcionesSomaticas->ciclos_del_sueno !!}</p>
                </div>
                <div class="seccion">
                    <p><strong>Apetito</strong></p>
                    <p>{!! $funcionesSomaticas->apetito !!}</p>
                </div>
                <div class="seccion">
                    <p><strong>Actividades de Autocuidado</strong></p>
                    <p>{!! $funcionesSomaticas->actividades_autocuidado !!}</p>
                </div>
            </div>
            <div class="seccion" style="background-color:rgb(243, 243, 243); margin-top: 20px;">
                <h3><strong>IMPRESIÓN DIAGNOSTICA</strong></h3>
                <div class="seccion">
                    <p><strong>Impresión Diagnóstica (CIE 10 - DSM-V):</strong></p>
                    <p>{!! $historia->impresion_diagnostica_detalle->nombre !!}</p>
                </div>
                <div class="seccion">
                    <p><strong>Establecido por primera vez</strong></p>
                    <p>{!! $historia->diagnostico_primera_vez !!}</p>
                </div>
            </div>
            <br><br>
            <div class="seccion" style="background-color:rgb(243, 243, 243); margin-top: 20px;">
                <h3><strong>PLAN DE INTERVENCIÓN</strong></h3>
                <div class="seccion">
                    <p><strong>Plan de intervención</strong></p>
                    <p>{!! $historia->plan_intervension_detalle->opcion !!}</p>
                </div>
                <div class="seccion">
                    <p><strong>Objetivo General</strong></p>
                    <p>{!! $historia->objetivo_general !!}</p>
                </div>
                <div class="seccion">
                    <p><strong>Objetivos Específicos</strong></p>
                    <p>{!! $historia->objetivos_especificos !!}</p>
                </div>
                <div class="seccion">
                    <p><strong>Sugerencia para Interconsultas</strong></p>
                    <p>{!! $historia->sugerencias_interconsultas !!}</p>
                </div>
                <div class="seccion">
                    <p><strong>Observaciones y Recomendaciones</strong></p>
                    <p>{!! $historia->observaciones_recomendaciones !!}</p>
                </div>
            </div>
        </div>
        <br><br>
        <div style="width: 100%; border-top: 1px solid grey;">
            <br>
            @if ($historia->profesional_detalle->firma != "sinFima.jpg")
            <img width="180"
                src="app-assets/images/firmasProfesionales/{{ $historia->profesional_detalle->firma }}"
                alt="">
             @endif
            <h2>{{$historia->profesional_detalle->nombre}}</h2>
            <h3><strong>Tarjeta Profesional: </strong>{{$historia->profesional_detalle->registro}}</h3>
        </div>
    </div>
</body>
</html>
