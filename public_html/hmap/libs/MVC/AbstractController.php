<?php
namespace MVC;

use Validate\Valid as Valid;
use Thr\Exept as Exeption;
use Config\Conf as Config;

abstract class AbstractController{
    
    public $_parser;
	public $_controller_path;
	public $_controller_action;
	public $_controller_view;
	public $_config;
	
	public $module_name;
	public $controller_name;
	public $_get_parser;
	public $action_name;
	public $default_module_name;
    
	public function __construct() {
		$this->_get_parser = explode('?',$_SERVER['REQUEST_URI']);
		$this->_parser = explode('/',$this->_get_parser[0]);	
		
		$this->_config = new Config;
		$this->default_module_name = $this->_config->getVar('application.defaultModule');
		$this->_module_name = $this->_parser[1];
	}
	
    public function run() {

			switch (count($this->_parser)) {
				case 4 : $this->init();break;
				case 2 : $this->init_default();break;
				default : throw Exeption::ThrowDef("Error: 404 Page not found",false);break;
			} 				
    }
                
    
	public function init() {
		$valid = new Valid;
		$this->module_name = $this->_parser[1];
		$this->controller_name = $this->_parser[2];
		$this->action_name = $this->_parser[3];
		$active_module = 0;
		
		if (is_dir(APP_DIR."/modules/".$this->module_name)) $active_module = $this->_config->getVar('modules.'.$this->module_name.'.active');
		
		if (($valid->isAlnum($this->module_name)) && ($valid->isAlnum($this->controller_name)) && ($valid->isAlnum($this->action_name))) {
			if ($active_module!=1) {
				throw Exeption::ThrowDef("Error: Module ".$this->module_name." not found");
			}
			$this->_controller_path = "controllers\\".ucfirst($this->controller_name)."Controller";
			$this->_controller_action = $this->action_name."Action";
			$this->_controller_view = APP_DIR."/modules/".$this->module_name."/views/".$this->controller_name."/".$this->action_name.".phtml";
			if (is_callable(array($this->_controller_path, $this->_controller_action))) {
				$call_value = call_user_func(array(new $this->_controller_path, $this->_controller_action));
				$this->views($call_value);
			} else {
				throw Exeption::ThrowDef("Error: Action ".$this->_controller_action." not found");
					}
		} else {
			throw Exeption::ThrowDef("Error: String query is failed");
		}
	}
	
	
	public function init_default() {
		if ($this->_parser[1]=='') {
			$this->module_name =$this->default_module_name;
			$this->controller_name = 'index';
			$this->action_name = 'index';
			$this->_controller_path = "controllers\IndexController";
			$this->_controller_action = "IndexAction";
			$this->_controller_view = APP_DIR."/modules/".$this->default_module_name."/views/index/index.phtml";
			
			if (is_callable(array($this->_controller_path, $this->_controller_action))) {
				$call_value = call_user_func(array(new $this->_controller_path, $this->_controller_action));
				$this->views($call_value);
			} else {
				throw Exeption::ThrowDef("Error: Action ".$this->_controller_action." not found");
					}
		} else {
			throw Exeption::ThrowDef("Error: 404 Page not found",false);
		}
	}
	

	public function views($view) {			
		if (file_exists($this->_controller_view)) {
			require_once($this->_controller_view);
		} 
		/*
			else {
				throw Exeption::ThrowDef("Error : not found view".$this->_controller_view);
			}
		*/
    }
	
	public function viewError($err_view,$mas,$mes,$params) {
			require_once(APP_DIR."/modules/".$this->default_module_name."/views/errors/".$err_view.".phtml");
		
	}
       
	public function viewLayout($lay,$layout_params = null) {	
		if (file_exists(APP_DIR."/modules/".$this->module_name."/layouts/".$lay.".phtml")) {
			require_once(APP_DIR."/modules/".$this->module_name."/layouts/".$lay.".phtml");
		} else {
			throw Exeption::ThrowDef("Error : not found layout ".$lay);
		}
    }
	
}