<?php

namespace Omniship\Berry\Http;

class ServicesRequest extends AbstractRequest
{


    public function getData()
    {
        return '';
    }

    public function sendData($data)
    {
        $query = $this->getClient()->SendRequest('get', 'packages/next_available_slots?count=6');
        return $this->createResponse($query);
    }

    protected function createResponse($data)
    {
        return $this->response = new ServicesResponse($this, $data);
    }
}
