<?php

class ads extends c_pdo
{
	function __construct()
	{
		parent::__construct();
		
		if ($this->existsTable ($this->getPrefix()."ads") === false)
		{
			$this->install();
		}
	}
	
	//установка необъодимых табличек
	private function install()
	{
		$this->query("CREATE TABLE IF NOT EXISTS `".$this->getPrefix()."ads` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `uin` VARCHAR(128),
		  `name` VARCHAR(64),
		  `description` VARCHAR(256),
		  `active` INT NOT NULL DEFAULT '1',
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
	}
	
	private function installDict()
	{
		$this->query("CREATE TABLE IF NOT EXISTS `".$this->getPrefix()."dict` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `uin` VARCHAR(128),
		  `description` VARCHAR(256),
		  `active` INT NOT NULL DEFAULT '1',
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
	}
	
	private function installImageTable()
	{
		$this->query("CREATE TABLE IF NOT EXISTS `".$this->getPrefix()."ads_images` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `section_id` INT NOT NULL DEFAULT '0',
		  `record_id` INT NOT NULL DEFAULT '0',
		  `name` VARCHAR(256),
		  `description` VARCHAR(512),
		  `file_name` VARCHAR(256),
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
	}
	
	//возвращает список контейнеров объявлений
	public function adslist($active_only=true)
	{
		if ($active_only == true)
		{
			$where = "WHERE `active`='1'";
		}
		else
		{
			$where = "";
		}
		
		$res = $this->query("SELECT * FROM `".$this->getPrefix()."ads` ".$where." ORDER BY `name` ASC");
		if (!is_array($res))
		{
			return array();
		}
		
		for ($i=0;$i<count($res);$i++)
		{
			$res[$i] = $this->arrayDecode($res[$i]);
		}
		
		return $res ;
	}
	
	//проверка uin-на
	private function verifyAdsUIN($uin)
	{
		$ch = "qwertyuiopasdfghjklzxcvbnm_";
		
		for ($i=0;$i<strlen($uin);$i++)
		{
			if (strpos($ch, substr($uin,$i,1))===false)
			{
				return false ;
			}
		}
		
		return true ;
	}
	
	//создает набор объявлений
	public function addAds($uin, $name, $description, $active="1")
	{
		if ($this->verifyAdsUIN($uin) == false)
		{
			return false ;
		}
		
		$res = $this->query("SELECT * FROM `".$this->getPrefix()."ads` WHERE `uin`='".$this->strCleaner($uin)."'") ;
		if (is_array($res) && count($res)>0)
		{
			return false ;
		}
		
		$this->query("INSERT INTO `".$this->getPrefix()."ads` (`uin`, `name`, `description`, `active`) VALUES (
			'".$uin."',
			'".$name."',
			'".$description."',
			'".$active."'
		)");
		
		$this->query("CREATE TABLE IF NOT EXISTS `".$this->getPrefix()."ads_".$uin."` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
		
		$this->query("CREATE TABLE IF NOT EXISTS `".$this->getPrefix()."ads_".$uin."_info` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `field` VARCHAR(128),
		  `type` VARCHAR(128),
		  `link` VARCHAR(128),
		  `name` VARCHAR(128),
		  `description` VARCHAR(256),
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
		
		$this->query("INSERT INTO `".$this->getPrefix()."ads_".$uin."_info` (`field`, `type`, `link`, `name`, `description`) VALUES (
			'id',
			'SYSTEM',
			'',
			'АВТОНУМЕРАТОР',
			''
		)");
		
		return true;
	}
	
	//загружает информацию о наборе объаялений
	public function returnAds($id)
	{
		$res = $this->query("SELECT * FROM `".$this->getPrefix()."ads` WHERE `id`='".$this->strCleaner($id)."'" );
		if (!is_array($res))
		{
			return false ;
		}
		
		return $this->arrayDecode($res[0]);
	}
	
	//загружает информацию о наборе объаялений по UIN
	public function returnAdsByUin($uin)
	{
		$res = $this->query("SELECT * FROM `".$this->getPrefix()."ads` WHERE `uin`='".$this->strCleaner($uin)."'" );
		if (!is_array($res))
		{
			return false ;
		}
		
		return $this->arrayDecode($res[0]);
	}
	
	//удаление набора объявлений
	public function deleteAds($id)
	{
		$ads = $this->returnAds($id);
		if ($ads == false)
		{
			return false;
		}
		
		$this->query("DELETE FROM `".$this->getPrefix()."ads` WHERE `id`='".$ads["id"]."'");
		$this->query("DROP TABLE `".$this->getPrefix()."ads_".$ads["uin"]."`");
		$this->query("DROP TABLE `".$this->getPrefix()."ads_".$ads["uin"]."_info`");
		
		return true ;
	}
	
	//возвращает список полей набора объявлений
	public function fieldsAds($id)
	{
		$res = $this->returnAds($id);
		
		if ($res == false)
		{
			return false ;
		}
		
		$fields = $this->query("SELECT * FROM `".$this->getPrefix()."ads_".$res["uin"]."_info` ORDER BY `name` ASC");
		if (!is_array($fields) || count($fields) ==0)
		{
			return false ;
		}
		
		for ($i=0;$i<count($fields);$i++)
		{
			$fields[$i] = $this->arrayDecode($fields[$i]);
		}
		
		return $fields ;
	}
	
	//добавляет поле в структуру набора объявлений
	public function addFieldAds($id, $uin, $type, $link, $name, $description)
	{
		$res = $this->returnAds($id);
		
		if ($type == "SELECT" || $type == "MULTISELECT")
		{
			if ($link == "")
			{
				return false ;
			}
			
			if (strpos($link, "DICT:") === false && strpos($link, "SQL:") === false && strpos($link, "HELPER:") === false)
			{
				return false ;
			}
		}
		
		//проверим UIN на дубликат
		$fields = $this->query("SELECT * FROM `".$this->getPrefix()."ads_".$res["uin"]."_info` WHERE `field`='".$this->strCleaner($uin)."'");
		if (is_array($fields) && count($fields)>0)
		{
			//найдено поле с таким UIN
			return false ;
		}
		
		if ($type != "INT" &&
		$type != "FLOAT" &&
		$type != "BOOLEAN" &&
		$type != "SELECT" &&
		$type != "MULTISELECT" &&
		$type != "TEXT" &&
		$type != "LONGTEXT" &&
		$type != "DATETIME" &&
		$type != "HIDDEN" )
		{
			return false ;
		}
		
		switch($type)
		{
			case "INT": $type_ = "INT"; break;
			case "FLOAT": $type_ = "FLOAT"; break;
			case "BOOLEAN": $type_ = "INT NOT NULL DEFAULT '0'"; break;
			case "SELECT": $type_ = "INT NOT NULL DEFAULT '0'"; break;
			case "MULTISELECT": $type_ = "VARCHAR(256)"; break;
			case "LONGTEXT": $type_ = "LONGTEXT"; break;
			case "TEXT": $type_ = "TEXT"; break;
			case "DATETIME": $type_ = "DATETIME"; break;
			case "HIDDEN": $type_ = "VARCHAR(256)"; break;
		}
		
		$this->query("ALTER TABLE `".$this->getPrefix()."ads_".$res["uin"]."` ADD COLUMN `".$uin."` ".$type_) ;
		$this->query("INSERT INTO `".$this->getPrefix()."ads_".$res["uin"]."_info` (`field`, `type`, `link`, `name`, `description`) VALUES (
			'".$this->strCleaner($uin)."',
			'".$this->strCleaner($type)."',
			'".$this->strCleaner($link)."',
			'".$this->strCleaner($name)."',
			'".$this->strCleaner($description)."'
		)");
		
		return true ;
	}
	
	//удалет поле из структуры набора объявлений по ключу набора объявлений и ключу поля
	public function deleteAdsField($ads_id, $field_id)
	{
		$res = $this->returnAds($this->strCleaner($ads_id));
		
		if ($res === false)
		{
			return false ;
		}
		
		$field = $this->query("SELECT * FROM `".$this->getPrefix()."ads_".$res["uin"]."_info` WHERE `id`='".$this->strCleaner($field_id)."'");
		
		if (is_array($field) && count($field)>0)
		{
			$this->query("DELETE FROM `".$this->getPrefix()."ads_".$res["uin"]."_info` WHERE `id`='".$this->strCleaner($field_id)."'");
			$this->query("ALTER TABLE `".$this->getPrefix()."ads_".$res["uin"]."` DROP `".$this->strDecode($field[0]["field"])."`");
		}
		
		return true;
	}
	
	//выводит список объявлений
	public function adsRecords($ads_id, $from=1, $count=10, $filter=array(), $order=array())
	{
		$ads = $this->returnAds($ads_id);
		
		$res = $this->query("SELECT * FROM `".$this->getPrefix()."ads_".$ads["uin"]."` ORDER BY `id` DESC");
		if (is_array($res) && count($res)>0)
		{
			for ($i=0;$i<count($res);$i++)
			{
				$res[$i] = $this->arrayDecode($res[$i]);
			}
			
			return $res ;
		}
		return array();
	}
	
	//выводит информацию о полях набора объявлений (информацию о структуре набора объявлений)
	public function adsFields($ads_id)
	{
		$ads = $this->returnAds($ads_id);
		if (!is_array($ads) || count($ads) == 0)
		{
			return array();
		}
		
		$fields = $this->query("SELECT * FROM `".$this->getPrefix()."ads_".$this->strDecode($ads["uin"])."_info` ORDER BY `field` ASC");
		if (!is_array($fields) || count($fields) == 0)
		{
			return array();
		}
		
		for ($i=0;$i<count($fields);$i++)
		{
			$fields[$i] = $this->arrayDecode($fields[$i]) ;
		}
		
		return $fields;
	}
	
	//выводит количество объявлений
	public function adsCount($ads_id)
	{
		$ads = $this->returnAds($ads_id);
		if (!is_array($ads) || count($ads) == 0)
		{
			return 0;
		}
		
		$count = $this->query("SELECT COUNT(*) FROM `".$this->getPrefix()."ads_".$ads["uin"]."`");
		
		if (!is_array($count))
		{
			return 0;
		}
		
		return $count[0]["COUNT(*)"] ;
	}
	
	//добавляет запись в набор записей, где id - идэшник записи, data - набор значения полей
	public function addAdsRecord($id, $data)
	{
		$fields = $this->adsFields($id) ;
		$ads = $this->returnAds($id);

		$sql = "INSERT INTO `".$this->getPrefix()."ads_".$ads["uin"]."` " ;
		$f = "" ;
		$v = "" ;
		for ($i=0;$i<count($fields);$i++)
		{
			if ($fields[$i]["type"] == "BOOLEAN")
			{
				if (!isset($data[$fields[$i]["field"]]))
				{
					$data[$fields[$i]["field"]] = "0";
				}
				else
				{
					$data[$fields[$i]["field"]] = "1";
				}
			}
			
			if (isset($data[$fields[$i]["field"]]))
			{
				if ($f == "")
				{
					$f = "`".$fields[$i]["field"]."`" ;
					$v = "'".$data[$fields[$i]["field"]]."'";
				}
				else
				{
					$f .= ", `".$fields[$i]["field"]."`" ;
					$v .= ", '".$data[$fields[$i]["field"]]."'";
				}
			}
		}
		$sql .= "(".$f.") VALUES (".$v.")" ;
		//print $sql ;
		
		$this->query($sql);
		$record_id = lastInsertId();
		
		//проверим картинки
		//print_r($_FILES);
		foreach ($_FILES as $key=>$value)
		{
			if ($_FILES[$key]["error"] == 0 && (strpos($key, "image_") !== false || strpos($key, "photo") !== false))
			{
				if ($this->existsTable($this->getPrefix()."ads_images") === false)
				{
					$this->installImageTable();
				}
				
				$count = $this->query("SELECT * FROM `".$this->getPrefix()."ads_images` WHERE `section_id`='".$ads["id"]."' AND `record_id`='".$record_id."' ORDER BY `id` DESC LIMIT 1");
				if (!is_array($count))
				{
					$count = "1";
				}
				else
				{
					$count = explode (".", $count[0]["file_name"]) ;
					$count = explode ("_", $count[0]);
					$count = ($count[count($count)-1]+1);
				}
				
				$tmp = explode ("_", $key);
				$filename = "image_".$ads["id"]."_".$record_id."_".$count ;
				
				$ia = new c_imageacceptor($key);
				$ia->resizeImageEx($_FILES[$key]["tmp_name"], $filename, 400, 300) ;
				
				$this->query("INSERT INTO `".$this->getPrefix()."ads_images` (`section_id`, `record_id`, `name`, `description`, `file_name`) VALUES (
				'".$ads["id"]."',
				'".$record_id."',
				'".$this->strCleaner($data["image_name_".$tmp[count($tmp)-1]])."',
				'".$this->strCleaner($data["image_description_".$tmp[count($tmp)-1]])."',
				'".$filename.".jpeg'
				)");
			}
		}
		
		return $record_id;
	}
	
	//возвращает элемент ввиде html верстки для добавления или редактирования значения
	public function drawAddElement($record=array())
	{
		if (!is_array($record) || count($record) ==0)
		{
			return "" ;
		}
		
		$out = "" ;
		
		if (!isset($record["value"]))
		{
			$record["value"] = "0";
		}
		
		switch($record["type"])
		{
			case "INT":
					$el = new C_INT($record["field"], $record["name"], $record["description"], (int)$record["value"]);
					$el->onkeyup = "" ;
					$out = $el->html();
				break;
			case "FLOAT":
					$el = new C_FLOAT($record["field"], $record["name"], $record["description"], $record["value"]);
					$el->onkeyup = "" ;
					$out = $el->html();
				break;
			case "BOOLEAN":
					$el = new C_BOOLEAN($record["field"], $record["name"], $record["description"], (int)$record["value"]);
					$el->onkeyup = "" ;
					$out = $el->html();
				break;
			case "SELECT":
					$values = "" ;
					
					if ($record["link"] != "" && $record["link"] != "0")
					{
						$link = explode (":", $record["link"]) ;
						if (!is_array($link) || count($link)<2)
						{
							$values = "";
							$record["value"] = "0";
						}
						else
						{
							if ($link[0] == "HELPER")
							{
								require_once ("helpers/".$link[1].".php") ;
								$helper = new $link[1] ;
								if (!is_object($helper))
								{
									$values = "";
									$record["value"] = "0";
								}
								else
								{
									$values = $helper->execute();
									if (!is_array($values))
									{
										$values = "" ;
										$record["value"] = "0";
									}
								}
							}
							elseif ($link[0] == "DICT")
							{
								if ($this->existsTable ($this->getPrefix()."dict") === false)
								{
									$this->installDict();
								}
								
								$res = $this->query("SELECT * FROM `".$this->db->getPrefix()."dict` WHERE `uin`='".$this->strCleaner($tmp[1])."'");
								if (!is_array($res))
								{
									$values = "" ;
									$record["value"] = "0";
								}
								else
								{
									$dict = $this->query("SELECT a.`id`, a.`value` FROM `".$this->getPrefix()."dict_".$this->strDecode($res[0]["uin"])."` a ORDER BY a.`value` ASC");
									if (!is_array($dict) || count($dict) ==0)
									{
										$values = "" ;
										$record["value"] = "0";
									}
									else
									{
										$values = $dict ;
									}
								}
							}
							elseif ($link[0] == "SQL")
							{
								$res = $this->query($link[1]);
								if (is_array($res) || count($res)>0)
								{
									for ($i=0;$i<count($res);$i++)
									{
										if (!isset($res[$i]["id"]) || !isset($res[$i]["value"]))
										{
											$values = "" ;
											$record["value"] = "0";
											break;
										}
										$res[$i]["id"] = $this->strDecode($res[$i]["id"]) ;
										$res[$i]["value"] = $this->strDecode($res[$i]["value"]) ;
									}
									
									$values = $res ;
								}
								else
								{
									$values = "" ;
									$record["value"] = "0";
								}
							}
							else
							{
								$values = "" ;
								$record["value"] = "0";
							}
						}
					}
					else
					{
						$record["value"] = "0";
					}
					
					$el = new C_SELECT($record["field"], $record["name"], $record["description"], $record["value"], $values);
					$el->onkeyup = "" ;
					$out = $el->html();
				break;
			case "MULTISELECT":
					$values = "" ;
					
					$val = explode (";",$record["value"]);
					if (!is_array($val) || count($val)==0)
					{
						$val = "" ;
					}
					
					if ($record["link"] != "" && $record["link"] != "0")
					{
						$link = explode (":", $record["link"]) ;
						if (!is_array($link) || count($link)<2)
						{
							$values = "";
							$record["value"] = "0";
						}
						else
							{
							if ($link[0] == "HELPER")
							{
								require_once ("helpers/".$link[1].".php") ;
								$helper = new $link[1] ;
								if (!is_object($helper))
								{
									$values = "";
									$record["value"] = "0";
								}
								else
								{
									$values = $helper->execute();
									if (!is_array($values))
									{
										$values = "" ;
										$record["value"] = "0";
									}
								}
							}
							elseif ($link[0] == "DICT")
							{
								if ($this->existsTable ($this->getPrefix()."dict") === false)
								{
									$this->installDict();
								}
								
								$res = $this->query("SELECT * FROM `".$this->db->getPrefix()."dict` WHERE `uin`='".$this->strCleaner($tmp[1])."'");
								if (!is_array($res))
								{
									$values = "" ;
									$record["value"] = "0";
								}
								else
								{
									$dict = $this->query("SELECT a.`id`, a.`value` FROM `".$this->getPrefix()."dict_".$this->strDecode($res[0]["uin"])."` a ORDER BY a.`value` ASC");
									if (!is_array($dict) || count($dict) ==0)
									{
										$values = "" ;
										$record["value"] = "0";
									}
									else
									{
										$values = $dict ;
									}
								}
							}
							elseif ($link[0] == "SQL")
							{
								$res = $this->query($link[1]);
								if (is_array($res) || count($res)>0)
								{
									for ($i=0;$i<count($res);$i++)
									{
										if (!isset($res[$i]["id"]) || !isset($res[$i]["value"]))
										{
											$values = "" ;
											$record["value"] = "0";
											break;
										}
										$res[$i]["id"] = $this->strDecode($res[$i]["id"]) ;
										$res[$i]["value"] = $this->strDecode($res[$i]["value"]) ;
									}
									
									$values = $res ;
								}
								else
								{
									$values = "" ;
									$record["value"] = "0";
								}
							}
							else
							{
								$values = "" ;
								$record["value"] = "0";
							}
						}
					}
					else
					{
						$record["value"] = "0";
					}
					
					$el = new C_MULTISELECT($record["field"], $record["name"], $record["description"], $record["value"], $values);
					$el->onkeyup = "" ;
					$out = $el->html();
				break;
			case "LONGTEXT":
					$el = new C_LONGTEXT($record["field"], $record["name"], $record["description"], $record["value"]);
					$el->onkeyup = "" ;
					$out = $el->html();
				break;
			case "TEXT":
					$el = new C_TEXT($record["field"], $record["name"], $record["description"], $record["value"]);
					$el->onkeyup = "" ;
					$out = $el->html();
				break;
			case "DATETIME":
					$el = new C_DATETIME($record["field"], $record["name"], $record["description"], $record["value"]);
					$el->onkeyup = "" ;
					$out = $el->html();
				break;
			case "HIDDEN":
					$el = new C_HIDDEN($record["field"], $record["name"], $record["description"], $record["value"]);
					$el->onkeyup = "" ;
					$out = $el->html();
				break;
			case "SYSTEM":
					$out = "";
				break;
		}
		
		return $out;
	}
	
	//возвращает значения записи (value) который и только !!!! в массиве !!!
	public function getRecordValues($set_id, $rec_id)
	{
		$ads = $this->returnAds($set_id);
		
		$rec_id = $this->query("SELECT * FROM `".$this->getPrefix()."ads_".$ads["uin"]."` WHERE `id`='".$this->strCleaner($rec_id)."'");
		return $rec_id[0];
	}
	
	//обрабатывает поле LINK у SELECT и  MULTISELECT
	public function returnLink($type, $value)
	{
		if ($type == "HELPER")
		{
			if (file_exists($_SERVER["DOCUMENT_ROOT"]."/helpers/".strtolower($value).".php") === false)
			{
				return array();
			}
			
			require_once ("helpers/".strtolower($value).".php") ;
			$helper = new $value ;
			return $helper->execute();
		}
		elseif ($type == "DICT")
		{
			if ($this->existsTable ($this->getPrefix()."dict") === false)
			{
				$this->installDict();
			}
			
			$res = $this->query("SELECT * FROM `".$this->db->getPrefix()."dict` WHERE `uin`='".$this->strCleaner($value)."'");
			if (!is_array($res))
			{
				return array();
			}
			else
			{
				$dict = $this->query("SELECT a.`id`, a.`value` FROM `".$this->getPrefix()."dict_".$this->strDecode($res[0]["uin"])."` a ORDER BY a.`value` ASC");
				if (!is_array($dict) || count($dict) ==0)
				{
					return array();
				}
				else
				{
					for ($i=0;$i<count($dict);$i++)
					{
						$dict[$i] = $this->arrayDecode($dict[$i]);
					}
					return $dict ;
				}
			}
		}
		elseif ($type == "SQL")
		{
			$res = $this->query($value);
			if (is_array($res) || count($res)>0)
			{
				for ($i=0;$i<count($res);$i++)
				{
					if (!isset($res[$i]["id"]) || !isset($res[$i]["value"]))
					{
						return array();
						break;
					}
					$res[$i]["id"] = $this->strDecode($res[$i]["id"]) ;
					$res[$i]["value"] = $this->strDecode($res[$i]["value"]) ;
				}
				
				$values = $res ;
			}
		}
		
		return array();
	}
	
	//обновляет запись в БД
	public function updateAdsRecord($id, $rec_id, $data)
	{
		$fields = $this->adsFields($id) ;
		$ads = $this->returnAds($id);

		$sql = "UPDATE `".$this->getPrefix()."ads_".$ads["uin"]."` SET " ;
		$f = "" ;
		$v = "" ;
		for ($i=0;$i<count($fields);$i++)
		{
			if ($fields[$i]["type"] == "BOOLEAN")
			{
				if (!isset($data[$fields[$i]["field"]]))
				{
					$data[$fields[$i]["field"]] = "0";
				}
				else
				{
					$data[$fields[$i]["field"]] = "1";
				}
			}
			
			if (isset($data[$fields[$i]["field"]]))
			{
				if ($f == "")
				{
					$f = "`".$fields[$i]["field"]."` = '".$data[$fields[$i]["field"]]."'";
				}
				else
				{
					$f .= ", `".$fields[$i]["field"]."` = '".$data[$fields[$i]["field"]]."'";
				}
			}
		}
		$sql .= $f. " WHERE `id`='".$this->strCleaner($rec_id)."'" ;
		
		$this->query($sql);
		$record_id = $rec_id;
		
		//проверим картинки
		foreach ($_FILES as $key=>$value)
		{
			if ($_FILES[$key]["error"] == 0 && strpos($key, "image_") !== false)
			{
				if ($this->existsTable($this->getPrefix()."ads_images") === false)
				{
					$this->installImageTable();
				}
				
				$count = $this->query("SELECT * FROM `".$this->getPrefix()."ads_images` WHERE `section_id`='".$ads["id"]."' AND `record_id`='".$record_id."' ORDER BY `id` DESC LIMIT 1");
				if (!is_array($count))
				{
					$count = "1";
				}
				else
				{
					$count = explode (".", $count[0]["file_name"]) ;
					$count = explode ("_", $count[0]);
					$count = ($count[count($count)-1]+1);
				}
				
				$tmp = explode ("_", $key);
				$filename = "image_".$ads["id"]."_".$record_id."_".$count ;
				
				$ia = new c_imageacceptor($key);
				$ia->resizeImageEx($_FILES[$key]["tmp_name"], $filename, 400, 300) ;
				
				$this->query("INSERT INTO `".$this->getPrefix()."ads_images` (`section_id`, `record_id`, `name`, `description`, `file_name`) VALUES (
				'".$ads["id"]."',
				'".$record_id."',
				'".$this->strCleaner($data["image_name_".$tmp[count($tmp)-1]])."',
				'".$this->strCleaner($data["image_description_".$tmp[count($tmp)-1]])."',
				'".$filename.".jpeg'
				)");
			}
		}		
		
		return true;	
	}
	
	//возвращает все картинки записи (объявления)
	public function recordImages($section, $record)
	{
		$res = $this->query("SELECT * FROM `".$this->getPrefix()."ads_images` WHERE `section_id`='".$this->strCleaner($section)."' AND `record_id`='".$this->strCleaner($record)."' ORDER BY `id` ASC");
		if (!is_array($res))
		{
			return array();
		}
		
		for ($i=0;$i<count($res);$i++)
		{
			$res[$i] = $this->arrayDecode($res[$i]);
		}
		
		return $res ;
	}
	
	//удаляем картинку у записи
	public function deleteRecordImage($filename)
	{
		$res = $this->query("SELECT * FROM `".$this->getPrefix()."ads_images` WHERE `file_name`='".$filename."'") ;
		unlink($_SERVER["DOCUMENT_ROOT"]."/images/upload/".$filename) ;
		$this->query ("DELETE FROM `".$this->getPrefix()."ads_images` WHERE `file_name`='".$filename."'");
		return $res[0] ;
	}
	
	//удаляем запись
	public function deleteAdsRecord($section, $record)
	{
		$ads = $this->returnAds($section) ;
		if (is_array($ads))
		{
			$this->query("DELETE FROM `".$this->getPrefix()."ads_".$ads["uin"]."` WHERE `id`='".$this->strCleaner($record)."'");
			$images = $this->recordImages($section, $record) ;
			if (is_array($images) && count($images)>0)
			{
				for ($i=0;$i<count($images);$i++)
				{
					$this->deleteRecordImage($images[$i]["file_name"]) ;
				}
			}
		}
		return true;
	}
	
	//возвращает картинки у объявления
	public function getRecordImages($ads_id, $rec_id)
	{
		$images = $this->query("SELECT * FROM `".$this->getPrefix()."ads_images` WHERE `section_id`='".$ads_id."' AND `record_id`='".$rec_id."' ORDER BY `id` ASC");
		if (!is_array($images))
		{
			return array();
		}
		
		for ($i=0;$i<count($images);$i++)
		{
			$images[$i] = $this->arrayDecode($images[$i]);
		}
		
		return $images;
	}
}