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
        self::initAuthenticator($packageName);
    }


    /**
     * @param string
     * @return void
     */
    protected static function initAuthenticator ($packageName)
    {
        require_once 'ESys/Authenticator.php';
        require_once 'ESys/Admin/CredentialsChecker.php';

        $conf = ESys_Application::get('config');
        $authenticator = new ESys_Authenticator(
            new ESys_Admin_CredentialsChecker(
                $conf->get($packageName, 'username'),
                $conf->get($packageName, 'password')
            ), 
            ESys_Application::get('session')
        );
        ESys_Application::set('authenticator', $authenticator);
    }


}
