<?php

class c_controller
{
	public $model = null ;
	public $view = null ;
	
	function __construct($model="",$view="")
	{
		$this->model = $model ;
		$this->view = $view ;
	}
	
	public function __default()
	{
		$method_name = explode (":", __METHOD__) ;
		$method_name = $method_name[count($method_name)-1];
		
		//���� ���� ������, ��������� �� ������
		if ($this->model != null)
		{
			$array = array("get"=>$_GET, "post"=>$_POST) ;
			$data = $this->model->$method_name($array) ;
		}
		else
		{
			$data = "" ;
		}
		
		//���� ���� �����������, ��������� ������
		if ($this->view != null)
		{
			$array = array("get"=>$_GET, "post"=>$_POST, "data"=>$data) ;
			$view = $this->view->$method_name($array) ;
		}
		else
		{
			$view = "" ;
		}
		
		return $view;
	}
}