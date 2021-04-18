<?php

namespace Omniship\Berry;

use Carbon\Carbon;
use GuzzleHttp\Client AS HttpClient;
use http\Client\Response;
use Omniship\Berry\Http\ServicesRequest;

class Client
{

    protected $key;
    protected $error;

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

    protected function SetHeader($ednpoint, $method, $api_key){
        $header['Content-Type'] = 'application/json';
        $header['Accept'] = 'application/vnd.api+json';
        if($ednpoint == 'users' && $method == 'POST'){
        } else {
            $header['X-BERRY-APIKEY'] = $api_key;
        }
        return $header;
    }

    public function SendRequest($method, $endpoint, $data = [], $ignore = null, $key = null){
        if(is_null($key)){
            $key = $this->key;
        }
        try {
            $client = new HttpClient(['base_uri' => $this->getServiceEndpoint()]);
            $response = $client->request($method, $endpoint, [
                'json' => $data,
                'headers' => $this->SetHeader($endpoint, $method, $this->key)
            ]);
            return json_decode($response->getBody()->getContents());
        } catch (\Exception $e){
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
      // return static::SERVICE_TESTING_URL;
         return static::SERVICE_PRODUCTION_URL;
    }
    public function CreateUser($data){
        $SendRequest = $this->SendRequest('POST', 'users', $data);
        if($SendRequest != null){
            return $SendRequest->api_app_keys[0];
        } else {
            return json_decode($this->error['error']);
        }
    }
    public function GetWarehouse($api_key, $warehouse_id){
        return $this->SendRequest('GET', 'addresses/'.$warehouse_id,'', '' ,$api_key);
    }
    public function GetWarehouses($api_key){
        return $this->SendRequest('GET', 'addresses','', '' ,$api_key);
    }
    public function RemoveWarehouse($api_key, $warehouse_id){
        return $this->SendRequest('DELETE', 'addresses/'.$warehouse_id,'', '' ,$api_key);
    }
    public function AddWarehouse($api_key, $data){
        return $this->SendRequest('POST', 'addresses', $data ,$api_key);
    }
    public function EditWarehouse($api_key, $id, $data){
        return $this->SendRequest('PUT', 'addresses/'.$id, $data ,$api_key);
    }
    public function GetProfile($api_key){
        return $this->SendRequest('GET', 'users','', '' ,$api_key);
    }
    public function GetServices(){
        $AvailableSlots = $this->SendRequest('get', 'packages/next_available_slots?count=6');
        $slots = [];
        foreach ($AvailableSlots as $service) {
            $ServivePickUp = Carbon::createFromTimeString($service[0], 'UTC');
            $ServiceId = $ServivePickUp->format('Y-m-d_H-i');
            $ServivePickUp->setTimezone('Europe/Sofia');
            $ServiceDropOff = Carbon::createFromTimeString($service[1], 'UTC');
            $ServiceId = $ServiceId . '__' . $ServiceDropOff->format('Y-m-d_H-i');
            $ServiceDropOff->setTimezone('Europe/Sofia');
            $slots[] = [
                'id' => $ServiceId,
                'name' => 'Доставка на ' . $ServivePickUp->format('d.m.Y') . ' от ' . $ServivePickUp->format('H:i') . ' до ' . $ServiceDropOff->format('H:i'),
            ];
        }
        return $slots;
    }

    public function GetCash($api_key, $page){
        return $this->SendRequest('GET', 'cod_transactions?page='.$page,'', '' ,$api_key);
    }
}
