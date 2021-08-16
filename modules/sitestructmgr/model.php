<?php

class sitestructmgr_model extends c_model
{
	//возвращает структуру сайта в массиве
	private function getStruct($parent_id=0, $path="")
	{
		$res = $this->query("SELECT * FROM `".$this->getPrefix()."site_struct` WHERE `parent_id`='".$this->strCleaner($parent_id)."' ORDER BY `order` ASC");
		
		if (!is_array($res))
		{
			return false ;
		}
		
		for ($i=0;$i<count($res);$i++)
		{
			$res[$i] = $this->arrayDecode($res[$i]);
			
			if ($res[$i]["id"] == "1")
			{
				$res[$i]["path"] = ".." ;			
				$tmp = $this->getStruct($res[$i]["id"], "..") ;
			}
			else
			{
				$res[$i]["path"] = $path."/".$res[$i]["url"] ;			
				$tmp = $this->getStruct($res[$i]["id"], $path."/".$res[$i]["url"]) ;
			}
			
			if (is_array($tmp))
			{
				$res[$i]["childs"] = $tmp ;
			}
			
		}
		
		return $res ;
	}
	
	//возращает список параметров у страницы
	function getParams($id)
	{
		$res = $this->query("SELECT * FROM `".$this->getPrefix()."site_struct_params` WHERE `site_struct_id`='".$id."' ORDER BY `order` ASC");
		if (!is_array($res))
		{
			return array();
		}
		
		for ($i=0;$i<count($res);$i++)
		{
			$res[$i]["name"] = $this->strDecode($res[$i]["name"]);
		}
		
		return $res ;
	}
	
	//возвращает путь до корня
	public function getPath($id)
	{
		$res = $this->query("SELECT * FROM `".$this->getPrefix()."site_struct` WHERE `id`='".$this->strCleaner($id)."'");
		if (!is_array($res))
		{
			print "zdes";
			return false ;
		}
		
		$url = "" ;
		while ($res[0]["parent_id"] != "0")
		{
			$url = $this->strDecode($res[0]["url"])."/".$url ;
			$res = $this->query("SELECT * FROM `".$this->getPrefix()."site_struct` WHERE `id`='".$res[0]["parent_id"]."'");
		}
		
		return $url;
	}
	
	//возвращает определенную структуру сайта (страничку)
	public function getSiteStruct($id)
	{
		$res = $this->query("SELECT * FROM `".$this->getPrefix()."site_struct` WHERE `id`='".$this->strCleaner($id)."'");
		//print "SELECT * FROM `".$this->getPrefix()."site_struct` WHERE `id`='".$this->strCleaner($id)."'";
		if (!is_array($res))
		{
			return false ;
		}
		
		return $this->arrayDecode($res[0]);
	}
	
	//возвращает свобоный порядок у страницы
	public function getFreeOrder($parent_id)
	{
		$res = $this->query("SELECT * FROM `".$this->getPrefix()."site_struct` WHERE `parent_id`='".$this->strCleaner($parent_id)."' ORDER BY `order` ASC");
		if (!is_array($res))
		{
			return "1";
		}
		
		for ($i=0;$i<count($res);$i++)
		{
			$this->updateData($this->getPrefix()."site_struct", array("order"=>($i+1)), array("id"=>$res[$i]["id"]));
		}
		
		return ($i+2) ;
	}
	
	//проверяет на уникальность URL
	public function validURL($url, $parent_id)
	{
		$ch = "1234567890-_qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM.йцукенгшщзхъфывапролджэячсмитьбюЙЦУКЕНГШЩЗХЪФЫВАПРОЛДЖЭЯЧСМИТЬБЮ";
		for ($i=0;$i<strlen($url);$i++)
		{
			if (strpos($ch, substr($url,$i,1)) === false)
			{
				return false ;
			}
		}
		
		$res = $this->query("SELECT * FROM `".$this->getPrefix()."site_struct` WHERE `parent_id`='".$this->strCleaner($parent_id)."' AND `url`='".$this->strCleaner($url)."' ORDER BY `order` ASC");
		if (is_array($res))
		{
			return false ;
		}
		
		return true;
	}

	//проверяет на уникальность URL
	public function validURLEx($url, $id, $parent_id)
	{
		$ch = "1234567890-_qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM.йцукенгшщзхъфывапролджэячсмитьбюЙЦУКЕНГШЩЗХЪФЫВАПРОЛДЖЭЯЧСМИТЬБЮ";
		for ($i=0;$i<strlen($url);$i++)
		{
			if (strpos($ch, substr($url,$i,1)) === false)
			{
				return false ;
			}
		}
		
		$res = $this->query("SELECT * FROM `".$this->getPrefix()."site_struct` WHERE `parent_id`='".$this->strCleaner($parent_id)."' AND `id` != '".$this->strCleaner($id)."' AND `url`='".$this->strCleaner($url)."' ORDER BY `order` ASC");
		if (is_array($res))
		{
			return false ;
		}
		
		return true;
	}	
	
	//вставляет данные о структуре
	public function insertSiteStruct()
	{
		if ($_POST["parent_id"] == "")
		{
			$_POST["parent_id"] = "1";
		}
		
		if ($_POST["template_id"] == "")
		{
			$_POST["template_id"] = "1";
		}
		
		$_POST["order"] = $this->getFreeOrder($_POST["parent_id"]);
		
		$this->insertData($this->getPrefix()."site_struct", $_POST);
		return true ;
	}
	
