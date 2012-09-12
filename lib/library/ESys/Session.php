<?php
require_once 'ESys/File/Util.php';

/**
 * Session management.
 *
 * All objects saved to sessions must be loadable via a registered autoloader.
 *
 * @package ESys
 */
class ESys_Session {


    protected $maxInactivity = null;


    /**
     * @param string $sessionName
     * @param int $maxInactivity
     * @param string $savePath
     */
    public function __construct ($sessionName, $maxInactivity = null, $savePath = null)
    {
        if (session_id()) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.'(): session has already been '.
                'started. Session may not behave correclty.', E_USER_WARNING);
        }
        session_name($sessionName);
        if (isset($savePath)) {
            session_save_path($savePath);
        }
        $this->maxInactivity = $maxInactivity;
        if (isset($this->maxInactivity)) {
            ini_set('session.gc_maxlifetime', $this->maxInactivity);
            $this->lazySessionStart();
        }
    }

    
    protected function lazySessionStart ()
    {
        if (session_id()) {
            return;
        }
        session_start();
        if (isset($this->maxInactivity)) {
            $lastActivity = $this->get(__CLASS__, 'lastActivity');
            if (isset($lastActivity)) {
                if (time() - $lastActivity > $this->maxInactivity) {
                    $this->reset();
                    return;
                }
            }
            $this->set(__CLASS__, 'lastActivity', time());
        }
        if (!array_key_exists(__CLASS__, $_SESSION)) {
            $_SESSION = array();
        }
    }

    
    /**
     * @param string $package
     * @param string $key
     * @param mixed $value
     * @param string $classFile
     * @return void
     */
    public function set ($package, $key, $value, $classFile = null)
    {
        $this->lazySessionStart();
        $_SESSION[__CLASS__][$package][$key] = $value;        
    }

    
    /**
     * @param string $package
     * @param string $key
     * @return mixed
     */
    public function get ($package, $key)
    {
        $this->lazySessionStart();
        if (!isset($_SESSION[__CLASS__][$package][$key])) {
            return null;
        }
        $value = $_SESSION[__CLASS__][$package][$key];
        if ($value instanceof __PHP_Incomplete_Class) {
            trigger_error(__METHOD__.'() you are accessing an inclomplete '.
                'object that failed to unserialize correctly. make sure you '.
                'have an autoloader registered. ', E_USER_WARNING);
        }
        return $value;
    }


    /**
     * @param string $package
     * @param string $key
     * @return void
     */
    public function delete ($package, $key)
    {
        $this->lazySessionStart();
        unset($_SESSION[__CLASS__][$package][$key]);
    }
    
    
    /**
     * @return string
     */
    public function getName ()
    {
        return session_name();
    }
    
    
    /**
     * @return string
     */
    public function getId ()
    {
        $this->lazySessionStart();
        return session_id();
    }
    

    /**
     * @return boolean
     */
    public function regenerateId ()
    {
        $this->lazySessionStart();
        return session_regenerate_id(true);
    }    


    /**
     * @return void
     */
    public function reset ()
    {
        $this->regenerateId();
        session_destroy();
        session_start();
        if (isset($this->maxInactivity)) {
            $this->set(__CLASS__, 'lastActivity', time());        
        }
    }


    /**
     * @return boolean
     */
    public function writeClose ()
    {
        return session_write_close();
    }    

}

