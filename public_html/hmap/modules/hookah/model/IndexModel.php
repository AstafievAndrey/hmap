<?php
namespace model;

use Db\DbQuery as DbQuery;
use SxGeo\SxGeo as Geo;
//use Log\Logg as Log;
//use Config\Conf as Config;
//use PDO;
    
class IndexModel {
    
    private $_geo;
    private $_db;
    
    public function __construct() {
	$this->_geo = new Geo();
	$this->_db = new DbQuery('hmap');
    }
    
    //делаем простой select
    public function simpleSelect($table) {
	return $this->_db
		    ->select()
		    ->from($table)
		    ->exec(TRUE);
    }
    
    //
    public function searchName($str,$city_id) {
	return $this->_db->select()
		    ->from("organizations")
		    ->where("city_id = :id and active=1 and name like '%$str%' ")
		    ->setValue(array("id"=>(int)$city_id))
		    ->exec(TRUE);
    }
    
    
    //вытаскиваем заведения из нужного города
    public function getOrg($city_id=0,$categ=0,$price=0,$alcohol=0) {
	$where="city_id = :id and active=1";
	if($categ==0 && $price==0 && $alcohol==0){
	    return $this->_db->select()
		    ->from("organizations")
		    ->where("city_id = :id and active=1")
		    ->setValue(array("id"=>(int)$city_id))
		    ->exec(TRUE);
	}else{
	    if($categ!=0){$where.=" and category_id = ".(int)$categ;}
	    if($alcohol!=0){$where.=" and alcohol = ".(int)$alcohol;}
	    if($price!=0){
		if((int)$price===1){
		    return $this->_db->select()->from("organizations")->where($where)->setValue(array("id"=>(int)$city_id))->order("h_price DESC")->exec();
		}else{
		    return $this->_db->select()->from("organizations")->where($where)->setValue(array("id"=>(int)$city_id))->order("h_price ASC")->exec();
		}
	    }else {return $this->_db->select()->from("organizations")->where($where)->setValue(array("id"=>(int)$city_id))->exec();}
	}
	
    }


    //проверим есть ли такой город в бд и если нет запишем его
    private function checkCity($city) {
	if(count($this->_db
			->select()
			->from("cities")
			->where("city_id = :city")
			->setValue(array("city"=>$city["city"]["id"]))
			->exec(TRUE)
		    )===0){
	    if((bool)$city){
		$this->_db->insert("cities", array("city_id","name_ru","name_en","lat","lon"), array(":city_id",":name_ru",":name_en",":lat",":lon"))
			->setValue(array(
				    "city_id"=>  (int)$city['city']["id"],
				    "name_ru"=>  htmlspecialchars($city['city']["name_ru"]),
				    "name_en"=>  htmlspecialchars($city['city']["name_en"]),
				    "lat"=>  (float)($city['city']["lat"]),
				    "lon"=>  (float)($city['city']["lon"]),
				)
			    )
			->exec();
	    }
	}
    }
    
    
    
    //определяем город по ip
    public function Geo() {
	$city = $this->_geo->getCityFull($_SERVER["REMOTE_ADDR"]);
	$this->checkCity($city);
	$organiz = $this->_db
			->select()
			->from("organizations")
			->where("city_id = :city and active=1")
			->setValue(array("city"=>(int)$city["city"]["id"]))
			->exec(TRUE);
	if(count($organiz)==0){
	    return array(
		    "count"=>0,
		    "city"=>$city,
		    "zav"=>$this->_db
				->select()
				->from("organizations")
				->where("active=1")
				->order("city_id ASC")
				->exec(TRUE)
		    );
	}else{
	    return array(
		    "count"=>count($organiz),
		    "city"=>$city,
		    "zav"=>$organiz
		    );
	}
	//die();
    }
    
    //вытащим кальянные из нужного города
    public function getCityHmap(){
	//header("Content-Type: text/html; charset=utf-8");
	//echo "<pre>";
	var_dump(
		    $this->_db
		    ->select()
		    ->from("organizations")
		    ->where("city_id = :city and category_id= :id")
		    ->setValue(array("city"=>551487,"id"=>(int)"1"))
		    ->exec(TRUE)
		);
	
	//die();
    }
    

}
