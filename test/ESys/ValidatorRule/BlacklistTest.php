<?php

require_once 'PHPUnit/Framework.php';
require_once 'ESys/ValidatorRule.php';


class ESys_ValidatorRule_BlacklistTest extends PHPUnit_Framework_TestCase {


    /**
     * @dataProvider provider
     */
    public function testBlacklistValidation ($value, $expectedResult)
    {
        $blacklist = array(
            'hello',
            'world',
            10,
        );
        $rule = new ESys_ValidatorRule_Blacklist($blacklist);
        $this->assertEquals($rule->validate($value), $expectedResult);
    }
    
    
    public function provider ()
    {
        return array(

            // VALID
            array('foo', true),
            array('Hello', true),
            array('hello ', true),
            array('hello world', true),

            // INVALID
            array('hello', false),
            array('world', false),
            array('10', false),
            array(10.0, false),
            
        );
    }



}