	//обновляет структуру сайта
	public function updateSiteStruct()
	{
		if ($_POST["parent_id"] == "")
		{
			$_POST["parent_id"] = "1";
		}
		
		if ($_POST["template_id"] == "")
		{
			$_POST["template_id"] = "1";
		}
		
		//проверим параметры
		
		$this->query("DELETE FROM `".$this->getPrefix()."site_struct_params` WHERE `site_struct_id`='".$this->strCleaner($_GET["id"])."'");
		$order = 0;
		foreach($_POST as $key=>$value)
		{
			if (strpos($key, "get_param_") !== false)
			{
				if ($_POST[$key] !== "")
				{
					$this->insertData($this->getPrefix()."site_struct_params", array("site_struct_id"=>$this->strCleaner($_GET["id"]), "name"=>$this->strCleaner($_POST[$key]), "order"=>$order)) ;
					$order++;
				}
				unset ($_POST[$key]);
			}
		}
		
		$this->updateData($this->getPrefix()."site_struct", $_POST, array("id"=>$this->strCleaner($_GET["id"])));
		return true ;
	}
	
	//возвращает список шаблонов
	public function getTemplates()
	{
		$res = $this->query("SELECT * FROM `".$this->getPrefix()."site_templates` ORDER BY `menu_name` ASC");
		if (is_array($res))
		{
			for ($i=0;$i<count($res);$i++)
			{
				$res[$i] = $this->arrayDecode($res[$i]) ;
			}
		}
		
		return $res ;
	}
	
	//метод по умолчанию
	public function __default($param="")
	{
		return $this->getStruct();
	}
	
	//удаляет рекурсивно все подразделы
	private function deleteStructs ($structs)
	{
		for ($i=0;$i<count($structs);$i++)
		{
			if (isset($structs[$i]["childs"]) && count($structs[$i]["childs"])>0)
			{
				$this->deleteStructs($structs[$i]["childs"]);
			}
			
			$this->query("DELETE FROM `".$this->getPrefix()."site_struct` WHERE `id`='".$structs[$i]["id"]."'");
		}
	}
	
	//удаляет раздел с подразделами
	public function deleteSiteStruct($id)
	{
		$structs = $this->getStruct($this->strCleaner($id));
		$this->deleteStructs($structs) ;
		$this->query("DELETE FROM `".$this->getPrefix()."site_struct` WHERE `id`='".$this->strCleaner($id)."'");
		return true ;
	}
	
	//поднять на один уровень вверх
	public function levelUpStruct($id)
	{
		//запросим саму структуру
		$struct = $this->query("SELECT `parent_id` FROM `".$this->getPrefix()."site_struct` WHERE `id`='".$this->strCleaner($id)."'");
		if (!is_array($struct))
		{
			return false ;
		}
		
		//запросим список сруктур
		$structs = $this->query("SELECT * FROM `".$this->getPrefix()."site_struct` WHERE `parent_id`='".$struct[0]["parent_id"]."' ORDER BY `order` ASC");
		
		if (!is_array($structs))
		{
			return false ;
		}
		
		//если он у нас и так первый  - выходим
		if ($structs[0]["id"] == $this->strCleaner($id))
		{
			return true ;
		}
		
		//пробежимся по списку
		for ($i=1;$i<count($structs);$i++)
		{
			if ($structs[$i]["id"] == $id)
			{
				$this->updateData ($this->getPrefix()."site_struct", array("order"=>$structs[$i-1]["order"]), array("id"=>$structs[$i]["id"])) ;
				$this->updateData ($this->getPrefix()."site_struct", array("order"=>$structs[$i]["order"]), array("id"=>$structs[$i-1]["id"])) ;
			}
		}
		
		return true ;
	}
	
	//поднять на один уровень вверх
	public function levelDownStruct($id)
	{
		//запросим саму структуру
		$struct = $this->query("SELECT `parent_id` FROM `".$this->getPrefix()."site_struct` WHERE `id`='".$this->strCleaner($id)."'");
		if (!is_array($struct))
		{
			return false ;
		}
		
		//запросим список сруктур
		$structs = $this->query("SELECT * FROM `".$this->getPrefix()."site_struct` WHERE `parent_id`='".$struct[0]["parent_id"]."' ORDER BY `order` ASC");
		
		if (!is_array($structs))
		{
			return false ;
		}
		
		//если он у нас и так первый  - выходим
		if ($structs[count($structs)-1]["id"] == $this->strCleaner($id))
		{
			return true ;
		}
		
		//пробежимся по списку
		for ($i=0;$i<count($structs)-1;$i++)
		{
			if ($structs[$i]["id"] == $id)
			{
				$this->updateData ($this->getPrefix()."site_struct", array("order"=>$structs[$i+1]["order"]), array("id"=>$structs[$i]["id"])) ;
				$this->updateData ($this->getPrefix()."site_struct", array("order"=>$structs[$i]["order"]), array("id"=>$structs[$i+1]["id"])) ;
			}
		}
		
		return true ;
	}	
}