<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Especialidades;
use App\Models\Profesional;
use App\Models\Entidades;
use App\Models\HistoriaPsicologica;
use App\Models\Paquetes;
use App\Models\Gastos;
use App\Models\Pruebas;
use Dompdf\Dompdf;
use \PDF;
use App\Models\Pacientes;
use App\Models\Sesiones;
use App\Models\CUPS;
use App\Models\CIE10;


class AdminitraccionController extends Controller
{
    public function Especialidades()
    {
        if (Auth::check()) {
            $bandera = "";
            return view('Adminitraccion.gestionarEspecialidades', compact('bandera'));
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function CUPS()
    {
        if (Auth::check()) {
            $bandera = "";
            return view('Adminitraccion.gestionarCUPS', compact('bandera'));
        }
    }

    public function CIE10()
    {
        if (Auth::check()) {
            $bandera = "";
            return view('Adminitraccion.gestionarCIE10', compact('bandera'));
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function verificarCodigoCUPS(Request $request)
    {
        $codigo = $request->input('codigo');
        $id = $request->input('idRegistro');
        $cupsExistente = DB::connection('mysql')
            ->table('referencia_cups')
            ->where('id', '!=', $id)
            ->where('codigo', $codigo)
            ->exists();

            return response()->json(!$cupsExistente);
    }

    public function verificarCodigoCIE10(Request $request)
    {
        $codigo = $request->input('codigo');
        $id = $request->input('idRegistro');
        $cie10Existente = DB::connection('mysql')
            ->table('referencia_cie10')
            ->where('id', '!=', $id)
            ->where('codigo', $codigo)
            ->exists();

        return response()->json(!$cie10Existente);
    }

    public function buscaCUPS(Request $request)
    {
        $id = $request->input('idRegistro');
        $cups = DB::connection('mysql')
            ->table('referencia_cups')
            ->where('id', $id)
            ->first();

        return response()->json($cups);
    }

    public function buscaCIE10(Request $request)
    {
        $id = $request->input('idRegistro');
        $cie10 = DB::connection('mysql')
            ->table('referencia_cie10')
            ->where('id', $id)
            ->first();

        return response()->json($cie10);
    }
    

    public function listaCUPS(Request $request){
        
        if (Auth::check()) {
            $perPage = 10; // Número de posts por página
            $page = request()->get('page', 1);
            $search = request()->get('search');
            if (!is_numeric($page)) {
                $page = 1; // Establecer un valor predeterminado si no es numérico
            }

            $cups = DB::connection('mysql')
                ->table('referencia_cups')
                ->where('estado', 'ACTIVO')
                ->select(
                    'nombre',
                    'id',
                    'codigo',
                    'habilitado'
                );

            if ($search) {
                $cups->where(function ($query) use ($search) {
                    $query->where('nombre', 'LIKE', '%' . $search . '%');
                });
            }

            $ListCUPS = $cups->paginate($perPage, ['*'], 'page', $page);

            $tdTable = '';
            $x = ($page - 1) * $perPage + 1;
            $const = 1;
            foreach ($ListCUPS as $i => $item) {
                if (!is_null($item)) {
                    if ($item->habilitado == 'SI') {
                        $habilitado = '<span class="badge bg-success">Habilitado</span>';
                    } else {
                        $habilitado = '<span class="badge bg-danger">Deshabilitado</span>';
                    }
                    $tdTable .= '<tr>
                                    <td>' . $item->codigo . '</td>
                                    <td>' . $item->nombre . '</td>
                                    <td>' . $habilitado . '</td>
                                    <td class="table-action min-w-100">
                                        <a onclick="editarRegistro(' . $item->id . ');" style="cursor: pointer;" title="Editar" class="text-fade hover-primary"><i class="align-middle"
                                                data-feather="edit-2"></i></a>
                                        <a onclick="eliminarRegistro(' . $item->id . ');" style="cursor: pointer;" title="Eliminar" class="text-fade hover-warning"><i class="align-middle"
                                                data-feather="trash"></i></a>
                                    </td>
                                </tr>';
                    $x++;
                    $const++;
                }
            }
            $pagination = $ListCUPS->links('Adminitraccion.Paginacion')->render();

            return response()->json([
                'cups' => $tdTable,
                'links' => $pagination
            ]);
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function listaCIE10(Request $request){
        
        if (Auth::check()) {
            $perPage = 10; // Número de posts por página
            $page = request()->get('page', 1);
            $search = request()->get('search');
            if (!is_numeric($page)) {
                $page = 1; // Establecer un valor predeterminado si no es numérico
            }

            $cie10 = DB::connection('mysql')
                ->table('referencia_cie10')
                ->where('estado', 'ACTIVO')
                ->select(
                    'nombre',
                    'id',
                    'codigo',
                    'habilitado'
                );

            if ($search) {
                $cie10->where(function ($query) use ($search) {
                    $query->where('nombre', 'LIKE', '%' . $search . '%');
                });
            }

            $ListCIE10 = $cie10->paginate($perPage, ['*'], 'page', $page);

            $tdTable = '';
            $x = ($page - 1) * $perPage + 1;
            $const = 1;
            foreach ($ListCIE10 as $i => $item) {
                if (!is_null($item)) {
                    if ($item->habilitado == 'SI') {
                        $habilitado = '<span class="badge bg-success">Habilitado</span>';
                    } else {
                        $habilitado = '<span class="badge bg-danger">Deshabilitado</span>';
                    }
                    $tdTable .= '<tr>
                                    <td>' . $item->codigo . '</td>
                                    <td>' . $item->nombre . '</td>
                                    <td>' . $habilitado . '</td>
                                    <td class="table-action min-w-100">
                                        <a onclick="editarRegistro(' . $item->id . ');" style="cursor: pointer;" title="Editar" class="text-fade hover-primary"><i class="align-middle"
                                                data-feather="edit-2"></i></a>
                                        <a onclick="eliminarRegistro(' . $item->id . ');" style="cursor: pointer;" title="Eliminar" class="text-fade hover-warning"><i class="align-middle"
                                                data-feather="trash"></i></a>
                                    </td>
                                </tr>';
                    $x++;
                    $const++;
                }
            }
            $pagination = $ListCIE10->links('Adminitraccion.Paginacion')->render();

            return response()->json([
                'cie10' => $tdTable,
                'links' => $pagination
            ]);
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function guardarCUPS(Request $request)
    {
        

        if (!Auth::check()) {
            return response()->json([
                'estado' => 'error',
                'mensaje' => 'Su sesión ha terminado.',
            ], 401); // Código de error 401: No autorizado
        }

        // Capturar los datos del request
        $data = $request->all();
        // Guardar la información del paciente
        $respuesta = CUPS::guardar($data);

        // Verificar el resultado y preparar la respuesta
        if ($respuesta) {
            $estado = true;
        } else {
            $estado = false;
        }

        // Retornar la respuesta en formato JSON
        return response()->json([
            'success' => $estado,
            'id' => $respuesta,
            'message' => 'Datos guardados'
        ]);
    }

    public function guardarCIE10(Request $request)
    {
        

        if (!Auth::check()) {
            return response()->json([
                'estado' => 'error',
                'mensaje' => 'Su sesión ha terminado.',
            ], 401); // Código de error 401: No autorizado
        }

        // Capturar los datos del request
        $data = $request->all();
        // Guardar la información del paciente
        $respuesta = CIE10::guardar($data);

        // Verificar el resultado y preparar la respuesta
        if ($respuesta) {
            $estado = true;
        } else {
            $estado = false;
        }

        // Retornar la respuesta en formato JSON
        return response()->json([
            'success' => $estado,
            'id' => $respuesta,
            'message' => 'Datos guardados'
        ]);
    }

    public function eliminarCUPS(Request $request)
    {
        $idReg = $request->input('idReg');
        $cups = DB::connection('mysql')
            ->table('referencia_cups')
            ->where('id', $idReg)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'CUPS eliminada correctamente'
        ]);
    }

    public function eliminarCIE10(Request $request)
    {
        $idReg = $request->input('idReg');
        $cie10 = DB::connection('mysql')
            ->table('referencia_cie10')
            ->where('id', $idReg)   
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'CIE10 eliminada correctamente'
        ]);
    }

    public function Sesiones()
    {
        if (Auth::check()) {
            $bandera = "";
            return view('Adminitraccion.gestionarSesiones', compact('bandera'));
        }
    }
    
    public function Pruebas()
    {
        if (Auth::check()) {
            $bandera = "";
            return view('Adminitraccion.gestionarPruebas', compact('bandera'));
        }
    }

    public function Recaudos()
    {
        if (Auth::check()) {
            $bandera = "";
            return view('Recaudos.gestionRecaudos', compact('bandera'));
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }
    public function Gastos()
    {
        if (Auth::check()) {
            $bandera = "";
            return view('Recaudos.gestionGastos', compact('bandera'));
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }
    public function Cajas()
    {
        if (Auth::check()) {
            $bandera = "";
            return view('Recaudos.gestionCajas', compact('bandera'));
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function Entidades()
    {
        if (Auth::check()) {
            $bandera = "";
            return view('Adminitraccion.gestionarEPS', compact('bandera'));
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function listaPruebas(Request $request) {
        
        if (Auth::check()) {
            $perPage = 10; // Número de posts por página
            $page = request()->get('page', 1);
            $search = request()->get('search');
            if (!is_numeric($page)) {
                $page = 1; // Establecer un valor predeterminado si no es numérico
            }

            $paquetes = DB::connection('mysql')
                ->table('pruebas')
                ->where('estado', 'ACTIVO')
                ->select(
                    'descripcion',
                    'id',
                    'precio'
                );

            if ($search) {
                $paquetes->where(function ($query) use ($search) {
                    $query->where('descripcion', 'LIKE', '%' . $search . '%');
                });
            }

            $ListPaquetes = $paquetes->paginate($perPage, ['*'], 'page', $page);

            $tdTable = '';
            $x = ($page - 1) * $perPage + 1;
            $const = 1;
            foreach ($ListPaquetes as $i => $item) {
                if (!is_null($item)) {
                    $valor = number_format($item->precio, 2, ',', '.');
                    $tdTable .= '<tr>
                                    <td>' . $item->descripcion . '</td>
                                    <td>$ ' . $valor . '</td>
                                    <td class="table-action min-w-100">
                                        <a onclick="editarRegistro(' . $item->id . ');" style="cursor: pointer;" title="Editar" class="text-fade hover-primary"><i class="align-middle"
                                                data-feather="edit-2"></i></a>
                                        <a onclick="eliminarRegistro(' . $item->id . ');" style="cursor: pointer;" title="Eliminar" class="text-fade hover-warning"><i class="align-middle"
                                                data-feather="trash"></i></a>
                                    </td>
                                </tr>';
                    $x++;
                    $const++;
                }
            }
            $pagination = $ListPaquetes->links('Adminitraccion.Paginacion')->render();

            return response()->json([
                'pruebas' => $tdTable,
                'links' => $pagination
            ]);
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }

    }

    public function listaSesiones(Request $request) {
        
        if (Auth::check()) {
            $perPage = 10; // Número de posts por página
            $page = request()->get('page', 1);
            $search = request()->get('search');
            if (!is_numeric($page)) {
                $page = 1; // Establecer un valor predeterminado si no es numérico
            }

            $sesiones = DB::connection('mysql')
                ->table('sesiones')
                ->where('estado', 'ACTIVO')
                ->select(
                    'descripcion',
                    'id',
                    'precio'
                );

            if ($search) {
                $sesiones->where(function ($query) use ($search) {
                    $query->where('descripcion', 'LIKE', '%' . $search . '%');
                });
            }

            $ListSesiones = $sesiones->paginate($perPage, ['*'], 'page', $page);

            $tdTable = '';
            $x = ($page - 1) * $perPage + 1;
            $const = 1;
            foreach ($ListSesiones as $i => $item) {
                if (!is_null($item)) {
                    $valor = number_format($item->precio, 2, ',', '.');
                    $tdTable .= '<tr>
                                    <td>' . $item->descripcion . '</td>
                                    <td>$ ' . $valor . '</td>
                                    <td class="table-action min-w-100">
                                        <a onclick="editarRegistro(' . $item->id . ');" style="cursor: pointer;" title="Editar" class="text-fade hover-primary"><i class="align-middle"
                                                data-feather="edit-2"></i></a>
                                        <a onclick="eliminarRegistro(' . $item->id . ');" style="cursor: pointer;" title="Eliminar" class="text-fade hover-warning"><i class="align-middle"
                                                data-feather="trash"></i></a>
                                    </td>
                                </tr>';
                    $x++;
                    $const++;
                }
            }
            $pagination = $ListSesiones->links('Adminitraccion.Paginacion')->render();

            return response()->json([
                'sesiones' => $tdTable,
                'links' => $pagination
            ]);
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }

    }


    function cerrarCaja()
    {
        if (Auth::check()) {
            $idCaja = request()->input('idCajaCierre');
            $saldoCierre = request()->input('valorMontoCierre');
            $gastos = request()->input('valorMontoGastos');
            $recaudos = request()->input('valorMontoRecaudos');
            $fechaCierre = request()->input('fecCierre');

            DB::beginTransaction();

            try {
                $consulta = DB::connection('mysql')
                    ->table('cajas')
                    ->where('id', $idCaja)
                    ->update([
                        'fecha_cierre' => $fechaCierre,
                        'recaudos' => $recaudos,
                        'gastos' => $gastos,
                        'saldo_cierre' => $saldoCierre,
                        'estado_caja' => 'Cerrada'
                    ]);

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

            if ($consulta) {
                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Caja cerrada correctamente'
                    ]
                );
            } else {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'No se encontró la caja o no se pudo cerrar'
                    ],
                    404
                );
            }
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function imprimirRecaudo(Request $request)
    {
        if (Auth::check()) {
            $pdf = new Dompdf();
            $idRecaudo = $request->input('idRecaudo');
            $recaudo = HistoriaPsicologica::busquedaRecaudo($idRecaudo);
            $mediosPagos = HistoriaPsicologica::listarMediosPagos($idRecaudo);
           
            // Ruta del logo y conversión a base64
            $logoPath = public_path('app-assets/images/logo/logo_prasca.png');
            $logoData = base64_encode(file_get_contents($logoPath));
            $logo = 'data:image/png;base64,' . $logoData;

            $fechaImpresion = now()->format('d-m-Y');
            $fechaPago = \Carbon\Carbon::parse($recaudo->fecha_pago)->format('d/m/Y');

            $paciente = Pacientes::busquedaPaciente($recaudo->id_paciente);
            $ncompro = self::addCeros($recaudo->id, 5);
           
            $html = '<html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; font-size: 12px; }
                table { width: 100%; border-collapse: collapse; }
                th, td { border: 0.5px solid black; padding: 4px; text-align: left; }
                th { background-color: #EAEBF4; }
                tr:nth-child(even) { background-color: #f2f2f2; }
                .no-border { border: none; text-align: center; }
                h3, h2 { margin: 5px 0; text-align: center; }
                .section { margin-bottom: 10px; }
                .page-break { page-break-after: always; }
            </style>
        </head>
        <body>';

            // Encabezado
            $html .= '<table>
            <tr>
                <td class="no-border"><img src="' . $logo . '" style="width: 200px;"></td>
                <td class="no-border">
                    <p><strong>DRA. MARIA ISABEL PUMAREJO</strong></p>
                    <p>PSICÓLOGA - T.P. No. 259542</p>
                    <p>Calle 11 # 11 - 07 San Joaquin</p>
                    <p>Teléfono: 312 5678078</p>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="no-border"><h3>COMPROBANTE DE PAGO - ' . $ncompro . '</h3></td>
            </tr>
        </table>';

            // Fechas
            $html .= '<table>
            <tr>
                <td><b>FECHA DE PAGO:</b> ' . $fechaPago . '</td>
                <td><b>FECHA DE IMPRESIÓN:</b> ' . $fechaImpresion . '</td>
            </tr>
        </table>';

            // Información del paciente
            $html .= '<div class="section">
            <table>
                <tr>
                    <td colspan="2"><strong>PRIMER APELLIDO:</strong> ' . $paciente->primer_apellido . '</td>
                    <td ><strong>SEGUNDO APELLIDO:</strong> ' . $paciente->segundo_apellido . '</td>
                </tr>
                <tr>
                    <td colspan="3"><strong>NOMBRES:</strong> ' . $paciente->primer_nombre . '</td>
                </tr>
                <tr>
                    <td><strong>IDENTIFICACIÓN:</strong> ' . $paciente->tipo_identificacion . ' ' . $paciente->identificacion . '</td>
                    <td><strong>EDAD:</strong> ' . $paciente->edad . '</td>
                    <td><strong>SEXO:</strong> ' . (($paciente->sexo === "M") ? "Mujer" : "Hombre") . '</td>
                </tr>
                <tr>
                    <td><strong>DIRECCIÓN:</strong> ' . $paciente->direccion . '</td>
                    <td colspan="2"><strong>CELULAR:</strong> ' . $paciente->telefono . '</td>
                </tr>
            </table>
        </div>';

            // Detalles del pago
            $html .= '<h3>DETALLES DEL PAGO</h3>
        <table>
            <tr>
                <td><strong>DESCRIPCIÓN:</strong><br>' . $recaudo->descripcion->descripcion . '</td>
                <td><strong>VALOR:</strong><br>$ ' . number_format($recaudo->pago_realizado, 2, ',', '.') . '</td>
            </tr>
        </table>';

            // Medios de pago
            $html .= '<h5>MEDIOS DE PAGO</h5>
        <table>';
            foreach ($mediosPagos as $medioPago) {
                $html .= '<tr>
                <td><strong>MÉTODO DE PAGO:</strong> ' . $medioPago->nombreMedioPago . '</td>
                <td><strong>VALOR:</strong> $ ' . number_format($medioPago->valor, 2, ',', '.') . '</td>
                <td><strong>REFERENCIA:</strong> ' . $medioPago->referencia . '</td>
            </tr>';
            }
            $html .= '</table>';

            // Detalles del servicio
            $html .= '<h3>DETALLES DEL SERVICIO</h3>
        <table>
            <tr>
                <td><strong>DESCRIPCIÓN:</strong><br>' . $recaudo->descripcion->descripcion . '</td>
                <td><strong>VALOR:</strong><br>$ ' . number_format($recaudo->precio, 2, ',', '.') . '</td>
                <td><strong>SALDO:</strong><br>$ ' . number_format($recaudo->saldo, 2, ',', '.') . '</td>
            </tr>
        </table>';

            // Cierre del HTML
            $html .= '</body></html>';

            // Generar PDF
            $pdf->loadHtml($html);
            $pdf->setPaper('A4', 'portrait');
            $pdf->render();

            return $pdf->stream('recaudo_' . $ncompro . '.pdf');
        }
    }

    public function addCeros($numero, $cantidad_digitos)
    {
        $numero_con_ceros = str_pad($numero, $cantidad_digitos, '0', STR_PAD_LEFT);
        return $numero_con_ceros;
    }

    public function Paquetes()
    {
        if (Auth::check()) {
            $bandera = "";
            return view('Adminitraccion.gestionarPaquetes', compact('bandera'));
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }
    public function Profesionales()
    {
        if (Auth::check()) {
            $bandera = "";
            return view('Adminitraccion.gestionarProfesionales', compact('bandera'));
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function eliminarPagoRecaudo()
    {
        try {
            $idPago = request()->input('idPago');
            if (!$idPago) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'ID de pago no proporcionado'
                    ],
                    400
                );
            }

            DB::beginTransaction();
            try {

                $consulta = DB::connection('mysql')
                    ->table('pagos')
                    ->where('id', $idPago)
                    ->update([
                        'estado' => 'ELIMINADO',
                    ]);

                $pagos = DB::connection('mysql')
                    ->table('pagos')
                    ->where('id', $idPago)
                    ->select('id_servicio')
                    ->first();

                // actualizar el saldo de la venta
                $venta = DB::connection('mysql')
                    ->table('servicios')
                    ->where('id', $pagos->id_servicio)
                    ->select('id', 'precio')
                    ->first();

                $listSaldo = DB::connection('mysql')
                    ->table('pagos')
                    ->where('id_servicio', $venta->id)
                    ->where('estado', 'ACTIVO')
                    ->sum('pago_realizado');

                $saldo = $venta->precio - $listSaldo;

                $actualizarSaldo = DB::connection('mysql')
                    ->table('ventas')
                    ->where('id_servicio', $venta->id)
                    ->update([
                        'saldo' => $saldo,
                        'estado_venta' => 'PENDIENTE'
                    ]);

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
            if ($consulta) {
                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Pago eliminado correctamente',
                        'idServicio' => $venta->id

                    ]
                );
            } else {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'No se encontró el pago o no se pudo eliminar'
                    ],
                    404
                );
            }
        } catch (\Exception $e) {
            // Manejar cualquier error o excepción
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Ocurrió un error al intentar eliminar el pago',
                    'error' => $e->getMessage()
                ],
                500
            );
        }
    }

    public function listaEntidades(Request $request)
    {
        if (Auth::check()) {
            $perPage = 10; // Número de posts por página
            $page = request()->get('page', 1);
            $search = request()->get('search');
            if (!is_numeric($page)) {
                $page = 1; // Establecer un valor predeterminado si no es numérico
            }

            $entidades = DB::connection('mysql')
                ->table('eps')
                ->where('estado', 'ACTIVO')
                ->select(
                    'entidad',
                    'id',
                    'codigo'
                );

            if ($search) {
                $entidades->where(function ($query) use ($search) {
                    $query->where('entidad', 'LIKE', '%' . $search . '%');
                });
            }

            $ListEntidades = $entidades->paginate($perPage, ['*'], 'page', $page);

            $tdTable = '';
            $x = ($page - 1) * $perPage + 1;
            $const = 1;
            foreach ($ListEntidades as $i => $item) {
                if (!is_null($item)) {
                    $tdTable .= '<tr>
                                    <td>' . $item->codigo . '</td>
                                    <td>' . $item->entidad . '</td>
                                    <td class="table-action min-w-100">
                                        <a onclick="editarRegistro(' . $item->id . ');" style="cursor: pointer;" title="Editar" class="text-fade hover-primary"><i class="align-middle"
                                                data-feather="edit-2"></i></a>
                                        <a onclick="eliminarRegistro(' . $item->id . ');" style="cursor: pointer;" title="Eliminar" class="text-fade hover-warning"><i class="align-middle"
                                                data-feather="trash"></i></a>
                                    </td>
                                </tr>';
                    $x++;
                    $const++;
                }
            }
            $pagination = $ListEntidades->links('Adminitraccion.Paginacion')->render();

            return response()->json([
                'entidades' => $tdTable,
                'links' => $pagination
            ]);
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function listaPaquetes(Request $request)
    {
        if (Auth::check()) {
            $perPage = 10; // Número de posts por página
            $page = request()->get('page', 1);
            $search = request()->get('search');
            if (!is_numeric($page)) {
                $page = 1; // Establecer un valor predeterminado si no es numérico
            }

            $paquetes = DB::connection('mysql')
                ->table('paquetes')
                ->where('estado', 'ACTIVO')
                ->select(
                    'descripcion',
                    'id',
                    'precio_por_sesion'
                );

            if ($search) {
                $paquetes->where(function ($query) use ($search) {
                    $query->where('descripcion', 'LIKE', '%' . $search . '%');
                });
            }

            $ListPaquetes = $paquetes->paginate($perPage, ['*'], 'page', $page);

            $tdTable = '';
            $x = ($page - 1) * $perPage + 1;
            $const = 1;
            foreach ($ListPaquetes as $i => $item) {
                if (!is_null($item)) {
                    $valor = number_format($item->precio_por_sesion, 2, ',', '.');
                    $tdTable .= '<tr>
                                    <td>' . $item->descripcion . '</td>
                                    <td>$ ' . $valor . '</td>
                                    <td class="table-action min-w-100">
                                        <a onclick="editarRegistro(' . $item->id . ');" style="cursor: pointer;" title="Editar" class="text-fade hover-primary"><i class="align-middle"
                                                data-feather="edit-2"></i></a>
                                        <a onclick="eliminarRegistro(' . $item->id . ');" style="cursor: pointer;" title="Eliminar" class="text-fade hover-warning"><i class="align-middle"
                                                data-feather="trash"></i></a>
                                    </td>
                                </tr>';
                    $x++;
                    $const++;
                }
            }
            $pagination = $ListPaquetes->links('Adminitraccion.Paginacion')->render();

            return response()->json([
                'paquetes' => $tdTable,
                'links' => $pagination
            ]);
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    function listaCategorias()
    {
        if (Auth::check()) {
            $categorias = DB::connection('mysql')
                ->table('categorias_gastos')
                ->where('estado', 'ACTIVO')
                ->select(
                    'descripcion',
                    'id'
                )->get();

            return response()->json([
                'categorias' => $categorias
            ]);
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function listaGastos(Request $request)
    {
        if (Auth::check()) {
            $perPage = 20; // Número de posts por página
            $page = request()->get('page', 1);
            $search = request()->get('search');
            $fecha1 = request()->get('fecha1');
            $fecha2 = request()->get('fecha2');

            $fechaInicio = \Carbon\Carbon::createFromFormat('d/m/Y', $fecha1)->format('Y-m-d') . 'T00:00:00';
            $fechaFin = \Carbon\Carbon::createFromFormat('d/m/Y', $fecha2)->format('Y-m-d') . 'T23:59:59';


            if (!is_numeric($page)) {
                $page = 1; // Establecer un valor predeterminado si no es numérico
            }

            $gastos = DB::connection('mysql')
                ->table('gastos')
                ->where('gastos.estado', 'ACTIVO')
                ->leftJoin('categorias_gastos', 'gastos.categoria', 'categorias_gastos.id')
                ->select(
                    'gastos.id',
                    'gastos.descripcion',
                    'gastos.valor',
                    'gastos.fecha_gasto',
                    'categorias_gastos.descripcion AS categoria'
                );

            if ($search) {
                $gastos->where(function ($query) use ($search) {
                    $query->where('gastos.descripcion', 'LIKE', '%' . $search . '%')
                        ->orWhere('categorias_gastos.descripcion', 'LIKE', '%' . $search . '%');
                });
            }

            if (!empty($fechaInicio)) {
                $gastos->whereBetween('gastos.fecha_gasto',  [$fechaInicio, $fechaFin]);
            }


            $ListGastos = $gastos->paginate($perPage, ['*'], 'page', $page);

            $gastosTotales = $gastos->sum('gastos.valor');

            $tdTable = '';
            $x = ($page - 1) * $perPage + 1;
            $const = 1;

            foreach ($ListGastos as $i => $item) {
                if (!is_null($item)) {
                    $valor = number_format($item->valor, 2, ',', '.');
                    $fecha = \Carbon\Carbon::parse($item->fecha_gasto)->format('d/m/Y');

                    $tdTable .= '<tr>
                                    <td>' . $item->descripcion . '</td>
                                    <td>' . $item->categoria . '</td>
                                    <td>' . $fecha . '</td>
                                    <td>$ ' . $valor . '</td>
                                    <td class="table-action min-w-100">
                                        <a onclick="editarRegistro(' . $item->id . ');" style="cursor: pointer;" title="Editar" class="text-fade hover-primary"><i class="align-middle"
                                                data-feather="edit-2"></i></a>
                                        <a onclick="eliminarRegistro(' . $item->id . ');" style="cursor: pointer;" title="Eliminar" class="text-fade hover-warning"><i class="align-middle"
                                                data-feather="trash"></i></a>
                                    </td>
                                </tr>';
                    $x++;
                    $const++;
                }
            }
            $pagination = $ListGastos->links('Recaudos.PaginacionRecaudos')->render();

            return response()->json([
                'gastos' => $tdTable,
                'links' => $pagination,
                'gastosTotales' => number_format($gastosTotales, 2, ',', '.')
            ]);
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function listaCajas(Request $request)
    {
        if (Auth::check()) {
            $perPage = 20; // Número de posts por página
            $page = request()->get('page', 1);
            if (!is_numeric($page)) {
                $page = 1; // Establecer un valor predeterminado si no es numérico
            }

            $cajas = DB::connection('mysql')
                ->table('cajas')
                ->leftJoin("users", "users.id", "cajas.usuario")
                ->select("cajas.*", "users.nombre_usuario")
                ->where('estado_reg', 'ACTIVO');

            $ultimaCaja = DB::connection('mysql')
                ->table('cajas')
                ->where('estado_caja', 'Cerrada')
                ->where('estado_reg', 'ACTIVO')
                ->latest()
                ->first();
            $saldoAnterior = 0;
            if ($ultimaCaja) {
                $saldoAnterior = $ultimaCaja->saldo_cierre;
            }

            $ListCajas = $cajas->paginate($perPage, ['*'], 'page', $page);

            $tdTable = '';
            $j = 1;
            $x = ($page - 1) * $perPage + 1;

            foreach ($ListCajas as $i => $item) {
                if (!is_null($item)) {
                    $saldo_inicial = $item->saldo_inicial + $item->abono_inicial;

                    $fechaApertura = $item->fecha_apertura;

                    $saldo_acomulado = Pacientes::recaudoCaja($fechaApertura);
                    $gastos = Gastos::GastosCaja($fechaApertura);

                    $saldo = ($saldo_inicial + $saldo_acomulado);

                    $saldo = $saldo - $gastos;

                    $tdTable .= '<tr>
                <td><span class="invoice-date">' . str_pad($j, 5, '0', STR_PAD_LEFT) . '</span></td>
                <td><span class="invoice-date">' . $fechaApertura . '</span></td>
                <td><span class="invoice-date">' . $item->fecha_cierre . '</span></td>
                <td><span class="invoice-date">$ ' . number_format($item->saldo_inicial, 2, ',', '.') . '</span></td>
                <td><span class="invoice-date">$ ' . number_format($saldo_acomulado, 2, ',', '.') . '</span></td>
                <td><span class="invoice-date">$ ' . number_format($gastos, 2, ',', '.') . '</span></td>';
                    if ($item->estado_caja == "Abierta") {
                        $tdTable .= '<td><span class="invoice-date"><span class="badge badge-success"> ' . $item->estado_caja . '</span></span></td>';
                    } else {
                        $tdTable .= '<td><span class="invoice-date"><span class="badge badge-warning"> ' . $item->estado_caja . '</span></span></td>';
                    }

                    $tdTable .= '<td>
                    <div class="invoice-action">
                    <button type="button" onclick="verDetalle(' . $item->id . ');"  class="waves-effect waves-light btn btn-primary mb-5"><i class="fa fa-search"></i> Ver detalles</button>
                    <button type="button" onclick="eliminar(' . $item->id . ');"  class="waves-effect waves-light btn btn-danger mb-5"><i class="fa fa-trash-o"></i> Eliminar</button>
                    </div>
                </td>
            </tr>';

                    $x++;
                    $j++;
                }
            }

            $pagination = $ListCajas->links('Recaudos.PaginacionCajas')->render();

            return response()->json([
                'cajas' => $tdTable,
                'links' => $pagination,
                'saldoAnterior' => $saldoAnterior,
            ]);
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }


    public function listaVentasPacientes(Request $request)
    {
        if (Auth::check()) {
            $perPage = 10; // Número de posts por página
            $page = request()->get('page', 1);
            $search = request()->get('search');
            if (!is_numeric($page)) {
                $page = 1; // Establecer un valor predeterminado si no es numérico
            }

            $servicios = DB::connection('mysql')
            ->table('servicios')        
            ->leftJoin("ventas", "servicios.id", "ventas.id_servicio")
            ->leftJoin('pacientes', 'servicios.id_paciente', 'pacientes.id')
            ->where('servicios.estado', 'ACTIVO')                     
            ->where('ventas.estado_venta', 'PENDIENTE')
            ->groupBy([
                'servicios.id',
                'servicios.id_tipo_servicio',
                'servicios.tipo',
                'servicios.id_paciente',
                'servicios.fecha',
                'servicios.precio',
                'ventas.cantidad',
                'ventas.estado_venta',
                'ventas.saldo',
                'pacientes.tipo_identificacion',
                'pacientes.identificacion',
                'pacientes.primer_nombre',
                'pacientes.segundo_nombre',
                'pacientes.primer_apellido',
                'pacientes.segundo_apellido'
            ])
            ->select(
                'servicios.id_tipo_servicio',
                DB::raw('CASE WHEN  servicios.tipo = "SESION" THEN "SESIÓN" ELSE  servicios.tipo END AS tipo'),
                DB::raw("CONCAT(pacientes.tipo_identificacion, ' ', pacientes.identificacion) AS identificacion_completa"),
                DB::raw("CONCAT(pacientes.primer_nombre,' ',pacientes.segundo_nombre,' ',pacientes.primer_apellido,' ',pacientes.segundo_apellido) AS nombre_paciente"),
                'servicios.id_paciente',
                'servicios.fecha',
                'servicios.precio',
                'servicios.id',
                'ventas.cantidad',
                'ventas.estado_venta',
                'ventas.saldo',
                DB::raw("(SELECT nombre FROM especialidades WHERE especialidades.id = servicios.id_tipo_servicio AND servicios.tipo = 'CONSULTA' LIMIT 1) AS descripcion_consulta"),
                DB::raw("(SELECT descripcion FROM sesiones WHERE sesiones.id = servicios.id_tipo_servicio AND servicios.tipo = 'SESION' LIMIT 1) AS descripcion_sesion"),
                DB::raw("(SELECT descripcion FROM paquetes WHERE paquetes.id = servicios.id_tipo_servicio AND servicios.tipo = 'PAQUETE' LIMIT 1) AS descripcion_paquete"),
                DB::raw("(SELECT descripcion FROM pruebas WHERE pruebas.id = servicios.id_tipo_servicio AND servicios.tipo = 'PRUEBAS' LIMIT 1) AS descripcion_pruebas"),
                DB::raw("
                    COALESCE(
                        (SELECT nombre FROM especialidades WHERE especialidades.id = servicios.id_tipo_servicio AND servicios.tipo = 'CONSULTA' LIMIT 1),
                        (SELECT descripcion FROM sesiones WHERE sesiones.id = servicios.id_tipo_servicio AND servicios.tipo = 'SESION' LIMIT 1),
                        (SELECT descripcion FROM paquetes WHERE paquetes.id = servicios.id_tipo_servicio AND servicios.tipo = 'PAQUETE' LIMIT 1),
                        (SELECT descripcion FROM pruebas WHERE pruebas.id = servicios.id_tipo_servicio AND servicios.tipo = 'PRUEBAS' LIMIT 1),
                        'Sin descripción'
                    ) AS descripcion
                ")
            );

            if ($search) {
                $servicios->where(function ($query) use ($search) {
                    $query->where('servicios.descripcion', 'LIKE', '%' . $search . '%')
                        ->orWhere('pacientes.identificacion', 'LIKE', '%' . $search . '%')
                        ->orWhere('pacientes.primer_nombre', 'LIKE', '%' . $search . '%')
                        ->orWhere('pacientes.primer_apellido', 'LIKE', '%' . $search . '%');
                });
            }

            $ListServicios = $servicios->paginate($perPage, ['*'], 'page', $page);

            $tdTable = '';
            $x = ($page - 1) * $perPage + 1;
            $const = 1;
            foreach ($ListServicios as $i => $item) {
                if (!is_null($item)) {
                    $saldo = number_format($item->saldo, 2, ',', '.');
                    $valor = number_format($item->precio, 2, ',', '.');
                    $fecha = \Carbon\Carbon::parse($item->fecha)->format('d/m/Y');

                    $tdTable .= '<tr>
                                    <td>
                                        <div style="cursor: pointer" onclick="realizarPago(' . $item->id . ');"
                                            class="bg-primary-light h-50 w-50 l-h-60 rounded text-center">
                                            <span class="fa fa-dollar fs-24"></span>
                                        </div>
                                    </td>
                                    <td>
                                        <h5 class="text-dark fw-600 hover-primary m-0">' . $item->identificacion_completa . ' - ' . $item->nombre_paciente . '</h5>
                                        <span class="text-fade d-block fs-14">
                                        <strong>Servicio: </strong> ' . $item->tipo . '</span>
                                        <span class="text-fade d-block fs-14">
                                        <strong>Descripción: </strong> ' . $item->descripcion . '</span>
                                    </td>
                                    <td>
                                        <h3>' . $fecha . '</h3>
                                    </td>
                                    <td>
                                    <div class="text-center"><h3>$' . $valor . '</h3></div>
                                        
                                    </td>
                                    <td>
                                        <h3>$ ' . $saldo . '</h3>
                                    </td>
                                </tr>';
                    $x++;
                    $const++;
                }
            }
            $pagination = $ListServicios->links('Recaudos.PaginacionRecaudos')->render();

            return response()->json([
                'paquetesVentas' => $tdTable,
                'links' => $pagination
            ]);
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function otraInformacionRecaudos(Request $request)
    {
        if (Auth::check()) {
            //consultar pagos realizados 
            $pagosPendientes = DB::connection('mysql')
                ->table('ventas')
                ->leftJoin('servicios', 'ventas.id_servicio', 'servicios.id')
                ->where('servicios.estado', 'ACTIVO')
                ->where('ventas.estado_venta', 'PENDIENTE')
                ->count();

            /// consultar ventas con pago
            $ventasPagadas = DB::connection('mysql')
                ->table('ventas')
                ->leftJoin('servicios', 'ventas.id_servicio', 'servicios.id')
                ->where('servicios.estado', 'ACTIVO')
                ->where('ventas.estado_venta', 'PAGADO')
                ->count();

            /// consutlar recaudo de mes
            $recaudoMes = DB::connection('mysql')
                ->table('pagos')

                ->where('estado', 'ACTIVO')
                ->whereMonth('fecha_pago', date('m'))
                ->sum('pago_realizado');

            //consutlar recaudo de dia
            $recaudoDia = DB::connection('mysql')
                ->table('pagos')
                ->where('estado', 'ACTIVO')
                ->whereDate('fecha_pago', date('Y-m-d'))
                ->sum('pago_realizado');


            //consultar historial de pagos realizados mostra los ultimo 5 pagos realizados ordenados por fecha
            $historialPagos = DB::connection('mysql')
                ->table('pagos')
                ->leftJoin('servicios', 'pagos.id_servicio', 'servicios.id')
                ->leftJoin('pacientes', 'servicios.id_paciente', 'pacientes.id')
                ->where('pagos.estado', 'ACTIVO')
                ->orderBy('fecha_pago', 'desc')
                ->groupBy([
                    'pagos.id',
                    'pagos.pago_realizado',
                    'pagos.fecha_pago',
                    'pacientes.primer_nombre',
                    'pacientes.primer_apellido',
                    'servicios.id_tipo_servicio',
                    'servicios.tipo'
                ])
                ->select(
                    'pagos.id',
                    'pagos.pago_realizado',
                    'pagos.fecha_pago',
                    'pacientes.primer_nombre',
                    'pacientes.primer_apellido',
                    DB::raw("
                    COALESCE(
                        (SELECT nombre FROM especialidades WHERE especialidades.id = servicios.id_tipo_servicio AND servicios.tipo = 'CONSULTA' LIMIT 1),
                        (SELECT descripcion FROM sesiones WHERE sesiones.id = servicios.id_tipo_servicio AND servicios.tipo = 'SESION' LIMIT 1),
                        (SELECT descripcion FROM paquetes WHERE paquetes.id = servicios.id_tipo_servicio AND servicios.tipo = 'PAQUETE' LIMIT 1),
                        (SELECT descripcion FROM pruebas WHERE pruebas.id = servicios.id_tipo_servicio AND servicios.tipo = 'PRUEBAS' LIMIT 1),
                        'Sin descripción'
                    ) AS descripcion
                ")

                )
                ->limit(5)
                ->get();

            return response()->json([
                'pagosPendientes' => $pagosPendientes,
                'ventasPagadas' => $ventasPagadas,
                'recaudoMes' => $recaudoMes,
                'recaudoDia' => $recaudoDia,
                'historialPagos' => $historialPagos
            ]);
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }



    public function listaVentasPacientesPagos(Request $request)
    {
        if (Auth::check()) {
            $perPage = 10; // Número de posts por página
            $page = request()->get('pagePago', 1);
            $search = request()->get('searchPago');
            if (!is_numeric($page)) {
                $page = 1; // Establecer un valor predeterminado si no es numérico
            }

            $paquetes = DB::connection('mysql')
                ->table('servicios')
                ->leftJoin('pacientes', 'servicios.id_paciente',  'pacientes.id')
                ->leftJoin('ventas', 'servicios.id',  'ventas.id_servicio')
                ->where('ventas.estado_venta', 'PAGADO')
                ->groupBy([
                    'servicios.id',
                    'pacientes.tipo_identificacion',
                    'pacientes.identificacion',
                    'pacientes.primer_nombre',
                    'pacientes.segundo_nombre',
                    'pacientes.primer_apellido',
                    'pacientes.segundo_apellido',
                    'servicios.id_tipo_servicio',
                    'servicios.tipo',
                    'ventas.total',
                    'ventas.id'
                ])
                ->select(
                    'servicios.id',
                    DB::raw("CONCAT(pacientes.tipo_identificacion, ' ', pacientes.identificacion) as identificacion_completa"),
                    DB::raw("CONCAT(pacientes.primer_nombre,' ',pacientes.segundo_nombre,' ',pacientes.primer_apellido,' ',pacientes.segundo_apellido) as nombre_paciente"),
                    'ventas.total',
                    'ventas.id AS id_venta',
                    DB::raw("
                    COALESCE(
                        (SELECT nombre FROM especialidades WHERE especialidades.id = servicios.id_tipo_servicio AND servicios.tipo = 'CONSULTA' LIMIT 1),
                        (SELECT descripcion FROM sesiones WHERE sesiones.id = servicios.id_tipo_servicio AND servicios.tipo = 'SESION' LIMIT 1),
                        (SELECT descripcion FROM paquetes WHERE paquetes.id = servicios.id_tipo_servicio AND servicios.tipo = 'PAQUETE' LIMIT 1),
                        (SELECT descripcion FROM pruebas WHERE pruebas.id = servicios.id_tipo_servicio AND servicios.tipo = 'PRUEBAS' LIMIT 1),
                        'Sin descripción'
                    ) AS descripcion
                ")

                );

            if ($search) {
                $paquetes->where(function ($query) use ($search) {
                    $query->where('servicios.descripcion', 'LIKE', '%' . $search . '%')
                        ->orWhere('pacientes.identificacion', 'LIKE', '%' . $search . '%')
                        ->orWhere('pacientes.primer_nombre', 'LIKE', '%' . $search . '%')
                        ->orWhere('pacientes.primer_apellido', 'LIKE', '%' . $search . '%');
                });
            }

            $ListPaquetes = $paquetes->paginate($perPage, ['*'], 'page', $page);

            $tdTable = '';
            $x = ($page - 1) * $perPage + 1;
            $const = 1;
            foreach ($ListPaquetes as $i => $item) {
                if (!is_null($item)) {
                    $pago_realizado = number_format($item->total, 2, ',', '.');
                    $tdTable .= '<tr>
                                    <td>
                                        <div style="cursor: pointer" onclick="verPago(' . $item->id . ');"
                                            class="bg-primary-light h-50 w-50 l-h-60 rounded text-center">
                                            <span class="fa fa-search fs-24"></span>
                                        </div>
                                    </td>
                                    <td>
                                        <h5 class="text-dark fw-600 hover-primary m-0">' . $item->identificacion_completa . ' - ' . $item->nombre_paciente . '</h5>
                                        <span class="text-fade d-block fs-14"><strong>Paquete: </strong> ' . $item->descripcion . '</span>
                                    </td>                               
                                    <td>
                                        <h3>$ ' . $pago_realizado . '</h3>
                                    </td>
                                </tr>';
                    $x++;
                    $const++;
                }
            }
            $pagination = $ListPaquetes->links('Recaudos.PaginacionRecaudosPagos')->render();

            return response()->json([
                'paquetesVentas' => $tdTable,
                'links' => $pagination,
            ]);
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function Usuarios()
    {
        if (Auth::check()) {
            $bandera = "";
            return view('Usuario.gestionarUsuarios', compact('bandera'));
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }
    public function Perfiles()
    {
        if (Auth::check()) {
            $bandera = "";
            return view('Usuario.gestionarPerfiles', compact('bandera'));
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }
    public function Logs()
    {
        if (Auth::check()) {
            $bandera = "";
            return view('Usuario.gestionarLogs', compact('bandera'));
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }


    public function  guardarEspecialidad(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'estado' => 'error',
                'mensaje' => 'Su sesión ha terminado.',
            ], 401); // Código de error 401: No autorizado
        }

        // Capturar los datos del request
        $data = $request->all();
        // Guardar la información del paciente
        $respuesta = Especialidades::guardar($data);

        // Verificar el resultado y preparar la respuesta
        if ($respuesta) {
            $estado = true;
        } else {
            $estado = false;
        }

        // Retornar la respuesta en formato JSON
        return response()->json([
            'success' => $estado,
            'id' => $respuesta,
            'message' => 'Datos guardados'
        ]);
    }
    public function  guardarPagoVenta(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'estado' => 'error',
                'mensaje' => 'Su sesión ha terminado.',
            ], 401); // Código de error 401: No autorizado
        }

        // Capturar los datos del request
        $data = $request->all();
        $respuesta = Paquetes::GuardarPagoPaquete($data);

        // Verificar el resultado y preparar la respuesta
        if ($respuesta) {
            $estado = true;
        } else {
            $estado = false;
        }

        // Retornar la respuesta en formato JSON
        return response()->json([
            'success' => $estado,
            'idPago' => $respuesta,
            'message' => 'Datos guardados'
        ]);
    }


    public function  guardarEntidades(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'estado' => 'error',
                'mensaje' => 'Su sesión ha terminado.',
            ], 401); // Código de error 401: No autorizado
        }

        // Capturar los datos del request
        $data = $request->all();
        // Guardar la información del paciente
        $respuesta = Entidades::guardar($data);

        // Verificar el resultado y preparar la respuesta
        if ($respuesta) {
            $estado = true;
        } else {
            $estado = false;
        }

        // Retornar la respuesta en formato JSON
        return response()->json([
            'success' => $estado,
            'id' => $respuesta,
            'message' => 'Datos guardados'
        ]);
    }
    public function  guardarPaquete(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'estado' => 'error',
                'mensaje' => 'Su sesión ha terminado.',
            ], 401); // Código de error 401: No autorizado
        }

        // Capturar los datos del request
        $data = $request->all();
        // Guardar la información del paciente
        $respuesta = Paquetes::guardar($data);

        // Verificar el resultado y preparar la respuesta
        if ($respuesta) {
            $estado = true;
        } else {
            $estado = false;
        }

        // Retornar la respuesta en formato JSON
        return response()->json([
            'success' => $estado,
            'id' => $respuesta,
            'message' => 'Datos guardados'
        ]);
    }
    public function  guardarPrueba(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'estado' => 'error',
                'mensaje' => 'Su sesión ha terminado.',
            ], 401); // Código de error 401: No autorizado
        }

        // Capturar los datos del request
        $data = $request->all();
        // Guardar la información del paciente
        $respuesta = Pruebas::guardar($data);

        // Verificar el resultado y preparar la respuesta
        if ($respuesta) {
            $estado = true;
        } else {
            $estado = false;
        }

        // Retornar la respuesta en formato JSON
        return response()->json([
            'success' => $estado,
            'id' => $respuesta,
            'message' => 'Datos guardados'
        ]);
    }
    public function  guardarSesion(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'estado' => 'error',
                'mensaje' => 'Su sesión ha terminado.',
            ], 401); // Código de error 401: No autorizado
        }

        // Capturar los datos del request
        $data = $request->all();
        // Guardar la información del paciente
        $respuesta = Sesiones::guardar($data);

        // Verificar el resultado y preparar la respuesta
        if ($respuesta) {
            $estado = true;
        } else {
            $estado = false;
        }

        // Retornar la respuesta en formato JSON
        return response()->json([
            'success' => $estado,
            'id' => $respuesta,
            'message' => 'Datos guardados'
        ]);
    }

    public function  guardarGastos(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'estado' => 'error',
                'mensaje' => 'Su sesión ha terminado.',
            ], 401); // Código de error 401: No autorizado
        }

        // Capturar los datos del request
        $data = $request->all();
        // Guardar la información del paciente
        $respuesta = Gastos::guardar($data);

        // Verificar el resultado y preparar la respuesta
        if ($respuesta) {
            $estado = true;
        } else {
            $estado = false;
        }

        // Retornar la respuesta en formato JSON
        return response()->json([
            'success' => $estado,
            'id' => $respuesta,
            'message' => 'Datos guardados'
        ]);
    }

    public function  guardarCaja(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'estado' => 'error',
                'mensaje' => 'Su sesión ha terminado.',
            ], 401); // Código de error 401: No autorizado
        }

        // Capturar los datos del request
        $data = $request->all();
        // Guardar la información del paciente
        $respuesta = Gastos::guardarCaja($data);

        // Verificar el resultado y preparar la respuesta
        if ($respuesta) {
            $estado = true;
        } else {
            $estado = false;
        }

        // Retornar la respuesta en formato JSON
        return response()->json([
            'success' => $estado,
            'id' => $respuesta,
            'message' => 'Datos guardados'
        ]);
    }

    public function detalleCaja()
    {
        if (Auth::check()) {

            $idCaja = request()->get('idCaja');
            $caja = Gastos::BuscarCajas($idCaja);

            //recaudo
            $recaudos = Gastos::recaudosCajaResumen($caja->fecha_apertura);

            //gastos
            $gastos = Gastos::GastosCajaDet($caja->fecha_apertura);



            return response()->json([
                'caja' => $caja,
                'recaudos' => $recaudos,
                'gastos' => $gastos
            ]);
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function  guardarCategoria(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'estado' => 'error',
                'mensaje' => 'Su sesión ha terminado.',
            ], 401); // Código de error 401: No autorizado
        }

        // Capturar los datos del request
        $data = $request->all();
        // Guardar la información del paciente
        $respuesta = Gastos::guardarCat($data);

        // Verificar el resultado y preparar la respuesta
        if ($respuesta) {
            $estado = true;
        } else {
            $estado = false;
        }

        // Retornar la respuesta en formato JSON
        return response()->json([
            'success' => $estado,
            'id' => $respuesta,
            'message' => 'Datos guardados'
        ]);
    }

    public function  guardarProfesional(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'estado' => 'error',
                'mensaje' => 'Su sesión ha terminado.',
            ], 401); // Código de error 401: No autorizado
        }

        // Capturar los datos del request
        $data = $request->all();

        if (isset($data['firmaProf'])) {
            $archivo = $data['firmaProf'];
            $nombreOriginal = $archivo->getClientOriginalName();

            // Generar un nombre único para el archivo
            $prefijo = substr(md5(uniqid(rand())), 0, 6);
            $nombreArchivo = self::sanear_string($prefijo . '_' . $nombreOriginal);

            // Guardar el archivo en la ruta especificada
            $archivo->move(public_path() . '/app-assets/images/firmasProfesionales/', $nombreArchivo);
            $data['firma'] = $nombreArchivo;
        } else {
            if ($data['accRegistro'] == 'guardar') {
                $data['firma'] = "sinFima.jpg";
            } else {
                $data['firma'] = $data['firmaOriginal'];
            }
        }

        // Guardar la información del paciente
        $respuesta = Profesional::guardar($data);

        // Verificar el resultado y preparar la respuesta
        if ($respuesta) {
            $estado = true;
        } else {
            $estado = false;
        }

        // Retornar la respuesta en formato JSON
        return response()->json([
            'success' => $estado,
            'id' => $respuesta,
            'message' => 'Datos guardados'
        ]);
    }

    public function listaEspecialidades(Request $request)
    {
        if (Auth::check()) {
            $perPage = 10; // Número de posts por página
            $page = request()->get('page', 1);
            $search = request()->get('search');
            if (!is_numeric($page)) {
                $page = 1; // Establecer un valor predeterminado si no es numérico
            }

            $especialidades = DB::connection('mysql')
                ->table('especialidades')
                ->where('estado', 'ACTIVO')
                ->select(
                    'nombre',
                    'precio',
                    'id'
                );

            if ($search) {
                $especialidades->where(function ($query) use ($search) {
                    $query->where('nombre', 'LIKE', '%' . $search . '%');
                });
            }

            $ListEspecialidades = $especialidades->paginate($perPage, ['*'], 'page', $page);

            $tdTable = '';
            $x = ($page - 1) * $perPage + 1;
            $const = 1;
            foreach ($ListEspecialidades as $i => $item) {
                if (!is_null($item)) {
                    $valor = number_format($item->precio, 2, ',', '.');
                    $tdTable .= '<tr>
                                    <td>' . $const . '</td>
                                    <td>' . $item->nombre . '</td>
                                    <td>$' .  $valor . '</td>
                                    <td class="table-action min-w-100">
                                        <a onclick="editarRegistro(' . $item->id . ');" style="cursor: pointer;" title="Editar" class="text-fade hover-primary"><i class="align-middle"
                                                data-feather="edit-2"></i></a>
                                        <a onclick="eliminarRegistro(' . $item->id . ');" style="cursor: pointer;" title="Eliminar" class="text-fade hover-warning"><i class="align-middle"
                                                data-feather="trash"></i></a>
                                    </td>
                                </tr>';
                    $x++;
                    $const++;
                }
            }
            $pagination = $ListEspecialidades->links('Adminitraccion.Paginacion')->render();

            return response()->json([
                'especialidades' => $tdTable,
                'links' => $pagination
            ]);
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function listaProfesionales(Request $request)
    {
        if (Auth::check()) {
            $perPage = 10; // Número de posts por página
            $page = request()->get('page', 1);
            $search = request()->get('search');
            if (!is_numeric($page)) {
                $page = 1; // Establecer un valor predeterminado si no es numérico
            }

            $profesionales = DB::connection('mysql')
                ->table('profesionales')
                ->where('estado', 'ACTIVO')
                ->select('identificacion', 'nombre', 'correo', 'id');

            if ($search) {
                $profesionales->where(function ($query) use ($search) {
                    $query->where('nombre', 'LIKE', '%' . $search . '%');
                });
            }

            $ListProfesionales = $profesionales->paginate($perPage, ['*'], 'page', $page);

            $tdTable = '';
            $x = ($page - 1) * $perPage + 1;
            $const = 1;
            foreach ($ListProfesionales as $i => $item) {
                if (!is_null($item)) {
                    $tdTable .= '<tr>
                                    <td>' . $const . '</td>
                                    <td>' . $item->identificacion . '</td>
                                    <td>' . $item->nombre . '</td>
                                    <td>' . $item->correo . '</td>
                                    <td class="table-action min-w-100">
                                        <a onclick="editarRegistro(' . $item->id . ');" style="cursor: pointer;" title="Editar" class="text-fade hover-primary"><i class="align-middle"
                                                data-feather="edit-2"></i></a>
                                        <a onclick="eliminarRegistro(' . $item->id . ');" style="cursor: pointer;" title="Eliminar" class="text-fade hover-warning"><i class="align-middle"
                                                data-feather="trash"></i></a>
                                    </td>
                                </tr>';
                    $x++;
                    $const++;
                }
            }

            $pagination = $ListProfesionales->links('Adminitraccion.Paginacion')->render();

            return response()->json([
                'profesionales' => $tdTable,
                'links' => $pagination
            ]);
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function cargarListaProf()
    {
        $profesionales = DB::connection('mysql')
            ->table('profesionales')
            ->where('estado', 'ACTIVO')
            ->get();
        return response()->json($profesionales);
    }
    public function cargarListaEsp()
    {
        $especialidades = DB::connection('mysql')
            ->table('especialidades')
            ->where('estado', 'ACTIVO')
            ->get();
        return response()->json($especialidades);
    }


    public function verificarIdentProfesional(Request $request)
    {
        $identificacion = $request->input('identificacion');
        // Verificar si el usuario ya está registrado
        $profesionalExistente = DB::table('profesionales')
            ->where('identificacion', $identificacion)
            ->exists();

        return response()->json(!$profesionalExistente);
    }
    public function verificarCodigoEntidad(Request $request)
    {
        $codigo = $request->input('codigo');
        $idEPS = $request->input('id');

        $pacienteExistente = DB::table('eps')
            ->where('codigo', $codigo)
            ->when($idEPS, function ($query) use ($idEPS) {

                $query->where('id', '!=', $idEPS);
            })
            ->exists();

        return response()->json(!$pacienteExistente); // Devuelve true si NO existe duplicado, false si ya está registrado

    }

    public function busquedaEspecialidad(Request $request)
    {
        $idRegistro = $request->input('idRegistro');
        $especialidad = Especialidades::busquedaEspecialidad($idRegistro);
        return response()->json($especialidad);
    }
    public function buscaEntidad(Request $request)
    {
        $idRegistro = $request->input('idRegistro');
        $entidad = Entidades::busquedaEntidad($idRegistro);
        return response()->json($entidad);
    }

    public function detalleVentaServicioPaciente(Request $request)
    {
        $idVenta = $request->input('idVenta');
        $PaqueteVenta = Paquetes::busquedaPaquetesVentas($idVenta);

        $historialpagos = DB::connection('mysql')
            ->table('pagos')
            ->leftJoin('medio_pagos', 'pagos.id', 'medio_pagos.id_pago')
            ->leftJoin("users", "pagos.usuario", "users.id")
            ->where('pagos.id_servicio', $idVenta)
            ->where('pagos.estado', 'ACTIVO')
            ->select(
                'pagos.id',
                'pagos.pago_realizado',
                'pagos.fecha_pago',
                'medio_pagos.id as idMedioPago',
                'pagos.abono',
                DB::raw("CASE
                WHEN medio_pagos.medio_pago = 'e' THEN 'Efectivo'
                WHEN medio_pagos.medio_pago = 't' THEN 'Transferencia'
                WHEN medio_pagos.medio_pago = 'tc' THEN 'Tarjeta de débito'
                ELSE 'Tarjeta de crédito' END as nombreMedioPago"),
                'medio_pagos.referencia',
                'users.nombre_usuario'
            )
            ->get();

        $totalAbonos = $historialpagos->sum('abono');

        return response()->json([
            'PaqueteVenta' => $PaqueteVenta,
            'totalAbonos' => $totalAbonos,
            'historialpagos' => $historialpagos
        ]);

        return response()->json($PaqueteVenta);
    }
    public function detalleVentaPagosPaciente(Request $request)
    {
        $idVenta = $request->input('idVenta');
        $PaqueteVenta = Paquetes::busquedaPaquetesVentas($idVenta);
        $historialpagos = DB::connection('mysql')
            ->table('pagos')
            ->leftJoin('users', 'pagos.usuario', 'users.id')
            ->where('id_servicio', $idVenta)
            ->where('pagos.estado', 'ACTIVO')
            ->select(
                'pagos.id',
                'pagos.pago_realizado',
                'pagos.fecha_pago',
                'users.nombre_usuario'
            )
            ->get();

        return response()->json([
            'PaqueteVenta' => $PaqueteVenta,
            'historialpagos' => $historialpagos
        ]);

        return response()->json($PaqueteVenta);
    }

    public function buscarPaquete(Request $request)
    {
        $idRegistro = $request->input('idRegistro');
        $paquete = Paquetes::busquedaPaquetes($idRegistro);
        return response()->json($paquete);
    }
    public function buscarPrueba(Request $request)
    {
        $idRegistro = $request->input('idRegistro');
        $prueba = Pruebas::busquedaPruebas($idRegistro);
        return response()->json($prueba);
    }

    public function buscarSesion(Request $request)
    {
        $idRegistro = $request->input('idRegistro');
        $sesion = Sesiones::busquedaSesiones($idRegistro);
        return response()->json($sesion);
    }

    public function buscarGasto(Request $request)
    {
        $idGasto = $request->input('idGasto');
        $gasto = Gastos::busquedaGasto($idGasto);
        return response()->json($gasto);
    }

    public function busquedaProfesional(Request $request)
    {
        $idRegistro = $request->input('idRegistro');
        $profesional = Profesional::busquedaProfesional($idRegistro);
        return response()->json($profesional);
    }

    public function eliminarEspecialidad()
    {
        try {
            $idReg = request()->input('idReg');
            if (!$idReg) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'ID de la especialidad no proporcionado'
                    ],
                    400
                );
            }

            $paciente = DB::connection('mysql')
                ->table('especialidades')
                ->where('id', $idReg)
                ->update([
                    'estado' => 'ELIMINADO',
                ]);

            if ($paciente) {
                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Especialidad eliminada correctamente'
                    ]
                );
            } else {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'No se encontró la especialidad o no se pudo eliminar'
                    ],
                    404
                );
            }
        } catch (\Exception $e) {
            // Manejar cualquier error o excepción
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Ocurrió un error al intentar eliminar la especialidad',
                    'error' => $e->getMessage()
                ],
                500
            );
        }
    }
    public function eliminarEntidad()
    {
        try {
            $idReg = request()->input('idReg');
            if (!$idReg) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'ID de la entidad no proporcionada'
                    ],
                    400
                );
            }


            $paciente = DB::connection('mysql')
                ->table('eps')
                ->where('id', $idReg)
                ->update([
                    'estado' => 'ELIMINADO',
                ]);

            if ($paciente) {
                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Entidad eliminada correctamente'
                    ]
                );
            } else {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'No se encontró la entidad o no se pudo eliminar'
                    ],
                    404
                );
            }
        } catch (\Exception $e) {
            // Manejar cualquier error o excepción
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Ocurrió un error al intentar eliminar la entidad',
                    'error' => $e->getMessage()
                ],
                500
            );
        }
    }

    public function eliminarPaquete()
    {
        try {
            $idReg = request()->input('idReg');
            if (!$idReg) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'ID del paquete no proporcionado'
                    ],
                    400
                );
            }


            $paciente = DB::connection('mysql')
                ->table('paquetes')
                ->where('id', $idReg)
                ->update([
                    'estado' => 'ELIMINADO',
                ]);

            if ($paciente) {
                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Paquete eliminado correctamente'
                    ]
                );
            } else {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'No se encontró el paquete o no se pudo eliminar'
                    ],
                    404
                );
            }
        } catch (\Exception $e) {
            // Manejar cualquier error o excepción
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Ocurrió un error al intentar eliminar el paquete',
                    'error' => $e->getMessage()
                ],
                500
            );
        }
    }

    public function eliminarPrueba(){
        try {
            $idReg = request()->input('idReg');
            if (!$idReg) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'ID de la prueba no proporcionado'
                    ],
                    400
                );
            }

            
            $paciente = DB::connection('mysql')
                ->table('pruebas')
                ->where('id', $idReg)
                ->update([
                    'estado' => 'ELIMINADO',
                ]);

            if ($paciente) {
                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Pruea eliminada correctamente'
                    ]
                );
            } else {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'No se encontró la prueba o no se pudo eliminar'
                    ],
                    404
                );
            }
        } catch (\Exception $e) {
            // Manejar cualquier error o excepción
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Ocurrió un error al intentar eliminar la prueba',
                    'error' => $e->getMessage()
                ],
                500
            );
        }
    }

    public function eliminarSesion(){
        try {
            $idReg = request()->input('idReg');
            if (!$idReg) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'ID de la sesión no proporcionado'
                    ],
                    400
                );
            }


            $paciente = DB::connection('mysql')
                ->table('sesiones')
                ->where('id', $idReg)
                ->update([
                    'estado' => 'ELIMINADO',
                ]);

            if ($paciente) {
                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Sesión eliminada correctamente'
                    ]
                );
            } else {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'No se encontró la sesión o no se pudo eliminar'
                    ],
                    404
                );
            }
        } catch (\Exception $e) {
            // Manejar cualquier error o excepción
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Ocurrió un error al intentar eliminar la sesión',
                    'error' => $e->getMessage()
                ],
                500
            );
        }
    }

    public function eliminarCategoria()
    {
        try {
            $idReg = request()->input('idCategoria');
            if (!$idReg) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'ID de la catetgoria no proporcionado'
                    ],
                    400
                );
            }

            $paciente = DB::connection('mysql')
                ->table('categorias_gastos')
                ->where('id', $idReg)
                ->update([
                    'estado' => 'ELIMINADO',
                ]);

            if ($paciente) {
                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Categoria eliminada correctamente'
                    ]
                );
            } else {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'No se encontró la categoria o no se pudo eliminar'
                    ],
                    404
                );
            }
        } catch (\Exception $e) {
            // Manejar cualquier error o excepción
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Ocurrió un error al intentar eliminar la categoria',
                    'error' => $e->getMessage()
                ],
                500
            );
        }
    }

    public function eliminarGasto()
    {
        try {
            $idReg = request()->input('idGastos');
            if (!$idReg) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'ID del gasto no proporcionado'
                    ],
                    400
                );
            }

            $paciente = DB::connection('mysql')
                ->table('gastos')
                ->where('id', $idReg)
                ->update([
                    'estado' => 'ELIMINADO',
                ]);

            if ($paciente) {
                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Gasto eliminado correctamente'
                    ]
                );
            } else {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'No se encontró el gasto o no se pudo eliminar'
                    ],
                    404
                );
            }
        } catch (\Exception $e) {
            // Manejar cualquier error o excepción
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Ocurrió un error al intentar eliminar el gasto',
                    'error' => $e->getMessage()
                ],
                500
            );
        }
    }
    public function eliminarCaja()
    {
        try {
            $idReg = request()->input('idCaja');
            if (!$idReg) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'ID de la caja no proporcionado'
                    ],
                    400
                );
            }

            $paciente = DB::connection('mysql')
                ->table('cajas')
                ->where('id', $idReg)
                ->update([
                    'estado_reg' => 'ELIMINADO',
                ]);

            if ($paciente) {
                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Caja eliminada correctamente'
                    ]
                );
            } else {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'No se encontró la caja o no se pudo eliminar'
                    ],
                    404
                );
            }
        } catch (\Exception $e) {
            // Manejar cualquier error o excepción
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Ocurrió un error al intentar eliminar la caja',
                    'error' => $e->getMessage()
                ],
                500
            );
        }
    }

    public function eliminarProfesional()
    {
        try {
            $idReg = request()->input('idReg');
            if (!$idReg) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'ID del profesional no proporcionado'
                    ],
                    400
                );
            }


            $profesional = Profesional::busquedaProfesional($idReg);

            $usuario = DB::connection('mysql')
                ->table('users')
                ->where('id', $profesional->idUsuario)
                ->update([
                    'estado' => 'ELIMINADO',
                ]);

            $paciente = DB::connection('mysql')
                ->table('profesionales')
                ->where('id', $idReg)
                ->update([
                    'estado' => 'ELIMINADO',
                ]);


            if ($paciente) {
                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Profesional eliminado correctamente'
                    ]
                );
            } else {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'No se encontró el profesional o no se pudo eliminar'
                    ],
                    404
                );
            }
        } catch (\Exception $e) {
            // Manejar cualquier error o excepción
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Ocurrió un error al intentar eliminar el profesional',
                    'error' => $e->getMessage()
                ],
                500
            );
        }
    }

    public function sanear_string($string)
    {

        $string = trim($string);

        $string = str_replace(
            array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
            array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
            $string
        );

        $string = str_replace(
            array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
            array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
            $string
        );

        $string = str_replace(
            array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
            array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
            $string
        );

        $string = str_replace(
            array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
            array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
            $string
        );

        $string = str_replace(
            array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
            array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
            $string
        );

        $string = str_replace(
            array('ñ', 'Ñ', 'ç', 'Ç'),
            array('n', 'N', 'c', 'C'),
            $string
        );

        //Esta parte se encarga de eliminar cualquier caracter extraño
        $string = str_replace(
            array(
                "¨",
                "º",
                "-",
                "~",
                "",
                "@",
                "|",
                "!",
                "·",
                "$",
                "%",
                "&",
                "/",
                "(",
                ")",
                "?",
                "'",
                " h¡",
                "¿",
                "[",
                "^",
                "<code>",
                "]",
                "+",
                "}",
                "{",
                "¨",
                "´",
                ">",
                "< ",
                ";",
                ",",
                ":",
                " ",
            ),
            '',
            $string
        );

        return $string;
    }
}
