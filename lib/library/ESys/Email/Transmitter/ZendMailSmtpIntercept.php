<?php
/**
 * @package ESys
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
            implode("\n", $message->to())."\n".
            "--------------------------------------------\n\n";
        $mail->setBodyText($interceptMessage.$message->bodyText());
        if ($html = $message->bodyHtml()) {
            $html = nl2br(esc_html($interceptMessage)) . $html;
            $mail->setBodyHtml($html);
        }
        $mail->setFrom($message->from());
        $mail->setSubject($message->subject());
        $mail->addTo($this->config['interceptAddress']);
        return $mail;
    }


}
