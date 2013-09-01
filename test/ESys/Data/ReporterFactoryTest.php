<?php


require_once 'ESys/Data/ReporterFactory.php';

class ESys_Data_ReporterFactoryTest extends PHPUnit_Framework_TestCase {


    protected $reportPackageName = 'ESys_Data_ReporterFactoryTest_MyPackage';


    /**
     * @dataProvider provideValidIdToClassMappingData
     */
    public function testInstantiatesObjectsBasedIdToClassConventions($dataStoreId, $expectedClass)
    {
        $factory = new ESys_Data_ReporterFactory($this->reportPackageName);
        $dataStore = $factory->getInstance($dataStoreId);
        $this->assertEquals($expectedClass, get_class($dataStore));
    }


    public function provideValidIdToClassMappingData ()
    {
        return array(
            array(
                'user',
                $this->reportPackageName.'_User_Reporter',
            ),
            array(
                'userAddress',
                $this->reportPackageName.'_UserAddress_Reporter',
            ),
            array(
                'user_address',
                $this->reportPackageName.'_UserAddress_Reporter',
            ),
        );
    }


}


class ESys_Data_ReporterFactoryTest_MyPackage_User_Reporter {
}

class ESys_Data_ReporterFactoryTest_MyPackage_UserAddress_Reporter {
}