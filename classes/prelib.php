<?php
error_reporting(E_ALL);
mb_internal_encoding("UTF-8");
set_time_limit(0);


function getTagValues($html, $startTag, $endTag)
{
	$start = mb_strpos($html, $startTag);
	
	$tmp = array();
	while ($start !== false)
	{
		$end = mb_strpos($html, $endTag, $start+1) ;
		if ($end === false)
		{
			$tmp []= mb_substr($html, $start+mb_strlen($startTag), mb_strlen($html)-$start-mb_strlen($startTag));
			break;
		}
		else
		{
			$tmp []= mb_substr($html, $start+mb_strlen($startTag), $end-$start-mb_strlen($startTag));
		}
		
		$start = mb_strpos($html, $startTag, $end) ;
	}
	
	return $tmp ;
}

function getPage($url)
{
	//возьмём список доступных объявлений
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	$data = curl_exec($ch);
	curl_close($ch);
	
	return $data;
}

function checkEmail($email)
{
	$email = ''; //входящая строка, в которой может быть все, что угодно, а должна быть почта
	if (preg_match("/^(?:[a-z0-9]+(?:[-_.]?[a-z0-9]+)?@[a-z0-9_.-]+(?:\.?[a-z0-9]+)?\.[a-z]{2,5})$/i", $email))
	{
	  return true;
	}
	else
	{
	  return false;
	}
}