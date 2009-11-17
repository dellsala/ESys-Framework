<?php

/**
 * @package ESys
 */
class ESys_Calendar {


    protected static $dayCountByMonth = array(
        1 => 31,
        2 => 28,
        3 => 31,
        4 => 30,
        5 => 31,
        6 => 30,
        7 => 31,
        8 => 31,
        9 => 30,
        10 => 31,
        11 => 30,
        12 => 31,
    );


    /**
     * Tests for Gregorian leap years.
     *
     * @param int 
     * @return boolean
     */
    public static function isLeapYear ($year)
    {
        $year = (int) $year;
        if (($year % 4) == 0) {
            if (($year % 100) != 0) {
                // divisible by 4 and not divisible by 100
                $isLeapYear = true;
            } else {
                if (($year % 400) == 0) {
                    // divisible by 4, divisible by 100, but also divisible by 400
                    $isLeapYear = true;
                } else {
                    // divisible by 4, divisible by 100, and not divisible by 400
                    $isLeapYear = false;
                }
            }
        } else {
            // not divisible by 4
            $isLeapYear = false;
        }
        return $isLeapYear;
    }


    /**
     * @param int
     * @param int
     * @return int
     */
    public static function daysInMonth ($month, $year = null)
    {
        $month = (int) $month;
        $year = isset($year) ? ((int) $year) : ((int) date('Y'));
        $dayCount = self::$dayCountByMonth[$month];
        if ($month == 2 && self::isLeapYear($year)) {
            $dayCount++;
        }
        return $dayCount;
    }


    /**
     * @param int
     * @return int
     */
    public static function daysInYear ($year = null)
    {
        $year = isset($year) ? ((int) $year) : ((int) date('Y'));
        $dayCount = 0;
        foreach (range(1, 12) as $month) {
            $dayCount += self::daysInMonth($month, $year);
        }
        return $dayCount;
    }


}



/**
 * @package ESys
 */
class ESys_Calendar_Month {


    protected $firstDate;
    
    protected $year;
    
    protected $month;

    protected $firstDayOfWeek;

    
    /**
     * @param int
     * @param int
     * @param int
     */
    public function __construct ($month, $year, $firstDayOfWeek = 1)
    {
        try {
            $this->firstDate = new DateTime($year.'-'.$month.'-01');
        } catch (Exception $e) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.'(): '.$e->getMessage(), E_USER_WARNING);
            $year = date('y');
            $month = date('m');
            $this->firstDate = new DateTime($year.'-'.$month.'-01');
        }
        $this->month = $month;
        $this->year = $year;
        $this->firstDayOfWeek = $firstDayOfWeek;
    }


    /**
     * @return array Array of ESys_Calendar_Week objects
     */
    public function getWeeks ()
    {
        $firstDayDayOfWeeek = ((int) $this->firstDate->format('w')) + 1;
        $firstDayOffset = $firstDayDayOfWeeek - $this->firstDayOfWeek;
        if ($firstDayOffset < 0) {
            $firstDayOffset += 7;
        }
        $daysInMonth = ESys_Calendar::daysInMonth($this->month, $this->year);
        $weekCount = ceil(($firstDayOffset + $daysInMonth) / 7);
        $nextDate = $this->firstDate;
        $weeks = array();
        for ($i=0; $i < $weekCount; $i++) {
            if ($i == ($weekCount - 1)) {
                $nextDate = new DateTime($this->year.'-'.$this->month.'-'.$daysInMonth);
            }
            $weeks[] = new ESys_Calendar_Week($nextDate, $this->firstDayOfWeek);
            $nextDate = new DateTime($nextDate->format('Y-m-d')." + 7 days");
        }
        return $weeks;
    }


    /**
     * @return string
     */
    public function getName ()
    {
        return $this->firstDate->format('F');
    }


    /**
     * @return string
     */
    public function getYear ()
    {
        return $this->firstDate->format('Y');
    }


}



/**
 * @package ESys
 */
class ESys_Calendar_Week {


    protected $firstDate;


    protected $targetMonth;
    

    protected $firstDayOfWeek;


    /**
     * @param DateTime
     * @param int
     */
    public function __construct ($date, $firstDayOfWeek = 1)
    {
        if (! $date instanceof DateTime) {
            $date = new DateTime($date);
        }
        $this->targetMonth = (int) $date->format('n');
        $dayOfWeek = ((int) $date->format('w')) + 1;
        if ($dayOfWeek == $firstDayOfWeek) {
            $firstDate = $date;
        } else {
            $daysOffset = $dayOfWeek - $firstDayOfWeek;
            if ($daysOffset < 0) {
                $daysOffset += 7;
            }
            $firstDate = new DateTime($date->format('Y-m-d')." - {$daysOffset} days");
        }
        $this->firstDate = $firstDate;
        $this->firstDayOfWeek = $firstDayOfWeek;
    }


    /**
     * @param boolean
     * @return array Array of DateTime objects
     */
    public function getDates ($trimMonth = false)
    {
        $dates = array();
        for ($i=0; $i<7; $i++) {
            if ($i == 0) {
                $nextDate = $this->firstDate;
            } else {
                $nextDate = new DateTime(
                    $this->firstDate->format('Y-m-d')." + {$i} days");
            }
            if ($trimMonth && $nextDate->format('n') != $this->targetMonth) {
                $dates[] = null;
            } else {
                $dates[] = $nextDate;
            }
        }
        return $dates;
    }


}
