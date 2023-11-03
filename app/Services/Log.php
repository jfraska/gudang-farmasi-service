<?php

namespace App\Services;

use App\Traits\ConsumeExternalServices;

class Log
{
    use ConsumeExternalServices;

	public $baseUri;

	public $secret;

	public function __construct()
	{
		$this->baseUri = config('services.log.base_uri');
		$this->secret = config('services.log.secret');
	}

	public function createLog($data)
	{
		return $this->performRequest('POST', '/api/log', $data);
	}
}