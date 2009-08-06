<?php

/**
 * Main application registry.
 * Used to store resources that need to be accessed globally.
 *
 * @package ESys
 */
class ESys_Application {

    private static $instance;
    
    private $registry = array();


    private static function instance ()
    {
        if (! isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }


    /**
     * Set a registry entry.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public static function set ($key, $value)
    {
        $app = self::instance();
        $app->registry[$key] = $value;
    }


    /**
     * Retrieve a registry entry.
     *
     * @param string $key
     * @return mixed
     */
    public static function get ($key)
    {
        $app = self::instance();
        if (! array_key_exists($key, $app->registry)) {
            trigger_error(__CLASS__.'::'.__FUNCTION__."(): {$key} is not registered",
                E_USER_NOTICE);
            return null;
        }
        return $app->registry[$key];
    }


    private function __construct ()
    {
    }


    /**
     * Clears all registered data. Used primarily for resetting
     * environment during testing.
     *
     * @return void
     */
    public static function reset ()
    {
        self::$instance = null;
    }


}



/**
 * Convenience methods for retreving frequently
 * accessing values from ESys_Application
 *
 * @package ESys
 */
class App {


    /**
     * Retrieve a registry entry.
     *
     * @param string $key
     * @return mixed
     */
    public static function get ($key)
    {
        return ESys_Application::get($key);
    }


    /**
     * Retrieve application urlBase value.
     *
     * @return string
     */
    public static function urlBase ()
    {
        return ESys_Application::get('config')->get('urlBase');
    }


    /**
     * Retrieve application libPath value.
     *
     * @return string
     */
    public static function libPath ()
    {
        return ESys_Application::get('config')->get('libPath');
    }


    /**
     * Retrieve application htdocs value.
     *
     * @return string
     */
    public static function htdocsPath ()
    {
        return ESys_Application::get('config')->get('htdocsPath');
    }


    private function __construct ()
    {
    }

}
