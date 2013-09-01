<?php

require_once 'ESys/Data/Record.php';
require_once 'ESys/Data/Store/Sql.php';
require_once 'ESys/Application.php';
require_once 'ESys/TestUtility/DBConnectionStub.php';


class ESys_Data_Store_SqlTest extends PHPUnit_Framework_TestCase {


    public function setUp ()
    {
        $db = new ESys_TestUtility_DBConnectionStub();
        ESys_Application::set('databaseConnection', $db);
    }


    public function tearDown ()
    {
        ESys_Application::reset();
    }


    public function testFetchNewReturnsExpectedDataRecordType ()
    {
        $userStore = new ESys_Data_Store_SqlTest_User_DataStore();
        $this->assertInstanceOf('ESys_Data_Store_SqlTest_User', $userStore->fetchNew());
    }


    public function testFetchNewReturnsPopulatedDataRecord ()
    {
        $userStore = new ESys_Data_Store_SqlTest_User_DataStore();
        $inputData = array(
            'id' => 25,
            'name' => 'John Doe',
        );
        $expectedData = array(
            'id' => null,
            'name' => 'John Doe',
        );
        $user = $userStore->fetchNew($inputData);
        $this->assertTrue($user->isNew(), 'user is not new');
        $this->assertEquals($expectedData, $user->getAll());
    }


    public function testFetchExistingRecord ()
    {
        $dbStub = ESys_Application::get('databaseConnection');
        $dbStub->registerQuery(
            'SELECT * FROM `user` WHERE `id` = 99', 
            array(array(
                'id' => '99',
                'name' => 'John Doe',
            ))
        );
        $userStore = new ESys_Data_Store_SqlTest_User_DataStore();
        $expectedUser = $userStore->fetchNew(array(
            'name' => 'John Doe',
        ));
        $expectedUser->set('id', 99);
        $actualUser = $userStore->fetch(99);
        $dbStub->reset();
        $this->assertEquals($expectedUser, $actualUser);
    }


}



class ESys_Data_Store_SqlTest_User extends ESys_Data_Record {

    public function getFieldList ()
    {
        return array('id','name');
    }


}


class ESys_Data_Store_SqlTest_User_DataStore extends ESys_Data_Store_Sql {

    protected function getTableName ()
    {
        return 'user';
    }


}


