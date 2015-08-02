<?php
namespace model;

use Db\DbQuery as DbQuery;
use Sessions\Ses as Ses;
use File\File as File;
use model\AuthModel as Auth;
use Url\Request as Req;
    
class ShopModel {
    
    private $_db;
    private $_ses;
    private $_auth;
    private $_req;
    private $_file;
    private $_owner_id;


    public function __construct() {
	$this->_db = new DbQuery('hmap');
	$this->_ses = new Ses();
	$this->_auth = new Auth();
	$this->_req = new Req();
	if($this->_auth->checkAuth()===false){
	    $this->_req->UrlRedirect("/hookah/authorization/index");
	}
	$this->_file = new File();
	$this->_owner_id= $this->_db->select("id")->from("owners")->where("login=:l and password=:p")->setValue(array("l"=>  $this->_ses->getSesValue("login"),"p"=>  $this->_ses->getSesValue("pass")))->exec(TRUE)[0]["id"];//id пользователя который добавляет
    }
    
    //вытаскиваем активные заведения
    public function activeTrueOrg() {
	return $this->_db->select("org_id,name,phone,site,adress,img_name,active")
		->from("organization_owner_view")
		->where("active=1 and id=".$this->_owner_id)
		->exec(TRUE);
    }
    
    //вытаскиваем заведения на модерации
    public function activeFalseOrg() {
	return $this->_db->select("org_id,name,phone,site,adress,img_name,active")
		->from("organization_owner_view")
		->where("active=0 and id=".$this->_owner_id)
		->exec(TRUE);
    }
    
    //сохраним изображение в бд
    protected function insertImg($name){
	
	if((bool)  $this->_db->insert("images", array("name"), array(":nm"))->setValue(array("nm"=>$name))->exec()){
	    return $this->_db->select("LAST_INSERT_ID() as id")->exec(true)[0]["id"];
	}else{
	    return FALSE;
	}
	
    }
    
    //записываем изображения в бд и на сервер
    private function setMainOrgImage($org_id){	
	$main_img=$this->_file->save($this->_file->getFile("image"));
	if($main_img!==0){
	    $img_id=$this->insertImg($main_img);
	    $this->_db->update("organizations", "organization_image=:img_id")
		    ->where("id=:id")->setValue(array("img_id"=>$img_id,"id"=>$org_id))->exec();
	}
	$images=$this->_file->save($this->_file->getFile("images"),10);
	for($i=0;$i<count($images);$i++){
	    foreach ($images[$i] as $key => $value) {
		if((bool)$value){
		    $img_id=$this->insertImg($key);
		    $this->_db->insert("organization_images",
			    array("organization_id","image_id"),
			    array(":org",":img"))
			    ->setValue(array("org"=>$org_id,"img"=>$img_id))->exec();
		}
	    }
	}
	return 1;
    }
    
    //сохраним график работы организации
    public function saveGraphWork($id,$org_id,$d_id,$time_begin,$time_end){
	switch((int)$id){
	    case 1: $res=$this->_db->insert("schedule",
		    array("organization_id","day_id","day_off"),array(":org",":day",":dayoff"))
		    ->setValue(array("org"=>(int)$org_id,"day"=>(int)$d_id,"dayoff"=>1))->exec();
		break;
	    case 2: $res=$this->_db->insert("schedule",
		    array("organization_id","day_id","around_o_clock"),array(":org",":day",":around"))
		    ->setValue(array("org"=>(int)$org_id,"day"=>(int)$d_id,"around"=>1))->exec();
		break;
	    case 3: $res=$this->_db->insert("schedule",
		    array("organization_id","day_id","time_begin","time_end"),
		    array(":org",":day",":begin",":end"))
		    ->setValue(array("org"=>(int)$org_id,"day"=>(int)$d_id,"begin"=>$time_begin,"end"=>$time_end))->exec();
		break;
	    default : $res==0; break;
	}
	return $res;
    }

    public function addHookah($name,$city,$adress,$lat,$lon,$categ,$alcohol,
	$food,$veranda,$parking,$about,$phone,$site) {
	$res = $this->_db->insert("organizations",
		    array('name','about','category_id','city_id','adress','lat','lon','alcohol','food','veranda','parking','phone','site'),
		    array(":name",":about",":categ",":city",':adress',':lat',':lon',':alcohol',':food',':veranda',':parking',':phone',':site')
		)
		->setValue(array(
			    "name"=>  htmlspecialchars($name),
			    "about"=>$about,"categ"=>(int)$categ,"city"=>(int)$city,
			    "adress"=>htmlspecialchars($adress),"lat"=>(float)$lat,"lon"=>(float)$lon,
			    "alcohol"=>(bool)$alcohol,"food"=>(bool)$food,"veranda"=>(bool)$veranda,"parking"=>(bool)$parking,
			    "phone"=>  htmlspecialchars($phone),"site"=>  htmlspecialchars($site)
			)
		    )->exec();
	if((bool)$res){
	    $last_id = $this->_db->select("LAST_INSERT_ID() as id")->exec(true)[0]["id"];//id добав организиции
	    $this->setMainOrgImage($last_id);
	    $this->_db->insert("organization_owner",array("organization_id","owner_id"),array(":o_id",":ow_id"))->setValue(array("o_id"=>(int)$last_id,"ow_id"=>$this->_owner_id))->exec();
	    return $last_id;
	}else{
	    return 0;
	}
	//return $this->_db->select("LAST_INSERT_ID() as id")->exec(true);
    }
    
}
