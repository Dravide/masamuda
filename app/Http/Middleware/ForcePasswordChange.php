<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChange
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->password_change_required) {
            // Allow Livewire internal routes
            if ($request->routeIs('livewire.update') || $request->routeIs('livewire.upload-file') || $request->routeIs('livewire.preview-file')) {
                return $next($request);
            }

            // Allow logout
            if ($request->routeIs('logout')) {
                return $next($request);
            }

            // Allow dashboard routes where the modal will be shown
            // Adjust these route names based on your actual dashboard routes
            if ($request->routeIs('sekolah.dashboard') || $request->routeIs('admin.dashboard') || $request->routeIs('siswa.dashboard')) {
                return $next($request);
            }
            
            // Redirect any other attempt to the respective dashboard
            $role = Auth::user()->role;
            if (in_array($role, ['admin', 'sekolah', 'siswa'])) {
                return redirect()->route($role . '.dashboard');
            }
            
            // Fallback
            return redirect('/');
        }

        return $next($request);
    }
}
