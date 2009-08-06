<?php

require_once 'PHPUnit/Framework.php';
require_once 'ESys/Validator.php';


class ESys_ValidatorRule_OptionalTest extends PHPUnit_Framework_TestCase {


    /**
     * @dataProvider provider
     */
    public function testOptionalValidation ($value, $expectedResult)
    {
        $subRule = new ESys_ValidatorRule_Email();
        $rule = new ESys_ValidatorRule_Optional($subRule);
        $this->assertEquals($rule->validate($value), $expectedResult);
    }


    public function provider ()
    {
        return array(

            // VALID
            array('', true),
            array(null, true),

            // INVLAID
            array(' ', false),
            array('invalid@email', false),

        );
    }


}
