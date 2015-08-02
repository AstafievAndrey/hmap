<?php 
namespace controllers;

use model\AuthModel as Auth;
use Url\Request as Req;
use model\admin\ArticlesModel as ArticlesModel;

class ArticlesController {
	
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
            $this->_model = new ArticlesModel();
	}
        
        //получить статьи определенной категории
	public function getArticlesCategotiesAction() {
	    echo json_encode(array(
                "state"=>"ok",
                "mass"=>  $this->_model
                        ->getArticlesCategory(
                                    (int)  $this->_req->reqPost("id")
                                ),
            ));
	}
        
        //удалим
        public function deleteAction() {
            echo $this->_model->deleteArticle((int)  $this->_req->reqPost("id"));
        }
        
        //редактируем статью
        public function updateArticleAction() {
            $art_id=(int)$this->_req->reqPost("art_id");
            $name_art = htmlspecialchars((string)$this->_req->reqPost("name_art"));
            $image_art_id = (int)$this->_req->reqPost("image_art_id");
            $images_art = json_decode($this->_req->reqPost("images"));
            $mass_categories_id = explode(",",htmlspecialchars((string)$this->_req->reqPost("mass_categ")));
            $text_art = str_replace("'", "\'", $this->_req->reqPost("text_art"));
	    //var_dump($text_art);
	    //die();
            echo $this->_model
                    ->updateArticle($art_id, $name_art, 
                            $image_art_id, $text_art, 
                            $mass_categories_id,$images_art);
	}
        
        //получаем данные для редактирования альбома
        public function getEditArticleAction() {
            $id = (int)$this->_req->reqPost("id");
            echo json_encode(array(
                                    "state"=>"ok",
                                    "article"=>$this->_model->select("articles","article_id=$id"),
                                    "images"=>$this->_model->select("images"),
                                    "categories"=>$this->_model->select("categories"),
                                    "article_categories_list"=>$this->_model->select("articles_categories_list","article_id=$id"),
                                    "article_images_list"=>$this->_model->select("images_articles_list","article_id=$id"),
                                )
                    );
	}
        
        //добавим статью в бд
	public function addAction() {
            
	    $name_article = htmlspecialchars((string)$this->_req->reqPost("name_article"));
            $image_art_id = (int)$this->_req->reqPost("image_art_id");
            $images_article = json_decode($this->_req->reqPost("images"));
            $categories_id = explode(",",htmlspecialchars((string)$this->_req->reqPost("categ_id")));
            $text_article = str_replace("'", "\'", $this->_req->reqPost("text_article"));
            $art_id = $this->_model->insertArticle($name_article, $text_article, $image_art_id);
            $this->_model->insertCategArticle($art_id, $categories_id);
            $this->_model->insertImagesArticle($art_id, $images_article);
            
            echo json_encode(array(
                                    "state"=>"ok",
                                    "article_id"=>$art_id
                                )
                    );
	}
	    
}
?>