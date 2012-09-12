<?php

class ESys_Email_Transmitter_SendmailIntercept implements ESys_Email_Transmitter {


    protected $interceptAddress;


    /**
     * @param string
     */
    public function __construct ($interceptAddress)
    {
        $this->interceptAddress = $interceptAddress;
    }


    /**
     * @param ESys_Email_Message
     * @return boolean
     */
    public function send (ESys_Email_Message $message)
    {
        $originalRecipientList = $message->get('to');
        $originalBodyText = $message->get('bodyText');
        $message->set('to', $this->interceptAddress);
        ob_start();
?>
--------------------------------------------
EMAIL INTERCEPT MODE -- Original Recipients:
<?php echo implode("\n", $originalRecipientList); ?> 
--------------------------------------------

<?php
        echo $originalBodyText;
        $message->set('bodyText', ob_get_clean());
        $transmitter = new ESys_Email_Transmitter_Sendmail();
        return $transmitter->send($message);
    }


    protected function sanitizeForEmail ($string)
    {
        return str_replace(array("\n","\r", '"'), '', $string);
    }


}
