<?php
/**
 * Created by PhpStorm.
 * User: joro
 * Date: 10.5.2017 г.
 * Time: 16:55 ч.
 */

namespace Omniship\Berry\Http;


class CancelBillOfLadingRequest extends AbstractRequest
{

    /**
     * @return array
     */
    public function getData() {

        $explode_id = explode('|', $this->getBolId());
        return [
            'bol_id' => $explode_id[1],
        ];
    }

    /**
     * @param mixed $data
     * @return CancelBillOfLadingResponse
     */
    public function sendData($data) {
        $query = $this->getClient()->SendRequest('post', '/jobs/'.$data['bol_id'].'/cancel', ['job' => ['reason' => 'Cancel order']]);
        return $this->createResponse($query);
    }

    /**
     * @param $data
     * @return CancelBillOfLadingResponse
     */
    protected function createResponse($data)
    {
        return $this->response = new CancelBillOfLadingResponse($this, $data);
    }

}
