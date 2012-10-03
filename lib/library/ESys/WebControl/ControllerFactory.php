<?php


class ESys_WebControl_ControllerFactory {
    

    public function __construct ()
    {
    }
    
    
    /**
     *
     * @param string $controllerId
     * @return ESys_WebControl_Controller|false
     */
    public function build ($controllerId)
    {
        if (! class_exists($controllerId)) {
            $classFileName = str_replace('_', '/', $controllerId).'.php';
            include_once($classFileName);
            if (! class_exists($controllerId)) {
                return false;
            }
        }
        return new $controllerId();
    }
    
    
}