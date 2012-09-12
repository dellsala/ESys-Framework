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
        if ($message->get('bodyHtml')) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.'() non-empty html body found. '.
                'Html not currently supported by this transport.', E_USER_NOTICE);
        }
        $messageBody = $message->get('bodyText');
        $messageRecipient = implode(', ',$message->get('to'));
        $messsageHeaders = 'From: '.$this->sanitizeForEmail($message->get('from'));
        $messageSubject = $this->sanitizeForEmail($message->get('subject'));
        return mail($messageRecipient, $messageSubject, $messageBody, $messsageHeaders);
    }


    protected function sanitizeForEmail ($string)
    {
        return str_replace(array("\n","\r", '"'), '', $string);
    }


}
