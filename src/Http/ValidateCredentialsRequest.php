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
        $getusers = $this->getClient()->SendRequest('get', 'users');
        return $this->createResponse($getusers);
    }

    protected function createResponse($data)
    {
        return $this->response = new ValidateCredentialsResponse($this, $data);
    }

}
