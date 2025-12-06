<?php
namespace App\Services;

use App\Models\ApiToken;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApiService
{
    protected string $baseUrl;
    protected int $timeout;

    public function __construct()
    {
        $this->baseUrl = config('services.api.base_url');
        $this->timeout = config('services.api.timeout', 30);
    }

    /**
     * Autenticar usuario contra la API
     * 
     * @param string $email
     * @param string $password
     * @return array
     * @throws \Exception
     */
    public function login(string $email, string $password): array
    {
        //dd('RUTA:', "{$this->baseUrl}/api/auth/login");
        try {

            /* 
                $response = Http::timeout($this->timeout)
                ->post("{$this->baseUrl}/api/auth/login", [
                    'email' => $email,
                    'password' => $password,
                ]); 
            */

            //dd('hola');
            $response = $this->getHttpClient()
                ->post("{$this->baseUrl}/api/auth/login", [
                    'email' => $email,
                    'password' => $password,
                ]);
            //dd('chau', $response);
            // Si la respuesta no es exitosa
            if ($response->failed()) {
                /* Log::warning('API login failed', [
                    'email' => $email,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]); */

                return [
                    'ok' => false,
                    'details' => $response->json('details') ?? null,
                    'status' => $response->status(),
                    'message' =>  $response->json('message') ?? 'Error de autenticación en la API',
                    'errors' => []
                ];
                //throw new \Exception('Credenciales inválidas', $response->status());
            }

            $data = $response->json();

            return [
                'ok' => true,
                'status' => $response->status(),
                'data' => $data
            ];
            //dd('chau 2', $data);
            // Validar que la respuesta tenga los datos necesarios
            if (!isset($data['accessToken']) || !isset($data['user'])) {
                Log::error('API response missing required fields', ['data' => $data]);
                throw new \Exception('Respuesta inválida de la API');
            }

            return $data;

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('API connection error', ['error' => $e->getMessage()]);
            //dd('API connection error', ['error' => $e->getMessage()]);
            throw new \Exception('No se pudo conectar con el servidor de autenticación');
        }
    }

    /**
     * Hacer una petición autenticada a la API
     * 
     * @param string $method (get, post, put, delete)
     * @param string $endpoint
     * @param array $data
     * @param string $token
     * @return array
     * @throws \Exception
     */
    /* 
        public function makeAuthenticatedRequest(
            string $method, 
            string $endpoint, 
            array $data = [], 
            //string $token
        ): array {
            try {
                

                $auth = Auth::user();
                $user = User::find($auth->id);
                $token = $user->getValidToken();
                
                $response = $this->getHttpClient()
                    ->withToken($token)
                    ->$method("{$this->baseUrl}/{$endpoint}", $data);
                if ($response->failed()) {
                    // Si es 401, el token expiró o es inválido
                    if ($response->status() === 401 || $response->status() === 403) {

                        $refreshToken = $this->refreshRemoteToken($user);

                        if (is_null($refreshToken)) {
                            throw new \Exception('Error de Autenticación en la API');
                        }

                        $response = $this->getHttpClient()
                            ->withToken($refreshToken)
                            ->$method("{$this->baseUrl}/{$endpoint}", $data);

                        return $response->json();
                        //throw new \Exception('Token inválido o expirado', 401);
                    }



                    //dd("{$this->baseUrl}/{$endpoint}", $response);

                    throw new \Exception('Error en la petición a la API');
                }

                if ($response->status() === 204) 
                {
                    return [];
                }

                return $response->json();

            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                Log::error('API connection error', ['error' => $e->getMessage()]);
                throw new \Exception('No se pudo conectar con el servidor');
            }
        } 
    */
    public function makeAuthenticatedRequest(
        string $method, 
        string $endpoint, 
        array $data = [],
    ): array {

        try {

            $auth = Auth::user();
            $user = User::find($auth->id);
            $token = $user->getValidToken();

            $response = $this->getHttpClient()
                ->withToken($token)
                ->$method("{$this->baseUrl}/{$endpoint}", $data);

            // --- Si falla la request ---
            if ($response->failed()) {

                // Token inválido → refrescar token
                if ($response->status() === 401 || $response->status() === 403) {

                    $refreshToken = $this->refreshRemoteToken($user);

                    if (is_null($refreshToken)) {
                        return [
                            'ok' => false,
                            'status' => 401,
                            'message' => 'Error de autenticación en la API',
                            'errors' => []
                        ];
                    }

                    $response = $this->getHttpClient()
                        ->withToken($refreshToken)
                        ->$method("{$this->baseUrl}/{$endpoint}", $data);

                    // si vuelve a fallar después del refresh
                    if ($response->failed()) {
                        return [
                            'ok' => false,
                            'details' => $response->json('details') ?? null,
                            'status' => $response->status(),
                            'message' => 'La API rechazó la autenticación aun tras refrescar token.',
                            'errors' => $response->json('errors') ?? []
                        ];
                    }
                }

                // Manejo de errores genéricos de la API
                return [
                    'ok' => false,
                    'details' => $response->json('details') ?? null,
                    'status' => $response->status(),
                    'message' => $response->json('message') ?? 'Error en la petición a la API',
                    'errors' => $response->json('errors') ?? []
                ];
            }

            // --- Sin contenido ---
            if ($response->status() === 204) {
                return [
                    'ok' => true,
                    'details' => $response->json('details') ?? null,
                    'status' => 204,
                    'data' => []
                ];
            }

            // --- Todo OK ---
            return [
                'ok' => true,
                'status' => $response->status(),
                'data' => $response->json()
            ];

        } catch (\Illuminate\Http\Client\ConnectionException $e) {

            return [
                'ok' => false,
                'status' => 0,
                'message' => 'No se pudo conectar con el servidor',
                'errors' => []
            ];
        }
    }


    /**
     * Refrescar el token de acceso
     * 
     * @param string $refreshToken
     * @return array
     * @throws \Exception
     */
    /* 
        public function refreshToken(string $refreshToken): array
        {
            try {
                $response = $this->getHttpClient()
                    ->post("{$this->baseUrl}/auth/refresh", [
                        'refresh_token' => $refreshToken,
                    ]);

                if ($response->failed()) {
                    throw new \Exception('No se pudo refrescar el token', $response->status());
                }

                return $response->json();

            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                Log::error('API connection error on refresh', ['error' => $e->getMessage()]);
                throw new \Exception('No se pudo conectar con el servidor');
            }
        } 
    */
    private function refreshRemoteToken(User $user)
    {
        $email    = $user->email;
        $password = $user->remote_password; // ← PASSWORD EN TEXTO PLANO

        if (!$password) {
            return null;
        }

        $login = $this->login($email, $password);

        if (!$login['ok']) {
            return null;
        }

        $data = $login['data'];
        $api_user_id = $data['user']['id'];
        User::where('api_user_id', $api_user_id)->first();

        $api_token = ApiToken::where('user_id', $api_user_id)->first();
        $api_token->update(
            [
                'user_id' => $user->id, // Buscar por user_id
                'access_token' => $data['accessToken'],
                'refresh_token' => $data['accessToken'] ?? null,
                'expires_at' => null,
            ]
        );
        // Guardar nuevo token
        $user->update([
            'role' => is_null($data['user']['role']) 
                ? null : json_encode($data['user']['role']),
            'avatar_url' => $data['user']['avatarUrl'] ?? null,
            'status' => $data['user']['status'] ?? null,
        ]);

        return $data['accessToken'];
    }


    /**
     * Cerrar sesión en la API
     * 
     * @param string $token
     * @return bool
     */
    public function logout(string $token): bool
    {
        try {
            $response = $this->getHttpClient()
                ->withToken($token)
                ->post("{$this->baseUrl}/auth/logout");
            return $response->successful();

        } catch (\Exception $e) {
            Log::warning('API logout error', ['error' => $e->getMessage()]);
            // No lanzamos excepción porque el logout local debe proceder de todas formas
            return false;
        }
    }

    /**
     * Obtener cliente HTTP configurado
     */
    protected function getHttpClient()
    {
        $client = Http::timeout($this->timeout);
        
        // SOLO PARA DESARROLLO: verificar si existe archivo cacert.pem local
        if (app()->environment('local')) {
            $cacertPath = base_path('cacert.pem');
            
            if (file_exists($cacertPath)) {
                $client = $client->withOptions([
                    'verify' => $cacertPath,
                ]);
            }
        }
        
        return $client;
    }
}