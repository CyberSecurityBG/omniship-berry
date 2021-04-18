<?php

namespace Omniship\Berry\Http;
use Omniship\Berry\Client;

class ShippingQuoteRequest extends AbstractRequest
{

    public function getData()
    {
        $SenderAddress = $this->getSenderAddress();
        if(!is_null($SenderAddress->getId())){
            $pickup = [
                'warehouse' => $SenderAddress->getId()
            ];
        } else {
            $SenderAddressline1 = $SenderAddress->getCity()->getName().', ';
            $SenderAddressline1 .= $SenderAddress->getStreet()->getName() . ' ' . $SenderAddress->getStreetNumber();
            $SenderAddressline2 = !empty($SenderAddress->getQuarter()->getName()) ? 'жк. '.$SenderAddress->getQuarter()->getName() . ', ' : '';
            $SenderAddressline2 .= !empty($SenderAddress->getBuilding()) ? 'бл. '.$SenderAddress->getBuilding() . ', ' : '';
            $SenderAddressline2 .= !empty($SenderAddress->getEntrance()) ? 'вх. '.$SenderAddress->getEntrance() . ', ' : '';
            $SenderAddressline2 .= !empty($SenderAddress->getFloor()) ? 'ет. '.$SenderAddress->getFloor() . ', ' : '';
            $SenderAddressline2 .= !empty($SenderAddress->getApartment()) ? 'ап. '.$SenderAddress->getApartment() : '';
            $SenderNote = $SenderAddress->getNote() ? $SenderAddress->getNote() : '';
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
        $Receiverline1 = $ReceiverAddress->getCity()->getName().', ';
        $Receiverline1 .=  $ReceiverAddress->getStreet()->getName().' '.$ReceiverAddress->getStreetNumber();
        $Receiverline2 = !empty($ReceiverAddress->getQuarter()) ? 'жк. '.$ReceiverAddress->getQuarter()->getName().', ' : '';
        $Receiverline2 .= !empty($ReceiverAddress->getBuilding()) ? 'бл. '.$ReceiverAddress->getBuilding().', ' : '';
        $Receiverline2 .= !empty($ReceiverAddress->getEntrance()) ? 'вх. '.$ReceiverAddress->getEntrance().', ' : '';
        $Receiverline2 .= !empty($ReceiverAddress->getFloor()) ? 'ет. '.$ReceiverAddress->getFloor().', ' : '';
        $Receiverline2 .= !empty($ReceiverAddress->getApartment()) ? 'ап. '.$ReceiverAddress->getApartment() : '';
        $ReceiverNote =   $ReceiverAddress->getNote() ? $ReceiverAddress->getNote() : '';
        $items = [];
        foreach($this->getItems() as $piece){
            $items[] = [
                'id' => $piece->id,
                'width' => (float)$piece->width,
                'height' => (float)$piece->height,
                'depth' =>  (float)$piece->depth,
                'weight' =>  (float)$piece,
                'quantity' => (int)$piece->quantity
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
        if(!is_null($query)) {
            $query->is_service = $this->getOtherParameters('is_services');
        }
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
