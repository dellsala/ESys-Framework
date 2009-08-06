<?php
require_once 'ESys/EmailMessage.php';

/**
 * @package ESys
 */
class ESys_FormMailer
{

    private $data = array();
    private $sendEmptyFields = false;
    private $fieldFormat = "[ %s ]\n%s\n\n";


    /**
     * @param boolean $value
     * @return void
     */
    public function sendEmptyFields ($value) { $this->sendEmptyFields = (boolean) $value; } 


    /**
     * @param string $value
     * @return void
     */
    public function setFieldFormat ($value) { $this->fieldFormat = $value; } 


    /**
     * @param array $data
     * @return void
     */
    public function setData ($data)
    {
        $this->data = $data;
    }


    /**
     * @return array
     */
    public function getdata ()
    {
        return $this->data;
    }


    /**
     * @param string $toEmail
     * @param string $subject
     * @param string $formEmail
     * @param array $extraHeader
     * @return boolean
     */
    public function sendEmail ($toEmail, $subject, $fromEmail = null, $extraHeaders = array())
    {
        $mailer = new ESys_EmailMessage();
        if (empty($fromEmail)) {
            $fromEmail = 'noreply@'.$_SERVER['SERVER_NAME'];
        }
        $mailer->setFrom($fromEmail);
        $mailer->setTo($toEmail);
        $mailer->setSubject($subject);
        if (! empty($extraHeaders)) {
          foreach ($extraHeaders as $name=>$value) {
            $mailer->setHeader($name, $value);
          }
        }
        
        $body = '';
        foreach ($this->data as $key => $value) {
            if ((!$this->sendEmptyFields) && empty($value)) { continue; }
            $key = strtoupper(str_replace('_', ' ', $key));
            $body .= sprintf($this->fieldFormat, $key, $value);
        }
        
        $mailer->setBody($body);
        
        if (! $mailer->send()) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.'(): unexpected error while '.
                'trying to send email.', E_USER_WARNING);
            return false;
        }
        return true;
    }

}


