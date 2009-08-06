<?php

/**
 * Templating Class
 *
 * @package ESys
 */
class ESys_Template {

    private $vars = array(); 

    private $tplfile ;       

    /** 
     * @param string $file The file name you want to load.
     */ 
    public function __construct ($file = null)
    {
        $this->tplfile = $file ;
    } 


    /** 
     * Set a template variable. 
     *
     * Supports passing objects to templates. It will now only
     * try to render the value if it is an object of type {@link ESys_Template}.
     *
     * @param string $name
     * @param mixed $value
     * @return void
     */ 
    public function set ($name, $value)
    { 
        if (is_object($value) && ($value instanceof ESys_Template)) {
            $this->vars[$name ] = $value->fetch();
        } else {
            $this->vars[$name ] = $value;
        }
    } 


    /**
     * Retrieve an optional template variable. Returns a default value
     * if it has not been set.
     * 
     * @param string $name
     * @param mixed $defaultValue
     * @return mixed
     */
    public function getOptional ($name, $defaultValue = null)
    {
        return isset($this->vars[$name])
            ? $this->vars[$name]
            : $defaultValue;
    }


    /**
     * Retrieve a required template variable. 
     * Will trigger a fatal error if the variable hasn't been set.
     * 
     * @param string $name
     * @return mixed
     */
    public function getRequired ($name)
    {
        if (! array_key_exists($name, $this->vars)) {
            $errorMessage = __CLASS__.'::'.__FUNCTION__.
                "(): missing the required variable '{$name}' in template {$this->tplfile}";
            trigger_error($errorMessage, E_USER_ERROR);
            return null;
        }
        return $this->vars[$name];
    }


    /**
     * Retrieve a required object of a specific type. 
     * Will trigger a fatal error if the variable hasn't been set,
     * or if the value isn't an object of the correct type.
     * 
     * @param string $name
     * @param string $expectedClass
     * @return mixed
     */
    public function getRequiredObject ($name, $expectedClass)
    {
        $value = $this->getRequired($name);
        if (! $value instanceof $expectedClass) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.
                "(): variable '{$name}' is not of type {$expectedClass} in template {$this->tplfile}",
                E_USER_WARNING);
        }
        return $value;
    }


    /** 
     * Open, parse, and return the template file as a string. 
     * 
     * @param string $file The template file name.
     * @return string
     */ 
    public function fetch ($file = null)
    {
        if (! $file) {
            $file = $this->tplfile;
        }
        ob_start();
        require $file;
        $contents = ob_get_clean();
        return $contents;
    }

}

