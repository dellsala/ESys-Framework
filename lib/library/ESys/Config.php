<?php

/**
 * The Config class holds the basic configuration info the
 * web app needs to function at the most basic level.
 *
 * @package ESys
 */
class ESys_Config {


    private $settings = array();
    

    /**
     * @param string $configFile 
     */
    public function __construct ($configFile)
    {
        $configArray = $this->fetchRawConfigData($configFile);
        $this->applyCoreSettings($configArray);
        $this->applyExtendedSettings($configArray);
    }


    /**
     * @param string $section
     * @param string $key
     * @param string $value
     * @return void
     */
    protected function set ($section, $key, $value)
    {
        $this->settings[$section][$key] = $value;
    }


    /**
     * Retrieving a config setting value.
     *
     * @param string $section
     * @param string $key
     * @return string
     */
    function get ($section, $key = null)
    {
        if (! isset($key)) {
            $key = $section;
            $section = 'ESys_Core';
        }
        if (! array_key_exists($section, $this->settings)
            || ! array_key_exists($key, $this->settings[$section]))
        {
            trigger_error(__CLASS__.'::'.__FUNCTION__.'(): requested setting '.
                $section.'/'.$key.' does not exist', E_USER_WARNING);
            return null;
        }
        return $this->settings[$section][$key];
    }



    /**
     * @param array $rawConfigData
     * @return void
     */
    protected function applyCoreSettings ($rawConfigData)
    {

        if (! isset($rawConfigData['ESys_Core'])) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.'(): '.
                'ESys_Core section not found in config file.', E_USER_ERROR);
            exit('Fatal Error: Invalid config file format. Check installation.');
        }
        $coreData = $rawConfigData['ESys_Core'];
        
        $libPath = defined('ESYS_LIB_PATH') ? ESYS_LIB_PATH : dirname(dirname(dirname(__FILE__)));
        $this->set('ESys_Core', 'libPath', $libPath);

        if (! isset($coreData['htdocsPath'])) {
            trigger_error('ESys_Config::applyCoreSettings(): '.
                'htdocsPath is undefined. Make sure this setting is '.
                'included in your config.ini file.', E_USER_WARNING);
            exit('Fatal Error: htdocsPath config setting is undefined. Check installation.');
        }
        $this->set('ESys_Core', 'htdocsPath', $coreData['htdocsPath']);

        $this->set('ESys_Core', 'urlBase',
            (isset($coreData['urlBase'])) ? $coreData['urlBase'] : '');

        $this->set('ESys_Core', 'urlDomain',
            (isset($coreData['urlDomain'])) ? $coreData['urlDomain'] : 'localhost');

        $this->set('ESys_Core', 'timezone',
            (isset($coreData['timezone'])) ? $coreData['timezone'] : 'UTC');

        $this->set('ESys_Core', 'displayErrors', 
            (isset($coreData['displayErrors']))
            ? ((boolean) $coreData['displayErrors']) : false);

        $this->set('ESys_Core', 'logErrors', 
            (isset($coreData['logErrors'])) 
            ? ((boolean) $coreData['logErrors']) : true);

        $this->set('ESys_Core', 'logErrorBacktraces', 
            (isset($coreData['logErrorBacktraces'])) 
            ? ((boolean) $coreData['logErrorBacktraces']) : false);

        $this->set('ESys_Core', 'productionMode',
            (isset($coreData['productionMode']))
            ? ((boolean) $coreData['productionMode']) : false);

        $this->set('ESys_Core', 'databaseHost',
            (isset($coreData['databaseHost'])) ? $coreData['databaseHost'] : 'localhost');

        $this->set('ESys_Core', 'databaseUser', 
            (isset($coreData['databaseUser'])) ? $coreData['databaseUser'] : null);

        $this->set('ESys_Core', 'databasePassword', 
            (isset($coreData['databasePassword']))
            ? $coreData['databasePassword'] : null);

        $this->set('ESys_Core', 'databaseName', 
            (isset($coreData['databaseName'])) ? $coreData['databaseName'] : null);

        $this->set('ESys_Core', 'databaseLogEvents', 
            (isset($coreData['databaseLogEvents'])) ? $coreData['databaseLogEvents'] : false);

    }


    /**
     * Applies the extra settings using the config data.
     *
     * By Default, this automatically loads all non-core settings in the config
     * file without any applying any default values or validation. Override this 
     * method in extending classes to add custom defaults or validation for 
     * non-core config fields.
     *
     * @param array $rawConfigData
     * @return void
     */
    protected function applyExtendedSettings ($rawConfigData)
    {
        unset($rawConfigData['ESys_Core']);
        $this->autoApplySettings($rawConfigData);
    }


    /**
     * @param array $rawConfigData
     * @return void
     */
    protected function autoApplySettings ($rawConfigData)
    {
        foreach ($rawConfigData as $section => $sectionData) {
            foreach ($sectionData as $key => $value) {
                $this->set($section, $key, $value);
            }
        }        
    }


    /**
     * Loads configuration information from a
     * configuration file.
     */
    private function fetchRawConfigData($configFile)
    {
        $configArray = parse_ini_file($configFile, true);
        if (! $configArray) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.'(): Failed to load '.
                "config data from file {$configFile}", E_USER_WARNING);
            exit('Fatal Error: Could not open config.ini file. Check installation.');
        }
        return $configArray;
    }
}
