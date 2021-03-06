<?php
/**
 * Created by PhpStorm.
 * User: joro
 * Date: 10.5.2017 г.
 * Time: 16:55 ч.
 */

namespace Omniship\Berry\Http;

use Omniship\Berry\Client as BerryClient;

use Omniship\Message\AbstractRequest as BaseAbstractRequest;

abstract class AbstractRequest extends BaseAbstractRequest
{
    protected $client;
    public function getKey(){

        return $this->getParameter('key');
    }

    public function setKey($value){
        return $this->setParameter('key', $value);
    }

    public function getClient()
    {
        if(is_null($this->client)) {
            $this->client = new BerryClient($this->getKey());
        }

        return $this->client;
    }

    abstract protected function createResponse($data);

}
