<?php

use Phalcon\Validation;

class Token extends \Phalcon\Mvc\Model
{
    public $token_id;
    public $token_value;
    public $valid_until;
    public $user_id;
    public function initialize()
    {

    }

    public function getSource()
    {
        return 'token';
    }

    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    public function jsonSerialize()
    {
        $data = array();

        $data['token_id'] = $this->token_id;
        $data['token_value'] = $this->token_value;
        $data['valid_until'] = $this->valid_until;
        $data['user_id'] = $this->user_id;
     
        return $data;
    }
}
