<?php
use Phalcon\Mvc\Controller;
use Phalcon\Http\Response;
class TokenController extends ControllerBaseAuth
{
    /**
* @api {post} /token/create Create token
* @apiName Create token
* @apiGroup Token
*
* @apiParam {string} username
* 

* @apiSuccessExample {json} Success-Response:
*     HTTP/1.1 200 OK
*    {"status":"SUCCESS","messages":"Verification token created successfuly!","token":"9f5d"}
*/
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
                    $token->token_value = substr($token->token_value, 0, 6);
                    $token->user_id = $user->user_id;
                    $token->valid_until = date("Y-m-d H:i:s", strtotime('+5 minute'));
                    if ($token->save()) {
                        $response->setStatusCode(201, "Created");
                        $response->setJsonContent(array('status' => 'SUCCESS', 'messages' => 'Verification token created successfuly!', "token" => $token->token_value,"validUntil" => 5));
                    } else {
                        $response->setStatusCode(400, "Unexpected error");
                        $response->setJsonContent(array('status' => 'ERROR', 'messages' => 'Creation failed!'));
                    }
                }else{
                    $t->valid_until = date("Y-m-d H:i:s", strtotime('+15 minute'));
                    if ($t->update()) {
                        $response->setStatusCode(200, "Ok");
                        $response->setJsonContent(array('status' => 'SUCCESS', 'messages' => 'User has active token! Reissued!', 'token'=>$t->token_value,"validUntil" => 15));
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
