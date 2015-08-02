<?php
namespace Sessions;
	
	/*
	Cookie - класс для работы с куки
	*/
	
	use Thr\Exept as Exeption;	
	

	class Cookie {
	
		/*
		setCookie($name,$val,$time = 0) - устанавливает значение куки по имени
		@ string name - имя куки
		@ string val - значение куки
		@ int val - время жизни куки
		*/
		
		public function setCookie($name,$val,$time = 0) {
			if ($time==0) $time = time()+3600;
			try {
					SetCookie($name,$val,$time);
				} catch (Exception $e)  {
				throw Exeption::ThrowDef('Failed create or change cookie var - '.$e);
			}
		}
		
		/*
		getCookie($name) - получение значение куки по имени
		@ string name - имя куки
		return string возвращает значение куки
		*/
		
		public function getCookie($name) {
			if (isset($_COOKIE[$name])) {
				return $_COOKIE[$name];
			}
			return false;
		}
		
		
		/*
		delCookie($name) - удаляет значение куки по имени
		@ string name - имя сессии
		*/
		
		public function delCookie($name) {
			try {
					SetCookie($name,"");
				} catch (Exception $e)  {
				throw Exeption::ThrowDef('Failed delete cookie var - '.$e);
			}
		}


	}