<?php

/**
 * A base class that provides a generic api for accessing a fixed set of
 * attributes without having to write custom setters and getters.
 *
 * Implement the getFieldList() method to return an array of the attributes
 * that the extending class will support.
 *
 * @package ESys
 */
 abstract class ESys_Data_Record {


    protected $data = array();


    /**
     * @param array $data
     */
    public function __construct ($data = null)
    {
        $this->initFields();
        if (isset($data)) {
            $this->setAll($data);
        }
    }


    /**
     * @return array
     */
    abstract public function getFieldList ();


    /**
     * @return void
     */
    protected function initFields ()
    {
        $fieldList = $this->getFieldList();
        foreach ($fieldList as $field) {
            $this->data[$field] = null;
        }        
    }


    /**
     * @param string $fieldName
     * @param mixed $value
     * @return void
     */
    public function set ($fieldName, $value)
    {
        $fieldList = $this->getFieldList();
        if (! in_array($fieldName, $fieldList)) {
            trigger_error(get_class($this).'::'.__FUNCTION__.'(): '.
                "field '".$fieldName."' is not registered", E_USER_WARNING);
            return;
        }
        $this->data[$fieldName] = $value;
        return;
    }


    /**
     * @param array $data
     * @return void
     */
    public function setAll ($data)
    {
        $fieldList = $this->getFieldList();
        foreach ($fieldList as $field) {
            if (array_key_exists($field, $data)) {
                $this->set($field, $data[$field]);
            }
        }
        return;
    }


    /**
     * @param string $fieldName
     * @return mixed
     */
    public function get ($fieldName)
    {
        $fieldList = $this->getFieldList();
        if (! in_array($fieldName, $fieldList)) {
            trigger_error(get_class($this).'::'.__FUNCTION__.'(): '.
                "field '".$fieldName."' is not registered", E_USER_WARNING);
            return null;
        }
        $value = $this->data[$fieldName];
        return $value;
    }



    /**
     * @return array
     */
    public function getAll ()
    {
        $data = array();
        foreach ($this->getFieldList() as $field) {
            $data[$field] = $this->get($field);
        }
        return $data;
    }


    /**
     * @return string
     */
    public function getId ()
    {
        return $this->get('id');
    }


    /**
     * @param boolean $setToTrue
     * @return boolean
     */
    public function isNew ($setToTrue = null)
    {
        if (isset($setToTrue)) {
            $this->set('id', null);
        }
        $isNew = $this->get('id');
        return empty($isNew);
    }


}