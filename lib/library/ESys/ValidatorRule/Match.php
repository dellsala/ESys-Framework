<?php
/**
 * @package ESys
 */

/**
 * @package ESys
 */
class ESys_ValidatorRule_Match extends ESys_ValidatorRule
{

    /**
     * @param string $pattern Regular expression pattern.
     */
    public function __construct ($pattern)
    {
        $this->pattern = $pattern;
    }

    /**
     * @param mixed $value
     * @return boolean
     */
    public function validate ($value)
    {
        return (boolean) preg_match($this->pattern, $value);
    }

}

