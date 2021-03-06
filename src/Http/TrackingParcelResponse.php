<?php

namespace Omniship\Berry\Http;
use Omniship\Common\Component;
use Omniship\Common\EventBag;
use Omniship\Common\TrackingBag;

class TrackingParcelResponse extends AbstractResponse
{
    public function getData()
    {
        if(is_null($this->data)){
            return $this->data;
        }
        $result = new TrackingBag();

        $row = 0;
        foreach($this->data as $quote) {
            $result->push([
                'id' => md5($quote->updated_at),
                'name' => null,
                'events' => $this->_getEvents($quote->status),
                'shipment_date' => null,
                'estimated_delivery_date' => null,
                'origin_service_area' => null,
                'destination_service_area' => null,
            ]);
            $row++;
        }

        return $result;
    }

    protected function _getEvents( $data)
    {
        $result = new EventBag();
            $result->push(new Component([
                'id' => $data,
                'name' => null,
            ]));
        return $result;
    }
}
