<?php

/**
 * For now, only supports text email (not html).
 */
class ESys_Email_Transmitter_Sendmail implements ESys_Email_Transmitter {



    /**
     * @param mixed
     */
    public function __construct ()
    {
    }


    /**
     * @param ESys_Email_Message
     * @return boolean
     */
    public function send (ESys_Email_Message $message)
    {
        if ($message->bodyHtml()) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.'() non-empty html body found. '.
                'Html not currently supported by this transport.', E_USER_NOTICE);
        }
        $messageBody = $message->bodyText();
        $messageRecipient = implode(', ', $message->to());
        $messsageHeaders = 'From: '.$this->sanitizeForEmail($message->from());
        $messageSubject = $this->sanitizeForEmail($message->subject());
        return mail($messageRecipient, $messageSubject, $messageBody, $messsageHeaders);
    }


    protected function sanitizeForEmail ($string)
    {
        return str_replace(array("\n","\r", '"'), '', $string);
    }


}
            
            
