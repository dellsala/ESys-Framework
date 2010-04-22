<?php

require_once 'PHPUnit/Framework.php';
require_once 'ESys/Factory.php';


class ESys_FactoryTest extends PHPUnit_Framework_TestCase {


    public function setup ()
    {
        ESys_FactoryTest_User::$instanceCount = 0;
    }


    public function testIntantiatesObjects ()
    {
        $factory = new ESys_Factory();
        $className = 'ESys_FactoryTest_User';
        $user = $factory->getInstance($className);
        $this->assertEquals($className, get_class($user));
    }


    public function testReturnsSameInstanceWithMultipleCalls ()
    {
        $factory = new ESys_Factory();
        $className = 'ESys_FactoryTest_User';
        $this->assertEquals(0, ESys_FactoryTest_User::$instanceCount,
            'expected 0 instances of users to be created');
        $user1 = $factory->getInstance($className);
        $user2 = $factory->getInstance($className);
        $this->assertEquals(1, ESys_FactoryTest_User::$instanceCount,
            'expected 1 instance of users to be created');
    }


}


class ESys_FactoryTest_User {


    public static $instanceCount = 0;
    
    public function __construct () 
    {
        self::$instanceCount++;
    }


}