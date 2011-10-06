<?php

class ESys_Data_Record_FileResource {


    protected $urlDirectory;
    
    protected $fileSystemPathDirectory;

    protected $directoryDepth;


    public function __construct ($fileSystemPathDirectory, $urlDirectory = "", $directoryDepth = 0)
    {
        $this->urlDirectory = $urlDirectory;
        $this->fileSystemPathDirectory = $fileSystemPathDirectory;
        $this->directoryDepth = $directoryDepth;
    }


    public function isInstalled ()
    {
        if (! file_exists($this->fileSystemPathDirectory)) {
            return false;
        }
        return ! $this->isFileSystemPathDirectoryEmpty();
    }


    public function install ($sourceFileSystemPath, $filename = null)
    {
        if (is_null($filename)) {
            $filename = basename($sourceFileSystemPath);
        }
        if (! file_exists($sourceFileSystemPath)) {
            trigger_error(__CLASS__.'::'.__FUNCTION__."() source file ".
                "'{$sourceFileSystemPath}' not found.", E_USER_WARNING);
            return false;
        }
        $this->uninstall();
        $this->prepareFileSystemPathDirectory();
        if (! is_writable($this->fileSystemPathDirectory)) {
            trigger_error(__CLASS__.'::'.__FUNCTION__."() target path ".
                "'{$this->fileSystemPathDirectory}' is not writable.", E_USER_WARNING);
            return false;
        }
        $targetFileSystemPath = $this->fileSystemPathDirectory.'/'.$filename;
        if (! rename($sourceFileSystemPath, $targetFileSystemPath)) {
            trigger_error(__CLASS__.'::'.__FUNCTION__."() saving '{$targetFileSystemPath}' ".
                "failed unexpectedly.", E_USER_WARNING);
            return false;
        }
        chmod($targetFileSystemPath, 0666);
        return true;
    }


    public function uninstall ()
    {
        if (! file_exists($this->fileSystemPathDirectory)) {
            return $this->removeFileSystemPathDirectory();
        }
        foreach (glob($this->fileSystemPathDirectory.'/*') as $file) {
            if (! unlink($file)) {
                trigger_error(__CLASS__.'::'.__FUNCTION__."() deleting '{$file}' ".
                    "failed unexpectedly.", E_USER_WARNING);
                return false;
            }
        }
        return $this->removeFileSystemPathDirectory();
    }


    public function fileSystemPath ()
    {
        if (! $filename = $this->filename()) {
            return null;
        }
        return $this->fileSystemPathDirectory.'/'.$filename;
    }


    public function url ()
    {
        if (! $filename = $this->filename()) {
            return null;
        }
        return $this->urlDirectory.'/'.$filename;
    }


    public function filename ()
    {
        $fileList = glob($this->fileSystemPathDirectory.'/*');
        if (empty($fileList)) {
            return null;
        }
        return basename($fileList[0]);
    }


    protected function prepareFileSystemPathDirectory ()
    {
        return $this->prepareFileSystemPathDirectoryToDepth($this->directoryDepth);
    }


    protected function prepareFileSystemPathDirectoryToDepth ($depth)
    {
        $targetPath = $this->fileSystemPathDirectory;
        for ($i = $depth; $i >= 0; $i--) {
            $targetSubDir = $targetPath;
            for ($j=0; $j < $i; $j++) {
                $targetSubDir = dirname($targetSubDir);
            }
            if (! file_exists($targetSubDir)) {
                mkdir($targetSubDir);
                chmod($targetSubDir, 0777);
            }
        }
    }


    protected function removeFileSystemPathDirectory ()
    {
        return $this->removeFileSystemPathDirectoryToDepth($this->directoryDepth);
    }


    protected function removeFileSystemPathDirectoryToDepth ($depth)
    {
        $targetPath = $this->fileSystemPathDirectory;
        for ($i = 0; $i <= $depth; $i++) {
            $targetSubDir = $targetPath;
            for ($j=0; $j < $i; $j++) {
                $targetSubDir = dirname($targetSubDir);
            }
            if (! file_exists($targetSubDir)) {
                continue;
            }
            if (count(glob($targetSubDir.'/*'))) {
                continue;
            }
            rmdir($targetSubDir);
        }
        return true;
    }


    protected function isFileSystemPathDirectoryEmpty ()
    {
        $fileList = array ();
        if ( $handle = opendir ( $this->fileSystemPathDirectory ) ) {
            while ( false !== ( $file = readdir ( $handle ) ) ) {
                if (substr($file, 0, 1) != ".") {
                    $fileList [] = $file;
                }
            }
            closedir ( $handle );
        }
        return (count ($fileList)) ? false : true;
    }



}
