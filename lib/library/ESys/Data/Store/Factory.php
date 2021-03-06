<?php

require_once 'ESys/Factory.php';


/**
 * @package ESys
 */
class ESys_Data_Store_Factory extends ESys_Factory {


    protected $modelPackageName;


    /**
     * @param string
     */
    public function __construct ($modelPackageName)
    {
        $this->modelPackageName = $modelPackageName;
    }


    /**
     * @param string
     * @return string
     */
    protected function mapIdToClassName ($id)
    {
        $entityName = str_replace(' ', '', ucwords(str_replace('_', ' ', $id)));
        return $this->modelPackageName.'_'.$entityName.'_DataStore';
    }



}