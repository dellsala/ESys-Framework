<?php

/**
 * @package ESys
 */
interface ESys_Email_Transmitter {


    /**
     * @param ESys_Email_Message
     * @return boolean
     */
    public function send (ESys_Email_Message $message);


}