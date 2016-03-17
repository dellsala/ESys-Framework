<?php
/**
 * @package ESys
 */


/**
 * @package ESys
 */
class ESys_ValidatorRule_RepeatField extends ESys_ValidatorRule
{

    private $fieldName;

    /**
     * @param string $fieldName
     */
    public function __construct ($fieldName)
    {
        $this->fieldName = $fieldName;
    }

    /**
     * @param mixed $value
     * @return boolean
     */
    public function validate ($value)
    {
        $otherData = $this->validator->getData();
        if (! isset($otherData[$this->fieldName])) { return false; }
        return ((string) $value) === ((string) $otherData[$this->fieldName]);
    }

}

