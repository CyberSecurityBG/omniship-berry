<?php

namespace Omniship\Berry\Http;

use Omniship\Common\ServiceBag;

class ServicesResponse extends AbstractResponse
{
    public function getData()
    {
        $result = new ServiceBag();
        if(!empty($this->data)) {
            foreach($this->data AS $services) {
                $Date = date_format(date_create($services[0]), 'd.m.Y');
                $From = date_format(date_create($services[0]), 'H:i');
                $To = date_format(date_create($services[1]), 'H:i');
                $result->push([
                    'id' => json_encode($services),
                    'name' => 'Доставка на '.$Date.' от '.$From.' до '.$To,
                ]);
            }
        }
        return $result;
    }

}
