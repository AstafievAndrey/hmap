<?php
namespace model;
	use Sessions\Ses as Session;
	use Db\DbQuery as Db;
	use Config\Conf as Config;
	
	
class AuthModel {
		
	public $session;
    
    public function __construct() {
        $this->session = new Session();
    }
		
		
		public function setAuth($login,$pass) {
			$db = new Db();
			$conf = new Config;
			if (($conf->getVar('super.user')==$login) && ($conf->getVar('super.pass')==$pass)) {
				$this->session->setSesValue('login', $login);
				$this->session->setSesValue('pass', $pass);
				$this->session->setSesValue('organization', 'all');
				return true;
			} else {
					$sql=$db->select()
						->from('Authorization')
						->join('Authorization','Organizations','User_Id')
						->where("Authorization.Login='".$login."' AND Authorization.Pass='".$pass."'");
					$sql = $sql->exec();

					
					if (count($sql)==1) {
						$this->session->setSesValue('login', $login);
						$this->session->setSesValue('pass', $pass);
						$this->session->setSesValue('organization', $sql[0]['Organization_Id']);
						return true;
					}
			}
			return false;
			
		}
		
		public function checkAuth() {
			$db = new Db();
			$login = $this->session->getSesValue('login');
			$pass =  $this->session->getSesValue('pass');
			
			$sql=$db->select('*')
				->from('Authorization')
				->where("Login='".$login."' AND Pass='".$pass."'");
			$sql = $sql->exec();
			
			if (count($sql)==1) {
				return true;
			}
			return false;
		}
		
		public function exitAuth() {
			$this->session->delSesValue('login');
			$this->session->delSesValue('pass');
			$this->session->delSesValue('organization');
		}
		
		public function getOrganization() {
			return $this->session->getSesValue('organization');
		}
		
	}
?>