<?php

namespace Omniship\Berry\Http;

class TrackingParcelRequest extends AbstractRequest
{

    public function getData()
    {
        $explode_id = explode('|', $this->getBolId());
        return $explode_id[1];
    }

    public function sendData($data)
    {
        $query = $this->getClient()->SendRequest('get', 'jobs/'.$data.'/history');
        return $this->createResponse($query);
    }

    protected function createResponse($data)
    {
        return $this->response = new TrackingParcelResponse($this, $data);
    }
}
