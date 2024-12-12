<?php

namespace App\Services\MicroAuth;

use Exception;
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
            $response =  $this->request('post', '/register', $request);

            return response()->json(json_decode($response->getBody(), true), $response->getStatusCode());

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            // Captura erros de cliente (422, 404, etc.) do micro auth
            $response = $e->getResponse();

            return response()->json(
                json_decode($response->getBody(), true),
                $response->getStatusCode()
            );
        } catch (Exception $e) {
            // Lida com outros erros
            return response()->json([
                'error' => true,
                'message' => 'Erro ao cadastrar usuário no micro auth: ' . $e->getMessage(),
            ], 500);
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