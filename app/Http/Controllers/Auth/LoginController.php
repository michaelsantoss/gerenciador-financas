<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->remember)) {
            $user = Auth::user();

            if ($user->is_super_admin) {
                $request->session()->regenerate();
                return redirect()->intended(route('admin.empresas.index'));
            }

            if (!$user->empresa_id) {
                Auth::logout();
                return back()->withErrors(['email' => 'Usuário sem empresa vinculada.']);
            }

            $request->session()->regenerate();
            return redirect()->intended('emprestimos');
        }

        return back()->withErrors(['email' => 'As credenciais informadas não correspondem aos nossos registros.']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
