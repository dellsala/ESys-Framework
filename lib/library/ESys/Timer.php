<?php

/**
 * @package ESys
 */
class ESys_Timer {

    private $startTime;
    private $endTime;


    public function __construct ()
    {
        $this->start();
    }


    /**
     * @return void
     */
    public function start ()
    {
        $this->startTime = $this->_microtimeFloat();
    }


    /**
     * @return double
     */
    public function elapsed ()
    {
        $currentTime = $this->_microtimeFloat();
        return $currentTime - $this->startTime;
    }


    private function _microtimeFloat ()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }

}