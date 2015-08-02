<?php
namespace File;
    
class File {
    
    private $_whitelist_image = array("jpg","png","jpeg");


    public function __construct() {
        
    }
    
    //генератор случайного значения
    protected function generateRandomString($length = 15) {
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$charactersLength = strlen($characters);
	$randomString = '';
	for ($i = 0; $i < $length; $i++) {
	    $randomString .= $characters[mt_rand(0, $charactersLength - 1)];
	}
	return $randomString;
    }
    
    public function getFile($name){
        return $_FILES[$name];
    }
    
    //сохраним одно изображение
    private function saveImage($file){
	$mass = explode(".",$file["name"]);
        $expension  = $mass[count($mass)-1];
        if($file['size']/1024<3000){
            if($file['type'] == "image/png" || $file['type'] == "image/jpg" || $file['type'] == "image/jpeg"){
                foreach ($this->_whitelist_image as $exp){
                    if($exp==$expension){
                        $res = 1;
                        break;
                    }
                }
            } else {return 0;}
        }else{return 0;}
        if($res===1){
            $file["name"]=time()."_".$this->generateRandomString().".".$expension;
            if(move_uploaded_file($file['tmp_name'],
                    str_replace("/libs/File", "", __DIR__)."/resourses/uploads/images/".$file["name"])){
                return $file["name"];
            }else {return 0;}
        }else{return 0;}     
    }
    
    //сохраним пачку изображений
    private function saveImages($files,$count){
	$res_mass=array();
	$res=0;
	for($i=0;$i<$count;$i++){
	    $mass = explode(".",$files["name"][$i]);
	    $expension  = $mass[count($mass)-1];
	    if($files['size'][$i]/1024<3000){
		if($files['type'][$i] == "image/png" || $files['type'][$i] == "image/jpg" || $files['type'][$i] == "image/jpeg"){
		    foreach ($this->_whitelist_image as $exp){
			if($exp==$expension){
			    $res = 1;
			    break;
			}
		    }
		} else {
		    array_push($res_mass,array($files["name"][$i]=>"Filed protection Type"));
		}
	    }else{
		array_push($res_mass,array($files["name"][$i]=>"Filed protection Size"));
	    }
	    if($res===1){
		$files["name"][$i]=time()."_".$this->generateRandomString().".".$expension;
		if(move_uploaded_file($files['tmp_name'][$i],
			str_replace("/libs/File", "", __DIR__)."/resourses/uploads/images/".$files["name"][$i])){
		    array_push($res_mass, array($files["name"][$i]=>TRUE));
		}else {
		    array_push($res_mass,array($files["name"][$i]=>"Filed Move_upload"));
		}
	    }
	}
	return $res_mass;
    }


    //проверим и сохраним изображение на сервер
    public function save($file,$count=0){ 
	//var_dump($file);
	if(is_array($file["name"])){
	    if(count($file["name"])>$count){
		return $this->saveImages($file, $count);
	    }else{
		return $this->saveImages($file, count($file["name"]));
	    }
	}else{
	    return $this->saveImage($file);
	}
        
    }
    
    //удалим файл с сервера
    public function deleteFile($type,$name){
        switch ((string)$type){
            case "image": 
                return unlink(str_replace("/libs/File", "", __DIR__)."/resourses/uploads/images/".$name);    
            case "doc": return 1;
                
            default : return 0;
        }
    }
}
