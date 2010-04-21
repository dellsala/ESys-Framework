<?php

/**
 * @package ESys
 */
class ESys_AutoLoader {


    protected $includePathList = array();


    /**
     * @param array
     */
    public function __construct ($includePathList = null)
    {
        if (! isset($includePathList)) {
            $libPath = dirname(dirname(dirname(__FILE__)));
            $includePathList = array(
                $libPath.'/components',
                $libPath.'/library',
            );
        }
        $this->includePathList = $includePathList;
    }


    /**
     * @return boolean
     */
    public function register ()
    {
        return spl_autoload_register(array($this, 'load'));
    }


    public function unregister ()
    {
        return spl_autoload_unregister(array($this, 'load'));
    }


    /**
     * @param string
     * @return void
     */
    public function load ($className)
    {
        foreach ($this->includePathList as $includePath) {
            if ($this->loadFromDirectory($className, $includePath)) {
                return;
            }
        }
    }


    /**
     * @param string
     * @param string
     * @return boolean
     */
    protected function loadFromDirectory ($className, $includeDirectory)
    {
        $fileName = str_replace('_', '/', $className);
        do {
            $fileName .= '.php';
            if (file_exists($includeDirectory.'/'.$fileName)) {
                require_once($includeDirectory.'/'.$fileName);
                return true;
            }
            $fileName = dirname($fileName);
        } while ($fileName != '.' );
        return false;
    }


}