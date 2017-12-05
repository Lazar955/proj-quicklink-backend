<?php

use Phalcon\Validation;

class User extends \Phalcon\Mvc\Model
{

    public $user_id;
    public $username;
    public $password;
    

    public function initialize()
    {

    }

    public function getSource()
    {
        return 'user';
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

        $data['user_id'] = $this->user_id;
        $data['username'] = $this->username;
        $data['password'] = $this->password;

        return $data;
    }

}
