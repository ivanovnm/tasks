<?php

class c_urlmanager extends c_pdo
{

	
	/*------------------- PRIVATE METHODS -------------------*/
	private function clearGet()
	{
		$url = explode ("?", $_SERVER["REQUEST_URI"]) ;
		$url = $url[0];
		$url = str_replace ("\\", "/", $url);
		while (strpos($url, "//") !== false)
		{
			$url = str_replace ("//", "/", $url);
		}
		return trim($url, "/") ;
	}
	
	//установка нужных таблиц
	private function install()
	{
		$this->query("CREATE TABLE `".$this->getPrefix()."sitestruct` (
		`id` INT(11) NOT NULL AUTO_INCREMENT ,
		`url` VARCHAR(2048),
		`hidden` INT NOT NULL DEFAULT '0',
		`params` INT NOT NULL DEFAULT '0',
		`shortname` VARCHAR(128),
		`fullname` VARCHAR(256),
		`title` VARCHAR(1024),
		`keywords` VARCHAR(2048),
		`description` VARCHAR(2048),
		`menuname` VARCHAR(64),
		`template` VARCHAR(128),
		`content` LONGTEXT,
		PRIMARY KEY  (`id`),
		KEY `id` (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
		
		$this->insertData($this->getPrefix()."sitestruct", array(
				"url"=>"",
				"shortname"=>$this->strCleaner("Главная страница"),
				"fullname"=>$this->strCleaner("Главная страница сайта"),
				"title"=>$this->strCleaner("Заголовок главной страницы сайта"),
				"keywords"=>$this->strCleaner("Ключевые слова главной страницы сайта"),
				"description"=>$this->strCleaner("Описание главной страницы сайта"),
				"menuname"=>$this->strCleaner("Главная"),
				"template"=>$this->strCleaner("main"),
				"content"=>$this->strCleaner("Содержимое главной страницы сайта, выводится при заходе на сайт, его корневую страницу")
			)
		);
		
		file_put_contents ($_SERVER["DOCUMENT_ROOT"]."/templates/main.tpl", "<html><head><title>{value:title}</title><keywords>{value:keywords}</keywords><description>{value:description}</description></head><body>{value:content}</body></html>");
	}
	
	//возвращает список переменных
	private function getVars ($template)
	{
		$vars = array();
		$start = strpos($template, "{var:");
		while ($start !== false)
		{
			$end = strpos ($template, "}", $start+1);
			if ($end === false)
			{
				$end = strlen($template);
			}
			
			$vars []= substr($template, $start, $end-$start+1) ;
			
			$start = strpos($template, "{var:", $end);
		}
		
		$out = array();
		for ($i=0;$i<count($vars);$i++)
		{
			$vars[$i] = str_replace ("{", "", $vars[$i]);
			$vars[$i] = str_replace ("}", "", $vars[$i]);
			$tmp = explode (":", $vars[$i]);
			if (isset($tmp[1]) && $tmp[1] != "")
			{
				$out [$tmp[1]] = $tmp[1];
			}
		}
		
		if (is_array($out) && count($out)>0)
		{
			return $out ;
		}
		
		return false;
	}
	
	//возвращает список модулей
	private function getModules($template)
	{
		$vars = array();
		$start = strpos($template, "{module:");
		while ($start !== false)
		{
			$end = strpos ($template, "}", $start+1);
			if ($end === false)
			{
				$end = strlen($template);
			}
			
			$vars []= substr($template, $start, $end-$start+1) ;
			
			$start = strpos($template, "{module:", $end);
		}
		
		$out = array();
		for ($i=0;$i<count($vars);$i++)
		{
			$vars[$i] = str_replace ("{", "", $vars[$i]);
			$vars[$i] = str_replace ("}", "", $vars[$i]);
			$tmp = explode (":", $vars[$i]);
			if (isset($tmp[1]) && $tmp[1] != "")
			{
				$tmp = explode ("=", $tmp[1]);
				if (!isset($tmp[1]))
				{
					$tmp[1] = "" ;
				}
				
				$out [] = array("name"=>$tmp[0], "params"=>$tmp[1]);
			}
		}
		
		if (is_array($out) && count($out)>0)
		{
			return $out ;
		}
		
		return false;	
	}
	
	/*------------------- PUBLIC METHODS -------------------*/
	public function execute()
	{
		//проверка подключения к БД
		if (mysql_errno()>0)
		{
			print "mysql error: ".mysql_error() ;
			exit();
		}
		
		//проверим наличие нужных таблиц
		/*if ($this->existsTable ($this->getPrefix()."sitestruct") === false)
		{
			$this->install();
		}*/
		
		//проверим запрашиваемый URL
		$url = $this->clearGet();
		
		//главная страница
		if ($url == "" || $url == "index.html" || $url == "index.htm")
		{
			$url = "root/index.html" ;
		}
		
		$url = explode ("/", $url);
		
		if (count($url) < 1) //еще одна проверка на главную страницу
		{
			//print "1";
			header ("Location: ../../../../") ;
			exit();
		}
		
		//если запрошена директория, а не конкретный фаил
		if (strpos($url[count($url)-1], ".") === false)
		{
			$url []= "index.html" ;
		}
		
		//если фаил не найден - 404 ошибка
		if (file_exists ($_SERVER["DOCUMENT_ROOT"]."/structs/".implode("/", $url)) === false)
		{
			//404 ощибка
			//print $_SERVER["DOCUMENT_ROOT"]."/structs/".implode("/", $url);
			header("HTTP/1.1 404 Not Found");
			exit();
		}
		
		//определим тип запрашиваемого ресурса
		$ext = explode (".", $url[count($url)-1]);
		$ext = $ext[count($ext)-1];
		
		$content = "" ;
		switch($ext)
		{
			case "jpeg": $header = "image/jpeg";
				break;
			case "jpg": $header = "image/jpeg";
				break;
			case "gif": $header = "image/gif";
				break;
			case "png": $header = "image/png";
				break;
			case "tiff": $header = "image/tiff";
				break;
			case "bmp": $header = "image/vnd.wap.wbmp";
				break;
			case "ico":  $header = "image/vnd.microsoft.icon";
				break;
			case "pdf": $header = "application/pdf";
				break;
			case "zip": $header = "application/zip";
				break;
			case "gz":  $header = "application/x-gzip";
				break;
			case "mp3": $header = "audio/mpeg";
				break;
			case "css": $header = "text/css";
				break;
			case "html": $header = "text/html";
				break;
			case "js": $header = "text/javascript";
				break;
			case "xml": $header = "text/xml";
				break;
			case "flv": $header = "video/x-flv";
				break;
			default : $header = "text/plain";
				break;
		}
		
		//если всё таки подан шаблон - разберем его
		//иначе просто выдача контента
		if ($ext == "html")
		{
			header($header);
			$template = $this->strDecode(file_get_contents($_SERVER["DOCUMENT_ROOT"]."/structs/".implode("/", $url))) ;
			
			//делаем обработку данных
			//...
			
			//загрузим для начала список переменных
			$vars = $this->getVars($template);
			
			$modules = $this->getModules($template);
			$stop_flag = 0 ;
			while (is_array($modules) && count($modules)>0)
			{
				for ($i=0;$i<count($modules);$i++)
				{
					$m_name = $modules[$i]["name"] ;
					$m_param = $modules[$i]["params"] ;
					
					require_once ($_SERVER["DOCUMENT_ROOT"]."/modules/".$m_name."/".$m_name.".php");
					$m = new $m_name ();
					$m->work_path = $_SERVER["DOCUMENT_ROOT"]."/modules/".$m_name ;
					$m->vars = $vars ;
					$m_content = $m->execute($m_param);
					if ($m_param == "")
					{
						$template = str_replace ("{module:".$m_name."}", $m_content, $template);
						$template = str_replace ("{module:".$m_name."=}", $m_content, $template);
					}
					else
					{
						$template = str_replace ("{module:".$m_name."=".$m_param."}", $m_content, $template);
					}
					
					//при обработке вызова модуля, в куске шаблона модуля могут появиться новые
					//переменные - проверм их и добавим в общий список переменных
					$tmp_var = $m->vars ;
					$new_vars = $this->getVars($template) ;
					foreach($new_vars as $key => $value)
					{
						$vars[$key] = $value;
					}
					
				}
				$stop_flag++;
				
				if ($stop_flag > 100)
				{
					print "maximum loop step = 100!";
					break;
				}
				
				$modules = $this->getModules($template) ;
			}
			
			//запишем значение переменных
			if (is_array($vars) && count($vars)>0)
			{
				foreach($vars as $key=>$value)
				{
					$template = str_replace ("{var:".$key."}", $value, $template);
				}
			}
			
			//выводим шаблон
			print $template;
			exit();
		}
		else
		{
			header($header);
			$fp = fopen ($_SERVER["DOCUMENT_ROOT"]."/structs/".implode("/", $url), "rb");
			while (!feof($fp))
			{
				print fgets($fp);
			}
			fclose($fp);
			exit();	
		}
	}
	
}