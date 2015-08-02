<?php
    namespace Db;
    
    use Config\Conf as Config;
    use Thr\Exept as Exeption;
    use PDO;

    
	/*
     класс для работы с БД
     
     @ public param_ini хранятся параметры ini файла
     
	 */
    
    abstract class Db {
	 
        protected $_pdo;
    
        public function __construct($conn = "mysql"){
            try {
                $param_ini = new Config();
		switch ($conn) {
		    case "mysql":
			$this->_pdo = new PDO($param_ini->getVar('db.conn'),$param_ini->getVar('db.user'),$param_ini->getVar('db.pass'));
			break;
		    case "mssql":
			$this->_pdo = new PDO($param_ini->getVar('db.conn_mssql'),$param_ini->getVar('db.user_mssql'),$param_ini->getVar('db.pass_mssql'));
			break;
		    default:
		       $this->_pdo = new PDO($param_ini->getVar('db.conn_'.$conn),$param_ini->getVar('db.user_'.$conn),$param_ini->getVar('db.pass_'.$conn));
		}
                //if ($conn == "mysql") $this->_pdo = new PDO($param_ini->getVar('db.conn'),$param_ini->getVar('db.user'),$param_ini->getVar('db.pass'));
		//if ($conn == "mssql") $this->_pdo = new PDO($param_ini->getVar('db.conn_mssql'),$param_ini->getVar('db.user_mssql'),$param_ini->getVar('db.pass_mssql'));
		
            }
			
            catch(\PDOException $e){
				throw Exeption::ThrowDb($e);
			}
            
        }
        
        private function __clon(){}
        
    }