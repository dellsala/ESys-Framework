<?php

require_once 'ESys/Factory.php';


class ESys_Data_Store_Factory extends ESys_Factory {


    protected $modelPackageName;


    public function __construct ($modelPackageName)
    {
        $this->modelPackageName = $modelPackageName;
    }


    protected function mapIdToClassName ($id)
    {
        $entityName = str_replace(' ', '', ucwords(str_replace('_', ' ', $id)));
        return $this->modelPackageName.'_'.$entityName.'_DataStore';
    }



}