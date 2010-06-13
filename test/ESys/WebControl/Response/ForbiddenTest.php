<?php

require_once 'PHPUnit/Framework.php';
require_once 'ESys/WebControl/Response.php';


class ESys_WebControl_Response_ForbiddenTest extends PHPUnit_Framework_TestCase {


    public function testResponseHas403Header ()
    {
        $response = new ESys_WebControl_Response_Forbidden('');
        $expectedHeaders = array(
            'HTTP/1.x 403 Forbidden',
        );
        $this->assertEquals($expectedHeaders, $response->getHeaders());
    }


}
