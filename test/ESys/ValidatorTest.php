<?php

require_once 'PHPUnit/Framework.php';
require_once 'ESys/Validator.php';

class ESys_ValidatorTest extends PHPUnit_Framework_TestCase {


    public function testSettingData ()
    {
        $data = array(
            'field1' => 12,
            'field2' => 10,
        );
        $validator = new ESys_Validator();
        $validator->setData($data);
        $this->assertEquals($validator->getData(), $data);
    }


    public function testSettingSingleField ()
    {
        $data = array(
            'field1' => 12,
            'field2' => 10,
        );
        $newFieldKey = 'field3';
        $newFieldValue = 22;
        $validator = new ESys_Validator();
        $validator->setData($data);
        $validator->setData($newFieldKey, $newFieldValue);
        $data[$newFieldKey] = $newFieldValue;
        $this->assertEquals($validator->getData(), $data);
    }


    /**
     * @dataProvider addingRuleProvider
     */
    public function testValidatesDataBasedConfiguredRules ($ruleList, $expectedResult)
    {
        $data = array(
            'name' => 'John Smith',
        );
        $validator = new ESys_Validator();
        $validator->setData($data);
        foreach ($ruleList as $rule) {
            $validator->addRule($rule['field'], $rule['rule'], $rule['message'], $rule['code']);
        }
        $this->assertEquals($validator->validate(), $expectedResult);
    }


    public function addingRuleProvider ()
    {
        return array(
            array(
                array(
                ),
                true,
            ),
            array(
                array(
                    array(
                        'field' => 'name',
                        'rule' => new ESys_ValidatorRule_Match('/^John Smith$/'),
                        'code' => 1,
                        'message' => 'Name must be John Smith',
                    )
                ),
                true,
            ),
            array(
                array(
                    array(
                        'field' => 'name',
                        'rule' => new ESys_ValidatorRule_Match('/^John Doe$/'),
                        'code' => 1,
                        'message' => 'Name must be John Doe',
                    )
                ),
                false,
            ),
        );
    }


    public function testValidatorProducesExpectedErrorData ()
    {
        $data = array(
            'email' => 'john@smith.com',
            'name' => 'John Smith',
        );
        $rules = array(
            array(
                'field' => 'email',
                'rule' => new ESys_ValidatorRule_MinLength(100),
                'message' => 'Email must be at least 100 chars long.',
                'code' => 1001,
            ),
            array(
                'field' => 'name',
                'rule' => new ESys_ValidatorRule_Match('/^[A-Z]+$/'),
                'message' => 'Name must be only uppercase letters, no spaces.',
                'code' => 2001,
            ),
            array(
                'field' => 'name',
                'rule' => new ESys_ValidatorRule_MaxLength(4),
                'message' => 'Name must be less than 5 characters long.',
                'code' => 3001,
            ),
        );
        $expectedErrorData = array(
            $rules[0]['field'] => array(
                array(
                    'message' => $rules[0]['message'],
                    'code' => $rules[0]['code'],
                ),
            ),
            $rules[1]['field'] => array(
                array(
                    'message' => $rules[1]['message'],
                    'code' => $rules[1]['code'],
                ),
                array(
                    'message' => $rules[2]['message'],
                    'code' => $rules[2]['code'],
                ),
            ),
        );

        $validator = new ESys_Validator();
        $validator->setData($data);
        foreach ($rules as $rule) {
            $validator->addRule($rule['field'], $rule['rule'], $rule['message'], $rule['code']);
        }
        $validator->validate();
        $this->assertEquals($expectedErrorData, $validator->getErrors());
    }



}
