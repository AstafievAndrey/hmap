<?php
use MVC\Init as Init;

	define ("APP_DIR", dirname(__FILE__));
	define ("APP_CONF", "default_application.ini");
		require_once(APP_DIR."/libs/MVC/Init.php");
		require_once(APP_DIR."/libs/Config/Ini.php");
		require_once(APP_DIR."/libs/Config/Conf.php");
	Init::InitConfig();
	require_once(APP_DIR."/modules/".Init::getModule()."/autoload.module.php");
?>