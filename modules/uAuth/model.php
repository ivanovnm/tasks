<?php

class uAuth_model extends c_model
{
	//регистрация
	public function registration()
	{
		if (isset($_POST["name"]))
		{
			if(!isset($_SESSION["captcha_cod"]) || !isset($_POST["captcha"]))
			{
				$_POST["error"] = "Символы с картинки не совпадают";
				return $_POST;
			}
			
			if(isset($_SESSION["captcha_cod"]) && $_POST["captcha"] != $_SESSION["captcha_cod"])
			{
				$_POST["error"] = "Символы с картинки не совпадают";
				return $_POST;
			}
			
			if($_POST["name"] == "" || $_POST["tel"] == "" || $_POST["email"] == "" )
			{
				$_POST["error"] = "Все поля обязательны для заполнения!";
				return $_POST;
			}
			
			if($_POST["type"] == "0")
			{
				$_POST["error"] = "Все поля обязательны для заполнения!";
				return $_POST;
			}
			
			$res = $this->query("select * from `ao_users` where `email`='".$this->strCleaner($_POST["email"])."'") ;
			
			if(is_array($res) && count($res) >0)
			{
				$_POST["error"] = "Пользователь с таким email уже существует";
				return $_POST;
			}
			
			$this->query("insert into `ao_users` (`name`, `tel`, `password`, `email`, `date_register`, `c_sid`, `type`) values (
			'".$this->strCleaner($_POST["name"])."',
			'".$this->strCleaner($_POST["tel"])."',
			'".$this->strCleaner($_POST["password"])."',
			'".$this->strCleaner($_POST["email"])."',
			'".date("Y-m-d H:i:s")."',
			'".md5($_POST["email"].time())."',
			'".$this->strCleaner($_POST["type"])."'
			)");
			
			$_SESSION["u_sid"] = $this->lastInsertId();
			//print "tut";
			header("Location: ../../../lk/?card");
			exit();
		}
		
		$_POST["error"] = "";
		
		return array($_POST);
	}
	
	//вХод
	public function enter()
	{
		if (isset($_SESSION["u_sid"]) && $_SESSION["u_sid"] != "")
		{
			$res = $this->query("select * from `ao_users` where `id`='".$this->strCleaner($_SESSION["u_sid"])."' and `active`='1'");
			if (is_array($res) && count($res) >0)
			{
				header("location: ../../../../lk/");
				exit();
			}
			else
			{
				unset($_SESSION["u_sid"]);
			}
		}
		
		if ((isset($_POST["email"]) || isset($_POST["password"])) && ($_POST["email"] == "" || $_POST["password"] == ""))
		{
			$_POST["error"] = "Заполните все поля!";
			return $_POST;
		}
		
		if (isset($_POST["email"]))
		{
			if(isset($_SESSION["captcha_cod"]) && $_POST["captcha"] != $_SESSION["captcha_cod"])
			{
				$_POST["error"] = "Символы с картинки не совпадают";
				return $_POST;
			}
			
			$res = $this->query("select * from `ao_users` where `email`='".$this->strCleaner($_POST["email"])."' AND `password`='".$this->strCleaner($_POST["password"])."'");
			
			if (!is_array($res) || count($res) == 0)
			{
				$_POST["error"] = "email и пароль указаны не верно";
				return $_POST;
			}
			
			$_SESSION["u_sid"] = $res[0]["id"];
			$this->query("update set `ao_users` `date_enter`='".date("Y-m-d H:i:s")."' where `id`='".$res[0]["id"]."'");
			header("Location: /lk/");
			exit();
		}
		
		$_POST["error"] = "";
		return array($_POST);
	}
	
