<?php 
namespace controllers;

use model\AuthModel as Auth;
use Url\Request as Req;
use Sessions\Ses as Session;
//use Validate\Valid as Valid;
use Config\Conf as Config;
use PDO;

class IndexController {
	
	protected $post;
	protected $auth;
        private $req;


        public function __construct() {
            $this->auth = new Auth();
            $this->req = new Req();
            if(!$this->auth->checkAuth()){
                $this->req->UrlRedirect("/admin/registration/login");
            }
	}

	public function indexAction() {
	    return;
	}
        
        public function exitAction() {
            
            $this->auth->exitAuth(); 
            $this->req->UrlRedirect("/admin/index/index");
	    return;
	}
	    
}
?>