<?php


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
        return new ESys_Email_Transmitter_NullStub();
    }


    protected function buildSendmailTransmitter ()
    {
        return new ESys_Email_Transmitter_Sendmail();
    }


    protected function buildSendmailInterceptTransmitter ()
    {
        return new ESys_Email_Transmitter_SendmailIntercept($this->transmitterConfig['interceptAddress']);
    }


    protected function buildSmtpInterceptTransmitter ()
    {
        $smtpConfig = $this->getSmtpConfig();
        $smtpConfig['interceptAddress'] = $this->transmitterConfig['interceptAddress'];
        $smtpHost = $this->transmitterConfig['smtpHost'];
        return new ESys_Email_Transmitter_ZendMailSmtpIntercept(
            $smtpHost,
            $smtpConfig
        );
    }


    protected function buildSmtpTransmitter ()
    {
        $smtpConfig = $this->getSmtpConfig();
        $smtpHost = $this->transmitterConfig['smtpHost'];
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