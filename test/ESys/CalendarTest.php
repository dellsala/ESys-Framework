<?php

require_once 'PHPUnit/Framework.php';
require_once 'ESys/Calendar.php';

class ESys_CalendarTest extends PHPUnit_Framework_TestCase {


    public function setup ()
    {
        date_default_timezone_set('America/New_York');
    }


    public function testCalculatesIfYearIsALeapYear ()
    {
        $leapYear = 2004;
        $nonLeapYear = 1900;
        $this->assertTrue(ESys_Calendar::isLeapYear($leapYear));
        $this->assertFalse(ESys_Calendar::isLeapYear($nonLeapYear));
    }


    public function testCalculatesDaysInMonth ()
    {
        $this->assertEquals(28, ESys_Calendar::daysInMonth(2));
    }


    public function testCalculatesDaysInMonthWithYearContext ()
    {
        $this->assertEquals(29, ESys_Calendar::daysInMonth(2, 2004));
    }


    public function testCalculatesDaysInYear ()
    {
        $leapYear = 2004;
        $nonLeapYear = 1900;
        $this->assertEquals(365, ESys_Calendar::daysInYear($nonLeapYear));
        $this->assertEquals(366, ESys_Calendar::daysInYear($leapYear));
    }




}