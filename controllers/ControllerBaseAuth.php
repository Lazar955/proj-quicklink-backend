<?php
use Phalcon\Mvc\Controller;
use Phalcon\Http\Response;

class ControllerBaseAuth extends Controller
{
    public $uc;
    public $data = null;

    public function authorizeRequest()
    {
        $request = $this->di->get("request");

        // Authorization of users and creation of UserContext, Use with every request
        $uc = new UserContext();
        if (!AuthHandler::authorizeRequest($request, $uc)) {
            $response = new Response();
            $response->setStatusCode(401, "Unauthorized!!");
            $response->setJsonContent(array('status' => 'ERROR', 'messages' => 'User is not authorized!!'));
            $response->send();
            return false;
        }
        return true;
    }

    public function getRequestParam($pname, $ptype, $required, $defaultint = 0)
    {

        if ($this->data == null) {
            $this->data = $this->request->getQuery();

            //join with post params
            if ($this->request->isPost())
                $this->data = array_merge($this->data, $this->request->getPost());
        }

        if ($required && !isset($this->data[$pname]))
            throw new InvalidRESTParameterException(400, "Field '$pname' not supplied.");
        //validate by type
        switch ($ptype) {
            case "string" :
                if (isset($this->data[$pname]))
                    return ltrim($this->data[$pname]);
                else
                    return "";
                break;
            case "int" :
                if (isset($this->data[$pname]))
                    return (int)$this->data[$pname];
                else
                    return (int)$defaultint;
                break;
        }

    }


}