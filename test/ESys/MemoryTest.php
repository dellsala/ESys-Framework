<?php

require_once 'PHPUnit/Framework.php';
require_once 'ESys/Memory.php';

class ESys_MemoryTest extends PHPUnit_Framework_TestCase {


    public function testProvidesMemoryUsed ()
    {
        $memory = new ESys_Memory(new ESys_MemoryTest_MemoryInfo(array(
            'usage' => 500000,
        )));
        $this->assertEquals(500000, $memory->used());
    }


    public function testProvidesMemoryLimit ()
    {
        $memory = new ESys_Memory(new ESys_MemoryTest_MemoryInfo(array(
            'limitIniValue' => '8M',
        )));
        $expectedBytes = 1048576 * 8;
        $this->assertEquals($expectedBytes, $memory->limit());

        $memory = new ESys_Memory(new ESys_MemoryTest_MemoryInfo(array(
            'limitIniValue' => '5K',
        )));
        $expectedBytes = 1024 * 5;
        $this->assertEquals($expectedBytes, $memory->limit());

        $memory = new ESys_Memory(new ESys_MemoryTest_MemoryInfo(array(
            'limitIniValue' => '2G',
        )));
        $expectedBytes = 1073741824 * 2;
        $this->assertEquals($expectedBytes, $memory->limit());

        $memory = new ESys_Memory(new ESys_MemoryTest_MemoryInfo(array(
            'limitIniValue' => '8000000',
        )));
        $expectedBytes = 8000000;
        $this->assertEquals($expectedBytes, $memory->limit());
    }


    public function testProvidesMemoryAvailable ()
    {
        $memory = new ESys_Memory(new ESys_MemoryTest_MemoryInfo(array(
            'usage' => 5000000,
            'limitIniValue' => '8000000',
        )));
        $expectedBytes = 3000000;
        $this->assertEquals($expectedBytes, $memory->available());
    }


    public function testProvidesMemoryPeak ()
    {
        $memory = new ESys_Memory(new ESys_MemoryTest_MemoryInfo(array(
            'peak' => 4000000,
        )));
        $expectedBytes = 4000000;
        $this->assertEquals($expectedBytes, $memory->peak());
    }


    public function testSupportsNoArgumentConstructor ()
    {
        try {
            $memory = new ESys_Memory();
        } catch (Exception $e) {
            $this->fail($e->getMessage());
        }
    }


}


class ESys_MemoryTest_MemoryInfo extends ESys_Memory_GlobalSystemInfo {


    public function __construct ($info)
    {
        $this->info = $info;
    }


    public function usage () 
    {
        return $this->info['usage'];
    }


    public function limitIniValue () 
    {
        return $this->info['limitIniValue'];
    }


    public function peak () 
    {
        return $this->info['peak'];
    }


}