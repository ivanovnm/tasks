<?php

class userauth extends c_pdo
{
	public function menu()
	{
		if (!isset($_SESSION["u_sid"]) || $_SESSION["u_sid"] == "")
		{
			return "<div class=\"header_element\"><a href=\"/userauth/?registration\">Регистрация</a></div>
				<div class=\"enter_icon\"><a href=\"/userauth/?enter\" alt=\"Вход\" title=\"Вход\"><img src=\"../../../../images/enter.png\"></a></div>
				<div class=\"btn bg_green white header_element\" style=\"margin-top: 8px!important; margin-right: 0px!important\"><a href=\"/lk/?addadw\" style=\"color: #fff!important\">&#160;&#160;&#160;Подать заявку&#160;&#160;&#160;</a></div>";
		}
		
		//если всё же не авторизованы или отключены
		$res = $this->query("select * from `ao_users` where `id`='".$this->strCleaner($_SESSION["u_sid"])."' and `active`='1'");
		if (!is_array($res) || count($res)==0)
		{
			unset($_SESSION["u_sid"]);
			header("Location: ../../../../../../");
			exit();
		}
		
		$card = $this->query("select * from `ao_org_cards` where `parent_id`='".$res[0]["id"]."'");
		if (!is_array($card) || count($card) == 0)
		{
			//print "1";
			$org = "";
		}
		else
		{
			$org = "<div class=\"header_element\"><a href=\"/lk/?card\">".$this->strDecode($card[0]["socr_org"])."</a></div>";
		}
		
		return $org."<div class=\"enter_icon\"><a href=\"/userauth/?enter\" alt=\"Личный кабинет\" title=\"Личный кабинет\"><img src=\"../../../../images/enter.png\"></a></div>
				<div class=\"btn bg_green white header_element\" style=\"margin-top: 8px!important; margin-right: 0px!important\"><a href=\"/lk/?addadw\" style=\"color: #fff!important\">&#160;&#160;&#160;Подать заявку&#160;&#160;&#160;</a></div>
				<div class=\"header_element\"><a href=\"/userauth/?exit\">Выход</a></div>";
	}
	
	public function __default()
	{
		if (!isset($_SESSION["u_sid"]) || $_SESSION["u_sid"] == "")
		{
			return "<div class=\"header_element\"><a href=\"/userauth/?registration\">Регистрация</a></div>
				<div class=\"enter_icon\"><a href=\"/userauth/?enter\" alt=\"Вход\" title=\"Вход\"><img src=\"../../../../images/enter.png\"></a></div>
				<div class=\"btn bg_green white header_element\" style=\"margin-top: 8px!important; margin-right: 0px!important\"><a href=\"/lk/?addadw\" style=\"color: #fff!important\">&#160;&#160;&#160;Подать заявку&#160;&#160;&#160;</a></div>";
		}
		
		//если всё же не авторизованы или отключены
		$res = $this->query("select * from `ao_users` where `id`='".$this->strCleaner($_SESSION["u_sid"])."' and `active`='1'");
		if (!is_array($res) || count($res)==0)
		{
			unset($_SESSION["u_sid"]);
			header("Location: ../../../../../../");
			exit();
		}
		
		
		$card = $this->query("select * from `ao_org_cards` where `parent_id`='".$res[0]["id"]."'");
		
		if (!is_array($card) || count($card) == 0)
		{
			//print "2";
			$org = "";
		}
		else
		{
			$org = "<div class=\"header_element\"><a href=\"/lk/?card\">".$this->strDecode($card[0]["socr_org"])."</a></div>";
		}
		
		return $org."<div class=\"enter_icon\"><a href=\"/userauth/?enter\" alt=\"Личный кабинет\" title=\"Личный кабинет\"><img src=\"../../../../images/enter.png\"></a></div>
				<div class=\"btn bg_green white header_element\" style=\"margin-top: 8px!important; margin-right: 0px!important\"><a href=\"/lk/?addadw\" style=\"color: #fff!important\">&#160;&#160;&#160;Подать заявку&#160;&#160;&#160;</a></div>
				<div class=\"header_element\"><a href=\"/userauth/?exit\">Выход</a></div>";
	}
	
	public function headerText()
	{
		if (isset($_GET["registration"]))
		{
			return "Регистрация пользователя";
		}
		
		if (isset($_GET["enter"]))
		{
			return "Вход";
		}
		
		return "ok";
	}
	
