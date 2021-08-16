<?php

class c_pdo
{
	private $host = "localhost";
	private $dbname = "realty";
	private $user = "root";
	private $password = "";
	private $prefix = "ao_";
	private $link = null;
	private $charset = "UTF8";
	private $pdo = null ;
	private $type = "mysql";
	
	function __construct($host="", $base="", $login="", $password=""){
		
		if ($host != "")
		{
			$this->host = $host;
		}
		
		if ($base != "")
		{
			$this->dbname = $base;
		}
		
		if ($login != "")
		{
			$this->user = $login;
		}
		
		if ($password != "")
		{
			$this->password = $password;
		}
		
		try
		{
			$this->pdo = new PDO( $this->type.":host=".$this->host.";dbname=".$this->dbname.";charset=".$this->charset,  $this->user,  $this->password);
			
			if($this->pdo->errorCode() > 0 )
			{
				print $this->pdo->errorInfo();
			}
			
			$this->pdo->query("SET NAMES UTF8;");
		}
		catch(PDOException $e)
		{
			die ("You have an error: ".$e->getMessage()."<br>");
		}
		
		$this->query("set names utf8");
	}
	
	public function query($query)
	{
		$res = $this->pdo->query($query);
		
		//логирование запросов
		//file_put_contents("sql.log",date("Y-m-d H:i:s").": ".$query."\n", FILE_APPEND);
		
		if (is_bool($res)){return false;}
		
		if($this->pdo->errorCode() > 0 )
		{
			print $this->pdo->errorInfo();
		}
		
		$result = $res->FETCHALL(PDO::FETCH_ASSOC);
		
		$out = array();
		foreach($result  as  $array)
		{
			$out []= $this->arrayDecode($array);
		}
		
		if (!is_array($out) || count($out) ==0)
		{
			return true;
		}
		
		return $out;
	}
	
	//возвращает последний добавленный индекс
	public function lastInsertId()
	{
		return $this->pdo->lastInsertId();
	}
	
	//записывает массив в таблицу
	public function insertData($table, $array)
	{
		//соберем параметры и значения
		$params = "" ;
		$values = "" ;
		
		foreach ($array as $key=>$value)
		{
			if ($key != "date" && $key != "pub_date")
			{
				$value = $this->strCleaner($value);
			}
			
			$key = $this->strCleaner($key);
			
			if ($params == "")
			{
				$params .= "`".$key."`" ;
				$values .= "'".$value."'" ;
			}
			else
			{
				$params .= ", `".$key."`" ;
				$values .= ", '".$value."'" ;
			}
		}
		
		$sql = "INSERT INTO `".$table."` (".$params.") VALUES (".$values.")";
		//file_put_contents($_SERVER["DOCUMENT_ROOT"]."/sql.log", $sql."\n", FILE_APPEND);
		
		$this->query($sql);
		
		if ($this->pdo->errorCode()>0)
		{
			return $this->pdo->errorInfo();
		}
		
		return true ;
	}

	//обновляет данные в таблице
	public function updateData($table, $array, $where=array())
	{
		//соберем параметры и значения
		$updates = "" ;
		
		foreach ($array as $key=>$value)
		{
			if ($key != "date" && $key != "pub_date")
			{
				$value = $this->strCleaner($value);
			}
			$key = $this->strCleaner($key);
			
			if ($updates == "")
			{
				$updates .= "`".$key."`='".$value."'" ;
			}
			else
			{
				$updates .= ", `".$key."`='".$value."'" ;
			}
		}
		
		$wheres = "" ;
		if (count($where) > 0 )
		{
			foreach($where as $key=>$value)
			{
				$value = $this->strCleaner($value);
				$key = $this->strCleaner($key);
				
				if ($wheres == "")
				{
					$wheres .= "`".$key."`='".$value."'" ;
				}
				else
				{
					$wheres .= " AND `".$key."`='".$value."'" ;
				}
			}
		}
		
		$sql = "UPDATE `".$table."`  SET ".$updates." WHERE ".$wheres;
		//file_put_contents($_SERVER["DOCUMENT_ROOT"]."/sql.log", $sql."\n", FILE_APPEND);
		$this->query($sql);
		
		if ($this->pdo->errorCode()>0)
		{
			return $this->pdo->errorInfo();
		}
		
		return true ;
	}
	
	public function existsTable($table = 'users') 
	{
 
		try {
			$result = $this->pdo->query("SELECT 1 FROM ".$table." LIMIT 1"); // формальный запрос
		} catch (Exception $e) {
			 
			return FALSE;
		}
 
		return $result !== FALSE;
	}
	
	public function getPrefix()
	{
		return $this->prefix;
	}
	
	public function strCleaner($string){
		$string = htmlspecialchars($string);
		$string = str_replace("'", "{stq}", $string);
		$string = str_replace("\\", "{ws}", $string);
		$string = str_replace("/", "{ls}", $string);
		$string = str_replace(" ", "{s}", $string);		
		$string = str_replace("\"", "{&q}", $string);
		$string = str_replace("=", "{e}", $string);
		$string = str_replace("|", "{v}", $string);
		//$string = str_replace("-", "{minus}", $string);
		$string = str_replace("+", "{p}", $string);
		$string = str_replace("?", "{q}", $string);
		$string = str_replace("&", "{a}", $string);
		$string = str_replace("!", "{w}", $string);
		$string = str_replace("%", "{ps}", $string);
		return $string ;	
	}

	public function strDecode($string){
		$string = str_replace("{stq}", "'", $string);
		$string = str_replace("{ws}", "\\", $string);
		$string = str_replace("{ls}", "/", $string);
		$string = str_replace("{s}", " ", $string);		
		$string = str_replace("{space}", " ", $string);
		$string = str_replace("{&q}", "\"", $string);
		$string = str_replace("{e}", "=", $string);
		$string = str_replace("{ravno}", "=", $string);
		$string = str_replace("{v}", "|", $string);
		//$string = str_replace("{minus}", "-", $string);
		$string = str_replace("{p}", "+", $string);
		$string = str_replace("{q}", "?", $string);
		$string = str_replace("{a}", "&", $string);
		$string = str_replace("{w}", "!", $string);
		$string = str_replace("{ps}", "%", $string);
		$string = htmlspecialchars_decode($string);
		return $string ;	
	}

	public function arrayCleaner($array)
	{
		foreach ($array as $key=>$value)
		{
			if ($key == "date" || $key == "pub_date")
			{
				continue ;
			}
			
			if (is_array($value))
			{
				$array[$key] = $this->arrayCleaner($value) ;
			}
			else
			{
				$array[$key] = $this->strCleaner($value) ;
			}
		}
		return $array ;
	}	
	
	public function arrayDecode($array)
	{
		if (!is_array($array))
		{
			return "" ;
		}
		
		foreach ($array as $key=>$value)
		{
			if ($key == "date" || $key == "pub_date" || is_array($value))
			{
				continue ;
			}
			
			if (is_array($value))
			{
				$array[$key] = $this->arrayDecode($value);
			}
			else
			{
				$array[$key] = $this->strDecode($value);
			}
		}
		return $array ;
	}
}