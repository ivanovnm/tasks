<?php

class c_view
{
	//замена меток значениями
	public function assignValues ($html, $data)
	{
		if (!is_array($data))
		{
			return $html ;
		}
		
		foreach ($data as $key=>$value)
		{
			$html = str_replace ("{%".$key."%}", $value, $html);
		}
		
		return $html;
	}
	
	public function __default($param="")
	{
		return "<p></p>";
	}
	
	//грузит шаблон и заменяет в нем метки на значение
	public function loadTemplate($module_name, $template_name, $values)
	{
		if (file_exists($_SERVER["DOCUMENT_ROOT"]."/templates/".$module_name."/".$template_name) === false)
		{
			//print $_SERVER["DOCUMENT_ROOT"]."/templates/".$module_name."/".$template_name."<br/>";
			return false;
		}
		
		$template = file_get_contents($_SERVER["DOCUMENT_ROOT"]."/templates/".$module_name."/".$template_name);
		
		if (!is_array($values) || count($values)==0)
		{
			//print "2";
			return $template ;
		}
		//print "3";
		return $this->assignValues($template, $values);
	}
}