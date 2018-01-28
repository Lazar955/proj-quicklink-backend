<?php
use Phalcon\Mvc\Controller;
use Phalcon\Http\Response;
class UserController extends ControllerBaseAuth
{
/**
* @api {post} /user/register Register user
* @apiName Create user
* @apiGroup User
*
* @apiParam {string} username
*  @apiParam {string} password

* @apiSuccessExample {json} Success-Response:
*     HTTP/1.1 200 OK
*    {"status":"SUCCESS","messages":"User created successfuly!"}
*/
    public function register()
    {
        try {
            $response = new Response();
            // $username = $this->getRequestParam("username", "string", true);
            // $pass = $this->getRequestParam("password", "string", true);
            $body = $this->request->getRawBody();
            $data = json_decode($body, true);
            $username = $data['username'];
            $pass = $data['password'];
            $user = User::findFirst(array(
                "conditions" => "username LIKE '" . $username . "'"
            ));
            if ($user == null) {
                $user = new User();
                $user->username = $username;
                $user->password = $this->security->hash($pass);
                if ($user->save()) {
                    $response->setStatusCode(201, "Created");
                    $response->setJsonContent(array('status' => 'SUCCESS', 'messages' => 'User created successfuly!'));
                } else {
                    $response->setStatusCode(400, "Unexpected error");
                    $response->setJsonContent(array('status' => 'ERROR', 'messages' => 'Register of new user failed!'));
                }
            } else {
                $response->setStatusCode(409, "Conflict");
                $response->setJsonContent(array('status' => 'ERROR', 'messages' => 'User with given username already exist!'));
            }
        } catch (InvalidRESTParameterException $e) {
            $response->setStatusCode(400, "Malformed request");
            $response->setJsonContent($e->jsonSerialize());
        } catch (Exception $e) {
            $response->setStatusCode(400, "Unexpected error");
            $response->setJsonContent(array('status' => 'ERROR', 'messages' => 'Unexpected error occurred!'));
        } finally {
            return $response;
        }
    }
    /**
* @api {get} /user/{id} Get user
* @apiName Get user
* @apiGroup User
*
* @apiParam {id} userid
* @apiSuccessExample {json} Success-Response:
*     HTTP/1.1 200 OK
*    {"user_id":"2","username":"laki"}
*/
    public function getUserById($id)
    {
         if (!$this->authorizeRequest()) return;
        $user = User::findFirst($id);
        $response = new Response();
        if ($user) {
            $response->setStatusCode(200, "OK");
            $response->setJsonContent($user);
        } else {
            $response->setStatusCode(400, "Unexpected error");
        }
        return $response;
    }
/**
* @api {post} /user/login Login username password
* @apiName Login UP
* @apiGroup User
*
* @apiParam {string} username
*  @apiParam {string} password

* @apiSuccessExample {json} Success-Response:
*     HTTP/1.1 200 OK
*    {"BearerToken":"Bearer token here","user":{"user_id":"2","username":"laki","password":""}}
*/
    public function login()
    {
        try {
            $response = new Response();
            $body = $this->request->getRawBody();
            $data = json_decode($body, true);
            $username = $data['username'];
            $password = $data['password'];
            $user = User::findFirst("username = '" . $username . "'");
            if ($user) {
                if ($this->security->checkHash($password, $user->password)) {
                    $uc = new UserContext();
                    $uc->user_id = $user->user_id;
                    $responseData = array();
                    $responseData["BearerToken"] =  $uc->getToken();
                    $user->password = "";
                    $responseData["user"] = $user;
                    $response->setJsonContent($responseData);
                } else {
                    $response->setStatusCode(401, "Unauthorized");
                }
            } else {
                $response->setStatusCode(400, "Unexpected error");
                $response->setJsonContent(array('status' => 'ERROR', 'messages' => 'Wrong credentials!'));
            }
        } catch (InvalidRESTParameterException $e) {
            $response->setStatusCode(400, "Malformed request");
            $response->setJsonContent($e->jsonSerialize());
        } catch (Exception $e) {
            $response->setStatusCode(400, "Unexpected error");
            $response->setJsonContent(array('status' => 'ERROR', 'messages' => 'Unexpected error occurred!'));
        } finally {
            return $response;
        }
    }
        /**
* @api {post} /user/loginWithToken Login with token
* @apiName Login token
* @apiGroup User
*
* @apiParam {string} tokenValue
*  @apiParam {string} device
*  @apiParam {string} ip
* @apiSuccessExample {json} Success-Response:
*     HTTP/1.1 200 OK
*    {"BearerToken":"Bearer token here","user":{"user_id":"2","username":"laki","password":""}}
*/
    public function loginWithToken()
    {
        try {
            $response = new Response();
            $body = $this->request->getRawBody();
            $data = json_decode($body, true);
            $tokenValue = $data['tokenValue'];
            $device = $data['device'];
            $ip = $data['ip'];
            $token = Token::findFirst("token_value = '" . $tokenValue . "'");
            if ($token != null) {
                if ($token->valid_until > date("Y-m-d H:i:s")) {
                    $user = User::findFirst($token->user_id);
                    if ($user != null) {
                        $uc = new UserContext();
                        $uc->user_id = $user->user_id;
                        $response->setHeader("Auth", "Bearer " . $uc->getToken());
                        $response->setStatusCode(200, "OK");
                        $responseData = array();
                       // $responseData["BearerToken"] = "Bearer " . $uc->getToken();
                        $responseData["BearerToken"] = $uc->getToken();
                        $user->password = "";
                        $responseData["user"] = $user;
                        $response->setJsonContent($responseData);
                        if ($token->delete() == false) {
                            $response->setStatusCode(400, "Unexpected error");
                            $response->setJsonContent("");
                        }
                        if(!$this->createLog($user->user_id,$device,$ip)){
                            $response->setStatusCode(400, "Unexpected log error");
                            $response->setJsonContent("Logger error!");
                        }
                    } else {
                        $response->setStatusCode(400, "Unexpected error");
                        $response->setJsonContent("User no found");
                    }
                } else {
                    $response->setStatusCode(400, "Ok");
                    $response->setJsonContent(array('status' => 'ERROR', 'messages' => 'Token expired!'));
                }
            } else {
                $response->setStatusCode(400, "Unexpected error");
                $response->setJsonContent(array('status' => 'ERROR', 'messages' => 'Invalid token!'));
            }
        } catch (InvalidRESTParameterException $e) {
            $response->setStatusCode(400, "Malformed request");
            $response->setJsonContent($e->jsonSerialize());
        } catch (Exception $e) {
            $response->setStatusCode(400, "Unexpected error");
            $response->setJsonContent(array('status' => 'ERROR', 'messages' => 'Unexpected error occurred!'));
        } finally {
            return $response;
        }
    }

    private static function createLog($userId,$device,$ip)
    {
        $log = new Log();
        $log->timestamp = date("Y-m-d H:i:s");
        $log->device = $device;
        $log->ip_address = $ip;
         $log->user_id = $userId;
        if($log->save()){
            return true;
        }
        return false;
    }
}
?>
