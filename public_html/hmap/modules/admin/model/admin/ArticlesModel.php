<?php
namespace model\admin;

use Db\DbQuery as Db;
use model\AuthModel as Auth;
use File\File as File;

	
	
class ArticlesModel {
		        
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
                for($i=0;$i<count($images_art);$i++){
                    $this->_db->insert("images_articles",
                                array("article_id","image_id"),
                                array((int)$art_id, (int)$images_art[$i]->image_id))
                                ->exec();
                }
            }
            return 1;
        }
        
        public function getAll(){
            return $this->_db->select()->from("images")->exec();
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
        
        //удаляем статью
        public function deleteArticle($id){
            return $this->_db->delete()->from("articles")->where("article_id=".(int)$id)->exec()==1 ? "ok" : "nook";
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
        
        //добавляем статью
        public  function insertArticle($name_article,$text_article, $image_art_id){            
            $this->_db->insert("articles",
                    array("name_article","text_article","image_art_id","date_article"),
                    array((string)$name_article,$text_article,(int)$image_art_id,  time()))
                    ->exec();
            return $this->_db->select("LAST_INSERT_ID() as id")->exec(true);
        }
        
        //получим все статьи определенной категории
        public function getArticlesCategory($category_id){
            return  $this->_db
                        ->select()
                        ->from("articles_categories_list")
                        ->where("category_id=".(int)$category_id)
                        ->exec();
        }
        
}
?>