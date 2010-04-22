<?php

require_once 'PHPUnit/Framework.php';
require_once 'ESys/PHPUnit/TestSuiteBuilder.php';

PHPUnit_Util_Filter::removeDirectoryFromFilter(dirname(__FILE__));

class MasterTestSuite extends PHPUnit_Framework_TestSuite {


    public static function suite ()
    {
        $suiteBuilder = new ESys_PHPUnit_TestSuiteBuilder();
        return $suiteBuilder->build(__CLASS__, dirname(__FILE__));
    }


    public function setUp ()
    {
    }


    public function tearDown ()
    {
    }


}