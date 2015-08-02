<?php
namespace Thr;

use Log\Logg as Log;
use MVC\Controller as Controller;
use Config\Conf as Config;

	 /*
		Thr - класс исключений
		@ static int itr - номер элемента для формирования массива в printTrace;
		@ static int count - количество элементов массива для отображения или не отображения(если более 1 элемента то массив выводится);
		@ static int count_end - количество элементов массива для установления конца выполнеия функции;
		@ static string args - строка аргументов;
		@ static string obj - строка объектов;
		@ static string printTraceLog - строка трасировки для записи файла лога;
		@ static array arTrace - массив трассировки;
     */

	class Exept {

		public static $itr = 0;
		public static $count = 0;
		public static $count_end = 0;
		public static $args = null;
		public static $obj = null;
		public static $printTraceLog = null;
		public static $arTrace = array();
		
		 /*
		 ThrowDb функция исключения для БД
		 @ object e - сообщение выдаваемое стандартным обраблтчиком;
		 @ string mes_user - сообщение для пользователя;
		 @ bool display - отображение трасировки;
		 @ string mes_user - сообщение для пользователя;
		 @ array params - параметры сообщения;
		 */

			public static function ThrowDb($e,$display = true,$mes_user = 'Страница не найдена',$params = 404) {
				$cfg = new Config;
				$mes = 'Error DataBase Connection: '.$e->getMessage();
				
				if ($cfg->getVar('application.writelog')==1) {
					$log = new Log('error');
					$log->writeLog("\n");
					$log->writeLog("------------Exeption------------");
					$log->writeLog("time : ".date('h:i:s'));
					$log->writeLog('message : Error DataBase Connection: '.$e->getMessage());
				}
				
				$staks = (array) debug_backtrace();
				
				if ($display!=false) {
						self::$count_end = count($staks);
						self::printTrace($staks);
				}
				
				if ($cfg->getVar('application.writelog.trace')==1) {
					$log->writeLog('trace : '.self::$printTraceLog);
				}
				
				$error_view = new Controller;
				if ($cfg->getVar('application.trace_errors')==1) {
					$error_view->viewError('error',self::$arTrace,$mes,$params);
				} else {
					$error_view->viewError('error_user',self::$arTrace,$mes_user);
				}
				
				exit();
			}

			
			/*
			printArgs функция формирования строки для аргументов и объектов
			@ array mas - ссылка на массив с агументами;
			*/
			
			public static function printArgs(&$mas) {

				if (is_object($mas)) {
						$mas = (array)$mas;
					}
					
					foreach ($mas as $key=>$value) {
					
							if ((!is_array($value)) && (!is_object($value))) {						
								self::$args = self::$args."{".$key." : ".$value."} , <br>";
								self::$obj = self::$obj."{".$key." : ".$value."} , <br>";		
							} else {					
									self::printArgs($value);	
						} 	
					}
			}
			
			
			
			/*
			printTrace функция формирования массива с трасировкой
			@ array mas - ссылка на массив с трасировкой;
			*/
			
			public static function printTrace(&$mas) {

				if (is_object($mas)) {
						$mas = (array)$mas;
					}
					
					$file = null;
					$line = null;
					$function = null;
					$class = null;
					$type = null;
					$args = null;
					$obj = null;
					
					foreach ($mas as $key=>$value) {
					
						if (($key!='args') && ($key!='object')) {

							if ((!is_array($value)) && (!is_object($value))) {				
										
								switch($key) {
									case "file" : $file = $value; break;
									case "line" : $line = $value; break; 
									case "function": $function = $value; break;
									case "class": $class = $value; break;
									case "type" : $type = $value; break; 
									default : break;
								}
												
							} else {
									
									self::$count = count($value);
									self::printTrace($value);
							}
						} else {
							
							if ($key=='args') {
								self::$args = "";
								self::printArgs($value);
								$args = self::$args;
							}
							
							if ($key=='object') {
								self::$obj = "";
								self::printArgs($value);
								$obj = self::$obj;
							}
						}
							
					}
					
					
					
					if ((self::$count>1) && (self::$count_end!=self::$itr) && ($file!="")) {
						self::$itr++;
						self::$arTrace[self::$itr]['file'] = $file;
						self::$arTrace[self::$itr]['line'] = $line;
						self::$arTrace[self::$itr]['function'] = $function;
						self::$arTrace[self::$itr]['class'] = $class;
						self::$arTrace[self::$itr]['type'] = $type;
						self::$arTrace[self::$itr]['args'] = $args;
						self::$arTrace[self::$itr]['obj'] = $obj;
						self::$printTraceLog = self::$printTraceLog."\n['file'] : [".$file."] , "."['line'] : [".$line."] , "."['function'] : [".$function."] , "."['class'] : [".$class."] , "."['type'] : [".$type."] \n args : "."['args'] : [".$args."] , "."['object'] : [".$obj."]";
						self::$printTraceLog = strip_tags(self::$printTraceLog);
					}
			}
			
			/*
			ThrowDef функция исключения общая
			@ string mes - сообщение исключения системное;
			@ string mes_user - сообщение для пользователя;
			@ bool display - отображение трасировки;
			@ string mes_user - сообщение для пользователя;
			@ array params - параметры сообщения;
			*/
		 
			public static function ThrowDef ($mes,$display = true,$mes_user = 'Страница не найдена',$params = 404) {
				$cfg = new Config;
				if ($cfg->getVar('application.writelog')==1) {
					$log = new Log('error');
					$log->writeLog("\n");
					$log->writeLog("------------Exeption------------");
					$log->writeLog("time : ".date('h:i:s'));
					$log->writeLog('message : '.$mes);
				}
				
				$staks = (array) debug_backtrace();
				if ($display!=false) {
					self::$count_end = count($staks);
					self::printTrace($staks);
				}
				
				if ($cfg->getVar('application.writelog.trace')==1) {
					$log->writeLog('trace : '.self::$printTraceLog);
				}
				
				$error_view = new Controller;
				
				if ($cfg->getVar('application.trace_errors')==1) {
					$error_view->viewError('error',self::$arTrace,$mes,$params);
				} else {
					$error_view->viewError('error_user',self::$arTrace,$mes_user,$params);
				}
				
				exit();
			}
			
}