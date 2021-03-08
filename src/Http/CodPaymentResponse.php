<?php
/**
 * Created by PhpStorm.
 * User: joro
 * Date: 10.5.2017 г.
 * Time: 17:22 ч.
 */

namespace Omniship\Berry\Http;

use Carbon\Carbon;
use Omniship\Common\CodPayment;

class CodPaymentResponse extends AbstractResponse
{
    /**
     * The data contained in the response.
     *
     * @var \Omniship\Econt\Lib\Response\CodPayment
     */
    protected $data;

    /**
     * @return CodPayment
     */
    public function getData()
    {
        if(!$this->data){
            return $this->data;
        }
        if($this->data->packages[0]->status == 'delivered') {
            $date = date_format(date_create($this->data->ended_at), 'Y-m-d H:i:s');
            $cod_payment = new CodPayment([
                'id' => $this->getRequest()->getBolId(),
                'date' => !empty($date) ? Carbon::createFromFormat('Y-m-d H:i:s',$date, 'Europe/Sofia') : null,
                'price' => $this->data->packages[0]->cod
            ]);
            return $cod_payment;
        }
        return null;
    }

}
