<?php

require_once 'ESys/File.php';


define('ESYS_FILE_UPLOAD_ERR_NOT_UPLOADED_FILE', 100);


/**
 * @package ESys
 */
class ESys_File_Upload {

    private $uploadData;
    
    /**
     * @param string $uploadFileName
     */
    public function __construct ($uploadFieldName)
    {
        if (! isset($_FILES[$uploadFieldName])) {
            $this->uploadData = array(
                'name' => null,
                'type' => null,
                'size' => null,
                'tmp_name' => null,
                'error' => UPLOAD_ERR_NO_FILE,
            );
        } else {
            $this->uploadData = $_FILES[$uploadFieldName];
            if ($this->uploadData['size'] == 0
                && $this->uploadData['error'] == UPLOAD_ERR_OK) 
            {
                $this->uploadData['error'] = UPLOAD_ERR_NO_FILE;
            }
        }
    }


    /**
     * @return boolean
     */
    public function submitted ()
    {
        return $this->statusCode() != UPLOAD_ERR_NO_FILE;
    }


    /**
     * @return boolean
     */
    public function transferred ()
    {
        if ($this->statusCode() == UPLOAD_ERR_OK
            && ! is_uploaded_file($this->uploadData['tmp_name']))
        {
            $this->uploadData['error'] = ESYS_FILE_UPLOAD_ERR_NOT_UPLOADED_FILE;
        }
        return $this->statusCode() == UPLOAD_ERR_OK;
    }


    /**
     * @return int
     */
    public function statusCode ()
    {
        return $this->uploadData['error'];
    }


    /**
     * @return string
     */
    public function statusMessage ()
    {
        switch ($this->statusCode()) 
        {
            case UPLOAD_ERR_OK:
                $message = 'The file uploaded with success.';
            break;
            case UPLOAD_ERR_INI_SIZE:
                $message = 'The uploaded file exceeds the upload_max_filesize '.
                    'directive in php.ini.';
            break;
            case UPLOAD_ERR_FORM_SIZE:
                $message = 'The uploaded file exceeds the MAX_FILE_SIZE directive '.
                    'that was specified in the HTML form.';
            break;
            case UPLOAD_ERR_PARTIAL:
                $message = 'The uploaded file was only partially uploaded.';
            break;
            case UPLOAD_ERR_NO_FILE:
                $message = 'No file was uploaded.';
            break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $message = 'Missing a temporary folder.';
            break;
            case UPLOAD_ERR_CANT_WRITE:
                $message = 'Failed to write file to disk.';
            break;
            case UPLOAD_ERR_EXTENSION:
                $message = 'File upload stopped by extension.';
            break;
            case ESYS_FILE_UPLOAD_ERR_NOT_UPLOADED_FILE:
                $message = 'Temporary file was not uploaded via HTTP. '.
                    'Possible file upload attack.';
            break;
            default:
                $message = 'Unknown error.';
            break;
        }
        return $message;
    }


    /**
     * @return string
     */
    public function getName ()
    {
        return $this->uploadData['name'];
    }


    /**
     * @return int
     */
    public function getSize ()
    {
        return $this->uploadData['size'];
    }


    /**
     * @return ESys_File
     */
    public function getFile ()
    {
        $file = new ESys_File($this->uploadData['tmp_name']);
        return $file;
    }



}
