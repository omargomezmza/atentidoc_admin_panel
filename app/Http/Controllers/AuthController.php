<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    
    /**
     * Mostrar formulario de login
    */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Procesar login
    */
    public function login(Request $request)
    {
        // 1. RECIBIR Y VALIDAR CREDENCIALES
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ], [
            'email.required' => 'El correo electrónico es obligatorio',
            'email.email' => 'Ingrese un correo electrónico válido',
            'password.required' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
        ]);

        // 2. VERIFICAR CREDENCIALES
        $attempUser = User::where('email', $credentials['email'])->first();

        if (!$attempUser) {
            return back()
                ->with([
                    'error' => 'Las credenciales proporcionadas son incorrectas.',
                    'exception' => ''
                ])->withInput();
        }

        $password = $credentials['password'];
        $hash = $attempUser->password_hash;
        if (!password_verify($password, $hash)) {
            return back()
                ->with([
                    'error' => 'Las credenciales proporcionadas son incorrectas.',
                    'exception' => ''
                ])->withInput();
        }

        // 3. AUTENTICAR USUARIO
        Auth::login($attempUser, $request->filled('remember') ?? false);

        // 4. Regenerar la sesión para prevenir session fixation
        $request->session()->regenerate();

        // Redireccionar al dashboard
        return redirect()->intended(route('admin.dashboard'));

    }

    /**
     * Cerrar sesión
     */
    public function logout(Request $request)
    {
        // 1. OBTENER USUARIO AUTENTICADO
        $auth = Auth::user();
        $user = User::find($auth->id);

        // 2. CERRAR SESIÓN
        Auth::logout();

        // 3. INVALIDAR SESIÓN
        $request->session()->invalidate();

        // 4. REGENERAR TOKEN CSRF
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Has cerrado sesión correctamente');
    }
}