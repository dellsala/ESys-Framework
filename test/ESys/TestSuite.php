<?php

require_once 'PHPUnit/Framework.php';
require_once 'ESys/PHPUnit/TestSuiteBuilder.php';


ESys_TestSuite::configureCodeCoverage();


class ESys_TestSuite extends PHPUnit_Framework_TestSuite {


    public static function configureCodeCoverage ()
    {
        $coverageBaseDir = dirname(dirname(dirname(__FILE__))).'/lib/library/ESys';
        PHPUnit_Util_Filter::addDirectoryToWhitelist($coverageBaseDir);
        PHPUnit_Util_Filter::removeFileFromWhitelist($coverageBaseDir.'/Feed/BlipTv.php');
        PHPUnit_Util_Filter::removeFileFromWhitelist($coverageBaseDir.'/Feed/YouTube.php');
        PHPUnit_Util_Filter::removeFileFromWhitelist($coverageBaseDir.'/MicroCms.php');
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