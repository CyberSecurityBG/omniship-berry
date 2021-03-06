<?php
/**
 * Created by PhpStorm.
 * User: joro
 * Date: 23.5.2017 г.
 * Time: 09:35 ч.
 */

namespace Omniship\Berry\Http;

use Omniship\Berry\Client;
use Omniship\Message\AbstractResponse AS BaseAbstractResponse;

class AbstractResponse extends BaseAbstractResponse
{

    protected $error;

    protected $errorCode;

    protected $client;


    /**
     * Get the initiating request object.
     *
     * @return AbstractRequest
     */
    public function getRequest()
    {
       return  $this->request;
    }

    /**
     * @return null|string
     */
    public function getMessage()
    {
        if($this->getCode() == 422 || $this->getCode() == 404){
            $decode = json_decode($this->getClient()->getError()['error']);
            return isset($decode->message) ? $decode->message : $this->getCode().' - '.$this->getClient()->getError()['error'];
        }
        return null;
    }

    /**
     * @return null|string
     */
    public function getCode()
    {
        return $this->getClient()->getError()['code'];
        return null;
    }

    /**
     * @return null|Client
     */
    public function getClient()
    {
        return $this->getRequest()->getClient();
    }

    /**
     * @param mixed $client
     * @return AbstractResponse
     */


}
