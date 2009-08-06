<?php

/**
 * Object oriented interface for accessing array elements.
 *
 * Provides a simplified interface for accessing associative arrays
 * with unknown elements and for setting defaults if the requested
 * element does not exist.
 *
 * @package ESys
 */
class ESys_ArrayAccessor {


	private $array;


    /**
     * @param array $array
     */
	public function __construct ($array) {
		$this->array = $array;
	}


    /**
     * @param string|array $key A key or array of keys.
     * @param mixed $defaultValue A default value or an array of default values.
     * @return mixed Key value or array of key values if $key is an array.
     */
	public function get ($key, $defaultValue = null) 
	{
		if (is_array($key)) {
			return $this->getList($key, $defaultValue);
		}
		return array_key_exists($key, $this->array) ? $this->array[$key] : $defaultValue;
	}


    /**
     * @param array $keyList
     * @param array|mixed $defaultValueList
     * @return array
     */
	private function getList ($keyList, $defaultValueList = null) 
	{
		$newArray = array();
		foreach ($keyList as $i => $key) {
			if (is_array($defaultValueList)) {
				$defaultValue = isset($defaultValueList[$i]) ? $defaultValueList[$i] : null;
			} else {
				$defaultValue = $defaultValueList;
			}
			$newArray[$key] = $this->get($key, $defaultValue);
		}
		return $newArray;
	}


}

