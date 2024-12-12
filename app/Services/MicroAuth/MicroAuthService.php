<?php

namespace App\Services\MicroAuth;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Milenatf\MicroservicesCommon\Services\Traits\ConsumerExternalService;

class MicroAuthService
{
    use ConsumerExternalService;

    protected $url, $token;

    public function __construct()
    {
        // $this->url = config('services.micro_auth.url');
        $this->url = config('services.micro_auth.url');
        $this->token = config('services.micro_auth.token');
    }

    public function register($request)
    {
        try {
            return $this->request('post', '/register', $request);

        } catch (Exception $e) {

            return response()->json([
                'error' => true,
                'message' => 'Erro ao regitrar no micro auth: ' . $e->getMessage(),
            ]);
        }
    }

    public function login($request)
    {
        try {
            return $this->request('post', "/login", [
                'email' => $request->email,
                'password' => $request->password,
                'device_name' => $request->device_name
            ]);
        } catch (\Exception $e) {
            Log::error('Erro no login do micro auth:', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function logout(string $bearerToken)
    {
        $header = ['Authorization' => $bearerToken];

        try {

            return $this->request('get', '/logout', [], $header);

        } catch(Exception $e) {
            Log::error('Erro realizar o logout:', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function validateToken(string $bearerToken)
    {
        $header = ['Authorization' => $bearerToken];

        try {

            return $this->request('get', '/validate-token', [], $header);

        } catch(Exception $e) {
            Log::error('Erro ao validar o token no micro auth:', ['message' => $e->getMessage()]);
            throw $e;
        }
    }
}