<?php
/**
 * @package ESys
 */


/**
 * The validator rule interface used with the ESys_Validator class.
 *
 * @package ESys
 */
abstract class ESys_ValidatorRule
{

    protected $validator;

    /**
     * @param mixed $value
     * @return boolean
     */
    abstract public function validate ($value);

    /**
     * @param ESys_Validator $validator
     * @return void
     */
    public function setValidator ($validator) 
    {
        $this->validator = $validator;
    }

}

