<?php
/**
 * Created by PhpStorm.
 * User: joro
 * Date: 10.5.2017 г.
 * Time: 16:55 ч.
 */

namespace  Omniship\Berry\Http;

class CreateBillOfLadingRequest extends AbstractRequest
{
    /**
     * @return array
     */
    public function getData() {
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
        $Receiverline1 =  $ReceiverAddress->getStreet()->getName().' '.$ReceiverAddress->getStreetNumber();
        $Receiverline2 = !is_null($ReceiverAddress->getQuarter()) ? $ReceiverAddress->getQuarter()->getName().', ' : '';
        $Receiverline2 .= !is_null($ReceiverAddress->getBuilding()) ? $ReceiverAddress->getBuilding().', ' : '';
        $Receiverline2 .= !is_null($ReceiverAddress->getEntrance()) ? $ReceiverAddress->getEntrance().', ' : '';
        $Receiverline2 .= !is_null($ReceiverAddress->getFloor()) ? $ReceiverAddress->getFloor().', ' : '';
        $Receiverline2 .= !is_null($ReceiverAddress->getApartment()) ? $ReceiverAddress->getApartment() : '';
        $ReceiverNote =   $ReceiverAddress->getNote() ? $ReceiverAddress->getNote() : '';

        $DropoffStart = !is_null($this->getOtherParameters('dropoff')) ? json_decode($this->getOtherParameters('dropoff')) : '';
        $items = [];
        foreach($this->getItems() as $piece){
            $items[] = [
                'id' => $piece->id,
                'width' => (float)$piece->width,
                'height' => (float)$piece->height,
                'depth' => (float)$piece->depth,
                'weight' => (int)$piece->weight
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
                'dropoff_window_start' => $DropoffStart[0],
                'dropoff_window_end' => $DropoffStart[1],
                'items' => $items
            ]
        ];
        return $data;
    }

    public function sendData($data) {
        $query = $this->getClient()->SendRequest('post', 'jobs', $data);
        return $this->createResponse($query);
    }

    /**
     * @param $data
     * @return ShippingQuoteResponse
     */
    protected function createResponse($data)
    {
        return $this->response = new CreateBillOfLadingResponse($this, $data);
    }

}
