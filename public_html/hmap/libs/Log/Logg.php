<?php
namespace Log;

use Thr\Exept as Exeption;

class Logg {

    public $fp;
	public $log_err_file;
	public $log_work_file;
	public $type_log;

	function __construct($type = 'work') {
		
		$this->log_err_file = APP_DIR . '/logs/'.date('d-m-Y').'-error_app.log';
		$this->log_work_file = APP_DIR . '/logs/'.date('d-m-Y').'-work_app.log';
		$this->type_log = $type;
		
		if ($type=='work') {
			try {
				if (!file_exists($this->log_work_file)) {
						$this->fp = fopen($this->log_work_file, 'w');
						fclose($this->fp);
					}
				$this->fp = fopen($this->log_work_file, 'a');
				
			} catch (Exception $e) {
				throw Exeption::ThrowDef('Failed create work file log'.$e->getMessage());
			}
		}
		
		if ($type=='error') {
			try {
				if (!file_exists($this->log_err_file)) {
						$this->fp = fopen($this->log_err_file, 'w');
						fclose($this->fp);
					}
				$this->fp = fopen($this->log_err_file, 'a');
				
			} catch (Exception $e) {
				throw Exeption::ThrowDef('Failed create error file log'.$e->getMessage());
			}
		}
		
	}
	
	public function writeLog($mes = "default message") {
		try {
			fwrite($this->fp,$mes."\n");
        } catch (Exception $e) {
			throw Exeption::ThrowDef('Failed write to file log'.$e->getMessage());
        }
	}
	
	
	function __destruct() {
			try {
				fclose($this->fp);
				
			} catch (Exception $e) {
				throw Exeption::ThrowDef('Failed close work file log'.$e->getMessage());
			}
		}
		
		
	}
