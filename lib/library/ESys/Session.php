<?php
require_once 'ESys/File/Util.php';

/**
 * @package ESys
 */
class ESys_Session {


    private $maxInactivity = null;


    /**
     * @param string $sessionName
     * @param int $maxInactivity
     * @param string $savePath
     */
    public function __construct ($sessionName, $maxInactivity = null, $savePath = null)
    {
        $config = ESys_Application::get('config');
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
    
    private function lazySessionStart ()
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
        $_SESSION[__CLASS__] = isset($_SESSION[__CLASS__]) ? $_SESSION[__CLASS__] : array();
        foreach($_SESSION[__CLASS__] as $package=>$key) {
            $_SESSION[__CLASS__][$package] = isset($_SESSION[__CLASS__][$package]) 
                ? $_SESSION[__CLASS__][$package] 
                : array();
            foreach($_SESSION[__CLASS__][$package] as $value) {
                if (! ($value instanceof ESys_Session_Object)) {
                    continue;
                }
                $className = $value->getClassName();
                if (class_exists($className)) {
                    continue;
                }
                $classFile = $value->getClassFile();
                if (ESys_File_Util::isIncludable($classFile)) {
                    include_once $classFile;
                } else {
                    $isFileIncluded = false;
                    $classNamePartList = explode('_', $className);
                    foreach($classNamePartList as $i=>$part) {
                        $filePath = implode('/', array_slice($classNamePartList, 0, $i+1)).'.php';
                        if (ESys_File_Util::isIncludable($filePath)) {
                            include_once $filePath;
                            $isFileIncluded = true;
                        }
                    }
                    trigger_error(__CLASS__.'::'.__FUNCTION__.
                        '(): class file not found for '.$className.'.', E_USER_WARNING);
                }
            }
        }
        session_write_close();
        session_start();
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
        if (is_object($value)) {
            $value = new ESys_Session_Object($value, $classFile);
        }
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
        $value = ($_SESSION[__CLASS__][$package][$key] instanceof ESys_Session_Object) 
            ? $_SESSION[__CLASS__][$package][$key]->getObject()
            : $_SESSION[__CLASS__][$package][$key];
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


/**
 * @package ESys
 */
class ESys_Session_Object {

      private $className;
      private $classFile;
      private $object;
      
      /**
       * @param object $object
       * @param string $classFile
       */
      public function __construct ($object, $classFile = null)
      {
          $this->className = get_class($object);
          if ($classFile) {
              $this->classFile = $classFile;
          } else {              
              $this->classFile = str_replace('_', '/', $this->className).'.php';
          }
          $this->object = $object;
      }
      
      
      /**
       * @return string
       */
      public function getClassName ()
      {
          return $this->className;
      }
      

      /**
       * @return string
       */
      public function getClassFile ()
      {
          return $this->classFile;
      }
      
      
      /**
       * @return object
       */
      public function getObject ()
      {
          return $this->object;
      }
}