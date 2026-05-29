<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\HistoriaPsicologica;
use App\Models\Paquetes;
use App\Models\Gastos;
use Dompdf\Dompdf;
use \PDF;
use App\Models\Pacientes;
class AdminitraccionController extends Controller
{
    public function consultarMontoCierre(Request $request)
    {
        $fechaCierre = $request->input('fechaCierre');
        $idCaja = $request->input('idCaja');

        $caja = Gastos::BuscarCajas($idCaja);



        $recaudos = Gastos::recaudosCajaResumen($caja->fecha_apertura, $fechaCierre);

        //gastos
        $gastos = Gastos::GastosCajaDet($caja->fecha_apertura, $fechaCierre);

        $monto = $recaudos->sum('valor') - $gastos->sum('valor');

        return response()->json(
            [
                'monto' => $monto,
                'recaudos' => $recaudos->sum('valor'),
                'gastos' => $gastos->sum('valor')
            ]
        );
    }

    public function Backup()
    {
        if (Auth::check()) {
            $bandera = "";
            return view('Adminitraccion.gestionarBackup', compact('bandera'));
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function listaBackup(Request $request)
    {
        if (Auth::check()) {
            $perPage = 10; // Número de posts por página
            $page = request()->get('page', 1);
            $search = request()->get('search');
            if (!is_numeric($page)) {
                $page = 1; // Establecer un valor predeterminado si no es numérico
            }

            $backups = DB::connection('mysql')
                ->table('respaldo_formularios')
                ->leftJoin('users', 'users.id', 'respaldo_formularios.user_id')
                ->select(
                    'respaldo_formularios.id',
                    'respaldo_formularios.datos',
                    'respaldo_formularios.created_at',
                    'respaldo_formularios.formulario',
                    'users.nombre_usuario',
                    'respaldo_formularios.paciente'
                )
                ->where(function ($query) {
                    $query->where('respaldo_formularios.datos', '!=', '[]'); // Excluir el arreglo vacío
                })
                ->orderBy('respaldo_formularios.created_at', 'desc');

            if ($search) {
                $backups->where(function ($query) use ($search) {
                    $query->where('users.nombre_usuario', 'LIKE', '%' . $search . '%');
                    $query->orWhere('respaldo_formularios.formulario', 'LIKE', '%' . $search . '%');
                    $query->orWhere('respaldo_formularios.paciente', 'LIKE', '%' . $search . '%');
                });
            }


            $ListBackups = $backups->paginate($perPage, ['*'], 'page', $page);

            $tdTable = '';
            $x = ($page - 1) * $perPage + 1;

            foreach ($ListBackups as $i => $item) {
                if (!is_null($item)) {
                    // Obtener el paciente de datos
                    $tdTable .= '<tr>
                                    <td style="text-transform: capitalize;">' . $item->paciente . '</td>                                   
                                    <td>' . $item->nombre_usuario . '</td>                                   
                                    <td>' . $item->formulario . '</td>                                   
                                    <td>' . date('d/m/Y H:i:s', strtotime($item->created_at)) . '</td>                                   
                                    <td><a href="javascript:void(0)" onclick="verDetalleBackup(' . $item->id . ');" class="btn btn-primary"><i class="align-middle" data-feather="eye"></i></a></td>                                   
                                </tr>';
                    $x++;
                }
            }

            $pagination = $ListBackups->links('Adminitraccion.Paginacion')->render();

            return response()->json([
                'backups' => $tdTable,
                'links' => $pagination,
            ]);
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function verDetalleBackup(Request $request)
    {
        $id = $request->input('id');
        $backup = DB::connection('mysql')
            ->table('respaldo_formularios')
            ->leftJoin('users', 'users.id', 'respaldo_formularios.user_id')
            ->select(
                'respaldo_formularios.id',
                'respaldo_formularios.datos',
                'respaldo_formularios.created_at',
                'users.nombre_usuario'
            )
            ->where('respaldo_formularios.id', $id)
            ->first();

        return response()->json($backup);
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

            if($recaudo->tipo_servicio == 'EPS'){
                $paciente = DB::connection('mysql')->table('eps')
                    ->select(
                    DB::raw("CONCAT(codigo, ' - ', entidad) as nombre_cliente"),
                    'nit',
                    'email',
                    'telefono'
                        )
                    ->where('id', $recaudo->id_paciente)
                    ->first();
            }else{
                $paciente = Pacientes::busquedaPaciente($recaudo->id_paciente);
            }
            $ncompro = self::addCeros($recaudo->id, 5);

            $html = '<html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; font-size: 12px; }
                    body::before {
                        content: "";
                        position: fixed;
                        top: 50%;
                        left: 50%;
                        transform: translate(-50%, -50%);
                        width: 400px;
                        height: 400px;
                        background-image: url(' . $logo . ');
                        background-size: contain;
                        background-repeat: no-repeat;
                        background-position: center;
                        opacity: 0.2;
                        z-index: -1;
                        pointer-events: none;
                    }
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
                    <p>PSICÓLOGA CLÍNICA - T.P. No. 259542</p>
                    <p>Calle 11 # 11 - 07 San Joaquin</p>
                    <p>Teléfono: 312 5678078</p>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="no-border"><h3>RECIBO DE CAJA - ' . $ncompro . '</h3></td>
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

            if($recaudo->tipo_servicio == 'EPS'){
                $html .= '<div class="section">
                <table>
                    <tr>
                        <td colspan="3"><strong>NOMBRE EPS:</strong> ' . $paciente->nombre_cliente . '</td>
                    </tr>
                    <tr>
                        <td colspan="3"><strong>NIT:</strong> ' . $paciente->nit . '</td>
                    </tr>
                    <tr>
                        <td colspan="3"><strong>EMAIL:</strong> ' . $paciente->email . '</td>
                    </tr>
                    <tr>
                        <td colspan="3"><strong>TELÉFONO:</strong> ' . $paciente->telefono . '</td>
                    </tr>
                </table>
            </div>';
            }else{
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
                    <td><strong>SEXO:</strong> ' . (($paciente->sexo === "M") ? "Femenino" : "Masculino") . '</td>
                </tr>
                <tr>
                    <td><strong>DIRECCIÓN:</strong> ' . $paciente->direccion . '</td>
                    <td colspan="2"><strong>CELULAR:</strong> ' . $paciente->telefono . '</td>
                </tr>
            </table>
        </div>';

            }

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

            $fechaInicio = \Carbon\Carbon::createFromFormat('d/m/Y', $fecha1)->format('Y-m-d');
            $fechaFin = \Carbon\Carbon::createFromFormat('d/m/Y', $fecha2)->format('Y-m-d');

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
                    'gastos.origen_recurso',
                    'categorias_gastos.descripcion AS categoria'
                );



            if ($search) {
                $gastos->where(function ($query) use ($search) {
                    $query->where('gastos.descripcion', 'LIKE', '%' . $search . '%')
                        ->orWhere('categorias_gastos.descripcion', 'LIKE', '%' . $search . '%')
                        ->orWhere('gastos.origen_recurso', 'LIKE', '%' . $search . '%');
                });
            }

            if (!empty($fechaInicio)) {
                $gastos->whereBetween('gastos.fecha_gasto',  [$fechaInicio, $fechaFin]);
            }

            // Clonar la consulta para los cálculos de totales
            $gastosCalculo = clone $gastos;
            $gastosCaja = clone $gastos;
            $gastosOtro = clone $gastos;

            $ListGastos = $gastos->paginate($perPage, ['*'], 'page', $page);

            // Calcular totales por origen de recurso
            $gastosTotales = $gastosCalculo->sum('gastos.valor');
            $gastosDeCaja = $gastosCaja->where('gastos.origen_recurso', 'Caja')->sum('gastos.valor');
            $gastosDeOtro = $gastosOtro->where('gastos.origen_recurso', 'Otro')->sum('gastos.valor');

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
                                    <td>' . $item->origen_recurso . '</td>
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
                'gastosTotales' => number_format($gastosTotales, 2, ',', '.'),
                'gastosDeCaja' => number_format($gastosDeCaja, 2, ',', '.'),
                'gastosDeOtro' => number_format($gastosDeOtro, 2, ',', '.')
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
            $saldoInicial = 0;
            if ($ultimaCaja) {
                $saldoAnterior = $ultimaCaja->saldo_cierre;
                $saldoInicial = $ultimaCaja->saldo_inicial;
            }

            //si no hay ultima caja 

            //consultar los recaudos de la ultima caja 
            if ($ultimaCaja) {
                $recaudos = Gastos::recaudosCajaResumen($ultimaCaja->fecha_apertura, $ultimaCaja->fecha_cierre);
                $gastosUltimaCaja = Gastos::GastosCajaCierre($ultimaCaja->fecha_apertura,$ultimaCaja->fecha_cierre);
                //filtrar reacudos por tipo de pago
                $recaudosEfectivo = $recaudos->where('medio_pago', 'e')->sum('valor');
                $recaudosTarjetac = $recaudos->where('medio_pago', 'tc')->sum('valor');
                $recaudosTarjetad = $recaudos->where('medio_pago', 'td')->sum('valor');
                $recaudosTransferencia = $recaudos->where('medio_pago', 't')->sum('valor');
            } else {
                $recaudos = [];
                $recaudosEfectivo = 0;
                $recaudosTarjetac = 0;
                $recaudosTarjetad = 0;
                $recaudosTransferencia = 0;
                $gastosUltimaCaja = 0;
            }

            //consultar los gastos de la ultima caja 

            $ListCajas = $cajas->paginate($perPage, ['*'], 'page', $page);

            $tdTable = '';
            $j = 1;
            $x = ($page - 1) * $perPage + 1;

            foreach ($ListCajas as $i => $item) {
                if (!is_null($item)) {
                    $saldo_inicial = $item->saldo_inicial + $item->abono_inicial;

                    $fechaApertura = $item->fecha_apertura;
                    if ($item->estado_caja == "Abierta") {
                        $saldo_acomulado = Pacientes::recaudoCaja($fechaApertura);
                        $gastos = Gastos::GastosCaja($fechaApertura);
                    } else {
                        $saldo_acomulado = $item->saldo_cierre;
                        $gastos = $item->gastos;
                    }

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
                'recaudosEfectivo' => $recaudosEfectivo + $saldoInicial - $gastosUltimaCaja,
                'recaudosTarjetac' => $recaudosTarjetac,
                'recaudosTarjetad' => $recaudosTarjetad,
                'recaudosTransferencia' => $recaudosTransferencia

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
                ->where('servicios.tipo_servicio','!=','EPS')
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
                ->orderBy('servicios.id', 'desc')
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
                            (SELECT descripcion FROM asesorias WHERE asesorias.id = servicios.id_tipo_servicio AND servicios.tipo = 'ASESORIA' LIMIT 1),
                            'Sin descripción'
                        ) AS descripcion
                ")
                );

            if ($search) {
                $servicios->where(function ($query) use ($search) {
                    // Búsqueda por identificación
                    $query->where('pacientes.identificacion', 'LIKE', '%' . $search . '%');
                    
                    // Búsqueda individual por campos
                    $query->orWhere('pacientes.primer_nombre', 'LIKE', '%' . $search . '%')
                        ->orWhere('pacientes.segundo_nombre', 'LIKE', '%' . $search . '%')
                        ->orWhere('pacientes.primer_apellido', 'LIKE', '%' . $search . '%')
                        ->orWhere('pacientes.segundo_apellido', 'LIKE', '%' . $search . '%');
                    
                    // Búsqueda en nombre completo (exacta)
                    $query->orWhere(DB::raw("CONCAT(pacientes.primer_nombre,' ',pacientes.segundo_nombre,' ',pacientes.primer_apellido,' ',pacientes.segundo_apellido)"), 'LIKE', '%' . $search . '%');
                    
                    // Búsqueda por palabras en desorden: divide el término de búsqueda en palabras
                    // y verifica que todas estén presentes en el nombre completo
                    $palabras = array_filter(explode(' ', trim($search)));
                    if (count($palabras) > 1) {
                        $query->orWhere(function ($subQuery) use ($palabras) {
                            $nombreCompleto = DB::raw("CONCAT(pacientes.primer_nombre,' ',pacientes.segundo_nombre,' ',pacientes.primer_apellido,' ',pacientes.segundo_apellido)");
                            foreach ($palabras as $palabra) {
                                $subQuery->where($nombreCompleto, 'LIKE', '%' . trim($palabra) . '%');
                            }
                        });
                    }
                    
                    // Búsqueda por tipo de servicio
                    $query->orWhere('servicios.tipo', 'LIKE', '%' . $search . '%');
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

                    $tdTable .= '<tr onclick="realizarPago(' . $item->id . ');" class="hover-opagos">
                                    <td style="width: 40%;">
                                        <h6 class="text-dark fw-600 hover-primary m-0">' . $item->identificacion_completa . ' - ' . $item->nombre_paciente . '</h6>
                                        <span class="text-fade d-block fs-14">
                                        <strong>Servicio: </strong> ' . $item->tipo . '</span>
                                        <span class="text-fade d-block fs-14">
                                        <strong>Descripción: </strong> ' . $item->descripcion . '</span>
                                    </td>
                                    <td style="width: 20%;">
                                        <h5>' . $fecha . '</h5>
                                    </td>
                                    <td style="width: 20%;">
                                    <div class="text-center"><h5>$' . $valor . '</h5></div>
                                        
                                    </td>
                                    <td style="width: 20%;">
                                        <h5>$ ' . $saldo . '</h5>
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

    public function listaVentasEps(Request $request)
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
                ->leftJoin('eps', 'servicios.id_paciente', 'eps.id')
                ->leftJoin('asesorias', 'servicios.id_tipo_servicio', 'asesorias.id')
                ->where('servicios.estado', 'ACTIVO')
                ->where('ventas.estado_venta', 'PENDIENTE')
                ->where('servicios.tipo_servicio', 'EPS')
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
                    'eps.entidad',
                    'asesorias.descripcion'
                ])
                ->orderBy('servicios.id', 'desc')
                ->select(
                    'servicios.id_tipo_servicio',
                    'eps.entidad',
                    'servicios.tipo',
                    'servicios.fecha',
                    'servicios.precio',
                    'servicios.id',
                    'ventas.cantidad',
                    'ventas.estado_venta',
                    'ventas.saldo',
                    'asesorias.descripcion'
                );

            if ($search) {
                $servicios->where(function ($query) use ($search) {
                    // Búsqueda individual por campos
                    $query->where('eps.entidad', 'LIKE', '%' . $search . '%')
                        ->orWhere('asesorias.descripcion', 'LIKE', '%' . $search . '%');
                    
                    // Búsqueda por palabras en desorden: divide el término de búsqueda en palabras
                    // y verifica que todas estén presentes en los campos
                    $palabras = array_filter(explode(' ', trim($search)));
                    if (count($palabras) > 1) {
                        // Búsqueda en eps.entidad con palabras en desorden
                        $query->orWhere(function ($subQuery) use ($palabras) {
                            foreach ($palabras as $palabra) {
                                $subQuery->where('eps.entidad', 'LIKE', '%' . trim($palabra) . '%');
                            }
                        });
                        
                        // Búsqueda en asesorias.descripcion con palabras en desorden
                        $query->orWhere(function ($subQuery) use ($palabras) {
                            foreach ($palabras as $palabra) {
                                $subQuery->where('asesorias.descripcion', 'LIKE', '%' . trim($palabra) . '%');
                            }
                        });
                    }
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

                    $tdTable .= '<tr onclick="realizarPago(' . $item->id . ');" class="hover-opagos">
                                    <td style="width: 40%;">
                                        <h6 class="text-dark fw-600 hover-primary m-0">' . $item->entidad . '</h6>
                                        <span class="text-fade d-block fs-14">
                                        <strong style="font-size: 12px;">Descripción del servicio: </strong> ' . $item->descripcion . '</span>
                                    </td>
                                    <td style="width: 20%;">
                                        <h5>' . $fecha . '</h5>
                                    </td>
                                    <td style="width: 20%;">
                                    <div class="text-center"><h5>$' . $valor . '</h5></div>
                                        
                                    </td>
                                    <td style="width: 20%;">
                                        <h5>$ ' . $saldo . '</h5>
                                    </td> 
                                </tr>';
                    $x++;
                    $const++;
                }
            }
            $pagination = $ListServicios->links('Recaudos.PaginacionRecaudosEps')->render();

            return response()->json([
                'paquetesVentas' => $tdTable,
                'links' => $pagination
            ]);
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function listaPagos(Request $request)
    {
        if (Auth::check()) {
            $tipo = $request->tipo;

            if ($tipo == 'pendientes' || $tipo == 'completados') {
                $pagos = DB::connection('mysql')
                    ->table('ventas')
                    ->leftJoin('servicios', 'ventas.id_servicio', 'servicios.id')
                    ->leftJoin('pacientes', 'servicios.id_paciente', 'pacientes.id')
                    ->leftJoin('eps', 'servicios.id_paciente', 'eps.id') // Asumiendo que se guarda en el mismo campo
                    ->where('servicios.estado', 'ACTIVO')
                    ->groupBy([
                        'ventas.id',
                        'ventas.total',
                        'ventas.estado_venta',
                        'pacientes.primer_nombre',
                        'pacientes.primer_apellido',
                        'eps.entidad',
                        'servicios.id_tipo_servicio',
                        'servicios.tipo',
                        'servicios.fecha',
                        'servicios.tipo_servicio',
                        'pacientes.tipo_identificacion',
                        'pacientes.identificacion',
                        'eps.codigo'
                    ])
                    ->select(
                        'ventas.id',
                        'ventas.total',
                        'ventas.estado_venta',
                        'servicios.fecha',
                        'servicios.tipo',
                        'servicios.tipo_servicio',
                        DB::raw("
            CASE 
                WHEN servicios.tipo_servicio = 'EPS' THEN CONCAT(eps.codigo, ' - ', eps.entidad)
                ELSE CONCAT(pacientes.tipo_identificacion, ' ', pacientes.identificacion, ' - ', pacientes.primer_nombre, ' ', pacientes.primer_apellido)
            END AS nombre_cliente
        "),
                        DB::raw("
            COALESCE(
                (SELECT nombre FROM especialidades WHERE especialidades.id = servicios.id_tipo_servicio AND servicios.tipo = 'CONSULTA' LIMIT 1),
                (SELECT descripcion FROM sesiones WHERE sesiones.id = servicios.id_tipo_servicio AND servicios.tipo = 'SESION' LIMIT 1),
                (SELECT descripcion FROM paquetes WHERE paquetes.id = servicios.id_tipo_servicio AND servicios.tipo = 'PAQUETE' LIMIT 1),
                (SELECT descripcion FROM pruebas WHERE pruebas.id = servicios.id_tipo_servicio AND servicios.tipo = 'PRUEBAS' LIMIT 1),
                (SELECT descripcion FROM asesorias WHERE asesorias.id = servicios.id_tipo_servicio AND servicios.tipo = 'ASESORIA' LIMIT 1),
                'Sin descripción'
            ) AS descripcion
        ")
                    );

                if ($tipo == 'pendientes') {
                    $pagos = $pagos->where('ventas.estado_venta', 'PENDIENTE');
                } else if ($tipo == 'completados') {
                    $pagos = $pagos->where('ventas.estado_venta', 'PAGADO');
                }

                $pagos = $pagos->orderBy('ventas.id', 'desc')->get();
            } else {

                $pagos = DB::connection('mysql')
                    ->table('pagos')
                    ->leftJoin('servicios', 'pagos.id_servicio', 'servicios.id')
                    ->leftJoin('pacientes', 'servicios.id_paciente', 'pacientes.id')
                    ->leftJoin('eps', 'servicios.id_paciente', 'eps.id')
                    ->leftJoin('medio_pagos', 'medio_pagos.id_pago', 'pagos.id')
                    ->leftJoin('ventas', 'ventas.id_servicio', 'servicios.id')
                    ->where('pagos.estado', 'ACTIVO')
                    ->groupBy([
                        'pagos.id',
                        'pagos.pago_realizado',
                        'pagos.fecha_pago',
                        'pacientes.primer_nombre',
                        'pacientes.primer_apellido',
                        'servicios.id_tipo_servicio',
                        'servicios.tipo',
                        'medio_pagos.medio_pago',
                        'eps.codigo',
                        'eps.entidad',
                        'servicios.tipo_servicio',
                        'pacientes.tipo_identificacion',
                        'pacientes.identificacion'
                    ])
                    ->select(
                        'pagos.id',
                        'pagos.pago_realizado as total',
                        DB::raw('pagos.fecha_pago AS fecha'),
                        DB::raw('pagos.pago_realizado AS precio'),
                        DB::raw("
                        CASE 
                        WHEN servicios.tipo_servicio = 'EPS' THEN CONCAT(eps.codigo, ' - ', eps.entidad)
                        ELSE CONCAT(pacientes.tipo_identificacion, ' ', pacientes.identificacion, ' - ', pacientes.primer_nombre, ' ', pacientes.primer_apellido)
                    END AS nombre_cliente
                    "),
                        DB::raw("
                        COALESCE(
                            (SELECT nombre FROM especialidades WHERE especialidades.id = servicios.id_tipo_servicio AND servicios.tipo = 'CONSULTA' LIMIT 1),
                            (SELECT descripcion FROM sesiones WHERE sesiones.id = servicios.id_tipo_servicio AND servicios.tipo = 'SESION' LIMIT 1),
                            (SELECT descripcion FROM paquetes WHERE paquetes.id = servicios.id_tipo_servicio AND servicios.tipo = 'PAQUETE' LIMIT 1),
                            (SELECT descripcion FROM pruebas WHERE pruebas.id = servicios.id_tipo_servicio AND servicios.tipo = 'PRUEBAS' LIMIT 1),
                            (SELECT descripcion FROM asesorias WHERE asesorias.id = servicios.id_tipo_servicio AND servicios.tipo = 'ASESORIA' LIMIT 1),
                            'Sin descripción'
                        ) AS descripcion
                    "),
                        DB::raw("CASE 
                        WHEN medio_pagos.medio_pago = 'e' THEN 'Efectivo' 
                        WHEN medio_pagos.medio_pago = 't' THEN 'Transferencia' 
                        WHEN medio_pagos.medio_pago = 'td' THEN 'Tarjeta debito' 
                        WHEN medio_pagos.medio_pago = 'tc' THEN 'Tarjeta credito' 
                        WHEN medio_pagos.medio_pago = 'otro' THEN 'Otro' 
                        ELSE 'Sin medio de pago' 
                    END AS medio_pago")
                    );

                if ($tipo == 'mes') {
                    $pagos = $pagos->whereMonth('pagos.fecha_pago', date('m'));
                } else if ($tipo == 'hoy') {
                    $pagos = $pagos->whereDate('pagos.fecha_pago', date('Y-m-d'));
                }

                $pagos = $pagos->orderBy('pagos.fecha_pago', 'desc')->get();
            }

            return response()->json([
                'pagos' => $pagos
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
                ->where('servicios.tipo_servicio', '!=', 'EPS')
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
                    'servicios.tipo',
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
                ->get();

            $historialPagosEps = DB::connection('mysql')
                ->table('pagos')
                ->leftJoin('servicios', 'pagos.id_servicio', 'servicios.id')
                ->leftJoin('eps', 'servicios.id_paciente', 'eps.id')
                ->where('pagos.estado', 'ACTIVO')
                ->where('servicios.tipo_servicio', 'EPS')
                ->orderBy('fecha_pago', 'desc')
                ->groupBy([
                    'pagos.id',
                    'pagos.pago_realizado',
                    'pagos.fecha_pago',
                    'eps.entidad',
                    'servicios.id_tipo_servicio',
                    'servicios.tipo'
                ])
                ->select(
                    'pagos.id',
                    'pagos.pago_realizado',
                    'pagos.fecha_pago',
                    'eps.entidad',
                    'servicios.tipo',
                    DB::raw("
                    COALESCE(
                        (SELECT descripcion FROM asesorias WHERE asesorias.id = servicios.id_tipo_servicio AND servicios.tipo = 'ASESORIA' LIMIT 1),
                        'Sin descripción'
                    ) AS descripcion
                ")

                )
                ->get();

            //concatenar  historial de pagos y historial de pagos eps
            $historialPagos = $historialPagos->concat($historialPagosEps);
            //ordenar historial de pagos por ultimas id
            $historialPagos = $historialPagos->sortByDesc('id')->take(5);


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
                ->leftJoin('pagos', 'ventas.id',  'pagos.id_servicio')
                ->where('ventas.estado_venta', 'PAGADO')
                ->where('servicios.estado', 'ACTIVO')
                ->where('pagos.estado', 'ACTIVO')
                ->where('servicios.tipo_servicio', '!=', 'EPS')
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
                    'ventas.id',
                    'servicios.fecha',
                    'pagos.fecha_pago'
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
                "),
                    'servicios.fecha',
                    'pagos.fecha_pago'

                );

            if ($search) {
                $paquetes->where(function ($query) use ($search) {
                    // Búsqueda por identificación
                    $query->where('pacientes.identificacion', 'LIKE', '%' . $search . '%');
                    
                    // Búsqueda individual por campos
                    $query->orWhere('pacientes.primer_nombre', 'LIKE', '%' . $search . '%')
                        ->orWhere('pacientes.segundo_nombre', 'LIKE', '%' . $search . '%')
                        ->orWhere('pacientes.primer_apellido', 'LIKE', '%' . $search . '%')
                        ->orWhere('pacientes.segundo_apellido', 'LIKE', '%' . $search . '%');
                    
                    // Búsqueda en nombre completo (exacta)
                    $query->orWhere(DB::raw("CONCAT(pacientes.primer_nombre,' ',pacientes.segundo_nombre,' ',pacientes.primer_apellido,' ',pacientes.segundo_apellido)"), 'LIKE', '%' . $search . '%');
                    
                    // Búsqueda por palabras en desorden: divide el término de búsqueda en palabras
                    // y verifica que todas estén presentes en el nombre completo
                    $palabras = array_filter(explode(' ', trim($search)));
                    if (count($palabras) > 1) {
                        $query->orWhere(function ($subQuery) use ($palabras) {
                            $nombreCompleto = DB::raw("CONCAT(pacientes.primer_nombre,' ',pacientes.segundo_nombre,' ',pacientes.primer_apellido,' ',pacientes.segundo_apellido)");
                            foreach ($palabras as $palabra) {
                                $subQuery->where($nombreCompleto, 'LIKE', '%' . trim($palabra) . '%');
                            }
                        });
                    }
                });
            }
            $paquetes->orderBy('pagos.fecha_pago', 'desc');

            $ListPaquetes = $paquetes->paginate($perPage, ['*'], 'page', $page);

            $tdTable = '';
            $x = ($page - 1) * $perPage + 1;
            $const = 1;
            foreach ($ListPaquetes as $i => $item) {
                if (!is_null($item)) {
                    $pago_realizado = number_format($item->total, 2, ',', '.');
                    $fecha_pago = \Carbon\Carbon::parse($item->fecha_pago)->format('d/m/Y');
                    $tdTable .= '<tr class="hover-opagos" onclick="verPago(' . $item->id . ');">
                                    <td>
                                        <h6 class="text-dark fw-600 hover-primary m-0">' . $item->identificacion_completa . ' - ' . $item->nombre_paciente . '</h6>
                                        <span class="text-fade d-block fs-14"><strong>Paquete: </strong> ' . $item->descripcion . '</span>
                                    </td>  
                                    <td>
                                        <h5>' . $fecha_pago . '</h5>
                                    </td>
                                    <td>
                                        <h5>$ ' . $pago_realizado . '</h5>
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
    public function listaVentasPacientesPagosEps(Request $request)
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
                ->leftJoin('eps', 'servicios.id_paciente',  'eps.id')
                ->leftJoin('ventas', 'servicios.id',  'ventas.id_servicio')
                ->leftJoin('pagos', 'ventas.id',  'pagos.id_servicio')
                ->where('ventas.estado_venta', 'PAGADO')
                ->where('servicios.estado', 'ACTIVO')
                ->where('pagos.estado', 'ACTIVO')
                ->where('servicios.tipo_servicio', 'EPS')
                ->groupBy([
                    'servicios.id',
                    'eps.entidad',
                    'servicios.id_tipo_servicio',
                    'servicios.tipo',
                    'ventas.total',
                    'ventas.id',
                    'servicios.fecha',
                    'pagos.fecha_pago'
                ])
                ->select(
                    'servicios.id',
                    'eps.entidad',
                    'ventas.total',
                    'ventas.id AS id_venta',
                    DB::raw("
                    COALESCE(
                        (SELECT descripcion FROM asesorias WHERE asesorias.id = servicios.id_tipo_servicio AND servicios.tipo = 'ASESORIA' LIMIT 1),
                        'Sin descripción'
                    ) AS descripcion
                "),
                    'servicios.fecha',
                    'pagos.fecha_pago'

                );

            if ($search) {
                $paquetes->where(function ($query) use ($search) {
                    // Búsqueda individual por campos
                    $query->where('eps.entidad', 'LIKE', '%' . $search . '%')
                        ->orWhere('servicios.tipo', 'LIKE', '%' . $search . '%');
                    
                    // Búsqueda por palabras en desorden: divide el término de búsqueda en palabras
                    // y verifica que todas estén presentes en los campos
                    $palabras = array_filter(explode(' ', trim($search)));
                    if (count($palabras) > 1) {
                        // Búsqueda en eps.entidad con palabras en desorden
                        $query->orWhere(function ($subQuery) use ($palabras) {
                            foreach ($palabras as $palabra) {
                                $subQuery->where('eps.entidad', 'LIKE', '%' . trim($palabra) . '%');
                            }
                        });
                        
                        // Búsqueda en servicios.tipo con palabras en desorden
                        $query->orWhere(function ($subQuery) use ($palabras) {
                            foreach ($palabras as $palabra) {
                                $subQuery->where('servicios.tipo', 'LIKE', '%' . trim($palabra) . '%');
                            }
                        });
                    }
                });
            }
            
            $paquetes->orderBy('pagos.fecha_pago', 'desc');

            $ListPaquetes = $paquetes->paginate($perPage, ['*'], 'page', $page);

            $tdTable = '';
            $x = ($page - 1) * $perPage + 1;
            $const = 1;
            foreach ($ListPaquetes as $i => $item) {
                if (!is_null($item)) {
                    $pago_realizado = number_format($item->total, 2, ',', '.');
                    $fecha_pago = \Carbon\Carbon::parse($item->fecha_pago)->format('d/m/Y');
                    $tdTable .= '<tr class="hover-opagos" onclick="verPago(' . $item->id . ');">
                                    <td>
                                        <h6 class="text-dark fw-600 hover-primary m-0">' . $item->entidad . '</h6>
                                        <span class="text-fade d-block fs-14"><strong>Descripción del servicio: </strong> ' . $item->descripcion . '</span>
                                    </td>  
                                    <td>
                                        <h5>' . $fecha_pago . '</h5>
                                    </td>
                                    <td>
                                        <h5>$ ' . $pago_realizado . '</h5>
                                    </td>
                                </tr>';
                    $x++;
                    $const++;
                }
            }
            $pagination = $ListPaquetes->links('Recaudos.PaginacionRecaudosPagosEps')->render();

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

            //si hay fecha de cierre, se debe filtras hatsa la fecha de cierre si no poner fecha actual

            $fechaCierre = $caja->fecha_cierre;
            if ($fechaCierre) {
                $fechaCierre = date('Y-m-d', strtotime($fechaCierre));
            } else {
                $fechaCierre = date('Y-m-d');
            }        

            //recaudo
            $recaudos = Gastos::recaudosCajaResumen($caja->fecha_apertura, $fechaCierre);

            //gastos
            $gastos = Gastos::GastosCajaDet($caja->fecha_apertura, $fechaCierre);
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
                WHEN medio_pagos.medio_pago = 'td' THEN 'Tarjeta de débito'
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

    public function buscarGasto(Request $request)
    {
        $idGasto = $request->input('idGasto');
        $gasto = Gastos::busquedaGasto($idGasto);
        return response()->json($gasto);
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

    public function obtenerDatosPago(Request $request)
    {
        try {
            $idPago = $request->input('idPago');

            if (!$idPago) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID del pago no proporcionado'
                ], 400);
            }

            $pago = DB::connection('mysql')
                ->table('pagos')
                ->leftJoin('servicios', 'pagos.id_servicio', 'servicios.id')
                ->leftJoin('pacientes', 'servicios.id_paciente', 'pacientes.id')
                ->leftJoin('medio_pagos', 'medio_pagos.id_pago', 'pagos.id')
                ->leftJoin('ventas', 'ventas.id_servicio', 'servicios.id')
                ->where('pagos.id', $idPago)
                ->groupBy([
                    'pagos.id',
                    'pagos.pago_realizado',
                    'pagos.fecha_pago',
                    'pacientes.primer_nombre',
                    'pacientes.primer_apellido',
                    'servicios.id_tipo_servicio',
                    'servicios.tipo',
                    'medio_pagos.medio_pago'
                ])
                ->select(
                    'pagos.id',
                    'pagos.pago_realizado as total',
                    DB::raw('pagos.fecha_pago AS fecha'),
                    DB::raw('pagos.pago_realizado AS precio'),
                    'pacientes.primer_nombre',
                    'pacientes.primer_apellido',
                    'medio_pagos.medio_pago',
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
                ->first();






            if (!$pago) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pago no encontrado'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'pago' => $pago
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los datos del pago',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function actualizarPagoRecaudo(Request $request)
    {
        try {
            $idPago = $request->input('idPago');
            $fechaPago = $request->input('fechaPago');
            //  $valorPago = $request->input('valorPago');
            $medioPago = $request->input('medioPago');
            $referencia = $request->input('referencia');

            // Validaciones
            if (!$idPago || !$fechaPago || !$medioPago) {
                return response()->json([
                    'success' => false,
                    'message' => 'Todos los campos obligatorios deben estar completos'
                ], 400);
            }

            if ($medioPago !== 'e' && !$referencia) {
                return response()->json([
                    'success' => false,
                    'message' => 'El número de referencia es obligatorio para este medio de pago'
                ], 400);
            }

            // Actualizar el pago
            $pagoActualizado = DB::connection('mysql')
                ->table('pagos')
                ->where('id', $idPago)
                ->update([
                    'fecha_pago' => $fechaPago
                ]);

            $medioPago = DB::connection('mysql')
                ->table('medio_pagos')
                ->where('id_pago', $idPago)
                ->update([
                    'medio_pago' => $medioPago,
                    'referencia' => $referencia
                ]);

            if ($pagoActualizado) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pago actualizado exitosamente'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo actualizar el pago, no se realizo ningun cambio'
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el pago',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
