<?php 
namespace controllers;

use model\AuthModel as Auth;
use model\ShopModel as Shop;
use model\GeneralModel as Gen;
use Url\Request as Req;


class ShopController {
	
	protected $post;
	private $_auth;
	private $_req;
	private $_general;
	private $_shop;

	public function __construct() {
	    $this->_req = new Req;
	    $this->_auth = new Auth();
	    if($this->_auth->checkAuth()===false){
		$this->_req->UrlRedirect("/hookah/authorization/index");
	    }
	    $this->_general = new Gen();
	    $this->_shop = new Shop();
	}
	
	public function addOrgAction() {
	    $res=$this->_shop->addHookah(
			$this->_req->reqPost("name"),$this->_req->reqPost("city"), 
			$this->_req->reqPost("adress"), $this->_req->reqPost("lat"), 
			$this->_req->reqPost("lon"), $this->_req->reqPost("categ"), 
			$this->_req->reqPost("alcohol"), $this->_req->reqPost("food"), 
			$this->_req->reqPost("veranda"), $this->_req->reqPost("parking"), 
			$this->_req->reqPost("about"),$this->_req->reqPost("phone"),
			$this->_req->reqPost("site")
		    );
	    for($i=1;$i<8;$i++){
		$this->_shop->saveGraphWork(
			$this->_req->reqPost("optradio".$i), 
			$res, 
			$i, 
			$this->_req->reqPost("work_b_".$i), 
			$this->_req->reqPost("work_e_".$i)
		    );
	    }
	    echo json_encode(1);
	}
	
	public function addAction() {
	    //return $this->_auth->checkAuth();
	    return array(
			"user"=>$this->_auth->checkAuth(),
			"cities"=>$this->_general->getCities()
		    );
	}
	
	public function indexAction() {
	    
	    return array(
		    "owners"=>$this->_auth->checkAuth(),
		    "active_true"=>  $this->_shop->activeTrueOrg(),
		    "active_false"=>  $this->_shop->activeFalseOrg(),
		);
	    
	}
	
	public function editAction() {
	    
	    return array(
		    "user"=>$this->_auth->checkAuth(),
		    "cities"=>$this->_general->getCities()
		);
	    
	}
	
	    
}
?>