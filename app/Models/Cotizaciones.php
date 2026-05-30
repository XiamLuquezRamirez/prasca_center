<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use function PHPSTORM_META\map;

class Cotizaciones extends Model
{
    use HasFactory;

    public static function guardarCotizacion($request)
{
    if ($request->input('accCotizacion') == 'guardar') {
        DB::beginTransaction();
        try {
            $cotizacion = DB::table('cotizaciones')->insertGetId([
                'paciente' => $request->input('idPaciente'),
                'fecha' => now(),
                'valor' => $request->input('totalCotizacionHidden'),
                'sub_total' => $request->input('subtotalCotizacionHidden'),
                'descuento' => $request->input('descuentoCotizacionHidden'),
                'estado' => 'ACTIVO'
            ]);

            // Datos del detalle
            $tipos = $request->input('tipoServicioCotizacion');
            $ids = $request->input('idServicioCotizacion');
            $cantidades = $request->input('cantidadCotizacion');
            $valoresOriginales = $request->input('valorOriginalServicioCotizacion');
            $valoresFinales = $request->input('valorFinalServicioCotizacion');
            $descuentosUnitarios = $request->input('descuentoUnitarioCotizacion');
            $descuentosTotales = $request->input('descuentoTotalCotizacion');
            $subtotales = $request->input('subtotalCotizacion');

            for ($i = 0; $i < count($tipos); $i++) {
                DB::table('detalles_cotizacion')->insert([
                    'cotizacion' => $cotizacion,
                    'tipo_servicio' => $tipos[$i],
                    'id_servicio' => $ids[$i],
                    'cantidad' => $cantidades[$i],
                    'valor_original' => $valoresOriginales[$i],
                    'valor_final' => $valoresFinales[$i],
                    'descuento_unitario' => $descuentosUnitarios[$i],
                    'descuento_total' => $descuentosTotales[$i],
                    'subtotal' => $subtotales[$i]
                ]);
            }

            DB::commit();
            return ['idCotizacion' => $cotizacion];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }
    } else {
        DB::beginTransaction();
      
        try {
            DB::table('cotizaciones')
                ->where('id', $request->input('idCotizacion'))
                ->update([
                    'valor' => $request->input('totalCotizacionHidden'),
                    'sub_total' => $request->input('subtotalCotizacionHidden'),
                    'descuento' => $request->input('descuentoCotizacionHidden'),
                ]);

            DB::table('detalles_cotizacion')
                ->where('cotizacion', $request->input('idCotizacion'))
                ->delete();

              

            // Nuevos detalles
            $tipos = $request->input('tipoServicioCotizacion');
            $ids = $request->input('idServicioCotizacion');
            $cantidades = $request->input('cantidadCotizacion');
            $valoresOriginales = $request->input('valorOriginalServicioCotizacion');
            $valoresFinales = $request->input('valorFinalServicioCotizacion');
            $descuentosUnitarios = $request->input('descuentoUnitarioCotizacion');
            $descuentosTotales = $request->input('descuentoTotalCotizacion');
            $subtotales = $request->input('subtotalCotizacion');
     
            for ($i = 0; $i < count($tipos); $i++) {
                DB::table('detalles_cotizacion')->insert([
                    'cotizacion' => $request->input('idCotizacion'),
                    'tipo_servicio' => $tipos[$i],
                    'id_servicio' => $ids[$i],
                    'cantidad' => $cantidades[$i],
                    'valor_original' => $valoresOriginales[$i],
                    'valor_final' => $valoresFinales[$i],
                    'descuento_unitario' => $descuentosUnitarios[$i],
                    'descuento_total' => $descuentosTotales[$i],
                    'subtotal' => $subtotales[$i]
                ]);
            }
          
            DB::commit();
      
            return ['idCotizacion' => $request->input('idCotizacion')];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['error' => $e->getMessage()];
        }
    }
}


    public static function buscaCotizacion($idCotizacion)
    {
        $cotizacion = DB::table('cotizaciones')
            ->where('id', $idCotizacion)
            ->first();
        return $cotizacion;
    }

    public static function buscaDetallesCotizacion($idCotizacion)
    {
        $detalles = DB::table('detalles_cotizacion')
            ->where('cotizacion', $idCotizacion)
            ->get();

        foreach ($detalles as $detalle) {
            if ($detalle->tipo_servicio == 'consulta') {
                $detalle->servicio = DB::table('especialidades')
                    ->where('id', $detalle->id_servicio)
                    ->select('nombre as descripcion')
                    ->first();
            } else if ($detalle->tipo_servicio == 'sesion') {
                $detalle->servicio = DB::table('sesiones')
                    ->where('id', $detalle->id_servicio)
                    ->select('descripcion')
                    ->first();
            } else if ($detalle->tipo_servicio == 'paquete') {
                $detalle->servicio = DB::table('paquetes')
                    ->where('id', $detalle->id_servicio)
                    ->select('descripcion')
                    ->first();
            } else if ($detalle->tipo_servicio == 'prueba') {
                $detalle->servicio = DB::table('pruebas')
                    ->where('id', $detalle->id_servicio)
                    ->select('descripcion')
                    ->first();
            }
        }


        return $detalles;
    }
}
