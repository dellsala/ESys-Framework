<?php
/**
 * @package ESys
 */


/**
 * @package ESys
 */
class ESys_ValidatorRule_IsoDate extends ESys_ValidatorRule
{

    public function __construct ()
    {
    }

    /**
     * @todo need a better regexp
     * @param mixed $value
     * @return boolean
     */
    public function validate ($value)
    {
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', ((string) $value))) {
            return false;
        }
        list($year, $month, $day) = explode('-',$value);
        $parsedDate = date('Y-m-d', mktime(0,0,0,$month,$day,$year));
        return $parsedDate == $value;
    }

}

