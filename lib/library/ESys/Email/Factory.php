<?php

require_once 'ESys/ArrayAccessor.php';
require_once 'ESysX/Email/Message.php';

class ESys_Email_Factory {


    protected $systemAddress;
    protected $transmitterConfig;
    protected $transmitter;


    public function __construct ($config)
    {
        $config = new ESys_ArrayAccessor($config);
        $this->systemAddress = $config->get('systemAddress');
        $this->transmitterConfig = $config->get('transmitterConfig');
    }


    public function newMessage ()
    {
        $message = new ESys_Email_Message();
        $message->setTo($this->systemAddress);
        $message->setFrom($this->systemAddress);
        return $message;
    }


    public function getTransmitter ()
    {
        if (! $this->transmitter) {
            $transmitterBuildMethod = 
                'build'.ucfirst($this->transmitterConfig['type']).'Transmitter';
            $this->transmitter = $this->{$transmitterBuildMethod}();
        }
        return $this->transmitter;
    }


    protected function buildSilentTransmitter ()
    {
        require_once 'ESysX/Email/Transmitter/NullStub.php';
        return new ESys_Email_Transmitter_NullStub();
    }


    protected function buildSendmailTransmitter ()
    {
        require_once 'ESysX/Email/Transmitter/Sendmail.php';
        return new ESys_Email_Transmitter_Sendmail();
    }


    protected function buildSendmailInterceptTransmitter ()
    {
        require_once 'ESysX/Email/Transmitter/SendmailIntercept.php';
        return new ESys_Email_Transmitter_SendmailIntercept($this->transmitterConfig['interceptAddress']);
    }


    protected function buildInterceptTransmitter ()
    {
        $smtpConfig = $this->getSmtpConfig();
        $smtpConfig['interceptAddress'] = $this->transmitterConfig['interceptAddress'];
        $smtpHost = $this->transmitterConfig['smtpHost'];
        require_once 'ESysX/Email/Transmitter/ZendMailSmtpIntercept.php';
        return new ESys_Email_Transmitter_ZendMailSmtpIntercept(
            $smtpHost,
            $smtpConfig
        );
    }


    protected function buildSmtpTransmitter ()
    {
        $smtpConfig = $this->getSmtpConfig();
        $smtpHost = $this->transmitterConfig['smtpHost'];
        require_once 'ESysX/Email/Transmitter/ZendMailSmtp.php';
        return new ESys_Email_Transmitter_ZendMailSmtp(
            $smtpHost,
            $smtpConfig
        );
    }


    protected function getSmtpConfig ()
    {
        $config = array(
            'username' => $this->transmitterConfig['smtpUsername'],
            'password' => $this->transmitterConfig['smtpPassword'],
            'port' => $this->transmitterConfig['smtpPort'],
        );
        if (! empty($config['username']) && ! empty($config['password'])) {
            $config['auth'] = 'login';
        }
        return $config;
    }


}