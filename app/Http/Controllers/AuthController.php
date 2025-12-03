<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\ApiService;
use App\Models\User;
use App\Models\ApiToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    protected ApiService $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Mostrar formulario de login
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Procesar el login
     * 
     * FLUJO COMPLETO:
     * 1. Recibir credenciales
     * 2. Enviarlas a la API
     * 3. Recibir y almacenar token
     * 4. Crear sesión en Laravel
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

        try {
            //dd('antes de ejecutarse el inicio de TRANSACCIÓN DB');
            // Iniciar transacción de base de datos
            //DB::beginTransaction();

            // 2. ENVIAR CREDENCIALES A LA API
            //Log::info('Intentando login', ['email' => $credentials['email']]);
            
            $apiResponse = $this->apiService->login(
                $credentials['email'],
                $credentials['password']
            );
            dd($apiResponse);
            // La respuesta de la API debería tener esta estructura:
            // {
            //     "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
            //     "token_type": "bearer",
            //     "expires_in": 3600,
            //     "refresh_token": "def502003e7c8f...",
            //     "user": {
            //         "id": 123,
            //         "email": "pedro@example.com",
            //         "name": "Pedro Pérez",
            //         "role": "admin"
            //     }
            // }

            // 3. ALMACENAR DATOS DEL USUARIO Y TOKEN

            // 3.1. Crear o actualizar usuario en BD local
            $current_user = User::where('api_user_id', $apiResponse['user']['id'])->first();
            if ($current_user){
                $current_user->update(
                    [
                        'api_user_id' => $apiResponse['user']['id'],
                        'email' =>  $apiResponse['user']['email'],
                        'role' => is_null($apiResponse['user']['role']) 
                            ? null : json_encode($apiResponse['user']['role']),
                        'avatar_url' => $apiResponse['user']['avatarUrl'] ?? null,
                        'status' => $apiResponse['user']['status'] ?? null,
                    ]
                );
            }
            else {
                $current_user = User::create(
                    [
                        'api_user_id' => $apiResponse['user']['id'],
                        'email' =>  $apiResponse['user']['email'],
                        'role' => is_null($apiResponse['user']['role']) 
                            ? null : json_encode($apiResponse['user']['role']),
                        'avatar_url' => $apiResponse['user']['avatarUrl'] ?? null,
                        'status' => $apiResponse['user']['status'] ?? null,
                    ]
                );
            }

            //Log::info('Usuario creado/actualizado', ['user_id' => $current_user->id]);

            // 3.2. Calcular fecha de expiración del token
            $expiresAt = now()->addSeconds($apiResponse['expires_in'] ?? 3600);

            dd("aqui", $apiResponse, $current_user);
            // 3.3. Guardar token en tabla api_tokens
            $api_token = ApiToken::where('user_id', $current_user->api_user_id)->first();
            if ($api_token) {
                $api_token->update(
                    [
                        'user_id' => $current_user->id, // Buscar por user_id
                        'access_token' => $apiResponse['accessToken'],
                        'refresh_token' => $apiResponse['accessToken'] ?? null,
                        'expires_at' => $expiresAt,
                    ]
                );
            }
            else {
                $api_token = ApiToken::create(
                    [
                        'user_id'=> $current_user->id,
                        'access_token' => $apiResponse['accessToken'],
                        'refresh_token' => $apiResponse['accessToken'] ?? null,
                        'expires_at' => $expiresAt,
                    ]
                );
            }

            /* Log::info('Token guardado', [
                'user_id' => $current_user->id,
                'expires_at' => $expiresAt->toDateTimeString(),
            ]); */

            // 4. CREAR SESIÓN EN LARAVEL
            // Esto crea una cookie de sesión que Laravel maneja automáticamente
            Auth::login($current_user, $request->filled('remember') ?? false);

            //Log::info('Sesión creada', ['user_id' => $current_user->id]);

            // Confirmar transacción
            DB::commit();

            // Regenerar la sesión para prevenir session fixation
            $request->session()->regenerate();

            // Redireccionar al dashboard
            return redirect()->intended(route('admin.dashboard'))
                ->with('success', '¡Bienvenido, ' . $current_user->email . '!');

        } catch (\Exception $e) {
            // Revertir transacción en caso de error
            DB::rollBack();

            dd("Email", $credentials['email'], $e);

            /* Log::error('Error en login', [
                'email' => $credentials['email'],
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]); */

            // Mensaje genérico para el usuario (no revelar detalles de seguridad)
            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'Las credenciales proporcionadas son incorrectas.',
                ]);
        }
    }

    /**
     * Cerrar sesión
     */
    public function logout(Request $request)
    {
        $auth = Auth::user();
        $user = $auth->id;

        if ($user) {
            // Intentar cerrar sesión en la API
            try {
                $token = $user->getValidToken();
                if ($token) {
                    $this->apiService->logout($token);
                }
            } catch (\Exception $e) {
                /* Log::warning('Error al cerrar sesión en API', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]); */
                // Continuar con el logout local aunque falle el de la API
            }

            // Eliminar token local
            $user->apiToken()->delete();
        }

        // Cerrar sesión en Laravel
        Auth::logout();

        // Invalidar sesión
        $request->session()->invalidate();

        // Regenerar token CSRF
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Has cerrado sesión correctamente');
    }
}