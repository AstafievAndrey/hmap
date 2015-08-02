<?php 
namespace controllers;

use model\IndexModel as ind;
use Url\Request as Req;

class IndexController {
	
	protected $post;
	protected $auth;
	private $_req;
	private $_indModel;


	public function __construct() {
	    $this->_req = new Req;
	    $this->_indModel = new ind;
	}
	
	public function indexAction() {
	    
	    return array(
		"city"=>$this->_indModel->Geo(),
		"cities"=>$this->_indModel->simpleSelect("cities"),
		"category_org"=>$this->_indModel->simpleSelect("category_organization")
	    );
	    
	}
	
	//поиск по названию
	public function searchByNameAction() {
	    echo json_encode($this->_indModel->searchName(
			htmlspecialchars((string)$this->_req->reqPost("str")),
			(int)$this->_req->reqPost("city_id")
		    ));
	}
	
	//первая загрузка страницы получение ajax данных
	public function indexAjaxAction(){
	    
	    echo json_encode($this->_indModel->Geo());
	    
	}
	
	//получить все заведения из выбранного города
	public function getCityOrgAction() {
	    echo json_encode(
		    $this->_indModel->getOrg(
			    (int)$this->_req->reqPost("id"),
			    (int)$this->_req->reqPost("categ"),
			    (int)$this->_req->reqPost("price"),
			    (int)$this->_req->reqPost("alcohol")
		    )
		);
	}
	    
}
?>