<?php
namespace model;

use Sessions\Ses as Session;
use Db\DbQuery as Db;
use model\AuthModel as Auth;
use File\File as File;

	
	
class AjaxModel {
		        
        private  $_db;
        private  $_auth;
        private  $_file;


        public function __construct() {
            $this->_db = new Db();
            $this->_auth =  new Auth();
            $this->_file = new File();
            if(!$this->_auth->checkAuth()){
                die("Not Authorization");
            }
        }
		
        public  function select($table=false,$wh=false){
            if($wh==false){
                return ($table!==FALSE)? $this->_db->select()->from((string)$table)->exec(TRUE) : FALSE;
            }else{
                return ($table!==FALSE)? $this->_db->select()
                        ->from((string)$table)
                        ->where($wh)
                        ->exec(TRUE) : FALSE;
            }
        }
        
        //редактируем отзыв
        public function updateReview($id,$from,$text){
            return $this->_db
                    ->update("reviews", "text_review='$text', from_review='$from'")
                    ->where("review_id=$id")
                    ->exec();
        }
        
        //одобрим отзыв
        public function approveReview($id) {
            return $this->_db
                    ->update("reviews", "show_review=1")
                    ->where("review_id=$id")
                    ->exec();
        }
        
        //удалим отзыв
        public function deleteReview($id){
            return $this->_db->delete()->from("reviews")->where("review_id=$id")->exec();
        }

        //выборка всех заявок
        public function getRequests(){
            return $this->_db->select()->from("requests")->order("answer asc")->exec(TRUE);
        }
        
        //редактируем заявку
        public function updateRequest($request_id){
            return $this->_db
                    ->update("requests", "answer=1")
                    ->where("request_id=$request_id")
                    ->exec();
        }
        
        //удаляем заявку
        public function deleteRequest($request_id){
            return $this->_db->delete()->from("requests")->where("request_id=$request_id")->exec();
        }


        //добавляем категорию в бд
        public function insertCategory($name_categ){
            $this->_db->insert("categories",
                                array("name_category"),
                                array(htmlspecialchars((string)$name_categ)))
                                ->exec();
            return $this->_db->select("LAST_INSERT_ID() as id")->exec(true);
        }
        
        //удаляем категорию из бд
        public function deleteCategory($categ_id){
            return $this->_db->delete()->from("categories")->where("category_id=$categ_id")->exec();
        }
        
        //редактируем категорию в бд
        public function updateCategory($categ_id,$name_categ){
            return $this->_db
                    ->update("categories", "name_category='$name_categ'")
                    ->where("category_id=$categ_id")
                    ->exec();
        }
        
        //проверим и добавим файл
        private function checkFile($name){
            return $this->_file->saveImage($this->_file->getFile($name));
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

        //добавляем статью
        public  function insertArticle($name_article,$text_article, $image_art_id){            
            $this->_db->insert("articles",
                    array("name_article","text_article","image_art_id","date_article"),
                    array((string)$name_article,$text_article,(int)$image_art_id,  time()))
                    ->exec();
            return $this->_db->select("LAST_INSERT_ID() as id")->exec(true);
        }
        
        //удаляем статью
        public function deleteArticle($id){
            return $this->_db->delete()->from("articles")->where("article_id=".(int)$id)->exec()==1 ? "ok" : "nook";
        }
        
        //добавляем категории к статье
        public  function insertCategArticle($art_id,$categ_id){            
            
            for($i=0;$i<count($categ_id);$i++){
                $this->_db->insert("articles_categories",
                    array("category_id","article_id"),
                    array((int)$categ_id[$i], (int)$art_id[0]["id"]))
                    ->exec();
            }
            
        }
        
        //добавляем изображения к статье 
        public  function insertImagesArticle($art_id,$images_article){            
            
            for($i=0;$i<count($images_article);$i++){
                $this->_db->insert("images_articles",
                    array("image_id","article_id"),
                    array((int)$images_article[$i]->image_id,(int)$art_id[0]["id"]))
                    ->exec();
            }
            return "ok";
            
        }
        
        //редактируем статью
        public function updateArticle($art_id,$name_art,$image_art_id,$text_art,$mass_categ,$images_art){
            if($this->_db
                    ->update("articles", "name_article='$name_art', image_art_id=$image_art_id, text_article='$text_art', date_article=".time())
                    ->where("article_id=$art_id")->exec()===1){
                $this->_db->delete()->from("images_articles")->where("article_id=$art_id")->exec();
                $this->_db->delete()->from("articles_categories")->where("article_id=$art_id")->exec();
                for($i=0;$i<count($mass_categ);$i++){
                    $this->_db->insert("articles_categories",
                            array("category_id","article_id"),
                            array((int)$mass_categ[$i], (int)$art_id))
                            ->exec();
                }
                for($i=0;$i<count($mass_categ);$i++){
                    $this->_db->insert("articles_categories",
                            array("category_id","article_id"),
                            array((int)$mass_categ[$i], (int)$art_id))
                            ->exec();
                }
                for($i=0;$i<count($images_art);$i++){
                    $this->_db->insert("images_articles",
                                array("article_id","image_id"),
                                array((int)$art_id, (int)$images_art[$i]->image_id))
                                ->exec();
                }
            }
            return 1;
        }
        
        //редактируем альбом
        public function updateAlbum($album_id,$name_album,$image_alb_id,$images_albums){
            if($this->_db->update("albums", "name_album='$name_album', image_alb_id=$image_alb_id, date_album=".time())->where("album_id=$album_id")->exec()===1){
                $this->_db->delete()->from("images_albums")->where("album_id=$album_id")->exec();
            }
            return 1;
        }
        
        //добавляем альбом
        public  function insertAlbum($name_album, $image_alb_id){            
            $this->_db->insert("albums",
                    array("name_album","image_alb_id","date_album"),
                    array((string)$name_album, (int)$image_alb_id,  time()))
                    ->exec();
            return $this->_db->select("LAST_INSERT_ID() as id")->exec(true);
        }
        
        //удаляем альбом из бд
        public function deleteAlbum($id){
            return $this->_db->delete()->from("albums")->where("album_id=$id")->exec()==1 ? "ok" : "nook";
        }
        
        //редактируем альбом
        public  function editAlbumImages($album_id, $images_id){
            for($i=0;$i<count($images_id);$i++){
                //echo (int)$images_id[$i]->image_id."/n \n";
                $this->_db->insert("images_albums",
                    array("album_id","image_id"),
                    array((int)$album_id, (int)$images_id[$i]->image_id))
                    ->exec();
            }
            return "ok";
        }
        
        //добавляем картинки в альбом
        public  function insertAlbumImages($album_id, $images_id){
            
            for($i=0;$i<count($images_id);$i++){
                $this->_db->insert("images_albums",
                    array("album_id","image_id"),
                    array((int)$album_id[0]["id"], (int)$images_id[$i]->image_id))
                    ->exec();
            }
            return "ok";
        }
}
?>