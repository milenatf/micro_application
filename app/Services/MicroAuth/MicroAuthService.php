<?php

namespace App\Services\MicroAuth;

use Milenatf\MicroservicesCommon\Services\Traits\ConsumerExternalService;

class MicroAuthService
{
    use ConsumerExternalService;

    protected $url, $token;

    public function __construct()
    {
        $this->url = config('services.auth.url');
        $this->url = config('services.auth.token');
    }

    public function authMicroApplication()
    {
        dd('Micro application ');
        // $response = $this->request('get', "")
    }
}