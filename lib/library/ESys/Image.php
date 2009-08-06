<?php

/**
 * @package ESys
 */
class ESys_Image {


    private $imageResource;

    private $imageInfo;


    public function __construct ()
    {
    }


    /**
     * @param string $filePath The path to the file to load into memory.
     * @return boolean
     */
    public function load ($filePath)
    {
        $imageInfo = getimagesize($filePath);
        if (! $imageInfo) {
            echo __FUNCTION__.' | '.__LINE__;   /// DEBUG
            trigger_error(__CLASS__.'::'.__FUNCTION__.'() unable to retrieve image info.',
                E_USER_WARNING);
            return false;
        }
        $imageResource = null;
        switch ($imageInfo['mime']) {
            case 'image/jpeg':
                $imageResource = imagecreatefromjpeg($filePath);
                break;
            case 'image/gif':
                $imageResource = imagecreatefromgif($filePath);
                break;
            case 'image/png':
                $imageResource = imagecreatefrompng($filePath);
                break;
            default:
                echo __FUNCTION__.' | '.__LINE__;   /// DEBUG
                trigger_error(__CLASS__.'::'.__FUNCTION__.'() unsuported file type',
                    E_USER_WARNING);
                return false;
                break;
        }
        if (! $imageResource) {
            echo __FUNCTION__.' | '.__LINE__;   /// DEBUG
            trigger_error(__CLASS__.'::'.__FUNCTION__.'() unable to parse image.',
                E_USER_WARNING);
            return false;
        }
        $this->imageResource = $imageResource;
        $this->imageInfo = $imageInfo;
        return true;
    }


    /**
     * @return void
     */
    public function release ()
    {
        imagedestroy($this->imageResource);
    }
    
    
    /**
     * @return int
     */
    public function width ()
    {
        return $this->imageInfo[0];
    }


    /**
     * @return int
     */
    public function height ()
    {
        return $this->imageInfo[1];
    }


    /**
     * @return double
     */
    public function aspectRatio ()
    {
        return $this->width() / $this->height();
    }


    /**
     * @return string
     */
    public function mimeType ()
    {
        return $this->imageInfo['mime'];
    }



    /**
     * @param string $filePath
     * @param int $quality
     * @return boolean
     */
    public function saveJpeg ($filePath, $quality = 80)
    {
        return imagejpeg($this->imageResource, $filePath, $quality);
    }


    /**
     * @param string $filePath
     * @param int $quality
     * @return boolean
     */
    public function savePng ($filePath, $quality = null)
    {
        exit(__CLASS__.'::'.__FUNCTION__.'() Not implemented.');
    }


    /**
     * @param string $filePath
     * @param int $quality
     * @return boolean
     */
    public function saveGif ($filePath, $quality = null)
    {
        exit(__CLASS__.'::'.__FUNCTION__.'() Not implemented.');
    }


    /**
     * @param int $maxHeight
     * @param int $maxWidth
     * @return boolean
     */
    public function scaleToMaxHeightAndWidth ($maxHeight, $maxWidth)
    {
        $maxAspectRatio = $maxWidth / $maxHeight;
        $maxIsWider = $maxAspectRatio > $this->aspectRatio();
        if ($maxIsWider) {
            return $this->scaleToHeight($maxHeight);
        } else {
            return $this->scaleToWidth($maxWidth);
        }        
    }


    /**
     * @param int $minHeight
     * @param int $minWidth
     * @return boolean
     */
    public function scaleToMinHeightAndWidth ($minHeight, $minWidth)
    {
        $minAspectRatio = $minWidth / $minHeight;
        $minIsWider = $minAspectRatio > $this->aspectRatio();
        if ($minIsWider) {
            return $this->scaleToWidth($minWidth);
        } else {
            return $this->scaleToHeight($minHeight);
        }        
    }


    /**
     * @param int $newHeight
     * @return boolean
     */
    public function scaleToHeight ($newHeight)
    {
        $newWidth = $newHeight * $this->aspectRatio();
        if ($newHeight == $this->height() && $newWidth == $this->width()) {
            return true;
        }
        return $this->resize($newHeight, $newWidth);
    }


    /**
     * @param int $newWidth
     * @return boolean
     */
    public function scaleToWidth ($newWidth)
    {
        $newHeight = $newWidth / $this->aspectRatio();
        if ($newHeight == $this->height() && $newWidth == $this->width()) {
            return true;
        }
        return $this->resize($newHeight, $newWidth);
    }
    

    /**
     * @param int $newHeight
     * @param int $newWidth
     * @return boolean
     */
    public function resize ($newHeight, $newWidth)
    {
        $newImageResource = imagecreatetruecolor($newWidth, $newHeight);
        $resizeResult = imagecopyresampled(
            $newImageResource, $this->imageResource, 0, 0, 0, 0, 
            $newWidth, $newHeight, $this->width(), $this->height()
        );
        if (! $resizeResult) {
            return false;
        }
        imagedestroy($this->imageResource);
        $this->imageResource = $newImageResource;
        $this->imageInfo[0] = $newWidth;
        $this->imageInfo[1] = $newHeight;
        return true;
    }


}