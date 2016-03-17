<?php
/**
 * @package ESys
 */



class ESys_ValidatorRule_NotEmpty extends ESys_ValidatorRule {
	
	
	public function validate ($value)
	{
		if (is_string($value) && preg_match('/^\s+$/', $value)) {
			return false;
		}
		return strlen($value) > 0;
	}
	
	
}

