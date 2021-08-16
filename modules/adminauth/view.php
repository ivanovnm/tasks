<?php

class adminauth_view extends c_view
{
	//форма авторизации
	public function auth($data="")
	{
		$html="
			<div class='col-4 col-lg-4 col-md-12 col-sm-12 col-xs-12'>
			</div>
			<div class='col-4 col-lg-4 col-md-12 col-sm-12 col-xs-12'>
				<form name='authform' method='post' action=''>
					<div class=\"form-group\">
						<h2>Вход в панель управления сайтом</h2>
					</div>
					<div class=\"form-group\">
						<p style=\"color: red; font-size: 1rem;\">{value:error}</p>
					</div>
					<div class=\"form-group\">
						<label for=\"exampleInputEmail1\">Логин</label>
						<input type=\"text\" class=\"form-control\" id=\"adminText\" aria-describedby=\"adminText\" placeholder=\"\" name=\"login\" id=\"login\">
					</div>
					<div class=\"form-group\">
						<label for=\"password\">Пароль</label>
						<input type=\"password\" class=\"form-control\" id=\"password\" placeholder=\"\" name=\"password\" id=\"password\">
					</div>
					<div class=\"form-group\">
						<label for=\"captcha_code\"><img src='../../../captcha.php?time=".time()."'/></label>
						<input type=\"text\" class=\"form-control\" id=\"captcha_code\" placeholder=\"\" name=\"captcha_code\" id=\"captcha_code\">
					</div>
					<button type=\"submit\" class=\"btn btn-primary\">Вход</button>
				</form>
			</div>
			<div class='col-4 col-lg-4 col-md-12 col-sm-12 col-xs-12'>
			</div>
		";
		
		$html = $this->assignValues($html, $data);
		
		return $html;
	}
	
	//меню в администрировании
	public function menu($data="")
	{
		$html = "
				<div class='row admin_menu'>
					<div class='col-2 col-xl-2 col-lg-2 col-md-12 col-sm-12'>
						<img src='../../../../images/logo.png' border='0' width='80%'/>
					</div>
					<div class='col-2 col-xl-2 col-lg-2 col-md-12 col-sm-12'>
						<a href='../../../../admin/sitestruct'>структура</a>
					</div>
					<div class='col-2 col-xl-2 col-lg-2 col-md-12 col-sm-12'>
						<a href='../../../../admin/contents'>содержимое</a>
					</div>
					<div class='col-2 col-xl-2 col-lg-2 col-md-12 col-sm-12'>
						<a href='../../../../admin/templates'>шаблоны</a>
					</div>
					<div class='col-2 col-xl-2 col-lg-2 col-md-12 col-sm-12'>
						<a href='../../../../admin/logoff'>выход</a>
					</div>
				</div>
		";
		
		$html = $this->assignValues($html, $data);
		
		return $html;
	}
}