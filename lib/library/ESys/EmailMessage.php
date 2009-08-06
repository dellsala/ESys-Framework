<?php

/**
 * @package ESys
 */
class ESys_EmailMessage {

    private $headerList = array();
    private $from;
    private $toList = array();
    private $subject;
    private $body;
    private $testMode = false;
    private $testOptions = array();
    

    public function __construct ()
    {
    }


    /**
     * @param string $name
     * @param string $value
     * @return void
     */
    public function setHeader ($name, $value)
    {
        $value = $this->cleanHeaderValue($value);
        $this->headerList[$name] = $value;
    }


    /**
     * @param string $value
     * @return void
     */
    public function setFrom ($value) 
    {
        $this->setHeader('From', $value);
        $this->setHeader('Reply-To', $value);
        $this->setHeader('Return-Path', $value);
    } 
    
    
    /**
     * @param string|array $emailList
     * @return void
     */
    public function setTo ($emailList) 
    { 
        if (! is_array($emailList)) {
            $emailList = array($emailList);
        }
        foreach ($emailList as $i => $email) {
            $emailList[$i] = $this->cleanHeaderValue($email);
        }
        $this->toList = $emailList; 
    } 
    
    
    /**
     * @param string $value
     * @return void
     */
    public function setSubject ($value) 
    { 
        $value = $this->cleanHeaderValue($value);
        $this->subject = $value; 
    } 


    /**
     * @param string $value
     * @return void
     */
    public function setBody ($value) 
    { 
        $this->body = $value; 
    } 
    
    
    /**
     * @param string $value
     * @return void
     */
    public function setTestMode ($value) 
    { 
        $this->testMode = (boolean) $value; 
    } 
    
    
    /**
     * @param array $value
     * @return void
     */
    public function setTestOptions ($value) 
    { 
        $this->testOptions = $value; 
    } 

    
    /**
     * @return boolean
     */
    public function send ()
    {
        $this->setHeader('Message-ID', '<'.md5(time()).'@'.$_SERVER['SERVER_NAME'].'>');
        $this->setHeader('X-Mailer', "PHP v".phpversion());
        if ($this->testMode) {
            $this->sendMailTest();
            return true;
        }
        return $this->sendMail();
    }


    private function sendMailTest ()
    {
        $testEmail = isset($this->testOptions['to']) ? $this->testOptions['to'] : null;
        $testFile = isset($this->testOptions['file']) ? $this->testOptions['file'] : null;
        if (! $testEmail && ! $testFile) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.'(): '.
                "missing both 'to' and 'file' test options. no action taken.",
                E_USER_WARNING);
            return;
        }
        if ($testEmail) {
            $this->transmit(array($testEmail));
        }
        if ($testFile) {
            $this->writeToFile($testFile);
        }
    }


    private function sendMail ()
    {
        return $this->transmit($this->toList);
    }


    private function transmit ($toList)
    {
        $headers = $this->buildHeaderString($this->headerList);
        $to = $this->buildToString($toList);
        if (! mail($to, $this->subject, $this->body, $headers)) 
        {
            trigger_error(__CLASS__.'::'.__FUNCTION__.'(): '.
                "sending mail failed unexpectedly.",
                E_USER_ERROR);
            return false;            
        }
        return true;
    }


    private function writeToFile ($file)
    {
        $headerList = array_merge(
            array(
                "To" => $this->buildToString($this->toList),
                "Subject" => $this->subject,
            ),
            $this->headerList
        );
        $emailString = $this->buildHeaderString($headerList);
        $emailString .= "\r\n\r\n";
        $emailString .= str_replace("\n", "\r\n", $this->body);
        if (! $fh = fopen($file, 'w')) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.'(): '.
                "unable to open file '{$file}' for writting email.",
                E_USER_ERROR);
            return false;
        }
        fwrite($fh, $emailString);
        fclose($fh);
        chmod($file, 0666);
        return true;
    }


    private function cleanHeaderValue ($value)
    {
        $value = str_replace(array("%0A", "%0D", "\n", "\r"), ' ', $value);
        return $value;
    }


    private function buildHeaderString ($headerList)
    {
        $headerString = array();
        foreach ($headerList as $name => $value) {
            $headerString[] = $name.': '.$value;
        }
        $headerString = implode("\r\n", $headerString);
        return $headerString;
    }


    private function buildToString ($emailList)
    {
        $toString = implode(", ", $emailList);
        return $toString;
    }


}

