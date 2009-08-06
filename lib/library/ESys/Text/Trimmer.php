<?php

/**
 * Content aware text trimmer.
 *
 * Trims a string to a specified length, and continues to trim further
 * until it is trimmed at a natural breaking point (a space).
 *
 * Also adds trailing string to the result.
 *
 * @package ESys
 */
class ESys_Text_Trimmer {

    var $maxLength;
    var $trailer;

    /**
     * @param int $length
     * @param string $trailer
     */
    public function __construct ($length, $trailer = ' [+]')
    {
        $this->setMaxLength($length);
        $this->setTrailer($trailer);
    }
    
    /**
     * @param int $length
     * @return void
     */
    public function setMaxLength ($length) {
        $this->maxLength = $length;
    }

    /**
     * @param string $trailer
     * @return void
     */
    public function setTrailer ($trailer) {
        $this->trailer = $trailer;
    }

    /**
     * @param string $value
     * @return string
     */
    public function trim ($value) {
        if (strlen($value) > $this->maxLength) {
            $returnValue = trim(substr($value, 0, $this->maxLength));
            if (strpos($returnValue, ' ') !== false) {
                $matches = array();
                if (preg_match('/^(.*?)[:;.,]*[ \n]+\S*$/', $returnValue, $matches)) {
                    $returnValue = $matches[1];
                    //echo '<pre>'.print_r($matches, true).'</pre>';    /// DEBUG
                }
            } else {
                $returnValue = substr($returnValue, -3);
            }
            $returnValue .= $this->trailer;
        } else {
            $returnValue = $value;
        }
        return $returnValue;
    }

}

