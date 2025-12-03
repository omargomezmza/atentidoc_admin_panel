<?php
namespace App\Services;

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

            $response = $this->getHttpClient()
                ->post("{$this->baseUrl}/api/auth/login", [
                    'email' => $email,
                    'password' => $password,
                ]);
            // Si la respuesta no es exitosa
            if ($response->failed()) {
                /* Log::warning('API login failed', [
                    'email' => $email,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]); */

                throw new \Exception('Credenciales inválidas', $response->status());
            }

            $data = $response->json();
            
            dd($data);
            // Validar que la respuesta tenga los datos necesarios
            if (!isset($data['accessToken']) || !isset($data['user'])) {
                Log::error('API response missing required fields', ['data' => $data]);
                throw new \Exception('Respuesta inválida de la API');
            }

            return $data;

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('API connection error', ['error' => $e->getMessage()]);
            dd('API connection error', ['error' => $e->getMessage()]);
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
    public function makeAuthenticatedRequest(
        string $method, 
        string $endpoint, 
        array $data = [], 
        string $token
    ): array {
        try {
            
            /* 
                $response = Http::timeout($this->timeout)
                ->withToken($token)
                ->$method("{$this->baseUrl}/{$endpoint}", $data); 
            */
            $response = $this->getHttpClient()
                ->withToken($token)
                ->$method("{$this->baseUrl}/{$endpoint}", $data);
            if ($response->failed()) {
                // Si es 401, el token expiró o es inválido
                if ($response->status() === 401) {
                    throw new \Exception('Token inválido o expirado', 401);
                }

                throw new \Exception('Error en la petición a la API', $response->status());
            }

            return $response->json();

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('API connection error', ['error' => $e->getMessage()]);
            throw new \Exception('No se pudo conectar con el servidor');
        }
    }

    /**
     * Refrescar el token de acceso
     * 
     * @param string $refreshToken
     * @return array
     * @throws \Exception
     */
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