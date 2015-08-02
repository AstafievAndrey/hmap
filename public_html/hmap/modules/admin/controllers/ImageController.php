<?php 
namespace controllers;

use model\AuthModel as Auth;
use Url\Request as Req;
use model\admin\ImageModel as ImageModel;

class ImageController {
	
	protected $post;
	protected $auth;
        private $_req;
        private $_model;


        public function __construct() {
            $this->auth = new Auth();
            $this->_req = new Req();
            if(!$this->auth->checkAuth()){
                $this->_req->UrlRedirect("/admin/registration/login");
            }     
            $this->_model = new ImageModel();
	}

	public function getAllAction() {
	    echo json_encode(array(
                "state"=>"ok",
                "mass"=>  $this->_model->getAll(),
            ));
	}
        
        public function getImageAction() {
            echo json_encode(array(
                "state"=>"ok",
                "mass"=>  $this->_model->getImage((int)$this->_req->reqPost("id")),
            ));
	}
        
        public function updateAction(){
            echo json_encode(array(
                "state"=>  $this->_model->updateImage(
                            (int)  $this->_req->reqPost("id"),
                            htmlspecialchars((string)  $this->_req->reqPost("title")),
                            htmlspecialchars((string) $this->_req->reqPost("alt"))
                        )
            ));
        }

        public function addAction() {
            echo json_encode(
                        $this->_model->insertImage(
                                htmlspecialchars((string)$this->_req->reqPost("title_image")),
                                htmlspecialchars((string)$this->_req->reqPost("alt_image"))
                            )
                    );
	}
        
        public function deleteAction() {
            echo json_encode(
                        array(
                            "state"=>$this->_model->deleteImage((int)$this->_req->reqPost("image_id")),                        
                        )
                    );
	}
	    
}
?>