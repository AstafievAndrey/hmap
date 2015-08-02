<?php
    namespace Db;
    
    use Config\Ini as Ini;

    /*
     класс для работы с БД
     
     @ static _instance статическая переменная хрянящаяя подключение к бд;
     @ public param_ini хранятся параметры ini файла
     */
    
    class Connection extends Db{
        
        public function __construct(){
            
            return parent::$_instanse;
        
        }
        
    }