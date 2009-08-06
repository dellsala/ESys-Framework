<?php

require_once 'ESys/WebControl/Request.php';

class ESys_WebControl_ControllerTest_RequestBuilder {

    public function createFromAction ($action)
    {
        $actionParameters = $action ? array($action) : array();
        return new ESys_WebControl_Request(array(
            'actionParameters' => $actionParameters
        ));
    }

}
