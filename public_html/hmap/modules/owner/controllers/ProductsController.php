<?php 
namespace controllers;

use model\AuthModel as Auth;
use Url\Request as Req;

class ProductsController {
	
	protected $post;
	private $_auth;
	private $_req;


	public function __construct() {
	    $this->_req = new Req;
	    $this->_auth = new Auth();
	    if($this->_auth->checkAuth()===false){
		$this->_req->UrlRedirect("/hookah/authorization/index");
	    }
	}
	
	public function addAction() {
	    return $this->_auth->checkAuth();
	}
	
	public function indexAction() {
	    
	    return $this->_auth->checkAuth();
	    
	}
	
	    
}
?>