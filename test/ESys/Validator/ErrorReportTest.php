<?php


require_once 'ESys/Validator.php';

class ESys_Validator_ErrorReportTest extends PHPUnit_Framework_TestCase {


    private $errorData = array(
        'email' => array(
            array(
                'code' => 1,
                'message' => 'A valid e-mail address is required',
            ),
        ),
        'first_name' => array(
            array(
                'code' => 2,
                'message' => 'The first name can not cantain html special characters',
            ),
            array(
                'code' => 3,
                'message' => 'The first name must be less than 255 characters',
            ),
        ),
    );


    public function testErrorCount ()
    {
        $errorReport = new ESys_Validator_ErrorReport(array());
        $this->assertEquals(0, $errorReport->errorCount());
        
        $errorReport = new ESys_Validator_ErrorReport($this->errorData);
        $this->assertEquals(3, $errorReport->errorCount());
    }


    public function testGetCodes ()
    {
        $errorReport = new ESys_Validator_ErrorReport($this->errorData);
        $this->assertEquals(array(1,2,3), $errorReport->getCodes());
    }


    public function testGetMessages ()
    {
        $errorReport = new ESys_Validator_ErrorReport($this->errorData);
        $actualMessageList = $errorReport->getMessages();
        $expectedMessageList = array(
            $this->errorData['email'][0]['message'],
            $this->errorData['first_name'][0]['message'],
            $this->errorData['first_name'][1]['message'],
        );
        
        $this->assertEquals($expectedMessageList, $actualMessageList);
    }

    /**
     * @dataProvider getMessagesByFieldData
     */
    public function testGetMessagesByField ($fieldName, $expectedMessageList)
    {
        $errorReport = new ESys_Validator_ErrorReport($this->errorData);
        $this->assertEquals($expectedMessageList, $errorReport->getMessages($fieldName));
    }


    public function getMessagesByFieldData()
    {
        return array(
            array(
                'first_name',
                array(
                    $this->errorData['first_name'][0]['message'],
                    $this->errorData['first_name'][1]['message'],
                )
            ),
            array(
                'email',
                array(
                    $this->errorData['email'][0]['message'],
                )
            ),
            array(
                'null_field',
                array()
            ),
        );
    }


    public function testGetFields ()
    {
        $errorReport = new ESys_Validator_ErrorReport($this->errorData);
        $expectedFieldList = array(
            'email',
            'first_name',
        );
        
        $this->assertEquals($expectedFieldList, $errorReport->getFields());
    }


    public function testGetRawData ()
    {
        $errorReport = new ESys_Validator_ErrorReport($this->errorData);
        $this->assertEquals($this->errorData, $errorReport->getRawData());
    }

}