<?php
/**
 * Created by PhpStorm.
 * User: joro
 * Date: 10.5.2017 г.
 * Time: 17:22 ч.
 */

namespace Omniship\Berry\Http;

use Carbon\Carbon;
use Omniship\Common\Bill\Create;

class CreateBillOfLadingResponse extends AbstractResponse
{
    /**
     * @var Parcel
     */
    protected $data;
    /**
     * @return Create
     */
    public function getData()
    {
        if(!$this->data){
            return $this->data;
        }
        $Date = date_format(date_create($this->data->packages[0]->dropoff_window_start), 'd.m.Y');
        $From = date_format(date_create($this->data->packages[0]->dropoff_window_start), 'H:i');
        $To = date_format(date_create($this->data->packages[0]->dropoff_window_end), 'H:i');
        $result = new Create();
        $result->setServiceId('Доставка на '.$Date.' от '.$From.' до '.$To);
        $result->setBolId($this->data->tracking_id.'|'.$this->data->id);
        $result->setBillOfLadingSource(isset($this->data->packages[0]->shipping_label_url) ? base64_encode(file_get_contents($this->data->packages[0]->shipping_label_url)) : base64_encode(file_get_contents($this->data->shipping_label_url)));
        $result->setBillOfLadingType($result::PDF);
        $result->setBillOfLadingUrl(isset($this->data->packages[0]->shipping_label_url) ? $this->data->packages[0]->shipping_label_url : $this->data->shipping_label_url);
        $result->setEstimatedDeliveryDate(Carbon::createFromFormat('d.m.Y', date_format(date_create($this->data->packages[0]->dropoff_window_start), 'd.m.Y')));
        $result->setInsurance(0.0);
        $result->setCashOnDelivery(!empty($this->data->packages[0]->cod) ? $this->data->packages[0]->cod : 0.0);
        $result->setTotal(!empty($this->data->packages[0]->cod) ? $this->data->packages[0]->cod : 0.0);
        $result->setCurrency('BGN');
        return $result;
    }

}