	//дефолтник
	public function __default($param="")
	{
		if (isset($_SESSION["u_sid"]))
		{
			$res = $this->query("select * from `ao_users` where `id`='".$this->strCleaner($_SESSION["u_sid"])."' and `active`='1'");
			if (is_array($res) && count($res)>0)
			{
				header("Location: /lk/");
			}
			else
			{
				unset($_SESSION["u_sid"]);
				header("Location: /userauth/?enter");
			}
		}
		else
		{
			header("Location: /userauth/?enter");
		}
	}
	
	//выход
	public function logout()
	{
		unset($_SESSION["u_sid"]);
		header("Location: ../../../../");
		exit();
	}
	
	//восстановление
	public function recover()
	{
		$data = array("error"=>"");
		
		if (isset($_POST["email"]))
		{
			
			if ($_POST["email"] == "")
			{
				$data["error"] = "Укажите адрес своей электронной почты!";
				return $data;
			}
			
			if (filter_var($_POST["email"], FILTER_VALIDATE_EMAIL) === false)
			{
				//print_r($_POST);
				$data["error"] = "Адрес электронной почты указан не корректно";
				return $data;
			}
			
			if(!isset($_SESSION["captcha_cod"]) || !isset($_POST["captcha"]))
			{
				$data["error"] = "Символы с картинки не совпадают";
				return $data;
			}
			
			if ($_POST["captcha"] !== $_SESSION["captcha_cod"])
			{
				$data["error"] = "Символы с картинки не совпадают";
				return $data;
			}
			
			$res = $this->query("select * from `ao_users` where `email`='".$this->strCleaner($_POST["email"])."'");
			if (!is_array($res) || count($res)==0)
			{
				$data["error"] = "Символы с картинки не совпадают";
				return $data;
			}
			
			$sid = md5($res[0]["email"].time());
			
			$this->query("update `ao_users` set `c_sid`='".$sid."' where `id`='".$res[0]["id"]."'");
			
			$to  = $this->strDecode($res[0]["email"]);
			$subject = "Восстановление доступа в личный кабинет сайта ВКВАДРАТЕ-ЖКХ"; 

			$message = "<p>Здравствуйте!</p><p>Для восстановления доступа в личный перейдите по <a href='http://".$_SERVER["SERVER_NAME"]."/userauth/?confirm&sid=".$sid."'>этой ссылке</a> и в личном кабинете, в настройках, задайте новый пароль. Если вы не восстанавливали доступи не хотите менять пароль, не переходите по ссылке.</p>";

			$headers  = "Content-type: text/html; charset=utf-8 \r\n"; 
			$headers .= "From: Робот ВКВАДРАТЕ-ЖКХ <tech@vkvadrate.store>\r\n"; 
			$headers .= "Reply-To: tech@vkvadrate.store\r\n"; 

			mail($to, $subject, $message, $headers);
			
			$data["error"] = "НА УКАЗАННЫЙ ПОЧТОВЫЙ ЯЩИК ОТПРАВЛЕНО ПИСЬМО С ИНСТРУКЦИЕЙ ДЛЯ ВОССТАНОВЛЕНИЯ";
			return $data;
		}
		
		return $data;
	}
	
	function confirm()
	{
		if(!isset($_GET["sid"]) || $_GET["sid"] == "")
		{
			print "Не верно указан ключ для восстановления!
			<script type=\"text/javascript\">
			setTimeout('location.replace(\"http://".$_SERVER["SERVER_NAME"]."\")',3000);
			</script>
			";
			exit();
		}
		
		$res = $this->query("select * from `ao_users` where `c_sid`='".$this->strCleaner($_GET["sid"])."'");
		
		if(!is_array($res) || count($res) == 0)
		{
			print "Не верно указан ключ для восстановления!
			<script type=\"text/javascript\">
			setTimeout('location.replace(\"http://".$_SERVER["SERVER_NAME"]."\")',3000);
			</script>
			";
			exit();
		}
		
		$_SESSION["u_sid"] = $res[0]["id"] ;
		header("Location: http://".$_SERVER["SERVER_NAME"]."/lk/?settings");
		exit();
	}
}