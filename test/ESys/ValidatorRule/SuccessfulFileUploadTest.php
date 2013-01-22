<?php

require_once 'PHPUnit/Framework.php';
require_once 'ESys/ValidatorRule.php';
require_once 'ESys/ValidatorRule/FileUploadStub.php';


class ESys_ValidatorRule_SuccessfulFileUploadTest extends PHPUnit_Framework_TestCase {


    public function testValidSuccessfulFileUpload ()
    {
        $value = new ESys_ValidatorRule_FileUploadStub(array(
            'wasSubmitted' => true,
            'wasTransferred' => true,
        ));
        $rule = new ESys_ValidatorRule_SuccessfulFileUpload();
        $this->assertEquals($rule->validate($value), true);
    }


    public function testFileUploadNotTransfered ()
    {
        $value = new ESys_ValidatorRule_FileUploadStub(array(
            'wasSubmitted' => true,
            'wasTransferred' => false,
        ));
        $rule = new ESys_ValidatorRule_SuccessfulFileUpload();
        $this->assertEquals($rule->validate($value), false);
    }


    public function testFileUploadNotSubmitted ()
    {
        $value = new ESys_ValidatorRule_FileUploadStub(array(
            'wasSubmitted' => false,
            'wasTransferred' => true,
        ));
        $rule = new ESys_ValidatorRule_SuccessfulFileUpload();
        $this->assertEquals($rule->validate($value), true);
    }



    public function testFileUploadIsWrongType ()
    {
        $value = null;
        $rule = new ESys_ValidatorRule_SuccessfulFileUpload();
        $this->assertEquals($rule->validate($value), false);
    }


}



