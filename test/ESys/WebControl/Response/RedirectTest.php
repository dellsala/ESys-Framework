<?php

require_once 'PHPUnit/Framework.php';
require_once 'ESys/WebControl/Response.php';


class ESys_WebControl_Response_RedirectTest extends PHPUnit_Framework_TestCase {


    public function testUnsupportedCode ()
    {
        $this->setExpectedException('Exception');
        $response = new ESys_WebControl_Response_Redirect('/some/url', 500);
    }


    /**
     * @dataProvider dataProvider
     */
    public function testBuildingRedirect ($location, $code, $expectedHeaders)
    {
        $response = new ESys_WebControl_Response_Redirect($location, $code);
        $this->assertEquals($expectedHeaders, $response->getHeaders());
        $this->assertEquals('', $response->getBody());
    }


    public function dataProvider ()
    {
        return array(
            array(
                '/some/new/url',
                302,
                array(
                    "HTTP/1.x 302 Found",
                    "Location: /some/new/url",
                ),
            ),
            array(
                '/some/new/url',
                303,
                array(
                    "HTTP/1.x 303 See Other",
                    "Location: /some/new/url",
                ),
            ),
            array(
                '/some/new/url',
                301,
                array(
                    "HTTP/1.x 301 Moved Permanently",
                    "Location: /some/new/url",
                ),
            ),
        );
    }



}
