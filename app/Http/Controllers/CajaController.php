<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Gastos;
use App\Models\Pacientes;

class CajaController extends Controller
{
    public function consultarMontoCierre(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'No autorizado'], 401);
        }
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

    public function cerrarCaja()
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

    public function listaCategorias()
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

    public function  guardarGastos(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'estado' => 'error',
                'mensaje' => 'Su sesión ha terminado.',
            ], 401);
        }

        try {
            $request->validate([
                'accRegistro' => 'required|in:guardar,actualizar',
                'descripcion' => 'required|string|max:255',
                'fecha'       => 'required|date',
                'categoria'   => 'required',
                'valor'       => 'required|numeric|min:0',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        }

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
            ], 401);
        }

        try {
            $request->validate([
                'saldoAnte'     => 'required|numeric|min:0',
                'abono'         => 'required|numeric|min:0',
                'fechaApertura' => 'required|date',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        }

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
            ], 401);
        }

        try {
            $request->validate([
                'accionCate'  => 'required|in:guardar,actualizar',
                'descripcion' => 'required|string|max:255',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        }

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
}
