<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminOnly
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {  // ← tambah tanda seru !
            return redirect('/login');
        }

        if (Auth::user()->role !== 'admin') {
            return redirect('/masuk')->with('error', 'Akses ditolak! Halaman ini khusus admin.');
        }

        return $next($request);
    }
}