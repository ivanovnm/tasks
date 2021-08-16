<?php

class sitestructmgr extends c_controller
{
	public function title()
	{
		return "title method";
	}
	
	//метод по дефолту
	public function __default($data="")
	{
		if (isset($_GET["id"]) && isset($_GET["action"]))
		{
			if ($_GET["action"] == "new")
			{
				
				
				if (isset($_POST))
				{
					$out = $_POST ;
				}
				else
				{
					$out = array();
				}
				
				//если есть пост данные, проверим их и запишемся
				if (isset($_POST["menu_name"]) && isset($_POST["url"]))
				{
					if ($_POST["menu_name"] !== "")
					{
						if ($this->model->validURL($_POST["url"], $_POST["parent_id"]) === true)
						{
							$this->model->insertSiteStruct();
							header ("Location: ?");
							exit();
						}
						else
						{
							$out ["error"] = "Страница с указанным URL уже существует, либо URL имеет не правильный формат.";
						}
					}
					else
					{
						$out ["error"] = "Поле 'Название в меню' не должно быть пустым!";
					}
				}
				else
				{			
					$out ["error"] = "" ;
				}
				
				if (!isset($_POST["id"]))
				{
					$out["id"] = "" ;
				}
				
				if (!isset($_POST["name"]))
				{
					$out["name"] = "" ;
				}
				
				if (!isset($_POST["menu_name"]))
				{
					$out["menu_name"] = "" ;
				}
				
				if (!isset($_POST["description"]))
				{
					$out["description"] = "" ;
				}
				
				if (!isset($_POST["url"]))
				{
					$out["url"] = "" ;
				}
				
				if (!isset($_POST["parent_id"]))
				{
					$out["parent_id"] = "1" ;
				}
				
				if (!isset($_POST["template_id"]))
				{
					$out["template_id"] = "1" ;
				}
				
				if (!isset($_POST["system"]))
				{
					$out["system"] = "0" ;
				}
				
				if (!isset($_POST["tag_title"]))
				{
					$out["tag_title"] = "" ;
				}
				
				if (!isset($_POST["tag_keywords"]))
				{
					$out["tag_keywords"] = "" ;
				}
				
				if (!isset($_POST["tag_description"]))
				{
					$out["tag_description"] = "" ;
				}
				
				return $this->view->newPage($out, $this->model->getTemplates(), $this->model->__default()) ;
			}
			elseif ($_GET["action"] == "edit") //редактирование страниц
			{
				$res = $this->model->getSiteStruct($_GET["id"]);
				if (!is_array($res))
				{
					header ("Location: ?") ;
					exit();
				}
				
				$res["params"] = $this->model->getParams($res["id"]) ;
				
				$res["error"] = "" ;
				//если есть пост данные, проверим их и запишемся
				if (isset($_POST["menu_name"]) && isset($_POST["url"]))
				{
					if ($_POST["menu_name"] !== "")
					{
						//print $res["parent_id"];
						if ($this->model->validURLEx($_POST["url"], $_GET["id"], $res["parent_id"]) === true)
						{
							$this->model->updateSiteStruct();
							header ("Location: ?");
							exit();
						}
						else
						{
							$out ["error"] = "Страница с указанным URL уже существует, либо URL имеет не правильный формат.";
						}
					}
					else
					{
						$out ["error"] = "Поле 'Название в меню' не должно быть пустым!";
					}
				}
				else
				{			
					$out ["error"] = "" ;
				}
				
				return $this->view->editPage($res, $this->model->getTemplates(), $this->model->__default()) ;
			}
			elseif ($_GET["action"] == "delete") //удаление раздела с подразделами
			{
				if (isset($_GET["accept"]))
				{
					$this->model->deleteSiteStruct($_GET["id"]) ;
					header ("Location: ?") ;
					exit();
				}
				
				return $this->view->confirmDelete($_GET) ;
			}
			elseif ($_GET["action"] == "up") //раздел поднять на один уровень вверх
			{
				$this->model->levelUpStruct($_GET["id"]) ;
				header ("Location: ?") ;
				exit();
			}
			elseif ($_GET["action"] == "down") //раздел поднять на один уровень вверх
			{
				$this->model->levelDownStruct($_GET["id"]) ;
				header ("Location: ?") ;
				exit();
			}
		}
		
		$struct = $this->model->__default() ;
		
		$view = $this->view->__default($struct) ;
		return $view;
	}
}