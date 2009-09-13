<?php

/**
 * @package ESys
 */
class ESys_Scaffolding_SourceTemplate extends ESys_Template {


    /**
     * @param string
     * @return string
     */
    public function fetch ($file = null)
    {
        return $this->parsePhpTags(parent::fetch($file));
    }


	/**
	 * @param string
	 * @return string
	 */
	protected function parsePhpTags ($string) 
	{
		$string = str_replace('<php>', '<?php', $string);
		$string = str_replace('</php>', '?'.'>', $string);
		return $string;
	}


}
