<?php

namespace App\Services;

use App\Traits\ConsumeExternalServices;

class Rs
{
    use ConsumeExternalServices;

    public $baseUri;

    public $secret;

    public function __construct()
    {
        $this->baseUri = config('services.rs.base_uri');
        $this->secret = config('services.rs.secret');
    }

    public function showUnit($token, $id)
    {
        $headers['Authorization'] = 'Bearer ' . $token;
        $result = $this->performRequest('GET', '/rs-service/unit/show', ["id" => $id], $headers);
        if ($result['code'] == 200) {
            return $result["data"];
        }

        return $result;
    }

    public function showSuplier($token, $id)
    {
        $headers['Authorization'] = 'Bearer ' . $token;
        $result = $this->performRequest('GET', '/rs-service/supplier/show', ["id" => $id], $headers);
        if ($result['code'] == 200) {
            return $result["data"];
        }

        return $result;
    }
    
    public function showPoliklinik($token, $id)
    {
        $headers['Authorization'] = 'Bearer ' . $token;
        $result = $this->performRequest('GET', '/poliklinik/show', ["id" => $id], $headers);
        if ($result['code'] == 200) {
            return $result["data"];
        }

        return $result;
    }
}
