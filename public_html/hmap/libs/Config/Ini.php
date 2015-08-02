<?php
    namespace Config;
	
	use Thr\Exept as Exeption;
    
    class Ini {
        
        public $ArrayIni;
        
        public function  __construct(){
			if (file_exists(APP_DIR.'/config/default_application.ini')) {
				$this->ArrayIni =  parse_ini_file(APP_DIR.'/config/'.APP_CONF);
			} else {
				throw Exeption::ThrowDef('File configuration not found');
			}
        }
		
        
    }
