<?php

use Phalcon\Validation;

class Link extends \Phalcon\Mvc\Model
{
    public $link_id;
    public $name;
    public $url;
    public $timestamp;
    public $user_id;
    public function initialize()
    {

    }

    public function getSource()
    {
        return 'link';
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

        $data['linkId'] = $this->link_id;
        $data['name'] = $this->name;
        $data['url'] = $this->url;
        $data['timestamp'] = $this->timestamp;
        $data['userId'] = $this->user_id;
        

        return $data;
    }
}
