<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function halamanLogin()
    {   
        if (Auth::check()) return redirect('/masuk');
        return view('login');
    }

    public function login(Request $request)
    {
        $username = trim($request->input('username'));
        $password = $request->input('password');

        $user = \App\Models\User::where('username', $username)->first();

        if (!$user || !\Illuminate\Support\Facades\Hash::check($password, $user->password)) {
            return back()->withErrors(['pesan' => 'Username atau password salah'])->withInput();
        }

        \Illuminate\Support\Facades\Auth::login($user);
        $request->session()->regenerate();
        $request->session()->save(); // ← tambah ini

        if ($user->role === 'admin' || $user->role === 'sekretaris') {
            return redirect()->intended('/dashboard');
        } else {
            return redirect()->intended('/masuk');
        }

        
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}