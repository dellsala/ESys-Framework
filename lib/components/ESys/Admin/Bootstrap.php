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

        require_once 'ESys/Admin/CredentialsChecker.php';
        $conf = ESys_Application::get('config');

        ESys_Bootstrap::initAuthenticator(
            new ESys_Admin_CredentialsChecker(
                $conf->get($packageName, 'username'),
                $conf->get($packageName, 'password')
            )
        );
    }


}
