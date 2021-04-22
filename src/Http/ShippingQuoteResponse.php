<?php

namespace Omniship\Berry\Http;
use Carbon\Carbon;
use Illuminate\Auth\Access\Gate;
use Omniship\Common\ShippingQuoteBag;
class ShippingQuoteResponse extends AbstractResponse
{
    public function getData()
    {
        if(!$this->data){
            return $this->data;
        }
        $result = new ShippingQuoteBag();
        $services =  $this->getClient()->SendRequest('get', 'packages/next_available_slots?count=6');
        if(is_null($this->data->pricing->total_gross) || $this->data->pricing->total_gross == 0){
            $price = 0.00;
        } else {
            $price = $this->data->pricing->total_gross;
        }
        if($this->data->is_service == 1) {
            foreach ($services as $service) {
                $ServivePickUp = Carbon::createFromTimeString($service[0], 'UTC');
                $ServiceId = $ServivePickUp->format('Y-m-d_H-i');
                $ServivePickUp->setTimezone('Europe/Sofia');
                $ServiceDropOff = Carbon::createFromTimeString($service[1], 'UTC');
                $ServiceId = $ServiceId . '__' . $ServiceDropOff->format('Y-m-d_H-i');
                $ServiceDropOff->setTimezone('Europe/Sofia');
                $result->push([
                    'id' => $ServiceId,
                    'name' => 'Доставка на ' . $ServivePickUp->format('d.m.Y') . ' от ' . $ServivePickUp->format('H:i') . ' до ' . $ServiceDropOff->format('H:i') . ' (' . count($this->data->packages) . ' пакет/а)',
                    'description' => null,
                    'price' => $price,
                    'pickup_date' => Carbon::createFromFormat('d.m.Y', date_format(date_create($this->data->packages[0]->dropoff_window_start), 'd.m.Y')),
                    'pickup_time' => Carbon::createFromFormat('d.m.Y', date_format(date_create($this->data->packages[0]->dropoff_window_start), 'd.m.Y')),
                    'delivery_date' => Carbon::createFromFormat('d.m.Y', date_format(date_create($service[0]), 'd.m.Y')),
                    'delivery_time' => Carbon::createFromFormat('d.m.Y', date_format(date_create($service[0]), 'd.m.Y')),
                    'currency' => $this->data->pricing->currency,
                    'tax' => null,
                    'insurance' => 0,
                    'exchange_rate' => null,
                    'payer' => 'SENDER',
                    'allowance_fixed_time_delivery' => false,
                    'allowance_cash_on_delivery' => true,
                    'allowance_insurance' => false,
                ]);
            }
        } else {
            $result->push([
                'id' => 1,
                'name' => 'Доставка до адрес',
                'description' => null,
                'price' => $price,
                'pickup_date' => Carbon::createFromFormat('d.m.Y', date_format(date_create($this->data->packages[0]->dropoff_window_start), 'd.m.Y')),
                'pickup_time' => Carbon::createFromFormat('d.m.Y', date_format(date_create($this->data->packages[0]->dropoff_window_start), 'd.m.Y')),
                'delivery_date' => null,
                'delivery_time' => null,
                'currency' => $this->data->pricing->currency,
                'tax' => null,
                'insurance' => 0,
                'exchange_rate' => null,
                'payer' => 'SENDER',
                'allowance_fixed_time_delivery' => false,
                'allowance_cash_on_delivery' => true,
                'allowance_insurance' => false,
            ]);
        }
        return $result;
    }
}
