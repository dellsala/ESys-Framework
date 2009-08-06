<?php

require_once 'ESys/File.php';


/**
 * @package ESys
 */
class ESys_Logger {


    private $file;


    private $sessionName;


    private $fileHandle;


    /**
     * @param string|ESys_File $fileObject
     * @param string $sessionName
     */
    public function __construct ($fileObject, $sessionName)
    {
        if (! is_object($fileObject)) {
            $fileObject = new ESys_File($fileObject);
        }
        $this->file = $fileObject;
        $this->sessionName = $sessionName;
    }


    private function prepareFile ()
    {
        if (! $this->file->isOpen()) {
            $fileIsNew = ! $this->file->exists();
            if (! $this->file->open('a+')) {
                trigger_error(__CLASS__.'::'.__FUNCTION__.
                    '(): could not open log file for writing', E_USER_WARNING);
                return false;
            }
            if ($fileIsNew) {
                $this->file->chmod(0666);
            }
        }
        return true;
    }


    /**
     * @param string $message
     * @return boolean
     */
    public function log ($message)
    {
        if (! $this->prepareFile()) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.
                '(): unable to log message due to a file error.', E_USER_WARNING);
            return false;
        }
        $logLine = date('Y-m-d H:i:s')." <{$this->sessionName}> {$message}\n";
        $this->file->lock('w');
        $this->file->write($logLine);
        $this->file->unlock();
        return true;
    }


    /**
     * @return void
     */
    public function __destruct ()
    {
        if ($this->file->isOpen()) {
            $this->file->close();
        }
    }


}