<?php
/**
 * @package ESys
 */


/**
 * @package ESys
 */
class ESys_ValidatorRule_MinLength extends ESys_ValidatorRule
{

    private $minLenth;

    /**
     * @param int $minLength
     */
    public function __construct ($minLength)
    {
        $this->minLength = $minLength;
    }

    /**
     * @param mixed $value
     * @return boolean
     */
    public function validate ($value)
    {
        if (is_array($value)) {
            return count($value) >= $this->minLength;
        }
        $value = (string) $value;
        return strlen($value) >= $this->minLength;
    }

}

