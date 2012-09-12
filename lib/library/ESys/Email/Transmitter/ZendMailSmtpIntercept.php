<?php

require_once 'ESysX/Email/Transmitter/ZendMailSmtp.php';

/**
 * @package AvalonRentals
 */
class ESys_Email_Transmitter_ZendMailSmtpIntercept extends ESys_Email_Transmitter_ZendMailSmtp {


    /**
     * @param mixed
     * @return void
     */
    protected function createZendMail (ESys_Email_Message $message)
    {
        $mail = new Zend_Mail('UTF-8');
        $interceptMessage = "-------------- INTERCEPT MODE --------------\n".
            "Original Intended Recipients:\n".
            implode("\n", $message->get('to'))."\n".
            "--------------------------------------------\n\n";
        $mail->setBodyText($interceptMessage.$message->get('bodyText'));
        if ($html = $message->get('bodyHtml')) {
            $html = nl2br(esc_html($interceptMessage)) . $html;
            $mail->setBodyHtml($html);
        }
        $mail->setFrom($message->get('from'));
        $mail->setSubject($message->get('subject'));
        $mail->addTo($this->config['interceptAddress']);
        return $mail;
    }


}
