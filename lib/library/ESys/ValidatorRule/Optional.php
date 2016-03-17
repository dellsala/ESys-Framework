<?php
/**
 * @package ESys
 */


/**
 * @package ESys
 */
class ESys_ValidatorRule_Optional extends ESys_ValidatorRule
{

    private $validatorRule;

    /**
     * @param ESys_ValidatorRule $validatorRule
     */
    public function __construct ($validatorRule)
    {
        $this->validatorRule = $validatorRule;
    }

    /**
     * @param mixed $value
     * @return boolean
     */
    public function validate ($value)
    {
        if (empty($value)) { return true; }
        return $this->validatorRule->validate($value);
    }

}    

