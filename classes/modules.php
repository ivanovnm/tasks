<?php

class modules extends c_pdo
{
	public $vars=array();
	public $work_path="";
	
	//загружает шаблон у модуля
	public function loadTemplate($name)
	{
		$content = file_get_contents($this->work_path."/templates/".$name.".html") ;
		if ($content === false)
		{
			return false;
		}
		
		return $this->strDecode($content);
	}
	
	//главный метод класса модуля
	public function execute($param="")
	{
		$tmp = $this->loadTemplate("main") ;
		return $tmp;
	}
}