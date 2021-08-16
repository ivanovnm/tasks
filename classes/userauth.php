<?php

class c_user_auth extends c_pdo
{
	public function isAuth()
	{
		if (!isset($_SESSION["u_auth"]) || $_SESSION["u_auth"] == "")
		{
			return false ;
		}
		
		
	}
}