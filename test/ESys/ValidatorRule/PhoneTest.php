<?php


require_once 'ESys/ValidatorRule.php';


class ESys_ValidatorRule_PhoneTest extends PHPUnit_Framework_TestCase {


    /**
     * @dataProvider provider
     */
    public function testPhoneValidation ($phoneString, $expectedValidationResult)
    {
        $rule = new ESys_ValidatorRule_Phone();
        $this->assertEquals($rule->validate($phoneString), $expectedValidationResult);
    }
    
    
    public function provider ()
    {
        return array(

            // VALID
            array('514-488-6636', true),
            array('(514) 488.6636', true),
            array('514 4886636', true),
            array('514-4886636', true),
            array('488.6636', true),
            array('4886636', true),
            array('1 (514) 488-6636', true),
            array('188-6636', true),
            array('123-123-188-6636', true),

            // INVALID
            array('488663', false),
            array('488x6636', false),
            array('488-6636 x232', false),
            
        );
    }



}
