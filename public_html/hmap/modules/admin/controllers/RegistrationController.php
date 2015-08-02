<?php 
namespace controllers;

use model\AuthModel as Auth;
use Url\Request as Req;

class RegistrationController {
	
        private $auth;
        private $req;
    
        public function __construct() {
            $this->auth = new Auth();
            $this->req = new Req();
	}

	public function loginAction() {
	    return;
	}
        
        public function checkloginAction(){
            $login = $this->req->reqPost("login");
            $pass =  md5(md5($this->req->reqPost("password")));
            if($this->auth->setAuth($login, $pass)){
                $this->req->UrlRedirect("/admin/index/index");
            }else{
                $this->req->UrlRedirect("/admin/registration/login");
            }
        }

	    
}
?>