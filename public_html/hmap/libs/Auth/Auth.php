<?php
 
namespace Auth;
use Config\Conf as Config;
use Thr\Exept as Exeption;
use Sessions\Ses as Session;
use Sessions\Cookie as Cookie;
use Db\DbQuery as Db;

class Auth{
    public $session;
    public $cookie;
    public $auth;
    
    public function __construct() {
        
        $this->session = new Session();
    }
    
    public function getAuth(){
        if(($this->session->getSesValue('auth')!=1) or (!$this->session->getSesValue('auth'))) {        
            return 0;
        }elseif ($this->session->getSesValue('auth')==1){
            return 1;
        }
    }
    
    public function setAuth($login,$pass){
        $config = new Config();
        $this->cookie= new Cookie();
        $table=$config->getVar('auth.table');
        $table_log=$config->getVar('auth.login');
        $table_pass=$config->getVar('auth.pass');
        $db = new Db();
        $sql=$db->select()
                ->from($table)
                ->where("$table_log='$login' and $table_pass='$pass'");
        $sql = $sql->exec();
        
        if (is_array($sql) && (count($sql)==1)){
            $this->cookie->setCookie('login', $login);
            $this->session->setSesValue('auth', 1);
        } else{
            $this->session->setSesValue('auth', 0);
        }
    }
    
}
