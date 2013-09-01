<?php


require_once 'ESys/ValidatorRule.php';


class ESys_ValidatorRule_NotEmptyTest extends PHPUnit_Framework_TestCase {


    public function testValidatesNonEmptyStrings ()
    {
        $rule = new ESys_ValidatorRule_NotEmpty();
        $result = $rule->validate('Hello');
        $this->assertEquals($result, true);
    }


	public function testInvalidatesEmptyStrings ()
	{
        $rule = new ESys_ValidatorRule_NotEmpty();
        $result = $rule->validate('');
        $this->assertEquals($result, false);
    }


	public function testValidatesZero ()
	{
        $rule = new ESys_ValidatorRule_NotEmpty();
        $result = $rule->validate(0);
        $this->assertEquals($result, true);
    }


	public function testInvalidatesWhitespaceString ()
	{
        $rule = new ESys_ValidatorRule_NotEmpty();
        $result = $rule->validate(' ');
        $this->assertEquals($result, false);
    }

	
	public function testInvalidatesNull ()
	{
        $rule = new ESys_ValidatorRule_NotEmpty();
        $result = $rule->validate(null);
        $this->assertEquals($result, false);
    }

	
}
