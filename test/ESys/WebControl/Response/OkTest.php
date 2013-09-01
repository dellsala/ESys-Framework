<?php


require_once 'ESys/WebControl/Response.php';


class ESys_WebControl_Response_OkTest extends PHPUnit_Framework_TestCase {


    public function testExpectedHeadersAndBody ()
    {
        $body = 'Some Test Body';
        $response = new ESys_WebControl_Response($body);
        $this->assertEquals(array(), $response->getHeaders());
        $this->assertEquals($body, $response->getBody());
    }


}
