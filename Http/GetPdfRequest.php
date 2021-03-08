<?php
namespace Omniship\Berry\Http;

class GetPdfRequest extends AbstractRequest
{
    /**
     * @return integer
     */
    public function getData() {
        $explode_id = explode('|', $this->getBolId());
        return [
            'bol_id' => $explode_id[1],
        ];
    }

    /**
     * @param mixed $data
     * @return GetPdfResponse
     */
    public function sendData($data) {
        $query = $this->getClient()->SendRequest('get', 'jobs/'.$data['bol_id']);
        return $this->createResponse($query);
    }

    /**
     * @param $data
     * @return GetPdfResponse
     */
    protected function createResponse($data)
    {
        return $this->response = new GetPdfResponse($this, $data);
    }

}
