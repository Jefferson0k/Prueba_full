<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\CashRegister;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckCashRegisterOpen
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Verificar si el usuario tiene caja aperturada
        $hasOpenCashRegister = CashRegister::hasUserOpenCashRegister($user->id);

        // Si la ruta actual es /aperturar y YA tiene caja → redirigir a habitaciones
        if ($request->routeIs('aperturar.view') && $hasOpenCashRegister) {
            return redirect()->route('online.view')
                ->with('info', 'Ya tienes una caja aperturada.');
        }

        // Si la ruta requiere caja abierta y NO tiene → redirigir a apertura
        if (!$hasOpenCashRegister && $this->requiresCashRegister($request)) {
            return redirect()->route('aperturar.view')
                ->with('error', 'Debes aperturar una caja antes de acceder a esta sección.');
        }

        return $next($request);
    }

    // Método para determinar qué rutas requieren caja abierta
    protected function requiresCashRegister(Request $request): bool
    {
        $protectedRoutes = [
            'online.view',
            'cuarto.view',
            // Agrega aquí otras rutas que requieran caja abierta
        ];

        return $request->routeIs($protectedRoutes);
    }
}