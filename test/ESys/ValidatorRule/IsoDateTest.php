<?php

require_once 'PHPUnit/Framework.php';
require_once 'ESys/ValidatorRule.php';


class ESys_ValidatorRule_IsoDateTest extends PHPUnit_Framework_TestCase {


    public function setup ()
    {
        date_default_timezone_set('UTC');
    }


    /**
     * @dataProvider provider
     */
    public function testDateValidation ($dateString, $expectedValidationResult)
    {
        $rule = new ESys_ValidatorRule_IsoDate();
        $this->assertEquals($rule->validate($dateString), $expectedValidationResult);
    }
    
    
    public function provider ()
    {
        return array(
            array('2008-12-03', true),
            array('1902-12-03', true),
            array('1902-01-15', true),
            array(' 1902-01-15', false),
            array('1902-01-15 ', false),
            array('1902--01-15 ', false),
            array('2008-', false),
            array('08-12-03', false),
            array('08-12-3', false),
            array('2008-13-01', false),
            array('2008-3-01', false),
        );
    }



}
