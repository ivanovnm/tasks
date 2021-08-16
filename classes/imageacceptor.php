<?php

class c_imageacceptor
{
	private $fildname = "" ;
	public $error_code = "0" ;
	
	function __construct($fildname)
	{
		$this->fildname = $fildname ;
	}
	
	function __desctruct()
	{
	}
	
	/*-----------------------------------------------------*/
	//проверка на существование
	private function ifExist($fildname)
	{
		if (file_exists($_FILES[$fildname]["tmp_name"]))
		{
			return true ;
		}
		
		$this->error_code = "1";
		//print "[1]";
		return false ;
	}
	
	//проверка на тип файла
	private function contentType($fildname)
	{
		if ($_FILES[$fildname]["type"] != "image/png" &&
		$_FILES[$fildname]["type"] != "image/jpeg" &&
		$_FILES[$fildname]["type"] != "image/pjpeg")
		{
			$this->error_code = "2";
			//print "[2]";
			return false ;
		}
		
		return true ;
	}
	
	public function imageSize($fildname)
	{
		$imageinfo = getimagesize($_FILES[$fildname]["tmp_name"]);
		return array($imageinfo[0],$imageinfo[1]);
	}
	
	//проверка на внутреннее содержимое
	private function containType($fildname)
	{
		$imageinfo = getimagesize($_FILES[$fildname]["tmp_name"]);
		
		if ($imageinfo["mime"] != "image/jpeg" &&
		$imageinfo["mime"] != "image/png" ) 
		{
			$this->error_code = "3";
			//print "[3]";
			return false ;
		}
		
		return true;
	}
	
	//проверка на расширение файла
	private function extensionType($fildname)
	{
		$blacklist = array(".php", ".phtml", ".php3", ".php4", ".gif", "tiff", ".pl", ".cgi", ".exe", ".js", ".java");
		
		foreach ($blacklist as $item) 
		{
			if(preg_match("/".$item."\$/i", $_FILES[$fildname]["name"])) 
			{
				$this->error_code = "4";
				return false ;
			}
		}
		
		return true;
	}
	
	//возвращает размер файла
	private function fileSize($fildname)
	{
		if (filesize($_FILES[$fildname]["tmp_name"]) > 51200)
		{
			$this->error_code = "4";
			//print "[4]";
			return false ;
		}
		
		return true;
	}
	
	/*-----------------------------------------------------*/
	//проверка на валидность изображения
	public function accept()
	{
		if ($this->ifExist($this->fildname) == false ||
			$this->contentType($this->fildname) == false ||
			$this->containType($this->fildname) == false ||
			$this->extensionType($this->fildname) == false)
		{
			return false ;
		}
		return true;
	}
	
	//масштабируем фото под разные размеры
	public function resizeImage($filename, $prefix)
	{
		$size_img = getimagesize($filename);
		if ($size_img[0] > $size_img[1])
		{
			$x1 = 225;
			$y1 = round(225*$size_img[1]/$size_img[0]);
			
			$x2 = 45;
			$y2 = round(45*$size_img[1]/$size_img[0]);

			$x3 = 90;
			$y3 = round(90*$size_img[1]/$size_img[0]);			
		}
		else
		{
			$x1 = round(225*$size_img[0]/$size_img[1]);
			$y1 = 225;
			
			$x2 = round(45*$size_img[0]/$size_img[1]);
			$y2 = 45;

			$x3 = round(90*$size_img[0]/$size_img[1]);
			$y3 = 90;			
		}
		
		//print $x1.",".$y1.",".$x2.",".$y2."<br>";
		
		$dest_img1 = imagecreatetruecolor($x1, $y1); 
		$dest_img2 = imagecreatetruecolor($x2, $y2); 
		$dest_img3 = imagecreatetruecolor($x3, $y3); 
		
		$white1 = imagecolorallocate($dest_img1, 255, 255, 255);
		$white2 = imagecolorallocate($dest_img2, 255, 255, 255);
		$white3 = imagecolorallocate($dest_img3, 255, 255, 255);
		
		if ($size_img[2]==2)
		{
			$src_img1 = imagecreatefromjpeg($filename);
			$src_img2 = imagecreatefromjpeg($filename);
			$src_img3 = imagecreatefromjpeg($filename);
		}
		elseif ($size_img[2]==3)
		{
			$src_img1 = imagecreatefrompng($filename);
			$src_img2 = imagecreatefrompng($filename);
			$src_img3 = imagecreatefrompng($filename);
		}
		
		imagecopyresampled($dest_img1, $src_img1, 0, 0, 0, 0, $x1, $y1, $size_img[0], $size_img[1]);
		imagecopyresampled ($dest_img2, $src_img2, 0, 0, 0, 0, $x2, $y2, $size_img[0], $size_img[1]);
		imagecopyresampled ($dest_img3, $src_img3, 0, 0, 0, 0, $x3, $y3, $size_img[0], $size_img[1]);
		
		if ($size_img[2]==2)
		{
			imagejpeg($dest_img1, $_SERVER["DOCUMENT_ROOT"]."/_resources/image/".$prefix.".jpeg");
			imagejpeg($dest_img2, $_SERVER["DOCUMENT_ROOT"]."/_resources/image/".$prefix."_small.jpeg", 100);
			imagejpeg($dest_img3, $_SERVER["DOCUMENT_ROOT"]."/_resources/image/".$prefix."_medium.jpeg", 100);
		} 
		elseif ($size_img[2]==3)
		{
			imagepng($dest_img1, $_SERVER["DOCUMENT_ROOT"]."/_resources/image/".$prefix.".png");
			imagepng($dest_img2, $_SERVER["DOCUMENT_ROOT"]."/_resources/image/".$prefix."_small.png");
			imagepng($dest_img3, $_SERVER["DOCUMENT_ROOT"]."/_resources/image/".$prefix."_medium.png");
		}
	}