	public function btn1()
	{
		if (!isset($_SESSION["u_sid"]) || $_SESSION["u_sid"] == "")
		{  
			return "<div class=\"btn_hover\" style=\"padding-top: 280px;\" onmouseover=\"showBtn('btn1')\" onmouseout=\"hideBtn('btn1')\">
				<a href=\"../../../../userauth/?registration\" class=\"btn_green\" id=\"btn1\" name=\"btn1\" style=\"display: none; width: 200px;  margin-left: 50px!important\">
				&#160;&#160;&#160;Регистрация&#160;&#160;&#160;
				</a>
			</div>";
		}
		
		return "";
	}
	
	public function btn2()
	{
		if (!isset($_SESSION["u_sid"]) || $_SESSION["u_sid"] == "")
		{  
			return "<div class=\"btn_hover\" style=\"padding-top: 280px;\" onmouseover=\"showBtn('btn2')\" onmouseout=\"hideBtn('btn2')\">
				<a href=\"../../../../userauth/?registration\" class=\"btn_green\" id=\"btn2\" name=\"btn2\" style=\"display: none; width: 200px;  margin-left: 50px!important\">
				&#160;&#160;&#160;Регистрация&#160;&#160;&#160;
				</a>
			</div>";
		}
		
		return "";
	}
	
	public function btn3()
	{
		if (!isset($_SESSION["u_sid"]) || $_SESSION["u_sid"] == "")
		{  
			return "<div class=\"btn_hover\" style=\"padding-top: 280px;\" onmouseover=\"showBtn('btn3')\" onmouseout=\"hideBtn('btn3')\">
				<a href=\"../../../../userauth/?registration\" class=\"btn_green\" id=\"btn3\" name=\"btn3\" style=\"display: none; width: 200px;  margin-left: 50px!important\">
				&#160;&#160;&#160;Регистрация&#160;&#160;&#160;
				</a>
			</div>";
		}
		
		return "";
	}
	
	public function btn4()
	{
		if (!isset($_SESSION["u_sid"]) || $_SESSION["u_sid"] == "")
		{  
			return "<div class=\"btn_hover\" style=\"padding-top: 280px;\" onmouseover=\"showBtn('btn4')\" onmouseout=\"hideBtn('btn4')\">
				<a href=\"../../../../userauth/?registration\" class=\"btn_green\" id=\"btn4\" name=\"btn4\" style=\"display: none; width: 200px;  margin-left: 50px!important\">
				&#160;&#160;&#160;Регистрация&#160;&#160;&#160;
				</a>
			</div>";
		}
		
		return "";
	}
	
	public function secondMenu()
	{
		if (!isset($_SESSION["u_sid"]) || $_SESSION["u_sid"] == "")
		{
			return "";
			
		}
		
		$data = $this->query("select * from `ao_users` where `id`='".$this->strCleaner($_SESSION["u_sid"])."' and `active`='1'");
		if (!is_array($data) || count($data) ==0)
		{
			unset($_SESSION["u_sid"]);
			return "";
		}
		
		if ($data[0]["type"] == "2")
		{
			return "
			<div class=\"block bg_line\">
				<div class=\"line\" style=\"text-align: left!important; height: 50px!important\">
					<div class=\"header_element\"><a href=\"../../../../lk/\" style='color: #464646'>Личный кабинет</a></div>
					<div class=\"header_element\"><a href=\"../../../../lk/?adwlist\" style='color: #464646'>Аукционы</a></div>
					<div class=\"header_element\"><a href=\"../../../../lk/?contracts\" style='color: #464646'>Контракты</a></div>
				</div>
			</div>
			<div class=\"block\" style=\"background-color: #36b8ff!important; height: 3px!important\">
			</div>
			";
		}
		else
		{
			return "
			<div class=\"block bg_line\">
				<div class=\"line\" style=\"text-align: left!important; height: 50px!important\">
					<div class=\"header_element\"><a href=\"../../../../lk/\" style='color: #464646'>Личный кабинет</a></div>
					<div class=\"header_element\"><a href=\"../../../../lk/?contracts\" style='color: #464646'>Контракты</a></div>
				</div>
			</div>
			<div class=\"block\" style=\"background-color: #36b8ff!important; height: 3px!important\">
			</div>
			";
		}
	}
}