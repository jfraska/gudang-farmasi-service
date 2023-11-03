<?php

namespace App\Traits;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

trait ConsumeExternalServices
{
    public function performRequest($method, $requestUrl, $formParams = [], $headers = [])
    {
        $client = new Client([
            'base_uri' => $this->baseUri,
        ]);

        if (!array_key_exists('Authorization', $headers)) {
            $headers['Authorization'] = $this->secret;
        }

        try {
            $response = $client->request($method, $requestUrl, [
                'json' => $formParams,
                'headers' => $headers,
            ]);
        } catch (RequestException $e) {
            $response = $e->getResponse();
        }
        
        return (array) json_decode($response->getBody()->getContents());
    }

}
