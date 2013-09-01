<?php


require_once 'ESys/Data/ReportRecord.php';


class ESys_Data_ReportRecordTest extends PHPUnit_Framework_TestCase {


    public function testProvidesGettersForFieldsInData ()
    {
        $user = new ESys_Data_ReportRecord(array(
            'id' => '123',
            'name' => 'John',
        ));
        $this->assertEquals('123', $user->getId());        
        $this->assertEquals('John', $user->getName());
    }


    public function testMapsUnderscoresInFieldsToCamelCaseGetters ()
    {
        $user = new ESys_Data_ReportRecord(array(
            'first_name' => 'John',
        ));
        $this->assertEquals('John', $user->getFirstName());
    }


    public function testTriggersErrorForNonExistentProperties ()
    {
        $user = new ESys_Data_ReportRecord(array(
            'first_name' => 'John',
        ));
        $this->setExpectedException('PHPUnit_Framework_Error');
        $user->getEmail();
    }


    public function testExportsDataToArray ()
    {
        $inputArray = array(
            'first_name' => 'John',
        );
        $record = new ESys_Data_ReportRecord($inputArray);
        $this->assertEquals($inputArray, $record->export());
    }


}
