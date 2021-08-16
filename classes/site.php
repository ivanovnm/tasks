<?php

class c_site extends c_pdo
{
	//установка недостающих табличек
	private function install()
	{
		$this->query("CREATE TABLE IF NOT EXISTS `".$this->getPrefix()."site_struct` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `parent_id` INT NOT NULL DEFAULT '1',
		  `url` VARCHAR(1024),
		  `system` INT NOT NULL DEFAULT '0',
		  `params` INT NOT NULL DEFAULT '0',
		  `order` INT NOT NULL DEFAULT '999999',
		  `template_id` INT NOT NULL DEFAULT '1',
		  `tag_title` VARCHAR(1024),
		  `tag_keywords` VARCHAR(2048),
		  `tag_description` VARCHAR(2048),
		  `menu_name` VARCHAR (32),
		  `name` VARCHAR(64),
		  `description` VARCHAR(256),
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
		
		$this->query("INSERT INTO `".$this->getPrefix()."site_struct` 
			(
				`id`, 
				`parent_id`, 
				`url`, 
				`order`, 
				`template_id`, 
				`tag_title`, 
				`tag_keywords`,
				`tag_description`,
				`menu_name`,
				`name`,
				`description`
			) 
			VALUES 
			(
				'1',
				'0',
				'',
				'1',
				'1',
				'".$this->strCleaner("Главная страница")."',
				'".$this->strCleaner("Ключевые,слова,или,фразы,через,запятую")."',
				'".$this->strCleaner("Описание главной страницы для поисковых систем")."',
				'".$this->strCleaner("Главная")."',
				'".$this->strCleaner("Главная страница")."',
				'".$this->strCleaner("Описание страницы для себя, что бы понимать, что эта за страница. могут размещаться разного рода комментарии, что бы не запутаться.")."'
			)");
			
		$this->query("CREATE TABLE IF NOT EXISTS `".$this->getPrefix()."site_templates` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `menu_name` VARCHAR (32),
		  `name` VARCHAR(64),
		  `description` VARCHAR(256),
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
		
		$this->query("INSERT INTO `".$this->getPrefix()."site_templates` (`id`, `menu_name`, `name`, `description`) VALUES (
		'1',
		'".$this->strCleaner("Основной")."',
		'".$this->strCleaner("Основной шаблон")."',
		'".$this->strCleaner("Шаблон отображения страниц - по умолчанию")."'
		)");
		
		$template = "<html><headr><title>{block:title=заголовок страницы}</title></head><body>{block:content=Основной контент страницы}{block:simple}</body></html>" ;
		file_put_contents($_SERVER["DOCUMENT_ROOT"]."/templates/template_1.html", $this->strCleaner($template));
		
		$this->query("CREATE TABLE IF NOT EXISTS `".$this->getPrefix()."site_struct_blocks` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `site_struct_id` int(11) DEFAULT '0',
		  `name` VARCHAR(64),
		  `type` VARCHAR(64),
		  `content` LONGTEXT,
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
	}
	
	public function execute()
	{
		//проверка существования нужных нам табличек
		if ($this->existsTable ($this->getPrefix()."site_struct") === false)
		{
			$this->install();
		}
		
		//получим url
		$url = clearGetEx();
		
		if (!is_array($url))
		{
			$url[0] = "";
		}
		
		//проверим запрос на параметры
		$param_str = array();
		if (strpos($url[count($url)-1], ".html") !== false)
		{
			$params = $url[count($url)-1];
			unset($url[count($url)-1]);
			
			$tmp = explode (".", $params) ;
			if ($tmp[1] != "html")
			{
				error404();
				exit();
			}
			
			$param_str = explode ("_", $tmp[0]);
		}
		
		
		//запросим из БД все url
		$chars = "bcdefghijklna" ;
			
		$select = "a.*" ;
		$tables = "`".$this->getPrefix()."site_struct` as a" ;
		$where = "a.`url`='' AND a.`parent_id`='0'" ;
		
		if (isset($url[0]) && $url[0] != "")
		{
			for ($i=0;$i<count($url);$i++)
			{
				if ($i>10) //специальный ограничитель по глубине сайта 10 - вложений!!!!
				{
					break;
					error404();
					exit();
				}
				
				$select .= ", ".substr($chars,$i,1).".*" ;
				$tables .= ", `".$this->getPrefix()."site_struct` as ".substr($chars,$i,1) ;
				$where .= " AND (".substr($chars,$i,1).".`url`='".$this->strCleaner($url[$i])."' AND ".substr($chars,$i,1).".`parent_id`=".substr($chars,$i-1,1).".`id`)" ;
			}
		}
		
		$structs = $this->query("SELECT ".$select." FROM ".$tables." WHERE ".$where) ;
		
		//отпарсим параметры
		if (is_array($param_str) && count($param_str)>0)
		{
			$prm = $this->query("SELECT * FROM `".$this->getPrefix()."site_struct_params` WHERE `site_struct_id`='".$structs[0]["id"]."' ORDER BY `order` ASC") ;
			if (!is_array($prm))
			{
				error404();
				exit();
			}
			
			for ($i=0;$i<count($prm);$i++)
			{
				if (!isset($param_str[$i]))
				{
					$param_str[$i] = "";
				}
				else
				{
					$_GET[$this->strDecode($prm[$i]["name"])] = $param_str[$i] ;
				}
			}
		}
		
		//print_r($structs);
		//print_r($url);
		
		if (!is_array($structs)) //если структура не найдена, то 404 ошибка
		{
			error404();
			exit();
		}
		
		//если всё-таки сайт обнаружен, то загрузим тогда еще и информацию о блоках в шаблоне
		if (file_exists ($_SERVER["DOCUMENT_ROOT"]."/templates/template_".$structs[0]["template_id"].".html") === false)
		{
			print ">>Не найден шаблон страницы: ".$_SERVER["DOCUMENT_ROOT"]."/templates/template_".$structs[0]["template_id"].".html";
			exit();
		}
		
		$template = $this->strDecode(file_get_contents($_SERVER["DOCUMENT_ROOT"]."/templates/template_".$structs[0]["template_id"].".html")) ;
		
		//вытащим информацию о модулях
		$start = 0 ;
		$modules = array();
		$start = strpos ($template, "{module", $start);
		while ($start !== false)
		{
			$start = strpos ($template, "{module", $start);
			
			if ($start !== false)
			{
				$tmp = strpos ($template, "}", $start+1);
				if ($tmp === false) //если нарушен синтаксис и нет закрывающей кавычки
				{
					break;
				}
				
				$b_name = explode(":", substr($template, $start, $tmp-$start)) ;
				if (!isset($b_name[1]) || $b_name[1] == "")
				{
					continue ;
				}
				
				$b_name = explode("=", $b_name[1]);
				if (!isset($b_name[1]))
				{
					$b_name[1] = "" ;
				}
				
				$modules[] = array("0"=>$b_name[0], "1"=>$b_name[1]) ;
				
				$start = $tmp ;
			}
		}
		
		//пройдемся по модулям
		for ($m=0;$m<count($modules);$m++)
		{
			$key = $modules[$m]["0"] ;
			$value = $modules[$m]["1"] ;
			
			if (file_exists($_SERVER["DOCUMENT_ROOT"]."/modules/".$key."/controller.php") === false) //если модуль не найден физически
			{
				break;
			}
			
			$method = "__default" ;
			
			//определим метод если он не по умолчанию
			if ($value != "")
			{
				$method = $value ;
			}
			
			$model = "";
			$view = "" ;
			
			//проверим существование модели и вьювера
			if (file_exists($_SERVER["DOCUMENT_ROOT"]."/modules/".$key."/model.php") === false || file_exists($_SERVER["DOCUMENT_ROOT"]."/modules/".$key."/view.php") === false)
			{
				if ($method=="__default")
				{
					$template = str_replace("{module:".$key."}", "не найдена модель или вьювер!", $tempalte);
				}
				else
				{
					$template = str_replace("{module:".$key."=".$value."}", "не найдена модель или вьювер!", $template);
				}
				
				continue ;
			}
			
			//подключим контроллер, модель и вьювер
			require_once ($_SERVER["DOCUMENT_ROOT"]."/modules/".$key."/controller.php") ;
			require_once ($_SERVER["DOCUMENT_ROOT"]."/modules/".$key."/model.php") ;
			require_once ($_SERVER["DOCUMENT_ROOT"]."/modules/".$key."/view.php") ;
			
			//создадим модель, вьювер и передадим это контроллеру
			$model_name = $key."_model" ;
			$model = new $model_name();
			
			$view_name = $key."_view" ;
			$view = new $view_name();
			
			$controller = new $key($model, $view);
			$template = str_replace("{module:".$key."=".$value."}", $controller->$method(), $template);
			
			unset($view);
			unset($model);
			unset($controller);
		}
		
		//вытащим информацию о блоках
		$start = 0 ;
		$blocks = array();
		$start = strpos ($template, "{block", $start);
		while ($start !== false)
		{
			$start = strpos ($template, "{block", $start);
			if ($start !== false)
			{
				$tmp = strpos ($template, "}", $start+1);
				if ($tmp === false) //если нарушен синтаксис и нет закрывающей кавычки
				{
					break;
				}
				
				$b_name = explode(":", substr($template, $start, $tmp-$start)) ;
				if (!isset($b_name[1]) || $b_name[1] == "")
				{
					$start = $tmp ;
					continue ;
				}
				
				$b_name = explode("=", $b_name[1]);
				if (!isset($b_name[1]))
				{
					$b_name[1] = "" ;
				}
				
				$blocks[$b_name[0]] = $b_name[1] ;
				
				$start = $tmp ;
			}
		}
		
		//составим список блоков
		foreach($blocks as $key=>$value)
		{
			$block = "" ;
			$content = $this->query("SELECT * FROM `".$this->getPrefix()."site_struct_blocks` WHERE `site_struct_id`='".$structs[0]["id"]."' AND `name`='".$this->strCleaner($key)."' ORDER BY `id` ASC");
			if (is_array($content))
			{
				for ($i=0;$i<count($content);$i++)
				{
					switch ($content[$i]["type"])
					{
						case "text":
							$block .= $this->strDecode($content[$i]["content"]) ;
							
						break;
						case "module":
							//проверим, передан ли метод для модуля
							$tmp = $this->strDecode($content[$i]["content"]) ;
							if (strpos($tmp, "=") !== false)
							{
								$tmp = explode ("=", $tmp);
							}
							else
							{
								$tmp = array("0"=>$tmp);
							}
							//проверим существование модуля
							if (file_exists($_SERVER["DOCUMENT_ROOT"]."/modules/".$tmp[0]."/controller.php") === false)
							{
								//print_r($tmp);
								$block .= "не найден контроллер модуля ".$tmp[0]."!";
								break;
							}
							
							$model = "" ;
							$view = "" ;
							
							//проверим существование MODEL, и если есть она - загрузим её
							if (file_exists($_SERVER["DOCUMENT_ROOT"]."/modules/".$tmp[0]."/model.php") !== false)
							{
								require_once($_SERVER["DOCUMENT_ROOT"]."/modules/".$tmp[0]."/model.php");
								$model_name = $tmp[0]."_model" ;
								$model = new $model_name();
							}
							
							//проверим существование VIEW, и если он есть - загрузим его
							if (file_exists($_SERVER["DOCUMENT_ROOT"]."/modules/".$tmp[0]."/view.php") !== false)
							{
								require_once($_SERVER["DOCUMENT_ROOT"]."/modules/".$tmp[0]."/view.php");
								$view_name = $tmp[0]."_view" ;
								$view = new $view_name();
							}							

							//создадим контроллер и передадим ему вьювер и модель
							require_once ($_SERVER["DOCUMENT_ROOT"]."/modules/".$tmp[0]."/controller.php") ;
							$c_name = $tmp[0];
							$controller = new $c_name($model, $view);
							
							if (isset($tmp[1]) && $tmp[1] != "")
							{
								$method = $tmp[1] ;
								
								$params = "";
								if (strpos($method, "(") !== false)
								{
									$t = explode ("(",$method) ;
									$method = str_replace(" ", "", $t[0]) ;
									$params = str_replace(")", "", $t[1]) ;
								}
								
								
								$block .= $controller->$method($params);
							}
							else
							{
								$block .= $controller->__default();
							}
							unset($view);
							unset($model);
							unset($controller);
							
						break;
						case "helper":
							if (file_exists($_SERVER["DOCUMENT_ROOT"]."/helpers/".$this->strDecode($content[$i]["content"]).".php") === false)
							{
								$block .= "не найден helper: ".$this->strDecode($content[$i]["content"])."!";
							}
							else
							{
								require_once ($_SERVER["DOCUMENT_ROOT"]."/helpers/".$this->strDecode($content[$i]["content"]).".php");
								$h_name = $this->strDecode($content[$i]["content"]) ;
								$helper = new $h_name();
								$block .= $helper->execute();
								unset($helper);
							}
						break;
					}
				}
			}
			
			//отреплейсим их в шаблоне при выводе
			if ($value == "")
			{
				$template = str_replace ("{block:".$key."=}", $block, $template);
				$template = str_replace ("{block:".$key."}", $block, $template);
			}
			else
			{
				$template = str_replace ("{block:".$key."=".$value."}", $block, $template);
			}
		}
		
		//вытащим информацию о хэлперах
		$start = 0 ;
		$helpers = array();
		$start = strpos ($template, "{helper", $start);
		while ($start !== false)
		{
			$start = strpos ($template, "{helper", $start);
			if ($start !== false)
			{
				$tmp = strpos ($template, "}", $start+1);
				if ($tmp === false) //если нарушен синтаксис и нет закрывающей кавычки
				{
					break;
				}
				
				$b_name = explode(":", substr($template, $start, $tmp-$start)) ;
				if (!isset($b_name[1]) || $b_name[1] == "")
				{
					$start = $tmp ;
					continue ;
				}
				
				$b_name = explode("=", $b_name[1]);
				if (!isset($b_name[1]))
				{
					$b_name[1] = "" ;
				}
				
				$helpers[] = array($b_name[0]=>$b_name[1]) ;
				
				$start = $tmp ;
			}
		}
		
		/*foreach($helpers as $key=>$value)
		{
			if (file_exists($_SERVER["DOCUMENT_ROOT"]."/helpers/".$key.".php") !== false)
			{
				require_once($_SERVER["DOCUMENT_ROOT"]."/helpers/".$key.".php");
				$helper = new $key();
				
				if ($value == "")
				{
					
					$template = str_replace("{helper:".$key."}", $helper->__default(), $template);
				}
				else
				{
					$template = str_replace("{helper:".$key."=".$value."}", $helper->$value(), $template);
				}
			}
			else
			{
				
				if ($value == "")
				{
					$template = str_replace("{helper:".$key."}", "не найден обработчик ".$key, $template);
				}
				else
				{
					$template = str_replace("{helper:".$key."=".$value."}", "не найден обработчик ".$key."->".$value."()", $template);
				}
			}
		}*/
		
		for($i=0;$i<count($helpers);$i++)
		{
			foreach($helpers[$i] as $key=>$value)
			{
				if (file_exists($_SERVER["DOCUMENT_ROOT"]."/helpers/".$key.".php") !== false)
				{
					require_once($_SERVER["DOCUMENT_ROOT"]."/helpers/".$key.".php");
					$helper = new $key();
					
					if ($value == "")
					{
						
						$template = str_replace("{helper:".$key."}", $helper->__default(), $template);
					}
					else
					{
						$template = str_replace("{helper:".$key."=".$value."}", $helper->$value(), $template);
					}
				}
				else
				{
					
					if ($value == "")
					{
						$template = str_replace("{helper:".$key."}", "не найден обработчик ".$key, $template);
					}
					else
					{
						$template = str_replace("{helper:".$key."=".$value."}", "не найден обработчик ".$key."->".$value."()", $template);
					}
				}
			}
		}
		//print_r($helpers);
		
		print $template ;
	}
}