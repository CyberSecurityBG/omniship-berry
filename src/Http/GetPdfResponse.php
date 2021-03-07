<?php

namespace Omniship\Berry\Http;

class GetPdfResponse extends AbstractResponse
{

    /**
     * @return bool
     */
    public function getData(){
        if(is_null($this->data)) {
            return $this->data;
        }
        $pdf = isset($this->data->packages[0]->shipping_label_url) ? file_get_contents($this->data->packages[0]->shipping_label_url) : file_get_contents($this->data->shipping_label_url);
        return $pdf;
    }

}
