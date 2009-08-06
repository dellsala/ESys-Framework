<?php

require_once 'PHPUnit/Framework.php';
require_once 'ESys/WebControl/ResponseFactory.php';
require_once 'ESys/WebControl/Request.php';


class ESys_WebControl_ResponseFactoryTest extends PHPUnit_Framework_TestCase {


    public function setUp ()
    {
        $this->responseFactory = new ESys_WebControl_ResponseFactory();
    }


    public function testBuildsOkResponse ()
    {
        $response = $this->responseFactory->build('ok', array('content' => 'Foo'));
        $this->assertType('ESys_WebControl_Response_Ok', $response, 'response is not of expected type');
        $this->assertEquals('Foo', $response->getBody(), 'response does not have expected body');
    }


    public function testBuildsNotFoundResponse ()
    {
        $response = $this->responseFactory->build('notFound', array());
        $this->assertType('ESys_WebControl_Response_NotFound', $response, 
            'response is not of expected type');
        $this->assertEquals('Resource not found', $response->getBody(), 
            'response does not have expected default body');
    }


    public function testBuildsErrorResponse ()
    {
        $response = $this->responseFactory->build('error', array());
        $this->assertType('ESys_WebControl_Response_Error', $response, 
            'response is not of expected type');
        $this->assertEquals('Unexpected error', $response->getBody(), 
            'response does not have expected default body');
    }


    public function testBuildsForbiddenResponse ()
    {
        $response = $this->responseFactory->build('forbidden', array());
        $this->assertType('ESys_WebControl_Response_Forbidden', $response, 
            'response is not of expected type');
        $this->assertEquals('Forbidden', $response->getBody(), 
            'response does not have expected default body');
    }


    public function testBuildThrowsErrorForUnsupportedType ()
    {
        $this->setExpectedException('Exception');
        $response = $this->responseFactory->build('badType', array('content' => 'Hello World'));
    }


    public function testSetRequestGetsAddedToData ()
    {
        $factoryWithoutRequest = new ESys_WebControl_ResponseFactoryTest_SpyResponseFactory();
        $spyResponse = $factoryWithoutRequest->build('spy', array('content'=>'Foo'));

        $this->assertFalse(array_key_exists('request', $spyResponse->data), 
            'build data should not have a request');

        $factoryWithRequest = new ESys_WebControl_ResponseFactoryTest_SpyResponseFactory();
        $factoryWithRequest->setRequest(new ESys_WebControl_Request());
        $spyResponse = $factoryWithRequest->build('spy', array('content'=>'Foo'));

        $this->assertTrue(array_key_exists('request', $spyResponse->data), 
            'build data is missing a request');
        $this->assertType('ESys_WebControl_Request', $spyResponse->data['request'],
            'request in build data is not of expected type');
    }


    public function testSetCommonDataAddsDataToBuild ()
    {
        $factory = new ESys_WebControl_ResponseFactoryTest_SpyResponseFactory();
        $spyResponse = $factory->build('spy', array('content'=>'Foo'));
        $this->assertEquals(array('content'=>'Foo'), $spyResponse->data, 
            'build method was not called with expected data');

        $factory = new ESys_WebControl_ResponseFactoryTest_SpyResponseFactory();
        $factory->setCommonData(array('commonContent'=>'Bar'));
        $spyResponse = $factory->build('spy', array('content'=>'Foo'));
        $expectedData = array(
            'commonContent'=>'Bar',
            'content'=>'Foo',
        );
        $this->assertEquals($expectedData, $spyResponse->data,
            'common data was not added to build method data');
    }

}



class ESys_WebControl_ResponseFactoryTest_SpyResponseFactory 
    extends ESys_WebControl_ResponseFactory
{

    protected function buildSpy ($data)
    {
        return new ESys_WebControl_ResponseFactoryTest_SpyResponse($data);
    }

}


class ESys_WebControl_ResponseFactoryTest_SpyResponse extends ESys_WebControl_Response
{

    public $data;

    public function __construct ($data)
    {
        $this->data = $data;
    }

}