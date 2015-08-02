<?php 
namespace controllers;

use model\ProfileModel as Prof;
use model\AuthModel as Auth;
use Url\Request as Req;

class ProfileController {
	
	protected $post;
	private $_auth;
	private $_req;
	private $_profile;


	public function __construct() {
	    $this->_req = new Req;
	    $this->_profile = new Prof();
	    $this->_auth = new Auth();
	    if($this->_auth->checkAuth()===false){
		$this->_req->UrlRedirect("/hookah/authorization/index");
	    }
	}
	
	public function indexAction() {
	    return $this->_auth->checkAuth();
	}
	
	//сохраним изменения в профиле
	public function saveProfileAction(){
	    echo json_encode(
		    $this->_profile->safeProfile(
				$this->_req->reqPost("id"),
				$this->_req->reqPost("name"), 
				$this->_req->reqPost("password"),
				$this->_req->reqPost("phone")
			    )
		    );
	}
	    
}
?>