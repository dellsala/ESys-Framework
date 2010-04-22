<?php

require_once 'ESys/Factory.php';


/**
 * @package ESys
 */
class ESys_Data_ReporterFactory extends ESys_Factory {


    protected $reportPackageName;


    /**
     * @param string
     */
    public function __construct ($reportPackageName)
    {
        $this->reportPackageName = $reportPackageName;
    }


    /**
     * @param string
     * @return string
     */
    protected function mapIdToClassName ($id)
    {
        $entityName = str_replace(' ', '', ucwords(str_replace('_', ' ', $id)));
        return $this->reportPackageName.'_'.$entityName.'_Reporter';
    }



}