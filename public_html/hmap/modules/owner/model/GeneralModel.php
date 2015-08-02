<?php
namespace model;

use Db\DbQuery as DbQuery;
use Sessions\Ses as Ses;
use model\AuthModel as Auth;
use Url\Request as Req;
//use Log\Logg as Log;
//use Config\Conf as Config;
//use PDO;
    
class GeneralModel {
    
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
    
    public function getCities(){
	return $this->_db->select()->from("cities")->exec(TRUE);
    }

}
