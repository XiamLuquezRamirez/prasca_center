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

        @page { margin: 5px; }

        /* Estilos para el salto de página en impresión */
        @media print {
            .page-break {
                page-break-before: always;
            }
        }

        td, tr {
            border: 1px solid black;
            padding: 5px;
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
        }

        .encabezado {
            background-color: #bfbfbf;
            font-weight: bold;
            width: 97.3%;
            padding: 10px;
            text-align: left;
        }

        .seccion {
            border-left: 1px solid grey;
            border-right: 1px solid grey;
            border-top: 1px solid grey;
            padding: 5px;
            border-radius: 5px 5px 0px 0px;
            background-color: white;
        }

        p {
            margin-bottom: 5px !important;
        }
    </style>
</head>
<body>
    <div class="container">
        <table>
            <tr>
                <td rowspan="2"> 
                    <div class="flex_div">
                        Historia <br> # {{$historia->id}}
                     </div>
                </td>
                <td></td>
            </tr>
            <tr>
                <td>
                    <div class="flex_div">
                        <strong>IMPRESIÓN DE HISTORIA CLÍNICA NEUROPSICOLOGÍA</strong>
                    </div>
                </td>
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
            <div class="seccion" style="background-color:rgb(238, 238, 238);">
                <h3><strong>DATOS GENERALES</strong></h3>
                <div class="seccion">
                    <p style="font-weight: bold;">Remisión</p>
                    <p>{!! $historia->remision !!}</p>
                </div>
                <div class="seccion">
                    <p><strong>DX principal</strong></p>
                    <p>{{ $historia->dx_principal_detalle->nombre }}</p>
                </div>
                <div class="seccion">
                    <p><strong>Código de consulta</strong></p>
                    <p>{{ $historia->codigo_consulta_detalle->nombre }}</p>
                </div>
                <div class="seccion">
                    <p><strong>Motivo de consulta</strong></p>
                    <p>{{ $historia->motivo_consulta_detalle->opcion }}</p>
                </div>
                @if ($historia->otro_motivo_consulta != null)
                    <div class="seccion">
                        <p><strong>Otro motivo de consulta</strong></p>
                        <p>{{ $historia->otro_motivo_consulta }}</p>
                    </div>
                @endif
                <div class="seccion">
                    <p><strong>Enfermedad actual</strong></p>
                    <p>{!! $historia->enfermedad_actual !!}</p>
                </div>
            </div>
            <br>
            <div class="seccion" style="background-color:rgb(238, 238, 238);">
                <h3><strong>ANTECEDENTES</strong></h3>
                <h4><strong>Médicos Personales</strong></h4>
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
        </div>
    </div>
</body>
</html>
