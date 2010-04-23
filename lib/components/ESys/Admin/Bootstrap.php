<?php

/**
 * @package ESys
 */
class ESys_Admin_Bootstrap {


    /**
     * @param string
     * @return void
     */
    public static function init ($packageName) 
    {
        require_once dirname(__FILE__).'/../../../library/ESys/Bootstrap.php';
        
        ESys_Bootstrap::init();
        ESys_Bootstrap::initDatabaseConnection();
        ESys_Bootstrap::initSession(strtoupper($packageName));
        ESys_Bootstrap::initAutoLoader();

        $conf = ESys_Application::get('config');

        ESys_Bootstrap::initAuthenticator(
            new ESys_Admin_CredentialsChecker(
                $conf->get($packageName, 'username'),
                $conf->get($packageName, 'password')
            )
        );

        $packageNameParts = explode('_', $packageName);
        array_pop($packageNameParts);
        array_push($packageNameParts, 'Domain');
        $domainPackageName = implode('_', $packageNameParts);
        ESys_Application::set(
            'dataStoreFactory',
            new ESys_Data_Store_Factory($domainPackageName)
        );

    }


}
