<?php

require_once 'PHPUnit/Framework.php';
require_once 'ESys/Calendar.php';

class ESys_Calendar_WeekTest extends PHPUnit_Framework_TestCase {


    public function setup ()
    {
        date_default_timezone_set('America/New_York');
    }


    public function testGetsDates ()
    {
        
        $week = new ESys_Calendar_Week(new DateTime('2009-12-01'));
        $dates = $week->getDates();
        $firstDate = $dates[0];
        $lastDate = $dates[count($dates)-1];
        $this->assertEquals(7, count($dates));
        $this->assertEquals('2009-11-29', $firstDate->format('Y-m-d'));
        $this->assertEquals('2009-12-05', $lastDate->format('Y-m-d'));
    }


    public function testTrimsDatesNotFromTargetMonth ()
    {
        $targetMonth = 12;
        $firstWeekOfMonth = new ESys_Calendar_Week(
            new DateTime("2009-{$targetMonth}-01")
        );
        $trimNonTargetMonthDates = true;
        $dates = $firstWeekOfMonth->getDates($trimNonTargetMonthDates);
        $this->assertNull($dates[0]);
        $this->assertNull($dates[1]);
        $this->assertNotNull($dates[2]);
        $this->assertEquals($targetMonth, $dates[2]->format('m'));
    }


    public function testDefaultsToSundayAsFirstDayOfWeek ()
    {
        $week = new ESys_Calendar_Week(new DateTime('2009-12-01'));
        $dates = $week->getDates();
        $firstDate = $dates[0];
        $this->assertEquals('Sunday', $firstDate->format('l'));
    }

    public function testAcceptsAlternateFirstDayOfWeek ()
    {
        $wednesdayIndex = 4;
        $week = new ESys_Calendar_Week(new DateTime('2009-12-01'), $wednesdayIndex);
        $dates = $week->getDates();
        $firstDate = $dates[0];
        $this->assertEquals('Wednesday', $firstDate->format('l'));
    }
    

}
