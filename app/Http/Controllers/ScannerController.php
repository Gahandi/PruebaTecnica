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
        
        // Verificar autenticación
        if (!auth()->check()) {
            abort(403, 'Debes iniciar sesión para acceder al escáner.');
        }
        
        // Verificar si el usuario es admin del space o tiene el permiso
        $user = auth()->user();
        $isAdmin = $user->isAdminOfSpace($space->id);
        $hasPermission = RoleSpacePermission::hasPermission($space->id, 'create checkins');
        
        $canSeeScanner = $isAdmin || $hasPermission;

        if ($canSeeScanner) {
            return view('scanner.index', ['space' => $space]);
        }

        abort(403, 'No tienes permisos para acceder al escáner.');
    }
    
}
