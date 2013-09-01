<?php


require_once 'ESys/ValidatorRule.php';

class ESys_ValidatorRule_MinLengthTest extends PHPUnit_Framework_TestCase {


    private $string;
    
    
    private $stringLength;


    public function setUp ()
    {
        $this->string = 'Hello World';
        $this->stringLength = strlen($this->string);
    }
    

    public function testStringTooShort ()
    {
        $rule = new ESys_ValidatorRule_MinLength($this->stringLength + 1);
        $this->assertEquals($rule->validate($this->string), false);
    }


    public function testStringIsLonger ()
    {
        $rule = new ESys_ValidatorRule_MinLength($this->stringLength - 1);
        $this->assertEquals($rule->validate($this->string), true);
    }


    public function testStringIsEqualInLength ()
    {
        $rule = new ESys_ValidatorRule_MinLength($this->stringLength);
        $this->assertEquals($rule->validate($this->string), true);
    }


    public function testArrayIsTooShort ()
    {
        $value = array();
        $rule = new ESys_ValidatorRule_MinLength(1);
        $this->assertEquals($rule->validate($value), false);
    }


    public function testArrayIsLonger ()
    {
        $value = array('item1', 'item2');
        $rule = new ESys_ValidatorRule_MinLength(1);
        $this->assertEquals($rule->validate($value), true);
    }


}
