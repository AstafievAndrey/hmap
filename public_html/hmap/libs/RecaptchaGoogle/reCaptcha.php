<?php
namespace RecaptchaGoogle;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class reCaptcha{

    public function __construct() {

    }
    
    public function checkCaptcha($secret=0,$response=0){
	return  json_decode(
		    file_get_contents("https://www.google.com/recaptcha/api/siteverify"
			    . "?secret=".$secret
			    ."&response=".$response)
		);
    }
}