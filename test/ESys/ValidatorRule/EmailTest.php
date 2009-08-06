<?php

require_once 'PHPUnit/Framework.php';
require_once 'ESys/Validator.php';


class ESys_ValidatorRule_EmailTest extends PHPUnit_Framework_TestCase {


    /**
     * @dataProvider provider
     */
    public function testEmailValidation ($emailString, $expectedValidationResult)
    {
        $rule = new ESys_ValidatorRule_Email();
        $this->assertEquals($rule->validate($emailString), $expectedValidationResult);
    }
    
    
    public function provider ()
    {
        return array(

            // VALID
            array('john@smith.com', true),
            array('john.smith@smith.com', true),
            array('john@john.smith.com', true),
            array('john1@smith.com', true),
            array('john#@smith.com', true),
            array('john$@smith.com', true),
            array('john!@smith.com', true),
            array('j@s.com', true),

            // INVALID
            array('john@smith@smith.com', false),
            array('.john@smith.com', false),
            array('john@smith.com.a', false),
            array('john..smith@smith.com', false),
            array('john@smith.c', false),
            array('john@.com', false),
            
        );
    }



}
