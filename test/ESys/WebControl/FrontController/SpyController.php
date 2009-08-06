<?php

require_once 'ESys/WebControl/Controller.php';
require_once 'ESys/WebControl/Response.php';


class ESys_WebControl_FrontController_SpyController extends ESys_WebControl_Controller {

    public function doAction ($request)
    {
        $response = new ESys_WebControl_FrontController_SpyResponse('body');
        $response->request = $request;
        return $response;
    }

}


class ESys_WebControl_FrontController_SpyResponse extends ESys_WebControl_Response {

    public $request = null;

}

