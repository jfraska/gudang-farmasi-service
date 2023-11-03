<?php

namespace App\Services;

use App\Traits\ConsumeExternalServices;

class Auth
{
    use ConsumeExternalServices;

	public $baseUri;

	public $secret;

	public function __construct()
	{
		$this->baseUri = config('services.auth.base_uri');
		$this->secret = config('services.auth.secret');
	}

	public function userCan($token, $usercan)
	{
        $headers['Authorization'] = $token;
		return $this->performRequest('POST', '/auth-service/api/usercan', ["user_can" => $usercan], $headers);
	}

	public function showRole($token, $id)
	{
        $headers['Authorization'] = 'Bearer ' . $token;
        $result = $this->performRequest('GET', '/auth-service/api/role', ["id" => $id], $headers);
        if ($result['code'] == 200) {
            return $result["data"];
        }

        return $result;
	}
}