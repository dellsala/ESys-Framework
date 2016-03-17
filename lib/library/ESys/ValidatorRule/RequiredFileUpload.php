<?php
/**
 * @package ESys
 */


/**
 * @package ESys
 */
class ESys_ValidatorRule_RequiredFileUpload extends ESys_ValidatorRule
{

    /**
     * @param mixed $value
     * @return boolean
     */
    public function validate ($value)
    {
        if (! $value instanceof ESys_File_Upload) {
            return false;
        }
        return $value->submitted();
    }

}