<?php
namespace Curl;

abstract class AbstractCurl{
    
    protected $_curl;
    
    protected function __construct() {
        $this->_curl=curl_init();
    }
    
}

