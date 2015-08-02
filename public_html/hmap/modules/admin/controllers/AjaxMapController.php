<?php
namespace controllers;
use model\AuthModel as Auth;
use model\AjaxMapModel as Ajax;
use Url\Request as Req;

class AjaxMapController {
    
    protected $post;
    private $_auth;
    private $_ajax;
    private $_req;


    public function __construct() {
        $this->_auth = new Auth();
        $this->_req = new Req();
        $this->_ajax = new Ajax;
        if(!$this->_auth->checkAuth()){
            die("Not Authorization");
        }
    }
    
    //удалим коттедж
    public function deleteCottageAction(){
	echo json_encode(
		    $this->_ajax->deleteCottage(
			    (int)$this->_req->reqPost("id")
		    )
		);
    }
    
    //отредактируем коттедж
    public function updateCottageAction(){
	echo $this->_ajax->updateCottage(
		htmlspecialchars((int)$this->_req->reqPost("cottage_id")), 
		htmlspecialchars((string)$this->_req->reqPost("name_cottage")), 
		(int)$this->_req->reqPost("city_id"), 
		(int)$this->_req->reqPost("waterbody"), 
		(int)$this->_req->reqPost("forest"), 
		0, 
		(float)$this->_req->reqPost("price"), 
		htmlspecialchars((string)$this->_req->reqPost("email")), 
		htmlspecialchars((string)$this->_req->reqPost("site")), 
		htmlspecialchars((string)$this->_req->reqPost("phone")), 
		htmlspecialchars((string)$this->_req->reqPost("about")),
		explode(",",htmlspecialchars((string)$this->_req->reqPost("coordinates")))
	    );
    }

    public function getCityAction(){
	echo json_encode(
		array(
		    "state"=>"ok",
		    "city"=>$this->_ajax->getCities()
		)
	    );
    }
    
    //добавим коттедж и его область
    public function addCottageAction(){
        echo $this->_ajax->insertPolygonPoints(
                //id коттеджа
                $this->_ajax->insertCottage(
                    htmlspecialchars((string)$this->_req->reqPost("name")),
                    (int)$this->_req->reqPost("city"),
                    (int)$this->_req->reqPost("waterbody"),
                    (int)$this->_req->reqPost("forest"),
                    0,
                    (int)$this->_req->reqPost("price"),
                    htmlspecialchars((string)$this->_req->reqPost("email")),
                    htmlspecialchars((string)$this->_req->reqPost("site")),
                    htmlspecialchars((string)$this->_req->reqPost("phone")),
                    htmlspecialchars((string)$this->_req->reqPost("about")))[0]["id"],
                //массив точек
                explode(",", htmlspecialchars((string)$this->_req->reqPost("points")))
            );
    }
    
}