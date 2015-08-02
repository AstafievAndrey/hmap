<?php 
namespace controllers;

use model\AuthModel as Auth;
use Url\Request as Req;

class GeneralController {
	
	private $_auth;
	private $_req;


	public function __construct() {
	    $this->_req = new Req;
	    $this->_auth = new Auth();
	    if($this->_auth->checkAuth()===false){
		$this->_req->UrlRedirect("/hookah/authorization/index");
	    }
	}

	//выход из страницы редактрования пользователя
	public function exitAction() {
	    $this->_auth->deleteAuth();
	    $this->_req->UrlRedirect("/hookah/authorization/index");
	}
	    
}
?>