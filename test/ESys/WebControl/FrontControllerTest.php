<?php

require_once 'PHPUnit/Framework.php';
require_once 'ESys/WebControl/FrontController.php';

class ESys_WebControl_FrontControllerTest extends PHPUnit_Framework_TestCase {


    public function setUp ()
    {
        $this->urlBase = '/path/to/project';
        $this->frontControllerPath = '/component';
        $this->httpGetData = array(
            'query' => '/path/to/controller/action/param',
        );
        $this->frontController =
            new ESys_WebControl_FrontController($this->urlBase, $this->frontControllerPath);
    }
    

    public function testDefaultInvalidActionBehavior ()
    {
        $response = $this->frontController->handleRequest($this->httpGetData, array(), array());
        $this->assertType('ESys_WebControl_Response_NotFound', $response);
    }


    public function testChangingInvalidActionBehavior ()
    {
        $this->frontController->setResponseFactory(
            new ESys_WebControl_FrontControllerTest_CustomResponseFactory()
        );
        $response = $this->frontController->handleRequest($this->httpGetData, array(), array());
        $this->assertType('ESys_WebControl_Response_NotFound', $response);
        $this->assertEquals(
            ESys_WebControl_FrontControllerTest_CustomResponseFactory::NOT_FOUND_MESSAGE,
            $response->getBody()
        );
    }


    /**
     * @dataProvider addingPathMappingQueryDataProvider
     */
    public function testAddingPathMapping ($urlQuery, $expectedResponseBody)
    {
        $this->frontController->addPath(
            '/', 
            'ESys_WebControl_FrontControllerTest_RootController'
        );
        $this->frontController->addPath(
            '/path/to/simple/controller', 
            'ESys_WebControl_FrontControllerTest_SimpleController'
        );
        $httpGetData = array(
            'query' => $urlQuery
        );
        $response = $this->frontController->handleRequest($httpGetData, array(), array());
        $this->assertEquals(
            $expectedResponseBody,
            $response->getBody()
        );
    }


    public function addingPathMappingQueryDataProvider ()
    {
        return array(
            array(
                'path/to/simple/controller/action',
                ESys_WebControl_FrontControllerTest_SimpleController::ACTION_MESSAGE
            ),
            array(
                'path/to/simple/controller/badaction',
                ESys_WebControl_FrontControllerTest_SimpleController::NOT_FOUND_MESSAGE
            ),
            array(
                '',
                ESys_WebControl_FrontControllerTest_RootController::INDEX_MESSAGE
            ),
            array(
                'some/unmapped/path',
                ESys_WebControl_FrontControllerTest_RootController::NOT_FOUND_MESSAGE
            ),
        );
    }


    public function testRequestCreationAndLoadingControllerFile ()
    {
        $this->frontController->addPath(
            '/path/to/controller', 
            'ESys_WebControl_FrontController_SpyController'
        );
        $this->assertFalse(class_exists('ESys_WebControl_FrontController_SpyController'),
            "class to be loaded from file should not already exist");
        $response = $this->frontController->handleRequest($this->httpGetData, array(), array());
        $this->assertEquals(
            new ESys_WebControl_Request(array(
                'basePath' => $this->urlBase,
                'frontControllerPath' => $this->frontControllerPath,
                'controllerPath' => '/path/to/controller',
                'actionParameters' => array('action', 'param'),
                'getData' => $this->httpGetData,
                'postData' => array(),
            )),
            $response->request
        );        
    }


    public function testThrowsErrorWhenUnableToLoadController ()
    {
        $this->frontController->addPath(
            '/path/to/controller', 
            'Some_Invalid_Controller_ClassName'
        );
        $this->setExpectedException('Exception');
        $response = $this->frontController->handleRequest($this->httpGetData, array(), array());
    }


}


class ESys_WebControl_FrontControllerTest_CustomResponseFactory
    extends ESys_WebControl_ResponseFactory {

    const NOT_FOUND_MESSAGE = 'custom action message';

    public function buildNotFound ($data)
    {
        return new ESys_WebControl_Response_NotFound(self::NOT_FOUND_MESSAGE);
    }

}


class ESys_WebControl_FrontControllerTest_SimpleController extends ESys_WebControl_Controller {

    const ACTION_MESSAGE = 'simple controller action';
    const NOT_FOUND_MESSAGE = 'simple controller action not found';

    public function doAction ($request)
    {
        return new ESys_WebControl_Response_Ok(self::ACTION_MESSAGE);
    }

    public function doDefault ($request)
    {
        return new ESys_WebControl_Response_NotFound(self::NOT_FOUND_MESSAGE);
    }


}


class ESys_WebControl_FrontControllerTest_RootController extends ESys_WebControl_Controller {

    const INDEX_MESSAGE = 'root controller index';
    const NOT_FOUND_MESSAGE = 'root controller action not found';

    public function doIndex ($request)
    {
        return new ESys_WebControl_Response_Ok(self::INDEX_MESSAGE);
    }

    public function doDefault ($request)
    {
        return new ESys_WebControl_Response_NotFound(self::NOT_FOUND_MESSAGE);
    }

}

