<?php


require_once 'ESys/Application.php';


class ESys_ApplicationTest extends PHPUnit_Framework_TestCase {


    public function teardown ()
    {
        ESys_Application::reset();
    }


    public function testGettingAndSetting ()
    {
        ESys_Application::set('name', 'John');
        $this->assertTrue(ESys_Application::get('name') == 'John');
    }


    public function testGettingUnregisteredValue ()
    {
        $this->setExpectedException('Exception');
        $this->assertNull(ESys_Application::get('name'));
    }


    public function testResetting ()
    {
        ESys_Application::set('name', 'John');
        ESys_Application::reset();
        $this->setExpectedException('Exception');
        $this->assertTrue(is_null(ESys_Application::get('name')));
    }


}