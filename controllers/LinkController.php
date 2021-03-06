<?php
use Phalcon\Mvc\Controller;
use Phalcon\Http\Response;
class LinkController extends ControllerBaseAuth
{

    //  if (!$this->authorizeRequest()) return;

 /**
 * @api {post} /link/create Create Link
 * @apiName LinkMe
 * @apiGroup Link
 *
 * @apiParam {string} username
 *  @apiParam {string} name
 *  @apiParam {string} url
 * @apiSuccessExample {json} Success-Response:
 *     HTTP/1.1 200 OK
 *     {"status":"SUCCESS","messages":"Link created successfuly!"}
 */
    public function create()
    {
        if (!$this->authorizeRequest()) return;
        try {
            $response = new Response();
            $body = $this->request->getRawBody();
            $data = json_decode($body, true);
            $username = $data['username'];
            $linkName = $data['name'];
            $url = $data['url'];
            $user = User::findFirst(array(
                "conditions" => "username LIKE '" . $username . "'"
            ));
            // 			Check if user already exists
            if ($user != null) {
                $link = new Link();
                $link->name = $linkName;
                $link->url = $url;
                $link->user_id = $user->user_id;
                $link->timestamp = date("Y-m-d H:i:s");
                //Save the new user
                if ($link->save()) {
                    $response->setStatusCode(201, "Created");
                    $response->setJsonContent(array('status' => 'SUCCESS', 'messages' => 'Link created successfuly!'));
                } else {
                    $response->setStatusCode(400, "Unexpected error");
                    $response->setJsonContent(array('status' => 'ERROR', 'messages' => 'Creation failed!'));
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
            $response->setJsonContent(array('status' => 'ERROR', 'messages' => 'Unexpected error occurred!' . $e));
        } finally {
            return $response;
        }
    }

/**
 * @api {get} /link/user/{id} Get links for user
 * @apiName  GetLinks
 * @apiGroup Link
 *
 * @apiParam {string} userid

 * @apiSuccessExample {json} Success-Response:
 *     HTTP/1.1 200 OK
 *    [{"linkId":"1","name":"testic haste","url":"https:\/\/hastebin.com\/agevobotin","timestamp":"2017-11-10 21:48:59","userId":"2"}]
 */
    public function getAllLinksForUID($userId)
    {
        if (!$this->authorizeRequest()) return;
        try {
            $response = new Response();
            $links = Link::find(array(
                "conditions" => "user_id LIKE '" . $userId . "'"
            ));
            if ($links != null) {
                $response->setStatusCode(200, "Ok");
                $response->setJsonContent($links);
            } else {
                $response->setStatusCode(409, "Conflict");
                $response->setJsonContent(array('status' => 'ERROR', 'messages' => 'No links found!'));
            }
        } catch (InvalidRESTParameterException $e) {
            $response->setStatusCode(400, "Malformed request");
            $response->setJsonContent($e->jsonSerialize());
        } catch (Exception $e) {
            $response->setStatusCode(400, "Unexpected error");
            $response->setJsonContent(array('status' => 'ERROR', 'messages' => 'Unexpected error occurred!' . $e));
        } finally {
            return $response;
        }
    }
/**
* @api {get} /link/delete Delete link
* @apiName DeleteLinks
* @apiGroup Link
*
* @apiParam {integer} linkId
* 

* @apiSuccessExample {json} Success-Response:
*     HTTP/1.1 200 OK
*    {"status":"SUCCESS","messages":"Link deleted!"}
*/
    public function deleteLink()
    {
        if (!$this->authorizeRequest()) return;
        try {
            $response = new Response();
            $body = $this->request->getRawBody();
            $data = json_decode($body, true);
            $linkId = $data['linkId'];
            $link = Link::findFirst($linkId);
            if ($link != null) {
                if ($link->delete()) {
                    $response->setStatusCode(200, "Ok");
                    $response->setJsonContent(array('status' => 'SUCCESS', 'messages' => 'Link deleted!'));
                } else {
                    $response->setStatusCode(400, "Unexpected error");
                }
            } else {
                $response->setStatusCode(409, "Conflict");
                $response->setJsonContent(array('status' => 'ERROR', 'messages' => 'No links found!'));
            }
        } catch (InvalidRESTParameterException $e) {
            $response->setStatusCode(400, "Malformed request");
            $response->setJsonContent($e->jsonSerialize());
        } catch (Exception $e) {
            $response->setStatusCode(400, "Unexpected error");
            $response->setJsonContent(array('status' => 'ERROR', 'messages' => 'Unexpected error occurred!' . $e));
        } finally {
            return $response;
        }
    }
/**
 * @api {post} /link/find Get links for user by name
 * @apiName FindLinks
 * @apiGroup Link
 *
 * @apiParam {string} userid
 * @apiParam {string} name Link name
 * 
 * @apiSuccessExample {json} Success-Response:
 *     HTTP/1.1 200 OK
 *    [{"linkId":"1","name":"testic haste","url":"https:\/\/hastebin.com\/agevobotin","timestamp":"2017-11-10 21:48:59","userId":"2"}]
 */

    public function getLinksByName()
    {
        if (!$this->authorizeRequest()) return;
        try {
            $response = new Response();
            $body = $this->request->getRawBody();
            $data = json_decode($body, true);
            $linkName = $data['name'];
            $userId = $data['userId'];
            $links = Link::find(array(
                "conditions" => "name LIKE '%" . $linkName . "%' AND user_id='" . $userId . "'"
            ));
            if ($links != null) {
                $response->setStatusCode(200, "Ok");
                $response->setJsonContent($links);
            } else {
                $response->setStatusCode(409, "Conflict");
                $response->setJsonContent(array('status' => 'ERROR', 'messages' => 'No links found!'));
            }
        } catch (InvalidRESTParameterException $e) {
            $response->setStatusCode(400, "Malformed request");
            $response->setJsonContent($e->jsonSerialize());
        } catch (Exception $e) {
            $response->setStatusCode(400, "Unexpected error");
            $response->setJsonContent(array('status' => 'ERROR', 'messages' => 'Unexpected error occurred!' . $e));
        } finally {
            return $response;
        }
    }

/**
 * @api {get} /link/edit Edit link
 * @apiName EditLinks
 * @apiGroup Link
 *
 * @apiParam {integer} linkId
 * @apiParam {string} linksProperties properties
 * 

 * @apiSuccessExample {json} Success-Response:
 *     HTTP/1.1 200 OK
 *    {"status":"SUCCESS","messages":"Link edited successfuly!"}
 */
    public function editLink()
    {
        if (!$this->authorizeRequest()) return;
        try {
            $response = new Response();
            $body = $this->request->getRawBody();
            $data = json_decode($body, true);
            $linkId = $data['linkId'];
            $link = Link::findFirst($linkId);
            if ($link != null) {
                if (isset($data['name'])) {
                    $link->name = $data['name'];
                }
                if (isset($data['url'])) {
                    $link->url = $data['url'];
                }
                if ($link->update()) {
                    $response->setStatusCode(200, 'OK');
                    $response->setJsonContent(array('status' => 'SUCCESS', 'messages' => 'Link edited successfuly!'));
                } else {
                    $response->setStatusCode(400, "Unexpected error");
                    $response->setJsonContent(array('status' => 'ERROR', 'messages' => 'Link not updated!'));
                }
            } else {
                $response->setStatusCode(409, "Conflict");
                $response->setJsonContent(array('status' => 'ERROR', 'messages' => 'No links found!'));
            }
        } catch (InvalidRESTParameterException $e) {
            $response->setStatusCode(400, "Malformed request");
            $response->setJsonContent($e->jsonSerialize());
        } catch (Exception $e) {
            $response->setStatusCode(400, "Unexpected error");
            $response->setJsonContent(array('status' => 'ERROR', 'messages' => 'Unexpected error occurred!' . $e));
        } finally {
            return $response;
        }
    }
}
?>
