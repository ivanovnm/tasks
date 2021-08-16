<?php

function clearGet()
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

function clearGetEx()
{
	$url = clearGet();
	return explode("/", $url);
}

function error404()
{
	header("HTTP/1.0 404 Not Found");
	header("Status: 404 Not Found");
	print file_get_contents($_SERVER["DOCUMENT_ROOT"]."/templates/404.html");
	exit();
}

function applyData($view, $data)
{
	
}

function ru2Lat($string)
{
	$rus = array('ё','ж','ц','ч','ш','щ','ю','я','Ё','Ж','Ц','Ч','Ш','Щ','Ю','Я', 'Ъ', 'ъ', 'ь', 'Ь', ' ', '.', ',');
	$lat = array('yo','zh','tc','ch','sh','sh','yu','ya','YO','ZH','TC','CH','SH','SH','YU','YA', '', '', '', '', '-', '-', '-');
	$string = str_replace($rus,$lat,$string);
	$string = strtr($string,
		 "АБВГДЕЗИЙКЛМНОПРСТУФХЫЭабвгдезийклмнопрстуфхыэ",
		 "ABVGDEZIJKLMNOPRSTUFHIEabvgdezijklmnoprstufhie");
	  
	return($string);
}

function toAscii($str, $replace=array(), $delimiter='-') {
	setlocale(LC_ALL, 'en_US.UTF8');
	if( !empty($replace) ) {
		$str = str_replace((array)$replace, ' ', $str);
	}

	$clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
	$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
	$clean = strtolower(trim($clean, '-'));
	$clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);

	return $clean;
}
