<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SekretarisOnly
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        // Sekretaris tidak boleh akses scan masuk/keluar
        if (Auth::user()->role === 'sekretaris') {
            return redirect('/dashboard');
        }

        return $next($request);
    }
}