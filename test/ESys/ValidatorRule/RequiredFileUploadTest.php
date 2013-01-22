<?php

require_once 'PHPUnit/Framework.php';
require_once 'ESys/ValidatorRule.php';
require_once 'ESys/ValidatorRule/FileUploadStub.php';


class ESys_ValidatorRule_RequiredFileUploadTest extends PHPUnit_Framework_TestCase {


    public function testValidRequiredFileUpload ()
    {
        $value = new ESys_ValidatorRule_FileUploadStub(array(
            'wasSubmitted' => true,
            'wasTransferred' => false,
        ));
        $rule = new ESys_ValidatorRule_RequiredFileUpload();
        $this->assertEquals($rule->validate($value), true);
    }


    public function testInvalidRequiredFileUpload ()
    {
        $value = new ESys_ValidatorRule_FileUploadStub(array(
            'wasSubmitted' => false,
            'wasTransferred' => false,
        ));
        $rule = new ESys_ValidatorRule_RequiredFileUpload();
        $this->assertEquals($rule->validate($value), false);
    }


    public function testRequiredFileUploadWrongType ()
    {
        $value = null;
        $rule = new ESys_ValidatorRule_RequiredFileUpload();
        $this->assertEquals($rule->validate($value), false);
    }


}



