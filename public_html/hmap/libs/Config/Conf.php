<?php
    namespace Config;
    
    class Conf extends Ini {
        
		public function getVar($var) {
			try {
				if ($this->ArrayIni[$var]) return $this->ArrayIni[$var];
			} catch (Exception $e) {
				Exeption::ThrowDef('Failed get config var'.$e->getMessage());
			}
		}
		
  
    }
