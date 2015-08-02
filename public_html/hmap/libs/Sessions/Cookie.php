<?php
namespace Sessions;
	
	/*
	Cookie - ����� ��� ������ � ����
	*/
	
	use Thr\Exept as Exeption;	
	

	class Cookie {
	
		/*
		setCookie($name,$val,$time = 0) - ������������� �������� ���� �� �����
		@ string name - ��� ����
		@ string val - �������� ����
		@ int val - ����� ����� ����
		*/
		
		public function setCookie($name,$val,$time = 0) {
			if ($time==0) $time = time()+3600;
			try {
					SetCookie($name,$val,$time);
				} catch (Exception $e)  {
				throw Exeption::ThrowDef('Failed create or change cookie var - '.$e);
			}
		}
		
		/*
		getCookie($name) - ��������� �������� ���� �� �����
		@ string name - ��� ����
		return string ���������� �������� ����
		*/
		
		public function getCookie($name) {
			if (isset($_COOKIE[$name])) {
				return $_COOKIE[$name];
			}
			return false;
		}
		
		
		/*
		delCookie($name) - ������� �������� ���� �� �����
		@ string name - ��� ������
		*/
		
		public function delCookie($name) {
			try {
					SetCookie($name,"");
				} catch (Exception $e)  {
				throw Exeption::ThrowDef('Failed delete cookie var - '.$e);
			}
		}


	}