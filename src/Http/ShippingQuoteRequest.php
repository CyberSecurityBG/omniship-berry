<?php

namespace Omniship\Berry\Http;
use Omniship\Berry\Client;

class ShippingQuoteRequest extends AbstractRequest
{

    public function getData()
    {
        $SenderAddress = $this->getSenderAddress();
        if($SenderAddress->getId()){
            $pickup = [
                'warehouse' => '831e45f5-b668-447a-9655-177d49467437'
            ];
        } else {
            $SenderAddressline1 = $SenderAddress->getStreet()->getName() . ' ' . $SenderAddress->getStreetNumber();
            $SenderAddressline2 = !is_null($SenderAddress->getQuarter()->getName()) ? $SenderAddress->getQuarter()->getName() . ', ' : '';
            $SenderAddressline2 .= !is_null($SenderAddress->getBuilding()) ? $SenderAddress->getBuilding() . ', ' : '';
            $SenderAddressline2 .= !is_null($SenderAddress->getEntrance()) ? $SenderAddress->getEntrance() . ', ' : '';
            $SenderAddressline2 .= !is_null($SenderAddress->getFloor()) ? $SenderAddress->getFloor() . ', ' : '';
            $SenderAddressline2 .= !is_null($SenderAddress->getApartment()) ? $SenderAddress->getApartment() : '';
            $SenderNote = $SenderAddress->getNote() ? implode(',', $SenderAddress->getNote()) : '';
            $pickup = [
                'address' => [
                    'line1' => $SenderAddressline1,
                    'line2' => $SenderAddressline2,
                    'comment' => $SenderNote,
                    'lon' => $SenderAddress->getLongitude(),
                    'lat' => $SenderAddress->getLatitude()
                ],
                'contact' => [
                    'name' => $SenderAddress->getFullName(),
                    'phone' => $SenderAddress->getPhone(),
                    'email' => ''
                ],
            ];
        }

        $ReceiverAddress = $this->getReceiverAddress();
        $Receiverline1 =  $ReceiverAddress->getStreet()->getName().' '.$ReceiverAddress->getStreetNumber();
        $Receiverline2 = !is_null($ReceiverAddress->getQuarter()) ? $ReceiverAddress->getQuarter()->getName().', ' : '';
        $Receiverline2 .= !is_null($ReceiverAddress->getBuilding()) ? $ReceiverAddress->getBuilding().', ' : '';
        $Receiverline2 .= !is_null($ReceiverAddress->getEntrance()) ? $ReceiverAddress->getEntrance().', ' : '';
        $Receiverline2 .= !is_null($ReceiverAddress->getFloor()) ? $ReceiverAddress->getFloor().', ' : '';
        $Receiverline2 .= !is_null($ReceiverAddress->getApartment()) ? $ReceiverAddress->getApartment() : '';
        $ReceiverNote =   $ReceiverAddress->getNote() ? implode(',', $ReceiverAddress->getNote()) : '';

        $items = [];
        foreach($this->getPieces() as $piece){
            $items[] = [
                'id' => $piece->id,
                'width' => $piece->width,
                'height' => $piece->height,
                'depth' => $piece->depth,
                'weight' => $piece->weight
            ];
        }
        $data['job']['packages'][] = [
            'pickup' => $pickup,
            'dropoff' => [
                'address' => [
                    'line1' => $Receiverline1,
                    'line2' => $Receiverline2,
                    'comment' => $ReceiverNote,
                    'lon' => $ReceiverAddress->getLongitude(),
                    'lat' => $ReceiverAddress->getLatitude()
                ],
                'contact' => [
                    'name' => $ReceiverAddress->getFullName(),
                    'phone' => $ReceiverAddress->getPhone(),
                    'email' => ''
                ]
            ],
            'meta' => [
                'cod' => $this->getCashOnDeliveryAmount() ?? '',
                'reference_id' => $this->getOtherParameters('order'),
                'description' => $this->getContent(),
                'items' => $items
            ]
        ];
        return $data;
    }

    public function sendData($data)
    {
        $query = $this->getClient()->SendRequest('post', 'jobs/inquire', $data);
        return $this->createResponse($query);
    }

    /**
     * @param $data
     * @return ShippingQuoteResponse
     */
    protected function createResponse($data)
    {
        return $this->response = new ShippingQuoteResponse($this, $data);
    }

}
