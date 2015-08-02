<?php
use MVC\Controller as Controller;
use Thr\Exept as Exeption;

define ("MODULE_DIR", dirname(__FILE__));

	spl_autoload_register(function ($className) {

		$classes = MODULE_DIR."/".str_replace("\\", "/", $className) .".php";
		$libs = APP_DIR."/libs/".str_replace("\\", "/", $className) .".php";

		if (file_exists($classes)) {
			require_once($classes);
				} else {
						if (file_exists($libs)) {
							require_once($libs);
						} else {
									throw Exeption::ThrowDef("NOT FOUND CLASS - ".$libs);
							}
					}
			});	

		$obj = new Controller;
		$obj->run();
?>