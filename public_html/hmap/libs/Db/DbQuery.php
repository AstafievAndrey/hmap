<?php
    namespace Db;
    
    use Db\Db as Db;
    use Thr\Exept as Exeption;
    use Validate\Valid as Valid;
    use PDO;
    /*
     @ protected $sql класс для формирования строки запроса
     */
    class DbQuery extends Db{
    
        public $_sql;
        protected $_db;
	protected $bindVal;

	public function __construct($conn = "mysql") {
            parent::__construct($conn);
        }
	
	//подготовка переменных для подготовленного запроса с именованными псевдопеременными
	public function setValue($param) {
	    $this->bindVal = $param;
	    return $this;
	}
	
	//Выполнение подготовленного запроса с именованными псевдопеременными
	protected function bind($type,$assoc=false){
	    if($type!=="select"){
		$this->_sql = str_replace("'", "", $this->_sql);
		$sth = $this->_pdo->prepare($this->_sql);
	    }else{
		$sth = $this->_pdo->prepare($this->_sql);
	    }
	    foreach ($this->bindVal as $key => $value) {
		$sth->bindValue(':'.$key, $value);
	    }
	    $this->bindVal=[];
	    if($type=="select"){
		$sth->execute();
		return (!$assoc) ? $sth->fetchall() : $sth->fetchall(PDO::FETCH_ASSOC);
	    }else{
		if($sth->execute()){return 1;}else{return 0;};
	    }
	    
	}


	/*
            выполняет сформированный заброс хранящийся в перменной  $_sql
        */
        public function exec($assoc=false){
            //echo $this->_sql; die();
            try{
                $this->_pdo->exec('SET NAMES utf8');
                switch ($this->_sql){
                    case stripos($this->_sql, 'select'):
			if(count($this->bindVal)>0){
			    $result=$this->bind("select",$assoc);
			}else{
			    $q=$this->_pdo->query($this->_sql);
			    $result = (!$assoc) ? $q->fetchall() : $q->fetchall(PDO::FETCH_ASSOC);
			}
                        break;
                    case stripos($this->_sql, 'delete'):    
                        $this->_pdo->exec($this->_sql) ? $result=1 : $result=0; 
                        break;
                    case stripos($this->_sql, 'insert'):
			if(count($this->bindVal)>0){
			    $result=$this->bind("insert");
			}else{
			    $this->_pdo->exec($this->_sql) ? $result=1 : $result=0;
			}
                        break;
                    case stripos($this->_sql, 'update'):  
                        //$this->_pdo->exec($this->_sql) ? $result=1 : $result=0;
			if(count($this->bindVal)>0){
			    $result=$this->bind("update");
			}else{
			    $this->_pdo->exec($this->_sql) ? $result=1 : $result=0;
			}
                        break;                                      
                    default:    return "Cann't exec()";                                     
                }
                return $result;
            } catch (Exception $e) {
                throw Exeption::ThrowDb($e);
            }
        }
        /*
        Выполнение не стандартных запросов
        */
       public function nonstandart($sql,$assoc=false){
            $q=$this->_pdo->query($sql);
            return (!$assoc) ? $q->fetchall() : $q->fetchall(PDO::FETCH_ASSOC);
       }
       /*
         Формируем Select
         */
        public function select($field=null){
            
            if (is_array($field)){
                $str = implode(',',$field);
            } else {
                if ($field==null){
                    $this->_sql="select * ";
                    return $this;
                } else {
                    $str=$field;
                }
            }
            
            if(is_string($str)){
                $this->_sql="select ".$str;
                return $this;
            }
            else {
                throw Exeption::ThrowDef('Ne korrekt param for Select');
            }
        }
        /*
         Формируем Delete
         */
        public function delete($field=null){
            
            if (is_array($field)){
                $str = implode(',',$field);
            } else {
                if ($field==null){
                    $this->_sql="delete ";
                    return $this;
                } else {
                    $str=$field;
                }
            }
            $this->_sql="delete ".$str;
            
        }
        /*
         Формируем Insert
         */
        public function insert($table=null,$field=null,$value=null){
            $str_field="(";// строка где хранятся поля
            $str_value="(";//строка где хранятся значения
            if (($table!=null) && ($field!=null) && ($value!=null)){
                if (is_array($field)){
                    foreach ($field as $key) $str_field.="$key,";
                } else {
                    throw Exeption::ThrowDef("Field isn't array");
                }
                if (is_array($value)){
                    foreach ($value as $key) if(is_string($key)) $str_value.="'$key',"; elseif(is_int($key)) $str_value.="$key,";
                } else {
                    throw Exeption::ThrowDef("Value isn't array");
                }
                $str_field=mb_substr($str_field, 0, -1).")";
                $str_value=mb_substr($str_value, 0, -1).")";
            
                $this->_sql="insert into $table $str_field values $str_value";
            } elseif ($value==null){
                $value=$field;
                if (is_array($value)){
                    foreach ($value as $key) if(is_string($key)) $str_value.="'$key',"; elseif(is_int($key)) $str_value.="$key,";
                } else {
                    throw Exeption::ThrowDef("Value isn't array");
                }
                $str_value=mb_substr($str_value, 0, -1).")";
            
                $this->_sql="insert into $table  values $str_value";
            }

            
            return $this;   
        }
        /*
         Формируем Update
         */
        public function update($table=null, $field=null){
            if (($table!=null) && ($field!=null) && (is_string($table)) && (is_string($field))){
                $this->_sql="update $table set $field";
            } else {
                throw Exeption::ThrowDef('Something wrong in update or set');
            }
            return $this;
        }
        /*
         Формируем From
         */
        public function from($field=null){
            if (is_array($field)){
                $str = implode(',',$field);
            } else {
                if ($field==null){
                    throw Exeption::ThrowDef('FROM is null');
                } else {
                    $str=$field;
                }
            }
            $this->_sql.=' from '.$str;
            return $this;
        }
        /*
         Формируем Join
         */
        public function join($table=null,$tabl=null,$field=null){
            if ((is_string($field)) && ($field!=null) && (is_string($table)) && ($table!=null) && (is_string($tabl)) && ($tabl!=null) ){
                $this->_sql.=" LEFT JOIN $tabl on $table.$field = $tabl.$field";
            }else {
                throw Exeption::ThrowDef("Join don't have table or field");
            }
            
            return $this;
        }
        /*
         Формируем Limit
         */
        public function limit($count=null,$count_t=null){
            if (is_int($count)){
                $this->_sql.=" limit $count";
                if ((is_int($count_t)) && ($count_t!=null)){
                    $this->_sql.=",$count_t";
                }
            }else {
                throw Exeption::ThrowDef("Limit error");
            }
            
            return $this;
        }
        /*
         Формируем Where
         */
        public function where($field=null){
            if ((is_string($field)) && ($field!=null) ){
                //$field = $this->_db->_pdo->quote($field);
                if (!stripos($this->_sql, 'where')) $this->_sql.=' where '.$field;
                else $this->_sql.=' and '.$field;
            }else {
                throw Exeption::ThrowDef('WHERE is null or array');
            }
            
            return $this;
        }
        
        public function orwhere($field=null){
            if ((is_string($field)) && ($field!=null) ){
                //$field = $this->_db->_pdo->quote($field);
                if (!stripos($this->_sql, 'where')) $this->_sql.=' where '.$field;
                else $this->_sql.=' or '.$field;
            }else {
                throw Exeption::ThrowDef('WHERE is null or array');
            }
            
            return $this;
        }
        
        /*
         Формируем Order
         */
        public function order($field=null){
            if ((is_string($field)) && ($field!=null) ){
                $this->_sql.=' order by '.$field;
            }else {
                throw Exeption::ThrowDef('Order is null or array');
            }
            
            return $this;
        }
        
        
    }