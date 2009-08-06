<?php

require_once 'ESys/File/Upload.php';


class ESys_ValidatorRule_FileUploadStub
    extends ESys_File_Upload
{

    private $config;


    public function __construct ($config)
    {
        $this->config = $config;
    }


    public function transferred ()
    {
        return $this->config['wasTransferred'];
    }


    public function submitted ()
    {
        return $this->config['wasSubmitted'];
    }

}