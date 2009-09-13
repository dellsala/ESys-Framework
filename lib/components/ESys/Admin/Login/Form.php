<?php

require_once 'ESys/Form.php';

class ESys_Admin_Login_Form extends ESys_Form {


    protected $hasLoginError = false;


    public function captureInput ($input)
    {
        $input = new ESys_ArrayAccessor($input);
        $this->data = $input->get(array(
            'username',
            'password',
        ));
    }


    public function createValidator ()
    {
        return new ESys_Validator();
    }


    public function hasLoginError ($value = null)
    {
        if (is_null($value)) {
            return $this->hasLoginError;
        }
        $this->hasLoginError = (boolean) $value;
    }


    public function render (ESys_WebControl_Request $request)
    {
        $view = new ESys_Template(dirname(__FILE__).'/templates/form.tpl.php');
        $view->set('request', $request);
        $view->set('form', $this);
        return $view->fetch();
    }


}


