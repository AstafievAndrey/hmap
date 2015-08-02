<?php 
namespace controllers;

use model\AuthModel as Auth;
use Url\Request as Req;
use Sessions\Ses as Session;
//use Validate\Valid as Valid;
use Config\Conf as Config;

class MessagesController {
	
	protected $post;
	protected $auth;
        private $_req;


        public function __construct() {
            $this->auth = new Auth();
            $this->_req = new Req();
            if(!$this->auth->checkAuth()){
                $this->_req->UrlRedirect("/admin/registration/login");
            }
	}

	public function indexAction() {
	    return;
	}
	
	public function getMessagesAction() {
	    echo 1;
	}
        
	    
}
?>