<?php
namespace model;

use Db\DbQuery as DbQuery;
use Sessions\Ses as Ses;
use Sessions\Cookie as Cookie;
//use Log\Logg as Log;
//use Config\Conf as Config;
//use PDO;
    
class AuthModel {
    
    private $_db;
    private $_ses;
    private $_cookie;


    public function __construct() {
	$this->_db = new DbQuery("hmap");
	$this->_ses = new Ses();
	$this->_cookie = new Cookie();
    }
    
    //генератор случайного значения
    protected function generateRandomString($length = 10) {
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ#@';
	$charactersLength = strlen($characters);
	$randomString = '';
	for ($i = 0; $i < $length; $i++) {
	    $randomString .= $characters[mt_rand(0, $charactersLength - 1)];
	}
	return $randomString;
    }
    
    //восстановление пароля
    public function reestablishPass($email){
	$res = $this->_db->select()->from("owners")
		->where("email = :e")
		->setValue(array("e"=>$email))
		->exec(TRUE);
	if(count($res)==1){
	    $new_pass=$this->generateRandomString();
	    $message = "<div style='font-size:16px;line-height:1.7;font-weight:bold;'><div>Здравствуйте! Спасибо за регистрацию на нашем ресурсе</div>"
			    . "<div>Ваш Логин: ".$res[0]["login"]."<br> Новый пароль: $new_pass</div>"
			    . "<div style='text-align:right'><a href='#'>C уважением команда HMap</a></div></div>";
	    $this->sendMail($email, $message);
	    if( (bool)$this->_db->update("owners", "password =:p")
		    ->where("email=:e")
		    ->setValue(array("p"=>md5(md5($new_pass)),"e"=>$email))
		    ->exec(true)){
			return array("state"=>1,"message"=>"Письмо отправлено на почту");
		    }
	}else{
	    return array("state"=>0,"message"=>"Такой email не найден в системе");
	}
    }


    public function setAuth($l,$p){
	$res = $this->_db->select()
		->from("owners")
		->where("login = :l and password = :p")
		->setValue(array("l"=>  htmlspecialchars($l),
		    "p"=>  md5(md5($p))))->exec(true);
	if(count($res)===1){
	    if((int)$res[0]["active"]==1){
		$this->_ses->setSesValue("login", $l);
		$this->_ses->setSesValue("pass", md5(md5($p)));
		return array("state"=>1,"message"=>"Все хорошо");
	    }else{
		return array("state"=>0,"message"=>"Ваша учетная запись не подтверждена через email");
	    }
	}else{
	    return array("state"=>0,"message"=>"Неверный логин или пароль");
	}
    }

    protected function sendMail($mail,$message) {
	$mailheaders = "Content-type:text/html;charset=utf-8";  
	    
	    if(mail(
			$mail, 
			"Сайт HMap.ru",
			$message,
			$mailheaders
		    )){
			return TRUE;
		    }else {
			return FALSE;
		    }
    }

    public function activeOwner($l,$p){
	if((bool)$this->_db->update("owners", "active = 1")
		->where("login=:l and password=:p")
		->setValue(array("l"=>$l,"p"=>$p))
		->exec()){
		    $this->deleteAuth();
		    $this->_ses->setSesValue("login", $l);
		    $this->_ses->setSesValue("pass", $p);
		}
    }

    public function checkLoginEmail($l,$e,$p){
	$count = $this->_db->select()->from("owners")->where("login = :login or email = :email")->setValue(array("login"=>  htmlspecialchars($l),"email"=>  htmlspecialchars($e)))->exec(true);
	if(count($count)===0){
	    $res = $this->_db->insert("owners", array("login","email","password"), array(":l",":e",":p"))
		    ->setValue(array("l"=>htmlspecialchars($l),"e"=>htmlspecialchars($e),"p"=>md5(md5($p))))
		    ->exec(true);
	    if((bool)$res){
		$message = "<div style='font-size:16px;line-height:1.7;font-weight:bold;'><div>Здравствуйте! Спасибо за регистрацию на нашем ресурсе</div>"
			    . "<div>Для завершения регистрации перейдите по ссылке ниже</div>"
			    . "<div>"
				. "<a href='http://".$_SERVER['SERVER_NAME']."/hookah/authorization/endAuth?log=".  htmlspecialchars($l)."&pass=".md5(md5($p))."'>"
				    . "Завершить регистрацию"
				. "</a>"
			    . "</div>"
			    . "<div style='text-align:right'><a href='#'>C уважением команда HMap</a></div></div>";
	    
		if($this->sendMail($e, $message)){
		    return array("state"=>1,"message"=>"Вам на почту отправлено письмо для завершения регистрации прочтите его.");
		}else{
		    $this->_db->insert("errors", array("text","time"), array(":txt",":t"))
		    ->setValue(array("txt"=>"Не удалось отправить письмо для завершения регистрации email".htmlspecialchars($e),
			"t"=>time()))
		    ->exec(true);
		}
	    }else{
		$this->sendMail("astafievandrejnikolaevich@gmail.com","Ошибка не удалось добавить в бд login = $l, email=$e, pass=$p");
		$this->_db->insert("errors", array("text","time"), array(":txt",":t"))
		    ->setValue(array("txt"=>"Ошибка не удалось добавить в бд login = $l, email=$e, pass=$p",
			"t"=>time()))
		    ->exec(true);
		return array("state"=>0,"message"=>"Извините регистрация временно недоступна");
	    }
	}else{
	    return array("state"=>0,"message"=>"Данный Логин или Email уже занят");
	}
    }

    public function checkAuth() {
	if($this->_ses->getSesValue("pass")){
	    $user = $this->_db->select()->from("owners")
		    ->where("login = :login and password = :pass and active=1")
		    ->setValue(
				array(
				    "login"=>  $this->_ses->getSesValue("login"),
				    "pass"=>$this->_ses->getSesValue("pass")
				)
			    )
		    ->exec(TRUE);
	    return (count($user)===1) ? $user[0]: false;
	}else{
	    return false;
	}
    }
    
    public function deleteAuth() {
	$this->_ses->delSesValue("login");
	$this->_ses->delSesValue("pass");
    }
    
    
}