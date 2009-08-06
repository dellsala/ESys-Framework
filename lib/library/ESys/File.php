<?php

require_once 'ESys/File/Util.php';


/**
 * @package ESys
 */
class ESys_File
{

    private $filePath;

    private $fileHandle;



    /**
     * @return void
     */
    public static function clearStatCache ()
    {
        clearstatcache();
    }


    /**
     * @param string $filePath
     */
    public function __construct ($filePath)
    {
        $this->filePath = ESys_File_Util::realPath($filePath);
    }


    /**
     * @return string
     */
    public function name ()
    {
        return basename($this->filePath);
    }


    /**
     * @return string
     */
    public function extension ()
    {
        $pathinfo = pathinfo($this->filePath);
        return $pathinfo['extension'];
    }


    /**
     * @return string
     */
    public function mimeType ()
    {
        return ESys_File_Util::mimeType($this->filePath);
    }


    /**
     * @return string
     */
    public function path ()
    {
        return $this->filePath;
    }


    /**
     * @return boolean
     */
    public function exists ()
    {
        return file_exists($this->filePath);
    }
    
    
    /**
     * @return boolean
     */
    public function isWritable ()
    {
        return is_writable($this->filePath);
    }


    /**
     * @return boolean
     */
    public function isReadable ()
    {
        return is_readable($this->filePath);
    }
    

    /**
     * @return boolean
     */
    public function isDirectory ()
    {
        return is_dir($this->filePath);
    }


    /**
     * @return boolean
     */
    public function isOpen ()
    {
        return (boolean) $this->fileHandle;
    }


    /**
     * @return int
     */
    public function lastModified ()
    {
        if (! $this->exists()) {
            return null;
        }
        $mtime = filemtime($this->filePath);
        return $mtime;
    }
    

    /**
     * @param boolean $humanFormat
     * @return string
     */
    public function size ($humanFormat = false)
    {
        if (! $this->exists()) {
            $size = 0;
        } else {
            $size = filesize($this->filePath);
        }
        if ($humanFormat) {
            $size = ESys_File_Util::humanSize($size);
        }
        return $size;
    }


    /**
     * @param int $mode
     * @return boolean
     */
    public function chmod ($mode)
    {
        return chmod($this->filePath, $mode);
    }


    /**
     * @param string $newFilePath
     * @return boolean
     */
    public function rename ($newFilePath)
    {
        if (! rename($this->filePath, $newFilePath)) {
            return false;
        }
        $this->filePath = ESys_File_Util::realPath($newFilePath);
        return true;
    }


    /**
     * @param string $newFilePath
     * @return boolean
     */
    public function move ($newFilePath)
    {
        return $this->rename($newFilePath);
    }


    /**
     * @return boolean
     */
    public function delete ()
    {
        return unlink($this->filePath);
    }


    /**
     * @param string $newFilePath
     * @return ESys_File
     */
    public function copy ($newFilePath)
    {
        if (! copy($this->filePath, $newFilePath)) {
            return false;
        }
        $newFile = new ESys_File($newFilePath);
        return $newFile;
    }


    /**
     * @param string $mode
     * @return boolean
     */
    public function open ($mode = 'r')
    {
        $fh = fopen($this->filePath, $mode);
        if (! $fh) {
            return false;
        }
        $this->fileHandle = $fh;
        return true;
    }


    /**
     * @return boolean
     */
    public function eof ()
    {
        return feof($this->fileHandle);
    }


    /**
     * @param int $length
     * @return string
     */
    public function read ($length = null)
    {
        if (! $this->fileHandle) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.'(): file must be opened first.', 
                E_USER_WARNING);
            return false;
        }
        if (isset($length)) {
            return fread($this->fileHandle, $length);
        }
        return fgets($this->fileHandle);
    }


    /**
     * @param string $string
     * @return boolean
     */
    public function write ($string)
    {
        if (! $this->fileHandle) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.'(): file must be opened first.', 
                E_USER_WARNING);
            return false;
        }
        return fwrite($this->fileHandle, $string);
    }


    /**
     * @return boolean
     */
    public function close ()
    {
        if (! $this->fileHandle) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.'(): file must be opened first.', 
                E_USER_WARNING);
            return false;
        }
        $result = fclose($this->fileHandle);
        unset($this->fileHandle);
        return $result;
    }


    /**
     * @param boolean $sendToStdout
     * @return string|int
     */
    public function contents ($sendToStdout = false)
    {
        if ($sendToStdout) {
            return readfile($this->filePath);
        }
        return implode('', file($this->filePath));
    }


    /**
     * @param string $mode
     * @return boolean
     */
    public function lock ($mode = 'w')
    {
        $lockModes = array(
            'r' => LOCK_SH,
            'w' => LOCK_EX,
        );
        if (! isset($lockModes[$mode])) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.'(): invalid lock mode.', 
                E_USER_WARNING);
            return false;
        }
        return flock($this->fileHandle, $lockModes[$mode]);
    }


    /**
     * @return boolean
     */
    public function unlock ()
    {
        return flock($this->fileHandle, LOCK_UN);
    }


}


