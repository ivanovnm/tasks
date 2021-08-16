<?php

class uAuth_view extends c_view
{
	public function __default($data="")
	{
		return "simplemodule default method";
	}
	
	public function registration($data ="")
	{
		$html = "
		<form name='registration' id='registration' method='post' action='?registration' style='text-align: center!important;'>
			<table border='0' cellspacing='5px' cellpadding='5px' width='600px' style='position: relative; margin-left: 50%; left: -300px;'>
				<tr>
					<td colspan='2' style='color: red'>
						{error}
					</td>
				</tr>
				<tr>
					<td style='text-align: left'>
						Вы заказчик или исполнитель:
					</td>
					<td>
						<select name='type' id='type' class='round'>
							<option value='0'>Выберите ...</option>
							<option value='1'>Заказчик</option>
							<option value='2'>Исполнитель</option>
						</select>
					</td>
				</tr>
				<tr>
					<td style='text-align: left' colspan='2'>
						Контактное лицо:<br/>
						<input type='edit' name='name' id='name' value='' class='round' placeholder='Ф.И.О.'>
					</td>
				</tr>
				<tr>
					<td style='text-align: left' colspan='2'>
						Контактный телефон:<br/>
						<input type='edit' name='tel' id='tel' value='' class='round' placeholder='+7 (000) 000-00-00'>
					</td>
				</tr>
				<tr>
					<td style='text-align: left' colspan='2'>
						Ваша почта:<br/>
						<input type='edit' name='email' id='email' value='' class='round' placeholder='@mail'>
					</td>
				</tr>
				<tr>
					<td style='text-align: left' colspan='2'>
						Придумайте пароль:<br/>
						<input type='edit' name='password' id='password' value='' class='round'>
					</td>
				</tr>
				<tr>
					<td style='text-align: right'>
						<img src='../../../../captcha.php?".time()."'>
					</td>
					<td>
						<input type='edit' name='captcha' id='captcha' value='' class='round' placeholder='Символы с картинки'>
					</td>
				</tr>
				<tr>
					<td colspan='2' style='text-align: left;'>
						<input type='checkbox' name='yes' id='yes'> Поставьте свое согласие на обратобку личных данных
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<i style='font-size: 11pt; color: silver;'>Все поля обязательны для заполнения!</i>
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<input type='submit' value='&#160;Зарегистрироваться&#160;' class='btn_green'>
					</td>
				</tr>
			</table>
		</form>
		";
		
		$html = str_replace("{error}", $_POST["error"], $html);
		
		return $html;
	}
	
	public function enter($data ="")
	{
		$html = "
		<form name='enter' id='enter' method='post' action='?enter' style='text-align: center!important;'>
			<table border='0' cellspacing='5px' cellpadding='5px' width='600px' style='position: relative; margin-left: 50%; left: -300px;'>
				<tr>
					<td colspan='2' style='color: red'>
						{error}
					</td>
				</tr>
				
				<tr>
					<td style='text-align: left' colspan='2'>
						Ваш адрес почты:<br/>
						<input type='edit' name='email' id='email' value='' class='round' placeholder='@mail'>
					</td>
				</tr>
				<tr>
					<td style='text-align: left' colspan='2'>
						Пароль:<br/>
						<input type='password' name='password' id='password' value='' class='round' placeholder=''><br/><br/>
					</td>
				</tr>
				<tr>
					<td style='text-align: right' width='50%'>
						<img src='../../../../captcha.php?".time()."'>
					</td>
					<td>
						<input type='edit' name='captcha' id='captcha' value='' class='round' placeholder='Символы с картинки'>
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<i>Все поля обязательны для заполнения</i>
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<input type='submit' value='&#160;&#160;&#160;Вход&#160;&#160;&#160;' class='btn_green'>&#160;&#160;&#160;<a href='?recover'>Забыли пароль?</a>
					</td>
				</tr>
			</table>
		</form>
		";
		
		$html = str_replace("{error}", $_POST["error"], $html);
		
		return $html;
	}
	
	//восстановление
	public function recover($data=array())
	{
		$html = "
		<form name='recover' id='recover' method='post' action='?recover' style='text-align: center!important;'>
			<table border='0' cellspacing='5px' cellpadding='5px' width='600px' style='position: relative; margin-left: 50%; left: -300px;'>
				<tr>
					<td colspan='2' style='color: red'>
						{error}
					</td>
				</tr>
				<tr>
					<td style='text-align: left' colspan='2'>
						Ваш адрес почты:<br/>
						<input type='edit' name='email' id='email' value='' class='round' placeholder='@mail'>
					</td>
				</tr>
				<tr>
					<td style='text-align: right' width='50%'>
						<img src='../../../../captcha.php?".time()."'>
					</td>
					<td>
						<input type='edit' name='captcha' id='captcha' value='' class='round' placeholder='Символы с картинки'>
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<i>Все поля обязательны для заполнения</i>
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<input type='submit' value='&#160;&#160;&#160;Восстановить&#160;&#160;&#160;' class='btn_green'>
					</td>
				</tr>
			</table>
		</form>
		";
		
		$html = str_replace("{error}", $data["error"], $html);
		
		return $html;
	}
}