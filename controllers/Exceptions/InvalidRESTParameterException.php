<?php

class InvalidRESTParameterException extends Exception
{
    public $responsecode;
    public $responsemessage;
    public $responseJSON;
    public $paramName;

    function InvalidRESTParameterException($code, $wrongparam)
    {
        $this->responsecode = $code;
        $this->responsemessage = "Field '$wrongparam' is invalid!";
        $this->paramName = $wrongparam;
    }

    function jsonSerialize()
    {
        $data = array();

        $data['status'] = 'Invalid REST Parameters';
        $data['messages'] = $this->responsemessage;

        return $data;
    }
}