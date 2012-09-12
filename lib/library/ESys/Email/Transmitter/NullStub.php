<?php

class ESys_Email_Transmitter_NullStub {

    
    public function send (ESys_Email_Message $message)
    {
        trigger_error(__CLASS__.'::'.__FUNCTION__."(): message swallowed. Yum!", 
            E_USER_NOTICE);
        return true;
    }
    

}