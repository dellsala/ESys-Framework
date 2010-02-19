<?php

require_once 'ESys/DB/SqlBuilder.php';
require_once 'ESys/DB/Connection.php';


class ESys_DB_SqlBuilderTest extends PHPUnit_Framework_TestCase {


    public function setUp ()
    {
        $this->sqlBuilder = new ESys_DB_SqlBuilder(
            new ESys_DB_SqlBuilderTest_Connection()
        );
    }


    /**
     * @dataProvider sqlConversionValues
     */
    public function testConvertsPhpValuesIntoSqlValues ($value, $convertedValue)
    {
        $this->assertSame($convertedValue, $this->sqlBuilder->convertValue($value));
    }


    public function sqlConversionValues ()
    {
        return array(
            array(
                'value' => null,
                'convertedValue' => 'NULL',
            ),
            array(
                'value' => 100,
                'convertedValue' => '100',
            ),
            array(
                'value' => '100',
                'convertedValue' => '100',
            ),
            array(
                'value' => -100,
                'convertedValue' => '-100',
            ),
            array(
                'value' => '-100',
                'convertedValue' => "'-100'",
            ),
            array(
                'value' => 100.01,
                'convertedValue' => "'100.01'",
            ),
            array(
                'value' => "100.01",
                'convertedValue' => "'100.01'",
            ),
            array(
                'value' => '0100',
                'convertedValue' => "'0100'",
            ),
        );
    }


}



class ESys_DB_SqlBuilderTest_Connection extends ESys_DB_Connection {


    public function __construct ()
    {
    }


    public function escape ($string)
    {
        return addslashes($string);
    }


}