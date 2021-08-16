<?php

class c_model extends c_pdo
{
	public function __default($param="")
	{
		return true ;
	}
	
	//записывай строку в таблицу
	// $table_name - имя таблицы
	// $associative_array - ассоциативный массив: поле = значение
	// $parent_name
	public function arrayToTable($table_name, $associative_array, $where_name="", $where_value="")
	{
		//табличка не найдена
		if ($this->existsTable($table_name) === false)
		{
			return false;
		}
		
		//подали не массив или пустой массив
		if (!is_array($associative_array) || count($associative_array)==0)
		{
			return false;
		}
		
		if ($where_name == "") //если добавляем новую запись
		{
			$sql = "insert into `".$table_name."` ";
			
			$fields = "";
			$values = "";
			
			foreach($associative_array as $key=>$value)
			{
				if ($fields == "")
				{
					$fields = "`".$key."`";
					$values = "'".$value."'";
				}
				else
				{
					$fields .= ", `".$key."`";
					$values .= ", '".$value."'";
				}
			}
			
			$sql .= "(".$fields.") values (".$values.")" ;
			$this->query($sql);
			
			//file_put_contents("sql.log", date("Y-m-d H:i:s")."	".$sql."\n");
			
			return true;
		}
		else //если идет обновление таблицы
		{
			$sql = "update `".$table_name."` set ";
			
			$str = "";
			
			foreach($associative_array as $key=>$value)
			{
				if ($fields == "")
				{
					$str = "`".$key."`='".$value."'";
				}
				else
				{
					$str .= ", `".$key."`='".$value."'";
				}
			}
			
			$sql .= $str." `".$where_name."`='".$where_value."'" ;
			$this->query($sql);
			
			//file_put_contents("sql.log", date("Y-m-d H:i:s")."	".$sql."\n");
			
			return true;
		}
	}
}