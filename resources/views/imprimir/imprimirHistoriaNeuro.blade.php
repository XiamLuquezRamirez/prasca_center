<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informe Historia Clínica</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
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

        td {
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
                <td colspan="2">{{$paciente->ocupacion}}</td>
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
                <td colspan="2">{{$paciente->zona_residencial == '01' ? 'Rural' : 'Urbana'}}</td>
            </tr>
        </table>
    </div>
</body>
</html>
