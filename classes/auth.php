<?php

class c_auth extends c_pdo
{

	//установка нужных табличек
	private function installTable()
	{
		$this->query("CREATE TABLE `".$this->getPrefix()."admins` (
		`id` INT(11) NOT NULL AUTO_INCREMENT ,
		`login` VARCHAR(32),
		`password` VARCHAR(32),
		`name` VARCHAR(256),
		`email` VARCHAR(256),
		`sid` VARCHAR(32),
		`active` INT NOT NULL DEFAULT '1',
		PRIMARY KEY  (`id`),
		KEY `id` (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
		
		$this->query("INSERT INTO `".$this->getPrefix()."admins` (`login`, `password`, `name`, `email`) VALUES ('".md5("admin")."', '".md5("aA1952183")."', 'Администратор', 'kiesoft@yandex.ru')");
	}
	
	//проверка авторизации администратора сайта
	public function isAdmin($info=false)
	{
		if ($this->existsTable ($this->getPrefix()."admins") === false)
		{
			$this->installTable();
		}
		
		if (!isset($_SESSION["isAdmin"]))
		{
			return false;
		}
		
		$res = $this->query("SELECT * FROM `".$this->getPrefix()."admins` WHERE `sid`='".$this->strCleaner($_SESSION["isAdmin"])."' AND `active`='1'") ;
		if (!is_array($res) || count($res)==0)
		{
			return false ;
		}
		
		if ($info == false)
		{
			return true;
		}
		else
		{
			return $res[0];
		}
	}
}