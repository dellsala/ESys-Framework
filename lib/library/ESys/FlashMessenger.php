<?php

/**
 * @package ESys
 */
class ESys_FlashMessenger {
    
    
    private $session;
    
    
    /**
     * @param ESys_Session $session
     */
    public function __construct (ESys_Session $session)
    {
        $this->session = $session;
    }
    
    
    /**
     * @param string $message
     * @return string New message id.
     */
    public function set ($message)
    {
        $id = substr(md5(print_r($message, true)), 0, 10);
        $this->session->set(__CLASS__, $id, $message);
        return $id;
    }
    
    
    /**
     * @param string $id
     * @return string
     */
    public function get ($id)
    {
        if (!$message = $this->session->get(__CLASS__, $id)){
            return null;
        }
        $this->session->delete(__CLASS__, $id);
        return $message;
    }


}