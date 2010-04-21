<?php

require_once 'PHPUnit/Framework.php';
require_once 'ESys/AutoLoader.php';


class ESys_AutoLoaderTest extends PHPUnit_Framework_TestCase {


    protected $autoLoader = null;


    public function setup ()
    {
    }



    public function testRegistersSelf ()
    {
        $this->autoLoader = new ESys_AutoLoader();
        $this->autoLoader->register();
        if (! $functionList = spl_autoload_functions()) {
            $functionList = array();
        }
        $this->assertTrue(
            in_array(array(get_class($this->autoLoader), 'load'), $functionList),
            'autoloader expected in registered function list'
        );
    }


    public function testUsesDefaultIncludePathList ()
    {
        $this->autoLoader = new ESys_AutoLoader();
        $libPath = dirname(dirname(dirname(__FILE__))).'/lib';
        $expectedIncludePathList = array(
            $libPath.'/components',
            $libPath.'/library',
        );
        $this->assertAttributeEquals($expectedIncludePathList, 
            'includePathList', $this->autoLoader,
            'autoloader expected a predefined include path list');
    }


    public function testSupportsCustomIncludePathList ()
    {
        $includePathList = array(
            '/some/include/path',
            '/some/other/include/path',
        );
        $this->autoLoader = new ESys_AutoLoader($includePathList);
        $this->assertAttributeEquals($includePathList, 
            'includePathList', $this->autoLoader,
            'autoloader expected to have stored custom include path list');
    }


    public function testLoadsClassesBasedOnPearStyleNaming ()
    {
        $this->autoLoader = new ESys_AutoLoader(array(
            dirname(__FILE__).'/AutoLoaderTest/include/'
        ));
        $class = 'Example1_User';
        $this->assertClassIsNotLoadable($class);
        $this->autoLoader->register();
        $this->assertClassIsLoadable($class);
    }


    public function testLoadsClassesWhosDefinitionIsInAParentPackageFile ()
    {
        $this->autoLoader = new ESys_AutoLoader(array(
            dirname(__FILE__).'/AutoLoaderTest/include/'
        ));
        $class = 'Example2_User_Validator';
        $this->assertClassIsNotLoadable($class);
        $this->autoLoader->register();
        $this->assertClassIsLoadable($class);
    }


    public function tearDown ()
    {
        $this->autoLoader->unregister();
    }


    protected function assertClassIsNotLoadable ($class)
    {
        $classIsLoadable = class_exists($class);
        $this->assertFalse(
            $classIsLoadable,
            "expected {$class} class to not be loadable"
        );
    }


    protected function assertClassIsLoadable ($class)
    {
        $classIsLoadable = class_exists($class);
        $this->assertTrue(
            $classIsLoadable,
            "expected {$class} class to be loadable"
        );
    }


}