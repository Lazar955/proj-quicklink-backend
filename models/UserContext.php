<?php



class UserContext
{
    public $issuer = "laki";
    public $user_id;
    public $token_expire_timestamp;
    public $approved;


    public function getToken()
    {

        $this->token_expire_timestamp = 1599320872;

        $payload = array(
            "iss" => $this->issuer,
            "exp" => $this->token_expire_timestamp,                            //calculate timestamp of expiring token
            "uid" => $this->user_id,                                     //user id
        );


        $jwt = JWT::encode($payload, JWT::$key);


        return $jwt;
    }

    public function setPayload($payload)
    {

        $this->user_id = $payload['uid'];
        $this->token_expire_timestamp = $payload['exp'];
    }


}