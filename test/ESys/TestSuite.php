<?php


require_once 'ESys/PHPUnit/TestSuiteBuilder.php';


ESys_TestSuite::configureCodeCoverage();


class ESys_TestSuite extends PHPUnit_Framework_TestSuite {


    public static function configureCodeCoverage ()
    {
        $coverageBaseDir = dirname(dirname(dirname(__FILE__))).'/lib/library/ESys';
        PHPUnit_Util_Filter::addDirectoryToWhitelist($coverageBaseDir);
    }


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