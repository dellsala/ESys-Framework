<?php

require_once 'PHPUnit/Framework.php';
require_once 'ESys/ArrayAccessor.php';


class ESys_ArrayAccessorTest extends PHPUnit_Framework_TestCase {


    protected $inputArray;


    public function setup ()
    {
        $this->arrayData = array(
            'hello' => 'world',
            'John' => 'Doe',
            'username' => 'admin',
        );
        $this->arrayAccessor = new ESys_ArrayAccessor($this->arrayData);
    }


    public function testGettingSingleElement ()
    {
        $this->assertEquals('world', $this->arrayAccessor->get('hello'));
    }


    public function testGettingMissingElement ()
    {
        $this->assertNull($this->arrayAccessor->get('not_set'));
    }


    public function testGettingMissingElementWithDefault ()
    {
        $defaultValue = 'default_value';
        $this->assertEquals($defaultValue, $this->arrayAccessor->get('not_set', $defaultValue));
    }


    public function testGettingExistingElementWithDefault ()
    {
        $this->assertEquals('Doe', $this->arrayAccessor->get('John', 'Smith'));
    }


    public function testGettingArray ()
    {
        $this->assertEquals(
            $this->arrayData, 
            $this->arrayAccessor->get(array_keys($this->arrayData))
        );
    }


    public function testGettingArrayWithSingleDefault ()
    {
        $expectedArray = array(
            'foo' => 'default',
            'hello' => 'world',
            'bar' => 'default',
        );
        $this->assertEquals(
            $expectedArray,
            $this->arrayAccessor->get(array_keys($expectedArray), 'default')
        );
    }


    public function testGettingArrayWithMultipleDefault ()
    {
        $expectedArray = array(
            'username' => 'admin',
            'foo' => 'default2',
            'bar' => 'default3',
        );
        $defaults = array(
            'default1',
            'default2',
            'default3',
        );
        $this->assertEquals(
            $expectedArray,
            $this->arrayAccessor->get(array_keys($expectedArray), $defaults)
        );
    }


}