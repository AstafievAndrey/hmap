<?php 
namespace controllers;

use model\IndexModel as ind;
use Url\Request as Req;

class CataloghookahController {
	
	protected $post;
	protected $auth;
	private $_req;
	private $_indModel;


	public function __construct() {
	    $this->_req = new Req;
	    $this->_indModel = new ind;
	}
	
	public function indexAction() {
	       
	}
	    
}
?>