<?php

require_once 'PHPUnit/Framework.php';
require_once 'ESys/ValidatorRule.php';


class ESys_ValidatorRule_WhitelistTest extends PHPUnit_Framework_TestCase {


    /**
     * @dataProvider provider
     */
    public function testWhitelistValidation ($value, $expectedResult)
    {
        $whitelist = array(
            'hello',
            'world',
            10,
        );
        $rule = new ESys_ValidatorRule_Whitelist($whitelist);
        $this->assertEquals($rule->validate($value), $expectedResult);
    }
    
    
    public function provider ()
    {
        return array(

            // VALID
            array('hello', true),
            array('world', true),
            array('10', true),
            array(10.0, true),

            // INVALID
            array('Hello', false),
            array('hello ', false),
            array('hello world', false),
            
            
        );
    }



}
