<?php


/**
 * @package ESys
 */
class ESys_Scaffolding_Package {

    protected $base;

    protected $sub;


    /**
     * @param string
     */
    public function __construct ($packageName)
    {
        $packageParts = explode("_", $packageName);
        $this->base = $packageParts[0];
        $this->sub = isset($packageParts[1]) ? $packageParts[1] : 'AdminApp';
    }
    
    /**
     * @return string
     */
    public function base ()
    {
        return $this->base;
    }


    /**
     * @return string
     */
    public function sub ()
    {
        return $this->sub;
    }
    

}
