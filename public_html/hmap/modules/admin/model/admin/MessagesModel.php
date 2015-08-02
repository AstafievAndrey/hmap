<?php
namespace model\admin;

use Db\DbQuery as Db;
use model\AuthModel as Auth;
use File\File as File;

	
	
class MessagesModel {
		        
        private  $_db;
        private  $_auth;
        private  $_file;


        public function __construct() {
            $this->_auth =  new Auth();
            if(!$this->_auth->checkAuth()){
                die("Not Authorization");
            }
            $this->_db = new Db();
            $this->_file = new File();
        }
        
	public function getAllMessages(){
	    
	}
        
}
?>