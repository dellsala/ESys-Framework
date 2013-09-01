<?php


require_once 'ESys/WebControl/Controller.php';
require_once 'ESys/WebControl/ControllerTest/RequestBuilder.php';
require_once 'ESys/WebControl/ResponseFactory.php';

class ESys_WebControl_ControllerTest extends PHPUnit_Framework_TestCase {


    public function setUp ()
    {
        $this->requestBuilder = new ESys_WebControl_ControllerTest_RequestBuilder();
        $this->controller = new ESys_WebControl_Controller();
    }


    public function testIndexAction ()
    {
        $request = $this->requestBuilder->createFromAction(null);
        $response = $this->controller->handleRequest($request);
        $this->assertInstanceOf('ESys_WebControl_Response_NotFound', $response);
    }


    public function testInvalidAction ()
    {
        $request = $this->requestBuilder->createFromAction('badaction');
        $response = $this->controller->handleRequest($request);
        $this->assertInstanceOf('ESys_WebControl_Response_NotFound', $response);
    }


    /**
     * @dataProvider reservedActionNameData
     */
    public function testReservedActionNameResultsInDefaultAction ($action)
    {
        $request = $this->requestBuilder->createFromAction($action);
        $response = $this->controller->handleRequest($request);
        $this->assertInstanceOf('ESys_WebControl_Response_NotFound', $response);
    }


    public function reservedActionNameData ()
    {
        return array(
            array('index'),
            array('default'),
            array('forbidden'),
        );
    }


    public function testInvalidActionBehaviorChange ()
    {
        $request = $this->requestBuilder->createFromAction('badaction');
        $this->controller->setResponseFactory(
            new ESys_WebControl_ControllerTest_CustomResponseFactory());
        $response = $this->controller->handleRequest($request);
        $this->assertInstanceOf('ESys_WebControl_ControllerTest_CustomNotFoundResponse', $response);
    }


}



class ESys_WebControl_ControllerTest_CustomResponseFactory 
    extends ESys_WebControl_ResponseFactory
{

    public function buildNotFound ($data)
    {
        return new ESys_WebControl_ControllerTest_CustomNotFoundResponse('Not Found');
    }


}



class ESys_WebControl_ControllerTest_CustomNotFoundResponse
    extends ESys_WebControl_Response_NotFound
{
}
