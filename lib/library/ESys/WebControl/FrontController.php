<?php

require_once 'ESys/WebControl/Request.php';
require_once 'ESys/WebControl/ResponseFactory.php';
require_once 'ESys/WebControl/ControllerFactory.php';


/**
 * @package ESys
 */
class ESys_WebControl_FrontController
{


    protected $urlBase;

    protected $scriptPath;

    protected $responseFactory;
    
    protected $controllerFactory;

    private $pathMap = array();


    public function __construct ($urlBase, $scriptPath)
    {
        $this->urlBase = empty($urlBase) ? '' : '/'.trim($urlBase, '/');
        $this->scriptPath = '/'.trim($scriptPath, '/');
    }


    /**
     * @param ESys_WebControl_Request $request
     * @return ESys_WebControl_Response
     */
    protected function handleNotFound (ESys_WebControl_Request $request)
    {
        $responseFactory = $this->getResponseFactory();
        $responseFactory->setRequest($request);
        return $responseFactory->build('notFound', array());
    }


    /**
     * @param ESys_WebControl_Request $request
     * @return ESys_WebControl_Response
     */
    protected function handleError (ESys_WebControl_Request $request)
    {
        $responseFactory = $this->getResponseFactory();
        $responseFactory->setRequest($request);
        return $responseFactory->build('error', array());
    }


    /**
     * @param ESys_WebControl_ResponseFactory
     * @return void
     */
    public function setResponseFactory (ESys_WebControl_ResponseFactory $factory)
    {
        $this->responseFactory = $factory;
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
     * @param ESys_WebControl_ControllerFactory
     * @return void
     */
    public function setControllerFactory (ESys_WebControl_ControllerFactory $factory)
    {
        $this->controllerFactory = $factory;
    }

    
    /**
     * @return ESys_WebControl_ControllerFactory
     */
    protected function getControllerFactory ()
    {
        if (! $this->controllerFactory) {
            $this->controllerFactory = new ESys_WebControl_ControllerFactory();
        }
        return $this->controllerFactory;
    }


    /**
     * @param string $path
     * @param string $className
     * @return void
     */
    public function addPath ($path, $className)
    {
        $this->pathMap[$path] = $className;
    }


    /**
     * @param array PHP's $_GET data
     * @param array PHP's $_POST data
     * @param array PHP's $_SERVER data
     * @return ESys_WebControl_Response
     */
    public function handleRequest ($getData, $postData, $serverData)
    {
        $queryPath = $this->calculateQueryPath($getData, $postData, $serverData);
        $actionParams = array();
        $controllerClassName = null;
        do {
            if (! $controllerClassName = $this->getMappedClassName($queryPath)) {
                array_unshift($actionParams, basename($queryPath));
                $queryPath = dirname($queryPath);
                if ($queryPath == '/') {
                    $controllerClassName = $this->getMappedClassName($queryPath);
                }
            }
        } while (! $controllerClassName && $queryPath != '/');
        $controllerPath = $queryPath;
        $request = new ESys_WebControl_Request(array(
            'basePath' => $this->urlBase,
            'frontControllerPath' => $this->scriptPath,
            'controllerPath' => $controllerPath,
            'actionParameters' => $actionParams,
            'getData' => $getData,
            'postData' => $postData,
            'serverData' => $serverData,
        ));
        if (! $controllerClassName) {
            return $this->handleNotFound($request);
        }
        $controller = $this->buildController($controllerClassName, $request);
        if (! $controller) {
            return $this->handleError($request);
        }
        return $controller->handleRequest($request);
    }


    /**
     * @param string $controllerClassName
     * @param ESys_WebControl_Request
     * @return ESys_WebControl_Controller
     */
    protected function buildController ($controllerClassName, $request)
    {
        $controller = $this->getControllerFactory()->build($controllerClassName);
        if (! $controller) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.'() unable to load '.
                "mapped class '{$controllerClassName}'", E_USER_WARNING);
            return null;
        }
        $responseFactory = $this->getResponseFactory();
        $responseFactory->setRequest($request);
        $controller->setResponseFactory($responseFactory);
        return $controller;
    }


    /**
     * @param string $path
     * @return string
     */
    protected function getMappedClassName ($path)
    {
        if (! isset($this->pathMap[$path])) {
            return null;
        }
        return $this->pathMap[$path];
    }


    /**
     * Calculates the query path (path to controller and action parameters)
     * from the raw HTTP input data.
     *
     * @param array PHP's $_GET data
     * @param array PHP's $_POST data
     * @param array PHP's $_SERVER data
     * @return void
     */
    protected function calculateQueryPath ($getData, $postData, $serverData)
    {
        return isset($getData['query']) 
            ? '/'.trim($getData['query'], '/') 
            : '/';
    }



}