<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use Illuminate\Support\Str;
use App\Models\Space;
use App\Models\RoleSpacePermission;

class ScannerController extends Controller
{
    //
    public function index()
    {
        $host = request()->getHost();

        // Extraer el subdominio (antes del primer punto)
        $subdomain = Str::before($host, '.');
    
        // Buscar en la tabla spaces donde el slug coincida con el subdominio
        $space = Space::where('subdomain', $subdomain)->first();
    
        // Si no existe el space, retornar 404
        if (!$space) {
            abort(404, 'Espacio no encontrado.');
        }
        // Verificar si el usuario autenticado tiene el permiso
        $canSeeScanner = RoleSpacePermission::hasPermission($space->id, 'create checkins');

        if ($canSeeScanner) {
            return view('scanner.index', ['space' => $space]);
        }

        abort(403, 'No tienes permisos para acceder al escáner.');
    }
    
}
