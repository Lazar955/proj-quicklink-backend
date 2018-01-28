<?php
use Phalcon\Mvc\Controller;
use Phalcon\Http\Response;
class LogController extends ControllerBaseAuth
{

/**
 * @api {get} /logs/:userId Request logs
 * @apiName LinkMe
 * @apiGroup Logs
 *
 * @apiParam {Number} userId Unique id
 * @apiSuccessExample {json} Success-Response:
 *     HTTP/1.1 200 OK
 *     [{"log_id":"1","timestamp":"2018-01-07 21:15:34","device":"postman","user_id":"2","ip_address":"192.168.0.1"}]
 */
    public function getLogsByUID($userId)
    {
      //  if (!$this->authorizeRequest()) return;
        try {
            $response = new Response();
            $log = Log::find(array(
                "conditions" => "user_id LIKE '" . $userId . "'"
            ));
            if ($log != null) {
                $response->setStatusCode(200, "Ok");
                $response->setJsonContent($log);
            } else {
                $response->setStatusCode(409, "Conflict");
                $response->setJsonContent(array('status' => 'ERROR', 'messages' => 'No logs found!'));
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
