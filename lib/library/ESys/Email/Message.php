<?php

require_once 'ESys/Data/Record.php';

class ESys_Email_Message {


    protected $from;
    protected $to;
    protected $subject;
    protected $bodyHtml;
    protected $bodyText;
    
    public function __construct ($params)
    {
        $mutablePropertyList = array(
            'from',
            'to',
            'subject',
            'bodyHtml',
            'bodyText',
        );
        $params = new ESys_ArrayAccessor($params);
        foreach ($mutablePropertyList as $property) {
            $this->{'set'.ucfirst($property)}($params->get($property, null));
        }
    }
    
    public function from ()
    {
        return $this->from;
    }


    public function to ()
    {
        return $this->to;
    }


    public function subject ()
    {
        return $this->subject;
    }


    public function bodyHtml ()
    {
        return $this->bodyHtml;
    }


    public function bodyText ()
    {
        return $this->bodyText;
    }
    
    
    public function setFrom ($from)
    {
        $this->from = $from;
    }


    public function setTo ($to)
    {
        $this->to = $to;
    }


    public function setSubject ($subject)
    {
        $this->subject = $subject;
    }


    public function setBodyHtml ($bodyHtml)
    {
        $this->bodyHtml = $bodyHtml;
    }


    public function setBodyText ($bodyText)
    {
        $this->bodyText = $bodyText;
    }

    
}