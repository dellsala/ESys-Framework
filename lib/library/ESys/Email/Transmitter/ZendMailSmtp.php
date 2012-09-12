<?php

require_once 'Zend/Mail.php';
require_once 'Zend/Mail/Transport/Smtp.php';
require_once 'ESysX/Email/Transmitter.php';

/**
 * @package ESys
 */
class ESys_Email_Transmitter_ZendMailSmtp implements ESys_Email_Transmitter {


    protected $host;
    
    protected $config;


    /**
     * @param mixed
     */
    public function __construct ($host, $config = array())
    {
        $this->host = $host;
        $this->config = $config;
    }


    /**
     * @param ESys_Email_Message
     * @return boolean
     */
    public function send (ESys_Email_Message $message)
    {
        $mail = $this->createZendMail($message);
        try {
            $mail->send($this->getTransport());
        } catch (Zend_Mail_Exception $e) {
            trigger_error(get_class($this).'::'.__FUNCTION__."(): sending failed: ".get_class($e)." {$e->getMessage()}", E_USER_WARNING);
            return false;
        }
        return true;
    }


    /**
     * @param mixed
     * @return void
     */
    protected function createZendMail (ESys_Email_Message $message)
    {
        $mail = new Zend_Mail('UTF-8');
        $mail->setBodyText($message->bodyText());
        if ($html = $message->bodyHtml()) {
            $mail->setBodyHtml($html);
        }
        $mail->setFrom($message->from());
        foreach ($message->to() as $address) {
            $mail->addTo($address);
        }
        $mail->setSubject($message->subject());
        return $mail;
    }


    /**
     * @return Zend_Mail_Transport_Abstract
     */
    protected function getTransport ()
    {
        return new Zend_Mail_Transport_Smtp(
            $this->host, $this->config
        );
    }


}
