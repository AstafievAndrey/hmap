<?php
namespace model;

use Sessions\Ses as Session;
use Db\DbQuery as Db;
use model\AuthModel as Auth;
use File\File as File;

	
	
class AjaxMapModel {
		        
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
	
	//удалим коттедж из бд
	public function deleteCottage($id){
	    $this->_db
		    ->delete()
		    ->from("coordinates")
		    ->where("cottage_id=".(int)$id)
		    ->exec();
	    return $this->_db
		    ->delete()
		    ->from("cottage")
		    ->where("cottage_id=".(int)$id)
		    ->exec();
	}

	
	//редактируем коттежный поселок
	public function updateCottage($id,$name,$city,$waterboby,$forest,$owner,
		$price,$email,$site,$phone,$about,$points){
	    
	    $this->_db
		    ->delete()
		    ->from("coordinates")
		    ->where("cottage_id=".(int)$id)
		    ->exec();
	    $this->insertPolygonPoints($id, $points);
	    
	    return $this->_db->update("cottage", "name_cottage='$name',"
		    . "city_id=$city,"
		    . "waterbody=$waterboby,"
		    . "forest=$forest,"
		    . "owner_id=$owner,"
		    . "price=$price,"
		    . "email='$email',"
		    . "site='$site',"
		    . "phone='$phone',"
		    . "about='$about'")->where("cottage_id=$id")->exec();
	}
	
	//добавляем нового застройщика в бд
        public  function insertOwner($name, $phone, $adress,$site,$email){            
            return $this->_db->insert("owners",
                    array("name_owner","phone_owner","adress","site","email_owner"),
                    array($name, $phone, $adress,$site,$email))
                    ->exec();
        }
        
        //получим список всех застройщиков
        public function getOwner(){
            return $this->_db->select()->from("owners")->exec(TRUE);
        }
        
        //получим список всех городов
        public function getCities(){
            return $this->_db->select()->from("cities")->exec(TRUE);
        }
        
        //добавляем коттедж
        public function insertCottage($name,$city,$waterboby,$forest,$owner,
                $price,$email,$site,$phone,$about){
            
            if((int)$this->_db->insert("cottage",
                    array("name_cottage","city_id","waterbody","forest",
                        "owner_id","price","email","site","phone","about"
                        ),
                    array($name,$city,$waterboby,$forest,$owner,
                            $price,$email,$site,$phone,$about))
                    ->exec()
            ){
                return $this->_db->select("LAST_INSERT_ID() as id")->exec(true);
            }else{
                die("AddCottage Warning!");
            }   
        }
        
        //добавляем точки для полигона
        public function insertPolygonPoints($id,$points){
            for($i=0;$i<count($points);$i+=2){
                $this->_db->insert("coordinates",
                            array("cottage_id","lat","lon"),
                            array($id,$points[$i],$points[$i+1])
                        )
                    ->exec();
            }
            return 1;
        }
        
        
}