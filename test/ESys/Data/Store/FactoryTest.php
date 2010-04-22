<?php

require_once 'PHPUnit/Framework.php';
require_once 'ESys/Data/Store/Factory.php';

class ESys_Data_Store_FactoryTest extends PHPUnit_Framework_TestCase {


    protected $modelPackageName = 'ESys_Data_Store_FactoryTest_MyPackage';


    /**
     * @dataProvider provideValidIdToClassMappingData
     */
    public function testInstantiatesObjectsBasedIdToClassConventions($dataStoreId, $expectedClass)
    {
        $factory = new ESys_Data_Store_Factory($this->modelPackageName);
        $dataStore = $factory->getInstance($dataStoreId);
        $this->assertEquals($expectedClass, get_class($dataStore));
    }


    public function provideValidIdToClassMappingData ()
    {
        return array(
            array(
                'user',
                $this->modelPackageName.'_User_DataStore',
            ),
            array(
                'user_address',
                $this->modelPackageName.'_UserAddress_DataStore',
            ),
        );
    }


}


class ESys_Data_Store_FactoryTest_MyPackage_User_DataStore {
}

class ESys_Data_Store_FactoryTest_MyPackage_UserAddress_DataStore {
}