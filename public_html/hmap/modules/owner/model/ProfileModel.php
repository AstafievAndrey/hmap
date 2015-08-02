<?php
namespace model;

use Db\DbQuery as DbQuery;
use Sessions\Ses as Ses;
use model\AuthModel as Auth;
use Url\Request as Req;
//use Log\Logg as Log;
//use Config\Conf as Config;
//use PDO;
    
class ProfileModel {
    
    private $_db;
    private $_ses;
    private $_auth;
    private $_req;


    public function __construct() {
	$this->_db = new DbQuery('hmap');
	$this->_ses = new Ses();
	$this->_auth = new Auth();
	$this->_req = new Req();
	if($this->_auth->checkAuth()===false){
	    $this->_req->UrlRedirect("/hookah/authorization/index");
	}
    }
    
    //занесем изменения в бд
    public function safeProfile($id,$name,$pass,$phone) {
	if($pass==""){
	    return $this->_db
		->update("owners", "name=:name,phone=:ph")
		->where("id=:id")
		->setValue(array(
			"name"=>  htmlspecialchars($name),"ph"=>(int)$phone,"id"=>(int)$id
		    )
		)
		->exec();
	}else{
	    return $this->_db
		    ->update("owners", "name=:name,password=:pass,phone=:ph")
		    ->where("id=:id")
		    ->setValue(array(
			    "name"=>  htmlspecialchars($name),"pass"=>md5(md5($pass)),
			    "ph"=>(int)$phone,"id"=>(int)$id
			)
		    )
		    ->exec();
	}
    }
        

}
