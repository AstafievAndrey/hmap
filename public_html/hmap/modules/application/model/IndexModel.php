<?php
namespace model;

use Db\DbQuery as DbQuery;
use Log\Logg as Log;
use Config\Conf as Config;
use PDO;
    
class IndexModel {

	public function getBase(){
                	
                $db = new DbQuery();
                $sql = $db->select()
                        ->from('Current_Coordinate')
                        ->where('Device_Id = 1 or Device_Id=3');
                echo '<br>Select ';
                var_dump($sql->exec());
                
                $sql = $db->select()
                        ->from('Organizations')
                        ->join('Organizations','Cities','City_Id');
                echo '<br>Join ';
                var_dump($sql->exec());
                echo '<br>Delete ';
                $sql_delete = $db->delete()
                        ->from('Cities')
                        ->where("Name_City = 'Альметьевск'");
                var_dump($sql_delete->exec());
                echo '<br>Insert ';
                $sql_insert = $db->insert('Cities',array('Name_City'),array('Бугульма'));
                var_dump($sql_insert->exec());
                echo '<br>Update ';
                $sql_set = $db->update('Cities',"Name_City='Альметьевск'")->where("Name_City='Бугульма'");
                var_dump($sql_set->exec());
                echo '<br><br>';	
		echo "This model";
                $db = new DbQuery('mssql');
                $sql = $db->select()
                        ->from('NavMessages')
                        ->order('Id ASC');
                echo '<br>Select ';
                var_dump($sql->exec());
		echo "<br>Model memory : ".(memory_get_usage()/1000)."<br>";
		/**/
	}
	


}
