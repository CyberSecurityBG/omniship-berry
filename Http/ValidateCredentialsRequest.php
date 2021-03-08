<?php

namespace Omniship\Berry\Http;
use Omniship\Berry\Client;
class ValidateCredentialsRequest extends AbstractRequest
{


    public function getData()
    {
    }

    public function sendData($data)
    {
        $services = (new Client($this->getKey()))->SendRequest('get', 'jobs/0', '', '404');
        return $this->createResponse($services);
    }

    protected function createResponse($data)
    {
        return $this->response = new ValidateCredentialsResponse($this, $data);
    }

}
