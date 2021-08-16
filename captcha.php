<?php 
session_start();
// Выставляем уровень обработки ошибок 
//error_reporting(E_ALL & ~E_NOTICE);
// Ширина изображения
$width = 81;
// Высота изображения
$height = 27;
// Количество символов в коде
$sign = 4;
// Защитный код
$code = "";
  
// Символы, используемые в коде
$letters = array('0','1','2','3','4','5','6','7','8','9');
// Компоненты для RGB-цвета
$figures = array('50','70','90','110',
			   '130','150','170','190','210');
	  
// Создаем пустое изображение
$img = imagecreatetruecolor($width, $height);
	  
// Заливаем фон белым цветом
$fon = imagecolorallocate($img, 255, 255, 255);
imagefill($img, 0, 0, $fon);
	 
// Накладываем защитный код
for($i=0; $i<$sign; $i++)
{
	//Ориентир
	$h = 1;
	//Рисуем
	$color = imagecolorallocatealpha(
					$img,
					$figures[rand(0,count($figures)-1)],
					$figures[rand(0,count($figures)-1)],
					$figures[rand(0,count($figures)-1)],
					rand(10,30)); 
	// Генерируем случайный символ
	$letter = $letters[rand(0,sizeof($letters)-1)];
	// Формируем координаты для вывода символа
	if(empty($x)) {$x = $width*0.08;}
	else {$x = $x + ($width*0.8)/$sign+rand(0,$width*0.01);}
	if($h == rand(1,2)) {$y = (($height*1)/4) + rand(0,$height*0.1);}
	else{ $y = (($height*1)/4) - rand(0,$height*0.1);}

	// Запоминаем символ в переменной $code
	$code .= $letter;
	// Изменяем регистр символа
	/*if($h == rand(0,1)) $letter = strtoupper($letter);*/
	// Выводим символ на изображение
	imagestring($img, 6 ,$x, $y, $letter, $color);
}

// Помещаем защитный код в сессию
$_SESSION['captcha_cod'] = $code;

/*$fp = fopen("test.php", "a");
fputs($fp, "[".$_SESSION['captcha_cod']."]");
fclose($fp);*/
	 
// Выводим изображение
header ("Content-type: image/jpeg"); 
return imagejpeg($img);