<?php

require_once ESys_Application::get('config')->get('htdocsPath').'/fckeditor/fckeditor.php';

/**
 * @package ESys
 */
class ESys_FCKEditor {


    private $editor;


    /**
     * @param string $id
     */
    public function __construct ($id)
    {
        $this->editor = new FCKEditor($id);
        $this->editor->BasePath = 
            ESys_Application::get('config')->get('urlBase').'/fckeditor/';
    }


    /**
     * @param string $setName
     * @return void
     */
    public function setToolbar ($setName)
    {
        $this->editor->ToolbarSet = $setName;
    }


    /**
     * @param int $width
     * @return void
     */
    public function setWidth ($width)
    {
        $this->editor->Width = $width;
    }


    /**
     * @param int $height
     * @return void
     */
    public function setHeight ($height)
    {
        $this->editor->Height = $height;
    }


    /**
     * @param string $value
     * @return void
     */
    public function setValue ($value)
    {
        $this->editor->Value = $value;
    }


    /**
     * @param array $options
     * @return void
     */
    public function setConfig ($options)
    {
        $this->editor->Config = $options;
    }


    /**
     * @return string
     */
    public function createHtml ()
    {
        return $this->editor->CreateHtml();
    }


}

