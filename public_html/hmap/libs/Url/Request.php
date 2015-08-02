<?php
namespace Url;

	/*
	Request - класс для работы с параметрами url
	*/

	class Request {
	
		/*
		reqGet - получение get запроса
		@ string req - имя параметра
		return string - значение параметра
		*/
	
		public function reqGet($req) {
			if (isset($_GET[$req])) {
				return $_GET[$req];
			}
			return 0;
		}
		
		/*
		reqPost - получение post запроса
		@ string req - имя параметра
		return string - значение параметра
		*/
		
		public function reqPost($req) {
			if (isset($_POST[$req])) {
				return $_POST[$req];
			}
			return 0;
		}
		
		/*
		UrlRedirect - перенаправление на указанную страницу
		@ string url - url траницы
		*/
		
		public function UrlRedirect($url) {
			header('Location: '.$url);
		}

}