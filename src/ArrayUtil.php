<?php
/**
* 
*/
class ArrayUtil
{
	public function getValue($array, $key, $default = null)
	{
		if (is_array($array) && (isset($array[$key]) || array_key_exists($key, $array))) {
			return $array[$key];
		}
		if (is_object($array)) {
			return $array->$key;
		}
		return $default;
	}
	
}