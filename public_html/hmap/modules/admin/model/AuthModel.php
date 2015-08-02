<?php
namespace model;

use Sessions\Ses as Session;
use Db\DbQuery as Db;

	
	
class AuthModel {
		
	protected $session;
    
        public function __construct() {
            $this->session = new Session();
        }
		
		
        public function setAuth($login,$pass) {
                $db = new Db();
                
                $sql=$db->select()
                        ->from('users')
                        ->where("login='".$login."' AND password='".$pass."'")
                        ->exec();
                if (count($sql)==1) {
                        $this->session->setSesValue('login', $login);
                        $this->session->setSesValue('password', $pass);
                        //return true;
                }
                
                return true;

        }
		
        public function checkAuth() {
                $db = new Db();
                $login = $this->session->getSesValue('login');
                $pass =  $this->session->getSesValue('password');

                $sql=$db->select('*')
                        ->from('users')
                        ->where("login='".$login."' AND password='".$pass."'")
                        ->exec();
                if (count($sql)==1) {
                        //return true;
                }

                return true;
        }

        public function exitAuth() {
                $this->session->delSesValue('login');
                $this->session->delSesValue('password');
        }

}
?>