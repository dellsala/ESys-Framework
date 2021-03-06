<?php
/**
 * @package ESys
 */






/**
 *
 */
class ESys_Bootstrap {

	
	public static function setLibPath ($path)
	{
		if (defined('ESYS_LIB_PATH')) {
			throw new RuntimeException('ESYS_LIB_PATH is already defined');
		}
		define('ESYS_LIB_PATH', $path);
	}
	

    /**
     * @return string
     */
    public static function getLibPath ()
    {
		if (! defined('ESYS_LIB_PATH')) {
			self::defineLibPath(dirname(dirname(dirname(__FILE__))));
		}
        return ESYS_LIB_PATH;
    }


    /**
     * @param string $configFile
     * @return void
     */
    public static function init ($configFile = null)
    {
        if (! $configFile) {
            $configFile = self::getLibPath().'/data/conf/config.ini';
        }
        self::checkSystemRequirements();
        self::setupIncludePath();
        self::normalizeServerVars();
        self::setupApplication($configFile);
    }


    /**
     * @return void
     */
    protected static function checkSystemRequirements ()
    {
        $badSettings = array();
        if (ini_get('register_globals')) {
            $badSettings[] = 'register_globals';
        }
        if (ini_get('magic_quotes_gpc')) {
            $badSettings[] = 'magic_quotes_gpc';
        }
        if (! empty($badSettings)) {
            exit('Fatal Error: Bad php configuration. Turn off the following settings: '.
                implode(', ', $badSettings));
        }
        unset($badSettings);
    }


    /**
     * @return void
     */
    protected static function setupIncludePath ()
    {
        ini_set('include_path', '.'.PATH_SEPARATOR.
            self::getLibPath().'/library'.PATH_SEPARATOR.
            self::getLibPath().'/components');
    }


    private static function normalizeServerVars ()
    {
        // TODO?
    }


    /**
     * @param string $configFile
     * @return void
     */
    protected static function setupApplication ($configFile)
    {
        require_once 'ESys/Application.php';
        require_once 'ESys/Config.php';
        require_once 'ESys/Template.php';
        require_once 'ESys/ErrorReporter.php';
        require_once 'ESys/Logger/ErrorReporterListener.php';

        $config = new ESys_Config($configFile);
        $errorReporter = new ESys_ErrorReporter();

        date_default_timezone_set($config->get('timezone'));

        ESys_Application::set('config', $config);
        ESys_Application::set('errorReporter', $errorReporter);

        if ($config->get('logErrors')) {
            $logFile = $config->get('libPath').'/data/log/error.log';
            if (file_exists($logFile)
                ? is_writable($logFile)
                : is_writable(dirname($logFile)))
            {
                $logSession = substr($_SERVER['SCRIPT_NAME'], strlen($config->get('urlBase')));
                $errorLogger = new ESys_Logger_ErrorReporterListener($logFile, $logSession);
                $errorReporter->addListener($errorLogger);
                ESys_Application::set('errorLogger', $errorLogger);
                if ($config->get('logErrorBacktraces')) {
                    $errorLogger->isLoggingBacktraces(true);
                }
            } else {
                trigger_error(__CLASS__.'::'.__FUNCTION__.'(): log file is not writable. '.
                    'logging disabled.', E_USER_WARNING);
            }
        }

    }


    /**
     * Performs default database connection setup.
     *
     * @return void
     */
    public static function initDatabaseConnection ()
    {
        require_once 'ESys/DB/Connection.php';
        require_once 'ESys/Logger/DBListener.php';
        $conf = ESys_Application::get('config');
        $db = new ESys_DB_Connection(
            $conf->get('databaseUser'),
            $conf->get('databasePassword'),
            $conf->get('databaseName'),
            $conf->get('databaseHost')
        );
        if ($conf->get('databaseLogEvents')) {
            $logFile = $conf->get('libPath').'/data/log/database.log';
            if (file_exists($logFile)
                ? is_writable($logFile)
                : is_writable(dirname($logFile)))
            {
                $logSession = substr($_SERVER['SCRIPT_NAME'], strlen($conf->get('urlBase')));
                        $dbLogger = new ESys_Logger_DBListener($logFile, $logSession);
                $db->addListener($dbLogger);
            } else {
                trigger_error(__CLASS__.'::'.__FUNCTION__.
                    "(): database log file {$logFile} is not writable. ".
                    'logging disabled.', E_USER_NOTICE);
            }
        }
        ESys_Application::set('databaseConnection', $db);
    }


    /**
     * Sets up default session object.
     *
     * @param string Name of the session
     * @return void
     */
    public static function initSession ($sessionName)
    {
        require_once 'ESys/Session.php';
        $conf = ESys_Application::get('config');
        $sessionSavePath = $conf->get('libPath').'/data/session/';
        $session = new ESys_Session($sessionName, null, $sessionSavePath);
        ESys_Application::set('session', $session);
    }



    /**
     * @param ESys_Authenticator_CredentialsChecker
     * @return void
     */
    public static function initAuthenticator (ESys_Authenticator_CredentialsChecker $credentialsChecker)
    {
        require_once 'ESys/Authenticator.php';
        $authenticator = new ESys_Authenticator(
            $credentialsChecker,
            ESys_Application::get('session')
        );
        ESys_Application::set('authenticator', $authenticator);
    }


    /**
     * @param array
     * @return void
     */
    public static function initAutoLoader ($includePathList = null)
    {
        require_once 'ESys/AutoLoader.php';
        $autoLoader = new ESys_AutoLoader($includePathList);
        $autoLoader->register();
        ESys_Application::set('autoLoader', $autoLoader);
    }
    
    
    public static function initEmail ()
    {
        $config = ESys_Application::get('config');
        $emailFactory = new ESys_Email_Factory(array(
            'systemAddress' => $config->get('ESys_Email', 'systemAddress'),
            'transmitterConfig' => array(
                'type' => $config->get('ESys_Email', 'mode'),
                'interceptAddress' => $config->get('ESys_Email', 'interceptAddress'),
                'smtpHost' => $config->get('ESys_Email', 'smtpHost'),
                'smtpPort' => $config->get('ESys_Email', 'smtpPort'),
                'smtpUsername' => $config->get('ESys_Email', 'smtpUsername'),
                'smtpPassword' => $config->get('ESys_Email', 'smtpPassword'),
            )
        ));
        ESys_Application::set('emailFactory', $emailFactory);
    }


}



function esc_html ($string)
{
    return htmlentities($string, ENT_COMPAT, 'UTF-8');
}