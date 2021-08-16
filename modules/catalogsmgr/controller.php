<?php

class catalogsmgr extends c_controller
{
	public function title()
	{
		return "title method";
	}
	
	public function __default($data="")
	{
		$data = $this->model->__default() ;	
		$view = $this->view->__default($data) ;
		
		if (!isset($_GET["catalog"])) //если не выбран каталог - покажим список каталогов
		{
			
		}
		
		
		return $view;
	}
	
}