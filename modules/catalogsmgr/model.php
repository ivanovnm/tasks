<?php

class catalogsmgr_model extends c_model
{
	function __construct()
	{
		parent::__construct();
		
		//создадим табличку группу каталогов
		$this->query("CREATE TABLE IF NOT EXISTS `".$this->getPrefix()."cat_groups` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `parent_id` INT NOT NULL DEFAULT '0',
		  `uin` VARCHAR(128),
		  `name` VARCHAR(128),
		  `visible` INT NOT NULL DEFAULT '1',
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
		
		$this->query("insert into `".$this->getPrefix()."cat_groups` (`uin`, `name`) VALUES ('main', 'Основная')");
		
		//список каталогов
		$this->query("CREATE TABLE IF NOT EXISTS `".$this->getPrefix()."cat_list` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `group_id` INT NOT NULL DEFAULT '0',
		  `uin` VARCHAR(128),
		  `name` VARCHAR(128),
		  `description` VARCHAR(512),
		  `visible` INT NOT NULL DEFAULT '1',
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
	}
}