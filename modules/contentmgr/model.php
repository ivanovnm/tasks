<?php

class contentmgr_model extends c_model
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
	
	//вытаскивает все блоки из текста
	private function extractBlocks($html)
	{
		$start = strpos ($html, "{block:") ;
		$blocks = array();
		
		while ($start !== false)
		{
			$stop = strpos ($html, "}", $start+1) ;
			if ($stop === false)
			{
				break;
			}
			
			$blocks []= substr($html, $start, $stop-$start) ;
			$start = strpos ($html, "{block:", $stop+1) ;
		}
		
		if (count($blocks) == 0)
		{
			return array() ;
		}
		
		//преобразуем массив
		for ($i=0;$i<count($blocks);$i++)
		{
			$blocks[$i] = str_replace ("{", "", $blocks[$i]) ;
			$blocks[$i] = str_replace ("}", "", $blocks[$i]) ;
			
			$tmp = explode (":", $blocks[$i]);			
			$tmp = explode ("=", $tmp[1]) ;
			
			if (count($tmp) == 1)
			{
				$tmp[1] = "" ;
			}
			
			//запросим содержимое блоков
						
			$blocks[$i] = array("name"=>$tmp[0], "description"=>$tmp[1]);
		}
		
		return $blocks;
	}
	
	public function __default($data="")
	{
		return $this->getStruct();
		
	}
	
	//вернем всю информацию о блоках, шаблоне, структуре
	public function getBlocks($struct_id)
	{
		$struct = $this->query("SELECT * FROM `".$this->getPrefix()."site_struct` WHERE `id`='".$this->strCleaner($struct_id)."'");
		if (!is_array($struct))
		{
			return false ;
		}
		$struct = $this->arrayDecode($struct[0]);
		$template = $this->strDecode(file_get_contents($_SERVER["DOCUMENT_ROOT"]."/templates/template_".$struct["template_id"].".html"));
		$blocks = $this->extractBlocks ($template);
		
		for ($i=0;$i<count($blocks);$i++)
		{
			
			$content = $this->query("SELECT * FROM `".$this->getPrefix()."site_struct_blocks` 
				WHERE `site_struct_id`='".$this->strCleaner($struct_id)."' AND `name`='".$blocks[$i]["name"]."' ORDER BY `id` ASC") ;
			if (!is_array($content))
			{
				$content = array();
			}
			for ($j=0;$j<count($content);$j++)
			{
				$content[$j] = $this->arrayDecode($content[$j]);
			}
			//print_r($content);
			$blocks[$i]["content"] = $content ;
		}
		
		return array("struct"=>$struct, "template"=>$template, "blocks"=>$blocks);
	}
	
	//возвращает всю необходимую информацию о контент-блоке и структуре
	public function getStructBlockInfo($struct_id, $block)
	{
		$struct = $this->query("SELECT * FROM `".$this->getPrefix()."site_struct` WHERE `id`='".$this->strCleaner($struct_id)."'");
		if (!is_array($struct))
		{
			return false ;
		}
		$struct = $this->arrayDecode($struct[0]);
		
		$modules = array();
		$handle = opendir($_SERVER["DOCUMENT_ROOT"]."/modules/");
        while (false !== ($item = readdir($handle))) 
		{
			if (is_dir($_SERVER["DOCUMENT_ROOT"]."/modules/".$item) && ($item != ".") && ($item != ".."))
			{
				$modules[]= $item;
			}
		} 
        closedir($handle);
		
		$helpers = array();
		$handle = opendir($_SERVER["DOCUMENT_ROOT"]."/helpers/");
        while (false !== ($item = readdir($handle))) 
		{
			$ext = explode (".",$item);
			if(count($ext)>1)
			{
				$ext = $ext[count($ext)-1] ;
			}
			if (!is_dir($_SERVER["DOCUMENT_ROOT"]."/helpers/".$item) && ($item != ".") && ($item != "..") && $ext == "php")
			{
				$item = explode (".", $item);
				$item = $item[0];
				$helpers[]= $item;
			}
		} 
        closedir($handle);
		
		return array("struct"=>$struct, "block"=>$block, "modules"=>$modules, "helpers"=>$helpers);
	}
	
	//сохраняет контент в блоке
	public function saveBlock($struct_id, $block, $type,$content,$params="")
	{
		if ($params == "")
		{
			$tmp_content = $content ;
		}
		else
		{
			$tmp_content = $content."=".$params ;
		}
		
		$this->insertData($this->getPrefix()."site_struct_blocks", array("site_struct_id"=>$struct_id, "name"=>$block, "type"=>$type, "content"=>$this->strCleaner($tmp_content)));
		return true ;
	}
	
	//удалет контент из блока
	public function deleteContentBlock($id)
	{
		$this->query("DELETE FROM `".$this->getPrefix()."site_struct_blocks` WHERE `id`='".$this->strCleaner($id)."'");
		return true ;
	}
	
	//поднимает вверх контент блок
	public function upContentBlock($id)
	{
		$content = $this->query("SELECT * FROM `".$this->getPrefix()."site_struct_blocks` WHERE `id`='".$this->strCleaner($id)."'");
		$contents = $this->query("SELECT * FROM `".$this->getPrefix()."site_struct_blocks` WHERE `site_struct_id`='".$this->strCleaner($content[0]["site_struct_id"])."' ORDER BY `id` ASC");
		
		for ($i=0;$i<count($contents);$i++)
		{
			if ($contents[$i]["id"] == $id && $i==0)
			{
				return true ;
			}
			elseif ($contents[$i]["id"] == $id)
			{
				$this->updateData($this->getPrefix()."site_struct_blocks", 
					array(
							"site_struct_id"=>$contents[$i]["site_struct_id"],
							"type"=>$contents[$i]["type"],
							"name"=>$contents[$i]["name"],
							"content"=>$contents[$i]["content"],
						), 
					array(
							"id"=>$contents[$i-1]["id"]
						)
					);
				$this->updateData($this->getPrefix()."site_struct_blocks", 
					array(
							"site_struct_id"=>$contents[$i-1]["site_struct_id"],
							"type"=>$contents[$i-1]["type"],
							"name"=>$contents[$i-1]["name"],
							"content"=>$contents[$i-1]["content"],
						), 
					array(
							"id"=>$contents[$i]["id"]
						)
					);
			}
		}
		
		return true ;
	}

	//перемещает вниз контент блок
	public function downContentBlock($id)
	{
		$content = $this->query("SELECT * FROM `".$this->getPrefix()."site_struct_blocks` WHERE `id`='".$this->strCleaner($id)."'");
		$contents = $this->query("SELECT * FROM `".$this->getPrefix()."site_struct_blocks` WHERE `site_struct_id`='".$this->strCleaner($content[0]["site_struct_id"])."' ORDER BY `id` ASC");
		
		for ($i=0;$i<count($contents);$i++)
		{
			if ($contents[$i]["id"] == $id && $i==count($contents)-1)
			{
				return true ;
			}
			elseif ($contents[$i]["id"] == $id)
			{
				$this->updateData($this->getPrefix()."site_struct_blocks", 
					array(
							"site_struct_id"=>$contents[$i]["site_struct_id"],
							"type"=>$contents[$i]["type"],
							"name"=>$contents[$i]["name"],
							"content"=>$contents[$i]["content"],
						), 
					array(
							"id"=>$contents[$i+1]["id"]
						)
					);
				$this->updateData($this->getPrefix()."site_struct_blocks", 
					array(
							"site_struct_id"=>$contents[$i+1]["site_struct_id"],
							"type"=>$contents[$i+1]["type"],
							"name"=>$contents[$i+1]["name"],
							"content"=>$contents[$i+1]["content"],
						), 
					array(
							"id"=>$contents[$i]["id"]
						)
					);
			}
		}
		
		return true ;
	}
	
	//возвращает запись контент блока
	public function getContentBlock($id)
	{
		$block = $this->query("SELECT * FROM `".$this->getPrefix()."site_struct_blocks` WHERE `id`='".$this->strCleaner($id)."'");
		return $this->arrayDecode($block[0]);
	}
	
	//обновляет содержимое контент-блока
	public function editContentBlock($block, $data)
	{
		if ($block["type"] == "text")
		{
			$this->updateData($this->getPrefix()."site_struct_blocks", array("content"=>$this->strCleaner($data["content"])), array("id"=>$block["id"]));
			return true ;
		}
		
		if ($block["type"] == "module" || $block["type"] == "helper")
		{
			if ($data["params"] == "")
			{
				$name = $data["name"] ;
			}
			else
			{
				$name = $data["name"]."=".$data["params"] ;
			}
			$this->updateData($this->getPrefix()."site_struct_blocks", array("content"=>$this->strCleaner($name)), array("id"=>$block["id"]));
			return true ;
		}
	}
}