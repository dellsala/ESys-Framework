<?php
/**
 * @package ESys
 */



/**
 * @package ESys
 */
class ESys_ValidatorRule_MaxLength extends ESys_ValidatorRule
{

    private $maxLength;

    /**
     * @param int $maxLength
     */
    public function __construct ($maxLength)
    {
        $this->maxLength = $maxLength;
    }

    /**
     * @param mixed $value
     * @return boolean
     */
    public function validate ($value)
    {
        if (is_array($value)) {
            return count($value) <= $this->maxLength;
        }
        $value = (string) $value;
        return strlen((string) $value) <= $this->maxLength;
    }

}

