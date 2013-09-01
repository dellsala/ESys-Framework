<?php


require_once 'ESys/ValidatorRule.php';


class ESys_ValidatorRule_RepeatFieldTest extends PHPUnit_Framework_TestCase {


    private $validator;
    

    public function setUp ()
    {
        $this->validator = new ESys_Validator();
        $this->validator->setData(array(
            'password' => '12345',
        ));
    }


    /**
     * @dataProvider provider
     */
    public function testRepeatField ($field, $value, $expectedResult)
    {
        $rule = new ESys_ValidatorRule_RepeatField($field);
        $rule->setValidator($this->validator);
        $this->assertEquals($rule->validate($value), $expectedResult);
    }


    public function provider ()
    {
        return array(
            array('password', '12345', true),
            array('password', '123456', false),
            array('pass', '12345', false),
        );
    }


}
