<?php


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
        $this->assertEquals(31, ESys_Calendar::daysInMonth(1));
    }


    public function testCalculatesDaysInMonthWithYearContext ()
    {
        $leapYear = 2004;
        $nonLeapYear = 1900;
        $this->assertEquals(28, ESys_Calendar::daysInMonth(2, $nonLeapYear));
        $this->assertEquals(29, ESys_Calendar::daysInMonth(2, $leapYear));
    }


    public function testCalculatesDaysInYear ()
    {
        $leapYear = 2004;
        $nonLeapYear = 1900;
        $this->assertEquals(365, ESys_Calendar::daysInYear($nonLeapYear));
        $this->assertEquals(366, ESys_Calendar::daysInYear($leapYear));
    }




}