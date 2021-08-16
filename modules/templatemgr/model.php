<?php

class templatemgr_model extends c_model
{
	//возвращает список шаблонов
	public function loadTemplates($data="")
	{
		$res = $this->query("SELECT * FROM `".$this->getPrefix()."site_templates` ORDER BY `name` ASC");
		if (!is_array($res))
		{
			return true ;
		}
		
		for ($i=0;$i<count($res);$i++)
		{
			$res[$i] = $this->arrayDecode($res[$i]);
		}
		
		return $res ;
	}
	
	//возвращает информацию о шаблоне
	public function getTemplate($id)
	{
		$res = $this->query("SELECT * FROM `".$this->getPrefix()."site_templates` WHERE `id`='".$this->strCleaner($id)."'");
		if (!is_array($res))
		{
			return true ;
		}
		
		$content = file_get_contents($_SERVER["DOCUMENT_ROOT"]."/templates/template_".$res[0]["id"].".html") ;
		$res[0]["content"] = $content ;
		
		return $this->arrayDecode($res[0]);
	}
	
	//обновляет шаблон
	public function updateTemplate($data = "")
	{
		$this->updateData($this->getPrefix()."site_templates", 
			array(
					"name"=>$this->strCleaner($data["name"]), 
					"menu_name"=>$this->strCleaner($data["menu_name"]), 
					"description"=>$this->strCleaner($data["description"])
					),
			array(
					"id"=>$this->strCleaner($data["id"])
					));
		file_put_contents($_SERVER["DOCUMENT_ROOT"]."/templates/template_".$data["id"].".html", $this->strCleaner($data["content"]));
		return true ;
	}
	
	//добавление шаблона
	public function insertTemplate($data)
	{
		$this->insertData($this->getPrefix()."site_templates", 
			array(
					"name"=>$this->strCleaner($data["name"]), 
					"menu_name"=>$this->strCleaner($data["menu_name"]), 
					"description"=>$this->strCleaner($data["description"])
					));
		file_put_contents($_SERVER["DOCUMENT_ROOT"]."/templates/template_".$this->lastInsertId().".html", $this->strCleaner($data["content"]));
		return true ;
	}
	
	//удаление шаблона
	public function deleteTemplate($id)
	{
		$this->query("DELETE FROM `".$this->getPrefix()."site_templates` WHERE `id`='".$this->strCleaner($id)."'");
		unlink($_SERVER["DOCUMENT_ROOT"]."/templates/template_".$id.".html") ;
		return true ;
	}
}