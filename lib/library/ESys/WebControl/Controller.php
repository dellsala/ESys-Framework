<?php

require_once 'ESys/WebControl/Response.php';
require_once 'ESys/WebControl/ResponseFactory.php';


/**
 * @package ESys
 */
class ESys_WebControl_Controller
{


    private $responseFactory;
    

    /**
     * Delegates a request to the appropriate action handling method.
     * 
     * @param ESys_WebControl_Request
     * @return ESys_WebControl_Response
     */
    function handleRequest (ESys_WebControl_Request $request)
    {
        $params = $request->actionParameters();
        $action = count($params) ? array_shift($params) : null;
        if ($action == 'index' 
            || $action == 'default'
            || $action == 'forbidden')
        {
            $action = null;
            array_unshift($params, $action);
        }
        if (! $this->isAuthorized($request)) {
            $actionMethod = 'doForbidden';
        } else if (empty($action) && ! count($params)) {
            $actionMethod = 'doIndex';
        } else if (empty($action)) {
            $actionMethod = 'doDefault';
        } else {
            $actionMethod = 'do'.str_replace(' ', '', ucwords(str_replace('-', ' ', $action)));
        }
        if (! method_exists($this, $actionMethod)) {
            $actionMethod = 'doDefault';
        }
        $response = $this->$actionMethod($request);
        return $response;
    }


    /**
     * @param ESys_WebControl_Request
     * @return boolean
     */
    protected function isAuthorized (ESys_WebControl_Request $request)
    {
        return true;
    }


    /**
     * Forbidden action.
     *
     * @param ESys_WebControl_Request
     * @return ESys_WebControl_Response
     */
    protected function doForbidden (ESys_WebControl_Request $request)
    {
        return $this->getResponseFactory()->build('forbidden', array());
    }


    /**
     * Index action
     *
     * @param ESys_WebControl_Request
     * @return ESys_WebControl_Response
     */
    protected function doIndex ($request)
    {
        return $this->doDefault($request);
    }


    /**
     * Default action
     *
     * @param ESys_WebControl_Request
     * @return ESys_WebControl_Response
     */
    protected function doDefault ($request)
    {
        return $this->getResponseFactory()->build('notFound', array());
    }


    /**
     * @param ESys_WebControl_ResponseFactory
     * @return ESys_WebControl_Response
     */
    public function setResponseFactory (ESys_WebControl_ResponseFactory $factory)
    {
        $this->responseFactory = $factory;
        $this->responseFactory->setCommonData($this->commonResponseData());
    }


    /**
     * @return ESys_WebControl_ResponseFactory
     */
    protected function getResponseFactory ()
    {
        if (! $this->responseFactory) {
            $this->responseFactory = new ESys_WebControl_ResponseFactory();
        }
        return $this->responseFactory;
    }


    /**
     * Template method that returns common data on the
     * response factory when it is assigned to the controller.
     *
     * Override this in sub-classes to provide response data that will be
     * common to all factory generated responses within the controller.
     *
     * @return array
     */
    protected function commonResponseData ()
    {
        return array();
    }


}


