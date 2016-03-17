<?php
/**
 * @package ESys
 */


/**
 * @package ESys
 */
class ESys_ValidatorRule_Phone extends ESys_ValidatorRule
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
        return (boolean) preg_match('/^[0-9\-. ()+]{7,}$/', $value);
    }

}

