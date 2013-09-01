<?php


require_once 'ESys/ValidatorRule.php';


class ESys_ValidatorRule_MatchTest extends PHPUnit_Framework_TestCase {


    public function setup ()
    {
        $this->pattern = '/^Hello$/';
    }


    public function testMatchesPattern ()
    {
        $rule = new ESys_ValidatorRule_Match($this->pattern);
        $result = $rule->validate('Hello');
        $this->assertEquals($result, true);
    }


    public function testDoesntMatchPattern ()
    {
        $rule = new ESys_ValidatorRule_Match($this->pattern);
        $result = $rule->validate('hello');
        $this->assertEquals($result, false);
    }


    public function testInvalidPattern ()
    {
        $rule = new ESys_ValidatorRule_Match('sdff/');
        try {
            $result = $rule->validate('hello');
        } catch (PHPUnit_Framework_Error $e) {
            $this->assertEquals($e->getCode(), E_WARNING);
            return;
        }
        $this->fail('Expected exception not thown.');
    }


}
