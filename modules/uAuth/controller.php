<?php

class uAuth extends c_controller
{
	public function title()
	{
		return "title method";
	}
	
	public function headerText()
	{
		if (isset($_GET["enter"]))
		{
			return "Вход";
		}
		
		if (isset($_GET["registration"]))
		{
			return "Регистрация";
		}
		
		if (isset($_GET["recover"]))
		{
			return "Восстановление доступа в личный кабинет";
		}
		
		return "ok";
	}
	
	public function __default()
	{
		if (isset($_GET["registration"]))
		{
			$data = $this->model->registration();
			$view = $this->view->registration($data);
			return $view ;
		}
		
		if (isset($_GET["enter"]))
		{
			$data = $this->model->enter();
			$view = $this->view->enter($data);
			return $view ;
		}
		
		if (isset($_GET["exit"]))
		{
			$data = $this->model->logout();
			return $view ;
		}
		
		if (isset($_GET["recover"]))
		{
			$data = $this->model->recover();
			$view = $this->view->recover($data);
			return $view ;
		}
		
		if (isset($_GET["confirm"]))
		{
			$data = $this->model->confirm();
			return true ;
		}
		
		header("Location: /userauth/?enter");
		return "zdes";
	}
	
}