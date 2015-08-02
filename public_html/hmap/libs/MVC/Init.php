<?php
namespace MVC;

use Config\Conf as Config;


class Init {

	public static function getModule() {
			$cfg = new Config;
			$module_pars = explode('/',$_SERVER['REQUEST_URI']);
			$module = $cfg->getVar('application.defaultModule');
			if ((count($module_pars)==4) && ($module_pars[1]!="") && (is_dir(APP_DIR."/modules/".$module_pars[1]) )) $module = $module_pars[1]; 
			return $module;
	}
		
	public function InitConfig() {
			$cfg = new Config;
			if ($cfg->getVar('application.display_errors')==1) {
				ini_set('display_errors','On');
			} else {
				ini_set('display_errors','Off');
			}

	}
	
}