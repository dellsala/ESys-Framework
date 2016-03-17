<?php
/**
 * @package ESys
 */


/**
 * @package ESys
 */
class ESys_ValidatorRule_Whitelist extends ESys_ValidatorRule
{

    private $whitelist;
    
    /**
     * @param array $whiteList Array of valid values.
     */
    public function __construct ($whitelist)
    {
        $this->whitelist = $whitelist;
    }

    /**
     * @param mixed $value
     * @return boolean
     */
    public function validate ($value)
    {
        return in_array($value, $this->whitelist);
    }

}

