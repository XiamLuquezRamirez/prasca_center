<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SistemaController extends Controller
{
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
}
