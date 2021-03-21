<?php

namespace Omniship\Berry;

use Omniship\Berry\Http\CodPaymentRequest;
use Omniship\Common\AbstractGateway;
use Omniship\Berry\Http\ValidateCredentialsRequest;
use Omniship\Berry\Http\ShippingQuoteRequest;
use Omniship\Berry\Http\CreateBillOfLadingRequest;
use Omniship\Berry\Http\ServicesRequest;
use Omniship\Berry\Http\CancelBillOfLadingRequest;
use Omniship\Berry\Http\GetPdfRequest;
use Omniship\Berry\Http\TrackingParcelRequest;
use Omniship\Berry\Http\UserRegisterRequest;
use Omniship\Berry\Client;

class Gateway extends AbstractGateway
{

    private $name = 'Berry';
    protected $client;
    const TRACKING_URL_DEV = 'https://sandbox.berry.bg/bg/t/';
    const TRACKING_URL = 'https://berry.bg/bg/t/';

    /**
     * @return stringc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return array
     */
    public function getDefaultParameters()
    {
        return array(
            'key' => '',
            'personal_phone' => ''
        );
    }

    public function getKey() {
        return $this->getParameter('key');
    }

    /**
     * @param $value
     * @return $this
     */
    public function setKey($value) {
        return $this->setParameter('key', $value);
    }


    /**
     * @return mixed
     */
    public function getEndpoint()
    {
        return $this->getParameter('endpoint');
    }

    public function getClient()
    {
        if (is_null($this->client)) {
            $this->client = new Client($this->getKey());
        }

        return $this->client;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setEndpoint($value)
    {
        return $this->setParameter('endpoint', $value);
    }

    public function supportsValidateCredentials(){
        return true;
    }


    public function validateCredentials(array $parameters = [])
    {
        return $this->createRequest(ValidateCredentialsRequest::class, $parameters);
    }

    public function getQuotes($parameters = [])
    {
        if($parameters instanceof ShippingQuoteRequest) {
            return $parameters;
        }
        if(!is_array($parameters)) {
            $parameters = [];
        }
        return $this->createRequest(ShippingQuoteRequest::class, $this->getParameters() + $parameters);
    }

    public function getServices($parameters = []){
        return $this->createRequest(ServicesRequest::class, $parameters);
    }

    public function supportsCashOnDelivery()
    {
        return true;
    }

    public function supportsCreateBillOfLading(){
        return true;
    }

    public function createBillOfLading($parameters = [])
    {
        if ($parameters instanceof CreateBillOfLadingRequest) {
            return $parameters;
        }
        if (!is_array($parameters)) {
            $parameters = [];
        }
        return $this->createRequest(CreateBillOfLadingRequest::class, $this->getParameters() + $parameters);
    }
    public function cancelBillOfLading($bol_id)
    {
        $this->setBolId($bol_id);
        return $this->createRequest(CancelBillOfLadingRequest::class, $this->getParameters());
    }
    public function getPdf($bol_id)
    {
        return $this->createRequest(GetPdfRequest::class, $this->setBolId($bol_id)->getParameters());
    }
    public function trackingUrl($parcel_id)
    {
        $explode = explode('|', $parcel_id);
        return static::TRACKING_URL.$explode[0];
    }

    public function trackingParcel($bol_id)
    {
        return $this->createRequest(TrackingParcelRequest::class, $this->setBolId($bol_id)->getParameters());
    }

    public function codPayment($bol_id)
    {
        return $this->createRequest(CodPaymentRequest::class, $this->setBolId($bol_id)->getParameters());
    }
}
