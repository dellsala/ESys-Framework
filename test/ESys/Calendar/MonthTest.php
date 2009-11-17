<?php

require_once 'PHPUnit/Framework.php';
require_once 'ESys/Calendar.php';

class ESys_Calendar_MonthTest extends PHPUnit_Framework_TestCase {


    public function setup ()
    {
        date_default_timezone_set('America/New_York');
    }


    /**
     * @dataProvider dataProviderMonthsNumbersAndExpectedNames
     */
    public function testGetsName ($monthNumber, $expectedMonthName)
    {
        $month = new ESys_Calendar_Month($monthNumber, 2011);
        $this->assertEquals($expectedMonthName, $month->getName());
    }


    public function dataProviderMonthsNumbersAndExpectedNames ()
    {
        return array(
            array(1, 'January'),
            array(6, 'June'),
        );
    }


    public function testGetsYear ()
    {
        $month = new ESys_Calendar_Month(1, 2011);
        $this->assertEquals(2011, $month->getYear());
    }


    public function testGetsWeeks ()
    {
        $monthWith5Weeks = new ESys_Calendar_Month(12, 2009);
        $weekList = $monthWith5Weeks->getWeeks();
        $firstDay = $this->getFirstDateFromWeekList($weekList);
        $lastDay = $this->getLastDateFromWeekList($weekList);
        $this->assertEquals(5, count($weekList));
        $this->assertEquals('Tuesday', $firstDay->format('l'));
        $this->assertEquals(2, array_search($firstDay, $weekList[0]->getDates()),
            'First day should be third date in first week because weeks start on Sunday.');
        $this->assertEquals('2009-12-01', $firstDay->format('Y-m-d'));
        $this->assertEquals('2009-12-31', $lastDay->format('Y-m-d'));
    }


    public function testDefaultsToSundayAsFirstDayOfWeek ()
    {
        $month = new ESys_Calendar_Month(12, 2009);
        $weekList = $month->getWeeks();
        $firstWeekDateList = $weekList[0]->getDates();
        $firstDate = $firstWeekDateList[0];
        $this->assertEquals('Sunday', $firstDate->format('l'));
    }


    public function testAcceptsAnAlternateFirstDayOfWeek ()
    {
        $wednesdayIndex = 4;
        $monthWithWeeksStartingOnWednesday = new ESys_Calendar_Month(12, 2009, $wednesdayIndex);
        $weekList = $monthWithWeeksStartingOnWednesday->getWeeks();
        $firstWeekDateList = $weekList[0]->getDates();
        $firstDate = $firstWeekDateList[0];
        $this->assertEquals('Wednesday', $firstDate->format('l'));
    }


    protected function getFirstDateFromWeekList ($weekList)
    {
        foreach ($weekList[0]->getDates(true) as $date) {
            if (! $date) {
                continue;
            }
            return $date;
        }
        return null;
    }


    protected function getLastDateFromWeekList ($weekList)
    {
        $lastWeek = $weekList[count($weekList) - 1];
        foreach (array_reverse($lastWeek->getDates(true)) as $date) {
            if (! $date) {
                continue;
            }
            return $date;
        }
        return null;
    }


}
