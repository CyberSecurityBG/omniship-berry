<?php

namespace Omniship\Berry;

use GuzzleHttp\Client AS HttpClient;
use http\Client\Response;

class Client
{

    protected $key;
    protected $error;
    protected $test_mode = false;

    const SERVICE_TESTING_URL = 'https://api.sandbox.berry.bg/v2/';
    const SERVICE_PRODUCTION_URL = 'https://api.berry.bg/v2/';

    public function __construct($key)
    {
        $this->key = $key;
    }


    public function getError()
    {
        return $this->error;
    }

    /**
     * @param boolean $test_mode
     * @return $this
     */
    public function setTestMode($test_mode)
    {
        $this->test_mode = $test_mode;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getTestMode()
    {
        return $this->test_mode;
    }


    public function SendRequest($method, $endpoint, $data = [], $ignore = null){
        try {
            $client = new HttpClient(['base_uri' => $this->getServiceEndpoint()]);
            $response = $client->request($method, $endpoint,  [
                'json' => $data,
                'headers' =>  [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/vnd.api+json',
                    'X-BERRY-APIKEY' => $this->key
                ]
            ]);

            return json_decode($response->getBody()->getContents());
        } catch (\Exception $e) { 
            if($ignore && $ignore == $e->getCode()){
                return true;
            }
            $this->error = [
                'code' => $e->getCode(),
                'error' => $e->getResponse()->getBody()->getContents()
            ];
        }
    }

    /**
     * Get url associated to a specific service
     *
     * @return string URL for the service
     */
    public function getServiceEndpoint()
    {
        return $this->getTestMode() ? static::SERVICE_TESTING_URL : static::SERVICE_PRODUCTION_URL;
    }
}
