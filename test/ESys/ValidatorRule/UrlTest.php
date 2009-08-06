<?php

require_once 'PHPUnit/Framework.php';
require_once 'ESys/Validator.php';


class ESys_ValidatorRule_UrlTest extends PHPUnit_Framework_TestCase {


    /**
     * @dataProvider defaultProtocolsProvider
     */
    public function testUrlValidationWithDefaultProtocols ($value, $expectedResult)
    {
        $rule = new ESys_ValidatorRule_Url();
        $this->assertEquals($rule->validate($value), $expectedResult);
    }
    
    
    public function defaultProtocolsProvider ()
    {
        return array(

            // VALID
            array('http://google.com', true),
            array('https://google.com', true),
            array('http://google.com?query=hello%20world&lang=en', true),

            // INVALID
            array('http:/google.com', false),
            array('ftp://google.com', false),
            array('google.com', false),
            array('/google.com', false),
            array('http://google.com?hello=world&name=joe?', false),
            
        );
    }


    /**
     * @dataProvider specifiedProtocolsProvider
     */
    public function testUrlValidationWithSpecifiedProtocols ($value, $expectedResult)
    {
        $protocols = array('ftp', 'file');
        $rule = new ESys_ValidatorRule_Url($protocols);
        $this->assertEquals($rule->validate($value), $expectedResult);
    }


    public function specifiedProtocolsProvider ()
    {
        return array(

            // VALID
            array('ftp://google.com', true),
            array('file://google.com', true),

            // INVALID
            array('http://google.com', false),

        );
    }



}
