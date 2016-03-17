<?php
/**
 * @package ESys
 */


/**
 * @package ESys
 */
class ESys_ValidatorRule_Blacklist extends ESys_ValidatorRule
{

    private $blacklist;
    
    /**
     * @param array $blackList Array of invalid values.
     */
    public function __construct ($blacklist)
    {
        $this->blacklist = $blacklist;
    }

    /**
     * @param mixed $value
     * @return boolean
     */
    public function validate ($value)
    {
        return !in_array($value, $this->blacklist);
    }

}

