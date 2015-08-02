<?php
namespace Validate;

/*
	Valid класс для проверки входных данных
*/

class Valid {
	
	/*
		isInt проверка на число
		@par занчение для проверки	
		return bool
	*/
	
	public function isInt($par) {
		if (is_numeric($par)) return true;
			return false;
	}
	
	
	/*
		isString проверка на строку
		@par занчение для проверки	
		return bool
	*/
	
	public function isString($par) {
		if (is_string($par)) return true;
			return false;
	}
	
	
	/*
		isAlnum проверка на число или строку
		@par занчение для проверки	
		return bool
	*/
	
	public function isAlnum($par) {
		if (ctype_alnum($par))  return true;
			return false;
	}
	
	
	/*
		isEmail проверка на email
		@par занчение для проверки	
		return bool
	*/
	
	public function isEmail($par) {
		if (filter_var($par, FILTER_VALIDATE_EMAIL))  return true;
			return false;
	}
	
	
	public function isAuthValid($par) {
		if (($this->isAlnum($par)) && (strlen($par)<=20) && ($par!="")) return true;
		return false;
	}
	
	
}