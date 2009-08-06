<?php

require_once 'ESys/Validator.php';

/**
 * Class that represents a form
 *
 * @package ESys
 */
abstract class ESys_Form {


    /**
     * The form data.
     */
    protected $data = array();


    /**
     * The ESys_Validator for the form.
     */
    protected $validator;


    /**
     *
     */
    public function __construct ()
    {
        $this->captureInput(array()); 
    }


    /**
     * @param array $rawInput
     */
    abstract public function captureInput ($rawInput);


    /**
     * @return array
     */
    public function getData ()
    {
        return $this->data;
    }


    /**
     * @return bool
     */
    public function validate () {
        $validator = $this->getValidator();
        $validator->setData($this->data);
        return $validator->validate();
    }


    /**
     * @return ESys_Validator
     */
    protected function getValidator () {
        if (!$this->validator) {
            $this->validator = $this->createValidator();
        }
        return $this->validator;
    }


    /**
     * @return ESys_Validator
     */
    abstract public function createValidator ();


    /**
     * @return bool
     */
    public function hasErrors ()
    {
        return ($this->getErrorReport()->errorCount() != 0);
    }


    /**
     * @return ESys_Validator_ErrorReport
     */
    public function getErrorReport ()
    {
        
        return $this->getValidator()->getErrorReport();
    }


    /**
     * @param ESys_WebControl_Request $request
     * @return string
     */
    public function render (ESys_WebControl_Request $request)
    {
        $view = new ESys_Template(dirname(str_replace('_', '/', get_class($this))).'/templates/form.tpl.php');
        $view->set('form', $this);
        $view->set('request', $request);
        return $view->fetch(); 
    }


}
