<?php

/**
 * @package ESys
 */
class ESys_Data_ReportRecord {


    protected $record;


    /**
     * @param array
     */
    public function __construct (array $record)
    {
        $this->record = $record;
    }


    /**
     * @param string
     * @param array
     * @return mixed
     */
    public function __call ($name, $arguments) 
    {
        if (strpos($name, 'get') !== 0) {
            trigger_error(__CLASS__.'::'.__FUNCTION__."(): unknown method {$name}()", E_USER_ERROR);
            return;
        }
        $fieldName = substr($name, strlen('get'));
        $fieldName = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $fieldName));
        if (! array_key_exists($fieldName, $this->record)) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.
                "(): invalid property '{$fieldName}'", E_USER_ERROR);
            return;
        }
        return $this->record[$fieldName];
    }


    /**
     * @return array
     */
    public function export ()
    {
        return $record;
    }


}