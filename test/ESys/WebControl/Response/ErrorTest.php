<?php


require_once 'ESys/WebControl/Response.php';


class ESys_WebControl_Response_ErrorTest extends PHPUnit_Framework_TestCase {


    public function testExpectedHeadersAndBody ()
    {
        $body = 'Internal Server Error';
        $expectedHeaders = array(
            'HTTP/1.x 500 Internal Server Error'
        );
        $response = new ESys_WebControl_Response_Error($body);
        $this->assertEquals($expectedHeaders, $response->getHeaders());
        $this->assertEquals($body, $response->getBody());
    }


}
