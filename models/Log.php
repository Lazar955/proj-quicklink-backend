<?php
use Phalcon\Validation;
class Log extends \Phalcon\Mvc\Model
{
    public $log_id;
    public $timestamp;
    public $device;
    public $user_id;
    public $ip_address;
    public function initialize()
    {
    }
    public function getSource()
    {
        return 'log';
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
        $data['logId'] = $this->log_id;
        $data['timestamp'] = $this->timestamp;
        $data['device'] = $this->device;
        $data['userId'] = $this->user_id;
        $data['ipAddress'] = $this->ip_address;
        
        return $data;
    }
}