	public function resizeImageEx($filename, $prefix, $w,$h)
	{
		$size_img = getimagesize($filename);
		
		//print $filename."=".$size_img[0]."=".$size_img[1]."<br>";
		
		$k = $w/$h ;
		$p = $size_img[0]/$size_img[1];
		
		if ($k<$p)
		{
			$y = $size_img[1] ;
			$x = $k*$size_img[1];
		}
		else
		{
			$y = $size_img[0]/$k ;
			$x = $size_img[0];
		}
		
		$dest_img1 = imagecreatetruecolor($w, $h); 
		
		$white1 = imagecolorallocate($dest_img1, 255, 255, 255);

		if ($size_img[2]==1)
		{
			$src_img1 = imagecreatefromgif($filename);
		}		
		elseif ($size_img[2]==2)
		{
			$src_img1 = imagecreatefromjpeg($filename);
		}
		elseif ($size_img[2]==3)
		{
			$src_img1 = imagecreatefrompng($filename);
		}

		imagecopyresampled($dest_img1, $src_img1, 0, 0, round($size_img[0]/2-$x/2), round($size_img[1]/2-$y/2), $w, $h, $x, $y);
		
		if ($size_img[2]==1)
		{
			imagegif($dest_img1, $_SERVER["DOCUMENT_ROOT"]."/images/upload/".$prefix.".jpeg");
		}
		elseif ($size_img[2]==2)
		{
			imagejpeg($dest_img1, $_SERVER["DOCUMENT_ROOT"]."/images/upload/".$prefix.".jpeg", 99);
		} 
		elseif ($size_img[2]==3)
		{
			imagepng($dest_img1, $_SERVER["DOCUMENT_ROOT"]."/images/upload/".$prefix.".jpeg");
		}
	}
	
	public function resizeAvatar($filename, $prefix, $w,$h)
	{
		$size_img = getimagesize($filename);
		//print $filename."=".$size_img[0]."=".$size_img[1]."<br>";
		
		$k = $w/$h ;
		$p = $size_img[0]/$size_img[1];
		
		if ($k<$p)
		{
			$y = $size_img[1] ;
			$x = $k*$size_img[1];
		}
		else
		{
			$y = $size_img[0]/$k ;
			$x = $size_img[0];
		}
		
		$dest_img1 = imagecreatetruecolor($w, $h); 

		$white1 = imagecolorallocate($dest_img1, 255, 255, 255);
		
		if ($size_img[2]==1)
		{
			$src_img1 = imagecreatefromgif($filename);
		}		
		elseif ($size_img[2]==2)
		{
			$src_img1 = imagecreatefromjpeg($filename);
		}
		elseif ($size_img[2]==3)
		{
			$src_img1 = imagecreatefrompng($filename);
		}
		
		imagecopyresampled($dest_img1, $src_img1, 0, 0, round($size_img[0]/2-$x/2), round($size_img[1]/2-$y/2), $w, $h, $x, $y);
		imagejpeg($dest_img1, $_SERVER["DOCUMENT_ROOT"]."/images/avatar/".$prefix.".jpeg", 99);
	}	

	public function copyImage($filename, $prefix)
	{
		$size_img = getimagesize($filename);
		
		$dest_img1 = imagecreatetruecolor($size_img[0], $size_img[1]); 
		
		$white1 = imagecolorallocate($dest_img1, 255, 255, 255);
		
		if ($size_img[2]==2)
		{
			$src_img1 = imagecreatefromjpeg($filename);
		}
		elseif ($size_img[2]==3)
		{
			$src_img1 = imagecreatefrompng($filename);
		}
		
		imagecopyresampled($dest_img1, $src_img1, 0, 0, 0, 0, $size_img[0], $size_img[1], $size_img[0], $size_img[1]);
		
		if ($size_img[2]==2)
		{
			imagejpeg($dest_img1, $_SERVER["DOCUMENT_ROOT"]."/_resources/image/".$prefix.".jpeg", 99);
		} 
		elseif ($size_img[2]==3)
		{
			imagepng($dest_img1, $_SERVER["DOCUMENT_ROOT"]."/_resources/image/".$prefix.".png");
		}
	}
	
	//в другую папку
	public function copyImageEx($filename, $prefix)
	{
		$size_img = getimagesize($filename);
		
		$dest_img1 = imagecreatetruecolor($size_img[0], $size_img[1]); 
		
		$white1 = imagecolorallocate($dest_img1, 255, 255, 255);
		
		if ($size_img[2]==2)
		{
			$src_img1 = imagecreatefromjpeg($filename);
		}
		elseif ($size_img[2]==3)
		{
			$src_img1 = imagecreatefrompng($filename);
		}
		
		imagecopyresampled($dest_img1, $src_img1, 0, 0, 0, 0, $size_img[0], $size_img[1], $size_img[0], $size_img[1]);
		
		if ($size_img[2]==2)
		{
			imagejpeg($dest_img1, $_SERVER["DOCUMENT_ROOT"]."/_images/".$prefix.".jpeg", 99);
		} 
		elseif ($size_img[2]==3)
		{
			imagepng($dest_img1, $_SERVER["DOCUMENT_ROOT"]."/_images/".$prefix.".png");
		}
	}	
	
}