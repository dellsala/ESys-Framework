<?php


require_once 'ESys/WebControl/Controller.php';
require_once 'ESys/WebControl/ControllerTest/RequestBuilder.php';


class ESys_WebControl_ControllerTest_ExtendedControllerTest extends PHPUnit_Framework_TestCase {


    public function setUp ()
    {
        $this->requestBuilder = new ESys_WebControl_ControllerTest_RequestBuilder();
        $this->controller = new ESys_WebControl_ControllerTest_ExtendedControllerTest_ExtendedController();
    }


    public function testIndexAction ()
    {
        $request = $this->requestBuilder->createFromAction(null);
        $response = $this->controller->handleRequest($request);
        $this->assertInstanceOf('ESys_WebControl_Response_Ok', $response);
        $this->assertEquals('index action output', $response->getBody());        
    }


    public function testHandlingRegisteredAction ()
    {
        $request = $this->requestBuilder->createFromAction('test');
        $response = $this->controller->handleRequest($request);
        $this->assertInstanceOf('ESys_WebControl_Response_Ok', $response);
        $this->assertEquals('test action output', $response->getBody());
    }


    public function testHandlingInvalidAction ()
    {
        $request = $this->requestBuilder->createFromAction('badaction');
        $response = $this->controller->handleRequest($request);
        $this->assertInstanceOf('ESys_WebControl_Response_NotFound', $response);
    }


    public function testHandlesActionWithHyphen ()
    {
        $request = $this->requestBuilder->createFromAction('my-action');
        $response = $this->controller->handleRequest($request);
        $this->assertInstanceOf('ESys_WebControl_Response_Ok', $response);
    }
    

    /**
     * @dataProvider forbiddenActionData
     */
    public function testHandlingForbiddenRequest ($action)
    {
        $request = $this->requestBuilder->createFromAction($action);
        $forbiddenController = new ESys_WebControl_ControllerTest_ExtendedControllerTest_ForbiddenController();
        $response = $forbiddenController->handleRequest($request);
        $this->assertInstanceOf('ESys_WebControl_Response_Forbidden', $response);
    }

    
    public function forbiddenActionData ()
    {
        return array(
            array(
                '',
            ),
            array(
                'badaction',
            ),
            array(
                'test',
            ),
        );
    }


}


class ESys_WebControl_ControllerTest_ExtendedControllerTest_ExtendedController 
    extends ESys_WebControl_Controller 
{

    protected function doIndex ($request)
    {
        return new ESys_WebControl_Response_Ok('index action output');
    }

    protected function doTest ($request)
    {
        return new ESys_WebControl_Response_Ok("test action output");
    }
    
    protected function doMyAction ($request)
    {
        return new ESys_WebControl_Response_Ok("myAction action output");
    }
    
}



class ESys_WebControl_ControllerTest_ExtendedControllerTest_ForbiddenController 
    extends ESys_WebControl_ControllerTest_ExtendedControllerTest_ExtendedController
{


    protected function isAuthorized (ESys_WebControl_Request $request)
    {
        return false;
    }


}
