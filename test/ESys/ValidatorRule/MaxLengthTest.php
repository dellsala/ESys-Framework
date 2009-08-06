<?php

require_once 'PHPUnit/Framework.php';
require_once 'ESys/Validator.php';

class ESys_ValidatorRule_MaxLengthTest extends PHPUnit_Framework_TestCase {


    private $string;
    
    
    private $stringLength;


    public function setUp ()
    {
        $this->string = 'Hello World';
        $this->stringLength = strlen($this->string);
    }
    

    public function testStringIsShorter ()
    {
        $rule = new ESys_ValidatorRule_MaxLength($this->stringLength + 1);
        $this->assertEquals($rule->validate($this->string), true);
    }


    public function testStringIsLonger ()
    {
        $rule = new ESys_ValidatorRule_MaxLength($this->stringLength - 1);
        $this->assertEquals($rule->validate($this->string), false);
    }


    public function testStringIsEqualLength ()
    {
        $rule = new ESys_ValidatorRule_MaxLength($this->stringLength);
        $this->assertEquals($rule->validate($this->string), true);
    }


    public function testArrayIsShorter ()
    {
        $value = array();
        $rule = new ESys_ValidatorRule_MaxLength(1);
        $this->assertEquals($rule->validate($value), true);
    }


    public function testArrayIsLonger ()
    {
        $value = array('item1', 'item2');
        $rule = new ESys_ValidatorRule_MaxLength(1);
        $this->assertEquals($rule->validate($value), false);
    }


}
