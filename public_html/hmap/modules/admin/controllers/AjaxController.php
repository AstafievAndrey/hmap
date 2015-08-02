<?php 
namespace controllers;

use model\AuthModel as Auth;
use model\AjaxModel as Ajax;
use Url\Request as Req;
use Sessions\Ses as Session;
use Config\Conf as Config;

class AjaxController {
	
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

	public function getAlbumsAction() {
            echo json_encode(array(
                                    "state"=>"ok",
                                    "mass"=>$this->_ajax->select("album_with_image")
                                )
                    );
	}
        
        //получим список всех заявок
        public function getRequestsAction(){
            echo json_encode(array(
                                    "state"=>"ok",
                                    "mass"=>$this->_ajax->getRequests()
                                )
                    );
        }
        
        //редактируем заявку
        public function updateAnswerRequestAction(){
            echo json_encode(array(
                                    "state"=>  $this->_ajax->updateRequest($this->_req->reqPost("id"))
                                )
                    );
        }
        
        //удаляем заявку
        public function deleteRequestAction(){
            echo json_encode(array(
                                    "state"=>  $this->_ajax->deleteRequest($this->_req->reqPost("request_id"))
                                )
                    );
        }

        //получим список всех категорий
        public function getCategoriesAction(){
            echo json_encode(array(
                                    "state"=>"ok",
                                    "mass"=>$this->_ajax->select("categories")
                                )
                    );
        }
        
        //Добавляем категорию в бд
        public function addCategoryAction(){                   
            echo json_encode(array(
                                    "state"=>"ok",
                                    "category_id"=>$this->_ajax->insertCategory(htmlspecialchars((string)  $this->_req->reqPost("name_categ")))
                                )
                    );
        }
        
        //Удаляем категорию из бд
        public function deleteCategoryAction(){                   
            echo json_encode(array(
                                    "state"=>$this
                                        ->_ajax
                                        ->deleteCategory((int)$this
                                                                ->_req
                                                                ->reqPost("id")
                                                )
                                )
                    );
        }
        
        //Редактируем категорию в бд
        public function updateCategoryAction(){                   
            echo json_encode(array(
                                    "state"=>$this
                                        ->_ajax
                                        ->updateCategory((int)$this->_req->reqPost("id"),
                                                    htmlspecialchars((string)$this->_req->reqPost("name_category"))
                                                )
                                )
                    );
        }
        
        //получим список всех статей
        public function getArticlesAction(){
            echo json_encode(array(
                                    "state"=>"ok",
                                    "mass"=>$this->_ajax->select("articles")
                                )
                    );
        }
        
        //добавляем статью в бд
        public function addArtcileAction() {
            $name_article = htmlspecialchars((string)$this->_req->reqPost("name_article"));
            $image_art_id = (int)$this->_req->reqPost("image_art_id");
            $images_article = json_decode($this->_req->reqPost("images"));
            $categories_id = explode(",",htmlspecialchars((string)$this->_req->reqPost("categ_id")));
            $text_article = $this->_req->reqPost("text_article");
            $art_id = $this->_ajax->insertArticle($name_article, $text_article, $image_art_id);
            $this->_ajax->insertCategArticle($art_id, $categories_id);
            $this->_ajax->insertImagesArticle($art_id, $images_article);
            
            echo json_encode(array(
                                    "state"=>"ok",
                                    "article_id"=>$art_id
                                )
                    );

	}
        
        //удаляем статью из бд
        public function deleteArtcileAction() {
            
            echo $this->_ajax->deleteArticle((int)  $this->_req->reqPost("id"));
            
	}
        
        //редактируем статью
        public function updateArticleAction() {
            $art_id=(int)$this->_req->reqPost("art_id");
            $name_art = htmlspecialchars((string)$this->_req->reqPost("name_art"));
            $image_art_id = (int)$this->_req->reqPost("image_art_id");
            $images_art = json_decode($this->_req->reqPost("images"));
            $mass_categories_id = explode(",",htmlspecialchars((string)$this->_req->reqPost("mass_categ")));
            $text_art = $this->_req->reqPost("text_art");
            echo $this->_ajax
                    ->updateArticle($art_id, $name_art, 
                            $image_art_id, $text_art, 
                            $mass_categories_id,$images_art);
	}
        
        //получаем данные для редактирования альбома
        public function getEditArticleAction() {
            $id = (int)$this->_req->reqPost("id");
            echo json_encode(array(
                                    "state"=>"ok",
                                    "article"=>$this->_ajax->select("articles","article_id=$id"),
                                    "images"=>$this->_ajax->select("images"),
                                    "categories"=>$this->_ajax->select("categories"),
                                    "article_categories_list"=>$this->_ajax->select("articles_categories_list","article_id=$id"),
                                    "article_images_list"=>$this->_ajax->select("images_articles_list","article_id=$id"),
                                )
                    );
	}
        
        
        //получим список отзывов
        public function getReviewsAction(){
            echo json_encode(array(
                                    "state"=>"ok",
                                    "reviews"=>  $this->_ajax->select("reviews","1 ORDER BY show_review ASC")
                                )
                    );
        }
        
