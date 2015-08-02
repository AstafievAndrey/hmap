<?php 
namespace controllers;

use model\AuthModel as auth;
use Url\Request as Req;
use RecaptchaGoogle\reCaptcha as capt;

class AuthorizationController {
	
	private $_req;
	private $_authModel;
	private $_captcha;


	public function __construct() {
	    $this->_req = new Req;
	    $this->_authModel = new auth;
	    $this->_captcha = new capt();
	}
	
	public function indexAction() {
	    
	    //var_dump($this->_authModel->checkAuth()); die();
	    
	    if($this->_authModel->checkAuth()===false){
		return;
	    }else{
		$this->_req->UrlRedirect("/owner/profile/index");
	    }
	    
	    return array(
		"auth"=>  $this->_authModel->checkAuth()
	    );
	    
	}
	
	//восстановление пароля
	public function reestablishAction(){
	    echo json_encode($this->_authModel->reestablishPass($this->_req->reqPost("email")));
	}


	public function inputAction() {
	    echo json_encode($this->_authModel->setAuth(
			$this->_req->reqPost("login"),  
			$this->_req->reqPost("password") 
		    ));
	}
	
	public function endAuthAction() {
	    
	    $this->_authModel->activeOwner($this->_req->reqGet("log"), $this->_req->reqGet("pass"));
	    $this->_req->UrlRedirect("/hookah/authorization/index");
	}
	
	public function addAction(){
	    $res = $this->_captcha->checkCaptcha("6LcBqQoTAAAAAEUxbxZddFAMtjZcmNQxUdgfUZxA",
		    $this->_req->reqPost("g-recaptcha-response")
		    );
	    if($res->success){
		echo json_encode(   $this->_authModel->checkLoginEmail(
				    $this->_req->reqPost("login"), 
				    $this->_req->reqPost("email"),
				    $this->_req->reqPost("password")
			)
		    );
	    }else{
		echo json_encode(array("state"=>"0","message"=>"Нажмите капчу"));
	    }
	}

	public function exitAuthAction() {
	    $this->_authModel->deleteAuth();
	    $this->_req->UrlRedirect("/hookah/authorization/index");
	}
	    
}
?>