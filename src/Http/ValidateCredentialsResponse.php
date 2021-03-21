<?php

namespace Omniship\Berry\Http;

class ValidateCredentialsResponse extends AbstractResponse
{

    /**
     * @return bool
     */
    public function getData()
    {
        return $this->data[0];
    }

}
