<?php

require_once 'PHPUnit/Framework.php';
require_once 'ESys/WebControl/Response.php';


class ESys_WebControl_Response_NotFoundTest extends PHPUnit_Framework_TestCase {


    public function testExpectedHeadersAndBody ()
    {
        $body = 'Some Test Body';
        $expectedHeaders = array(
            'HTTP/1.x 404 Not Found'
        );
        $response = new ESys_WebControl_Response_NotFound($body);
        $this->assertEquals($expectedHeaders, $response->getHeaders());
        $this->assertEquals($body, $response->getBody());
    }


}
