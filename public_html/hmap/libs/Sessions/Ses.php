<?php
namespace Sessions;
	
	/*
	Ses - ����� ��� ������ � ��������
	__construct - ������ ������
	*/
	
	use Thr\Exept as Exeption;	

	class Ses {
		
		public function __construct() {
			try {
				if (session_id()=="")  session_start();
			} catch (Exception $e) {
				throw Exeption::ThrowDef('Failed start sesions - '.$e);
			}
			
		}
	
		/*
		getSesValue($ses_name) - ��������� �������� ������ �� �����
		@ string ses_name - ��� ������
		return string ���������� �������� ������
		*/
		
		public function getSesValue($ses_name) {
			if (isset($_SESSION[$ses_name])) {
					return $_SESSION[$ses_name];
				} 
				return false;
		}
		
		/*
		setSesValue($ses_name,$ses_val) - ������������� �������� ������ �� �����
		@ string ses_name - ��� ������
		@ string ses_val - �������� ������
		*/
		
		public function setSesValue($ses_name,$ses_val) {
			try {
				$_SESSION[$ses_name] = $ses_val;
			} catch (Exception $e) {
				throw Exeption::ThrowDef('Failed create sesions var - '.$e);
			}
		}
		
		/*
		delSesValue($ses_name) - ������� �������� ������ �� �����
		@ string ses_name - ��� ������
		*/
		
		public function delSesValue($ses_name) {
			try {
                            unset($_SESSION[$ses_name]);
			} catch (Exception $e) {
				throw Exeption::ThrowDef('Failed delete sesions var - '.$e);
			}
		
		}
		
	}