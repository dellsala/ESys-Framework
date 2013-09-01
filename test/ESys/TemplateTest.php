<?php


require_once 'ESys/Template.php';

class ESys_TemplateTest extends PHPUnit_Framework_TestCase {


    public function testSetMethod ()
    {
        $template = new ESys_Template(dirname(__FILE__).'/TemplateTest/test.tpl.php');
        $expectedValue = 'bar';
        $template->set('foo', $expectedValue);
        $this->assertEquals($expectedValue, $template->getRequired('foo'));
    }


    public function testGetMissingRequiredVariable ()
    {
        $template = new ESys_Template(dirname(__FILE__).'/TemplateTest/test.tpl.php');
        
        $this->setExpectedException('Exception');
        $template->getRequired('doesNotExist');
    }
}