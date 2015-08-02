<?php
namespace model\admin;

use Db\DbQuery as Db;
use model\AuthModel as Auth;
use File\File as File;

	
	
class ImageModel {
		        
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
        
        //проверим и добавим файл
        private function checkFile($name){
            return $this->_file->saveImage($this->_file->getFile($name));
        }
        
        //получим список всех изображений
        
        public function getAll(){
            return $this->_db->select()->from("images")->order("image_id DESC")->exec();
        }
        
        //получим список всех изображений
        
        public function getImage($image_id){
            return $this->_db->select()->from("images")->where("image_id=".(int)$image_id)->exec();
        }
        
        //добавляем изображение в бд
        public function insertImage($title,$alt){
            $name_image=  $this->checkFile("addFile");
            if($name_image!==0){
                    return array(   "state"=>$this->_db->insert("images",
                                        array("name_image","title_image","alt_image","date_image"),
                                        array((string)$name_image,htmlspecialchars((string)$title),htmlspecialchars((string)$alt), time()))
                                        ->exec(),
                                    "id"=>$this->_db->select("LAST_INSERT_ID() as id")->exec(true),
                                    "title"=>$title,
                                    "alt"=>$alt,
                                    "name"=>$name_image
                        );
            }else {
                return 0;
            }
        }
        
        //удаляем изображение
        public function deleteImage($image_id){
            $del_img=$this->_db->select()->from("images")->where("image_id=$image_id")->exec(TRUE);
            $this->_file->deleteFile("image", htmlspecialchars((string)$del_img[0]["name_image"]));
            return $this->_db->delete()->from("images")->where("image_id=$image_id")->exec();
        }
        
        //редактируем изображение
        public function updateImage($image_id,$title,$alt){
            return $this->_db
                    ->update("images", "title_image='$title', alt_image='$alt', date_image=".time())
                    ->where("image_id=$image_id")->exec();
        }
        
}
?>