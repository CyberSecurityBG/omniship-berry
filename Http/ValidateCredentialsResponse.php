<?php

namespace Omniship\Berry\Http;

class ValidateCredentialsResponse extends AbstractResponse
{

    /**
     * @return bool
     */
    public function getData()
    {

        return (bool)$this->data;
    }

}
