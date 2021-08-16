<?php

class adminauth extends c_controller
{
	//менюшка администрирования
	public function menu()
	{
		if (!isset($_SESSION["asid"]) || $_SESSION["asid"] == "")
		{
			header ("Location: ../../../admin/auth");
			exit();
		}
		
		$res = $this->model->adminInfo($_SESSION["asid"]);
		if (!is_array($res))
		{
			unset($_SESSION["asid"]);
			header("Location: ../../../admin/auth");
			exit();
		}
		
		/*$res = $this->model->adminInfo($_SESSION["asid"]);
		print_r($this->model);*/
		
		return $this->view->menu(array("data"=>""));
	}
	
	//авторизация администраторов
	public function auth()
	{
		//если нет переменной в сесси - точно не авторизованы
		if (!isset($_SESSION["asid"]) && !isset($_POST["login"]) && !isset($_POST["password"]) && !isset($_POST["captcha_code"]))
		{
			return $this->view->auth(array("error"=>""));
		}
		elseif (isset($_SESSION["asid"]) && !isset($_POST["login"]) && !isset($_POST["password"]) && !isset($_POST["captcha_code"]))
		{
			$res = $this->model->adminInfo($_SESSION["asid"]);
			if (is_array($res))
			{
				header("Location: ../../../admin/sitestruct");
				exit();
			}
			else
			{
				unset($_SESSION["asid"]);
				return $this->view->auth(array("error"=>""));
			}
		}
		elseif (isset($_POST["login"]) && isset($_POST["password"]) && isset($_POST["captcha_code"])) //если есть POST данные - проверим их
		{
			$tmp = $this->model->enterAdmin($_POST["login"], $_POST["password"], $_POST["captcha_code"]);
			if ($tmp === false)
			{
				return $this->view->auth(array("error"=>"неверная пара логин/пароль или не правильно указаны символы с картинки!"));
			}
			else
			{
				header("Location: ../../../admin/sitestruct");
				exit();
			}
		}
		
		return $this->view->auth(array("error"=>""));
	}
	
	//разлогинивание
	public function logoff()
	{
		unset($_SESSION["asid"]);
		header ("Location: ../../../../admin/");
		exit();
	}
	
	//метод по умолчанию
	public function __default()
	{
		
	}
}