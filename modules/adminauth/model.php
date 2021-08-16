<?php

class adminauth_model extends c_model
{
	//установка таблиц для администраторов
	private function installAdminsTable()
	{
		$this->query("CREATE TABLE IF NOT EXISTS `".$this->getPrefix()."admins` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `login` VARCHAR(32),
		  `password` VARCHAR(32),
		  `sid` VARCHAR(32),
		  `fio` VARCHAR(128),
		  `active` INT NOT NULL DEFAULT'0',
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
		
		$this->query("INSERT INTO `".$this->getPrefix()."admins` VALUES (`login`,`password`,`fio`,`active`) VALUES (
		'".md5("admin")."',
		'".md5("admin")."',
		'Администратор',
		'1')");
	}
	
	//проверка авторизации
	public function adminInfo($sid)
	{
		if ($this->existsTable($this->getPrefix()."admins") === false)
		{
			$this->installAdminsTable();
		}
		
		$res = $this->query("SELECT * FROM `".$this->getPrefix()."admins` WHERE `sid`='".$this->strCleaner($sid)."' AND `active`='1'");
		if (!is_array($res))
		{
			return false ;
		}
		
		return $res[0];
	}
	
	//вход для администраторов
	public function enterAdmin($login, $password, $captcha)
	{
		if ($_SESSION["captcha_cod"] != $captcha)
		{
			//print "1";
			return false ;
		}
		
		$res = $this->query("SELECT * FROM `".$this->getPrefix()."admins` WHERE `login`='".md5($login)."' AND `password`='".md5($password)."' AND `active`='1'");
		if (!is_array($res))
		{
			//print "SELECT * FROM `".$this->getPrefix()."admins` WHERE `login`='".md5($login)."' AND `password`='".md5($password)."' AND `active`='1'";
			return false ;
		}
		
		$asid = md5($login.$password.time());
		$_SESSION["asid"] = $asid ;
		
		$this->updateData ($this->getPrefix()."admins", array("sid"=>$asid), array("id"=>$res[0]["id"]));
		
		return true;
	}
	
	
}