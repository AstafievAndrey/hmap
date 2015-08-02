<?php
namespace model;
    
class FileModel {
    
    private $_whitelist_image = array("jpg","png","jpeg");


    public function __construct() {
        
    }
    
    public function getFile($name){
        return $_FILES[$name];
    }
    
    //вроверим и сохраним изображение на диск
    public function saveImage($file){
        
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
            } else {
                return 0;
            }
        }else{
            return 0;
        }
        
        if($res===1){
            $file["name"]=time().".".$expension;
            //echo str_replace("/modules", "", __DIR__);
            echo move_uploaded_file($file['tmp_name'],
                    "/home/f/fb790567/testinsite.ru/public_html/resourses/uploads/images/".$file["name"]);
            if(move_uploaded_file($file['tmp_name'],"../images/all/".$file["name"])){
                var_dump($file);
                return 1;
            }else {
                return 0;
            }
        }else{
           return 0; 
        }
        
    }
    
}
