<?php

/**
 * @package ESys
 */
class ESys_Scaffolding_SourceFileWriter {


    protected $baseDirectory;


    /**
     * @param string
     * @return boolean
     */
    public function setBaseDirectory ($baseDirectory)
    {
        $baseDirectory = rtrim($baseDirectory, '/');
        if (! is_dir($baseDirectory)) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.
                "() target directory '{$baseDirectory}' does not exist.",
                E_USER_WARNING);
            return false;
        }
        if (! is_writable($baseDirectory)) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.
                "() target directory '{$baseDirectory}' is not writable.",
                E_USER_WARNING);
            return false;
        }
        $this->baseDirectory = $baseDirectory;
        return true;
    }



    /**
     * @param string
     * @param string
     * @return boolean
     */
    public function write ($fileSubPath, $source)
    {
        $subDirectoryPartList = explode('/', dirname($fileSubPath));
        $targetDirectory = $this->baseDirectory;
        foreach ($subDirectoryPartList as $subDirectoryPart) {
            $targetDirectory .= '/'.$subDirectoryPart;
            if (! file_exists($targetDirectory) && ! mkdir($targetDirectory)) {
                trigger_error(__CLASS__.'::'.__FUNCTION__.'(): '.
                    "failed to create directory ".$targetDirectory, E_USER_WARNING);
                return false;
            }
            if (! is_dir($targetDirectory)) {
                trigger_error(__CLASS__.'::'.__FUNCTION__.'(): '.
                    "target directory {$targetDirectory} already exists as a file", E_USER_WARNING);
                return false;
            }
            chmod($targetDirectory, 0777);
        }
        $fileName = $this->baseDirectory .'/'.$fileSubPath;
        if (file_exists($fileName)) {
            echo "{$fileName} already exists.\nOverwrite it? (y/n) ";
            if (strtolower(trim(fgets(STDIN))) != 'y') {
                echo "File skipped.\n";
                return true;
            }
        }
        if (file_put_contents($fileName, $source) === false) {
            return false;
        }
        chmod($fileName, 0666);
        return true;
    }



}