        //получим конктретный отзыв
        public function getReviewAction(){
            echo json_encode(array(
                                    "state"=>"ok",
                                    "reviews"=>  $this->_ajax
                                                ->select("reviews","review_id=".
                                                        $this->_req->reqPost("id"))
                                )
                    );
        }
        
        //изменим отзыв в бд
        public function updateReviewAction(){
             echo json_encode(array(
                                    "state"=>  $this->_ajax->updateReview(
                                            (int)$this->_req->reqPost("id"),
                                            htmlspecialchars((string)$this->_req->reqPost("from")),
                                            htmlspecialchars((string)$this->_req->reqPost("text"))
                                        )
                                )
                    );
        }
        
        //удалим отзыв из бд
        public function deleteReviewAction(){
            echo json_encode(array(
                                    "state"=>  $this->_ajax->deleteReview(
                                                    (int)$this->_req->reqPost("id")
                                                )
                                        )
                                );
        }
        
        //одобрим отзыв
        public function approveReviewAction() {
            echo json_encode(array(
                                    "state"=>  $this->_ajax->approveReview(
                                                    (int)$this->_req->reqPost("id")
                                                )
                                        )
                                );
        }

        //получим список категорий и картинок для добавлении статьи
        public function getCategoriesImagesAction(){
            echo json_encode(array(
                                    "state"=>"ok",
                                    "mass_images"=>$this->_ajax->select("images"),
                                    "mass_categories"=>$this->_ajax->select("categories"),
                                )
                    ); 
        }
        
        //добавим изображение в бд и запишем на сервер
        public function addImageAction(){
            $title = htmlspecialchars((string)$this->_req->reqPost("title_image"));
            $alt = htmlspecialchars((string)$this->_req->reqPost("alt_image"));
            echo json_encode($this->_ajax->insertImage($title,$alt));
        }
        
        //добавим изображение в бд и запишем на сервер
        public function deleteImageAction(){
            $image_id = (int)$this->_req->reqPost("image_id");
            echo json_encode(
                        array("state"=>$this->_ajax->deleteImage($image_id))
                    );
        }
        
        //получим данные редактируемого изображение
        public function getEditImageAction(){
            $image_id=  (int)$this->_req->reqPost("id");
            echo json_encode(
                        array(
                            "state"=>"ok",
                            "mass"=>  $this->_ajax->select("images", "image_id=$image_id")
                        )
                    );
        }
        
        //редактируем изображение
        public function updateImageAction(){
            $image_id = (int)  $this->_req->reqPost("id");
            $title = htmlspecialchars((string)  $this->_req->reqPost("title"));
            $alt = htmlspecialchars((string) $this->_req->reqPost("alt"));
            echo json_encode(array("state"=>  $this->_ajax->updateImage($image_id, $title, $alt)));
        }

        //получим все изображения
        public function getImagesAction() {
            echo json_encode(array(
                                    "state"=>"ok",
                                    "mass"=>$this->_ajax->select("images")
                                )
                    );
	}
        
        //принимаем запрос на добавление нового альбома в ДБ
        public function addAlbumAction(){
            $mass = json_decode($this->_req->reqPost("images"));
            $name_album = htmlspecialchars((string)$this->_req->reqPost("name_album"));
            $image_alb_id = (int)$this->_req->reqPost("image_alb_id");
            $lastInsertId=$this->_ajax->insertAlbum($name_album, $image_alb_id);
            echo json_encode(array(
                                    "state"=>$this->_ajax->insertAlbumImages($lastInsertId, $mass),
                                    "album_id"=>$lastInsertId
                                )
                    );
        }
        
        //удаляем альбом из бд
        public function deleteAlbumAction(){
            $id = $this->_req->reqPost("id");
            echo json_encode(array(
                                    "state"=>$this->_ajax->deleteAlbum((int)$id),
                                )
                    );
        }
        
        //получаем данные для редактирования альбома
        public function getEditAlbumAction(){
            $id = (int)$this->_req->reqPost("id");
            echo json_encode(array(
                                    "state"=>"ok",
                                    "images"=>$this->_ajax->select("images"),
                                    "album"=>$this->_ajax->select("album_with_image","album_id=$id"),
                                    "images_albums_list"=>$this->_ajax->select("images_albums_list","album_id=$id"),
                                )
                    );
        }
        
        //редактируем альбом
        public function updateAlbumAction(){
            $album_id=(int)  $this->_req->reqPost("album_id");
            $name_album=htmlspecialchars((string)$this->_req->reqPost("name_album"));
            $image_alb_id=(int)  $this->_req->reqPost("image_alb_id");
            $images_albums= json_decode($this->_req->reqPost("images_albums"));
            if($this->_ajax->updateAlbum($album_id, $name_album, $image_alb_id, $images_albums)==1){
                echo $this->_ajax->editAlbumImages($album_id, $images_albums);
            }
        }
}
?>