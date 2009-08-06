<?php

require_once 'PHPUnit/Framework.php';
require_once 'ESys/WebControl/Response.php';


class ESys_WebControl_ResponseTest extends PHPUnit_Framework_TestCase {


    public function testSettingAndGettingHeaders ()
    {
        $headers = array(
            'HTTP/1.x 500 Server Error',
            'Some other header',
        );
        $response = new ESys_WebControl_Response('Some Test Body');
        foreach ($headers as $header) {
            $response->addHeader($header);
        }
        $this->assertEquals($headers, $response->getHeaders());
    }


    public function testSettingAndGettingBody ()
    {
        $body = 'Some Test Body';
        $response = new ESys_WebControl_Response($body);
        $this->assertEquals($body, $response->getBody());
    }


}
