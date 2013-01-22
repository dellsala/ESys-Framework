<?php
/**
 * @package ESys
 */

require_once 'ESys/Image.php';
require_once 'ESys/Data/Record/FileResource.php';

class ESys_Data_Record_FileResourceImage extends ESys_Data_Record_FileResource {


    protected $jpegQuality;


    public function __construct ($fileSystemPathDirectory, $urlDirectory = "", $directoryDepth = 0, $jpegQuality = 80)
    {
        parent::__construct($fileSystemPathDirectory, $urlDirectory, $directoryDepth);
        $this->jpegQuality = $jpegQuality;
    }



    public function filename ()
    {
        $fileList = glob($this->fileSystemPathDirectory.'/*.raw.???');
        if (empty($fileList)) {
            return null;
        }
        return basename($fileList[0]);
    }


    public function extension ()
    {
        return substr(strrchr($this->filename(), '.'), 1);
    }


    public function install ($sourceFileSystemPath, $filename = null)
    {
        if (is_null($filename)) {
            $filename = basename($sourceFileSystemPath);
        }
        $extension = $this->normalizeFileExtension(substr(strrchr($filename, '.'), 1));
        if (! $this->isExtensionSupported($extension)) {
            trigger_error(__CLASS__.'::'.__FUNCTION__."(): unsupported image type", E_USER_WARNING);
            return false;
        }
        $rawFilename = preg_replace('/\.[^\.]+$/', '', $filename).'.raw.'.$extension;
        return parent::install($sourceFileSystemPath, $rawFilename);
    }


    public function urlByHeight ($height, $extension = null)
    {
        if (! $resizedFileSystemPath = $this->fileSystemPathByHeight($height, $extension)) {
            return false;
        }
        return $this->urlDirectory.'/'.basename($resizedFileSystemPath);
    }


    public function urlByWidth ($width, $extension = null)
    {
        if (! $resizedFileSystemPath = $this->fileSystemPathByWidth($width, $extension)) {
            return false;
        }
        return $this->urlDirectory.'/'.basename($resizedFileSystemPath);
    }


    public function urlByWidthAndHeight ($width, $height, $extension = null)
    {
        if (! $resizedFileSystemPath = $this->fileSystemPathByWidthAndHeight($width, $height, $extension)) {
            return false;
        }
        return $this->urlDirectory.'/'.basename($resizedFileSystemPath);
    }


    public function fileSystemPathByHeight ($height, $extension = null)
    {
        return $this->generatedFileSystemPathBySize(null, $height, $extension);
    }


    public function fileSystemPathByWidth ($width, $extension = null)
    {
        return $this->generatedFileSystemPathBySize($width, null, $extension);
    }


    public function fileSystemPathByWidthAndHeight ($width, $height, $extension = null)
    {
        return $this->generatedFileSystemPathBySize($width, $height, $extension);
    }


    protected function generatedFileSystemPathBySize ($width = null, $height = null, $extension = null)
    {
        $extension = $extension ? $extension : $this->extension();
        $expectedFileSystemPath = $this->expectedFileSystemPathBySize($width, $height, $extension);
        if (file_exists($expectedFileSystemPath)) {
            return $expectedFileSystemPath;
        }
        return $this->generateScaledImageFile($width, $height, $extension);
    }


    protected function expectedFileSystemPathBySize ($width, $height, $extension)
    {
        $sourceFileSystemPath = $this->fileSystemPath();
        if ($width && $height) {
            $targetFileSystemPath = preg_replace(
                '/\.raw\..{3}$/', 
                ".w{$width}.h{$height}.{$extension}", 
                $sourceFileSystemPath
            );
        } else if (!$width && $height) {
            $targetFileSystemPath = preg_replace(
                '/\.raw\..{3}$/', 
                ".h{$height}.{$extension}", 
                $sourceFileSystemPath
            );
        } else {
            $targetFileSystemPath = preg_replace(
                '/\.raw\..{3}$/', 
                ".w{$width}.{$extension}", 
                $sourceFileSystemPath
            );
        }
        return $targetFileSystemPath;
    }


    protected function generateScaledImageFile($width = null, $height = null, $extension = null)
    {
        $sourceFileSystemPath = $this->fileSystemPath();
        $extension = $extension ? $extension : $this->extension();
        
        if (!$this->isExtensionSupported($extension)) {
            trigger_error(__CLASS__.'::'.__FUNCTION__."(): unsupported image type", E_USER_WARNING);
            return false;
        }
        if(!$width && !$height) {
            trigger_error(__CLASS__.'::'.__FUNCTION__."(): width or height argument required", E_USER_WARNING);
            return false;
        }
        if (! is_writable($this->fileSystemPathDirectory)) {
            trigger_error(__CLASS__.'::'.__FUNCTION__."() target path ".
                "'".$this->fileSystemPathDirectory."' is not writable.", E_USER_WARNING);
            return false;
        }            
        $image = new ESys_Image();
        if (! $image->load($sourceFileSystemPath)) {
            trigger_error(__CLASS__.'::'.__FUNCTION__."() loading '{$sourceFileSystemPath}' ".
                "as an image resource failed unexpectedly.", E_USER_WARNING);
            return false;
        }
        if ($width && $height) {
            $image->scaleToMaxHeightAndWidth($height, $width);
        } else if (!$width && $height) {
            $image->scaleToHeight($height);
        } else {
            $image->scaleToWidth($width);
        }
        $targetFileSystemPath = $this->expectedFileSystemPathBySize($width, $height, $extension);
        switch ($extension) {
            case 'gif':
                if (! $image->saveGif($targetFileSystemPath)) {
                    trigger_error(__CLASS__.'::'.__FUNCTION__."() saving '{$targetFileSystemPath}' ".
                        "to a gif file failed unexpectedly.", E_USER_WARNING);
                    return false;
                }
                break;
            case 'png':
                if (! $image->savePng($targetFileSystemPath)) {
                    trigger_error(__CLASS__.'::'.__FUNCTION__."() saving '{$targetFileSystemPath}' ".
                        "to a png file failed unexpectedly.", E_USER_WARNING);
                    return false;
                }
                break;
            case 'jpg':
                if (! $image->saveJpeg($targetFileSystemPath, $this->jpegQuality)) {
                    trigger_error(__CLASS__.'::'.__FUNCTION__."() saving '{$targetFileSystemPath}' ".
                        "to a jpg file failed unexpectedly.", E_USER_WARNING);
                    return false;
                }
                break;
        }
        $image->release();
        chmod($targetFileSystemPath, 0666);
        return $targetFileSystemPath;
    }



    protected function isExtensionSupported ($extension)
    {
        $supportedExtensionList = array(
            'jpg',
            'gif',
            'png',
        );
        return in_array($extension, $supportedExtensionList);
    }


    protected function normalizeFileExtension ($extension)
    {
        $extension == strtolower($extension);
        if ($extension == 'jpeg') {
            $extension = 'jpg';
        }
        return $extension;
    }


}
