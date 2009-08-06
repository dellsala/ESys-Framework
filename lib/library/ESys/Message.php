<?php


/**
 * @package ESys
 */
class ESys_Message {


    private $content;
    
    /**
     * @param string $content
     * @return void
     */
    public function ESys_Message ($content)
    {
        $this->content = $content;
    }


    /**
     * @return string
     */
    public function getContent ()
    {
        return $this->content;
    }


}


/**
 * @package ESys
 */
class ESys_Message_Error extends ESys_Message {


}


/**
 * @package ESys
 */
class ESys_Message_Warning extends ESys_Message {


}


/**
 * @package ESys
 */
class ESys_Message_Info extends ESys_Message {


}



