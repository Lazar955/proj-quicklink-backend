<?php

use Phalcon\Mvc\Controller;


use Phalcon\Http\Response;


class TokenController extends ControllerBaseAuth
{


    public function create()
    {
        if (!$this->authorizeRequest()) return;
        try {

            $response = new Response();
            $body = $this->request->getRawBody();

            $data = json_decode($body, true);
            $username = $data['username'];

            $user = User::findFirst(array(
                "conditions" => "username LIKE '" . $username . "'"
            ));


            if ($user != null) {

                $t = Token::findFirst(array(
                    "conditions" => "user_id LIKE '" . $user->user_id . "'"
                ));


                if($t == null){
                    $token = new Token();

                    $token->token_value = hash('crc32', uniqid());;
                    $token->token_value = substr($token->token_value, 0, 4);
                    $token->user_id = $user->user_id;
                    $token->valid_until = date("Y-m-d H:i:s", strtotime('+5 minute'));

                    if ($token->save()) {
                        $response->setStatusCode(201, "Created");
                        $response->setJsonContent(array('status' => 'SUCCESS', 'messages' => 'Verification token created successfuly!', "token" => $token->token_value));
                    } else {
                        $response->setStatusCode(400, "Unexpected error");
                        $response->setJsonContent(array('status' => 'ERROR', 'messages' => 'Creation failed!'));
                    }
                }else{
                    $t->valid_until = date("Y-m-d H:i:s", strtotime('+15 minute'));
                    if ($t->update()) {
                        $response->setStatusCode(400, "Unexpected error");
                        $response->setJsonContent(array('status' => 'ERROR', 'messages' => 'User has active token! Reissued!', 'token'=>$t->token_value));
                    } else {
                        $response->setStatusCode(400, "Unexpected error");
                        $response->setJsonContent(array('status' => 'ERROR', 'messages' => 'Creation failed!'));
                    }

                }



            } else {

                $response->setStatusCode(409, "Conflict");
                $response->setJsonContent(array('status' => 'ERROR', 'messages' => 'User not found!'));
            }


        } catch (InvalidRESTParameterException $e) {
            $response->setStatusCode(400, "Malformed request");
            $response->setJsonContent($e->jsonSerialize());
        } catch (Exception $e) {

            $response->setStatusCode(400, "Unexpected error");

        } finally {
            return $response;
        }


    }


}


?>
