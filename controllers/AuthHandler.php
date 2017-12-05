<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 12.4.2016
 * Time: 13:08
 */
class AuthHandler
{
    public static function authorizeRequest($request, $uc)
    {

        //authorize request with jwt
        $auth = $request->getHeader("Auth");

        try {
            $uc->approved = false;
            $decoded = JWT::decode($auth, JWT::$key, array('HS256'));


            $uc->approved = true;
            $decoded_array = (array)$decoded;
            $uc->setPayload($decoded_array);
            return true;
        } catch (Exception $e) {

            return false;
        }
    }

}